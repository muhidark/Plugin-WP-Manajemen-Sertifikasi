<?php
/**
 * Public-Facing Functionality
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Public {

    public static function init() {
        add_shortcode( 'lsp_sertifikasi_daftar', array( __CLASS__, 'shortcode_daftar_kegiatan' ) );
        add_shortcode( 'lsp_sertifikasi_form', array( __CLASS__, 'shortcode_form_pendaftaran' ) );
        
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
    }

    public static function shortcode_daftar_kegiatan( $atts ) {
        $atts = shortcode_atts( array(
            'limit' => -1,
        ), $atts );
        
        // Check if kegiatan_id is in URL
        if ( isset( $_GET['kegiatan_id'] ) ) {
            $kegiatan_id = intval( $_GET['kegiatan_id'] );
            
            // Render form instead of list
            $kegiatan = LSP_Cert_Database::get_kegiatan( $kegiatan_id );
            
            if ( ! $kegiatan || $kegiatan->status !== 'dibuka' ) {
                return '<div class="lsp-notice error">Kegiatan tidak tersedia atau sudah ditutup.</div>';
            }
            
            $skema = LSP_Cert_Integration::get_skema( $kegiatan->skema_id );
            $tuk = LSP_Cert_Integration::get_tuk( $kegiatan->tuk_id );
            $unit_kompetensi = LSP_Cert_Integration::get_unit_kompetensi( $kegiatan->skema_id );
            
            ob_start();
            include LSP_CERT_PLUGIN_DIR . 'public/views/form-pendaftaran.php';
            return ob_get_clean();
        }
        
        $kegiatan_list = LSP_Cert_Database::get_kegiatan_tersedia();
        
        if ( $atts['limit'] > 0 ) {
            $kegiatan_list = array_slice( $kegiatan_list, 0, $atts['limit'] );
        }
        
        ob_start();
        include LSP_CERT_PLUGIN_DIR . 'public/views/daftar-kegiatan.php';
        return ob_get_clean();
    }

    public static function shortcode_form_pendaftaran( $atts ) {
        $atts = shortcode_atts( array(
            'kegiatan_id' => 0,
        ), $atts );
        
        $kegiatan_id = $atts['kegiatan_id'];
        
        // If no kegiatan_id specified, check URL parameter
        if ( ! $kegiatan_id && isset( $_GET['kegiatan_id'] ) ) {
            $kegiatan_id = intval( $_GET['kegiatan_id'] );
        }
        
        if ( ! $kegiatan_id ) {
            return '<div class="lsp-notice error">Kegiatan tidak ditemukan. Silakan pilih kegiatan terlebih dahulu.</div>';
        }
        
        $kegiatan = LSP_Cert_Database::get_kegiatan( $kegiatan_id );
        
        if ( ! $kegiatan || $kegiatan->status !== 'dibuka' ) {
            return '<div class="lsp-notice error">Kegiatan tidak tersedia atau sudah ditutup.</div>';
        }
        
        $skema = LSP_Cert_Integration::get_skema( $kegiatan->skema_id );
        $tuk = LSP_Cert_Integration::get_tuk( $kegiatan->tuk_id );
        $unit_kompetensi = LSP_Cert_Integration::get_unit_kompetensi( $kegiatan->skema_id );
        
        ob_start();
        include LSP_CERT_PLUGIN_DIR . 'public/views/form-pendaftaran.php';
        return ob_get_clean();
    }

    public static function enqueue_scripts() {
        global $post;
        
        if ( is_a( $post, 'WP_Post' ) && ( has_shortcode( $post->post_content, 'lsp_sertifikasi_daftar' ) || has_shortcode( $post->post_content, 'lsp_sertifikasi_form' ) ) ) {
            wp_enqueue_style(
                'lsp-cert-public',
                LSP_CERT_PLUGIN_URL . 'assets/css/public.css',
                array(),
                LSP_CERT_VERSION
            );
            
            wp_enqueue_script(
                'lsp-cert-public',
                LSP_CERT_PLUGIN_URL . 'assets/js/public.js',
                array( 'jquery' ),
                LSP_CERT_VERSION,
                true
            );
            
            wp_localize_script( 'lsp-cert-public', 'lspCertPublic', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'lsp_cert_public_nonce' ),
            ));
        }
    }
}
