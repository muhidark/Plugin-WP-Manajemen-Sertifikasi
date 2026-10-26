<?php
/**
 * Admin Menu & Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Admin {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
        add_action( 'admin_menu', array( __CLASS__, 'filter_submenus' ), 999 );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_filter( 'parent_file', array( __CLASS__, 'fix_parent_menu_highlight' ) );
        add_filter( 'submenu_file', array( __CLASS__, 'fix_submenu_highlight' ) );
    }

    public static function register_menus() {
        // Pengguna yang hanya berhak mencetak dokumen (mis. asesor) mendapat menu
        // sendiri, sehingga tidak pernah melihat halaman pengelolaan kegiatan.
        if ( ! current_user_can( 'lsp_cert_manage_activities' ) ) {
            if ( current_user_can( 'lsp_cert_print_docs' ) ) {
                add_menu_page(
                    'Cetak Dokumen Sertifikasi',
                    'Cetak Dokumen',
                    'lsp_cert_print_docs',
                    'lsp-cetak-dokumen',
                    array( 'LSP_Cert_Cetak_Dokumen', 'admin_page' ),
                    'dashicons-printer',
                    30
                );
            }
            return;
        }

        // Main menu
        add_menu_page(
            'Manajemen Dokumen Sertifikasi',
            'Manajemen Dokumen Sertifikasi',
            'lsp_cert_view_docs',
            'lsp-sertifikasi',
            array( __CLASS__, 'route_default_page' ),
            'dashicons-clipboard',
            30
        );

        // Submenu 1: Jadwal (kegiatan)
        // Capability disamakan dengan menu induk karena slug-nya sama ('lsp-sertifikasi');
        // pembatasan pengelolaan kegiatan ditegakkan di LSP_Cert_Kegiatan::admin_page().
        add_submenu_page(
            'lsp-sertifikasi',
            'Jadwal Sertifikasi',
            'Jadwal',
            'lsp_cert_view_docs',
            'lsp-sertifikasi',
            array( 'LSP_Cert_Kegiatan', 'admin_page' )
        );

        // Submenu 2: Pendaftaran
        add_submenu_page(
            'lsp-sertifikasi',
            'Pendaftaran',
            'Pendaftaran',
            'lsp_cert_manage_activities',
            'lsp-pendaftaran',
            array( 'LSP_Cert_Pendaftaran', 'admin_page' )
        );

        // Submenu 3: Cetak Dokumen
        add_submenu_page(
            'lsp-sertifikasi',
            'Cetak Dokumen Asesmen',
            'Cetak Dokumen',
            'lsp_cert_print_docs',
            'lsp-cetak-dokumen',
            array( 'LSP_Cert_Cetak_Dokumen', 'admin_page' )
        );

        // Hidden submenus (registered with parent null so they don't clutter the sidebar, but remain fully accessible)
        // Alias compatibility: lsp-kegiatan
        add_submenu_page(
            null,
            'Jadwal Sertifikasi',
            'Jadwal Sertifikasi',
            'lsp_cert_manage_activities',
            'lsp-kegiatan',
            array( 'LSP_Cert_Kegiatan', 'admin_page' )
        );

        // Submenu: Instrumen FR.IA.02
        add_submenu_page(
            'lsp-sertifikasi',
            'Instrumen FR.IA.02',
            'Tugas Praktik FR.IA.02',
            'lsp_cert_manage_instruments',
            'lsp-ia02',
            array( 'LSP_Cert_IA02', 'admin_page' )
        );

        // Submenu: Instrumen FR.IA.03
        add_submenu_page(
            'lsp-sertifikasi',
            'Instrumen FR.IA.03',
            'Pertanyaan FR.IA.03',
            'lsp_cert_manage_instruments',
            'lsp-ia03',
            array( 'LSP_Cert_IA03', 'admin_page' )
        );

        // Submenu: Instrumen FR.IA.04
        add_submenu_page(
            'lsp-sertifikasi',
            'Instrumen FR.IA.04',
            'Materi FR.IA.04 (DIT)',
            'lsp_cert_manage_instruments',
            'lsp-ia04',
            array( 'LSP_Cert_IA04', 'admin_page' )
        );

        // Submenu: Instrumen FR.IA.05
        add_submenu_page(
            'lsp-sertifikasi',
            'Instrumen FR.IA.05',
            'Soal FR.IA.05 (PG)',
            'lsp_cert_manage_instruments',
            'lsp-ia05',
            array( 'LSP_Cert_IA05', 'admin_page' )
        );

        // Submenu: Kelompok Pekerjaan (FR.IA.01, FR.IA.02, FR.IA.03)
        add_submenu_page(
            'lsp-sertifikasi',
            'Kelompok Pekerjaan',
            'Kelompok Pekerjaan',
            'lsp_cert_manage_instruments',
            'lsp-kelompok-pekerjaan',
            array( 'LSP_Cert_Kelompok_Pekerjaan', 'admin_page' )
        );
    }

    /**
     * Memastikan nama menu utama Manajemen Dokumen Sertifikasi rapi
     */
    public static function filter_submenus() {
        global $menu;

        // Pastikan nama menu utama Manajemen Dokumen Sertifikasi
        if ( is_array( $menu ) ) {
            foreach ( $menu as $k => $m ) {
                if ( isset( $m[2] ) && 'lsp-sertifikasi' === $m[2] ) {
                    $menu[ $k ][0] = 'Manajemen Dokumen Sertifikasi';
                    break;
                }
            }
        }
    }

    /**
     * Pastikan menu utama tetap tersorot aktif untuk subpage tersembunyi
     */
    public static function fix_parent_menu_highlight( $parent_file ) {
        global $plugin_page;
        if ( in_array( $plugin_page, array( 'lsp-sertifikasi', 'lsp-kegiatan', 'lsp-ia02', 'lsp-ia03', 'lsp-ia04', 'lsp-ia05', 'lsp-kelompok-pekerjaan' ), true ) ) {
            return 'lsp-sertifikasi';
        }
        return $parent_file;
    }

    /**
     * Pastikan submenu yang tepat tetap tersorot aktif
     */
    public static function fix_submenu_highlight( $submenu_file ) {
        global $plugin_page;
        if ( 'lsp-kegiatan' === $plugin_page ) {
            return 'lsp-sertifikasi';
        }
        if ( in_array( $plugin_page, array( 'lsp-ia02', 'lsp-ia03', 'lsp-ia04', 'lsp-ia05', 'lsp-kelompok-pekerjaan' ), true ) ) {
            return 'lsp-cetak-dokumen';
        }
        return $submenu_file;
    }

    /**
     * Halaman pendarat menu utama "Manajemen Dokumen Sertifikasi".
     *
     * Administrator (punya lsp_cert_manage_activities) langsung masuk ke Jadwal.
     * Pengguna yang hanya berhak mencetak dokumen (mis. asesor) diarahkan ke
     * halaman Cetak Dokumen, sehingga submenu kegiatan tidak perlu dibuka.
     */
    public static function route_default_page() {
        if ( current_user_can( 'lsp_cert_manage_activities' ) ) {
            LSP_Cert_Kegiatan::admin_page();
            return;
        }

        if ( current_user_can( 'lsp_cert_print_docs' ) ) {
            wp_safe_redirect( admin_url( 'admin.php?page=lsp-cetak-dokumen' ) );
            exit;
        }

        wp_die( 'Anda tidak memiliki hak akses ke halaman ini.', 'Akses Ditolak', array( 'response' => 403 ) );
    }

    public static function page_dashboard() {
        $total_kegiatan = count( LSP_Cert_Database::get_all_kegiatan() );
        $total_pendaftaran = count( LSP_Cert_Database::get_all_pendaftaran() );
        $pending_verifikasi = count( LSP_Cert_Database::get_all_pendaftaran( array( 'status_kelengkapan' => 'pending' ) ) );
        $disetujui = count( LSP_Cert_Database::get_all_pendaftaran( array( 'status_rekomendasi' => 'disetujui' ) ) );
        
        include LSP_CERT_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    public static function enqueue_scripts( $hook ) {
        // Only load on our plugin pages
        if ( strpos( $hook, 'lsp-' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'lsp-cert-admin',
            LSP_CERT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            LSP_CERT_VERSION
        );

        wp_enqueue_script(
            'lsp-cert-admin',
            LSP_CERT_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            LSP_CERT_VERSION,
            true
        );

        wp_localize_script( 'lsp-cert-admin', 'lspCert', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'lsp_cert_nonce' ),
        ));
    }
}
