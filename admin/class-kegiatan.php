<?php
/**
 * Kegiatan Sertifikasi Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Kegiatan {

    public static function init() {
        // Intercept action cetak dokumen kegiatan (FR.MAPA.02)
        add_action( 'admin_init', array( __CLASS__, 'handle_print_action' ) );

        // AJAX handlers
        add_action( 'wp_ajax_lsp_cert_save_kegiatan', array( __CLASS__, 'ajax_save_kegiatan' ) );
        add_action( 'wp_ajax_lsp_cert_delete_kegiatan', array( __CLASS__, 'ajax_delete_kegiatan' ) );
        add_action( 'wp_ajax_lsp_cert_update_status', array( __CLASS__, 'ajax_update_status' ) );
    }

    /**
     * Intercept request untuk cetak dokumen sertifikasi tingkat kegiatan (FR.MAPA.02)
     */
    public static function handle_print_action() {
        if ( isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'lsp-kegiatan', 'lsp-sertifikasi' ), true ) && isset( $_GET['action'] ) && $_GET['action'] === 'print' ) {
            $id  = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
            $doc = isset( $_GET['doc'] ) ? sanitize_key( $_GET['doc'] ) : 'mapa02';

            if ( ! $id ) {
                wp_die( 'ID kegiatan tidak valid.', 'Error', array( 'response' => 400 ) );
            }

            LSP_Cert_Document_Printer::render_kegiatan_print_page( $id, $doc );
        }
    }

    public static function admin_page() {
        if ( ! current_user_can( 'lsp_cert_manage_activities' ) ) {
            // Pengguna yang hanya berhak mencetak dokumen diarahkan ke halaman cetak,
            // bukan ditolak, karena menu induk memang terlihat untuk mereka.
            if ( current_user_can( 'lsp_cert_print_docs' ) ) {
                wp_safe_redirect( admin_url( 'admin.php?page=lsp-cetak-dokumen' ) );
                exit;
            }
            wp_die( 'Anda tidak memiliki hak akses ke halaman ini.', 'Akses Ditolak', array( 'response' => 403 ) );
        }

        $action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';
        
        switch ( $action ) {
            case 'add':
            case 'edit':
                self::render_form();
                break;
            default:
                self::render_list();
                break;
        }
    }

    private static function render_list() {
        $kegiatan_list = LSP_Cert_Database::get_all_kegiatan();
        include LSP_CERT_PLUGIN_DIR . 'admin/views/kegiatan-list.php';
    }

    private static function render_form() {
        $id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
        $kegiatan = $id ? LSP_Cert_Database::get_kegiatan( $id ) : null;
        
        $skema_list = LSP_Cert_Integration::get_all_skema();
        $tuk_list = LSP_Cert_Integration::get_all_tuk();
        $asesor_list = LSP_Cert_Integration::get_all_asesor();
        
        include LSP_CERT_PLUGIN_DIR . 'admin/views/kegiatan-form.php';
    }

    public static function ajax_save_kegiatan() {
        check_ajax_referer( 'lsp_cert_nonce', 'nonce' );
        
        if ( ! current_user_can( 'lsp_cert_manage_activities' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }
        
        // Asesor bertugas (array of IDs)
        $asesor_ids = array();
        if ( isset( $_POST['asesor_ids'] ) && is_array( $_POST['asesor_ids'] ) ) {
            $asesor_ids = array_values( array_unique( array_filter( array_map( 'intval', $_POST['asesor_ids'] ) ) ) );
        }

        // Asesor Penyusun MUK
        $penyusun_id   = ! empty( $_POST['penyusun_id'] ) ? intval( $_POST['penyusun_id'] ) : null;
        $penyusun_nama = sanitize_text_field( $_POST['penyusun_nama'] ?? '' );
        $penyusun_met  = sanitize_text_field( $_POST['penyusun_met'] ?? '' );
        if ( $penyusun_id ) {
            $penyusun_obj = LSP_Cert_Integration::get_asesor( $penyusun_id );
            if ( $penyusun_obj ) {
                $penyusun_nama = $penyusun_obj['name'] ?? $penyusun_nama;
                $penyusun_met  = $penyusun_obj['no_reg'] ?? $penyusun_met;
            }
        }

        // Validator MUK
        $validator_id   = ! empty( $_POST['validator_id'] ) ? intval( $_POST['validator_id'] ) : null;
        $validator_nama = sanitize_text_field( $_POST['validator_nama'] ?? '' );
        $validator_met  = sanitize_text_field( $_POST['validator_met'] ?? '' );
        if ( $validator_id ) {
            $validator_obj = LSP_Cert_Integration::get_asesor( $validator_id );
            if ( $validator_obj ) {
                $validator_nama = $validator_obj['name'] ?? $validator_nama;
                $validator_met  = $validator_obj['no_reg'] ?? $validator_met;
            }
        }

        $paket_muk_id = ! empty( $_POST['paket_muk_id'] ) ? intval( $_POST['paket_muk_id'] ) : null;
        if ( ! $paket_muk_id && class_exists( 'LSP_Cert_Paket_MUK' ) && ! empty( $_POST['skema_id'] ) ) {
            $def_paket = LSP_Cert_Paket_MUK::get_default_paket( intval( $_POST['skema_id'] ) );
            if ( $def_paket ) {
                $paket_muk_id = $def_paket->id;
            }
        }

        $data = array(
            'skema_id'            => intval( $_POST['skema_id'] ),
            'paket_muk_id'        => $paket_muk_id,
            'tanggal_pelaksanaan' => sanitize_text_field( $_POST['tanggal_pelaksanaan'] ),
            'tuk_id'              => intval( $_POST['tuk_id'] ),
            'lokasi_uji'          => sanitize_text_field( $_POST['lokasi_uji'] ?? '' ),
            'alamat_lokasi_uji'   => sanitize_textarea_field( $_POST['alamat_lokasi_uji'] ?? '' ),
            'kuota'               => intval( $_POST['kuota'] ),
            'status'              => sanitize_text_field( $_POST['status'] ),
            'asesor_ids'          => ! empty( $asesor_ids ) ? wp_json_encode( $asesor_ids ) : null,
            'penyusun_id'         => $penyusun_id,
            'penyusun_nama'       => $penyusun_nama,
            'penyusun_met'        => $penyusun_met,
            'validator_id'        => $validator_id,
            'validator_nama'      => $validator_nama,
            'validator_met'       => $validator_met,
        );
        
        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        
        if ( $id ) {
            LSP_Cert_Database::update_kegiatan( $id, $data );
            wp_send_json_success( array( 'message' => 'Kegiatan berhasil diupdate' ) );
        } else {
            $new_id = LSP_Cert_Database::insert_kegiatan( $data );
            wp_send_json_success( array( 'message' => 'Kegiatan berhasil ditambahkan', 'id' => $new_id ) );
        }
    }

    public static function ajax_delete_kegiatan() {
        check_ajax_referer( 'lsp_cert_nonce', 'nonce' );
        
        if ( ! current_user_can( 'lsp_cert_manage_activities' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }
        
        $id = intval( $_POST['id'] );
        
        // Check if there are registrations
        $peserta_count = LSP_Cert_Database::get_peserta_count( $id );
        if ( $peserta_count > 0 ) {
            wp_send_json_error( 'Tidak dapat menghapus kegiatan yang sudah memiliki pendaftar' );
        }
        
        LSP_Cert_Database::delete_kegiatan( $id );
        wp_send_json_success( array( 'message' => 'Kegiatan berhasil dihapus' ) );
    }

    public static function ajax_update_status() {
        check_ajax_referer( 'lsp_cert_nonce', 'nonce' );
        
        if ( ! current_user_can( 'lsp_cert_manage_activities' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }
        
        $id = intval( $_POST['id'] );
        $status = sanitize_text_field( $_POST['status'] );
        
        LSP_Cert_Database::update_kegiatan( $id, array( 'status' => $status ) );
        wp_send_json_success( array( 'message' => 'Status berhasil diupdate' ) );
    }
}
