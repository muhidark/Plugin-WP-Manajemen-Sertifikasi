<?php
/**
 * PH-06 — Test gate integritas APL.02.
 *
 * Browser hanya boleh mengirim kode unit, nomor elemen, dan pilihan K/BK.
 * Struktur (judul unit, nama elemen) harus dibangun ulang dari Master SKKNI.
 *
 * Jalankan: php tests/ph06-apl02-integrity.php
 */

$_SERVER['HTTP_HOST']      = 'localhost';
$_SERVER['REQUEST_URI']    = '/wordpress/';
$_SERVER['SERVER_NAME']    = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';

define( 'WP_ADMIN', true );
define( 'LSP_CERT_TESTING', true );

require dirname( __DIR__, 4 ) . '/wp-load.php';
wp_set_current_user( 1 );

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

$skema = get_posts( array( 'post_type' => 'skema', 'posts_per_page' => 1, 'post_status' => 'any' ) );
if ( ! $skema || empty( LSP_Cert_Integration::get_unit_kompetensi( $skema[0]->ID ) ) ) {
    echo "SKIP: perlu satu Master Skema dengan unit & elemen lengkap\n";
    exit( 0 );
}
$skema_id = (int) $skema[0]->ID;
$units    = LSP_Cert_Integration::get_unit_kompetensi( $skema_id );

$unit1 = $units[0];
$unit2 = isset( $units[1] ) ? $units[1] : null;
$el1   = $unit1['elemen'][0] ?? null;
if ( ! $el1 ) {
    echo "SKIP: unit pertama tanpa elemen\n";
    exit( 0 );
}
$el2 = $unit1['elemen'][1] ?? null;

// --- Fixture kegiatan ------------------------------------------------------
$tabel_k = $wpdb->prefix . 'lsp_kegiatan';
$tabel_p = $wpdb->prefix . 'lsp_pendaftaran';
$tabel_j = $wpdb->prefix . 'lsp_apl02_jawaban';

$wpdb->insert( $tabel_k, array(
    'skema_id'            => $skema_id,
    'tanggal_pelaksanaan' => date( 'Y-m-d', strtotime( '+7 days' ) ),
    'tuk_id'              => 0,
    'kuota'               => 10,
    'status'              => 'dibuka',
) );
$kegiatan_id = (int) $wpdb->insert_id;

// --- Kirim pendaftaran dengan data APL.02 yang dimanipulasi -----------------
// Nonce harus dibuat sebagai pengguna anonim (user 0), karena request HTTP-nya anonim.
wp_set_current_user( 0 );
$nonce = wp_create_nonce( 'lsp_cert_public_nonce' );
wp_set_current_user( 1 );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_lsp_cert_rl_daftar_%' OR option_name LIKE '_transient_timeout_lsp_cert_rl_daftar_%'" );

$apl02 = array(
    // valid, tapi judul & nama elemen dipalsukan
    array(
        'kode'        => $unit1['kode'],
        'judul'       => 'JUDUL PALSU DARI BROWSER',
        'elemen_nomor' => (int) $el1['nomor'],
        'elemen_nama' => 'ELEMEN PALSU DARI BROWSER',
        'jawaban'     => 'K',
    ),
);
if ( $el2 ) {
    // valid, jawaban huruf kecil (harus dinormalkan)
    $apl02[] = array(
        'kode'         => $unit1['kode'],
        'judul'        => 'Judul lain palsu',
        'elemen_nomor' => (int) $el2['nomor'],
        'elemen_nama'  => 'Nama lain palsu',
        'jawaban'      => 'bk',
    );
}
// kode unit tidak dikenal
$apl02[] = array( 'kode' => 'ZZZ.000XXX.999.9', 'elemen_nomor' => 1, 'jawaban' => 'K', 'judul' => 'X', 'elemen_nama' => 'Y' );
// nomor elemen tidak ada
$apl02[] = array( 'kode' => $unit1['kode'], 'elemen_nomor' => 99, 'jawaban' => 'K' );
// jawaban di luar K/BK
$apl02[] = array( 'kode' => $unit1['kode'], 'elemen_nomor' => (int) $el1['nomor'], 'jawaban' => 'KOMPETEN' );
$apl02[] = array( 'kode' => $unit1['kode'], 'elemen_nomor' => (int) $el1['nomor'], 'jawaban' => '' );

$body = array(
    'action'         => 'lsp_cert_submit_pendaftaran',
    'nonce'          => $nonce,
    'kegiatan_id'    => $kegiatan_id,
    'nama_lengkap'   => 'Peserta Uji APL02',
    'nik'            => '3200000000' . wp_rand( 100000, 999999 ),
    'tempat_lahir'   => 'Bandung',
    'tanggal_lahir'  => '1995-05-05',
    'jenis_kelamin'  => 'Laki-laki',
    'alamat'         => 'Jalan Integritas 1',
    'kode_pos'       => '40111',
    'telepon'        => '081234567890',
    'email'          => 'apl02_' . wp_rand( 1000, 9999 ) . '@example.test',
    'pendidikan'     => 'S1',
    'tujuan_asesmen' => 'sertifikasi',
);

$ch = curl_init( 'http://localhost/wordpress/wp-admin/admin-ajax.php' );
curl_setopt_array( $ch, array(
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query( $body ) . '&' . http_build_query( array( 'apl02' => $apl02 ) ),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 25,
) );
$resp = curl_exec( $ch );
$code = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );
curl_close( $ch );

echo "== A. Pengiriman ==\n";
cek( 'A1 HTTP diterima', $code, 200 );
cek_true( 'A2 pendaftaran tersimpan', false !== strpos( (string) $resp, '"success":true' ) );

$pid = 0;
if ( preg_match( '/"pendaftaran_id":(\d+)/', (string) $resp, $m ) ) { $pid = (int) $m[1]; }
cek_true( 'A3 id pendaftaran dikembalikan', $pid > 0 );

$harap_valid = $el2 ? 2 : 1;
cek( 'A4 jumlah jawaban sah tersimpan', (int) preg_match( '/"apl02_tersimpan":(\d+)/', (string) $resp, $m2 ) ? (int) $m2[1] : -1, $harap_valid );
$harap_abaikan = 4; // kode asing, elemen 99, jawaban KOMPETEN, jawaban kosong
cek( 'A5 jumlah entri diabaikan', (int) preg_match( '/"apl02_diabaikan":(\d+)/', (string) $resp, $m3 ) ? (int) $m3[1] : -1, $harap_abaikan );

echo "\n== B. Isi database memakai data master ==\n";
$rows = $pid ? $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$tabel_j} WHERE pendaftaran_id = %d ORDER BY elemen_nomor", $pid ) ) : array();
cek( 'B1 jumlah baris jawaban', count( $rows ), $harap_valid );

if ( ! empty( $rows ) ) {
    cek( 'B2 judul unit dari master (bukan palsu)', $rows[0]->unit_kompetensi_judul, $unit1['judul'] );
    cek( 'B3 nama elemen dari master (bukan palsu)', $rows[0]->elemen_nama, $el1['nama'] );
    cek( 'B4 jawaban K tersimpan', strtoupper( $rows[0]->jawaban ), 'K' );
    if ( $el2 && isset( $rows[1] ) ) {
        cek( 'B5 jawaban bk dinormalkan', strtoupper( $rows[1]->jawaban ), 'BK' );
        cek( 'B6 elemen kedua dari master', $rows[1]->elemen_nama, $el2['nama'] );
    }
    cek_true( 'B7 tidak ada judul palsu di DB', false === strpos( wp_json_encode( $rows ), 'PALSU' ) );
}

echo "\n== C. Dokumen APL.02 memakai data master ==\n";
if ( $pid ) {
    ob_start();
    try { LSP_Cert_Document_Printer::render_print_page( $pid, 'apl02' ); } catch ( Throwable $e ) {}
    $html = ob_get_clean();

    cek_true( 'C1 dokumen terbentuk', strlen( $html ) > 1000 );
    cek_true( 'C2 teks elemen asli muncul', false !== strpos( $html, $el1['nama'] ) );
    cek_true( 'C3 tidak ada teks palsu', false === strpos( $html, 'ELEMEN PALSU' ) && false === strpos( $html, 'JUDUL PALSU' ) );
    // Abaikan blok <script> (berisi karakter centang untuk pratinjau di layar).
    $html_tanpa_script = preg_replace( '/<script\b[^>]*>.*?<\/script>/is', '', $html );
    $jumlah_centang = substr_count( (string) $html_tanpa_script, '☑' );
    cek( 'C4 jumlah tanda centang = jumlah jawaban tersimpan', $jumlah_centang, $harap_valid );
}

echo "\n== D. Pembersihan ==\n";
if ( $pid ) {
    $wpdb->delete( $tabel_j, array( 'pendaftaran_id' => $pid ) );
    $wpdb->delete( $tabel_p, array( 'id' => $pid ) );
}
$wpdb->delete( $tabel_k, array( 'id' => $kegiatan_id ) );
cek( 'D1 pendaftaran uji terhapus', (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tabel_p} WHERE id = %d", $pid ) ), 0 );
cek( 'D2 kegiatan uji terhapus', (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tabel_k} WHERE id = %d", $kegiatan_id ) ), 0 );

echo "\n" . ( $fail === 0 ? "SEMUA LULUS\n" : "{$fail} GAGAL\n" );
exit( $fail === 0 ? 0 : 1 );
