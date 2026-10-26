<?php
/**
 * PH-03b — Test gate persyaratan dasar & administratif.
 *
 * Persyaratan administratif berlaku sama untuk semua skema dan disimpan sebagai
 * pengaturan global Core; persyaratan dasar tetap per skema. Tidak ada daftar
 * bawaan yang dikarang di plugin Certification.
 *
 * Jalankan: php tests/ph03b-persyaratan.php
 */

$_SERVER['HTTP_HOST']      = 'localhost';
$_SERVER['REQUEST_URI']    = '/wordpress/';
$_SERVER['SERVER_NAME']    = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';

require dirname( __DIR__, 4 ) . '/wp-load.php';
wp_set_current_user( 1 );

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

echo "== A. Skema yang meta-nya terisi ==\n";
$skema = get_posts( array( 'post_type' => 'skema', 'posts_per_page' => 1, 'post_status' => 'any' ) );
$skema_id = $skema ? $skema[0]->ID : 0;

if ( ! $skema_id ) {
    echo "SKIP: tidak ada Master Skema\n";
} else {
    $dasar = LSP_Cert_Integration::get_persyaratan_dasar( $skema_id );
    $admin = LSP_Cert_Integration::get_persyaratan_admin( $skema_id );
    cek_true( 'A1 persyaratan dasar terbaca dari meta skema', count( $dasar ) > 0 );
    cek_true( 'A2 persyaratan administratif terbaca', count( $admin ) > 0 );
    cek_true( 'A3 tidak ada teks generik "Fotokopi Ijazah Pendidikan Terakhir"', ! in_array( 'Fotokopi Ijazah Pendidikan Terakhir', $dasar, true ) );
}

echo "\n== B. Skema tanpa meta → administratif dari pengaturan global ==\n";
$temp_id = wp_insert_post( array( 'post_type' => 'skema', 'post_title' => 'TEMP-PH03B', 'post_status' => 'draft' ) );

$dasar_kosong = LSP_Cert_Integration::get_persyaratan_dasar( $temp_id );
cek( 'B1 persyaratan dasar kosong (tidak dikarang)', count( $dasar_kosong ), 0 );

$admin_global = LSP_Cert_Integration::get_persyaratan_admin( $temp_id );
cek( 'B2 administratif mengikuti pengaturan global', count( $admin_global ), 2 );
cek_true( 'B3 isi global menyebut KTP', stripos( implode( ' ', $admin_global ), 'KTP' ) !== false );

echo "\n== C. Pengaturan global bisa diubah ==\n";
$sebelum = get_option( 'lsp_core_persyaratan_admin', null );
update_option( 'lsp_core_persyaratan_admin', "1) KTP asli dan fotokopi;\n2) Pas foto 4x6 latar merah;\n3) Surat pernyataan sehat." );

$admin_baru = LSP_Cert_Integration::get_persyaratan_admin( $temp_id );
cek( 'C1 jumlah baris baru', count( $admin_baru ), 3 );
cek( 'C2 baris pertama', $admin_baru[0], '1) KTP asli dan fotokopi;' );
cek( 'C3 baris ketiga', $admin_baru[2], '3) Surat pernyataan sehat.' );

// Meta skema tetap menang atas pengaturan global
update_post_meta( $temp_id, 'persyaratan_admin', "1) Dokumen khusus skema ini." );
$admin_meta = LSP_Cert_Integration::get_persyaratan_admin( $temp_id );
cek( 'C4 meta skema menang atas global', $admin_meta[0], '1) Dokumen khusus skema ini.' );

// Pulihkan
if ( null === $sebelum ) {
    delete_option( 'lsp_core_persyaratan_admin' );
} else {
    update_option( 'lsp_core_persyaratan_admin', $sebelum );
}
wp_delete_post( $temp_id, true );
cek_true( 'C5 fixture terhapus', get_post( $temp_id ) === null );

$pulih = class_exists( 'LSP_Core_Management' ) && method_exists( 'LSP_Core_Management', 'get_persyaratan_admin_global' )
    ? LSP_Core_Management::get_persyaratan_admin_global()
    : (string) get_option( 'lsp_core_persyaratan_admin', '' );
cek_true( 'C6 pengaturan global pulih', null === $sebelum ? ( '' !== $pulih ) : ( $pulih === $sebelum ) );

echo "\n" . ( $fail === 0 ? "SEMUA LULUS\n" : "{$fail} GAGAL\n" );
exit( $fail === 0 ? 0 : 1 );
