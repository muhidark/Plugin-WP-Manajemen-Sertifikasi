<?php
/**
 * Form Handler for Public Forms
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Form_Handler {

    public static function init() {
        add_action( 'wp_ajax_lsp_cert_submit_pendaftaran', array( __CLASS__, 'submit_pendaftaran' ) );
        add_action( 'wp_ajax_nopriv_lsp_cert_submit_pendaftaran', array( __CLASS__, 'submit_pendaftaran' ) );
    }

    public static function submit_pendaftaran() {
        check_ajax_referer( 'lsp_cert_public_nonce', 'nonce' );

        // Pembatas permintaan: maksimal 3 pengiriman per menit per alamat IP.
        // Mencegah satu orang atau bot membanjiri pendaftaran dan menghabiskan kuota.
        $client_ip = self::get_client_ip();
        $rate_key  = 'lsp_cert_rl_daftar_' . md5( $client_ip );
        $attempts  = (int) get_transient( $rate_key );

        if ( $attempts >= 3 ) {
            wp_send_json_error( array(
                'message' => 'Terlalu banyak percobaan pendaftaran dari jaringan Anda. Silakan tunggu 1 menit, lalu coba lagi.',
            ), 429 );
        }
        set_transient( $rate_key, $attempts + 1, MINUTE_IN_SECONDS );


        // Validate required fields
        $required_fields = array(
            'kegiatan_id', 'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir',
            'jenis_kelamin', 'alamat', 'kode_pos', 'telepon', 'email', 'pendidikan'
        );
        
        foreach ( $required_fields as $field ) {
            if ( empty( $_POST[$field] ) ) {
                wp_send_json_error( array( 'message' => "Field $field wajib diisi" ) );
            }
        }
        
        // Validate email
        if ( ! is_email( $_POST['email'] ) ) {
            wp_send_json_error( array( 'message' => 'Format email tidak valid' ) );
        }
        
        // Check kuota & validasi kegiatan
        $kegiatan_id = intval( $_POST['kegiatan_id'] );
        $kegiatan = LSP_Cert_Database::get_kegiatan( $kegiatan_id );
        
        if ( ! $kegiatan ) {
            wp_send_json_error( array( 'message' => 'Kegiatan tidak ditemukan' ) );
        }

        // Aturan pendaftaran ditegakkan di server, bukan hanya di tampilan:
        // status kegiatan, tanggal pelaksanaan, dan keabsahan skema.
        $masalah_kegiatan = self::validasi_kegiatan( $kegiatan );
        if ( '' !== $masalah_kegiatan ) {
            wp_send_json_error( array( 'message' => $masalah_kegiatan ), 409 );
        }

        // Validasi format identitas (server-side, tidak bergantung atribut HTML).
        $masalah_identitas = self::validasi_identitas( $_POST );
        if ( '' !== $masalah_identitas ) {
            wp_send_json_error( array( 'message' => $masalah_identitas ) );
        }
        
        $peserta_count = LSP_Cert_Database::get_peserta_count( $kegiatan_id );
        if ( $peserta_count >= $kegiatan->kuota ) {
            wp_send_json_error( array( 'message' => 'Kuota kegiatan sudah penuh' ) );
        }

        // Cegah pendaftaran ganda: NIK atau email yang sama pada kegiatan yang sama.
        $nik_input   = sanitize_text_field( wp_unslash( $_POST['nik'] ) );
        $email_input = sanitize_email( wp_unslash( $_POST['email'] ) );
        $ganda       = LSP_Cert_Database::cek_pendaftar_ganda( $kegiatan_id, $nik_input, $email_input );

        if ( 'nik' === $ganda ) {
            wp_send_json_error( array(
                'message' => 'NIK ini sudah terdaftar pada kegiatan ini. Silakan hubungi admin bila ingin memperbaiki data.',
            ), 409 );
        }
        if ( 'email' === $ganda ) {
            wp_send_json_error( array(
                'message' => 'Email ini sudah dipakai untuk mendaftar pada kegiatan ini. Silakan hubungi admin bila ingin memperbaiki data.',
            ), 409 );
        }
        
        // Prepare data
        $data = array(
            'kegiatan_id' => $kegiatan_id,
            'nama_lengkap' => sanitize_text_field( $_POST['nama_lengkap'] ),
            'nik' => sanitize_text_field( $_POST['nik'] ),
            'tempat_lahir' => sanitize_text_field( $_POST['tempat_lahir'] ),
            'tanggal_lahir' => sanitize_text_field( $_POST['tanggal_lahir'] ),
            'jenis_kelamin' => sanitize_text_field( $_POST['jenis_kelamin'] ),
            'kebangsaan' => sanitize_text_field( $_POST['kebangsaan'] ?? 'Indonesia' ),
            'alamat' => sanitize_textarea_field( $_POST['alamat'] ),
            'kode_pos' => sanitize_text_field( $_POST['kode_pos'] ),
            'telepon' => sanitize_text_field( $_POST['telepon'] ),
            'email' => sanitize_email( $_POST['email'] ),
            'pendidikan' => sanitize_text_field( $_POST['pendidikan'] ),
            'nama_perusahaan' => sanitize_text_field( $_POST['nama_perusahaan'] ?? '' ),
            'jabatan' => sanitize_text_field( $_POST['jabatan'] ?? '' ),
            'alamat_kantor' => sanitize_textarea_field( $_POST['alamat_kantor'] ?? '' ),
            'kode_pos_kantor' => sanitize_text_field( $_POST['kode_pos_kantor'] ?? '' ),
            'telepon_kantor' => sanitize_text_field( $_POST['telepon_kantor'] ?? '' ),
            'fax_kantor' => sanitize_text_field( $_POST['fax_kantor'] ?? '' ),
            'email_kantor' => sanitize_email( $_POST['email_kantor'] ?? '' ),
            'masa_kerja' => sanitize_text_field( $_POST['masa_kerja'] ?? '' ),
            'tujuan_asesmen' => sanitize_text_field( $_POST['tujuan_asesmen'] ?? 'sertifikasi' ),
            'persyaratan_dasar' => isset( $_POST['persyaratan_dasar'] ) ? array_map( 'sanitize_text_field', (array) $_POST['persyaratan_dasar'] ) : array(),
            'persyaratan_admin' => isset( $_POST['persyaratan_admin'] ) ? array_map( 'sanitize_text_field', (array) $_POST['persyaratan_admin'] ) : array(),
        );
        
        // Insert pendaftaran
        $pendaftaran_id = LSP_Cert_Database::insert_pendaftaran( $data );
        
        if ( ! $pendaftaran_id ) {
            wp_send_json_error( array( 'message' => 'Gagal menyimpan data pendaftaran' ) );
        }
        
        // Save APL 02 answers — struktur dibangun ulang dari Master SKKNI.
        //
        // Browser hanya boleh menentukan pilihan K/BK beserta kode unit & nomor elemen.
        // Judul unit dan nama elemen SELALU diambil dari master, sehingga manipulasi
        // hidden field tidak bisa mengubah isi dokumen resmi.
        $apl02_input = array();
        if ( isset( $_POST['apl02'] ) && is_array( $_POST['apl02'] ) ) {
            $apl02_input = $_POST['apl02'];
        } elseif ( isset( $_POST['apl02_jawaban'] ) && is_array( $_POST['apl02_jawaban'] ) ) {
            $apl02_input = $_POST['apl02_jawaban'];
        }

        $apl02_diabaikan = 0;
        if ( ! empty( $apl02_input ) ) {
            $susun = self::susun_jawaban_apl02( (int) $kegiatan->skema_id, $apl02_input );
            $apl02_diabaikan = $susun['diabaikan'];

            foreach ( $susun['jawaban'] as $row ) {
                $row['pendaftaran_id'] = $pendaftaran_id;
                LSP_Cert_Database::insert_apl02_jawaban( $row );
            }
        }
        
        wp_send_json_success( array(
            'message' => 'Pendaftaran berhasil disimpan',
            'pendaftaran_id' => $pendaftaran_id,
            'apl02_tersimpan' => isset( $susun['jawaban'] ) ? count( $susun['jawaban'] ) : 0,
            'apl02_diabaikan' => $apl02_diabaikan,
        ));
    }

    /**
     * Bangun ulang jawaban APL.02 dari Master SKKNI.
     *
     * Browser hanya boleh mengirim kode unit, nomor elemen, dan pilihan K/BK.
     * Judul unit dan nama elemen tidak pernah diambil dari request; bila kode unit
     * atau nomor elemen tidak ada di master, entri tersebut diabaikan.
     *
     * @param int   $skema_id
     * @param array $input Data mentah dari request.
     * @return array{jawaban:array,diabaikan:int}
     */
    private static function susun_jawaban_apl02( $skema_id, $input ) {
        $units = LSP_Cert_Integration::get_unit_kompetensi( (int) $skema_id );
        $hasil = array();
        $diabaikan = 0;

        if ( empty( $units ) || ! is_array( $units ) ) {
            return array( 'jawaban' => array(), 'diabaikan' => is_array( $input ) ? count( $input ) : 0 );
        }

        // Peta master: kode unit → judul + daftar elemen (nomor → nama).
        $peta = array();
        foreach ( $units as $u ) {
            $kode = trim( (string) ( $u['kode'] ?? '' ) );
            if ( '' === $kode ) {
                continue;
            }
            $peta[ $kode ] = array(
                'judul'  => (string) ( $u['judul'] ?? '' ),
                'elemen' => array(),
            );
            foreach ( (array) ( $u['elemen'] ?? array() ) as $idx => $e ) {
                $nomor = isset( $e['nomor'] ) ? (int) $e['nomor'] : ( $idx + 1 );
                $peta[ $kode ]['elemen'][ $nomor ] = (string) ( $e['nama'] ?? '' );
            }
        }

        $terpakai = array();

        foreach ( (array) $input as $item ) {
            if ( ! is_array( $item ) ) {
                $diabaikan++;
                continue;
            }

            $kode = trim( (string) ( $item['kode'] ?? '' ) );
            if ( '' === $kode || ! isset( $peta[ $kode ] ) ) {
                $diabaikan++;
                continue;
            }

            $nomor = (int) ( $item['elemen_nomor'] ?? 0 );
            if ( ! isset( $peta[ $kode ]['elemen'][ $nomor ] ) ) {
                $diabaikan++;
                continue;
            }

            $jawaban = strtoupper( trim( (string) ( $item['jawaban'] ?? '' ) ) );
            if ( ! in_array( $jawaban, array( 'K', 'BK' ), true ) ) {
                $diabaikan++;
                continue;
            }

            $kunci = $kode . '#' . $nomor;
            $terpakai[ $kunci ] = array(
                'unit_kompetensi_kode'  => $kode,
                'unit_kompetensi_judul' => $peta[ $kode ]['judul'],
                'elemen_nomor'          => $nomor,
                'elemen_nama'           => $peta[ $kode ]['elemen'][ $nomor ],
                'jawaban'               => $jawaban,
            );
        }

        return array( 'jawaban' => array_values( $terpakai ), 'diabaikan' => $diabaikan );
    }

    /**
     * Validasi aturan kegiatan sebelum pendaftaran disimpan.
     *
     * Aturan ini sebelumnya hanya diterapkan di tampilan (kegiatan yang dibuka saja
     * yang ditampilkan), sehingga request langsung dapat melewatinya.
     *
     * @param object $kegiatan
     * @return string Pesan masalah, atau '' bila lolos.
     */
    private static function validasi_kegiatan( $kegiatan ) {
        $status = isset( $kegiatan->status ) ? (string) $kegiatan->status : '';
        if ( 'dibuka' !== $status ) {
            return 'Kegiatan ini belum dibuka atau sudah ditutup untuk pendaftaran.';
        }

        $hari_ini   = current_time( 'Y-m-d' );
        $pelaksanaan = isset( $kegiatan->tanggal_pelaksanaan ) ? (string) $kegiatan->tanggal_pelaksanaan : '';
        if ( '' === $pelaksanaan || $pelaksanaan < $hari_ini ) {
            return 'Tanggal pelaksanaan kegiatan ini sudah lewat. Silakan pilih jadwal lain.';
        }

        $skema_id = isset( $kegiatan->skema_id ) ? (int) $kegiatan->skema_id : 0;
        if ( ! $skema_id || empty( LSP_Cert_Integration::get_skema( $skema_id ) ) ) {
            return 'Skema sertifikasi pada kegiatan ini tidak valid. Silakan hubungi admin LSP.';
        }

        return '';
    }

    /**
     * Validasi format data identitas pendaftar.
     *
     * @param array $post Data POST mentah.
     * @return string Pesan masalah, atau '' bila lolos.
     */
    private static function validasi_identitas( $post ) {
        $nik = preg_replace( '/\s+/', '', (string) ( $post['nik'] ?? '' ) );
        if ( ! preg_match( '/^\d{16}$/', $nik ) ) {
            return 'NIK harus terdiri dari 16 angka.';
        }

        $telepon = preg_replace( '/[^0-9]/', '', (string) ( $post['telepon'] ?? '' ) );
        if ( strlen( $telepon ) < 8 || strlen( $telepon ) > 15 ) {
            return 'Nomor telepon harus 8–15 angka.';
        }

        $kode_pos = preg_replace( '/\s+/', '', (string) ( $post['kode_pos'] ?? '' ) );
        if ( ! preg_match( '/^\d{5}$/', $kode_pos ) ) {
            return 'Kode pos harus 5 angka.';
        }

        $lahir_raw = (string) ( $post['tanggal_lahir'] ?? '' );
        $lahir     = \DateTime::createFromFormat( 'Y-m-d', $lahir_raw );
        if ( ! $lahir || $lahir->format( 'Y-m-d' ) !== $lahir_raw ) {
            return 'Tanggal lahir harus berformat YYYY-MM-DD.';
        }
        $hari_ini = new \DateTime( current_time( 'Y-m-d' ) );
        if ( $lahir > $hari_ini ) {
            return 'Tanggal lahir tidak boleh di masa depan.';
        }
        $umur = (int) $lahir->diff( $hari_ini )->y;
        if ( $umur < 15 ) {
            return 'Usia pendaftar minimal 15 tahun.';
        }

        $jk = (string) ( $post['jenis_kelamin'] ?? '' );
        if ( ! in_array( $jk, array( 'Laki-laki', 'Perempuan' ), true ) ) {
            return 'Jenis kelamin harus dipilih: Laki-laki atau Perempuan.';
        }

        $tujuan = (string) ( $post['tujuan_asesmen'] ?? 'sertifikasi' );
        if ( ! in_array( $tujuan, array( 'sertifikasi', 'sertifikasi_ulang', 'pkt', 'rpl', 'lainnya' ), true ) ) {
            return 'Tujuan asesmen tidak valid.';
        }

        return '';
    }

    /**
     * Alamat IP pengunjung yang aman dipakai sebagai kunci pembatas permintaan.
     *
     * @return string
     */
    private static function get_client_ip() {
        $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? trim( (string) $_SERVER['REMOTE_ADDR'] ) : '';
        if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
            return $ip;
        }
        return '127.0.0.1';
    }
}
