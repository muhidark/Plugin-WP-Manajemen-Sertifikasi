<?php
/**
 * PH-04 — Test gate role & capability.
 *
 * Memastikan `asesor` tidak lagi dipakai sebagai capability, capability baru
 * terpasang pada role yang tepat, dan isolasi data asesor tetap bekerja.
 *
 * Jalankan: php tests/ph04-capability.php
 */

$_SERVER['HTTP_HOST']      = 'localhost';
$_SERVER['REQUEST_URI']    = '/wordpress/wp-admin/';
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
    printf( "%s %-56s harap=%s dapat=%s\n", $ok ? 'PASS' : 'FAIL', $label, var_export( $expected, true ), var_export( $actual, true ) );
}
function cek_true( $label, $cond ) {
    global $fail;
    if ( ! $cond ) { $fail++; }
    printf( "%s %s\n", $cond ? 'PASS' : 'FAIL', $label );
}

echo "== A. Peta capability pada role ==\n";
$admin_role = get_role( 'administrator' );
$asesor_role = get_role( 'asesor' );

cek_true( 'A1 version capability tersimpan', get_option( 'lsp_cert_caps_version' ) === LSP_Cert_Capabilities::VERSION );

foreach ( LSP_Cert_Capabilities::all_caps() as $cap ) {
    cek_true( "A2 administrator punya {$cap}", ! empty( $admin_role->capabilities[ $cap ] ) );
}
cek_true( 'A3 asesor punya lsp_cert_view_docs', ! empty( $asesor_role->capabilities['lsp_cert_view_docs'] ) );
cek_true( 'A4 asesor punya lsp_cert_print_docs', ! empty( $asesor_role->capabilities['lsp_cert_print_docs'] ) );
cek_true( 'A5 asesor TIDAK punya manage_activities', empty( $asesor_role->capabilities['lsp_cert_manage_activities'] ) );
cek_true( 'A6 asesor TIDAK punya manage_instruments', empty( $asesor_role->capabilities['lsp_cert_manage_instruments'] ) );

echo "\n== B. Perilaku current_user_can per peran ==\n";
$asesor_id = 0;
foreach ( get_users( array( 'role' => 'asesor', 'number' => 1, 'fields' => 'ID' ) ) as $uid ) {
    $asesor_id = (int) $uid;
}

$sub_id = wp_insert_user( array(
    'user_login' => 'ph04_sub_' . wp_rand( 1000, 9999 ),
    'user_pass'  => wp_generate_password( 16 ),
    'user_email' => 'ph04_sub_' . wp_rand( 1000, 9999 ) . '@example.test',
    'role'       => 'subscriber',
) );

wp_set_current_user( 1 );
cek_true( 'B1 admin: print_docs', current_user_can( 'lsp_cert_print_docs' ) );
cek_true( 'B2 admin: manage_activities', current_user_can( 'lsp_cert_manage_activities' ) );

if ( $asesor_id ) {
    wp_set_current_user( $asesor_id );
    cek_true( 'B3 asesor: print_docs', current_user_can( 'lsp_cert_print_docs' ) );
    cek_true( 'B4 asesor: view_docs', current_user_can( 'lsp_cert_view_docs' ) );
    cek( 'B5 asesor: manage_activities', current_user_can( 'lsp_cert_manage_activities' ), false );
    // Catatan: WP menyimpan nama role di array caps user, sehingga
    // current_user_can( 'asesor' ) bernilai TRUE untuk pemilik role tersebut.
    // Itulah sebabnya kode lama "bekerja", meski memakai role slug sebagai capability.
    cek_true( 'B6 asesor: capability "asesor" (role slug) = true (perilaku WP)', current_user_can( 'asesor' ) );
} else {
    echo "SKIP: tidak ada user role asesor\n";
}

if ( ! is_wp_error( $sub_id ) ) {
    wp_set_current_user( $sub_id );
    cek( 'B7 subscriber: print_docs', current_user_can( 'lsp_cert_print_docs' ), false );
    cek( 'B8 subscriber: view_docs', current_user_can( 'lsp_cert_view_docs' ), false );
}

echo "\n== C. Isolasi data asesor saat mencetak ==\n";
$pendaftaran = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}lsp_pendaftaran ORDER BY id LIMIT 1" );

if ( ! $pendaftaran || ! $asesor_id ) {
    echo "SKIP: data pendaftaran / user asesor tidak tersedia\n";
} else {
    add_filter( 'wp_die_handler', function () {
        return function ( $message ) { throw new RuntimeException( trim( strip_tags( (string) $message ) ) ); };
    } );

    $asesor_asli = (int) $pendaftaran->asesor_id;

    // Asesor yang ditugaskan → boleh
    $wpdb->update( $wpdb->prefix . 'lsp_pendaftaran', array( 'asesor_id' => $asesor_id ), array( 'id' => $pendaftaran->id ) );
    wp_set_current_user( $asesor_id );
    $blok = '';
    try { LSP_Cert_Document_Printer::render_print_page( $pendaftaran->id, 'apl01' ); } catch ( RuntimeException $e ) { $blok = $e->getMessage(); }
    cek( 'C1 asesor ditugaskan: tidak diblokir', $blok, '' );

    // Asesor lain → ditolak
    $lain_id = 0;
    foreach ( get_users( array( 'role' => 'asesor', 'number' => 2, 'fields' => 'ID' ) ) as $uid ) {
        if ( (int) $uid !== $asesor_id ) { $lain_id = (int) $uid; break; }
    }
    if ( $lain_id ) {
        $wpdb->update( $wpdb->prefix . 'lsp_pendaftaran', array( 'asesor_id' => $lain_id ), array( 'id' => $pendaftaran->id ) );
        wp_set_current_user( $asesor_id );
        $blok2 = '';
        try { LSP_Cert_Document_Printer::render_print_page( $pendaftaran->id, 'apl01' ); } catch ( RuntimeException $e ) { $blok2 = $e->getMessage(); }
        cek_true( 'C2 asesor lain: ditolak', '' !== $blok2 );
    } else {
        echo "SKIP: hanya ada satu user asesor\n";
    }

    // Subscriber → ditolak
    if ( ! is_wp_error( $sub_id ) ) {
        wp_set_current_user( $sub_id );
        $blok3 = '';
        try { LSP_Cert_Document_Printer::render_print_page( $pendaftaran->id, 'apl01' ); } catch ( RuntimeException $e ) { $blok3 = $e->getMessage(); }
        cek_true( 'C3 subscriber: ditolak', '' !== $blok3 );
    }

    $wpdb->update( $wpdb->prefix . 'lsp_pendaftaran', array( 'asesor_id' => $asesor_asli ), array( 'id' => $pendaftaran->id ) );
    $pulih = $wpdb->get_var( $wpdb->prepare( "SELECT asesor_id FROM {$wpdb->prefix}lsp_pendaftaran WHERE id = %d", $pendaftaran->id ) );
    cek( 'C4 asesor_id pendaftaran dipulihkan', (int) $pulih, $asesor_asli );
}

echo "\n== D. Menu admin per capability ==\n";
wp_set_current_user( 1 );
LSP_Cert_Admin::register_menus();

$submenu = isset( $GLOBALS['submenu']['lsp-sertifikasi'] ) ? $GLOBALS['submenu']['lsp-sertifikasi'] : array();
$cap_menu = array();
foreach ( $submenu as $item ) {
    if ( isset( $item[2], $item[1] ) ) { $cap_menu[ $item[2] ] = $item[1]; }
}

cek( 'D1 submenu cetak dokumen', isset( $cap_menu['lsp-cetak-dokumen'] ) ? $cap_menu['lsp-cetak-dokumen'] : 'tidak ada', 'lsp_cert_print_docs' );
cek( 'D2 submenu pendaftaran', isset( $cap_menu['lsp-pendaftaran'] ) ? $cap_menu['lsp-pendaftaran'] : 'tidak ada', 'lsp_cert_manage_activities' );
cek( 'D3 submenu IA.02', isset( $cap_menu['lsp-ia02'] ) ? $cap_menu['lsp-ia02'] : 'tidak ada', 'lsp_cert_manage_instruments' );
cek( 'D4 submenu kelompok pekerjaan', isset( $cap_menu['lsp-kelompok-pekerjaan'] ) ? $cap_menu['lsp-kelompok-pekerjaan'] : 'tidak ada', 'lsp_cert_manage_instruments' );

$menu_cap = '';
foreach ( $GLOBALS['menu'] as $m ) {
    if ( isset( $m[2] ) && 'lsp-sertifikasi' === $m[2] ) { $menu_cap = $m[1]; break; }
}
cek( 'D5 capability menu utama', $menu_cap, 'lsp_cert_view_docs' );

if ( ! is_wp_error( $sub_id ) ) {
    require_once ABSPATH . 'wp-admin/includes/user.php';
    wp_delete_user( $sub_id );
    cek_true( 'E1 user uji dihapus', false === get_userdata( $sub_id ) );
}

echo "\n" . ( $fail === 0 ? "SEMUA LULUS\n" : "{$fail} GAGAL\n" );
exit( $fail === 0 ? 0 : 1 );
