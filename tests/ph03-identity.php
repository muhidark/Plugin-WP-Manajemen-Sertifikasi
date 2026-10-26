<?php
/**
 * PH-03 — Test gate identitas & metadata dokumen.
 *
 * Memastikan tidak ada nama penyusun/validator atau Nomor MET contoh yang
 * tercetak otomatis, dan template tetap berfungsi saat metadata kosong.
 *
 * Jalankan: php tests/ph03-identity.php
 */

$_SERVER['HTTP_HOST']      = 'localhost';
$_SERVER['REQUEST_URI']    = '/wordpress/';
$_SERVER['SERVER_NAME']    = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';

require dirname( __DIR__, 4 ) . '/wp-load.php';
wp_set_current_user( 1 );

define( 'LSP_CERT_TESTING', true );

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

$dummy = array( "'Masriah'", "'Eko Prasyanto'", "'MET.000.032423 2019'", "'MET.000.014249 2019'", 'MET.000.032423', 'MET.000.014249' );

echo "== A. Tidak ada identitas contoh di kode template ==\n";
$berkas = glob( dirname( __DIR__ ) . '/templates/docs/*.php' );
$berkas[] = dirname( __DIR__ ) . '/includes/class-document-printer.php';
$temuan = array();
foreach ( $berkas as $f ) {
    $isi = file_get_contents( $f );
    foreach ( $dummy as $d ) {
        $pos = strpos( $isi, $d );
        if ( false !== $pos ) {
            $temuan[] = basename( $f ) . ' → ' . $d;
        }
    }
}
cek( 'A1 jumlah kemunculan identitas contoh', count( $temuan ), 0 );
if ( $temuan ) {
    foreach ( $temuan as $t ) { echo "   - {$t}\n"; }
}

echo "\n== B. Helper daftar_identitas_kosong() ==\n";
$kosong_semua = LSP_Cert_Document_Printer::daftar_identitas_kosong( array( 'name' => '', 'no_reg' => '' ), array( 'name' => '', 'no_reg' => '' ) );
cek( 'B1 semua kosong → 4 label', count( $kosong_semua ), 4 );
$lengkap = LSP_Cert_Document_Printer::daftar_identitas_kosong( array( 'name' => 'A', 'no_reg' => 'MET.1' ), array( 'name' => 'B', 'no_reg' => 'MET.2' ) );
cek( 'B2 lengkap → 0 label', count( $lengkap ), 0 );
$sebagian = LSP_Cert_Document_Printer::daftar_identitas_kosong( array( 'name' => 'A', 'no_reg' => '' ), array( 'name' => '', 'no_reg' => 'MET.2' ) );
cek( 'B3 sebagian → 2 label', count( $sebagian ), 2 );

echo "\n== C. Render seluruh jenis dokumen (data apa adanya) ==\n";
$kegiatan = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}lsp_kegiatan ORDER BY id LIMIT 1" );
$pendaftaran = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}lsp_pendaftaran ORDER BY id LIMIT 1" );

if ( ! $kegiatan || ! $pendaftaran ) {
    echo "SKIP: data kegiatan/pendaftaran tidak tersedia\n";
} else {
    $docs_p = array( 'apl01', 'apl02', 'all', 'mapa01_obs', 'mapa01_dit', 'mapa01_vp', 'ak01', 'ak02', 'ak03', 'ak04', 'ak05', 'ak06', 'ak07', 'ia01', 'ia02', 'ia03', 'ia04a', 'ia04b', 'ia05a', 'ia05b', 'ia05c' );
    $docs_k = array( 'mapa02' );
    $gagal_render = array(); $ada_dummy = array();

    foreach ( $docs_p as $d ) {
        ob_start();
        try { LSP_Cert_Document_Printer::render_print_page( $pendaftaran->id, $d ); } catch ( Throwable $e ) {}
        $html = ob_get_clean();
        if ( strlen( $html ) < 1000 || stripos( $html, 'Fatal error' ) !== false ) { $gagal_render[] = $d; }
        if ( preg_match( '/Masriah|Eko Prasyanto|MET\.000\.032423|MET\.000\.014249/', $html ) ) { $ada_dummy[] = $d; }
    }
    foreach ( $docs_k as $d ) {
        ob_start();
        try { LSP_Cert_Document_Printer::render_kegiatan_print_page( $kegiatan->id, $d ); } catch ( Throwable $e ) {}
        $html = ob_get_clean();
        if ( strlen( $html ) < 1000 ) { $gagal_render[] = $d; }
        if ( preg_match( '/Masriah|Eko Prasyanto|MET\.000\.032423|MET\.000\.014249/', $html ) ) { $ada_dummy[] = $d; }
    }

    cek( 'C1 dokumen gagal/kosong', count( $gagal_render ), 0 );
    if ( $gagal_render ) { echo '   - ' . implode( ', ', $gagal_render ) . "\n"; }
    cek( 'C2 dokumen memuat identitas contoh', count( $ada_dummy ), 0 );
    if ( $ada_dummy ) { echo '   - ' . implode( ', ', $ada_dummy ) . "\n"; }
    cek_true( 'C3 jumlah dokumen diuji ≥ 20', ( count( $docs_p ) + count( $docs_k ) ) >= 20 );

    echo "\n== D. Metadata kosong: cetak kosong + peringatan admin ==\n";
    $backup_k = $kegiatan;
    $backup_p = $pendaftaran;
    file_put_contents( '/tmp/ph03_backup.php', "<?php\nreturn " . var_export( array( 'k' => (array) $backup_k, 'p' => (array) $backup_p ), true ) . ";\n" );

    $wpdb->update( $wpdb->prefix . 'lsp_kegiatan', array( 'penyusun_nama' => '', 'penyusun_met' => '', 'validator_nama' => '', 'validator_met' => '', 'penyusun_id' => 0, 'validator_id' => 0 ), array( 'id' => $kegiatan->id ) );
    $wpdb->update( $wpdb->prefix . 'lsp_pendaftaran', array( 'penyusun_nama' => '', 'penyusun_met' => '', 'validator_nama' => '', 'validator_met' => '' ), array( 'id' => $pendaftaran->id ) );

    ob_start();
    try { LSP_Cert_Document_Printer::render_kegiatan_print_page( $kegiatan->id, 'mapa02' ); } catch ( Throwable $e ) {}
    $html_kosong = ob_get_clean();

    cek_true( 'D1 peringatan admin muncul', strpos( $html_kosong, 'identitas tim asesor belum lengkap' ) !== false );
    cek_true( 'D2 peringatan ditandai lsp-no-print', preg_match( '/class="lsp-no-print"[^>]*>\s*<strong>Peringatan admin/', $html_kosong ) === 1 );
    cek_true( 'D3 tidak ada nama contoh saat kosong', ! preg_match( '/Masriah|Eko Prasyanto/', $html_kosong ) );
    cek_true( 'D4 dokumen tetap tercetak', strlen( $html_kosong ) > 1000 );

    // Pulihkan
    $bak = include '/tmp/ph03_backup.php';
    $wpdb->update( $wpdb->prefix . 'lsp_kegiatan', array(
        'penyusun_id' => $bak['k']['penyusun_id'], 'penyusun_nama' => $bak['k']['penyusun_nama'], 'penyusun_met' => $bak['k']['penyusun_met'],
        'validator_id' => $bak['k']['validator_id'], 'validator_nama' => $bak['k']['validator_nama'], 'validator_met' => $bak['k']['validator_met'],
    ), array( 'id' => $bak['k']['id'] ) );
    $wpdb->update( $wpdb->prefix . 'lsp_pendaftaran', array(
        'penyusun_nama' => $bak['p']['penyusun_nama'], 'penyusun_met' => $bak['p']['penyusun_met'],
        'validator_nama' => $bak['p']['validator_nama'], 'validator_met' => $bak['p']['validator_met'],
    ), array( 'id' => $bak['p']['id'] ) );

    $k2 = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}lsp_kegiatan WHERE id = " . (int) $kegiatan->id, ARRAY_A );
    $p2 = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}lsp_pendaftaran WHERE id = " . (int) $pendaftaran->id, ARRAY_A );

    // Bandingkan kolom identitas saja (urutan kolom dari get_row bisa berbeda).
    $kolom_k = array( 'penyusun_id', 'penyusun_nama', 'penyusun_met', 'validator_id', 'validator_nama', 'validator_met' );
    $sama_k = true;
    foreach ( $kolom_k as $c ) {
        if ( (string) $k2[ $c ] !== (string) $bak['k'][ $c ] ) { $sama_k = false; }
    }
    cek_true( 'D5 data kegiatan pulih identik', $sama_k );

    $kolom_p = array( 'penyusun_nama', 'penyusun_met', 'validator_nama', 'validator_met' );
    $sama_p = true;
    foreach ( $kolom_p as $c ) {
        if ( (string) $p2[ $c ] !== (string) $bak['p'][ $c ] ) { $sama_p = false; }
    }
    cek_true( 'D6 data pendaftaran pulih identik', $sama_p );
}

echo "\n" . ( $fail === 0 ? "SEMUA LULUS\n" : "{$fail} GAGAL\n" );
exit( $fail === 0 ? 0 : 1 );
