<?php
/**
 * PH-02 — Test gate integritas SKKNI / Elemen / KUK.
 *
 * Memerlukan WordPress aktif (dijalankan dari CLI):
 *   php tests/ph02-skkni-integrity.php
 *
 * Exit code 0 = semua lulus.
 */

$_SERVER['HTTP_HOST']      = 'localhost';
$_SERVER['REQUEST_URI']    = '/wordpress/';
$_SERVER['SERVER_NAME']    = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';

require dirname( __DIR__, 4 ) . '/wp-load.php';

wp_set_current_user( 1 );

$fail = 0;
function check( $label, $actual, $expected ) {
    global $fail;
    $ok = ( $actual === $expected );
    if ( ! $ok ) { $fail++; }
    printf( "%s %-62s harap=%s dapat=%s\n", $ok ? 'PASS' : 'FAIL', $label, var_export( $expected, true ), var_export( $actual, true ) );
}
function check_true( $label, $cond ) {
    global $fail;
    if ( ! $cond ) { $fail++; }
    printf( "%s %s\n", $cond ? 'PASS' : 'FAIL', $label );
}

// Handler wp_die yang melempar exception agar pengujian tidak menghentikan proses.
add_filter( 'wp_die_handler', function () {
    return function ( $message, $title = '', $args = array() ) {
        throw new RuntimeException( $title . ' :: ' . trim( preg_replace( '/\s+/', ' ', strip_tags( (string) $message ) ) ) );
    };
} );

function coba_enforce( $skema_id, $doc_type ) {
    try {
        LSP_Cert_Document_Printer::enforce_skkni_integrity( $skema_id, $doc_type );
        return array( 'blocked' => false, 'pesan' => '' );
    } catch ( RuntimeException $e ) {
        return array( 'blocked' => true, 'pesan' => $e->getMessage() );
    }
}

echo "== A. Data master lengkap ==\n";

$skemas = get_posts( array( 'post_type' => 'skema', 'posts_per_page' => -1, 'post_status' => 'any' ) );
$complete_id = 0;
foreach ( $skemas as $s ) {
    $st = LSP_Cert_Integration::get_competency_completeness( $s->ID );
    if ( $st['is_complete'] ) { $complete_id = $s->ID; break; }
}

if ( $complete_id ) {
    $st = LSP_Cert_Integration::get_competency_completeness( $complete_id );
    printf( "skema lengkap: #%d (%d unit, %d elemen, %d KUK)\n", $complete_id, $st['total_units'], $st['elemen'], $st['kuk'] );
    check_true( 'A1 is_complete = true', $st['is_complete'] );
    check_true( 'A2 jumlah elemen > 0', $st['elemen'] > 0 );
    check_true( 'A3 jumlah KUK > 0', $st['kuk'] > 0 );

    $units = LSP_Cert_Integration::get_unit_kompetensi( $complete_id );
    $serialized = wp_json_encode( $units );
    check_true( 'A4 tidak ada teks karangan "Melaksanakan unit kompetensi"', strpos( $serialized, 'Melaksanakan unit kompetensi' ) === false );
    check_true( 'A5 tidak ada KUK karangan "Menerapkan prosedur kerja sesuai standar"', strpos( $serialized, 'Menerapkan prosedur kerja sesuai standar' ) === false );
    check_true( 'A6 sumber data = skkni_terstruktur', ( $units[0]['sumber'] ?? '' ) === 'skkni_terstruktur' );

    $r = coba_enforce( $complete_id, 'apl02' );
    check_true( 'A7 FR.APL.02 tetap boleh dicetak', ! $r['blocked'] );
    $r = coba_enforce( $complete_id, 'ia01' );
    check_true( 'A8 FR.IA.01 tetap boleh dicetak', ! $r['blocked'] );
} else {
    echo "SKIP: tidak ada skema dengan data kompetensi lengkap\n";
}

echo "\n== B. Master hanya punya daftar unit (tanpa elemen/KUK) ==\n";

$temp_id = wp_insert_post( array(
    'post_type'   => 'skema',
    'post_title'  => 'TEMP-PH02-UJI',
    'post_status' => 'draft',
) );
update_post_meta( $temp_id, 'unit_kompetensi', "LSP.TEST.001 | Unit Uji Coba Satu\nLSP.TEST.002 | Unit Uji Coba Dua" );

$units_t = LSP_Cert_Integration::get_unit_kompetensi( $temp_id );
check( 'B1 jumlah unit terbaca', count( $units_t ), 2 );
check_true( 'B2 elemen unit pertama kosong (tidak dikarang)', empty( $units_t[0]['elemen'] ) );
check_true( 'B3 elemen unit kedua kosong', empty( $units_t[1]['elemen'] ) );
check( 'B4 sumber data = meta_skema', $units_t[0]['sumber'] ?? '', 'meta_skema' );

$st_t = LSP_Cert_Integration::get_competency_completeness( $temp_id );
check( 'B5 is_complete = false', $st_t['is_complete'], false );
check( 'B6 total unit', $st_t['total_units'], 2 );
check( 'B7 unit lengkap', $st_t['units_lengkap'], 0 );
check( 'B8 jumlah elemen', $st_t['elemen'], 0 );
check( 'B9 jumlah KUK', $st_t['kuk'], 0 );
check_true( 'B10 daftar missing berisi 2 unit', count( $st_t['missing'] ) === 2 );
check( 'B11 alasan missing', $st_t['missing'][0]['alasan'] ?? '', 'Elemen & KUK belum tersedia' );

$r = coba_enforce( $temp_id, 'apl02' );
check_true( 'B12 FR.APL.02 DITOLAK saat elemen/KUK kosong', $r['blocked'] );
check_true( 'B13 pesan menyebut Elemen/KUK', stripos( $r['pesan'], 'Elemen/KUK Belum Lengkap' ) !== false );
check_true( 'B14 pesan menyebut unit bermasalah', stripos( $r['pesan'], 'LSP.TEST.001' ) !== false );

$r = coba_enforce( $temp_id, 'ia01' );
check_true( 'B15 FR.IA.01 DITOLAK', $r['blocked'] );
$r = coba_enforce( $temp_id, 'all' );
check_true( 'B16 paket APL.01+02 DITOLAK', $r['blocked'] );
$r = coba_enforce( $temp_id, 'bundle_all' );
check_true( 'B17 seluruh berkas DITOLAK', $r['blocked'] );

echo "\n== C. Dokumen tanpa Elemen/KUK tidak terpengaruh ==\n";
foreach ( array( 'apl01', 'mapa01_obs', 'mapa02', 'ak01' ) as $dt ) {
    $r = coba_enforce( $temp_id, $dt );
    check_true( "C {$dt} tetap boleh dicetak", ! $r['blocked'] );
}

echo "\n== D. Skema tanpa unit kompetensi sama sekali ==\n";
$empty_id = wp_insert_post( array( 'post_type' => 'skema', 'post_title' => 'TEMP-PH02-KOSONG', 'post_status' => 'draft' ) );
$st_e = LSP_Cert_Integration::get_competency_completeness( $empty_id );
check( 'D1 total unit = 0', $st_e['total_units'], 0 );
check( 'D2 is_complete = false', $st_e['is_complete'], false );
$r = coba_enforce( $empty_id, 'apl02' );
check_true( 'D3 FR.APL.02 DITOLAK', $r['blocked'] );
check_true( 'D4 pesan menyebut unit kompetensi belum ada', stripos( $r['pesan'], 'belum memiliki daftar Unit Kompetensi' ) !== false );

// Bersihkan fixture
wp_delete_post( $temp_id, true );
wp_delete_post( $empty_id, true );
check_true( 'E1 fixture sementara terhapus', get_post( $temp_id ) === null && get_post( $empty_id ) === null );

echo "\n" . ( $fail === 0 ? "SEMUA LULUS\n" : "{$fail} GAGAL\n" );
exit( $fail === 0 ? 0 : 1 );
