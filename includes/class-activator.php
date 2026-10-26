<?php
/**
 * Plugin Activator
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Activator {

    public static function init() {
        // Nothing to initialize for now
    }

    public static function activate() {
        self::create_tables();
        self::set_default_options();
    }

    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Table: Kegiatan Sertifikasi
        $table_kegiatan = $wpdb->prefix . 'lsp_kegiatan';
        $sql_kegiatan = "CREATE TABLE IF NOT EXISTS $table_kegiatan (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            skema_id bigint(20) NOT NULL,
            paket_muk_id bigint(20) DEFAULT NULL,
            tanggal_pelaksanaan date NOT NULL,
            tuk_id bigint(20) NOT NULL,
            lokasi_uji varchar(255) DEFAULT NULL,
            alamat_lokasi_uji text DEFAULT NULL,
            kuota int(11) NOT NULL DEFAULT 20,
            status varchar(20) NOT NULL DEFAULT 'draft',
            asesor_ids text DEFAULT NULL,
            penyusun_id bigint(20) DEFAULT NULL,
            penyusun_nama varchar(255) DEFAULT NULL,
            penyusun_met varchar(100) DEFAULT NULL,
            validator_id bigint(20) DEFAULT NULL,
            validator_nama varchar(255) DEFAULT NULL,
            validator_met varchar(100) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY skema_id (skema_id),
            KEY tuk_id (tuk_id),
            KEY status (status)
        ) $charset_collate;";

        // Table: Pendaftaran (Data Asesi & APL 01)
        $table_pendaftaran = $wpdb->prefix . 'lsp_pendaftaran';
        $sql_pendaftaran = "CREATE TABLE IF NOT EXISTS $table_pendaftaran (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            kegiatan_id bigint(20) NOT NULL,
            paket_muk_id bigint(20) DEFAULT NULL,
            nama_lengkap varchar(255) NOT NULL,
            nik varchar(50) NOT NULL,
            tempat_lahir varchar(100) NOT NULL,
            tanggal_lahir date NOT NULL,
            jenis_kelamin varchar(10) NOT NULL,
            kebangsaan varchar(100) NOT NULL DEFAULT 'Indonesia',
            alamat text NOT NULL,
            kode_pos varchar(10) NOT NULL,
            telepon varchar(20) NOT NULL,
            email varchar(100) NOT NULL,
            pendidikan varchar(100) NOT NULL,
            nama_perusahaan varchar(255) DEFAULT NULL,
            jabatan varchar(100) DEFAULT NULL,
            alamat_kantor text DEFAULT NULL,
            kode_pos_kantor varchar(10) DEFAULT NULL,
            telepon_kantor varchar(20) DEFAULT NULL,
            fax_kantor varchar(20) DEFAULT NULL,
            email_kantor varchar(100) DEFAULT NULL,
            masa_kerja varchar(50) DEFAULT NULL,
            tujuan_asesmen varchar(50) NOT NULL DEFAULT 'sertifikasi',
            persyaratan_dasar longtext DEFAULT NULL,
            persyaratan_admin longtext DEFAULT NULL,
            status_kelengkapan varchar(20) NOT NULL DEFAULT 'pending',
            status_rekomendasi varchar(20) NOT NULL DEFAULT 'pending',
            asesor_id bigint(20) DEFAULT NULL,
            validator_id bigint(20) DEFAULT NULL,
            penyusun_nama varchar(255) DEFAULT NULL,
            penyusun_met varchar(100) DEFAULT NULL,
            validator_nama varchar(255) DEFAULT NULL,
            validator_met varchar(100) DEFAULT NULL,
            tanggal_persetujuan datetime DEFAULT NULL,
            catatan_admin text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY kegiatan_id (kegiatan_id),
            KEY asesor_id (asesor_id),
            KEY status_kelengkapan (status_kelengkapan),
            KEY status_rekomendasi (status_rekomendasi)
        ) $charset_collate;";

        // Table: Jawaban APL 02
        $table_apl02 = $wpdb->prefix . 'lsp_apl02_jawaban';
        $sql_apl02 = "CREATE TABLE IF NOT EXISTS $table_apl02 (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            pendaftaran_id bigint(20) NOT NULL,
            unit_kompetensi_kode varchar(50) NOT NULL,
            unit_kompetensi_judul varchar(255) NOT NULL,
            elemen_nomor int(11) NOT NULL,
            elemen_nama text NOT NULL,
            jawaban varchar(10) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY pendaftaran_id (pendaftaran_id),
            KEY unit_kompetensi_kode (unit_kompetensi_kode)
        ) $charset_collate;";

        // Table: Paket MUK (Bank Data Bundling MAPA & IA)
        $table_paket_muk = $wpdb->prefix . 'lsp_paket_muk';
        $sql_paket_muk = "CREATE TABLE IF NOT EXISTS $table_paket_muk (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            skema_id bigint(20) NOT NULL,
            kode_versi varchar(100) NOT NULL,
            nama_paket varchar(255) NOT NULL,
            deskripsi text DEFAULT NULL,
            is_default tinyint(1) NOT NULL DEFAULT 0,
            status varchar(20) NOT NULL DEFAULT 'aktif',
            kelompok_pekerjaan longtext DEFAULT NULL,
            ia02_data longtext DEFAULT NULL,
            ia03_data longtext DEFAULT NULL,
            ia04_data longtext DEFAULT NULL,
            ia05_data longtext DEFAULT NULL,
            created_by bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY skema_id (skema_id),
            KEY status (status)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_kegiatan );
        dbDelta( $sql_pendaftaran );
        dbDelta( $sql_apl02 );
        dbDelta( $sql_paket_muk );
    }

    private static function set_default_options() {
        add_option( 'lsp_cert_version', LSP_CERT_VERSION );
        add_option( 'lsp_cert_settings', array(
            'require_admin_approval' => true,
            'auto_assign_asesor' => false,
        ));
    }
}
