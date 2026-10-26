<?php
/**
 * Instrumen FR.IA.03 — Manajemen Pertanyaan Mendukung Observasi (PMO)
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_IA03 {

    /**
     * Inisialisasi hook
     */
    public static function init() {
        if ( is_admin() ) {
            add_action( 'admin_init', array( __CLASS__, 'handle_save_action' ) );
        }
    }

    /**
     * Ambil konfigurasi instrumen FR.IA.03 berdasarkan ID Skema (dan paket MUK jika ada)
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
            if ( $paket && ! empty( $paket->ia03_data ) && is_array( $paket->ia03_data ) ) {
                $saved = $paket->ia03_data;
            }
        } elseif ( class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            $def_p = LSP_Cert_Paket_MUK::get_default_paket( $skema_id );
            if ( $def_p && ! empty( $def_p->ia03_data ) && is_array( $def_p->ia03_data ) ) {
                $saved = $def_p->ia03_data;
            }
        }

        if ( ! is_array( $saved ) ) {
            $option_key = 'lsp_cert_ia03_skema_' . $skema_id;
            $saved      = get_option( $option_key, null );
        }

        if ( ! is_array( $saved ) ) {
            return self::get_default_instrument( $skema_id );
        }

        return array(
            'skema_id'   => $skema_id,
            'panduan'    => isset( $saved['panduan'] ) ? (string) $saved['panduan'] : '',
            'questions'  => isset( $saved['questions'] ) && is_array( $saved['questions'] ) ? $saved['questions'] : array(),
            'updated_at' => isset( $saved['updated_at'] ) ? (string) $saved['updated_at'] : '',
            'updated_by' => isset( $saved['updated_by'] ) ? intval( $saved['updated_by'] ) : 0,
        );
    }

    /**
     * Nilai default instrumen FR.IA.03 (4 Pertanyaan Standar Dimensi Kompetensi BNSP)
     *
     * @param int $skema_id
     * @return array
     */
    public static function get_default_instrument( $skema_id = 0 ) {
        $default_questions = array(
            array(
                'order'         => 1,
                'dimensi'       => 'CMS',
                'pertanyaan'    => 'Jika pada saat pelaksanaan tugas atau demonstrasi praktik terjadi kendala teknis tak terduga (misalnya peralatan utama mati mendadak, data rusak, atau koneksi terputus), tindakan darurat dan sistematis apa yang pertama kali Anda lakukan?',
                'kunci_jawaban' => 'Asesi mampu menjelaskan prosedur darurat penanganan insiden, isolasi masalah, dan pelaporan kepada supervisor sesuai SOP keselamatan kerja.',
            ),
            array(
                'order'         => 2,
                'dimensi'       => 'TRS',
                'pertanyaan'    => 'Jika lingkungan kerja atau perangkat/aplikasi yang Anda gunakan bermigrasi ke sistem atau versi baru yang belum pernah Anda operasikan sebelumnya, bagaimana strategi Anda untuk beradaptasi cepat agar hasil kerja tetap memenuhi standar?',
                'kunci_jawaban' => 'Asesi mampu menerapkan prinsip transfer skills, mempelajari dokumentasi/manual sistem baru, serta melakukan pengujian fungsional secara mandiri.',
            ),
            array(
                'order'         => 3,
                'dimensi'       => 'JRES',
                'pertanyaan'    => 'Sebutkan prosedur dasar Keselamatan dan Kesehatan Kerja (K3) serta standar komunikasi etis yang wajib Anda patuhi selama melaksanakan seluruh tahapan demonstrasi pekerjaan!',
                'kunci_jawaban' => 'Asesi mematuhi regulasi K3 lingkungan kerja, penggunaan APD/ergonomi yang tepat, dan komunikasi profesional terhadap rekan kerja maupun asesor.',
            ),
            array(
                'order'         => 4,
                'dimensi'       => 'TS',
                'pertanyaan'    => 'Jelaskan langkah-langkah kritis dalam memverifikasi ketepatan dan kualitas hasil kerja sebelum diserahkan kepada pihak manajemen atau pengguna akhir!',
                'kunci_jawaban' => 'Asesi melakukan cek silang (*quality control*) terhadap spesifikasi output, memastikan kesesuaian format laporan, dan menandatangani bukti serah terima.',
            ),
        );

        return array(
            'skema_id'   => $skema_id,
            'panduan'    => '',
            'questions'  => $default_questions,
            'updated_at' => '',
            'updated_by' => 0,
        );
    }

    /**
     * Simpan konfigurasi instrumen FR.IA.03
     *
     * @param int   $skema_id
     * @param array $questions
     * @param string $panduan
     * @param int   $paket_muk_id
     * @return bool
     */
    public static function save_instrument( $skema_id, $questions, $panduan = '', $paket_muk_id = 0 ) {
        $skema_id = intval( $skema_id );
        if ( $skema_id <= 0 ) {
            return false;
        }

        $clean_questions = array();
        if ( is_array( $questions ) ) {
            $order = 1;
            foreach ( $questions as $q ) {
                $pertanyaan = sanitize_textarea_field( $q['pertanyaan'] ?? '' );
                if ( empty( $pertanyaan ) ) {
                    continue;
                }

                $clean_questions[] = array(
                    'order'         => $order++,
                    'dimensi'       => sanitize_text_field( $q['dimensi'] ?? 'TS' ),
                    'pertanyaan'    => $pertanyaan,
                    'kunci_jawaban' => sanitize_textarea_field( $q['kunci_jawaban'] ?? '' ),
                );
            }
        }

        $clean_data = array(
            'skema_id'   => $skema_id,
            'panduan'    => sanitize_textarea_field( $panduan ),
            'questions'  => $clean_questions,
            'updated_at' => current_time( 'mysql' ),
            'updated_by' => get_current_user_id() ?: 1,
        );

        global $wpdb;
        if ( $paket_muk_id > 0 && class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            $table = $wpdb->prefix . 'lsp_paket_muk';
            $wpdb->update(
                $table,
                array( 'ia03_data' => wp_json_encode( $clean_data ) ),
                array( 'id' => $paket_muk_id )
            );
        }

        // Simpan juga ke option skema sebagai master fallback
        $option_key = 'lsp_cert_ia03_skema_' . $skema_id;
        update_option( $option_key, $clean_data );

        return true;
    }

    /**
     * Tangani penyimpanan form admin POST
     */
    public static function handle_save_action() {
        if ( ! isset( $_POST['action'] ) || 'lsp_save_ia03' !== $_POST['action'] ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Akses tidak diizinkan.', 403 );
        }

        check_admin_referer( 'lsp_ia03_save_nonce', 'lsp_ia03_nonce' );

        $skema_id     = isset( $_POST['skema_id'] ) ? intval( $_POST['skema_id'] ) : 0;
        $paket_muk_id = isset( $_POST['paket_muk_id'] ) ? intval( $_POST['paket_muk_id'] ) : 0;
        $panduan      = isset( $_POST['panduan'] ) ? sanitize_textarea_field( $_POST['panduan'] ) : '';
        $questions    = isset( $_POST['questions'] ) && is_array( $_POST['questions'] ) ? $_POST['questions'] : array();

        self::save_instrument( $skema_id, $questions, $panduan, $paket_muk_id );

        $redirect_args = array(
            'page'     => 'lsp-ia03',
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
     * Render Halaman Pengaturan Instrumen FR.IA.03
     */
    public static function admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
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

        include LSP_CERT_PLUGIN_DIR . 'admin/views/ia03-settings.php';
    }
}
