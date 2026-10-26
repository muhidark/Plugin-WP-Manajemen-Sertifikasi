<?php
/**
 * PH-09 — Audit otorisasi endpoint dokumen & instrumen.
 *
 * Matriks uji: admin, asesor A, asesor B, subscriber, anonim — pada endpoint cetak
 * (dokumen peserta & dokumen kegiatan) dan endpoint AJAX instrumen.
 *
 * Jalankan: php tests/ph09-authz-audit.php
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
    printf( "%s %-62s harap=%s dapat=%s\n", $ok ? 'PASS' : 'FAIL', $label, var_export( $expected, true ), var_export( $actual, true ) );
}
function cek_true( $label, $cond ) {
    global $fail;
    if ( ! $cond ) { $fail++; }
    printf( "%s %s\n", $cond ? 'PASS' : 'FAIL', $label );
}

/**
 * Buat cookie login + nonce AJAX untuk satu user.
 *
 * @param int $uid
 * @return array{cookie:string,nonce:string}
 */
function identitas( $uid ) {
    $uid  = (int) $uid;
    $user = get_userdata( $uid );
    if ( ! $user ) {
        return array( 'cookie' => '', 'nonce' => '' );
    }

    $exp   = time() + 3600;
    $token = WP_Session_Tokens::get_instance( $uid )->create( $exp );
    $hash  = $user->user_pass;
    $frag  = ( str_starts_with( $hash, '$P$' ) || str_starts_with( $hash, '$2y$' ) ) ? substr( $hash, 8, 4 ) : substr( $hash, -4 );

    $cookie = array();
    foreach ( array( 'auth' => AUTH_COOKIE, 'secure_auth' => SECURE_AUTH_COOKIE, 'logged_in' => LOGGED_IN_COOKIE ) as $scheme => $nama ) {
        $key  = wp_hash( $user->user_login . '|' . $frag . '|' . $exp . '|' . $token, $scheme );
        $hmac = hash_hmac( 'sha256', $user->user_login . '|' . $exp . '|' . $token, $key );
        $cookie[] = $nama . '=' . $user->user_login . '|' . $exp . '|' . $token . '|' . $hmac;
    }

    // Nonce dihitung dengan token sesi yang sama seperti cookie di atas.
    $login_value = $user->user_login . '|' . $exp . '|' . $token . '|' . hash_hmac(
        'sha256',
        $user->user_login . '|' . $exp . '|' . $token,
        wp_hash( $user->user_login . '|' . $frag . '|' . $exp . '|' . $token, 'logged_in' )
    );
    $simpan_cookie = $_COOKIE;
    $_COOKIE[ LOGGED_IN_COOKIE ] = $login_value;
    wp_set_current_user( $uid );
    $nonce = wp_create_nonce( 'lsp_cert_nonce' );
    $_COOKIE = $simpan_cookie;
    wp_set_current_user( 0 );

    return array( 'cookie' => implode( '; ', $cookie ), 'nonce' => $nonce );
}

/**
 * Permintaan HTTP.
 */
function minta( $url, $cookie = '', $post = null ) {
    $ch = curl_init( $url );
    $opt = array( CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 25 );
    if ( $cookie ) { $opt[ CURLOPT_COOKIE ] = $cookie; }
    if ( null !== $post ) {
        $opt[ CURLOPT_POST ]       = true;
        $opt[ CURLOPT_POSTFIELDS ] = http_build_query( $post );
    }
    curl_setopt_array( $ch, $opt );
    $body = curl_exec( $ch );
    $code = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );
    curl_close( $ch );
    return array( 'code' => $code, 'body' => (string) $body );
}

// --- Identitas uji ---------------------------------------------------------
$asesor_a = 39; // ditugaskan di K1
$asesor_b = 38; // ditugaskan di K2
$sub_id   = wp_insert_user( array(
    'user_login' => 'ph09_sub_' . wp_rand( 1000, 9999 ),
    'user_pass'  => wp_generate_password( 16 ),
    'user_email' => 'ph09_' . wp_rand( 1000, 9999 ) . '@example.test',
    'role'       => 'subscriber',
) );

$id_admin = identitas( 1 );
$id_a     = identitas( $asesor_a );
$id_b     = identitas( $asesor_b );
$id_sub   = is_wp_error( $sub_id ) ? array( 'cookie' => '', 'nonce' => '' ) : identitas( $sub_id );

// --- Fixture ---------------------------------------------------------------
$skema = get_posts( array( 'post_type' => 'skema', 'posts_per_page' => 1, 'post_status' => 'any' ) );
if ( ! $skema ) { echo "SKIP: tidak ada Master Skema\n"; exit( 0 ); }
$skema_id = (int) $skema[0]->ID;

$tabel_k = $wpdb->prefix . 'lsp_kegiatan';
$tabel_p = $wpdb->prefix . 'lsp_pendaftaran';

$wpdb->insert( $tabel_k, array(
    'skema_id' => $skema_id, 'tanggal_pelaksanaan' => date( 'Y-m-d', strtotime( '+7 days' ) ),
    'tuk_id' => 0, 'kuota' => 5, 'status' => 'dibuka', 'asesor_ids' => wp_json_encode( array( $asesor_a ) ),
) );
$k1 = (int) $wpdb->insert_id;

$wpdb->insert( $tabel_k, array(
    'skema_id' => $skema_id, 'tanggal_pelaksanaan' => date( 'Y-m-d', strtotime( '+7 days' ) ),
    'tuk_id' => 0, 'kuota' => 5, 'status' => 'dibuka', 'asesor_ids' => wp_json_encode( array( $asesor_b ) ),
) );
$k2 = (int) $wpdb->insert_id;

$buat_peserta = function ( $kegiatan_id, $nik, $email, $asesor_id ) use ( $wpdb, $tabel_p ) {
    $wpdb->insert( $tabel_p, array(
        'kegiatan_id' => $kegiatan_id, 'nama_lengkap' => 'Peserta ' . $nik, 'nik' => $nik,
        'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1995-05-05', 'jenis_kelamin' => 'Laki-laki',
        'alamat' => 'Jalan Uji', 'kode_pos' => '40111', 'telepon' => '081234567890',
        'email' => $email, 'pendidikan' => 'S1', 'asesor_id' => $asesor_id,
    ) );
    return (int) $wpdb->insert_id;
};

$p1 = $buat_peserta( $k1, '3200000000000101', 'p1_' . wp_rand( 1000, 9999 ) . '@example.test', $asesor_a );
$p2 = $buat_peserta( $k2, '3200000000000102', 'p2_' . wp_rand( 1000, 9999 ) . '@example.test', $asesor_b );
$p3 = $buat_peserta( $k1, '3200000000000103', 'p3_' . wp_rand( 1000, 9999 ) . '@example.test', null ); // kegiatan A, tanpa asesor
$p4 = $buat_peserta( $k2, '3200000000000104', 'p4_' . wp_rand( 1000, 9999 ) . '@example.test', null ); // kegiatan B, tanpa asesor

$base = 'http://localhost/wordpress/wp-admin/admin.php';

echo "== A. Dokumen peserta ==\n";
cek( 'A1 admin cetak peserta K1', minta( "$base?page=lsp-pendaftaran&action=print&id={$p1}&doc=apl01", $id_admin['cookie'] )['code'], 200 );
cek( 'A2 asesor A cetak peserta miliknya', minta( "$base?page=lsp-pendaftaran&action=print&id={$p1}&doc=apl01", $id_a['cookie'] )['code'], 200 );
cek( 'A3 asesor A cetak peserta asesor B (IDOR)', minta( "$base?page=lsp-pendaftaran&action=print&id={$p2}&doc=apl01", $id_a['cookie'] )['code'], 403 );
cek( 'A4 asesor A cetak peserta kegiatan A tanpa asesor', minta( "$base?page=lsp-pendaftaran&action=print&id={$p3}&doc=apl01", $id_a['cookie'] )['code'], 200 );
cek( 'A5 asesor A cetak peserta kegiatan B tanpa asesor (fail-closed)', minta( "$base?page=lsp-pendaftaran&action=print&id={$p4}&doc=apl01", $id_a['cookie'] )['code'], 403 );
cek( 'A6 subscriber cetak dokumen', minta( "$base?page=lsp-pendaftaran&action=print&id={$p1}&doc=apl01", $id_sub['cookie'] )['code'], 403 );
cek( 'A7 anonim cetak dokumen', minta( "$base?page=lsp-pendaftaran&action=print&id={$p1}&doc=apl01" )['code'], 302 );

echo "\n== B. Dokumen kegiatan (FR.MAPA.02) ==\n";
cek( 'B1 admin cetak kegiatan K1', minta( "$base?page=lsp-sertifikasi&action=print&id={$k1}&doc=mapa02", $id_admin['cookie'] )['code'], 200 );
cek( 'B2 asesor A cetak kegiatan tugasnya', minta( "$base?page=lsp-sertifikasi&action=print&id={$k1}&doc=mapa02", $id_a['cookie'] )['code'], 200 );
cek( 'B3 asesor A cetak kegiatan asesor B (IDOR)', minta( "$base?page=lsp-sertifikasi&action=print&id={$k2}&doc=mapa02", $id_a['cookie'] )['code'], 403 );
cek( 'B4 subscriber cetak kegiatan', minta( "$base?page=lsp-sertifikasi&action=print&id={$k1}&doc=mapa02", $id_sub['cookie'] )['code'], 403 );

echo "\n== C. AJAX instrumen (paket MUK) ==\n";
$ajax = 'http://localhost/wordpress/wp-admin/admin-ajax.php';

$r = minta( $ajax, $id_admin['cookie'], array( 'action' => 'lsp_cert_get_paket_by_skema', 'nonce' => $id_admin['nonce'], 'skema_id' => $skema_id ) );
cek( 'C1 admin boleh membaca daftar paket', $r['code'], 200 );
cek_true( 'C2 respons sukses', false !== strpos( $r['body'], '"success":true' ) );

$r = minta( $ajax, $id_a['cookie'], array( 'action' => 'lsp_cert_get_paket_by_skema', 'nonce' => $id_a['nonce'], 'skema_id' => $skema_id ) );
cek( 'C3 asesor ditolak membaca daftar paket', $r['code'], 403 );

$r = minta( $ajax, $id_sub['cookie'], array( 'action' => 'lsp_cert_get_paket_by_skema', 'nonce' => $id_sub['nonce'], 'skema_id' => $skema_id ) );
cek( 'C4 subscriber ditolak membaca daftar paket', $r['code'], 403 );

$r = minta( $ajax, $id_a['cookie'], array( 'action' => 'lsp_cert_save_kegiatan', 'nonce' => 'palsu', 'id' => $k1 ) );
cek_true( 'C5 nonce palsu ditolak pada handler tulis', 200 !== $r['code'] || false !== strpos( $r['body'], '-1' ) );

echo "\n== D. Tabel otorisasi handler AJAX ==\n";
$handlers = array(
    'admin/class-kegiatan.php'            => array( 'ajax_save_kegiatan', 'ajax_delete_kegiatan', 'ajax_update_status' ),
    'admin/class-pendaftaran.php'         => array( 'ajax_save_verifikasi', 'ajax_update_kelengkapan', 'ajax_update_rekomendasi' ),
    'includes/class-paket-muk.php'        => array( 'ajax_save_paket', 'ajax_clone_paket', 'ajax_delete_paket', 'ajax_set_default_paket', 'ajax_get_paket_by_skema' ),
);
$kurang_nonce = array(); $kurang_cap = array();
foreach ( $handlers as $file => $fungsi ) {
    $txt = file_get_contents( dirname( __DIR__ ) . '/' . $file );
    foreach ( $fungsi as $fn ) {
        $pos = strpos( $txt, 'function ' . $fn );
        if ( false === $pos ) { continue; }
        $badan = substr( $txt, $pos, 2500 );
        if ( ! preg_match( '/check_ajax_referer|wp_verify_nonce|check_admin_referer/', $badan ) ) { $kurang_nonce[] = $fn; }
        if ( ! preg_match( '/current_user_can/', $badan ) ) { $kurang_cap[] = $fn; }
    }
}
cek( 'D1 handler tanpa pemeriksaan nonce', count( $kurang_nonce ), 0 );
if ( $kurang_nonce ) { echo '   - ' . implode( ', ', $kurang_nonce ) . "\n"; }
cek( 'D2 handler tanpa pemeriksaan capability', count( $kurang_cap ), 0 );
if ( $kurang_cap ) { echo '   - ' . implode( ', ', $kurang_cap ) . "\n"; }

// --- Bersihkan -------------------------------------------------------------
$daftar_p = implode( ',', array_map( 'intval', array( $p1, $p2, $p3, $p4 ) ) );
$wpdb->query( "DELETE FROM {$wpdb->prefix}lsp_apl02_jawaban WHERE pendaftaran_id IN ($daftar_p)" );
$wpdb->query( "DELETE FROM {$tabel_p} WHERE id IN ($daftar_p)" );
$wpdb->query( "DELETE FROM {$tabel_k} WHERE id IN (" . (int) $k1 . ',' . (int) $k2 . ')' );
if ( ! is_wp_error( $sub_id ) ) {
    require_once ABSPATH . 'wp-admin/includes/user.php';
    wp_delete_user( $sub_id );
}
cek( 'E1 fixture peserta terhapus', (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tabel_p} WHERE id IN ($daftar_p)" ), 0 );
cek( 'E2 fixture kegiatan terhapus', (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tabel_k} WHERE id IN (" . (int) $k1 . ',' . (int) $k2 . ')' ), 0 );

echo "\n" . ( $fail === 0 ? "SEMUA LULUS\n" : "{$fail} GAGAL\n" );
exit( $fail === 0 ? 0 : 1 );
