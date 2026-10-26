<?php
/**
 * Document Printer Manager
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Document_Printer {

    /**
     * Daftarkan dokumen yang tersedia untuk dicetak
     * Modular & Extensible: Jika ada form baru di masa depan (MAPA, AK, dll),
     * cukup didaftarkan ke registry ini atau via filter lsp_cert_available_documents.
     *
     * @return array
     */
    public static function get_available_documents() {
        $docs = array(
            'bundle_all' => array(
                'id'          => 'bundle_all',
                'title'       => '📦 Seluruh Berkas Sertifikasi Asesi (1 s/d 25 All-in-One)',
                'code'        => 'BNSP 1-25 BUNDLE',
                'description' => 'Mencetak seluruh berkas sertifikasi kompetensi peserta (APL 01-02, MAPA 01-02, AK 01-04, IA 01-05, AK 02-03)',
            ),
            'all' => array(
                'id'          => 'all',
                'title'       => '📄 Semua Dokumen (Paket Lengkap APL 01 & 02)',
                'code'        => 'FR.APL.01 & 02',
                'description' => 'Paket lengkap Permohonan Sertifikasi & Asesmen Mandiri',
            ),
            'apl01' => array(
                'id'          => 'apl01',
                'title'       => 'FR.APL.01 Permohonan Sertifikasi',
                'code'        => 'FR.APL.01',
                'description' => 'Formulir Permohonan Sertifikasi Kompetensi',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-apl01.php',
            ),
            'apl02' => array(
                'id'          => 'apl02',
                'title'       => 'FR.APL.02 Asesmen Mandiri',
                'code'        => 'FR.APL.02',
                'description' => 'Formulir Asesmen Mandiri & Portofolio',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-apl02.php',
            ),
            'mapa01_obs' => array(
                'id'          => 'mapa01_obs',
                'title'       => 'FR.MAPA.01 Observasi Langsung',
                'code'        => 'FR.MAPA.01 (Observasi)',
                'description' => 'Merencanakan Aktivitas dan Proses Asesmen - Metode Observasi Langsung',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-mapa01.php',
                'metode'      => 'observasi',
            ),
            'mapa01_dit' => array(
                'id'          => 'mapa01_dit',
                'title'       => 'FR.MAPA.01 Daftar Instruksi Terstruktur (DIT)',
                'code'        => 'FR.MAPA.01 (DIT)',
                'description' => 'Merencanakan Aktivitas dan Proses Asesmen - Metode DIT',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-mapa01.php',
                'metode'      => 'dit',
            ),
            'mapa01_vp' => array(
                'id'          => 'mapa01_vp',
                'title'       => 'FR.MAPA.01 Verifikasi Portofolio',
                'code'        => 'FR.MAPA.01 (Portofolio)',
                'description' => 'Merencanakan Aktivitas dan Proses Asesmen - Metode Verifikasi Portofolio',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-mapa01.php',
                'metode'      => 'portofolio',
            ),
            'mapa01_all' => array(
                'id'          => 'mapa01_all',
                'title'       => '📑 FR.MAPA.01 Semua Versi (Observasi, DIT, Portofolio)',
                'code'        => 'FR.MAPA.01 (Semua)',
                'description' => 'Paket ketiga versi FR.MAPA.01 sekaligus dalam lembar terpisah',
            ),
            'mapa02' => array(
                'id'          => 'mapa02',
                'title'       => 'FR.MAPA.02 — Peta Instrumen Asesmen',
                'code'        => 'FR.MAPA.02',
                'description' => 'Peta Instrumen Asesmen Hasil Pendekatan Asesmen dan Perencanaan Asesmen',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-mapa02.php',
            ),
            'ak01' => array(
                'id'          => 'ak01',
                'title'       => 'FR.AK.01 — Persetujuan Asesmen dan Kerahasiaan',
                'code'        => 'FR.AK.01',
                'description' => 'Persetujuan Asesmen dan Kerahasiaan antara Asesi dan Asesor',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak01.php',
            ),
            'ak02' => array(
                'id'          => 'ak02',
                'title'       => 'FR.AK.02 — Rekaman Asesmen Kompetensi',
                'code'        => 'FR.AK.02',
                'description' => 'Rekaman Asesmen Kompetensi dan Bukti Asesmen per Peserta',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak02.php',
            ),
            'ak03' => array(
                'id'          => 'ak03',
                'title'       => 'FR.AK.03 — Umpan Balik dan Catatan Asesmen',
                'code'        => 'FR.AK.03',
                'description' => 'Umpan Balik dari Asesi dan Catatan Pelaksanaan Asesmen',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak03.php',
            ),
            'ak04' => array(
                'id'          => 'ak04',
                'title'       => 'FR.AK.04 — Banding Asesmen',
                'code'        => 'FR.AK.04',
                'description' => 'Formulir Pengajuan Banding Asesmen oleh Asesi',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak04.php',
            ),
            'ak07' => array(
                'id'          => 'ak07',
                'title'       => 'FR.AK.07 — Ceklis Penyesuaian yang Wajar dan Beralasan',
                'code'        => 'FR.AK.07',
                'description' => 'Ceklis Penyesuaian yang Wajar dan Beralasan untuk Asesi dengan Kebutuhan Khusus / Kondisi Tertentu',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak07.php',
            ),
            'ak05' => array(
                'id'          => 'ak05',
                'title'       => 'FR.AK.05 — Laporan Asesmen',
                'code'        => 'FR.AK.05',
                'description' => 'Laporan Hasil Asesmen Peserta dan Rekomendasi Asesor',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak05.php',
            ),
            'ak06' => array(
                'id'          => 'ak06',
                'title'       => 'FR.AK.06 — Meninjau Proses Asesmen',
                'code'        => 'FR.AK.06',
                'description' => 'Formulir Peninjauan Proses Asesmen dan Dimensi Kompetensi',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak06.php',
            ),
            'ia04a' => array(
                'id'          => 'ia04a',
                'title'       => 'FR.IA.04A — DIT Proyek Singkat / Kegiatan Terstruktur',
                'code'        => 'FR.IA.04A',
                'description' => 'Penjelasan Proyek Singkat atau Kegiatan Terstruktur Lainnya (STAR)',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia04a.php',
            ),
            'ia04b' => array(
                'id'          => 'ia04b',
                'title'       => 'FR.IA.04B — Penilaian Proyek Singkat / Kegiatan Terstruktur',
                'code'        => 'FR.IA.04B',
                'description' => 'Lembar Penilaian Proyek Singkat atau Kegiatan Terstruktur Lainnya',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia04b.php',
            ),
            'ia05a' => array(
                'id'          => 'ia05a',
                'title'       => 'FR.IA.05A — Pertanyaan Tertulis Pilihan Ganda',
                'code'        => 'FR.IA.05A',
                'description' => 'Naskah Pertanyaan Tertulis Pilihan Ganda (DPT)',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia05a.php',
            ),
            'ia05b' => array(
                'id'          => 'ia05b',
                'title'       => 'FR.IA.05B — Lembar Kunci Jawaban Pilihan Ganda',
                'code'        => 'FR.IA.05B',
                'description' => 'Lembar Kunci Jawaban Pertanyaan Tertulis Pilihan Ganda (Terbatas)',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia05b.php',
            ),
            'ia05c' => array(
                'id'          => 'ia05c',
                'title'       => 'FR.IA.05C — Lembar Jawaban Pilihan Ganda',
                'code'        => 'FR.IA.05C',
                'description' => 'Lembar Jawaban Pilihan Ganda dan Hasil Penilaian Asesi',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia05c.php',
            ),
            'ia01' => array(
                'id'          => 'ia01',
                'title'       => 'FR.IA.01 — Ceklis Observasi Aktivitas Praktik',
                'code'        => 'FR.IA.01',
                'description' => 'Ceklis Observasi Aktivitas di Tempat Kerja atau Tempat Kerja Simulasi',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia01.php',
            ),
            'ia02' => array(
                'id'          => 'ia02',
                'title'       => 'FR.IA.02 — Tugas Praktik Demonstrasi',
                'code'        => 'FR.IA.02',
                'description' => 'Tugas Praktik Demonstrasi (TPD) berbasis Kelompok Pekerjaan',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia02.php',
            ),
            'ia03' => array(
                'id'          => 'ia03',
                'title'       => 'FR.IA.03 — Pertanyaan Mendukung Observasi',
                'code'        => 'FR.IA.03',
                'description' => 'Pertanyaan untuk Mendukung Observasi (PMO) Asesi',
                'template'    => LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia03.php',
            ),
        );

        return apply_filters( 'lsp_cert_available_documents', $docs );
    }

    /**
     * Render halaman cetak siap print
     *
     * @param int    $pendaftaran_id
     * @param string $doc_type
     */
    public static function render_print_page( $pendaftaran_id, $doc_type = 'all' ) {
        // Cek otorisasi: capability khusus cetak dokumen (admin & asesor resmi).
        if ( ! current_user_can( 'lsp_cert_print_docs' ) ) {
            wp_die( 'Anda tidak memiliki hak akses untuk mencetak dokumen ini.', 'Akses Ditolak', array( 'response' => 403 ) );
        }

        // Keamanan Khusus FR.IA.05B: Lembar Kunci Jawaban terbatas hanya untuk Admin dan Asesor resmi
        if ( 'ia05b' === $doc_type ) {
            if ( ! current_user_can( 'lsp_cert_print_docs' ) ) {
                wp_die( 'Akses ditolak: FR.IA.05B (Lembar Kunci Jawaban) merupakan dokumen rahasia dan hanya dapat diakses oleh Admin atau Asesor.', 'Akses Ditolak', array( 'response' => 403 ) );
            }
        }

        $pendaftaran = LSP_Cert_Database::get_pendaftaran( $pendaftaran_id );
        if ( ! $pendaftaran ) {
            wp_die( 'Data pendaftaran tidak ditemukan.', 'Data Tidak Ditemukan', array( 'response' => 404 ) );
        }

        // Isolasi data peserta (fail-closed):
        //   - bila peserta sudah punya asesor, HANYA asesor itu (atau admin) yang boleh;
        //   - bila belum punya asesor, asesor harus ditugaskan pada kegiatannya.
        if ( ! current_user_can( 'manage_options' ) && LSP_Cert_Integration::is_asesor( get_current_user_id() ) ) {
            $current_user_id = (int) get_current_user_id();

            if ( ! empty( $pendaftaran->asesor_id ) ) {
                $ditugaskan = ( (int) $pendaftaran->asesor_id === $current_user_id );
            } else {
                $kegiatan_peserta = LSP_Cert_Database::get_kegiatan( $pendaftaran->kegiatan_id );
                $ditugaskan       = $kegiatan_peserta
                    ? LSP_Cert_Integration::asesor_bertugas_di_kegiatan( $current_user_id, $kegiatan_peserta )
                    : false;
            }

            if ( ! $ditugaskan ) {
                wp_die( 'Anda tidak memiliki hak akses untuk mencetak dokumen peserta ini.', 'Akses Ditolak', array( 'response' => 403 ) );
            }
        }

        $kegiatan = LSP_Cert_Database::get_kegiatan( $pendaftaran->kegiatan_id );
        if ( ! $kegiatan ) {
            wp_die( 'Data kegiatan tidak ditemukan.', 'Data Tidak Ditemukan', array( 'response' => 404 ) );
        }

        // Ambil Paket MUK (Multi-Versi Bundling) jika ada, fallback ke default skema
        $paket_muk_id = ! empty( $pendaftaran->paket_muk_id ) 
            ? (int) $pendaftaran->paket_muk_id 
            : ( ! empty( $kegiatan->paket_muk_id ) ? (int) $kegiatan->paket_muk_id : 0 );
        $paket_muk    = null;
        if ( class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            if ( $paket_muk_id ) {
                $paket_muk = LSP_Cert_Paket_MUK::get_paket( $paket_muk_id );
            } else {
                $paket_muk = LSP_Cert_Paket_MUK::get_default_paket( $kegiatan->skema_id );
                if ( $paket_muk ) {
                    $paket_muk_id = (int) $paket_muk->id;
                }
            }
        }
        $nama_paket_muk = $paket_muk ? $paket_muk->nama_paket : '';
        $kode_versi_muk = $paket_muk ? $paket_muk->kode_versi : '';

        // Gerbang integritas SKKNI: dokumen yang memuat Elemen/KUK hanya boleh dicetak
        // bila master kompetensi lengkap (tidak ada Elemen/KUK karangan).
        self::enforce_skkni_integrity( $kegiatan->skema_id, $doc_type );
        self::enforce_apl02_completeness( (int) $pendaftaran->id, $doc_type );

        // Validasi Cakupan Kelompok Pekerjaan sebelum cetak FR.IA.01, FR.IA.02, FR.IA.03 (Task 3 Phase IA-01-03C)
        self::enforce_kelompok_pekerjaan_coverage( $kegiatan->skema_id, $doc_type, $paket_muk_id );

        $skema         = LSP_Cert_Integration::get_skema( $kegiatan->skema_id );
        $tuk           = LSP_Cert_Integration::get_tuk( $kegiatan->tuk_id );
        $asesor        = ! empty( $pendaftaran->asesor_id ) ? LSP_Cert_Integration::get_asesor( $pendaftaran->asesor_id ) : null;
        $units_skkni   = LSP_Cert_Integration::get_unit_kompetensi( $kegiatan->skema_id );
        $apl02_jawaban = LSP_Cert_Database::get_apl02_jawaban( $pendaftaran_id );
        $available_docs = self::get_available_documents();

        // Validasi doc_type
        if ( ! array_key_exists( $doc_type, $available_docs ) ) {
            $doc_type = 'all';
        }

        // Ambil opsi kategori latar belakang asesi untuk pencetakan saat ini (default 0 / belum dipilih)
        $selected_kat     = isset( $_GET['kat'] ) ? intval( $_GET['kat'] ) : 0;
        $selected_kat_obs = isset( $_GET['kat_obs'] ) ? intval( $_GET['kat_obs'] ) : $selected_kat;
        $selected_kat_dit = isset( $_GET['kat_dit'] ) ? intval( $_GET['kat_dit'] ) : $selected_kat;
        $selected_kat_vp  = isset( $_GET['kat_vp'] ) ? intval( $_GET['kat_vp'] ) : $selected_kat;

        // Siapkan data Tim Asesor: Penyusun dan Validator (memiliki Nama dan Nomor MET)
        // Data ini dipakai di FR.MAPA.01 dan formulir sertifikasi lainnya
        $penyusun_nama = ! empty( $pendaftaran->penyusun_nama ) 
            ? $pendaftaran->penyusun_nama 
            : ( ! empty( $kegiatan->penyusun_nama ) ? $kegiatan->penyusun_nama : ( $asesor['name'] ?? '' ) );
        
        $penyusun_met = ! empty( $pendaftaran->penyusun_met )
            ? $pendaftaran->penyusun_met
            : ( ! empty( $kegiatan->penyusun_met ) ? $kegiatan->penyusun_met : ( $asesor['no_reg'] ?? '' ) );

        $validator_nama = ! empty( $pendaftaran->validator_nama )
            ? $pendaftaran->validator_nama
            : ( ! empty( $kegiatan->validator_nama ) ? $kegiatan->validator_nama : '' );

        $validator_met = ! empty( $pendaftaran->validator_met )
            ? $pendaftaran->validator_met
            : ( ! empty( $kegiatan->validator_met ) ? $kegiatan->validator_met : '' );

        $penyusun = array(
            'id'     => $pendaftaran->asesor_id ?: ($kegiatan->penyusun_id ?? 0),
            'name'   => $penyusun_nama,
            'no_reg' => $penyusun_met,
        );

        $validator = array(
            'id'     => $pendaftaran->validator_id ?: ($kegiatan->validator_id ?? 0),
            'name'   => $validator_nama,
            'no_reg' => $validator_met,
        );

        // Tidak ada identitas contoh: bila data master tidak diisi, cetak kosong
        // dan beri peringatan khusus admin di halaman cetak (tidak ikut tercetak).
        $identitas_kosong = self::daftar_identitas_kosong( $penyusun, $validator );

        // Master Document Layout
        include LSP_CERT_PLUGIN_DIR . 'templates/docs/document-layout.php';

        if ( ! defined( 'LSP_CERT_TESTING' ) ) {
            exit;
        }
    }

    /**
     * Render halaman cetak dokumen tingkat kegiatan (seperti FR.MAPA.02)
     * Tidak memerlukan ID pendaftaran atau ID peserta
     *
     * @param int    $kegiatan_id
     * @param string $doc_type
     */
    public static function render_kegiatan_print_page( $kegiatan_id, $doc_type = 'mapa02' ) {
        // Cek otorisasi: capability khusus cetak dokumen (admin & asesor resmi).
        if ( ! current_user_can( 'lsp_cert_print_docs' ) ) {
            wp_die( 'Anda tidak memiliki hak akses untuk mencetak dokumen ini.', 'Akses Ditolak', array( 'response' => 403 ) );
        }

        // Keamanan Khusus FR.IA.05B: Lembar Kunci Jawaban terbatas hanya untuk Admin dan Asesor resmi
        if ( 'ia05b' === $doc_type ) {
            if ( ! current_user_can( 'lsp_cert_print_docs' ) ) {
                wp_die( 'Akses ditolak: FR.IA.05B (Lembar Kunci Jawaban) merupakan dokumen rahasia dan hanya dapat diakses oleh Admin atau Asesor.', 'Akses Ditolak', array( 'response' => 403 ) );
            }
        }

        $kegiatan = LSP_Cert_Database::get_kegiatan( $kegiatan_id );
        if ( ! $kegiatan ) {
            wp_die( 'Data kegiatan sertifikasi tidak ditemukan.', 'Data Tidak Ditemukan', array( 'response' => 404 ) );
        }

        $skema = LSP_Cert_Integration::get_skema( $kegiatan->skema_id );
        if ( ! $skema ) {
            wp_die( 'Data Master Skema tidak ditemukan atau belum lengkap untuk kegiatan sertifikasi ini.', 'Master Skema Tidak Ditemukan', array( 'response' => 404 ) );
        }

        // Isolasi kegiatan: asesor non-admin hanya boleh kegiatan yang ditugaskan kepadanya.
        if ( ! current_user_can( 'manage_options' ) && LSP_Cert_Integration::is_asesor( get_current_user_id() ) ) {
            if ( ! LSP_Cert_Integration::asesor_bertugas_di_kegiatan( get_current_user_id(), $kegiatan ) ) {
                wp_die( 'Anda tidak memiliki hak akses untuk mencetak dokumen kegiatan ini.', 'Akses Ditolak', array( 'response' => 403 ) );
            }
        }

        // Ambil Paket MUK level KEGIATAN (Multi-Versi Bundling) jika ada,
        // fallback ke default skema. Dokumen kegiatan tidak punya konteks peserta,
        // sehingga `$pendaftaran` tidak boleh dipakai sebagai sumber di sini.
        $paket_muk_id = ! empty( $kegiatan->paket_muk_id ) ? (int) $kegiatan->paket_muk_id : 0;
        $paket_muk    = null;
        if ( class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            if ( $paket_muk_id ) {
                $paket_muk = LSP_Cert_Paket_MUK::get_paket( $paket_muk_id );
            } else {
                $paket_muk = LSP_Cert_Paket_MUK::get_default_paket( $kegiatan->skema_id );
                if ( $paket_muk ) {
                    $paket_muk_id = (int) $paket_muk->id;
                }
            }
        }
        $nama_paket_muk = $paket_muk ? $paket_muk->nama_paket : '';
        $kode_versi_muk = $paket_muk ? $paket_muk->kode_versi : '';

        // Gerbang integritas SKKNI: dokumen yang memuat Elemen/KUK hanya boleh dicetak
        // bila master kompetensi lengkap (tidak ada Elemen/KUK karangan).
        self::enforce_skkni_integrity( $kegiatan->skema_id, $doc_type );

        // Validasi Cakupan Kelompok Pekerjaan sebelum cetak FR.IA.01, FR.IA.02, FR.IA.03 (Task 3 Phase IA-01-03C)
        self::enforce_kelompok_pekerjaan_coverage( $kegiatan->skema_id, $doc_type, $paket_muk_id );

        $tuk            = LSP_Cert_Integration::get_tuk( $kegiatan->tuk_id );
        $units_skkni    = LSP_Cert_Integration::get_unit_kompetensi( $kegiatan->skema_id );
        $available_docs = self::get_available_documents();
        $pendaftaran    = null;
        $apl02_jawaban  = array();

        // Validasi doc_type
        if ( ! array_key_exists( $doc_type, $available_docs ) ) {
            $doc_type = 'mapa02';
        }

        $selected_kat     = 0;
        $selected_kat_obs = 0;
        $selected_kat_dit = 0;
        $selected_kat_vp  = 0;

        // Ambil data Penyusun & Validator dari tingkat kegiatan
        $penyusun_nama = ! empty( $kegiatan->penyusun_nama ) 
            ? $kegiatan->penyusun_nama 
            : ( ! empty( $kegiatan->penyusun_id ) && ( $asesor_p = LSP_Cert_Integration::get_asesor( $kegiatan->penyusun_id ) ) ? $asesor_p['name'] : '' );

        $penyusun_met = ! empty( $kegiatan->penyusun_met ) 
            ? $kegiatan->penyusun_met 
            : ( ! empty( $kegiatan->penyusun_id ) && ( $asesor_p = LSP_Cert_Integration::get_asesor( $kegiatan->penyusun_id ) ) ? $asesor_p['no_reg'] : '' );

        $validator_nama = ! empty( $kegiatan->validator_nama ) 
            ? $kegiatan->validator_nama 
            : ( ! empty( $kegiatan->validator_id ) && ( $asesor_v = LSP_Cert_Integration::get_asesor( $kegiatan->validator_id ) ) ? $asesor_v['name'] : '' );

        $validator_met = ! empty( $kegiatan->validator_met ) 
            ? $kegiatan->validator_met 
            : ( ! empty( $kegiatan->validator_id ) && ( $asesor_v = LSP_Cert_Integration::get_asesor( $kegiatan->validator_id ) ) ? $asesor_v['no_reg'] : '' );

        $penyusun = array(
            'id'     => $kegiatan->penyusun_id ?? 0,
            'name'   => $penyusun_nama,
            'no_reg' => $penyusun_met,
        );

        $validator = array(
            'id'     => $kegiatan->validator_id ?? 0,
            'name'   => $validator_nama,
            'no_reg' => $validator_met,
        );

        $asesor = array(
            'id'     => $kegiatan->penyusun_id ?? 0,
            'name'   => $penyusun_nama,
            'no_reg' => $penyusun_met,
        );

        // Tidak ada identitas contoh: kosong tetap kosong, dengan peringatan untuk admin.
        $identitas_kosong = self::daftar_identitas_kosong( $penyusun, $validator );

        // Master Document Layout
        include LSP_CERT_PLUGIN_DIR . 'templates/docs/document-layout.php';

        if ( ! defined( 'LSP_CERT_TESTING' ) ) {
            exit;
        }
    }

    /**
     * Daftar label identitas tim asesor yang belum terisi pada data master.
     *
     * Dipakai untuk peringatan admin di halaman cetak. Tidak ada nama atau Nomor MET
     * contoh yang dipakai sebagai pengganti: field kosong tetap kosong.
     *
     * @param array $penyusun
     * @param array $validator
     * @return string[]
     */
    public static function daftar_identitas_kosong( $penyusun, $validator ) {
        $kosong = array();

        if ( '' === trim( (string) ( $penyusun['name'] ?? '' ) ) ) {
            $kosong[] = 'Nama Penyusun';
        }
        if ( '' === trim( (string) ( $penyusun['no_reg'] ?? '' ) ) ) {
            $kosong[] = 'Nomor MET Penyusun';
        }
        if ( '' === trim( (string) ( $validator['name'] ?? '' ) ) ) {
            $kosong[] = 'Nama Validator';
        }
        if ( '' === trim( (string) ( $validator['no_reg'] ?? '' ) ) ) {
            $kosong[] = 'Nomor MET Validator';
        }

        return $kosong;
    }

    /**
     * Gerbang kelengkapan FR.APL.02.
     *
     * Dokumen asesmen mandiri dengan kolom K/BK kosong tidak boleh dicetak:
     * begitu ditandatangani, formulir kosong menjadi bukti resmi bahwa asesi
     * tidak menyatakan apa pun. Cetak ditahan sampai seluruh elemen dijawab.
     *
     * @param int    $pendaftaran_id
     * @param string $doc_type
     */
    public static function enforce_apl02_completeness( $pendaftaran_id, $doc_type ) {
        $docs = array( 'apl02', 'all', 'bundle_all' );
        if ( ! in_array( $doc_type, $docs, true ) ) {
            return;
        }

        $kep = LSP_Cert_Integration::get_apl02_kelengkapan( $pendaftaran_id );

        if ( 0 === (int) $kep['total'] ) {
            return; // master belum punya elemen — sudah ditahan gerbang SKKNI
        }

        if ( $kep['lengkap'] ) {
            return;
        }

        $doc_labels = array(
            'apl02'      => 'FR.APL.02 (Asesmen Mandiri)',
            'all'        => 'FR.APL.01 & FR.APL.02',
            'bundle_all' => 'Seluruh Berkas Sertifikasi Asesi',
        );
        $label = isset( $doc_labels[ $doc_type ] ) ? $doc_labels[ $doc_type ] : strtoupper( $doc_type );

        $items = '';
        foreach ( array_slice( $kep['kosong'], 0, 10 ) as $k ) {
            $items .= '<li><strong style="font-family: monospace;">' . esc_html( $k['kode'] ) . '</strong> &mdash; Elemen '
                . (int) $k['nomor'] . ( '' !== $k['nama'] ? ': ' . esc_html( $k['nama'] ) : '' ) . '</li>';
        }
        if ( count( $kep['kosong'] ) > 10 ) {
            $items .= '<li>&hellip; dan ' . ( count( $kep['kosong'] ) - 10 ) . ' elemen lainnya</li>';
        }

        $sisa = (int) $kep['total'] - (int) $kep['terjawab'];

        wp_die(
            '<div style="max-width: 720px; margin: 40px auto; padding: 0 20px; font-family: Georgia, serif; color: #111827;">'
            . '<div style="border-left: 4px solid #b91c1c; background: #fef2f2; padding: 18px 22px; border-radius: 4px;">'
            . '<h1 style="margin: 0 0 6px; font-size: 20px; color: #991b1b;">FR.APL.02 Belum Lengkap</h1>'
            . '<p style="margin: 0; font-size: 13px; color: #7f1d1d;">Pencetakan <strong>' . esc_html( $label ) . '</strong> ditahan.</p>'
            . '</div>'
            . '<p style="font-size: 13.5px; line-height: 1.6; margin-top: 18px;">'
            . 'Asesmen mandiri baru terisi <strong>' . (int) $kep['terjawab'] . ' dari ' . (int) $kep['total'] . ' elemen</strong>'
            . ( $sisa > 0 ? ' &mdash; ' . $sisa . ' elemen masih kosong' : '' ) . '. '
            . 'Formulir asesmen mandiri yang kosong tidak boleh dicetak dan ditandatangani, karena akan menjadi '
            . 'bukti resmi bahwa asesi tidak menyatakan kompeten maupun belum kompeten.'
            . '</p>'
            . '<div style="background: #ffffff; border: 1px solid #fecaca; border-radius: 6px; padding: 12px 16px;">'
            . '<div style="font-size: 12.5px; font-weight: bold; color: #991b1b; margin-bottom: 6px;">Elemen yang belum dijawab:</div>'
            . '<ul style="margin: 0; padding-left: 20px; font-size: 12.5px; line-height: 1.6; color: #374151;">' . $items . '</ul>'
            . '</div>'
            . '<p style="font-size: 12.5px; color: #4b5563; margin-top: 16px;">'
            . 'Minta asesi melengkapi FR.APL.02 (setiap elemen dipilih <strong>K</strong> atau <strong>BK</strong>), '
            . 'lalu ulangi pencetakan.'
            . '</p>'
            . '</div>',
            'FR.APL.02 Belum Lengkap',
            array( 'response' => 409 )
        );
    }

    /**
     * Gerbang integritas SKKNI: hentikan pencetakan dokumen yang memuat Elemen/KUK
     * apabila master kompetensi belum lengkap.
     *
     * Menggantikan perilaku lama yang mengarang Elemen/KUK generik ketika data
     * SKKNI terstruktur tidak tersedia — data karangan tidak boleh masuk dokumen resmi.
     *
     * @param int    $skema_id
     * @param string $doc_type
     */
    public static function enforce_skkni_integrity( $skema_id, $doc_type ) {
        $docs_butuh_elemen = array( 'apl02', 'ia01', 'all', 'bundle_all' );
        if ( ! in_array( $doc_type, $docs_butuh_elemen, true ) ) {
            return;
        }

        $status = LSP_Cert_Integration::get_competency_completeness( $skema_id );
        if ( $status['is_complete'] ) {
            return;
        }

        $doc_labels = array(
            'apl02'      => 'FR.APL.02 (Asesmen Mandiri)',
            'ia01'       => 'FR.IA.01 (Ceklis Observasi Aktivitas Praktik)',
            'all'        => 'FR.APL.01 & FR.APL.02',
            'bundle_all' => 'Seluruh Berkas Sertifikasi Asesi',
        );
        $doc_label = isset( $doc_labels[ $doc_type ] ) ? $doc_labels[ $doc_type ] : strtoupper( $doc_type );

        if ( 0 === (int) $status['total_units'] ) {
            $alasan_utama = '<p style="font-size: 13.5px; line-height: 1.5; color: #374151;">'
                . 'Master Skema <strong>belum memiliki daftar Unit Kompetensi</strong>. '
                . 'Tanpa unit kompetensi, dokumen <strong>' . esc_html( $doc_label ) . '</strong> tidak dapat disusun.'
                . '</p>';
            $daftar = '';
        } else {
            $alasan_utama = '<p style="font-size: 13.5px; line-height: 1.5; color: #374151;">'
                . 'Dokumen <strong>' . esc_html( $doc_label ) . '</strong> memuat Elemen Kompetensi dan Kriteria Unjuk Kerja (KUK). '
                . 'Keduanya wajib berasal dari data SKKNI resmi pada Master Skema — tidak boleh diisi data pengganti.'
                . '</p>';

            $items = '';
            foreach ( $status['missing'] as $m ) {
                $items .= '<li><strong style="font-family: monospace;">' . esc_html( $m['kode'] ) . '</strong> &mdash; '
                    . esc_html( $m['judul'] ) . ' <span style="color:#b91c1c;">(' . esc_html( $m['alasan'] ) . ')</span></li>';
            }
            $daftar = '<div style="background: #ffffff; border: 1px solid #fecaca; border-radius: 6px; padding: 12px 16px; margin: 15px 0;">'
                . '<div style="font-size: 12.5px; font-weight: bold; color: #991b1b; margin-bottom: 6px;">'
                . 'Unit Kompetensi Tanpa Elemen/KUK (' . count( $status['missing'] ) . ' dari ' . (int) $status['total_units'] . ' unit):</div>'
                . '<ul style="margin: 0; padding-left: 20px; font-size: 12.5px; line-height: 1.6; color: #374151;">' . $items . '</ul>'
                . '</div>';
        }

        $admin_action = '';
        if ( current_user_can( 'manage_options' ) ) {
            $skema_url = admin_url( 'post.php?post=' . (int) $skema_id . '&action=edit' );
            $admin_action = '<p style="margin-top: 18px;">'
                . '<a href="' . esc_url( $skema_url ) . '" class="button button-primary" style="background:#0284c7; border-color:#0284c7; color:#fff; padding:6px 16px; text-decoration:none; border-radius:4px; font-weight:bold; display:inline-block;">'
                . '👉 Lengkapi Data SKKNI pada Master Skema'
                . '</a>'
                . '</p>';
        }

        $message = '<div style="font-family: Arial, sans-serif; max-width: 650px; margin: 30px auto; padding: 24px; border: 2px solid #ef4444; border-radius: 8px; background: #fff5f5; color: #991b1b; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">'
            . '<h2 style="margin-top: 0; color: #b91c1c; font-size: 18px;">🚫 Pencetakan Ditolak: Elemen/KUK Belum Lengkap</h2>'
            . $alasan_utama
            . '<p style="font-size: 13px; color: #4b5563;">'
            . '<strong>Status Master:</strong> '
            . (int) $status['units_lengkap'] . ' dari ' . (int) $status['total_units'] . ' unit lengkap'
            . ' &middot; ' . (int) $status['elemen'] . ' elemen'
            . ' &middot; ' . (int) $status['kuk'] . ' KUK'
            . '</p>'
            . $daftar
            . $admin_action
            . '<p style="margin-top: 20px; font-size: 12px; color: #6b7280; border-top: 1px solid #fecaca; padding-top: 10px;">'
            . 'Dokumen ditahan otomatis di sisi server agar dokumen resmi tidak memuat Elemen/KUK hasil karangan.'
            . '</p>'
            . '</div>';

        wp_die( $message, 'Pencetakan Ditolak — Elemen/KUK Belum Lengkap', array( 'response' => 403 ) );
    }

    /**
     * Validasi cakupan kelompok pekerjaan sebelum mencetak FR.IA.01, FR.IA.02, atau FR.IA.03
     * Menghentikan pencetakan final di sisi server jika cakupan belum 100%
     *
     * @param int    $skema_id
     * @param string $doc_type
     * @param int    $paket_muk_id
     */
    public static function enforce_kelompok_pekerjaan_coverage( $skema_id, $doc_type, $paket_muk_id = 0 ) {
        if ( ! in_array( $doc_type, array( 'ia01', 'ia02', 'ia03', 'bundle_all' ), true ) ) {
            return;
        }

        $coverage = LSP_Cert_Kelompok_Pekerjaan::validate_coverage( $skema_id, $paket_muk_id );
        if ( ! $coverage['is_complete'] ) {
            $unassigned_items = '';
            if ( ! empty( $coverage['unassigned_units'] ) ) {
                foreach ( $coverage['unassigned_units'] as $u ) {
                    $unassigned_items .= '<li><strong style="font-family: monospace;">' . esc_html( $u['kode'] ) . '</strong> &mdash; ' . esc_html( $u['judul'] ) . '</li>';
                }
            }

            $admin_action = '';
            if ( current_user_can( 'manage_options' ) ) {
                $settings_url = admin_url( 'admin.php?page=lsp-kelompok-pekerjaan&skema_id=' . $skema_id . ( $paket_muk_id ? '&paket_muk_id=' . $paket_muk_id : '' ) );
                $admin_action = '<p style="margin-top: 18px;">'
                    . '<a href="' . esc_url( $settings_url ) . '" class="button button-primary" style="background:#0284c7; border-color:#0284c7; color:#fff; padding:6px 16px; text-decoration:none; border-radius:4px; font-weight:bold; display:inline-block;">'
                    . '👉 Atur Kelompok Pekerjaan Sekarang'
                    . '</a>'
                    . '</p>';
            }

            $doc_labels = array(
                'ia01' => 'FR.IA.01 (Ceklis Observasi Aktivitas Praktik)',
                'ia02' => 'FR.IA.02 (Tugas Praktik Demonstrasi)',
                'ia03' => 'FR.IA.03 (Pertanyaan Mendukung Observasi)',
            );
            $doc_label = $doc_labels[ $doc_type ] ?? strtoupper( $doc_type );

            $message = '<div style="font-family: Arial, sans-serif; max-width: 650px; margin: 30px auto; padding: 24px; border: 2px solid #ef4444; border-radius: 8px; background: #fff5f5; color: #991b1b; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">'
                . '<h2 style="margin-top: 0; color: #b91c1c; font-size: 18px; display: flex; align-items: center; gap: 8px;">🚫 Pencetakan Dokumen Ditolak: Cakupan Belum 100%</h2>'
                . '<p style="font-size: 13.5px; line-height: 1.5; color: #374151;">'
                . 'Sesuai regulasi BNSP, dokumen <strong>' . esc_html( $doc_label ) . '</strong> hanya dapat dicetak apabila seluruh unit kompetensi dalam Master Skema telah terkelompokkan ke dalam Kelompok Pekerjaan.'
                . '</p>'
                . '<p style="font-size: 13px; color: #4b5563;">'
                . '<strong>Status Cakupan Saat Ini:</strong> <span style="color: #b91c1c; font-weight: bold;">' . esc_html( $coverage['coverage_percent'] ) . '%</span> (' . esc_html( $coverage['total_grouped_unique'] ) . ' dari ' . esc_html( $coverage['total_master'] ) . ' unit terkelompok).'
                . '</p>'
                . '<div style="background: #ffffff; border: 1px solid #fecaca; border-radius: 6px; padding: 12px 16px; margin: 15px 0;">'
                . '<div style="font-size: 12.5px; font-weight: bold; color: #991b1b; margin-bottom: 6px;">Unit Kompetensi yang Belum Dikelompokkan (' . count( $coverage['unassigned_units'] ) . ' Unit):</div>'
                . '<ul style="margin: 0; padding-left: 20px; font-size: 12.5px; line-height: 1.6; color: #374151;">' . $unassigned_items . '</ul>'
                . '</div>'
                . $admin_action
                . '<p style="margin-top: 20px; font-size: 12px; color: #6b7280; border-top: 1px solid #fecaca; padding-top: 10px;">'
                . 'Dokumen ditahan secara otomatis di sisi server untuk mencegah pencetakan dokumen yang kehilangan unit kompetensi.'
                . '</p>'
                . '</div>';

            wp_die( $message, 'Pencetakan Ditolak — Cakupan Belum 100%', array( 'response' => 403 ) );
        }
    }
}
