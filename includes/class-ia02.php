<?php
/**
 * Instrumen FR.IA.02 — Manajemen Tugas Praktik Demonstrasi (TPD)
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_IA02 {

    /**
     * Inisialisasi hook
     */
    public static function init() {
        if ( is_admin() ) {
            add_action( 'admin_init', array( __CLASS__, 'handle_save_action' ) );
        }
    }

    /**
     * Ambil konfigurasi instrumen FR.IA.02 berdasarkan ID Skema (dan paket MUK jika ada)
     *
     * @param int $skema_id
     * @param int $paket_muk_id
     * @return array
     */
    public static function get_instrument( $skema_id, $paket_muk_id = 0 ) {
        $skema_id = intval( $skema_id );
        if ( $skema_id <= 0 ) {
            return self::get_default_instrument();
        }

        $saved = null;

        // Cek jika ada spesifik paket MUK
        if ( $paket_muk_id > 0 && class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            $paket = LSP_Cert_Paket_MUK::get_paket( $paket_muk_id );
            if ( $paket && ! empty( $paket->ia02_data ) && is_array( $paket->ia02_data ) ) {
                $saved = $paket->ia02_data;
            }
        } elseif ( class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            $def_p = LSP_Cert_Paket_MUK::get_default_paket( $skema_id );
            if ( $def_p && ! empty( $def_p->ia02_data ) && is_array( $def_p->ia02_data ) ) {
                $saved = $def_p->ia02_data;
            }
        }

        if ( ! is_array( $saved ) ) {
            $option_key = 'lsp_cert_ia02_skema_' . $skema_id;
            $saved      = get_option( $option_key, null );
        }

        if ( ! is_array( $saved ) ) {
            return self::get_default_instrument( $skema_id );
        }

        return array(
            'skema_id'      => $skema_id,
            'skenario'      => isset( $saved['skenario'] ) ? (string) $saved['skenario'] : '',
            'langkah_kerja' => isset( $saved['langkah_kerja'] ) ? (string) $saved['langkah_kerja'] : '',
            'hasil_kerja'   => isset( $saved['hasil_kerja'] ) ? (string) $saved['hasil_kerja'] : '',
            'perlengkapan'  => isset( $saved['perlengkapan'] ) ? (string) $saved['perlengkapan'] : '',
            'peralatan'     => isset( $saved['peralatan'] ) ? (string) $saved['peralatan'] : '',
            'durasi_waktu'  => isset( $saved['durasi_waktu'] ) ? (string) $saved['durasi_waktu'] : '120 Menit',
            'updated_at'    => isset( $saved['updated_at'] ) ? (string) $saved['updated_at'] : '',
            'updated_by'    => isset( $saved['updated_by'] ) ? intval( $saved['updated_by'] ) : 0,
        );
    }

    /**
     * Nilai default instrumen FR.IA.02
     *
     * @param int $skema_id
     * @return array
     */
    public static function get_default_instrument( $skema_id = 0 ) {
        $skema       = $skema_id ? LSP_Cert_Integration::get_skema( $skema_id ) : array();
        $skema_title = $skema['title'] ?? 'Sertifikasi Kompetensi';

        $default_skenario = sprintf(
            "Anda seorang asesi sedang mengajukan sertifikasi skema %s. Anda diharapkan mampu mendemonstrasikan seluruh tugas praktik sesuai dengan kelompok pekerjaan secara profesional, terstruktur, dan memenuhi standar keselamatan kerja yang berlaku di tempat kerja.",
            $skema_title
        );

        $default_langkah = "1. Mempersiapkan seluruh peralatan dan perangkat kerja pendukung sesuai instruksi kerja.\n2. Melaksanakan tahapan pekerjaan sesuai SOP dan standar keselamatan dan kesehatan kerja (K3).\n3. Mengolah data dan menyelesaikan tugas demonstrasi sesuai spesifikasi teknis.\n4. Mendokumentasikan hasil pekerjaan dan melaporkan hasil kerja secara tertulis kepada asesor.";

        $default_hasil = "Hasil pekerjaan berupa demonstrasi fisik/praktik langsung, bukti karya/dokumen pendukung, dan lembar verifikasi hasil pekerjaan yang diserahkan kepada asesor.";

        $default_perlengkapan = "Alat Keselamatan & Kesehatan Kerja (K3)\nPerangkat media penyimpanan / flashdisk / cloud\nBahan penunjang demonstrasi / dokumen kerja\nFormulir checklist laporan pekerjaan";

        $default_peralatan = "Komputer / laptop / gawai penunjang\nPerangkat kerja spesifik bidang profesi\nAksesori / peripheral pendukung teknis\nAlat Tulis Kantor (ATK)";

        return array(
            'skema_id'      => $skema_id,
            'skenario'      => $default_skenario,
            'langkah_kerja' => $default_langkah,
            'hasil_kerja'   => $default_hasil,
            'perlengkapan'  => $default_perlengkapan,
            'peralatan'     => $default_peralatan,
            'durasi_waktu'  => '120 Menit',
            'updated_at'    => '',
            'updated_by'    => 0,
        );
    }

    /**
     * Simpan konfigurasi instrumen FR.IA.02
     *
     * @param int   $skema_id
     * @param array $data
     * @param int   $paket_muk_id
     * @return bool
     */
    public static function save_instrument( $skema_id, $data, $paket_muk_id = 0 ) {
        $skema_id = intval( $skema_id );
        if ( $skema_id <= 0 ) {
            return false;
        }

        $clean_data = array(
            'skema_id'      => $skema_id,
            'skenario'      => sanitize_textarea_field( $data['skenario'] ?? '' ),
            'langkah_kerja' => sanitize_textarea_field( $data['langkah_kerja'] ?? '' ),
            'hasil_kerja'   => sanitize_textarea_field( $data['hasil_kerja'] ?? '' ),
            'perlengkapan'  => sanitize_textarea_field( $data['perlengkapan'] ?? '' ),
            'peralatan'     => sanitize_textarea_field( $data['peralatan'] ?? '' ),
            'durasi_waktu'  => sanitize_text_field( $data['durasi_waktu'] ?? '120 Menit' ),
            'updated_at'    => current_time( 'mysql' ),
            'updated_by'    => get_current_user_id() ?: 1,
        );

        global $wpdb;
        $saved = false;

        if ( $paket_muk_id > 0 && class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            $table = $wpdb->prefix . 'lsp_paket_muk';
            $saved = (bool) $wpdb->update(
                $table,
                array( 'ia02_data' => wp_json_encode( $clean_data ) ),
                array( 'id' => $paket_muk_id )
            );
        }

        // Simpan juga ke option skema sebagai master fallback
        $option_key = 'lsp_cert_ia02_skema_' . $skema_id;
        update_option( $option_key, $clean_data );

        return true;
    }

    /**
     * Tangani penyimpanan form admin POST
     */
    public static function handle_save_action() {
        if ( ! isset( $_POST['action'] ) || 'lsp_save_ia02' !== $_POST['action'] ) {
            return;
        }

        if ( ! current_user_can( 'lsp_cert_manage_instruments' ) ) {
            wp_die( 'Akses tidak diizinkan.', 403 );
        }

        check_admin_referer( 'lsp_ia02_save_nonce', 'lsp_ia02_nonce' );

        $skema_id     = isset( $_POST['skema_id'] ) ? intval( $_POST['skema_id'] ) : 0;
        $paket_muk_id = isset( $_POST['paket_muk_id'] ) ? intval( $_POST['paket_muk_id'] ) : 0;

        self::save_instrument( $skema_id, $_POST, $paket_muk_id );

        $redirect_args = array(
            'page'     => 'lsp-ia02',
            'skema_id' => $skema_id,
            'saved'    => '1',
        );

        if ( $paket_muk_id ) {
            $redirect_args['paket_muk_id'] = $paket_muk_id;
        }

        wp_safe_redirect( add_query_arg( $redirect_args, admin_url( 'admin.php' ) ) );
        exit;
    }

    /**
     * Render Halaman Pengaturan Instrumen FR.IA.02
     */
    public static function admin_page() {
        if ( ! current_user_can( 'lsp_cert_manage_instruments' ) ) {
            wp_die( 'Akses ditolak.' );
        }

        $all_skema = LSP_Cert_Integration::get_all_skema();

        $selected_skema_id = isset( $_GET['skema_id'] ) ? intval( $_GET['skema_id'] ) : 0;
        if ( ! $selected_skema_id && ! empty( $all_skema ) ) {
            $selected_skema_id = (int) $all_skema[0]['id'];
        }

        $paket_muk_id = isset( $_GET['paket_muk_id'] ) ? intval( $_GET['paket_muk_id'] ) : 0;
        $instrument   = self::get_instrument( $selected_skema_id, $paket_muk_id );
        $skema_data   = $selected_skema_id ? LSP_Cert_Integration::get_skema( $selected_skema_id ) : null;
        $units_skkni  = $selected_skema_id ? LSP_Cert_Integration::get_unit_kompetensi( $selected_skema_id ) : array();

        include LSP_CERT_PLUGIN_DIR . 'admin/views/ia02-settings.php';
    }
}
