<?php
/**
 * MICRO-FIX v1.1.2 — uji MF-01 s.d. MF-18.
 *
 * FIX-01: fallback nama asesor contoh ("Masriah Hebat") pada tampilan admin
 *         cetak dokumen diganti netral ("Belum ditentukan").
 * FIX-02: `render_kegiatan_print_page()` tidak lagi membaca `$pendaftaran->paket_muk_id`.
 *
 * Jalankan: php tests/mf-112-microfix.php
 */

$_SERVER['HTTP_HOST']      = 'localhost';
$_SERVER['REQUEST_URI']    = '/wordpress/';
$_SERVER['SERVER_NAME']    = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';

define( 'WP_ADMIN', true );
define( 'LSP_CERT_TESTING', true );

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

function identitas( $uid ) {
    $user = get_userdata( (int) $uid );
    if ( ! $user ) { return array( 'cookie' => '', 'nonce' => '' ); }

    $exp   = time() + 3600;
    $token = WP_Session_Tokens::get_instance( (int) $uid )->create( $exp );
    $hash  = $user->user_pass;
    $frag  = ( str_starts_with( $hash, '$P$' ) || str_starts_with( $hash, '$2y$' ) ) ? substr( $hash, 8, 4 ) : substr( $hash, -4 );

    $cookie = array();
    foreach ( array( 'auth' => AUTH_COOKIE, 'secure_auth' => SECURE_AUTH_COOKIE, 'logged_in' => LOGGED_IN_COOKIE ) as $scheme => $nama ) {
        $key  = wp_hash( $user->user_login . '|' . $frag . '|' . $exp . '|' . $token, $scheme );
        $hmac = hash_hmac( 'sha256', $user->user_login . '|' . $exp . '|' . $token, $key );
        $cookie[] = $nama . '=' . $user->user_login . '|' . $exp . '|' . $token . '|' . $hmac;
    }

    $login = $user->user_login . '|' . $exp . '|' . $token . '|' . hash_hmac(
        'sha256',
        $user->user_login . '|' . $exp . '|' . $token,
        wp_hash( $user->user_login . '|' . $frag . '|' . $exp . '|' . $token, 'logged_in' )
    );
    $simpan = $_COOKIE;
    $_COOKIE[ LOGGED_IN_COOKIE ] = $login;
    wp_set_current_user( (int) $uid );
    $nonce = wp_create_nonce( 'lsp_cert_nonce' );
    $_COOKIE = $simpan;
    wp_set_current_user( 0 );

    return array( 'cookie' => implode( '; ', $cookie ), 'nonce' => $nonce );
}

function minta( $url, $cookie = '', $post = null ) {
    $ch  = curl_init( $url );
    $opt = array( CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30 );
    if ( $cookie ) { $opt[ CURLOPT_COOKIE ] = $cookie; }
    if ( null !== $post ) {
        $opt[ CURLOPT_POST ]       = true;
        $opt[ CURLOPT_POSTFIELDS ] = http_build_query( $post, '', '&' );
    }
    curl_setopt_array( $ch, $opt );
    $body = curl_exec( $ch );
    $code = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );
    curl_close( $ch );
    return array( 'code' => $code, 'body' => (string) $body, 'ukuran' => strlen( (string) $body ) );
}

/** Ambil isi satu method (dengan kurung kurawal berpasangan). */
function badan_method( $file, $nama ) {
    $txt = file_get_contents( $file );
    $pos = strpos( $txt, 'function ' . $nama );
    if ( false === $pos ) { return ''; }
    $mulai = strpos( $txt, '{', $pos );
    $dalam = 0;
    for ( $i = $mulai; $i < strlen( $txt ); $i++ ) {
        if ( '{' === $txt[ $i ] ) { $dalam++; }
        elseif ( '}' === $txt[ $i ] ) { $dalam--; if ( 0 === $dalam ) { return substr( $txt, $mulai, $i - $mulai + 1 ); } }
    }
    return '';
}

$admin   = identitas( 1 );
$base    = 'http://localhost/wordpress/wp-admin/admin.php';
$ajax    = 'http://localhost/wordpress/wp-admin/admin-ajax.php';
$tabel_k = $wpdb->prefix . 'lsp_kegiatan';
$tabel_p = $wpdb->prefix . 'lsp_pendaftaran';
$tabel_j = $wpdb->prefix . 'lsp_apl02_jawaban';
$plugin  = dirname( __DIR__ );

$skema_list = get_posts( array( 'post_type' => 'skema', 'posts_per_page' => 1, 'post_status' => 'any' ) );
if ( ! $skema_list ) { echo "SKIP: tidak ada Master Skema\n"; exit( 0 ); }
$skema_id = (int) $skema_list[0]->ID;

// --- fixture: asesor bernama, kegiatan, dua peserta (berasesor & tanpa asesor)
$slug_asesor = 'mf112_asesor_' . wp_rand( 1000, 9999 );
$asesor_id   = wp_insert_user( array(
    'user_login' => $slug_asesor, 'user_pass' => wp_generate_password( 16 ),
    'user_email' => $slug_asesor . '@example.test',
    'first_name' => 'Asesor', 'last_name' => 'Microfix', 'role' => 'asesor',
) );
update_user_meta( $asesor_id, 'nomor_met', 'MET.MF.000001.2026' );
$nama_asesor = trim( get_userdata( $asesor_id )->first_name . ' ' . get_userdata( $asesor_id )->last_name );

$wpdb->insert( $tabel_k, array(
    'skema_id' => $skema_id, 'tanggal_pelaksanaan' => date( 'Y-m-d', strtotime( '+7 days' ) ),
    'tuk_id' => 0, 'kuota' => 5, 'status' => 'dibuka', 'asesor_ids' => wp_json_encode( array( $asesor_id ) ),
) );
$kegiatan_id = (int) $wpdb->insert_id;

$buat_peserta = function ( $nik, $asesor ) use ( $wpdb, $tabel_p, $kegiatan_id ) {
    $wpdb->insert( $tabel_p, array(
        'kegiatan_id' => $kegiatan_id, 'nama_lengkap' => 'Peserta Microfix ' . $nik, 'nik' => $nik,
        'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1995-05-05', 'jenis_kelamin' => 'Laki-laki',
        'alamat' => 'Jalan Microfix', 'kode_pos' => '40111', 'telepon' => '081200000002',
        'email' => 'mf' . $nik . '@example.test', 'pendidikan' => 'S1', 'asesor_id' => $asesor,
    ) );
    return (int) $wpdb->insert_id;
};
$p_berasesor = $buat_peserta( '3273000000000801', $asesor_id );
$p_tanpa     = $buat_peserta( '3273000000000802', null );

echo "== FIX-01 — nama asesor pada tampilan admin ==\n";

$url_mapa1_p1 = "{$base}?page=lsp-cetak-dokumen&tab=mapa1&subtab=print&skema_id={$skema_id}&kegiatan_id={$kegiatan_id}&pendaftaran_id={$p_berasesor}";
$url_mapa1_p2 = "{$base}?page=lsp-cetak-dokumen&tab=mapa1&subtab=print&skema_id={$skema_id}&kegiatan_id={$kegiatan_id}&pendaftaran_id={$p_tanpa}";

$r = minta( $url_mapa1_p1, $admin['cookie'] );
cek( 'MF-01a halaman MAPA.01 admin terbuka', $r['code'], 200 );
cek_true( 'MF-01b nama asesor aktual tampil', false !== strpos( $r['body'], $nama_asesor ) );

$r = minta( $url_mapa1_p2, $admin['cookie'] );
cek_true( 'MF-02a fallback netral "Belum ditentukan"', false !== strpos( $r['body'], 'Belum ditentukan' ) );
cek_true( 'MF-02b nama contoh tidak muncul', false === strpos( $r['body'], 'Masriah' ) );

$url_mapa2_p1 = "{$base}?page=lsp-cetak-dokumen&tab=mapa2&subtab=print&skema_id={$skema_id}&kegiatan_id={$kegiatan_id}&pendaftaran_id={$p_berasesor}";
$url_mapa2_p2 = "{$base}?page=lsp-cetak-dokumen&tab=mapa2&subtab=print&skema_id={$skema_id}&kegiatan_id={$kegiatan_id}&pendaftaran_id={$p_tanpa}";

$r = minta( $url_mapa2_p1, $admin['cookie'] );
cek( 'MF-03a halaman MAPA.02 admin terbuka', $r['code'], 200 );
cek_true( 'MF-03b nama asesor aktual tampil', false !== strpos( $r['body'], $nama_asesor ) );

$r = minta( $url_mapa2_p2, $admin['cookie'] );
cek_true( 'MF-04a fallback netral tampil', false !== strpos( $r['body'], 'Belum ditentukan' ) );
cek_true( 'MF-04b nama contoh tidak muncul', false === strpos( $r['body'], 'Masriah' ) );

// MF-05: literal nama contoh di seluruh berkas runtime (kecuali tests/docs)
$sisa = array();
$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $plugin, FilesystemIterator::SKIP_DOTS ) );
foreach ( $it as $f ) {
    $path = $f->getPathname();
    if ( ! $f->isFile() ) { continue; }
    if ( ! preg_match( '/\.(php|js|css|html|txt|md)$/', $path ) ) { continue; }
    if ( false !== strpos( $path, '/tests/' ) || false !== strpos( $path, '/docs/' ) || false !== strpos( $path, '/.git/' ) ) { continue; }
    $isi = file_get_contents( $path );
    if ( preg_match( '/Masriah|Eko Prasyanto/', $isi ) ) { $sisa[] = str_replace( $plugin . '/', '', $path ); }
}
cek( 'MF-05 literal nama contoh di runtime', count( $sisa ), 0 );
if ( $sisa ) { echo '       → ' . implode( ', ', $sisa ) . "\n"; }

echo "\n== FIX-02 — sumber Paket MUK ==\n";

$berkas_printer = $plugin . '/includes/class-document-printer.php';
$badan_kegiatan = badan_method( $berkas_printer, 'render_kegiatan_print_page' );
$badan_peserta  = badan_method( $berkas_printer, 'render_print_page' );

cek_true( 'MF-06 dokumen kegiatan tidak membaca $pendaftaran->paket_muk_id', false === strpos( $badan_kegiatan, '$pendaftaran->paket_muk_id' ) );
cek_true( 'MF-07 dokumen kegiatan memakai $kegiatan->paket_muk_id', false !== strpos( $badan_kegiatan, '$kegiatan->paket_muk_id' ) );
cek_true( 'MF-08 dokumen peserta tetap memakai $pendaftaran->paket_muk_id', false !== strpos( $badan_peserta, '$pendaftaran->paket_muk_id' ) );

echo "\n== Dokumen tetap dapat dicetak ==\n";
$dok_peserta = array( 'mapa01_obs', 'mapa01_dit', 'mapa01_vp', 'mapa01_all', 'ak05', 'ak06',
    'ia01', 'ia02', 'ia03', 'ia04a', 'ia04b', 'ia05a', 'ia05b', 'ia05c' );
$gagal = array();
foreach ( $dok_peserta as $doc ) {
    $r = minta( "{$base}?page=lsp-pendaftaran&action=print&id={$p_berasesor}&doc={$doc}", $admin['cookie'] );
    if ( 200 !== $r['code'] || $r['ukuran'] < 3000 ) { $gagal[] = $doc . ' (' . $r['code'] . ')'; }
}
cek( 'MF-09..MF-13 dokumen peserta gagal', count( $gagal ), 0 );
if ( $gagal ) { echo '       → ' . implode( ', ', $gagal ) . "\n"; }

$r = minta( "{$base}?page=lsp-sertifikasi&action=print&id={$kegiatan_id}&doc=mapa02", $admin['cookie'] );
cek( 'MF-10b FR.MAPA.02 kegiatan', $r['code'], 200 );
cek_true( sprintf( 'MF-10c ukuran wajar (%.1f KB)', $r['ukuran'] / 1024 ), $r['ukuran'] > 20000 );
cek_true( 'MF-10d tanpa warning PHP di dokumen', false === strpos( $r['body'], 'Attempt to read property' ) );

echo "\n== Tidak ada perubahan di luar scope ==\n";
cek( 'MF-14a versi skema tidak berubah', LSP_Cert_Migrations::installed(), '1.1.0' );
cek( 'MF-14b jumlah kolom wp_lsp_kegiatan', count( $wpdb->get_results( "SHOW COLUMNS FROM {$tabel_k}" ) ), 18 );
cek_true( 'MF-15a asesor punya capability cetak', user_can( $asesor_id, 'lsp_cert_print_docs' ) );
cek_true( 'MF-15b asesor tidak punya capability kelola', ! user_can( $asesor_id, 'lsp_cert_manage_activities' ) );

// MF-16: pendaftaran publik tetap berjalan
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_lsp_cert_rl_daftar_%' OR option_name LIKE '_transient_timeout_lsp_cert_rl_daftar_%'" );
wp_set_current_user( 0 );
$nonce_publik = wp_create_nonce( 'lsp_cert_public_nonce' );
wp_set_current_user( 1 );

$r = minta( $ajax, '', array(
    'action' => 'lsp_cert_submit_pendaftaran', 'nonce' => $nonce_publik, 'kegiatan_id' => $kegiatan_id,
    'nama_lengkap' => 'Peserta Uji Publik', 'nik' => '3273000000000803',
    'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1996-02-02', 'jenis_kelamin' => 'Perempuan',
    'alamat' => 'Jalan Uji', 'kode_pos' => '40111', 'telepon' => '081200000003',
    'email' => 'mf112.publik@example.test', 'pendidikan' => 'S1',
) );
cek( 'MF-16 pendaftaran publik tetap berjalan', $r['code'], 200 );

// MF-17: gerbang APL.02 tetap aktif
$p_baru = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tabel_p} WHERE kegiatan_id = %d ORDER BY id DESC LIMIT 1", $kegiatan_id ) );
$kep = LSP_Cert_Integration::get_apl02_kelengkapan( $p_baru );
cek_true( 'MF-17a total elemen dari master terhitung', (int) $kep['total'] > 0 );
cek_true( 'MF-17b belum lengkap tanpa jawaban', false === $kep['lengkap'] );
$r = minta( "{$base}?page=lsp-pendaftaran&action=print&id={$p_baru}&doc=apl02", $admin['cookie'] );
cek( 'MF-17c cetak APL.02 masih ditahan', $r['code'], 409 );

// MF-18: lint seluruh berkas PHP
$lint_gagal = array();
foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $plugin, FilesystemIterator::SKIP_DOTS ) ) as $f ) {
    if ( ! $f->isFile() || 'php' !== $f->getExtension() ) { continue; }
    $path = $f->getPathname();
    if ( false !== strpos( $path, '/.git/' ) ) { continue; }
    exec( 'php -l ' . escapeshellarg( $path ) . ' 2>&1', $out, $rc );
    if ( 0 !== $rc ) { $lint_gagal[] = str_replace( $plugin . '/', '', $path ); }
    $out = array();
}
cek( 'MF-18 PHP lint gagal', count( $lint_gagal ), 0 );
if ( $lint_gagal ) { echo '       → ' . implode( ', ', $lint_gagal ) . "\n"; }

// --- bersihkan -------------------------------------------------------------
$ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$tabel_p} WHERE kegiatan_id = %d", $kegiatan_id ) );
foreach ( $ids as $pid ) {
    $wpdb->query( $wpdb->prepare( "DELETE FROM {$tabel_j} WHERE pendaftaran_id = %d", (int) $pid ) );
}
$wpdb->query( $wpdb->prepare( "DELETE FROM {$tabel_p} WHERE kegiatan_id = %d", $kegiatan_id ) );
$wpdb->query( $wpdb->prepare( "DELETE FROM {$tabel_k} WHERE id = %d", $kegiatan_id ) );
require_once ABSPATH . 'wp-admin/includes/user.php';
wp_delete_user( $asesor_id );
cek( 'Z1 fixture dibersihkan', (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tabel_p} WHERE kegiatan_id = %d", $kegiatan_id ) ), 0 );

echo "\n" . ( $fail === 0 ? "SEMUA LULUS\n" : "{$fail} GAGAL\n" );
exit( $fail === 0 ? 0 : 1 );
