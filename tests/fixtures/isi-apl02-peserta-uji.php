<?php
/**
 * Isi FR.APL.02 peserta uji STG-01 (#88) dengan jawaban K untuk semua elemen.
 *
 * Dipakai sekali agar dokumen uji STG-02 bisa dicetak. Bukan bagian dari alur
 * produksi: pada kegiatan nyata, jawaban diisi oleh asesi lewat formulir.
 *
 * Jalankan: php tests/fixtures/isi-apl02-peserta-uji.php [pendaftaran_id]
 */

$_SERVER['HTTP_HOST']      = 'localhost';
$_SERVER['REQUEST_URI']    = '/wordpress/';
$_SERVER['SERVER_NAME']    = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';

require dirname( __DIR__, 5 ) . '/wp-load.php';

$pid = isset( $argv[1] ) ? (int) $argv[1] : (int) get_option( 'lsp_stg01_peserta', 88 );
$pendaftaran = LSP_Cert_Database::get_pendaftaran( $pid );
if ( ! $pendaftaran ) {
    fwrite( STDERR, "Pendaftaran #{$pid} tidak ditemukan\n" );
    exit( 1 );
}

$kegiatan = LSP_Cert_Database::get_kegiatan( $pendaftaran->kegiatan_id );
$skema_id = (int) $kegiatan->skema_id;

LSP_Cert_Database::delete_apl02_jawaban( $pid );

$jumlah = 0;
foreach ( LSP_Cert_Integration::get_unit_kompetensi( $skema_id ) as $unit ) {
    $kode = trim( (string) ( $unit['kode'] ?? '' ) );
    if ( '' === $kode ) { continue; }

    foreach ( (array) ( $unit['elemen'] ?? array() ) as $idx => $e ) {
        LSP_Cert_Database::insert_apl02_jawaban( array(
            'pendaftaran_id'        => $pid,
            'unit_kompetensi_kode'  => $kode,
            'unit_kompetensi_judul' => (string) ( $unit['judul'] ?? '' ),
            'elemen_nomor'          => isset( $e['nomor'] ) ? (int) $e['nomor'] : ( $idx + 1 ),
            'elemen_nama'           => (string) ( $e['nama'] ?? '' ),
            'jawaban'               => 'K',
        ) );
        $jumlah++;
    }
}

$kep = LSP_Cert_Integration::get_apl02_kelengkapan( $pid );
printf(
    "Peserta #%d (%s): %d jawaban ditulis, kelengkapan %d/%d, BK %d, lengkap=%s\n",
    $pid,
    $pendaftaran->nama_lengkap,
    $jumlah,
    (int) $kep['terjawab'],
    (int) $kep['total'],
    (int) $kep['bk'],
    $kep['lengkap'] ? 'ya' : 'tidak'
);
