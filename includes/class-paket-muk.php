<?php
/**
 * Bank Data Paket MUK (Bundling MAPA & Instrumen IA)
 * Mengelola Paket Materi Uji Kompetensi Terpadu & Multi-Versi per Skema
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Paket_MUK {

    public static function init() {
        if ( is_admin() ) {
            add_action( 'wp_ajax_lsp_cert_get_paket_by_skema', array( __CLASS__, 'ajax_get_paket_by_skema' ) );
            add_action( 'wp_ajax_lsp_cert_save_paket', array( __CLASS__, 'ajax_save_paket' ) );
            add_action( 'wp_ajax_lsp_cert_clone_paket', array( __CLASS__, 'ajax_clone_paket' ) );
            add_action( 'wp_ajax_lsp_cert_delete_paket', array( __CLASS__, 'ajax_delete_paket' ) );
            add_action( 'wp_ajax_lsp_cert_set_default_paket', array( __CLASS__, 'ajax_set_default_paket' ) );
        }
    }

    /**
     * Ambil detail Paket MUK berdasarkan ID
     *
     * @param int $id
     * @return object|null
     */
    public static function get_paket( $id ) {
        global $wpdb;
        $id = intval( $id );
        if ( $id <= 0 ) {
            return null;
        }

        $table = $wpdb->prefix . 'lsp_paket_muk';
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ) );

        if ( ! $row ) {
            return null;
        }

        $row->kelompok_pekerjaan = ! empty( $row->kelompok_pekerjaan ) ? json_decode( $row->kelompok_pekerjaan, true ) : array();
        $row->ia02_data          = ! empty( $row->ia02_data ) ? json_decode( $row->ia02_data, true ) : array();
        $row->ia03_data          = ! empty( $row->ia03_data ) ? json_decode( $row->ia03_data, true ) : array();
        $row->ia04_data          = ! empty( $row->ia04_data ) ? json_decode( $row->ia04_data, true ) : array();
        $row->ia05_data          = ! empty( $row->ia05_data ) ? json_decode( $row->ia05_data, true ) : array();

        return $row;
    }

    /**
     * Alias get_by_skema
     */
    public static function get_by_skema( $skema_id, $status = '' ) {
        return self::get_paket_by_skema( $skema_id, $status );
    }

    /**
     * Ambil semua paket MUK untuk suatu skema
     *
     * @param int    $skema_id
     * @param string $status
     * @return array
     */
    public static function get_paket_by_skema( $skema_id, $status = '' ) {
        global $wpdb;
        $skema_id = intval( $skema_id );
        if ( $skema_id <= 0 ) {
            return array();
        }

        $table = $wpdb->prefix . 'lsp_paket_muk';
        if ( $status ) {
            $results = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM $table WHERE skema_id = %d AND status = %s ORDER BY is_default DESC, id ASC",
                $skema_id,
                $status
            ) );
        } else {
            $results = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM $table WHERE skema_id = %d ORDER BY is_default DESC, id ASC",
                $skema_id
            ) );
        }

        if ( empty( $results ) ) {
            // Auto generate Versi 01 Default jika belum ada paket sama sekali untuk skema ini
            $default_id = self::ensure_default_paket( $skema_id );
            if ( $default_id ) {
                return self::get_paket_by_skema( $skema_id, $status );
            }
            return array();
        }

        foreach ( $results as $r ) {
            $r->kelompok_pekerjaan = ! empty( $r->kelompok_pekerjaan ) ? json_decode( $r->kelompok_pekerjaan, true ) : array();
            $r->ia04_data          = ! empty( $r->ia04_data ) ? json_decode( $r->ia04_data, true ) : array();
            $r->ia05_data          = ! empty( $r->ia05_data ) ? json_decode( $r->ia05_data, true ) : array();
        }

        return $results;
    }

    /**
     * Ambil Paket MUK default untuk suatu skema
     *
     * @param int $skema_id
     * @return object|null
     */
    public static function get_default_paket( $skema_id ) {
        global $wpdb;
        $skema_id = intval( $skema_id );
        if ( $skema_id <= 0 ) {
            return null;
        }

        $table = $wpdb->prefix . 'lsp_paket_muk';
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE skema_id = %d AND is_default = 1 LIMIT 1",
            $skema_id
        ) );

        if ( ! $row ) {
            // Coba ambil paket pertama yang aktif
            $row = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM $table WHERE skema_id = %d AND status = 'aktif' ORDER BY id ASC LIMIT 1",
                $skema_id
            ) );
        }

        if ( ! $row ) {
            // Jika benar-benar belum ada, buat paket default otomatis
            $def_id = self::ensure_default_paket( $skema_id );
            if ( $def_id ) {
                return self::get_paket( $def_id );
            }
            return null;
        }

        $row->kelompok_pekerjaan = ! empty( $row->kelompok_pekerjaan ) ? json_decode( $row->kelompok_pekerjaan, true ) : array();
        $row->ia02_data          = ! empty( $row->ia02_data ) ? json_decode( $row->ia02_data, true ) : array();
        $row->ia03_data          = ! empty( $row->ia03_data ) ? json_decode( $row->ia03_data, true ) : array();
        $row->ia04_data          = ! empty( $row->ia04_data ) ? json_decode( $row->ia04_data, true ) : array();
        $row->ia05_data          = ! empty( $row->ia05_data ) ? json_decode( $row->ia05_data, true ) : array();

        return $row;
    }

    /**
     * Memastikan minimal ada 1 paket default untuk skema yang bersangkutan
     * Mengambil data warisan (legacy) dari wp_options jika ada
     *
     * @param int $skema_id
     * @return int ID paket yang dibuat
     */
    public static function ensure_default_paket( $skema_id ) {
        global $wpdb;
        $skema_id = intval( $skema_id );
        if ( $skema_id <= 0 ) {
            return 0;
        }

        $table = $wpdb->prefix . 'lsp_paket_muk';
        $existing = self::get_default_paket( $skema_id );
        if ( $existing ) {
            return $existing;
        }

        $skema = LSP_Cert_Integration::get_skema( $skema_id );
        $skema_title = $skema['title'] ?? ( 'Skema #' . $skema_id );

        // Tarik data yang mungkin sudah tersimpan di wp_options
        $opt_kelompok = get_option( 'lsp_cert_kelompok_pekerjaan_skema_' . $skema_id, null );
        $opt_ia04     = get_option( 'lsp_cert_ia04_skema_' . $skema_id, null );
        $opt_ia05     = get_option( 'lsp_cert_ia05_skema_' . $skema_id, null );

        $data = array(
            'skema_id'           => $skema_id,
            'kode_versi'         => 'Versi 01',
            'nama_paket'         => 'MAPA — ' . $skema_title . ' — Versi 01 (Paket A)',
            'deskripsi'          => 'Paket materi uji kompetensi terpadu (MAPA & FR.IA) standar default.',
            'is_default'         => 1,
            'status'             => 'aktif',
            'kelompok_pekerjaan' => is_array( $opt_kelompok ) ? wp_json_encode( $opt_kelompok ) : null,
            'ia04_data'          => is_array( $opt_ia04 ) ? wp_json_encode( $opt_ia04 ) : null,
            'ia05_data'          => is_array( $opt_ia05 ) ? wp_json_encode( $opt_ia05 ) : null,
            'created_by'         => get_current_user_id() ?: 1,
        );

        $wpdb->insert( $table, $data );
        return self::get_paket( (int) $wpdb->insert_id );
    }

    /**
     * Buat atau perbarui Paket MUK
     *
     * @param array $data
     * @return int|false ID paket
     */
    public static function save_paket( $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_paket_muk';

        $id       = isset( $data['id'] ) ? intval( $data['id'] ) : 0;
        $skema_id = isset( $data['skema_id'] ) ? intval( $data['skema_id'] ) : 0;
        if ( $skema_id <= 0 ) {
            return false;
        }

        $skema = LSP_Cert_Integration::get_skema( $skema_id );
        $skema_title = $skema['title'] ?? ( 'Skema #' . $skema_id );

        $kode_versi = ! empty( $data['kode_versi'] ) ? sanitize_text_field( $data['kode_versi'] ) : 'Versi 01';
        $nama_paket = ! empty( $data['nama_paket'] ) ? sanitize_text_field( $data['nama_paket'] ) : ( 'MAPA — ' . $skema_title . ' — ' . $kode_versi );
        $deskripsi  = isset( $data['deskripsi'] ) ? sanitize_textarea_field( $data['deskripsi'] ) : '';
        $is_default = ! empty( $data['is_default'] ) ? 1 : 0;
        $status     = ! empty( $data['status'] ) ? sanitize_key( $data['status'] ) : 'aktif';

        $save_data = array(
            'skema_id'   => $skema_id,
            'kode_versi' => $kode_versi,
            'nama_paket' => $nama_paket,
            'deskripsi'  => $deskripsi,
            'is_default' => $is_default,
            'status'     => $status,
        );

        if ( isset( $data['kelompok_pekerjaan'] ) ) {
            $save_data['kelompok_pekerjaan'] = is_array( $data['kelompok_pekerjaan'] ) ? wp_json_encode( $data['kelompok_pekerjaan'] ) : $data['kelompok_pekerjaan'];
        }
        if ( isset( $data['ia04_data'] ) ) {
            $save_data['ia04_data'] = is_array( $data['ia04_data'] ) ? wp_json_encode( $data['ia04_data'] ) : $data['ia04_data'];
        }
        if ( isset( $data['ia05_data'] ) ) {
            $save_data['ia05_data'] = is_array( $data['ia05_data'] ) ? wp_json_encode( $data['ia05_data'] ) : $data['ia05_data'];
        }

        // Jika diset sebagai default, reset default paket lain pada skema ini
        if ( $is_default ) {
            $wpdb->update( $table, array( 'is_default' => 0 ), array( 'skema_id' => $skema_id ) );
        }

        if ( $id > 0 ) {
            $wpdb->update( $table, $save_data, array( 'id' => $id ) );
            return $id;
        } else {
            $save_data['created_by'] = get_current_user_id() ?: 1;
            $wpdb->insert( $table, $save_data );
            return (int) $wpdb->insert_id;
        }
    }

    /**
     * Gandakan / Duplikasi (Clone) Paket MUK
     *
     * @param int    $source_id
     * @param string $new_kode_versi
     * @return int|false ID paket baru
     */
    public static function clone_paket( $source_id, $new_kode_versi = '' ) {
        global $wpdb;
        $source = self::get_paket( $source_id );
        if ( ! $source ) {
            return false;
        }

        $skema = LSP_Cert_Integration::get_skema( $source->skema_id );
        $skema_title = $skema['title'] ?? ( 'Skema #' . $source->skema_id );

        if ( empty( $new_kode_versi ) ) {
            // Hitung jumlah versi yang ada untuk memberi nomor versi otomatis
            $table = $wpdb->prefix . 'lsp_paket_muk';
            $count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE skema_id = %d", $source->skema_id ) );
            $new_kode_versi = sprintf( 'Versi %02d', $count + 1 );
        }

        $new_nama = 'MAPA — ' . $skema_title . ' — ' . $new_kode_versi;

        $clone_data = array(
            'skema_id'           => $source->skema_id,
            'kode_versi'         => $new_kode_versi,
            'nama_paket'         => $new_nama,
            'deskripsi'          => 'Duplikasi dari ' . $source->nama_paket,
            'is_default'         => 0,
            'status'             => 'aktif',
            'kelompok_pekerjaan' => ! empty( $source->kelompok_pekerjaan ) ? wp_json_encode( $source->kelompok_pekerjaan ) : null,
            'ia02_data'          => ! empty( $source->ia02_data ) ? wp_json_encode( $source->ia02_data ) : null,
            'ia03_data'          => ! empty( $source->ia03_data ) ? wp_json_encode( $source->ia03_data ) : null,
            'ia04_data'          => ! empty( $source->ia04_data ) ? wp_json_encode( $source->ia04_data ) : null,
            'ia05_data'          => ! empty( $source->ia05_data ) ? wp_json_encode( $source->ia05_data ) : null,
            'created_by'         => get_current_user_id() ?: 1,
        );

        $table = $wpdb->prefix . 'lsp_paket_muk';
        $wpdb->insert( $table, $clone_data );
        return (int) $wpdb->insert_id;
    }

    /**
     * Hapus Paket MUK
     *
     * @param int $id
     * @return bool
     */
    public static function delete_paket( $id ) {
        global $wpdb;
        $id = intval( $id );
        if ( $id <= 0 ) {
            return false;
        }

        $table = $wpdb->prefix . 'lsp_paket_muk';
        $paket = self::get_paket( $id );
        if ( ! $paket ) {
            return false;
        }

        // Cek apakah digunakan oleh jadwal kegiatan
        $table_kegiatan = $wpdb->prefix . 'lsp_kegiatan';
        $used = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_kegiatan WHERE paket_muk_id = %d", $id ) );
        if ( $used > 0 ) {
            return new WP_Error( 'in_use', 'Paket MUK ini sedang digunakan oleh ' . $used . ' jadwal sertifikasi dan tidak dapat dihapus.' );
        }

        return (bool) $wpdb->delete( $table, array( 'id' => $id ) );
    }

    // ==========================================
    // AJAX HANDLERS
    // ==========================================

    public static function ajax_get_paket_by_skema() {
        check_ajax_referer( 'lsp_cert_nonce', 'nonce' );
        $skema_id = intval( $_REQUEST['skema_id'] ?? 0 );
        if ( ! $skema_id ) {
            wp_send_json_error( 'ID Skema tidak valid' );
        }

        $list = self::get_paket_by_skema( $skema_id, 'aktif' );
        $options = array();
        foreach ( $list as $p ) {
            $options[] = array(
                'id'         => $p->id,
                'kode_versi' => $p->kode_versi,
                'nama_paket' => $p->nama_paket,
                'is_default' => (bool) $p->is_default,
            );
        }

        wp_send_json_success( $options );
    }

    public static function ajax_save_paket() {
        check_ajax_referer( 'lsp_cert_nonce', 'nonce' );
        if ( ! current_user_can( 'lsp_cert_manage_instruments' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }

        $id         = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        $skema_id   = isset( $_POST['skema_id'] ) ? intval( $_POST['skema_id'] ) : 0;
        $kode_versi = isset( $_POST['kode_versi'] ) ? sanitize_text_field( wp_unslash( $_POST['kode_versi'] ) ) : '';
        $nama_paket = isset( $_POST['nama_paket'] ) ? sanitize_text_field( wp_unslash( $_POST['nama_paket'] ) ) : '';
        $deskripsi  = isset( $_POST['deskripsi'] ) ? sanitize_textarea_field( wp_unslash( $_POST['deskripsi'] ) ) : '';
        $is_default = ! empty( $_POST['is_default'] ) ? 1 : 0;

        if ( ! $skema_id ) {
            wp_send_json_error( 'ID Skema tidak valid' );
        }

        if ( empty( $kode_versi ) ) {
            wp_send_json_error( 'Kode Versi / Paket wajib diisi' );
        }

        if ( empty( $nama_paket ) ) {
            $skema = LSP_Cert_Integration::get_skema( $skema_id );
            $skema_title = $skema['title'] ?? ( 'Skema #' . $skema_id );
            $nama_paket = 'MAPA — ' . $skema_title . ' — ' . $kode_versi;
        }

        $data = array(
            'skema_id'   => $skema_id,
            'kode_versi' => $kode_versi,
            'nama_paket' => $nama_paket,
            'deskripsi'  => $deskripsi,
            'is_default' => $is_default,
        );

        if ( $id > 0 ) {
            $data['id'] = $id;
        } else {
            // Jika paket baru, wariskan kelompok pekerjaan dari default paket jika ada
            $def_p = self::get_default_paket( $skema_id );
            if ( $def_p && ! empty( $def_p->kelompok_pekerjaan ) ) {
                $data['kelompok_pekerjaan'] = $def_p->kelompok_pekerjaan;
            }
        }

        $saved_id = self::save_paket( $data );
        if ( ! $saved_id ) {
            wp_send_json_error( 'Gagal menyimpan paket MUK' );
        }

        wp_send_json_success( array(
            'message' => 'Paket MUK baru berhasil disimpan',
            'id'      => $saved_id,
        ) );
    }

    public static function ajax_clone_paket() {
        check_ajax_referer( 'lsp_cert_nonce', 'nonce' );
        if ( ! current_user_can( 'lsp_cert_manage_instruments' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }

        $source_id      = intval( $_POST['source_id'] ?? 0 );
        $new_kode_versi = sanitize_text_field( $_POST['new_kode_versi'] ?? '' );

        if ( ! $source_id ) {
            wp_send_json_error( 'ID Paket sumber tidak valid' );
        }

        $new_id = self::clone_paket( $source_id, $new_kode_versi );
        if ( ! $new_id ) {
            wp_send_json_error( 'Gagal menggandakan paket MUK' );
        }

        wp_send_json_success( array(
            'message' => 'Paket MUK berhasil digandakan',
            'new_id'  => $new_id,
        ) );
    }

    public static function ajax_set_default_paket() {
        check_ajax_referer( 'lsp_cert_nonce', 'nonce' );
        if ( ! current_user_can( 'lsp_cert_manage_instruments' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }

        $id = intval( $_POST['id'] ?? 0 );
        $paket = self::get_paket( $id );
        if ( ! $paket ) {
            wp_send_json_error( 'Paket MUK tidak ditemukan' );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'lsp_paket_muk';
        $wpdb->update( $table, array( 'is_default' => 0 ), array( 'skema_id' => $paket->skema_id ) );
        $wpdb->update( $table, array( 'is_default' => 1 ), array( 'id' => $id ) );

        wp_send_json_success( array( 'message' => 'Paket MUK berhasil dijadikan default untuk skema ini' ) );
    }

    public static function ajax_delete_paket() {
        check_ajax_referer( 'lsp_cert_nonce', 'nonce' );
        if ( ! current_user_can( 'lsp_cert_manage_instruments' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }

        $id = intval( $_POST['id'] ?? 0 );
        $res = self::delete_paket( $id );
        if ( is_wp_error( $res ) ) {
            wp_send_json_error( $res->get_error_message() );
        }
        if ( ! $res ) {
            wp_send_json_error( 'Gagal menghapus paket MUK' );
        }

        wp_send_json_success( array( 'message' => 'Paket MUK berhasil dihapus' ) );
    }
}
