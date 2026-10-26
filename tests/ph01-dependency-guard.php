<?php
/**
 * PH-01 — Test gate dependency guard.
 *
 * Menguji lsp_cert_core_available() / lsp_cert_check_dependencies() dalam konteks
 * terisolasi (tanpa WordPress penuh), mensimulasikan tiga kondisi:
 *
 *   C3. is_plugin_active() tidak tersedia (perilaku frontend WP < 6.7)
 *   C1. Core tidak tersedia
 *   C2. Core tersedia
 *
 * Jalankan: php tests/ph01-dependency-guard.php
 * Exit code 0 = semua lulus.
 */

$stub_root = sys_get_temp_dir() . '/lsp-ph01-stub-' . getmypid();
@mkdir( $stub_root . '/wp-admin/includes', 0777, true );
file_put_contents(
    $stub_root . '/wp-admin/includes/plugin.php',
    "<?php\nif ( ! function_exists( 'is_plugin_active' ) ) {\n    function is_plugin_active( \$plugin ) { return false; }\n}\n"
);

define( 'ABSPATH', $stub_root . '/' );

// Stub fungsi WordPress yang dipakai saat file utama plugin dimuat.
function add_action() {}
function register_activation_hook() {}
function register_deactivation_hook() {}
function plugin_dir_path( $file ) { return dirname( $file ) . '/'; }
function plugin_dir_url( $file ) { return 'http://example.test/'; }
function plugin_basename( $file ) { return basename( dirname( $file ) ) . '/' . basename( $file ); }
function is_admin() { return false; }
function deactivate_plugins() {}
function wp_die( $message ) { throw new RuntimeException( 'wp_die: ' . $message ); }

$plugin_file = dirname( __DIR__ ) . '/lsp-certification-management.php';
require $plugin_file;

$fail = 0;
function check( $label, $actual, $expected ) {
    global $fail;
    $ok = ( $actual === $expected );
    if ( ! $ok ) { $fail++; }
    printf( "%s %-58s harap=%s dapat=%s\n",
        $ok ? 'PASS' : 'FAIL',
        $label,
        var_export( $expected, true ),
        var_export( $actual, true )
    );
}

// --- C3: fungsi is_plugin_active() belum ada -------------------------------
check( 'C3 is_plugin_active() belum terdefinisi', function_exists( 'is_plugin_active' ), false );

$hasil = lsp_cert_core_available();          // harus memuat plugin.php lalu false
check( 'C3 helper tidak fatal & mengembalikan false', $hasil, false );
check( 'C3 plugin.php stub berhasil dimuat', function_exists( 'is_plugin_active' ), true );

// --- C1: Core tidak tersedia ----------------------------------------------
check( 'C1 class Core tidak ada', class_exists( 'LSP_Core_Management' ), false );
check( 'C1 helper = false', lsp_cert_core_available(), false );
check( 'C1 check_dependencies = false', lsp_cert_check_dependencies(), false );

// --- C2: Core tersedia ------------------------------------------------------
if ( ! class_exists( 'LSP_Core_Management' ) ) {
    eval( 'class LSP_Core_Management { public static function get_skema_skkni_data( $id ) { return array(); } }' );
}
check( 'C2 helper = true', lsp_cert_core_available(), true );
check( 'C2 check_dependencies = true', lsp_cert_check_dependencies(), true );

// --- C4: Core aktif tetapi metode SKKNI hilang (kelas tidak lengkap) -------
// Tidak bisa diuji di proses yang sama karena kelas sudah terdefinisi;
// dicatat sebagai batasan: kelas Core dianggap kontrak penuh.

array_map( 'unlink', glob( $stub_root . '/wp-admin/includes/plugin.php' ) );
@rmdir( $stub_root . '/wp-admin/includes' );
@rmdir( $stub_root . '/wp-admin' );
@rmdir( $stub_root );

echo $fail === 0 ? "\nSEMUA LULUS\n" : "\n{$fail} GAGAL\n";
exit( $fail === 0 ? 0 : 1 );
