<?php
/**
 * Versioning & migrasi skema database.
 *
 * Sebelumnya pemeriksaan skema (SHOW COLUMNS / SHOW INDEX) dijalankan pada setiap
 * request. Sekarang seluruh perubahan skema dikumpulkan di sini dan hanya dijalankan
 * ketika versi database berbeda dari versi plugin.
 *
 * Aturan:
 *   - setiap langkah WAJIB idempoten (IF NOT EXISTS / cek kolom / cek indeks);
 *   - naikkan LSP_CERT_DB_VERSION setiap kali skema berubah;
 *   - jangan pernah menurunkan versi.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Migrations {

    /** Opsi penyimpan versi skema yang sudah diterapkan. */
    const OPTION = 'lsp_cert_db_version';

    /**
     * Versi skema yang dituju.
     *
     * @return string
     */
    public static function target() {
        return defined( 'LSP_CERT_DB_VERSION' ) ? (string) LSP_CERT_DB_VERSION : '0';
    }

    /**
     * Versi skema yang tersimpan pada instalasi ini.
     *
     * @return string
     */
    public static function installed() {
        return (string) get_option( self::OPTION, '0' );
    }

    /**
     * Jalankan migrasi hanya bila versi berbeda. Pemeriksaan ini satu query opsi.
     *
     * @return bool True bila migrasi dijalankan.
     */
    public static function maybe_migrate() {
        if ( self::installed() === self::target() ) {
            return false;
        }

        self::run();
        update_option( self::OPTION, self::target() );

        return true;
    }

    /**
     * Terapkan seluruh langkah migrasi. Semua langkah idempoten, sehingga aman
     * dijalankan pada instalasi baru maupun instalasi lama.
     */
    public static function run() {
        require_once LSP_CERT_PLUGIN_DIR . 'includes/class-activator.php';

        // Langkah 1 — tabel dasar (CREATE TABLE IF NOT EXISTS via dbDelta).
        LSP_Cert_Activator::create_tables();

        // Langkah 2 — kolom tambahan pada wp_lsp_kegiatan.
        LSP_Cert_Activator::upgrade_columns();

        // Langkah 3 — indeks unik peserta (pengaman konkurensi PH-07).
        if ( class_exists( 'LSP_Cert_Database' ) ) {
            LSP_Cert_Database::ensure_unique_indexes( true );
        }
    }
}
