<?php
/**
 * Integration with LSP Core Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Integration {

    public static function init() {
        // Nothing to initialize for now
    }

    /**
     * Get all skema from core management
     */
    public static function get_all_skema() {
        $args = array(
            'post_type' => 'skema',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        $posts = get_posts( $args );
        $skema_list = array();
        
        foreach ( $posts as $post ) {
            $skema_list[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'kode' => get_post_meta( $post->ID, 'kode_skema', true ),
                'jenis' => get_post_meta( $post->ID, 'jenis_kemasan', true ),
                'level' => get_post_meta( $post->ID, 'level_skema', true ),
                'biaya' => get_post_meta( $post->ID, 'biaya_sertifikasi', true ),
            );
        }
        
        return $skema_list;
    }

    /**
     * Get skema by ID
     */
    public static function get_skema( $skema_id ) {
        $post = get_post( $skema_id );
        
        if ( ! $post || $post->post_type !== 'skema' ) {
            return null;
        }
        
        return array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'kode' => get_post_meta( $post->ID, 'kode_skema', true ),
            'jenis' => get_post_meta( $post->ID, 'jenis_kemasan', true ),
            'level' => get_post_meta( $post->ID, 'level_skema', true ),
            'biaya' => get_post_meta( $post->ID, 'biaya_sertifikasi', true ),
            'persyaratan' => get_post_meta( $post->ID, 'persyaratan_skema', true ),
            'persyaratan_admin' => get_post_meta( $post->ID, 'persyaratan_admin', true ),
            'acuan_skkni' => get_post_meta( $post->ID, 'acuan_skkni', true ),
            'unit_kompetensi' => get_post_meta( $post->ID, 'unit_kompetensi', true ),
        );
    }

    /**
     * Pecah teks persyaratan menjadi daftar baris.
     *
     * @param string $text
     * @return string[]
     */
    public static function parse_persyaratan_lines( $text ) {
        $list = array();

        if ( empty( $text ) || ! is_string( $text ) ) {
            return $list;
        }

        foreach ( explode( "\n", str_replace( "\r", "", $text ) ) as $line ) {
            $line = trim( $line );
            if ( '' !== $line ) {
                $list[] = $line;
            }
        }

        return $list;
    }

    /**
     * Get persyaratan dasar list.
     *
     * Persyaratan dasar bersifat per skema (ijazah, pengalaman, sertifikat pelatihan).
     * Tidak ada daftar bawaan: bila Master Skema belum mengisi, hasilnya kosong.
     *
     * @param int $skema_id
     * @return string[]
     */
    public static function get_persyaratan_dasar( $skema_id ) {
        return self::parse_persyaratan_lines( get_post_meta( $skema_id, 'persyaratan_skema', true ) );
    }

    /**
     * Get persyaratan administrasi list.
     *
     * Urutan sumber: meta skema → pengaturan global Core (berlaku untuk semua skema).
     * Tidak ada daftar bawaan di plugin ini.
     *
     * @param int $skema_id
     * @return string[]
     */
    public static function get_persyaratan_admin( $skema_id ) {
        $list = self::parse_persyaratan_lines( get_post_meta( $skema_id, 'persyaratan_admin', true ) );

        if ( empty( $list ) ) {
            if ( class_exists( 'LSP_Core_Management' ) && method_exists( 'LSP_Core_Management', 'get_persyaratan_admin_global' ) ) {
                $list = self::parse_persyaratan_lines( LSP_Core_Management::get_persyaratan_admin_global() );
            } else {
                $list = self::parse_persyaratan_lines( get_option( 'lsp_core_persyaratan_admin', '' ) );
            }
        }

        return $list;
    }

    /**
     * Get unit kompetensi from skema
     */
    public static function get_unit_kompetensi( $skema_id ) {
        // Cek data SKKNI terstruktur dari LSP Core Management atau LSP Core Skema
        $skkni_data = null;
        if ( class_exists( 'LSP_Core_Management' ) && method_exists( 'LSP_Core_Management', 'get_skema_skkni_data' ) ) {
            $skkni_data = LSP_Core_Management::get_skema_skkni_data( $skema_id );
        } elseif ( class_exists( 'LSP_Core_Skema' ) && method_exists( 'LSP_Core_Skema', 'get_skema_skkni_data' ) ) {
            $skkni_data = LSP_Core_Skema::get_skema_skkni_data( $skema_id );
        }

        if ( ! empty( $skkni_data ) && is_array( $skkni_data ) ) {
            $units = array();
            foreach ( $skkni_data as $u ) {
                $units[] = array(
                    'kode'      => $u['kode_unit'] ?? $u['kode'] ?? '',
                    'judul'     => $u['judul_unit'] ?? $u['judul'] ?? '',
                    'deskripsi' => $u['deskripsi_unit'] ?? $u['deskripsi'] ?? '',
                    'elemen'    => ! empty( $u['elemen'] ) && is_array( $u['elemen'] ) ? $u['elemen'] : array(),
                    'sumber'    => $u['sumber'] ?? 'skkni_terstruktur',
                );
            }
            return $units;
        }

        // Cadangan: daftar unit dari meta Master Skema.
        //
        // Yang diambil HANYA kode dan judul unit — keduanya memang data master.
        // Elemen dan KUK sengaja dibiarkan kosong: keduanya tidak boleh dikarang,
        // karena langsung tercetak di dokumen resmi asesmen (FR.APL.02, FR.IA.01).
        // Kelengkapan diverifikasi oleh get_competency_completeness() dan
        // dijaga oleh LSP_Cert_Document_Printer::enforce_skkni_integrity().
        $unit_text = get_post_meta( $skema_id, 'unit_kompetensi', true );

        if ( empty( $unit_text ) ) {
            return array();
        }

        $units = array();
        $lines = explode( "\n", str_replace( "\r", "", $unit_text ) );

        foreach ( $lines as $line ) {
            $line = trim( $line );
            if ( empty( $line ) ) {
                continue;
            }

            // Format: KODE | JUDUL
            $parts = explode( '|', $line );
            if ( count( $parts ) >= 2 ) {
                $units[] = array(
                    'kode'   => trim( $parts[0] ),
                    'judul'  => trim( $parts[1] ),
                    'elemen' => array(),
                    'sumber' => 'meta_skema',
                );
            }
        }

        return $units;
    }

    /**
     * Periksa kelengkapan data kompetensi (unit + elemen + KUK) untuk sebuah skema.
     *
     * Dipakai sebagai gate sebelum mencetak dokumen yang memuat Elemen/KUK.
     * Tidak ada lagi data pengganti: bila master tidak lengkap, pencetakan dihentikan.
     *
     * @param int $skema_id
     * @return array{is_complete:bool,total_units:int,units_lengkap:int,missing:array,elemen:int,kuk:int}
     */
    public static function get_competency_completeness( $skema_id ) {
        $units   = self::get_unit_kompetensi( $skema_id );
        $missing = array();
        $elemen_total = 0;
        $kuk_total    = 0;

        foreach ( $units as $u ) {
            $kode   = isset( $u['kode'] ) ? (string) $u['kode'] : '';
            $judul  = isset( $u['judul'] ) ? (string) $u['judul'] : '';
            $elemen = ! empty( $u['elemen'] ) && is_array( $u['elemen'] ) ? $u['elemen'] : array();

            $jumlah_kuk_unit = 0;
            foreach ( $elemen as $e ) {
                $elemen_total++;
                if ( ! empty( $e['kuk'] ) ) {
                    $jumlah_kuk_unit += count( (array) $e['kuk'] );
                }
            }
            $kuk_total += $jumlah_kuk_unit;

            if ( empty( $elemen ) ) {
                $missing[] = array(
                    'kode'   => $kode,
                    'judul'  => $judul,
                    'alasan' => 'Elemen & KUK belum tersedia',
                );
            } elseif ( 0 === $jumlah_kuk_unit ) {
                $missing[] = array(
                    'kode'   => $kode,
                    'judul'  => $judul,
                    'alasan' => 'Elemen ada, KUK belum tersedia',
                );
            }
        }

        return array(
            'is_complete'   => ( ! empty( $units ) && empty( $missing ) ),
            'total_units'   => count( $units ),
            'units_lengkap' => count( $units ) - count( $missing ),
            'missing'       => $missing,
            'elemen'        => $elemen_total,
            'kuk'           => $kuk_total,
        );
    }

    /**
     * Get all TUK from core management
     */
    public static function get_all_tuk() {
        $args = array(
            'post_type' => 'tuk',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        $posts = get_posts( $args );
        $tuk_list = array();
        
        foreach ( $posts as $post ) {
            $tuk_list[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'kode' => get_post_meta( $post->ID, 'kode_tuk', true ),
                'jenis' => get_post_meta( $post->ID, 'jenis_tuk', true ),
                'alamat' => get_post_meta( $post->ID, 'alamat_tuk', true ),
                'kota' => get_post_meta( $post->ID, 'kota_tuk', true ),
                'provinsi' => get_post_meta( $post->ID, 'provinsi_tuk', true ),
                'telepon' => get_post_meta( $post->ID, 'telepon_tuk', true ),
                'email' => get_post_meta( $post->ID, 'email_tuk', true ),
            );
        }
        
        return $tuk_list;
    }

    /**
     * Get TUK by ID
     */
    public static function get_tuk( $tuk_id ) {
        $post = get_post( $tuk_id );
        
        if ( ! $post || $post->post_type !== 'tuk' ) {
            return null;
        }
        
        return array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'kode' => get_post_meta( $post->ID, 'kode_tuk', true ),
            'jenis' => get_post_meta( $post->ID, 'jenis_tuk', true ),
            'alamat' => get_post_meta( $post->ID, 'alamat_tuk', true ),
            'kota' => get_post_meta( $post->ID, 'kota_tuk', true ),
            'provinsi' => get_post_meta( $post->ID, 'provinsi_tuk', true ),
            'telepon' => get_post_meta( $post->ID, 'telepon_tuk', true ),
            'email' => get_post_meta( $post->ID, 'email_tuk', true ),
        );
    }

    /**
     * Normalisasi jenis TUK secara aman (Sewaktu / Tempat Kerja / Mandiri)
     * Mengembalikan salah satu dari: 'sewaktu', 'tempat_kerja', 'mandiri', atau null jika kosong/tidak dikenal
     *
     * @param string $raw_jenis
     * @return string|null
     */
    public static function normalize_jenis_tuk( $raw_jenis ) {
        if ( empty( $raw_jenis ) || ! is_string( $raw_jenis ) ) {
            return null;
        }

        $clean = strtolower( trim( $raw_jenis ) );
        if ( strpos( $clean, 'sewaktu' ) !== false ) {
            return 'sewaktu';
        }
        if ( strpos( $clean, 'tempat kerja' ) !== false || strpos( $clean, 'tempat_kerja' ) !== false ) {
            return 'tempat_kerja';
        }
        if ( strpos( $clean, 'mandiri' ) !== false ) {
            return 'mandiri';
        }

        return null;
    }

    /**
     * Get all asesor from core management
     */
    public static function get_all_asesor() {
        $args = array(
            'role' => 'asesor',
            'orderby' => 'display_name',
            'order' => 'ASC'
        );
        
        $users = get_users( $args );
        $asesor_list = array();
        
        foreach ( $users as $user ) {
            $no_reg = get_user_meta( $user->ID, 'nomor_met', true )
                   ?: get_user_meta( $user->ID, 'no_registrasi_met', true )
                   ?: get_user_meta( $user->ID, 'no_met', true )
                   ?: get_user_meta( $user->ID, 'no_reg', true );

            $asesor_list[] = array(
                'id'     => $user->ID,
                'name'   => $user->display_name,
                'email'  => $user->user_email,
                'bidang' => get_user_meta( $user->ID, 'bidang_keahlian', true ),
                'no_reg' => $no_reg,
            );
        }
        
        return $asesor_list;
    }

    /**
     * Get asesor by ID
     */
    public static function get_asesor( $asesor_id ) {
        $user = get_user_by( 'id', $asesor_id );
        
        if ( ! $user || ! in_array( 'asesor', $user->roles ) ) {
            return null;
        }

        $no_reg = get_user_meta( $user->ID, 'nomor_met', true )
               ?: get_user_meta( $user->ID, 'no_registrasi_met', true )
               ?: get_user_meta( $user->ID, 'no_met', true )
               ?: get_user_meta( $user->ID, 'no_reg', true );
        
        return array(
            'id'     => $user->ID,
            'name'   => $user->display_name,
            'email'  => $user->user_email,
            'bidang' => get_user_meta( $user->ID, 'bidang_keahlian', true ),
            'no_reg' => $no_reg,
        );
    }

    /**
     * Periksa apakah user ID tertentu memiliki role asesor
     *
     * @param int|null $user_id
     * @return bool
     */
    public static function is_asesor( $user_id = null ) {
        if ( null === $user_id ) {
            $user_id = get_current_user_id();
        }
        if ( ! $user_id ) {
            return false;
        }
        $user = get_user_by( 'id', $user_id );
        return ( $user && in_array( 'asesor', (array) $user->roles, true ) );
    }
}
