<?php
/**
 * STG-01 — Deployment & alur penerimaan (acceptance) di instalasi lokal.
 *
 * Menjalankan 17 langkah roadmap secara berurutan dan mencatat hasilnya.
 * Data uji yang dibuat (kegiatan + peserta) SENGAJA dipertahankan agar bisa
 * dipakai pada STG-02 (uji cetak manual di browser).
 *
 * Jalankan: php tests/stg01-browser-acceptance.php
 */

$_SERVER['HTTP_HOST']      = 'localhost';
$_SERVER['REQUEST_URI']    = '/wordpress/';
$_SERVER['SERVER_NAME']    = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';

define( 'WP_ADMIN', true );
define( 'LSP_CERT_TESTING', true );

require dirname( __DIR__, 4 ) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

global $wpdb;
$fail = 0;
$langkah = 0;

function langkah( $judul ) {
    global $langkah;
    $langkah++;
    printf( "\n[%02d] %s\n", $langkah, $judul );
}
function cek( $label, $actual, $expected ) {
    global $fail;
    $ok = ( $actual === $expected );
    if ( ! $ok ) { $fail++; }
    printf( "  %s %-58s harap=%s dapat=%s\n", $ok ? 'PASS' : 'FAIL', $label, var_export( $expected, true ), var_export( $actual, true ) );
}
function cek_true( $label, $cond ) {
    global $fail;
    if ( ! $cond ) { $fail++; }
    printf( "  %s %s\n", $cond ? 'PASS' : 'FAIL', $label );
}

function identitas( $uid ) {
    $uid  = (int) $uid;
    $user = get_userdata( $uid );
    if ( ! $user ) { return array( 'cookie' => '', 'nonce' => '' ); }

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

    $login_value = $user->user_login . '|' . $exp . '|' . $token . '|' . hash_hmac(
        'sha256',
        $user->user_login . '|' . $exp . '|' . $token,
        wp_hash( $user->user_login . '|' . $frag . '|' . $exp . '|' . $token, 'logged_in' )
    );
    $simpan = $_COOKIE;
    $_COOKIE[ LOGGED_IN_COOKIE ] = $login_value;
    wp_set_current_user( $uid );
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

$admin    = identitas( 1 );
$base     = 'http://localhost/wordpress/wp-admin/admin.php';
$ajax     = 'http://localhost/wordpress/wp-admin/admin-ajax.php';
$tabel_k  = $wpdb->prefix . 'lsp_kegiatan';
$tabel_p  = $wpdb->prefix . 'lsp_pendaftaran';

// ===========================================================================
langkah( 'Aktivasi Core' );
deactivate_plugins( array(
    'lsp-certification-management/lsp-certification-management.php',
    'lsp-core-management/lsp-core-management.php',
) );
cek_true( 'kedua plugin nonaktif', ! is_plugin_active( 'lsp-core-management/lsp-core-management.php' ) && ! is_plugin_active( 'lsp-certification-management/lsp-certification-management.php' ) );

$r = activate_plugin( 'lsp-core-management/lsp-core-management.php' );
cek_true( 'aktivasi Core tanpa error', ! is_wp_error( $r ) );
cek_true( 'Core aktif', is_plugin_active( 'lsp-core-management/lsp-core-management.php' ) );
cek_true( 'kelas LSP_Core_Management tersedia', class_exists( 'LSP_Core_Management' ) );

// ===========================================================================
langkah( 'Aktivasi Certification' );
$r = activate_plugin( 'lsp-certification-management/lsp-certification-management.php' );
cek_true( 'aktivasi Certification tanpa error', ! is_wp_error( $r ) );
cek_true( 'Certification aktif', is_plugin_active( 'lsp-certification-management/lsp-certification-management.php' ) );

$tabel_ada = 0;
foreach ( array( 'lsp_kegiatan', 'lsp_pendaftaran', 'lsp_apl02_jawaban', 'lsp_paket_muk' ) as $t ) {
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->prefix . $t ) ) === $wpdb->prefix . $t ) { $tabel_ada++; }
}
cek( 'empat tabel tersedia', $tabel_ada, 4 );
cek( 'versi skema tercatat', LSP_Cert_Migrations::installed(), LSP_Cert_Migrations::target() );

// ===========================================================================
langkah( 'Master Skema' );
$skema_list = get_posts( array( 'post_type' => 'skema', 'posts_per_page' => 1, 'post_status' => 'any' ) );
cek_true( 'ada Master Skema', ! empty( $skema_list ) );
$skema    = $skema_list[0];
$skema_id = (int) $skema->ID;

$skkni = LSP_Core_Management::get_skema_skkni_data( $skema_id );
$n_unit = count( $skkni );
$n_elemen = 0; $n_kuk = 0;
foreach ( $skkni as $u ) {
    $n_elemen += count( $u['elemen'] ?? array() );
    foreach ( ( $u['elemen'] ?? array() ) as $e ) { $n_kuk += count( $e['kuk'] ?? array() ); }
}
cek_true( "skema #{$skema_id} punya Elemen/KUK ({$n_unit} unit / {$n_elemen} elemen / {$n_kuk} KUK)", $n_unit > 0 && $n_elemen > 0 );
cek( 'halaman publik skema', minta( get_permalink( $skema_id ) )['code'], 200 );
cek( 'suntingan skema di admin', minta( "http://localhost/wordpress/wp-admin/post.php?post={$skema_id}&action=edit", $admin['cookie'] )['code'], 200 );

// ===========================================================================
langkah( 'Master TUK' );
$tuk = get_posts( array( 'post_type' => 'tuk', 'posts_per_page' => 1, 'post_status' => 'any' ) );
if ( $tuk ) {
    $tuk_id = (int) $tuk[0]->ID;
    printf( "  (memakai TUK #%d: %s)\n", $tuk_id, $tuk[0]->post_title );
} else {
    $tuk_id = wp_insert_post( array( 'post_type' => 'tuk', 'post_title' => 'TUK Uji STG-01', 'post_status' => 'publish' ) );
    printf( "  (membuat TUK #%d)\n", $tuk_id );
}
cek_true( 'TUK tersedia', $tuk_id > 0 && 'tuk' === get_post_type( $tuk_id ) );

// ===========================================================================
langkah( 'Master Asesor' );
$asesor_slug = 'stg01_asesor_' . wp_rand( 1000, 9999 );
$asesor_id   = wp_insert_user( array(
    'user_login' => $asesor_slug,
    'user_pass'  => wp_generate_password( 16 ),
    'user_email' => $asesor_slug . '@example.test',
    'first_name' => 'Asesor',
    'last_name'  => 'STG-01',
    'role'       => 'asesor',
) );
cek_true( 'akun asesor dibuat', ! is_wp_error( $asesor_id ) );
update_user_meta( $asesor_id, 'nomor_met', 'MET.STG.000001.2026' );
update_user_meta( $asesor_id, 'tanggal_terbit_met', '2026-01-15' );
$u = get_userdata( $asesor_id );
cek_true( 'role asesor melekat', in_array( 'asesor', (array) $u->roles, true ) );
cek_true( 'capability cetak dokumen dimiliki', user_can( $asesor_id, 'lsp_cert_print_docs' ) );
cek_true( 'capability kelola kegiatan TIDAK dimiliki', ! user_can( $asesor_id, 'lsp_cert_manage_activities' ) );

// ===========================================================================
langkah( 'Kelompok Pekerjaan' );
$unit_kode = array();
foreach ( $skkni as $u2 ) { $unit_kode[] = $u2['kode_unit'] ?? ''; }
$unit_kode = array_values( array_filter( $unit_kode ) );

$kelompok = array();
foreach ( array_slice( $unit_kode, 0, 2 ) as $i => $kode ) {
    $kelompok[] = array( 'nama' => 'Kelompok Uji ' . ( $i + 1 ), 'units' => array( $kode ) );
}
update_option( 'lsp_cert_kelompok_pekerjaan_skema_' . $skema_id, $kelompok );
cek_true( 'kelompok pekerjaan tersimpan', count( get_option( 'lsp_cert_kelompok_pekerjaan_skema_' . $skema_id ) ) === count( $kelompok ) );
cek( 'halaman Kelompok Pekerjaan', minta( "{$base}?page=lsp-kelompok-pekerjaan", $admin['cookie'] )['code'], 200 );

// ===========================================================================
langkah( 'Instrumen' );
foreach ( array( 'lsp-ia02', 'lsp-ia03', 'lsp-ia04', 'lsp-ia05' ) as $slug ) {
    $r = minta( "{$base}?page={$slug}", $admin['cookie'] );
    cek( "halaman {$slug}", $r['code'], 200 );
}
$r = minta( "{$base}?page=lsp-ia02&skema_id={$skema_id}", $admin['cookie'] );
cek_true( 'editor instrumen menampilkan daftar unit', false !== strpos( $r['body'], 'Unit' ) );

// ===========================================================================
langkah( 'Pembuatan Kegiatan' );
$r = minta( $ajax, $admin['cookie'], array(
    'action'              => 'lsp_cert_save_kegiatan',
    'nonce'               => $admin['nonce'],
    'skema_id'            => $skema_id,
    'tanggal_pelaksanaan' => date( 'Y-m-d', strtotime( '+14 days' ) ),
    'tuk_id'              => $tuk_id,
    'lokasi_uji'          => 'TUK Uji STG-01',
    'alamat_lokasi_uji'   => 'Jalan Uji No. 1, Bandung',
    'kuota'               => 5,
    'status'              => 'dibuka',
    'asesor_ids'          => array( $asesor_id ),
    'penyusun_id'         => $asesor_id,
    'penyusun_nama'       => 'Asesor STG-01',
    'penyusun_met'        => 'MET.STG.000001.2026',
    'validator_id'        => $asesor_id,
    'validator_nama'      => 'Asesor STG-01',
    'validator_met'       => 'MET.STG.000001.2026',
) );
cek( 'AJAX simpan kegiatan', $r['code'], 200 );
cek_true( 'respons sukses', false !== strpos( $r['body'], '"success":true' ) );

$kegiatan_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tabel_k} WHERE skema_id = %d ORDER BY id DESC LIMIT 1", $skema_id ) );
cek_true( "kegiatan tersimpan (#{$kegiatan_id})", $kegiatan_id > 0 );
cek( 'daftar kegiatan di admin', minta( "{$base}?page=lsp-sertifikasi", $admin['cookie'] )['code'], 200 );

// ===========================================================================
langkah( 'Pendaftaran Asesi' );
$nonce_publik = wp_create_nonce( 'lsp_cert_public_nonce' );
$nik   = '3273' . wp_rand( 100000000000, 999999999999 );
$email = 'peserta.stg01.' . wp_rand( 1000, 9999 ) . '@example.test';

// FR.APL.02 wajib lengkap sejak PH-11: bangun payload dari master, semua K.
$apl02_payload = array();
foreach ( LSP_Cert_Integration::get_unit_kompetensi( $skema_id ) as $unit_master ) {
    $kode_unit = trim( (string) ( $unit_master['kode'] ?? '' ) );
    if ( '' === $kode_unit ) { continue; }
    foreach ( (array) ( $unit_master['elemen'] ?? array() ) as $idx_master => $el_master ) {
        $apl02_payload[] = array(
            'kode'         => $kode_unit,
            'elemen_nomor' => isset( $el_master['nomor'] ) ? (int) $el_master['nomor'] : ( $idx_master + 1 ),
            'jawaban'      => 'K',
        );
    }
}

$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_lsp_cert_rl_daftar_%' OR option_name LIKE '_transient_timeout_lsp_cert_rl_daftar_%'" );

$r = minta( $ajax, '', array(
    'action'        => 'lsp_cert_submit_pendaftaran',
    'nonce'         => $nonce_publik,
    'kegiatan_id'   => $kegiatan_id,
    'nama_lengkap'  => 'Peserta Uji STG-01',
    'nik'           => $nik,
    'tempat_lahir'  => 'Bandung',
    'tanggal_lahir' => '1996-04-12',
    'jenis_kelamin' => 'Laki-laki',
    'alamat'        => 'Jalan Uji No. 2',
    'kode_pos'      => '40111',
    'telepon'       => '081200000001',
    'email'         => $email,
    'pendidikan'    => 'S1',
    'apl02'         => $apl02_payload,
) );
cek( 'AJAX pendaftaran publik', $r['code'], 200 );
cek_true( 'APL.02 lengkap tersimpan', false !== strpos( $r['body'], '"apl02_status":"lengkap"' ) );
cek_true( 'respons sukses', false !== strpos( $r['body'], '"success":true' ) );

$pendaftaran_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tabel_p} WHERE kegiatan_id = %d ORDER BY id DESC LIMIT 1", $kegiatan_id ) );
cek_true( "pendaftaran tersimpan (#{$pendaftaran_id})", $pendaftaran_id > 0 );
$wpdb->update( $tabel_p, array( 'asesor_id' => $asesor_id ), array( 'id' => $pendaftaran_id ) );
cek( 'daftar peserta di admin', minta( "{$base}?page=lsp-pendaftaran", $admin['cookie'] )['code'], 200 );

// ===========================================================================
$dok_peserta = array( 'apl01', 'apl02', 'mapa01_obs', 'mapa01_dit', 'mapa01_vp', 'mapa01_all',
    'ak01', 'ak02', 'ak03', 'ak04', 'ak05', 'ak06', 'ak07',
    'ia01', 'ia02', 'ia03', 'ia04a', 'ia04b', 'ia05a', 'ia05b', 'ia05c', 'all', 'bundle_all' );

$grup = array(
    'APL.01'      => array( 'apl01' ),
    'APL.02'      => array( 'apl02' ),
    'MAPA'        => array( 'mapa01_obs', 'mapa01_dit', 'mapa01_vp', 'mapa01_all', 'mapa02' ),
    'AK'          => array( 'ak01', 'ak02', 'ak03', 'ak04', 'ak05', 'ak06', 'ak07' ),
    'IA'          => array( 'ia01', 'ia02', 'ia03', 'ia04a', 'ia04b', 'ia05a', 'ia05b', 'ia05c' ),
    'Gabungan'    => array( 'all', 'bundle_all' ),
);
$nomor = array( 'APL.01' => 10, 'APL.02' => 11, 'MAPA' => 12, 'AK' => 13, 'IA' => 14, 'Gabungan' => 14 );
$ukuran = array();

foreach ( $grup as $nama_grup => $daftar ) {
    langkah( "Dokumen {$nama_grup}" );
    foreach ( $daftar as $doc ) {
        if ( 'mapa02' === $doc ) {
            $r = minta( "{$base}?page=lsp-sertifikasi&action=print&id={$kegiatan_id}&doc={$doc}", $admin['cookie'] );
        } else {
            $r = minta( "{$base}?page=lsp-pendaftaran&action=print&id={$pendaftaran_id}&doc={$doc}", $admin['cookie'] );
        }
        $ukuran[ $doc ] = $r['ukuran'];
        $ditolak = ( false !== strpos( $r['body'], 'Pencetakan Ditolak' ) || false !== strpos( $r['body'], 'Akses Ditolak' ) );
        cek_true( sprintf( '%-11s %5.1f KB', $doc, $r['ukuran'] / 1024 ), 200 === $r['code'] && $r['ukuran'] > 3000 && ! $ditolak );
    }
}

// ===========================================================================
langkah( 'Hak akses Admin' );
foreach ( array( 'lsp-sertifikasi', 'lsp-pendaftaran', 'lsp-cetak-dokumen', 'lsp-ia02', 'lsp-ia05', 'lsp-kelompok-pekerjaan' ) as $slug ) {
    cek( "admin buka {$slug}", minta( "{$base}?page={$slug}", $admin['cookie'] )['code'], 200 );
}

// ===========================================================================
langkah( 'Hak akses Asesor' );
$id_asesor = identitas( $asesor_id );
cek( 'asesor cetak peserta tugasnya', minta( "{$base}?page=lsp-pendaftaran&action=print&id={$pendaftaran_id}&doc=apl01", $id_asesor['cookie'] )['code'], 200 );
cek( 'asesor cetak dokumen kegiatan tugasnya', minta( "{$base}?page=lsp-sertifikasi&action=print&id={$kegiatan_id}&doc=mapa02", $id_asesor['cookie'] )['code'], 200 );
cek( 'asesor tidak boleh kelola kegiatan', minta( "{$base}?page=lsp-sertifikasi", $id_asesor['cookie'] )['code'], 403 );
cek( 'asesor tidak boleh kelola peserta', minta( "{$base}?page=lsp-pendaftaran", $id_asesor['cookie'] )['code'], 403 );
cek( 'asesor tidak boleh kelola instrumen', minta( "{$base}?page=lsp-ia02", $id_asesor['cookie'] )['code'], 403 );

// ===========================================================================
langkah( 'Hak akses pengguna tidak berwenang' );
$sub_slug = 'stg01_sub_' . wp_rand( 1000, 9999 );
$sub_id   = wp_insert_user( array(
    'user_login' => $sub_slug,
    'user_pass'  => wp_generate_password( 16 ),
    'user_email' => $sub_slug . '@example.test',
    'role'       => 'subscriber',
) );
$id_sub = identitas( $sub_id );

cek( 'subscriber cetak dokumen', minta( "{$base}?page=lsp-pendaftaran&action=print&id={$pendaftaran_id}&doc=apl01", $id_sub['cookie'] )['code'], 403 );
cek( 'subscriber buka kelola kegiatan', minta( "{$base}?page=lsp-sertifikasi", $id_sub['cookie'] )['code'], 403 );
cek( 'anonim cetak dokumen', minta( "{$base}?page=lsp-pendaftaran&action=print&id={$pendaftaran_id}&doc=apl01" )['code'], 302 );
cek( 'anonim ajax pendaftaran tanpa nonce', minta( $ajax, '', array( 'action' => 'lsp_cert_submit_pendaftaran', 'kegiatan_id' => $kegiatan_id ) )['code'] >= 400 ? 403 : 200, 403 );

// asesor kedua: tidak ditugaskan
$asesor2_slug = 'stg01_asesor2_' . wp_rand( 1000, 9999 );
$asesor2_id   = wp_insert_user( array(
    'user_login' => $asesor2_slug,
    'user_pass'  => wp_generate_password( 16 ),
    'user_email' => $asesor2_slug . '@example.test',
    'role'       => 'asesor',
) );
$id_a2 = identitas( $asesor2_id );
cek( 'asesor lain cetak peserta (IDOR)', minta( "{$base}?page=lsp-pendaftaran&action=print&id={$pendaftaran_id}&doc=apl01", $id_a2['cookie'] )['code'], 403 );
cek( 'asesor lain cetak kegiatan (IDOR)', minta( "{$base}?page=lsp-sertifikasi&action=print&id={$kegiatan_id}&doc=mapa02", $id_a2['cookie'] )['code'], 403 );

// bersihkan akun uji (data kegiatan & peserta dipertahankan untuk STG-02)
require_once ABSPATH . 'wp-admin/includes/user.php';
wp_delete_user( $sub_id );
wp_delete_user( $asesor2_id );

echo "\n== Data yang dipertahankan untuk STG-02 ==\n";
printf( "  skema #%d (%s)\n", $skema_id, $skema->post_title );
printf( "  kegiatan #%d | peserta #%d | asesor #%d (%s)\n", $kegiatan_id, $pendaftaran_id, $asesor_id, $asesor_slug );
printf( "  cetak: %s?page=lsp-pendaftaran&action=print&id=%d&doc=apl01\n", $base, $pendaftaran_id );

echo "\n" . ( $fail === 0 ? "SEMUA LULUS\n" : "{$fail} GAGAL\n" );
exit( $fail === 0 ? 0 : 1 );
