<?php
/**
 * Controller Cetak Dokumen Asesmen Sertifikasi
 * Portal Terpadu Dokumen APL, MAPA, AK, dan IA
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Cetak_Dokumen {

    public static function init() {
        // Init hooks if needed
    }

    /**
     * Render Halaman Admin Cetak Dokumen
     */
    public static function admin_page() {
        if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'asesor' ) ) {
            wp_die( 'Anda tidak memiliki hak akses ke halaman ini.' );
        }

        $all_skema    = LSP_Cert_Integration::get_all_skema();
        $all_kegiatan = LSP_Cert_Database::get_all_kegiatan();

        foreach ( $all_kegiatan as $kg ) {
            $kg_skema = LSP_Cert_Integration::get_skema( $kg->skema_id );
            $kg_tuk   = LSP_Cert_Integration::get_tuk( $kg->tuk_id );
            $kg->judul_kegiatan = ( $kg_skema['title'] ?? 'Skema #' . $kg->skema_id ) . ' &mdash; ' . date_i18n( 'd/m/Y', strtotime( $kg->tanggal_pelaksanaan ) );
            $kg->kode_kegiatan  = $kg_tuk['title'] ?? ( 'TUK #' . $kg->tuk_id );
        }

        // Ambil tab aktif: apl, mapa1, mapa2, ak01, ak04, ak07, ia, keputusan, laporan, roadmap, paket_muk
        $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'apl';
        $valid_tabs = array( 'paket_muk', 'apl', 'mapa1', 'mapa2', 'ak01', 'ak04', 'ak07', 'ia', 'keputusan', 'laporan', 'roadmap', 'lainnya' );
        if ( ! in_array( $active_tab, $valid_tabs, true ) ) {
            $active_tab = 'apl';
        }

        // Subtab untuk MAPA 1: skkni, kelompok, print
        $active_subtab = isset( $_GET['subtab'] ) ? sanitize_key( $_GET['subtab'] ) : 'skkni';
        $valid_subtabs = array( 'skkni', 'kelompok', 'print' );
        if ( ! in_array( $active_subtab, $valid_subtabs, true ) ) {
            $active_subtab = 'skkni';
        }

        // Skema yang dipilih (default skema pertama jika ada)
        $selected_skema_id = isset( $_GET['skema_id'] ) ? intval( $_GET['skema_id'] ) : 0;
        if ( ! $selected_skema_id && ! empty( $all_skema ) ) {
            $selected_skema_id = (int) $all_skema[0]['id'];
        }

        // Kegiatan / Jadwal yang dipilih (default jadwal pertama jika ada)
        $selected_kegiatan_id = isset( $_GET['kegiatan_id'] ) ? intval( $_GET['kegiatan_id'] ) : 0;
        if ( ! $selected_kegiatan_id && ! empty( $all_kegiatan ) ) {
            $selected_kegiatan_id = (int) $all_kegiatan[0]->id;
        }

        // Data kegiatan terpilih
        $selected_kegiatan = null;
        if ( $selected_kegiatan_id ) {
            $selected_kegiatan = LSP_Cert_Database::get_kegiatan( $selected_kegiatan_id );
            if ( $selected_kegiatan ) {
                $sel_skema = LSP_Cert_Integration::get_skema( $selected_kegiatan->skema_id );
                $sel_tuk   = LSP_Cert_Integration::get_tuk( $selected_kegiatan->tuk_id );
                $selected_kegiatan->judul_kegiatan = ( $sel_skema['title'] ?? 'Skema #' . $selected_kegiatan->skema_id ) . ' &mdash; ' . date_i18n( 'd/m/Y', strtotime( $selected_kegiatan->tanggal_pelaksanaan ) );
                $selected_kegiatan->kode_kegiatan  = $sel_tuk['title'] ?? ( 'TUK #' . $selected_kegiatan->tuk_id );

                if ( ! isset( $_GET['skema_id'] ) ) {
                    $selected_skema_id = (int) $selected_kegiatan->skema_id;
                }
            }
        }

        // Data skema terpilih & unit SKKNI
        $skema_data   = $selected_skema_id ? LSP_Cert_Integration::get_skema( $selected_skema_id ) : null;
        $master_units = $selected_skema_id ? LSP_Cert_Integration::get_unit_kompetensi( $selected_skema_id ) : array();

        // Data peserta / asesi pada kegiatan terpilih
        $peserta_list = array();
        if ( $selected_kegiatan_id ) {
            $peserta_list = LSP_Cert_Database::get_all_pendaftaran( array(
                'kegiatan_id' => $selected_kegiatan_id,
            ) );
        }

        // Asesi terpilih untuk cetak dokumen berbasis peserta
        if ( isset( $_GET['pendaftaran_id'] ) ) {
            $selected_pendaftaran_id = intval( $_GET['pendaftaran_id'] );
        } elseif ( ! empty( $peserta_list ) ) {
            $selected_pendaftaran_id = (int) $peserta_list[0]->id;
        } else {
            $selected_pendaftaran_id = 0;
        }
        $selected_pendaftaran = null;
        if ( $selected_pendaftaran_id > 0 ) {
            $selected_pendaftaran = LSP_Cert_Database::get_pendaftaran( $selected_pendaftaran_id );
        }

        // Data Kelompok Pekerjaan & Cakupan untuk MAPA 1
        $paket_muk_id = isset( $_GET['paket_muk_id'] ) ? intval( $_GET['paket_muk_id'] ) : ( ! empty( $selected_kegiatan->paket_muk_id ) ? (int) $selected_kegiatan->paket_muk_id : 0 );
        $kelompok = array();
        $coverage = array();
        if ( $selected_skema_id ) {
            $kelompok = LSP_Cert_Kelompok_Pekerjaan::get_kelompok( $selected_skema_id, $paket_muk_id );
            $coverage = LSP_Cert_Kelompok_Pekerjaan::validate_coverage( $selected_skema_id, $paket_muk_id );
        }

        include LSP_CERT_PLUGIN_DIR . 'admin/views/cetak-dokumen.php';
    }
}
