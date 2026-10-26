<?php
/**
 * PH-08 — Test gate versioning & migrasi database.
 *
 * Memeriksa: versi skema tercatat, migrasi idempoten, tidak ada pemeriksaan skema
 * pada setiap request, instalasi baru berhasil, dan data lama tidak hilang.
 *
 * Jalankan: php tests/ph08-db-migration.php
 */

$_SERVER['HTTP_HOST']      = 'localhost';
$_SERVER['REQUEST_URI']    = '/wordpress/';
$_SERVER['SERVER_NAME']    = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';

require dirname( __DIR__, 4 ) . '/wp-load.php';

global $wpdb;
$fail = 0;
function cek( $label, $actual, $expected ) {
    global $fail;
    $ok = ( $actual === $expected );
    if ( ! $ok ) { $fail++; }
    printf( "%s %-58s harap=%s dapat=%s\n", $ok ? 'PASS' : 'FAIL', $label, var_export( $expected, true ), var_export( $actual, true ) );
}
function cek_true( $label, $cond ) {
    global $fail;
    if ( ! $cond ) { $fail++; }
    printf( "%s %s\n", $cond ? 'PASS' : 'FAIL', $label );
}

echo "== A. Versi skema ==\n";
cek_true( 'A1 konstanta LSP_CERT_DB_VERSION terdefinisi', defined( 'LSP_CERT_DB_VERSION' ) );
cek( 'A2 opsi versi = versi plugin', LSP_Cert_Migrations::installed(), LSP_Cert_Migrations::target() );

echo "\n== B. Tidak ada pemeriksaan skema tiap request ==\n";
$q_awal = (int) $wpdb->num_queries;
$dijalankan = LSP_Cert_Migrations::maybe_migrate();
$q_selisih = (int) $wpdb->num_queries - $q_awal;
cek( 'B1 maybe_migrate() tidak menjalankan migrasi', $dijalankan, false );
cek_true( 'B2 jumlah query ≤ 1 (hanya baca opsi)', $q_selisih <= 1 );

echo "\n== C. Migrasi ulang setelah versi dihapus & indeks dibuang ==\n";
$tabel_p = $wpdb->prefix . 'lsp_pendaftaran';

$jumlah_sebelum = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tabel_p}" );

// Simulasikan instalasi lama: belum ada versi skema, indeks unik belum terpasang.
delete_option( LSP_Cert_Migrations::OPTION );
@$wpdb->query( "ALTER TABLE `{$tabel_p}` DROP INDEX `uniq_kegiatan_nik`" );
@$wpdb->query( "ALTER TABLE `{$tabel_p}` DROP INDEX `uniq_kegiatan_email`" );

$indeks_hilang = $wpdb->get_var( $wpdb->prepare( "SHOW INDEX FROM `{$tabel_p}` WHERE Key_name = %s", 'uniq_kegiatan_nik' ) );
cek_true( 'C1 kondisi awal: indeks belum ada', empty( $indeks_hilang ) );

$dijalankan = LSP_Cert_Migrations::maybe_migrate();
cek( 'C2 migrasi dijalankan', $dijalankan, true );
cek( 'C3 versi tercatat kembali', LSP_Cert_Migrations::installed(), LSP_Cert_Migrations::target() );

foreach ( array( 'uniq_kegiatan_nik', 'uniq_kegiatan_email' ) as $idx ) {
    $ada = $wpdb->get_var( $wpdb->prepare( "SHOW INDEX FROM `{$tabel_p}` WHERE Key_name = %s", $idx ) );
    cek_true( "C4 indeks {$idx} terpasang kembali", ! empty( $ada ) );
}

$jumlah_sesudah = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tabel_p}" );
cek( 'C5 data pendaftaran tidak hilang', $jumlah_sesudah, $jumlah_sebelum );

echo "\n== D. Idempoten: migrasi kedua tidak mengubah apa pun ==\n";
$struktur_1 = $wpdb->get_row( "SHOW CREATE TABLE {$tabel_p}", ARRAY_A );
delete_option( LSP_Cert_Migrations::OPTION );
LSP_Cert_Migrations::maybe_migrate();
$struktur_2 = $wpdb->get_row( "SHOW CREATE TABLE {$tabel_p}", ARRAY_A );
cek_true( 'D1 struktur tabel identik setelah migrasi ulang', $struktur_1 === $struktur_2 );
cek( 'D2 data tetap utuh', (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tabel_p}" ), $jumlah_sebelum );

echo "\n== E. Instalasi baru (prefix sementara) ==\n";
$prefix_asli = $wpdb->prefix;
$prefix_uji  = 'wptest_' . wp_rand( 1000, 9999 ) . '_';
$wpdb->prefix = $prefix_uji;

LSP_Cert_Activator::create_tables();
LSP_Cert_Activator::upgrade_columns();

$tabel_dibuat = 0;
foreach ( array( 'lsp_kegiatan', 'lsp_pendaftaran', 'lsp_apl02_jawaban', 'lsp_paket_muk' ) as $nama ) {
    $tabel = $prefix_uji . $nama;
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $tabel ) ) === $tabel ) {
        $tabel_dibuat++;
    }
}
cek( 'E1 empat tabel dibuat', $tabel_dibuat, 4 );

$kolom_lokasi = $wpdb->get_results( "SHOW COLUMNS FROM `{$prefix_uji}lsp_kegiatan` LIKE 'lokasi_uji'" );
cek_true( 'E2 kolom lokasi_uji ikut dibuat', ! empty( $kolom_lokasi ) );

// Bersihkan tabel uji
foreach ( array( 'lsp_kegiatan', 'lsp_pendaftaran', 'lsp_apl02_jawaban', 'lsp_paket_muk' ) as $nama ) {
    $wpdb->query( "DROP TABLE IF EXISTS `{$prefix_uji}{$nama}`" );
}
$wpdb->prefix = $prefix_asli;

$sisa = 0;
foreach ( array( 'lsp_kegiatan', 'lsp_pendaftaran', 'lsp_apl02_jawaban', 'lsp_paket_muk' ) as $nama ) {
    $tabel = $prefix_uji . $nama;
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $tabel ) ) === $tabel ) {
        $sisa++;
    }
}
cek( 'E3 tabel uji dibersihkan', $sisa, 0 );

echo "\n== F. Tabel produksi tetap utuh ==\n";
cek_true( 'F1 prefix dikembalikan', $wpdb->prefix === $prefix_asli );
cek( 'F2 data pendaftaran masih utuh', (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tabel_p}" ), $jumlah_sebelum );

echo "\n" . ( $fail === 0 ? "SEMUA LULUS\n" : "{$fail} GAGAL\n" );
exit( $fail === 0 ? 0 : 1 );
