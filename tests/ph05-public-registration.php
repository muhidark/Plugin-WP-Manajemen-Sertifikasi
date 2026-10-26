<?php
/**
 * PH-05 — Test gate pendaftaran publik (HTTP nyata via admin-ajax.php).
 *
 * Menguji aturan yang harus ditegakkan server-side: status kegiatan, tanggal,
 * keabsahan skema, kuota, duplikasi identitas, format data, dan rate limit.
 *
 * Jalankan: php tests/ph05-public-registration.php
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
    printf( "%s %-56s harap=%s dapat=%s\n", $ok ? 'PASS' : 'FAIL', $label, var_export( $expected, true ), var_export( $actual, true ) );
}
function cek_true( $label, $cond ) {
    global $fail;
    if ( ! $cond ) { $fail++; }
    printf( "%s %s\n", $cond ? 'PASS' : 'FAIL', $label );
}

$aj_url  = 'http://localhost/wordpress/wp-admin/admin-ajax.php';
$nonce   = wp_create_nonce( 'lsp_cert_public_nonce' );
$rl_key  = 'lsp_cert_rl_daftar_' . md5( '127.0.0.1' );

// Skema valid harus ada; kalau tidak ada, uji tidak bisa bermakna.
$skema = get_posts( array( 'post_type' => 'skema', 'posts_per_page' => 1, 'post_status' => 'any' ) );
if ( ! $skema ) {
    echo "SKIP: tidak ada Master Skema untuk fixture kegiatan\n";
    exit( 0 );
}
$skema_id = (int) $skema[0]->ID;

$tabel_k = $wpdb->prefix . 'lsp_kegiatan';
$tabel_p = $wpdb->prefix . 'lsp_pendaftaran';

// --- Fixture kegiatan ------------------------------------------------------
$hari_ini = current_time( 'Y-m-d' );
$fixtures = array(
    'terbuka'      => array( 'status' => 'dibuka',  'tanggal' => date( 'Y-m-d', strtotime( '+7 days' ) ), 'skema' => $skema_id, 'kuota' => 10 ),
    'ditutup'      => array( 'status' => 'ditutup', 'tanggal' => date( 'Y-m-d', strtotime( '+7 days' ) ), 'skema' => $skema_id, 'kuota' => 10 ),
    'kedaluwarsa'  => array( 'status' => 'dibuka',  'tanggal' => date( 'Y-m-d', strtotime( '-3 days' ) ), 'skema' => $skema_id, 'kuota' => 10 ),
    'skema_rusak'  => array( 'status' => 'dibuka',  'tanggal' => date( 'Y-m-d', strtotime( '+7 days' ) ), 'skema' => 99999999, 'kuota' => 10 ),
    'penuh'        => array( 'status' => 'dibuka',  'tanggal' => date( 'Y-m-d', strtotime( '+7 days' ) ), 'skema' => $skema_id, 'kuota' => 1 ),
);

$ids = array();
foreach ( $fixtures as $nama => $f ) {
    $wpdb->insert( $tabel_k, array(
        'skema_id'            => $f['skema'],
        'tanggal_pelaksanaan' => $f['tanggal'],
        'tuk_id'              => 0,
        'kuota'               => $f['kuota'],
        'status'              => $f['status'],
    ) );
    $ids[ $nama ] = (int) $wpdb->insert_id;
}

// Isi satu pendaftaran agar kegiatan "penuh" benar-benar penuh.
$wpdb->insert( $tabel_p, array(
    'kegiatan_id'   => $ids['penuh'],
    'nama_lengkap'  => 'Peserta Fixture Penuh',
    'nik'           => '3200000000000001',
    'tempat_lahir'  => 'Bandung',
    'tanggal_lahir' => '1990-01-01',
    'jenis_kelamin' => 'Laki-laki',
    'alamat'        => 'Jalan Uji',
    'kode_pos'      => '40111',
    'telepon'       => '081234567890',
    'email'         => 'fixture_penuh@example.test',
    'pendidikan'    => 'S1',
) );
$pendaftaran_fixture = (int) $wpdb->insert_id;

/**
 * Kirim pendaftaran ke admin-ajax.php.
 *
 * @param array $override Data yang menimpa default.
 * @return array{code:int,body:string}
 */
function bersihkan_rate_limit() {
    global $wpdb;
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_lsp_cert_rl_daftar_%' OR option_name LIKE '_transient_timeout_lsp_cert_rl_daftar_%'" );
    wp_cache_flush();
}

function kirim( $override = array() ) {
    global $aj_url, $nonce, $rl_key, $ids;

    bersihkan_rate_limit(); // isolasi rate limit antar kasus

    $default = array(
        'action'        => 'lsp_cert_submit_pendaftaran',
        'nonce'         => $nonce,
        'kegiatan_id'   => $ids['terbuka'],
        'nama_lengkap'  => 'Peserta Uji ' . wp_rand( 1000, 9999 ),
        'nik'           => '3200000000' . wp_rand( 100000, 999999 ),
        'tempat_lahir'  => 'Bandung',
        'tanggal_lahir' => '1995-05-05',
        'jenis_kelamin' => 'Laki-laki',
        'alamat'        => 'Jalan Pengujian Nomor 1',
        'kode_pos'      => '40111',
        'telepon'       => '081234567890',
        'email'         => 'uji_' . wp_rand( 1000, 9999 ) . '@example.test',
        'pendidikan'    => 'S1 Ilmu Komunikasi',
        'tujuan_asesmen' => 'sertifikasi',
    );
    $body = array_merge( $default, $override );

    $ch = curl_init( $aj_url );
    curl_setopt_array( $ch, array(
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query( $body ),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
    ) );
    $out  = curl_exec( $ch );
    $code = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );
    curl_close( $ch );

    return array( 'code' => $code, 'body' => (string) $out );
}

echo "== A. Kegiatan valid ==\n";
$r = kirim();
cek( 'A1 HTTP diterima', $r['code'], 200 );
cek_true( 'A2 sukses tersimpan', false !== strpos( $r['body'], '"success":true' ) );
$baru_id = 0;
if ( preg_match( '/"pendaftaran_id":(\d+)/', $r['body'], $m ) ) { $baru_id = (int) $m[1]; }
cek_true( 'A3 id pendaftaran dikembalikan', $baru_id > 0 );

echo "\n== B. Aturan kegiatan ditegakkan ==\n";
$r = kirim( array( 'kegiatan_id' => $ids['ditutup'] ) );
cek( 'B1 kegiatan ditutup ditolak', $r['code'], 409 );
cek_true( 'B2 pesan menyebut ditutup', false !== stripos( $r['body'], 'ditutup' ) );

$r = kirim( array( 'kegiatan_id' => $ids['kedaluwarsa'] ) );
cek( 'B3 kegiatan lewat tanggal ditolak', $r['code'], 409 );
cek_true( 'B4 pesan menyebut tanggal lewat', false !== stripos( $r['body'], 'sudah lewat' ) );

$r = kirim( array( 'kegiatan_id' => $ids['skema_rusak'] ) );
cek( 'B5 skema tidak valid ditolak', $r['code'], 409 );
cek_true( 'B6 pesan menyebut skema', false !== stripos( $r['body'], 'Skema' ) );

$r = kirim( array( 'kegiatan_id' => 99999999 ) );
cek_true( 'B7 kegiatan tidak ada ditolak', false !== strpos( $r['body'], 'tidak ditemukan' ) );

$r = kirim( array( 'kegiatan_id' => $ids['penuh'] ) );
cek_true( 'B8 kuota penuh ditolak', false !== stripos( $r['body'], 'penuh' ) );

echo "\n== C. Duplikasi peserta ==\n";
$nik_unik   = '3200000000' . wp_rand( 100000, 999999 );
$email_unik = 'dup_' . wp_rand( 1000, 9999 ) . '@example.test';
$r = kirim( array( 'nik' => $nik_unik, 'email' => $email_unik ) );
cek( 'C1 pendaftaran pertama diterima', $r['code'], 200 );
if ( preg_match( '/"pendaftaran_id":(\d+)/', $r['body'], $m2 ) ) { $dup_id = (int) $m2[1]; } else { $dup_id = 0; }

$r = kirim( array( 'nik' => $nik_unik, 'email' => 'lain_' . wp_rand( 1000, 9999 ) . '@example.test' ) );
cek( 'C2 NIK ganda ditolak', $r['code'], 409 );
cek_true( 'C3 pesan menyebut NIK', false !== stripos( $r['body'], 'NIK' ) );

$r = kirim( array( 'nik' => '3200000000' . wp_rand( 100000, 999999 ), 'email' => $email_unik ) );
cek( 'C4 email ganda ditolak', $r['code'], 409 );
cek_true( 'C5 pesan menyebut email', false !== stripos( $r['body'], 'Email' ) );

echo "\n== D. Format data identitas ==\n";
$r = kirim( array( 'nik' => '12345' ) );
cek_true( 'D1 NIK bukan 16 angka ditolak', false !== stripos( $r['body'], '16 angka' ) );

$r = kirim( array( 'tanggal_lahir' => '05-05-1995' ) );
cek_true( 'D2 format tanggal lahir salah ditolak', false !== stripos( $r['body'], 'YYYY-MM-DD' ) );

$r = kirim( array( 'tanggal_lahir' => date( 'Y-m-d', strtotime( '+1 year' ) ) ) );
cek_true( 'D3 tanggal lahir masa depan ditolak', false !== stripos( $r['body'], 'masa depan' ) );

$r = kirim( array( 'jenis_kelamin' => 'Lainnya' ) );
cek_true( 'D4 jenis kelamin tak dikenal ditolak', false !== stripos( $r['body'], 'Jenis kelamin' ) );

$r = kirim( array( 'kode_pos' => '40' ) );
cek_true( 'D5 kode pos tidak valid ditolak', false !== stripos( $r['body'], 'Kode pos' ) );

$r = kirim( array( 'tujuan_asesmen' => 'sembarang' ) );
cek_true( 'D6 tujuan asesmen tak dikenal ditolak', false !== stripos( $r['body'], 'Tujuan asesmen' ) );

$r = kirim( array( 'email' => 'bukan-email' ) );
cek_true( 'D7 email tidak valid ditolak', false !== stripos( $r['body'], 'email tidak valid' ) );

echo "\n== E. Nonce & rate limit ==\n";
$r = kirim( array( 'nonce' => 'palsu' ) );
cek_true( 'E1 nonce palsu ditolak', 200 !== $r['code'] || false !== strpos( $r['body'], '-1' ) );

bersihkan_rate_limit();
$kode = array();
for ( $i = 0; $i < 4; $i++ ) {
    $res = kirim_tanpa_reset();
    $kode[] = $res['code'];
}
cek( 'E2 tiga kiriman pertama diproses', $kode[2], 200 );
cek( 'E3 kiriman keempat kena rate limit', $kode[3], 429 );

function kirim_tanpa_reset() {
    global $aj_url, $nonce, $ids;
    $body = array(
        'action'        => 'lsp_cert_submit_pendaftaran',
        'nonce'         => $nonce,
        'kegiatan_id'   => $ids['terbuka'],
        'nama_lengkap'  => 'Rate Test ' . wp_rand( 1000, 9999 ),
        'nik'           => '3200000000' . wp_rand( 100000, 999999 ),
        'tempat_lahir'  => 'Bandung',
        'tanggal_lahir' => '1995-05-05',
        'jenis_kelamin' => 'Laki-laki',
        'alamat'        => 'Jalan Uji',
        'kode_pos'      => '40111',
        'telepon'       => '081234567890',
        'email'         => 'rate_' . wp_rand( 1000, 9999 ) . '@example.test',
        'pendidikan'    => 'S1',
        'tujuan_asesmen' => 'sertifikasi',
    );
    $ch = curl_init( $aj_url );
    curl_setopt_array( $ch, array( CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query( $body ), CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20 ) );
    $out  = curl_exec( $ch );
    $code = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );
    curl_close( $ch );
    return array( 'code' => $code, 'body' => (string) $out );
}

// --- Bersihkan semua data uji ---------------------------------------------
bersihkan_rate_limit();
$wpdb->query( "DELETE FROM {$tabel_p} WHERE kegiatan_id IN (" . implode( ',', array_map( 'intval', $ids ) ) . ")" );
foreach ( $ids as $id ) {
    $wpdb->delete( $tabel_k, array( 'id' => $id ) );
}
$sisa = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tabel_k} WHERE id IN (" . implode( ',', array_map( 'intval', $ids ) ) . ")" );
cek( 'F1 fixture kegiatan terhapus', $sisa, 0 );
$sisa_p = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tabel_p} WHERE kegiatan_id IN (" . implode( ',', array_map( 'intval', $ids ) ) . ")" );
cek( 'F2 pendaftaran uji terhapus', $sisa_p, 0 );

echo "\n" . ( $fail === 0 ? "SEMUA LULUS\n" : "{$fail} GAGAL\n" );
exit( $fail === 0 ? 0 : 1 );
