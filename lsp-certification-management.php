<?php
/**
 * Plugin Name: LSP Certification Management
 * Plugin URI:  https://lspkomunikasi.id/
 * Description: Plugin untuk manajemen proses sertifikasi kompetensi: pendaftaran, APL 01, APL 02, verifikasi admin, penugasan asesor, dan pencetakan dokumen.
 * Version:     1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author:      Muhadi Ganteng
 * Author URI:  https://instagram.com/yuk.sepedaan
 * Text Domain: lsp-certification-management
 * License:     GPLv2 or later
 * Depends:     lsp-core-management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'LSP_CERT_VERSION', '1.0.0' );
define( 'LSP_CERT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LSP_CERT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LSP_CERT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Check if Core Management is active
function lsp_cert_check_dependencies() {
    if ( ! is_plugin_active( 'lsp-core-management/lsp-core-management.php' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error"><p>';
            echo '<strong>LSP Certification Management</strong> memerlukan <strong>LSP Core Management</strong> untuk aktif. Silakan aktifkan plugin tersebut terlebih dahulu.';
            echo '</p></div>';
        });
        return false;
    }
    return true;
}

// Initialize plugin
function lsp_cert_init() {
    if ( ! lsp_cert_check_dependencies() ) {
        return;
    }

    // Load includes
    require_once LSP_CERT_PLUGIN_DIR . 'includes/class-activator.php';
    require_once LSP_CERT_PLUGIN_DIR . 'includes/class-database.php';
    require_once LSP_CERT_PLUGIN_DIR . 'includes/class-integration.php';
    require_once LSP_CERT_PLUGIN_DIR . 'includes/class-ia02.php';
    require_once LSP_CERT_PLUGIN_DIR . 'includes/class-ia03.php';
    require_once LSP_CERT_PLUGIN_DIR . 'includes/class-ia04.php';
    require_once LSP_CERT_PLUGIN_DIR . 'includes/class-ia05.php';
    require_once LSP_CERT_PLUGIN_DIR . 'includes/class-kelompok-pekerjaan.php';
    require_once LSP_CERT_PLUGIN_DIR . 'includes/class-paket-muk.php';
    require_once LSP_CERT_PLUGIN_DIR . 'includes/class-document-printer.php';
    
    // Load admin
    if ( is_admin() ) {
        require_once LSP_CERT_PLUGIN_DIR . 'admin/class-admin.php';
        require_once LSP_CERT_PLUGIN_DIR . 'admin/class-kegiatan.php';
        require_once LSP_CERT_PLUGIN_DIR . 'admin/class-pendaftaran.php';
        require_once LSP_CERT_PLUGIN_DIR . 'admin/class-cetak-dokumen.php';
    }
    
    // Load public
    require_once LSP_CERT_PLUGIN_DIR . 'public/class-public.php';
    require_once LSP_CERT_PLUGIN_DIR . 'public/class-form-handler.php';
    
    // Initialize classes
    LSP_Cert_Activator::init();
    LSP_Cert_Database::init();
    LSP_Cert_Integration::init();
    LSP_Cert_IA02::init();
    LSP_Cert_IA03::init();
    LSP_Cert_IA04::init();
    LSP_Cert_IA05::init();
    LSP_Cert_Kelompok_Pekerjaan::init();
    LSP_Cert_Paket_MUK::init();
    
    if ( is_admin() ) {
        LSP_Cert_Admin::init();
        LSP_Cert_Kegiatan::init();
        LSP_Cert_Pendaftaran::init();
        LSP_Cert_Cetak_Dokumen::init();
    }
    
    LSP_Cert_Public::init();
    LSP_Cert_Form_Handler::init();
}
add_action( 'plugins_loaded', 'lsp_cert_init' );

// Activation hook
function lsp_cert_activate() {
    require_once LSP_CERT_PLUGIN_DIR . 'includes/class-activator.php';
    LSP_Cert_Activator::activate();
}
register_activation_hook( __FILE__, 'lsp_cert_activate' );

// Deactivation hook
function lsp_cert_deactivate() {
    // Cleanup if needed
}
register_deactivation_hook( __FILE__, 'lsp_cert_deactivate' );
