<?php
/**
 * PH-07 — Test gate konkurensi & keunikan pendaftaran.
 *
 * Menjalankan beberapa proses PHP paralel yang memanggil
 * LSP_Cert_Database::daftar_peserta() serentak, lalu memeriksa bahwa:
 *   - kuota tidak pernah terlampaui;
 *   - satu NIK/email tidak pernah tersimpan dua kali;
 *   - indeks unik benar-benar terpasang di database.
 *
 * Jalankan: php tests/ph07-concurrency.php
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

/**
 * Jalankan beberapa worker secara paralel.
 *
 * @param int   $n         Jumlah proses.
 * @param int   $kegiatan  ID kegiatan.
 * @param array $identitas Daftar [nik, email] per proses (boleh sama semua).
 * @return array Daftar status hasil.
 */
function jalankan_paralel( $n, $kegiatan, $identitas ) {
    $worker = __DIR__ . '/ph07-worker.php';
    $procs  = array();

    for ( $i = 0; $i < $n; $i++ ) {
        $nik   = $identitas[ $i ][0];
        $email = $identitas[ $i ][1];
        $cmd   = escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $worker ) . ' '
               . (int) $kegiatan . ' ' . escapeshellarg( $nik ) . ' ' . escapeshellarg( $email ) . ' '
               . escapeshellarg( 'Worker ' . $i ) . ' 2>/dev/null';

        $desk = array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) );
        $proc = proc_open( $cmd, $desk, $pipes );
        if ( is_resource( $proc ) ) {
            $procs[] = array( 'proc' => $proc, 'pipes' => $pipes );
        }
    }

    $hasil = array();
    foreach ( $procs as $p ) {
        $out = stream_get_contents( $p['pipes'][1] );
        fclose( $p['pipes'][1] );
        fclose( $p['pipes'][2] );
        proc_close( $p['proc'] );

        $json = json_decode( (string) $out, true );
        $hasil[] = is_array( $json ) ? ( $json['status'] ?? 'parse_gagal' ) : 'parse_gagal';
    }

    return $hasil;
}

$skema = get_posts( array( 'post_type' => 'skema', 'posts_per_page' => 1, 'post_status' => 'any' ) );
if ( ! $skema ) {
    echo "SKIP: tidak ada Master Skema\n";
    exit( 0 );
}
$skema_id = (int) $skema[0]->ID;
$tabel_k  = $wpdb->prefix . 'lsp_kegiatan';
$tabel_p  = $wpdb->prefix . 'lsp_pendaftaran';

// Pastikan indeks unik sudah dibuat oleh maybe_upgrade_database().
LSP_Cert_Database::ensure_unique_indexes();

echo "== A. Indeks unik ==\n";
foreach ( array( 'uniq_kegiatan_nik', 'uniq_kegiatan_email' ) as $idx ) {
    $info = $wpdb->get_row( $wpdb->prepare( "SHOW INDEX FROM `{$tabel_p}` WHERE Key_name = %s", $idx ) );
    cek_true( "A indeks {$idx} ada", ! empty( $info ) );
    if ( $info ) {
        cek( "A {$idx} unik (Non_unique=0)", (int) $info->Non_unique, 0 );
    }
}

// --- B. Kuota 1, 10 pendaftar berbeda secara paralel ----------------------
$wpdb->insert( $tabel_k, array(
    'skema_id'            => $skema_id,
    'tanggal_pelaksanaan' => date( 'Y-m-d', strtotime( '+7 days' ) ),
    'tuk_id'              => 0,
    'kuota'               => 1,
    'status'              => 'dibuka',
) );
$keg_sempit = (int) $wpdb->insert_id;

$identitas = array();
for ( $i = 0; $i < 10; $i++ ) {
    $identitas[] = array( '3200000000' . sprintf( '%06d', 100000 + $i ), 'paralel_' . $i . '_' . wp_rand( 1000, 9999 ) . '@example.test' );
}

echo "\n== B. Kuota 1 vs 10 request paralel ==\n";
$status = jalankan_paralel( 10, $keg_sempit, $identitas );
$hitung = array_count_values( $status );

cek( 'B1 jumlah yang berhasil', $hitung['ok'] ?? 0, 1 );
cek( 'B2 sisanya ditolak karena kuota', $hitung['penuh'] ?? 0, 9 );
$baris = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tabel_p} WHERE kegiatan_id = %d", $keg_sempit ) );
cek( 'B3 baris di database tidak melebihi kuota', $baris, 1 );

// --- C. NIK & email sama, 8 request paralel -------------------------------
$wpdb->insert( $tabel_k, array(
    'skema_id'            => $skema_id,
    'tanggal_pelaksanaan' => date( 'Y-m-d', strtotime( '+7 days' ) ),
    'tuk_id'              => 0,
    'kuota'               => 10,
    'status'              => 'dibuka',
) );
$keg_lebar = (int) $wpdb->insert_id;

$nik_sama   = '3200000000' . sprintf( '%06d', 900001 );
$email_sama = 'sama_' . wp_rand( 1000, 9999 ) . '@example.test';
$identitas_sama = array();
for ( $i = 0; $i < 8; $i++ ) {
    $identitas_sama[] = array( $nik_sama, $email_sama );
}

echo "\n== C. NIK & email identik, 8 request paralel ==\n";
$status2 = jalankan_paralel( 8, $keg_lebar, $identitas_sama );
$hitung2 = array_count_values( $status2 );

cek( 'C1 tepat satu yang berhasil', $hitung2['ok'] ?? 0, 1 );
cek( 'C2 sisanya ditolak sebagai duplikat', $hitung2['ganda'] ?? 0, 7 );
$baris2 = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tabel_p} WHERE kegiatan_id = %d AND nik = %s", $keg_lebar, $nik_sama ) );
cek( 'C3 hanya satu baris tersimpan', $baris2, 1 );

// --- D. Jalur HTTP tetap berfungsi ---------------------------------------
echo "\n== D. Jalur HTTP (rate limit tetap berlaku) ==\n";
$nonce = null;
wp_set_current_user( 0 );
$nonce = wp_create_nonce( 'lsp_cert_public_nonce' );

$wpdb->insert( $tabel_k, array(
    'skema_id'            => $skema_id,
    'tanggal_pelaksanaan' => date( 'Y-m-d', strtotime( '+7 days' ) ),
    'tuk_id'              => 0,
    'kuota'               => 2,
    'status'              => 'dibuka',
) );
$keg_http = (int) $wpdb->insert_id;

$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_lsp_cert_rl_daftar_%' OR option_name LIKE '_transient_timeout_lsp_cert_rl_daftar_%'" );

$body = array(
    'action'         => 'lsp_cert_submit_pendaftaran',
    'nonce'          => $nonce,
    'kegiatan_id'    => $keg_http,
    'nama_lengkap'   => 'Peserta HTTP',
    'nik'            => '3200000000' . sprintf( '%06d', 950001 ),
    'tempat_lahir'   => 'Bandung',
    'tanggal_lahir'  => '1995-05-05',
    'jenis_kelamin'  => 'Laki-laki',
    'alamat'         => 'Jalan Uji',
    'kode_pos'       => '40111',
    'telepon'        => '081234567890',
    'email'          => 'http_' . wp_rand( 1000, 9999 ) . '@example.test',
    'pendidikan'     => 'S1',
    'tujuan_asesmen' => 'sertifikasi',
);

$ch = curl_init( 'http://localhost/wordpress/wp-admin/admin-ajax.php' );
curl_setopt_array( $ch, array( CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query( $body ), CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 25 ) );
$resp = curl_exec( $ch );
$code = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );
curl_close( $ch );

cek( 'D1 HTTP menerima pendaftaran', $code, 200 );
cek_true( 'D2 respons sukses', false !== strpos( (string) $resp, '"success":true' ) );

// --- Bersihkan ------------------------------------------------------------
$semua_keg = array( $keg_sempit, $keg_lebar, $keg_http );
$daftar    = implode( ',', array_map( 'intval', $semua_keg ) );
$wpdb->query( "DELETE FROM {$tabel_p} WHERE kegiatan_id IN ($daftar)" );
foreach ( $semua_keg as $kid ) {
    $wpdb->delete( $tabel_k, array( 'id' => $kid ) );
}
cek( 'E1 fixture kegiatan terhapus', (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tabel_k} WHERE id IN ($daftar)" ), 0 );
cek( 'E2 pendaftaran uji terhapus', (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tabel_p} WHERE kegiatan_id IN ($daftar)" ), 0 );

echo "\n" . ( $fail === 0 ? "SEMUA LULUS\n" : "{$fail} GAGAL\n" );
exit( $fail === 0 ? 0 : 1 );
