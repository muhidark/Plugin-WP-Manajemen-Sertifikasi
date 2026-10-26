<?php
/**
 * Form Handler for Public Forms
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Form_Handler {

    public static function init() {
        add_action( 'wp_ajax_lsp_cert_submit_pendaftaran', array( __CLASS__, 'submit_pendaftaran' ) );
        add_action( 'wp_ajax_nopriv_lsp_cert_submit_pendaftaran', array( __CLASS__, 'submit_pendaftaran' ) );
    }

    public static function submit_pendaftaran() {
        check_ajax_referer( 'lsp_cert_public_nonce', 'nonce' );
        
        // Validate required fields
        $required_fields = array(
            'kegiatan_id', 'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir',
            'jenis_kelamin', 'alamat', 'kode_pos', 'telepon', 'email', 'pendidikan'
        );
        
        foreach ( $required_fields as $field ) {
            if ( empty( $_POST[$field] ) ) {
                wp_send_json_error( array( 'message' => "Field $field wajib diisi" ) );
            }
        }
        
        // Validate email
        if ( ! is_email( $_POST['email'] ) ) {
            wp_send_json_error( array( 'message' => 'Format email tidak valid' ) );
        }
        
        // Check kuota
        $kegiatan_id = intval( $_POST['kegiatan_id'] );
        $kegiatan = LSP_Cert_Database::get_kegiatan( $kegiatan_id );
        
        if ( ! $kegiatan ) {
            wp_send_json_error( array( 'message' => 'Kegiatan tidak ditemukan' ) );
        }
        
        $peserta_count = LSP_Cert_Database::get_peserta_count( $kegiatan_id );
        if ( $peserta_count >= $kegiatan->kuota ) {
            wp_send_json_error( array( 'message' => 'Kuota kegiatan sudah penuh' ) );
        }
        
        // Prepare data
        $data = array(
            'kegiatan_id' => $kegiatan_id,
            'nama_lengkap' => sanitize_text_field( $_POST['nama_lengkap'] ),
            'nik' => sanitize_text_field( $_POST['nik'] ),
            'tempat_lahir' => sanitize_text_field( $_POST['tempat_lahir'] ),
            'tanggal_lahir' => sanitize_text_field( $_POST['tanggal_lahir'] ),
            'jenis_kelamin' => sanitize_text_field( $_POST['jenis_kelamin'] ),
            'kebangsaan' => sanitize_text_field( $_POST['kebangsaan'] ?? 'Indonesia' ),
            'alamat' => sanitize_textarea_field( $_POST['alamat'] ),
            'kode_pos' => sanitize_text_field( $_POST['kode_pos'] ),
            'telepon' => sanitize_text_field( $_POST['telepon'] ),
            'email' => sanitize_email( $_POST['email'] ),
            'pendidikan' => sanitize_text_field( $_POST['pendidikan'] ),
            'nama_perusahaan' => sanitize_text_field( $_POST['nama_perusahaan'] ?? '' ),
            'jabatan' => sanitize_text_field( $_POST['jabatan'] ?? '' ),
            'alamat_kantor' => sanitize_textarea_field( $_POST['alamat_kantor'] ?? '' ),
            'kode_pos_kantor' => sanitize_text_field( $_POST['kode_pos_kantor'] ?? '' ),
            'telepon_kantor' => sanitize_text_field( $_POST['telepon_kantor'] ?? '' ),
            'fax_kantor' => sanitize_text_field( $_POST['fax_kantor'] ?? '' ),
            'email_kantor' => sanitize_email( $_POST['email_kantor'] ?? '' ),
            'masa_kerja' => sanitize_text_field( $_POST['masa_kerja'] ?? '' ),
            'tujuan_asesmen' => sanitize_text_field( $_POST['tujuan_asesmen'] ?? 'sertifikasi' ),
            'persyaratan_dasar' => isset( $_POST['persyaratan_dasar'] ) ? array_map( 'sanitize_text_field', (array) $_POST['persyaratan_dasar'] ) : array(),
            'persyaratan_admin' => isset( $_POST['persyaratan_admin'] ) ? array_map( 'sanitize_text_field', (array) $_POST['persyaratan_admin'] ) : array(),
        );
        
        // Insert pendaftaran
        $pendaftaran_id = LSP_Cert_Database::insert_pendaftaran( $data );
        
        if ( ! $pendaftaran_id ) {
            wp_send_json_error( array( 'message' => 'Gagal menyimpan data pendaftaran' ) );
        }
        
        // Save APL 02 answers (supports both apl02 structured array and legacy apl02_jawaban)
        if ( isset( $_POST['apl02'] ) && is_array( $_POST['apl02'] ) ) {
            foreach ( $_POST['apl02'] as $item ) {
                if ( ! empty( $item['jawaban'] ) ) {
                    LSP_Cert_Database::insert_apl02_jawaban( array(
                        'pendaftaran_id' => $pendaftaran_id,
                        'unit_kompetensi_kode' => sanitize_text_field( $item['kode'] ?? '' ),
                        'unit_kompetensi_judul' => sanitize_text_field( $item['judul'] ?? '' ),
                        'elemen_nomor' => intval( $item['elemen_nomor'] ?? 1 ),
                        'elemen_nama' => sanitize_text_field( $item['elemen_nama'] ?? '' ),
                        'jawaban' => sanitize_text_field( $item['jawaban'] ),
                    ));
                }
            }
        } elseif ( isset( $_POST['apl02_jawaban'] ) && is_array( $_POST['apl02_jawaban'] ) ) {
            foreach ( $_POST['apl02_jawaban'] as $jawaban ) {
                LSP_Cert_Database::insert_apl02_jawaban( array(
                    'pendaftaran_id' => $pendaftaran_id,
                    'unit_kompetensi_kode' => sanitize_text_field( $jawaban['kode'] ?? '' ),
                    'unit_kompetensi_judul' => sanitize_text_field( $jawaban['judul'] ?? '' ),
                    'elemen_nomor' => intval( $jawaban['elemen_nomor'] ?? 1 ),
                    'elemen_nama' => sanitize_text_field( $jawaban['elemen_nama'] ?? '' ),
                    'jawaban' => sanitize_text_field( $jawaban['jawaban'] ?? 'K' ),
                ));
            }
        }
        
        wp_send_json_success( array(
            'message' => 'Pendaftaran berhasil disimpan',
            'pendaftaran_id' => $pendaftaran_id,
        ));
    }
}
