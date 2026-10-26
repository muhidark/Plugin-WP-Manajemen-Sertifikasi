<?php
/**
 * Worker pendaftaran untuk uji konkurensi PH-07.
 *
 * Dijalankan sebagai proses terpisah oleh tests/ph07-concurrency.php:
 *   php tests/ph07-worker.php <kegiatan_id> <nik> <email> [nama]
 *
 * Mencetak JSON hasil LSP_Cert_Database::daftar_peserta().
 */

$_SERVER['HTTP_HOST']      = 'localhost';
$_SERVER['REQUEST_URI']    = '/wordpress/';
$_SERVER['SERVER_NAME']    = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';

require dirname( __DIR__, 4 ) . '/wp-load.php';

$kegiatan_id = isset( $argv[1] ) ? (int) $argv[1] : 0;
$nik         = isset( $argv[2] ) ? (string) $argv[2] : '';
$email       = isset( $argv[3] ) ? (string) $argv[3] : '';
$nama        = isset( $argv[4] ) ? (string) $argv[4] : 'Peserta Worker';

$data = array(
    'kegiatan_id'   => $kegiatan_id,
    'nama_lengkap'  => $nama,
    'nik'           => $nik,
    'tempat_lahir'  => 'Bandung',
    'tanggal_lahir' => '1995-05-05',
    'jenis_kelamin' => 'Laki-laki',
    'alamat'        => 'Jalan Uji Konkurensi',
    'kode_pos'      => '40111',
    'telepon'       => '081234567890',
    'email'         => $email,
    'pendidikan'    => 'S1',
);

$hasil = LSP_Cert_Database::daftar_peserta( $kegiatan_id, $data );
echo wp_json_encode( $hasil );
