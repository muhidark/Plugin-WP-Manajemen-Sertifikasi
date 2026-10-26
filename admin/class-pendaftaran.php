<?php
/**
 * Pendaftaran Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Pendaftaran {

    public static function init() {
        // Handle print action before headers are sent
        add_action( 'admin_init', array( __CLASS__, 'handle_print_action' ) );

        // AJAX handlers
        add_action( 'wp_ajax_lsp_cert_save_verifikasi', array( __CLASS__, 'ajax_save_verifikasi' ) );
        add_action( 'wp_ajax_lsp_cert_update_kelengkapan', array( __CLASS__, 'ajax_update_kelengkapan' ) );
        add_action( 'wp_ajax_lsp_cert_update_rekomendasi', array( __CLASS__, 'ajax_update_rekomendasi' ) );
    }

    /**
     * Intercept request untuk cetak dokumen sertifikasi (APL 01, APL 02, dsb)
     */
    public static function handle_print_action() {
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'lsp-pendaftaran' && isset( $_GET['action'] ) && $_GET['action'] === 'print' ) {
            $id  = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
            $doc = isset( $_GET['doc'] ) ? sanitize_key( $_GET['doc'] ) : 'all';

            if ( ! $id ) {
                wp_die( 'ID pendaftaran tidak valid.', 'Error', array( 'response' => 400 ) );
            }

            LSP_Cert_Document_Printer::render_print_page( $id, $doc );
        }
    }

    public static function admin_page() {
        if ( ! current_user_can( 'lsp_cert_manage_activities' ) ) {
            wp_die( 'Anda tidak memiliki hak akses ke halaman ini.', 'Akses Ditolak', array( 'response' => 403 ) );
        }

        $action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';
        
        switch ( $action ) {
            case 'view':
                self::render_detail();
                break;
            default:
                self::render_list();
                break;
        }
    }

    private static function render_list() {
        $kegiatan_id = isset( $_GET['kegiatan_id'] ) ? intval( $_GET['kegiatan_id'] ) : 0;
        $status_filter = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';
        
        $args = array();
        if ( $kegiatan_id ) {
            $args['kegiatan_id'] = $kegiatan_id;
        }
        if ( $status_filter ) {
            $args['status_kelengkapan'] = $status_filter;
        }
        
        $pendaftaran_list = LSP_Cert_Database::get_all_pendaftaran( $args );
        $kegiatan_list = LSP_Cert_Database::get_all_kegiatan();
        
        include LSP_CERT_PLUGIN_DIR . 'admin/views/pendaftaran-list.php';
    }

    private static function render_detail() {
        $id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
        $pendaftaran = LSP_Cert_Database::get_pendaftaran( $id );
        
        if ( ! $pendaftaran ) {
            echo '<div class="notice notice-error"><p>Pendaftaran tidak ditemukan.</p></div>';
            return;
        }
        
        $kegiatan = LSP_Cert_Database::get_kegiatan( $pendaftaran->kegiatan_id );
        $skema = LSP_Cert_Integration::get_skema( $kegiatan->skema_id );
        $tuk = LSP_Cert_Integration::get_tuk( $kegiatan->tuk_id );
        $asesor_list = LSP_Cert_Integration::get_all_asesor();
        $apl02_jawaban = LSP_Cert_Database::get_apl02_jawaban( $id );
        $all_paket_muk = ( class_exists( 'LSP_Cert_Paket_MUK' ) && ! empty( $kegiatan->skema_id ) ) 
            ? LSP_Cert_Paket_MUK::get_paket_by_skema( $kegiatan->skema_id ) 
            : array();
        
        include LSP_CERT_PLUGIN_DIR . 'admin/views/pendaftaran-detail.php';
    }

    public static function ajax_save_verifikasi() {
        check_ajax_referer( 'lsp_cert_nonce', 'nonce' );

        if ( ! current_user_can( 'lsp_cert_manage_activities' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }

        $id = intval( $_POST['id'] ?? 0 );
        if ( ! $id ) {
            wp_send_json_error( 'ID pendaftaran tidak valid' );
        }

        $status_kelengkapan = sanitize_text_field( $_POST['status_kelengkapan'] ?? 'pending' );
        $status_rekomendasi = sanitize_text_field( $_POST['status_rekomendasi'] ?? 'pending' );
        $asesor_id          = intval( $_POST['asesor_id'] ?? 0 );
        $paket_muk_id       = intval( $_POST['paket_muk_id'] ?? 0 );
        $catatan            = sanitize_textarea_field( $_POST['catatan'] ?? '' );

        $data = array(
            'status_kelengkapan' => $status_kelengkapan,
            'status_rekomendasi' => $status_rekomendasi,
            'paket_muk_id'       => $paket_muk_id ?: null,
            'catatan_admin'      => $catatan,
        );

        if ( $asesor_id ) {
            $data['asesor_id'] = $asesor_id;
            $asesor_obj = LSP_Cert_Integration::get_asesor( $asesor_id );
            if ( $asesor_obj ) {
                $data['penyusun_nama'] = $asesor_obj['name'] ?? '';
                $data['penyusun_met']  = $asesor_obj['no_reg'] ?? '';
            }
        } else {
            $data['asesor_id'] = null;
        }

        if ( $status_rekomendasi === 'disetujui' ) {
            $data['tanggal_persetujuan'] = current_time( 'mysql' );
        }

        LSP_Cert_Database::update_pendaftaran( $id, $data );
        wp_send_json_success( array( 'message' => 'Verifikasi dan Rekomendasi Asesmen berhasil disimpan' ) );
    }

    public static function ajax_update_kelengkapan() {
        check_ajax_referer( 'lsp_cert_nonce', 'nonce' );
        
        if ( ! current_user_can( 'lsp_cert_manage_activities' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }
        
        $id = intval( $_POST['id'] );
        $status = sanitize_text_field( $_POST['status'] );
        $catatan = sanitize_textarea_field( $_POST['catatan'] ?? '' );
        
        $data = array(
            'status_kelengkapan' => $status,
        );
        
        if ( $catatan ) {
            $data['catatan_admin'] = $catatan;
        }
        
        LSP_Cert_Database::update_pendaftaran( $id, $data );
        wp_send_json_success( array( 'message' => 'Status kelengkapan berhasil diupdate' ) );
    }

    public static function ajax_update_rekomendasi() {
        check_ajax_referer( 'lsp_cert_nonce', 'nonce' );
        
        if ( ! current_user_can( 'lsp_cert_manage_activities' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }
        
        $id = intval( $_POST['id'] );
        $status = sanitize_text_field( $_POST['status'] );
        $asesor_id = intval( $_POST['asesor_id'] ?? 0 );
        $penyusun_nama = sanitize_text_field( $_POST['penyusun_nama'] ?? '' );
        $penyusun_met  = sanitize_text_field( $_POST['penyusun_met'] ?? '' );
        $validator_id  = intval( $_POST['validator_id'] ?? 0 );
        $validator_nama = sanitize_text_field( $_POST['validator_nama'] ?? '' );
        $validator_met  = sanitize_text_field( $_POST['validator_met'] ?? '' );
        $paket_muk_id   = isset( $_POST['paket_muk_id'] ) ? intval( $_POST['paket_muk_id'] ) : 0;
        
        $data = array(
            'status_rekomendasi' => $status,
            'penyusun_nama'      => $penyusun_nama,
            'penyusun_met'       => $penyusun_met,
            'validator_id'       => $validator_id ?: null,
            'validator_nama'     => $validator_nama,
            'validator_met'      => $validator_met,
        );

        if ( isset( $_POST['paket_muk_id'] ) ) {
            $data['paket_muk_id'] = $paket_muk_id ?: null;
        }
        
        if ( $status === 'disetujui' ) {
            $data['asesor_id'] = $asesor_id ?: null;
            $data['tanggal_persetujuan'] = current_time( 'mysql' );
        } elseif ( $asesor_id ) {
            $data['asesor_id'] = $asesor_id;
        }
        
        LSP_Cert_Database::update_pendaftaran( $id, $data );
        wp_send_json_success( array( 'message' => 'Rekomendasi berhasil diupdate' ) );
    }
}
