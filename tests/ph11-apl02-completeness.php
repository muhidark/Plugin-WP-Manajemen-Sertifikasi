<?php
/**
 * PH-11 — Kelengkapan FR.APL.02 (temuan uji manual STG-02).
 *
 * Temuan pengguna: formulir APL.02 tercetak dengan seluruh kolom K/BK kosong,
 * dan admin dapat menerima pendaftaran tanpa memeriksanya.
 *
 * Empat lapis yang diuji:
 *   1. kiriman APL.02 sebagian ditolak (tidak tersimpan setengah jadi);
 *   2. pendaftaran tanpa APL.02 tetap boleh, tetapi tidak bisa diterima;
 *   3. jawaban BK mewajibkan konfirmasi ke asesi sebelum diterima;
 *   4. cetak FR.APL.02 / paket lengkap ditahan selama masih kosong.
 *
 * Jalankan: php tests/ph11-apl02-completeness.php
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
    printf( "%s %-60s harap=%s dapat=%s\n", $ok ? 'PASS' : 'FAIL', $label, var_export( $expected, true ), var_export( $actual, true ) );
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

/** Susun payload APL.02 untuk N elemen pertama dari master. */
function payload_apl02( $skema_id, $jumlah = null, $jawaban = 'K' ) {
    $baris = array();
    $unit  = LSP_Cert_Integration::get_unit_kompetensi( (int) $skema_id );
    foreach ( $unit as $u ) {
        $kode = trim( (string) ( $u['kode'] ?? '' ) );
        if ( '' === $kode ) { continue; }
        foreach ( (array) ( $u['elemen'] ?? array() ) as $idx => $e ) {
            $baris[] = array(
                'kode'         => $kode,
                'elemen_nomor' => isset( $e['nomor'] ) ? (int) $e['nomor'] : ( $idx + 1 ),
                'jawaban'      => $jawaban,
            );
        }
    }
    return null === $jumlah ? $baris : array_slice( $baris, 0, $jumlah );
}

$admin   = identitas( 1 );
$base    = 'http://localhost/wordpress/wp-admin/admin.php';
$ajax    = 'http://localhost/wordpress/wp-admin/admin-ajax.php';
$nonce_p = wp_create_nonce( 'lsp_cert_public_nonce' );
$tabel_k = $wpdb->prefix . 'lsp_kegiatan';
$tabel_p = $wpdb->prefix . 'lsp_pendaftaran';
$tabel_j = $wpdb->prefix . 'lsp_apl02_jawaban';

$skema_list = get_posts( array( 'post_type' => 'skema', 'posts_per_page' => 1, 'post_status' => 'any' ) );
if ( ! $skema_list ) { echo "SKIP: tidak ada Master Skema\n"; exit( 0 ); }
$skema_id = (int) $skema_list[0]->ID;
$total_elemen = count( payload_apl02( $skema_id ) );
if ( $total_elemen < 3 ) { echo "SKIP: master skema tidak punya cukup elemen\n"; exit( 0 ); }
printf( "Master skema #%d: %d elemen\n\n", $skema_id, $total_elemen );

// Kegiatan uji
$wpdb->insert( $tabel_k, array(
    'skema_id' => $skema_id, 'tanggal_pelaksanaan' => date( 'Y-m-d', strtotime( '+10 days' ) ),
    'tuk_id' => 0, 'kuota' => 10, 'status' => 'dibuka', 'asesor_ids' => wp_json_encode( array( 1 ) ),
) );
$kegiatan_id = (int) $wpdb->insert_id;

$bersihkan_rl = function () use ( $wpdb ) {
    // Pembatas 3 pendaftaran/menit per IP akan mengganggu uji berurutan.
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_lsp_cert_rl_daftar_%' OR option_name LIKE '_transient_timeout_lsp_cert_rl_daftar_%'" );
};

$daftar = function ( $nama, $nik, $apl02 = null ) use ( $ajax, $nonce_p, $kegiatan_id, $bersihkan_rl ) {
    $bersihkan_rl();
    $body = array(
        'action' => 'lsp_cert_submit_pendaftaran', 'nonce' => $nonce_p, 'kegiatan_id' => $kegiatan_id,
        'nama_lengkap' => $nama, 'nik' => $nik, 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1996-01-01',
        'jenis_kelamin' => 'Laki-laki', 'alamat' => 'Jalan Uji', 'kode_pos' => '40111',
        'telepon' => '081200000009', 'email' => strtolower( str_replace( ' ', '.', $nama ) ) . '@example.test',
        'pendidikan' => 'S1',
    );
    if ( null !== $apl02 ) { $body['apl02'] = $apl02; }
    return minta( $ajax, '', $body );
};

$terima = function ( $pendaftaran_id, $konfirmasi = false ) use ( $ajax, $admin ) {
    $body = array(
        'action' => 'lsp_cert_save_verifikasi', 'nonce' => $admin['nonce'], 'id' => $pendaftaran_id,
        'status_kelengkapan' => 'lengkap', 'status_rekomendasi' => 'disetujui',
    );
    if ( $konfirmasi ) { $body['konfirmasi_bk'] = 1; }
    return minta( $ajax, $admin['cookie'], $body );
};

echo "== A. Pendaftaran tanpa APL.02 ==\n";
$r = $daftar( 'Peserta Kosong Apl02', '3273000000000901' );
cek( 'A1 pendaftaran diterima', $r['code'], 200 );
cek_true( 'A2 respons menandai apl02 kosong', false !== strpos( $r['body'], '"apl02_status":"kosong"' ) );

$p_kosong = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tabel_p} WHERE kegiatan_id = %d ORDER BY id DESC LIMIT 1", $kegiatan_id ) );
$kep = LSP_Cert_Integration::get_apl02_kelengkapan( $p_kosong );
cek( 'A3 elemen terjawab', (int) $kep['terjawab'], 0 );
cek( 'A4 total elemen dari master', (int) $kep['total'], $total_elemen );
cek_true( 'A5 ditandai belum terisi', false === $kep['terisi'] );

echo "\n== B. Admin tidak bisa menerima APL.02 kosong ==\n";
$r = $terima( $p_kosong );
cek( 'B1 status HTTP', $r['code'], 409 );
cek_true( 'B2 pesan menyebut APL.02 belum diisi', false !== strpos( $r['body'], 'belum diisi sama sekali' ) );
cek( 'B3 status tidak berubah', $wpdb->get_var( $wpdb->prepare( "SELECT status_rekomendasi FROM {$tabel_p} WHERE id = %d", $p_kosong ) ), 'pending' );

echo "\n== C. Cetak ditahan saat APL.02 kosong ==\n";
$r = minta( "{$base}?page=lsp-pendaftaran&action=print&id={$p_kosong}&doc=apl02", $admin['cookie'] );
cek( 'C1 cetak APL.02 ditolak', $r['code'], 409 );
cek_true( 'C2 pesan "FR.APL.02 Belum Lengkap"', false !== strpos( $r['body'], 'FR.APL.02 Belum Lengkap' ) );
cek( 'C3 cetak APL.01 tetap boleh', minta( "{$base}?page=lsp-pendaftaran&action=print&id={$p_kosong}&doc=apl01", $admin['cookie'] )['code'], 200 );
$r = minta( "{$base}?page=lsp-pendaftaran&action=print&id={$p_kosong}&doc=bundle_all", $admin['cookie'] );
cek( 'C4 paket lengkap ditolak', $r['code'], 409 );

echo "\n== D. Kiriman APL.02 sebagian ditolak ==\n";
$sebelum = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tabel_p}" );
$r = $daftar( 'Peserta Sebagian', '3273000000000902', payload_apl02( $skema_id, 3 ) );
cek( 'D1 status HTTP', $r['code'], 422 );
cek_true( 'D2 pesan menyebut jumlah elemen belum dijawab', false !== strpos( $r['body'], 'belum dijawab' ) );
$sesudah = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tabel_p}" );
cek( 'D3 tidak ada pendaftaran yatim', $sesudah, $sebelum );

echo "\n== E. Kiriman APL.02 lengkap (semua K) ==\n";
$r = $daftar( 'Peserta Lengkap', '3273000000000903', payload_apl02( $skema_id ) );
cek( 'E1 pendaftaran diterima', $r['code'], 200 );
$p_lengkap = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tabel_p} WHERE kegiatan_id = %d ORDER BY id DESC LIMIT 1", $kegiatan_id ) );
$kep = LSP_Cert_Integration::get_apl02_kelengkapan( $p_lengkap );
cek( 'E2 semua elemen terjawab', (int) $kep['terjawab'], $total_elemen );
cek( 'E3 semua dijawab K', (int) $kep['k'], $total_elemen );
cek_true( 'E4 dinyatakan lengkap', $kep['lengkap'] );
cek( 'E5 baris jawaban tersimpan', (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tabel_j} WHERE pendaftaran_id = %d", $p_lengkap ) ), $total_elemen );

echo "\n== F. Penerimaan saat lengkap & tanpa BK ==\n";
$r = $terima( $p_lengkap );
cek( 'F1 diterima', $r['code'], 200 );
cek( 'F2 status tersimpan', $wpdb->get_var( $wpdb->prepare( "SELECT status_rekomendasi FROM {$tabel_p} WHERE id = %d", $p_lengkap ) ), 'disetujui' );

echo "\n== G. Jawaban BK mewajibkan konfirmasi ==\n";
$baris_bk = payload_apl02( $skema_id );
$baris_bk[0]['jawaban'] = 'BK';
$baris_bk[1]['jawaban'] = 'BK';
LSP_Cert_Database::delete_apl02_jawaban( $p_lengkap );
foreach ( $baris_bk as $row ) {
    $row['pendaftaran_id'] = $p_lengkap;
    LSP_Cert_Database::insert_apl02_jawaban( array(
        'pendaftaran_id'        => $p_lengkap,
        'unit_kompetensi_kode'  => $row['kode'],
        'unit_kompetensi_judul' => '',
        'elemen_nomor'          => $row['elemen_nomor'],
        'elemen_nama'           => '',
        'jawaban'               => $row['jawaban'],
    ) );
}
$wpdb->update( $tabel_p, array( 'status_rekomendasi' => 'pending', 'konfirmasi_bk' => null ), array( 'id' => $p_lengkap ) );

$kep = LSP_Cert_Integration::get_apl02_kelengkapan( $p_lengkap );
cek( 'G1 dua elemen BK terdeteksi', (int) $kep['bk'], 2 );
cek_true( 'G2 tetap dianggap lengkap', $kep['lengkap'] );

$r = $terima( $p_lengkap );
cek( 'G3 ditolak tanpa konfirmasi', $r['code'], 409 );
cek_true( 'G4 pesan menyebut konfirmasi ke asesi', false !== strpos( $r['body'], 'Konfirmasi ke asesi' ) );

$r = $terima( $p_lengkap, true );
cek( 'G5 diterima setelah konfirmasi', $r['code'], 200 );
cek_true( 'G6 waktu konfirmasi tercatat', ! empty( $wpdb->get_var( $wpdb->prepare( "SELECT konfirmasi_bk FROM {$tabel_p} WHERE id = %d", $p_lengkap ) ) ) );

echo "\n== H. Cetak setelah lengkap ==\n";
$r = minta( "{$base}?page=lsp-pendaftaran&action=print&id={$p_lengkap}&doc=apl02", $admin['cookie'] );
cek( 'H1 cetak APL.02', $r['code'], 200 );
cek_true( 'H2 tidak ada pesan penolakan', false === strpos( $r['body'], 'Belum Lengkap' ) );
cek_true( sprintf( 'H3 ukuran dokumen wajar (%.1f KB)', $r['ukuran'] / 1024 ), $r['ukuran'] > 40000 );
cek( 'H4 paket lengkap', minta( "{$base}?page=lsp-pendaftaran&action=print&id={$p_lengkap}&doc=bundle_all", $admin['cookie'] )['code'], 200 );

// --- bersihkan -------------------------------------------------------------
foreach ( array( $p_kosong, $p_lengkap ) as $pid ) {
    $wpdb->query( $wpdb->prepare( "DELETE FROM {$tabel_j} WHERE pendaftaran_id = %d", $pid ) );
    $wpdb->query( $wpdb->prepare( "DELETE FROM {$tabel_p} WHERE id = %d", $pid ) );
}
$wpdb->query( $wpdb->prepare( "DELETE FROM {$tabel_k} WHERE id = %d", $kegiatan_id ) );
cek( 'I1 data uji dibersihkan', (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tabel_p} WHERE kegiatan_id = %d", $kegiatan_id ) ), 0 );

echo "\n" . ( $fail === 0 ? "SEMUA LULUS\n" : "{$fail} GAGAL\n" );
exit( $fail === 0 ? 0 : 1 );
