<?php
/**
 * Capability model LSP Certification Management.
 *
 * Menggantikan pemakaian role slug sebagai capability (`current_user_can( 'asesor' )`),
 * yang selalu bernilai false karena Core mendefinisikan `asesor` sebagai role, bukan
 * capability.
 *
 * Peta hak akses:
 *   administrator → keempat capability
 *   asesor        → lsp_cert_view_docs, lsp_cert_print_docs
 *   lainnya       → tidak ada
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Capabilities {

    /** Naikkan versi ini bila peta capability berubah, agar grant ulang berjalan sekali. */
    const VERSION = '1.0.0';

    const CAP_MANAGE_ACTIVITIES  = 'lsp_cert_manage_activities';
    const CAP_MANAGE_INSTRUMENTS = 'lsp_cert_manage_instruments';
    const CAP_VIEW_DOCS          = 'lsp_cert_view_docs';
    const CAP_PRINT_DOCS         = 'lsp_cert_print_docs';

    /**
     * Seluruh capability milik plugin.
     *
     * @return string[]
     */
    public static function all_caps() {
        return array(
            self::CAP_MANAGE_ACTIVITIES,
            self::CAP_MANAGE_INSTRUMENTS,
            self::CAP_VIEW_DOCS,
            self::CAP_PRINT_DOCS,
        );
    }

    public static function init() {
        self::maybe_grant();
        add_filter( 'user_has_cap', array( __CLASS__, 'allow_print_route' ), 10, 3 );
    }

    /**
     * Izinkan pemegang lsp_cert_print_docs melewati gerbang halaman pengelola
     * HANYA untuk request cetak (action=print) pada URL yang sudah dipakai
     * tautan cetak yang ada.
     *
     * Handler cetak selalu berakhir sendiri (`render_print_page()` berakhir dengan
     * exit, atau wp_die bila data tidak valid), sehingga halaman daftar tidak pernah
     * terbuka lewat izin ini.
     *
     * @param array  $allcaps
     * @param array  $caps
     * @param array  $args
     * @return array
     */
    public static function allow_print_route( $allcaps, $caps, $args ) {
        if ( empty( $args[0] ) || self::CAP_MANAGE_ACTIVITIES !== $args[0] ) {
            return $allcaps;
        }
        if ( empty( $allcaps[ self::CAP_PRINT_DOCS ] ) ) {
            return $allcaps;
        }
        if ( ! is_admin() || ! isset( $_GET['page'], $_GET['action'] ) ) {
            return $allcaps;
        }
        if ( 'print' !== $_GET['action'] ) {
            return $allcaps;
        }

        $page = sanitize_key( wp_unslash( $_GET['page'] ) );
        if ( in_array( $page, array( 'lsp-pendaftaran', 'lsp-sertifikasi', 'lsp-kegiatan' ), true ) ) {
            $allcaps[ self::CAP_MANAGE_ACTIVITIES ] = true;
        }

        return $allcaps;
    }

    /**
     * Berikan capability ke role sekali per versi.
     */
    public static function maybe_grant() {
        if ( get_option( 'lsp_cert_caps_version' ) === self::VERSION ) {
            return;
        }
        self::grant();
        update_option( 'lsp_cert_caps_version', self::VERSION );
    }

    /**
     * Terapkan peta capability ke role. Idempoten.
     */
    public static function grant() {
        if ( ! function_exists( 'get_role' ) ) {
            return;
        }

        $admin = get_role( 'administrator' );
        if ( $admin ) {
            foreach ( self::all_caps() as $cap ) {
                $admin->add_cap( $cap );
            }
        }

        $asesor = get_role( 'asesor' );
        if ( $asesor ) {
            $asesor->add_cap( self::CAP_VIEW_DOCS );
            $asesor->add_cap( self::CAP_PRINT_DOCS );
            $asesor->remove_cap( self::CAP_MANAGE_ACTIVITIES );
            $asesor->remove_cap( self::CAP_MANAGE_INSTRUMENTS );
        }
    }

    /**
     * Apakah pengguna boleh mengelola kegiatan & pendaftaran.
     *
     * @return bool
     */
    public static function can_manage_activities() {
        return current_user_can( self::CAP_MANAGE_ACTIVITIES );
    }

    /**
     * Apakah pengguna boleh mengelola instrumen asesmen (IA, kelompok pekerjaan, paket MUK).
     *
     * @return bool
     */
    public static function can_manage_instruments() {
        return current_user_can( self::CAP_MANAGE_INSTRUMENTS );
    }

    /**
     * Apakah pengguna boleh melihat dokumen.
     *
     * @return bool
     */
    public static function can_view_docs() {
        return current_user_can( self::CAP_VIEW_DOCS );
    }

    /**
     * Apakah pengguna boleh mencetak dokumen.
     *
     * @return bool
     */
    public static function can_print_docs() {
        return current_user_can( self::CAP_PRINT_DOCS );
    }
}
