<?php
/**
 * Instrumen FR.IA.04 — Manajemen Kegiatan Terstruktur (DIT) & Penilaian Proyek Singkat
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_IA04 {

    /**
     * Inisialisasi hook
     */
    public static function init() {
        if ( is_admin() ) {
            add_action( 'admin_init', array( __CLASS__, 'handle_save_action' ) );
        }
    }

    /**
     * Ambil konfigurasi instrumen FR.IA.04 berdasarkan ID Skema (dan paket MUK jika ada)
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
            if ( $paket && ! empty( $paket->ia04_data ) && is_array( $paket->ia04_data ) ) {
                $saved = $paket->ia04_data;
            }
        } elseif ( class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            $def_p = LSP_Cert_Paket_MUK::get_default_paket( $skema_id );
            if ( $def_p && ! empty( $def_p->ia04_data ) && is_array( $def_p->ia04_data ) ) {
                $saved = $def_p->ia04_data;
            }
        }

        if ( ! is_array( $saved ) ) {
            $option_key = 'lsp_cert_ia04_skema_' . $skema_id;
            $saved      = get_option( $option_key, null );
        }

        if ( ! is_array( $saved ) ) {
            return self::get_default_instrument( $skema_id );
        }

        return array(
            'skema_id'         => $skema_id,
            // BAGIAN A — MATERI FR.IA.04A
            'judul_kegiatan'   => isset( $saved['judul_kegiatan'] ) ? (string) $saved['judul_kegiatan'] : '',
            'situasi'          => isset( $saved['situasi'] ) ? (string) $saved['situasi'] : '',
            'tugas'            => isset( $saved['tugas'] ) ? (string) $saved['tugas'] : '',
            'tindakan'         => isset( $saved['tindakan'] ) ? (string) $saved['tindakan'] : '',
            'hasil'            => isset( $saved['hasil'] ) ? (string) $saved['hasil'] : '',
            'data_informasi'   => isset( $saved['data_informasi'] ) ? (string) $saved['data_informasi'] : '',
            'hal_demonstrasi'  => isset( $saved['hal_demonstrasi'] ) ? (string) $saved['hal_demonstrasi'] : '',
            'waktu_proyek'     => isset( $saved['waktu_proyek'] ) ? (string) $saved['waktu_proyek'] : '240 menit',
            'waktu_presentasi' => isset( $saved['waktu_presentasi'] ) ? (string) $saved['waktu_presentasi'] : '30 menit',
            // BAGIAN B — MATERI FR.IA.04B (Daftar Pertanyaan Penilaian)
            'questions'        => isset( $saved['questions'] ) && is_array( $saved['questions'] ) ? $saved['questions'] : array(),
            'updated_at'       => isset( $saved['updated_at'] ) ? (string) $saved['updated_at'] : '',
            'updated_by'       => isset( $saved['updated_by'] ) ? intval( $saved['updated_by'] ) : 0,
        );
    }

    /**
     * Nilai default instrumen
     *
     * @param int $skema_id
     * @return array
     */
    public static function get_default_instrument( $skema_id = 0 ) {
        return array(
            'skema_id'         => $skema_id,
            'judul_kegiatan'   => '',
            'situasi'          => '',
            'tugas'            => '',
            'tindakan'         => '',
            'hasil'            => '',
            'data_informasi'   => '',
            'hal_demonstrasi'  => '',
            'waktu_proyek'     => '240 menit',
            'waktu_presentasi' => '30 menit',
            'questions'        => array(),
            'updated_at'       => '',
            'updated_by'       => 0,
        );
    }

    /**
     * Simpan konfigurasi instrumen FR.IA.04 untuk skema tertentu (dan paket MUK jika ada)
     *
     * @param int   $skema_id
     * @param array $materi_ia04a
     * @param array $questions_ia04b
     * @param int   $paket_muk_id
     * @return bool
     */
    public static function save_instrument( $skema_id, $materi_ia04a, $questions_ia04b = array(), $paket_muk_id = 0 ) {
        $skema_id = intval( $skema_id );
        if ( $skema_id <= 0 ) {
            return false;
        }

        // Sanitasi Bagian A (Materi IA.04A)
        $clean_ia04a = array(
            'judul_kegiatan'   => sanitize_text_field( $materi_ia04a['judul_kegiatan'] ?? '' ),
            'situasi'          => sanitize_textarea_field( $materi_ia04a['situasi'] ?? '' ),
            'tugas'            => sanitize_textarea_field( $materi_ia04a['tugas'] ?? '' ),
            'tindakan'         => sanitize_textarea_field( $materi_ia04a['tindakan'] ?? '' ),
            'hasil'            => sanitize_textarea_field( $materi_ia04a['hasil'] ?? '' ),
            'data_informasi'   => sanitize_textarea_field( $materi_ia04a['data_informasi'] ?? '' ),
            'hal_demonstrasi'  => sanitize_textarea_field( $materi_ia04a['hal_demonstrasi'] ?? '' ),
            'waktu_proyek'     => sanitize_text_field( $materi_ia04a['waktu_proyek'] ?? '240 menit' ),
            'waktu_presentasi' => sanitize_text_field( $materi_ia04a['waktu_presentasi'] ?? '30 menit' ),
        );

        // Sanitasi Bagian B (Daftar Pertanyaan IA.04B)
        $clean_questions = array();
        if ( is_array( $questions_ia04b ) ) {
            $order_counter = 1;
            foreach ( $questions_ia04b as $q ) {
                $pertanyaan = sanitize_textarea_field( $q['pertanyaan'] ?? '' );
                $lingkup    = sanitize_text_field( $q['lingkup'] ?? '' );
                $elemen_kuk = sanitize_text_field( $q['elemen_kuk'] ?? '' );
                $dimensi    = sanitize_text_field( $q['dimensi'] ?? '' );
                $unit_id    = isset( $q['unit_id'] ) ? sanitize_text_field( $q['unit_id'] ) : '';

                // Abaikan baris jika pertanyaan dan lingkup kosong total
                if ( empty( $pertanyaan ) && empty( $lingkup ) ) {
                    continue;
                }

                $clean_questions[] = array(
                    'order'      => $order_counter++,
                    'unit_id'    => $unit_id,
                    'lingkup'    => $lingkup,
                    'pertanyaan' => $pertanyaan,
                    'elemen_kuk' => $elemen_kuk,
                    'dimensi'    => $dimensi,
                );
            }
        }

        $data = array_merge( $clean_ia04a, array(
            'skema_id'   => $skema_id,
            'questions'  => $clean_questions,
            'updated_at' => current_time( 'mysql' ),
            'updated_by' => get_current_user_id(),
        ) );

        if ( $paket_muk_id > 0 && class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            global $wpdb;
            $table = $wpdb->prefix . 'lsp_paket_muk';
            $wpdb->update( $table, array( 'ia04_data' => wp_json_encode( $data ) ), array( 'id' => $paket_muk_id ) );
        } elseif ( class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            $def_p = LSP_Cert_Paket_MUK::get_default_paket( $skema_id );
            if ( $def_p ) {
                global $wpdb;
                $table = $wpdb->prefix . 'lsp_paket_muk';
                $wpdb->update( $table, array( 'ia04_data' => wp_json_encode( $data ) ), array( 'id' => $def_p->id ) );
            }
        }

        $option_key = 'lsp_cert_ia04_skema_' . $skema_id;
        return update_option( $option_key, $data );
    }

    /**
     * Handle proses penyimpanan dari form admin
     */
    public static function handle_save_action() {
        if ( ! isset( $_POST['action'] ) || 'lsp_save_ia04' !== $_POST['action'] ) {
            return;
        }

        if ( ! current_user_can( 'lsp_cert_manage_instruments' ) ) {
            wp_die( 'Akses ditolak: Anda tidak memiliki izin untuk menyimpan konfigurasi instrumen.', 'Akses Ditolak', array( 'response' => 403 ) );
        }

        check_admin_referer( 'lsp_ia04_save_nonce', 'lsp_ia04_nonce' );

        $skema_id = isset( $_POST['skema_id'] ) ? intval( $_POST['skema_id'] ) : 0;
        if ( $skema_id <= 0 ) {
            wp_redirect( add_query_arg( array(
                'page'  => 'lsp-ia04',
                'error' => 'invalid_skema',
            ), admin_url( 'admin.php' ) ) );
            exit;
        }

        $materi_ia04a = array(
            'judul_kegiatan'   => isset( $_POST['judul_kegiatan'] ) ? wp_unslash( $_POST['judul_kegiatan'] ) : '',
            'situasi'          => isset( $_POST['situasi'] ) ? wp_unslash( $_POST['situasi'] ) : '',
            'tugas'            => isset( $_POST['tugas'] ) ? wp_unslash( $_POST['tugas'] ) : '',
            'tindakan'         => isset( $_POST['tindakan'] ) ? wp_unslash( $_POST['tindakan'] ) : '',
            'hasil'            => isset( $_POST['hasil'] ) ? wp_unslash( $_POST['hasil'] ) : '',
            'data_informasi'   => isset( $_POST['data_informasi'] ) ? wp_unslash( $_POST['data_informasi'] ) : '',
            'hal_demonstrasi'  => isset( $_POST['hal_demonstrasi'] ) ? wp_unslash( $_POST['hal_demonstrasi'] ) : '',
            'waktu_proyek'     => isset( $_POST['waktu_proyek'] ) ? wp_unslash( $_POST['waktu_proyek'] ) : '240 menit',
            'waktu_presentasi' => isset( $_POST['waktu_presentasi'] ) ? wp_unslash( $_POST['waktu_presentasi'] ) : '30 menit',
        );

        $paket_muk_id  = isset( $_POST['paket_muk_id'] ) ? intval( $_POST['paket_muk_id'] ) : 0;
        $raw_questions = isset( $_POST['questions'] ) && is_array( $_POST['questions'] ) ? wp_unslash( $_POST['questions'] ) : array();

        self::save_instrument( $skema_id, $materi_ia04a, $raw_questions, $paket_muk_id );

        $redirect_args = array(
            'page'     => 'lsp-ia04',
            'skema_id' => $skema_id,
            'saved'    => '1',
        );
        if ( $paket_muk_id ) {
            $redirect_args['paket_muk_id'] = $paket_muk_id;
        }

        wp_redirect( add_query_arg( $redirect_args, admin_url( 'admin.php' ) ) );
        exit;
    }

    /**
     * Render halaman admin Instrumen FR.IA.04
     */
    public static function admin_page() {
        if ( ! current_user_can( 'lsp_cert_manage_instruments' ) ) {
            wp_die( 'Anda tidak memiliki hak akses ke halaman ini.' );
        }

        $all_skema = LSP_Cert_Integration::get_all_skema();
        $selected_skema_id = isset( $_GET['skema_id'] ) ? intval( $_GET['skema_id'] ) : 0;

        // Default skema pertama jika belum dipilih
        if ( ! $selected_skema_id && ! empty( $all_skema ) ) {
            $selected_skema_id = $all_skema[0]['id'];
        }

        $paket_muk_id = isset( $_GET['paket_muk_id'] ) ? intval( $_GET['paket_muk_id'] ) : 0;
        $instrument   = self::get_instrument( $selected_skema_id, $paket_muk_id );
        $skema_data   = $selected_skema_id ? LSP_Cert_Integration::get_skema( $selected_skema_id ) : null;
        $units_skkni  = $selected_skema_id ? LSP_Cert_Integration::get_unit_kompetensi( $selected_skema_id ) : array();

        // Cari contoh kegiatan terkait skema untuk pratinjau cetak
        $sample_kegiatan = null;
        if ( $selected_skema_id ) {
            global $wpdb;
            $table_kegiatan  = $wpdb->prefix . 'lsp_kegiatan';
            $sample_kegiatan = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$table_kegiatan} WHERE skema_id = %d ORDER BY id DESC LIMIT 1",
                $selected_skema_id
            ) );
        }

        include LSP_CERT_PLUGIN_DIR . 'admin/views/ia04-settings.php';
    }
}
