<?php
/**
 * Instrumen FR.IA.05 — Manajemen Soal Pilihan Ganda & Kunci Jawaban
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_IA05 {

    /**
     * Inisialisasi hook
     */
    public static function init() {
        if ( is_admin() ) {
            add_action( 'admin_init', array( __CLASS__, 'handle_save_action' ) );
        }
    }

    /**
     * Ambil konfigurasi instrumen FR.IA.05 berdasarkan ID Skema (dan paket MUK jika ada)
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
            if ( $paket && ! empty( $paket->ia05_data ) && is_array( $paket->ia05_data ) ) {
                $saved = $paket->ia05_data;
            }
        } elseif ( class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            $def_p = LSP_Cert_Paket_MUK::get_default_paket( $skema_id );
            if ( $def_p && ! empty( $def_p->ia05_data ) && is_array( $def_p->ia05_data ) ) {
                $saved = $def_p->ia05_data;
            }
        }

        if ( ! is_array( $saved ) ) {
            $option_key = 'lsp_cert_ia05_skema_' . $skema_id;
            $saved      = get_option( $option_key, null );
        }

        if ( ! is_array( $saved ) ) {
            return self::get_default_instrument();
        }

        return array(
            'skema_id'    => $skema_id,
            'pertanyaan'  => isset( $saved['pertanyaan'] ) ? (string) $saved['pertanyaan'] : '',
            'kunci'       => isset( $saved['kunci'] ) ? (string) $saved['kunci'] : '',
            'jumlah_soal' => isset( $saved['jumlah_soal'] ) ? max( 1, intval( $saved['jumlah_soal'] ) ) : 10,
            'updated_at'  => isset( $saved['updated_at'] ) ? (string) $saved['updated_at'] : '',
            'updated_by'  => isset( $saved['updated_by'] ) ? intval( $saved['updated_by'] ) : 0,
        );
    }

    /**
     * Nilai default instrumen
     *
     * @return array
     */
    public static function get_default_instrument() {
        return array(
            'skema_id'    => 0,
            'pertanyaan'  => '',
            'kunci'       => '',
            'jumlah_soal' => 10,
            'updated_at'  => '',
            'updated_by'  => 0,
        );
    }

    /**
     * Simpan konfigurasi instrumen FR.IA.05 untuk skema tertentu (dan paket MUK jika ada)
     *
     * @param int    $skema_id
     * @param string $pertanyaan
     * @param string $kunci
     * @param int    $jumlah_soal
     * @param int    $paket_muk_id
     * @return bool
     */
    public static function save_instrument( $skema_id, $pertanyaan, $kunci, $jumlah_soal, $paket_muk_id = 0 ) {
        $skema_id    = intval( $skema_id );
        $jumlah_soal = max( 1, intval( $jumlah_soal ) );

        if ( $skema_id <= 0 ) {
            return false;
        }

        // Sanitasi multiline text secara aman, mempertahankan baris baru (\n) dan mencegah XSS
        $sanitized_pertanyaan = sanitize_textarea_field( $pertanyaan );
        $sanitized_kunci      = sanitize_textarea_field( $kunci );

        $data = array(
            'skema_id'    => $skema_id,
            'pertanyaan'  => $sanitized_pertanyaan,
            'kunci'       => $sanitized_kunci,
            'jumlah_soal' => $jumlah_soal,
            'updated_at'  => current_time( 'mysql' ),
            'updated_by'  => get_current_user_id(),
        );

        if ( $paket_muk_id > 0 && class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            global $wpdb;
            $table = $wpdb->prefix . 'lsp_paket_muk';
            $wpdb->update( $table, array( 'ia05_data' => wp_json_encode( $data ) ), array( 'id' => $paket_muk_id ) );
        } elseif ( class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            $def_p = LSP_Cert_Paket_MUK::get_default_paket( $skema_id );
            if ( $def_p ) {
                global $wpdb;
                $table = $wpdb->prefix . 'lsp_paket_muk';
                $wpdb->update( $table, array( 'ia05_data' => wp_json_encode( $data ) ), array( 'id' => $def_p->id ) );
            }
        }

        $option_key = 'lsp_cert_ia05_skema_' . $skema_id;
        return update_option( $option_key, $data );
    }

    /**
     * Parse textarea kunci jawaban menjadi array nomor => kunci
     * Format yang didukung:
     * - "1. B\n2. C"
     * - "1: B\n2: C"
     * - "1 - B\n2 - C"
     * - "1 B\n2 C"
     * - "B\nC\nA" (urutan otomatis)
     *
     * @param string $raw_kunci
     * @param int    $jumlah_soal
     * @return array
     */
    public static function parse_kunci_jawaban( $raw_kunci, $jumlah_soal = 0 ) {
        if ( empty( $raw_kunci ) ) {
            return array();
        }

        $lines    = preg_split( '/\r\n|\r|\n/', trim( $raw_kunci ) );
        $parsed   = array();
        $auto_idx = 1;

        foreach ( $lines as $line ) {
            $line = trim( $line );
            if ( '' === $line ) {
                continue;
            }

            if ( preg_match( '/^(\d+)[\.\:\-\s\)]+\s*([A-Za-z0-9]+)/', $line, $matches ) ) {
                $num = intval( $matches[1] );
                $ans = strtoupper( trim( $matches[2] ) );
                $parsed[ $num ] = $ans;
            } elseif ( preg_match( '/^[A-Za-z0-9]+$/', $line ) ) {
                $parsed[ $auto_idx ] = strtoupper( $line );
                $auto_idx++;
            } else {
                $parsed[ $auto_idx ] = $line;
                $auto_idx++;
            }
        }

        return $parsed;
    }

    /**
     * Handle proses penyimpanan dari form admin
     */
    public static function handle_save_action() {
        if ( ! isset( $_POST['action'] ) || 'lsp_save_ia05' !== $_POST['action'] ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Akses ditolak: Anda tidak memiliki izin untuk menyimpan konfigurasi instrumen.', 'Akses Ditolak', array( 'response' => 403 ) );
        }

        check_admin_referer( 'lsp_ia05_save_nonce', 'lsp_ia05_nonce' );

        $skema_id    = isset( $_POST['skema_id'] ) ? intval( $_POST['skema_id'] ) : 0;
        $pertanyaan  = isset( $_POST['pertanyaan'] ) ? wp_unslash( $_POST['pertanyaan'] ) : '';
        $kunci       = isset( $_POST['kunci'] ) ? wp_unslash( $_POST['kunci'] ) : '';
        $jumlah_soal = isset( $_POST['jumlah_soal'] ) ? intval( $_POST['jumlah_soal'] ) : 10;

        if ( $skema_id <= 0 ) {
            wp_redirect( add_query_arg( array(
                'page'  => 'lsp-ia05',
                'error' => 'invalid_skema',
            ), admin_url( 'admin.php' ) ) );
            exit;
        }

        if ( $jumlah_soal <= 0 ) {
            $jumlah_soal = 10;
        }

        $paket_muk_id = isset( $_POST['paket_muk_id'] ) ? intval( $_POST['paket_muk_id'] ) : 0;

        self::save_instrument( $skema_id, $pertanyaan, $kunci, $jumlah_soal, $paket_muk_id );

        $redirect_args = array(
            'page'     => 'lsp-ia05',
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
     * Render halaman admin Instrumen FR.IA.05
     */
    public static function admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Anda tidak memiliki hak akses ke halaman ini.' );
        }

        $all_skema = LSP_Cert_Integration::get_all_skema();
        $selected_skema_id = isset( $_GET['skema_id'] ) ? intval( $_GET['skema_id'] ) : 0;

        // Jika belum ada yang dipilih dan daftar skema ada, gunakan skema pertama sebagai default
        if ( ! $selected_skema_id && ! empty( $all_skema ) ) {
            $selected_skema_id = $all_skema[0]['id'];
        }

        $paket_muk_id = isset( $_GET['paket_muk_id'] ) ? intval( $_GET['paket_muk_id'] ) : 0;
        $instrument   = self::get_instrument( $selected_skema_id, $paket_muk_id );
        $skema_data   = $selected_skema_id ? LSP_Cert_Integration::get_skema( $selected_skema_id ) : null;

        // Ambil contoh kegiatan untuk skema ini jika ada (untuk tombol pratinjau cetak)
        $sample_kegiatan = null;
        if ( $selected_skema_id ) {
            global $wpdb;
            $table_kegiatan = $wpdb->prefix . 'lsp_kegiatan';
            $sample_kegiatan = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$table_kegiatan} WHERE skema_id = %d ORDER BY id DESC LIMIT 1",
                $selected_skema_id
            ) );
        }

        include LSP_CERT_PLUGIN_DIR . 'admin/views/ia05-settings.php';
    }
}
