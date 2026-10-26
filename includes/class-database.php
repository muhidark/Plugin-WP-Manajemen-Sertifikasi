<?php
/**
 * Database Operations
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Database {

    public static function init() {
        self::maybe_upgrade_database();
    }

    /**
     * Migrasi database yang aman & idempoten untuk menambah kolom baru jika belum ada
     */
    public static function maybe_upgrade_database() {
        global $wpdb;
        $table_kegiatan = $wpdb->prefix . 'lsp_kegiatan';

        // Pastikan tabel sudah terbentuk sebelum alter
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_kegiatan'" ) !== $table_kegiatan ) {
            return;
        }

        // Cek kolom lokasi_uji
        $col_lokasi = $wpdb->get_results( "SHOW COLUMNS FROM `$table_kegiatan` LIKE 'lokasi_uji'" );
        if ( empty( $col_lokasi ) ) {
            $wpdb->query( "ALTER TABLE `$table_kegiatan` ADD COLUMN `lokasi_uji` varchar(255) DEFAULT NULL AFTER `tuk_id`" );
        }

        // Cek kolom alamat_lokasi_uji
        $col_alamat = $wpdb->get_results( "SHOW COLUMNS FROM `$table_kegiatan` LIKE 'alamat_lokasi_uji'" );
        if ( empty( $col_alamat ) ) {
            $wpdb->query( "ALTER TABLE `$table_kegiatan` ADD COLUMN `alamat_lokasi_uji` text DEFAULT NULL AFTER `lokasi_uji`" );
        }
    }

    // ============ KEGIATAN ============

    public static function get_kegiatan( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_kegiatan';
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ) );
    }

    public static function get_all_kegiatan( $status = null ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_kegiatan';
        
        if ( $status ) {
            return $wpdb->get_results( $wpdb->prepare( 
                "SELECT * FROM $table WHERE status = %s ORDER BY tanggal_pelaksanaan DESC", 
                $status 
            ));
        }
        
        return $wpdb->get_results( "SELECT * FROM $table ORDER BY tanggal_pelaksanaan DESC" );
    }

    public static function get_kegiatan_tersedia() {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_kegiatan';
        return $wpdb->get_results( 
            "SELECT * FROM $table WHERE status = 'dibuka' AND tanggal_pelaksanaan >= CURDATE() ORDER BY tanggal_pelaksanaan ASC" 
        );
    }

    public static function insert_kegiatan( $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_kegiatan';
        
        $insert_data = array(
            'skema_id'            => $data['skema_id'],
            'paket_muk_id'        => ! empty( $data['paket_muk_id'] ) ? intval( $data['paket_muk_id'] ) : null,
            'tanggal_pelaksanaan' => $data['tanggal_pelaksanaan'],
            'tuk_id'              => $data['tuk_id'],
            'kuota'               => $data['kuota'],
            'status'              => $data['status'] ?? 'draft',
        );

        if ( isset( $data['lokasi_uji'] ) )        $insert_data['lokasi_uji']        = sanitize_text_field( $data['lokasi_uji'] );
        if ( isset( $data['alamat_lokasi_uji'] ) ) $insert_data['alamat_lokasi_uji'] = sanitize_textarea_field( $data['alamat_lokasi_uji'] );
        if ( isset( $data['asesor_ids'] ) )        $insert_data['asesor_ids']        = is_array( $data['asesor_ids'] ) ? wp_json_encode( array_values( array_filter( array_map( 'intval', $data['asesor_ids'] ) ) ) ) : ( ! empty( $data['asesor_ids'] ) ? sanitize_text_field( $data['asesor_ids'] ) : null );
        if ( isset( $data['penyusun_id'] ) )   $insert_data['penyusun_id']   = ! empty( $data['penyusun_id'] ) ? intval( $data['penyusun_id'] ) : null;
        if ( isset( $data['penyusun_nama'] ) ) $insert_data['penyusun_nama'] = sanitize_text_field( $data['penyusun_nama'] );
        if ( isset( $data['penyusun_met'] ) )  $insert_data['penyusun_met']  = sanitize_text_field( $data['penyusun_met'] );
        if ( isset( $data['validator_id'] ) )  $insert_data['validator_id']  = ! empty( $data['validator_id'] ) ? intval( $data['validator_id'] ) : null;
        if ( isset( $data['validator_nama'] ) ) $insert_data['validator_nama'] = sanitize_text_field( $data['validator_nama'] );
        if ( isset( $data['validator_met'] ) )  $insert_data['validator_met']  = sanitize_text_field( $data['validator_met'] );
        
        $wpdb->insert( $table, $insert_data );
        
        return $wpdb->insert_id;
    }

    public static function update_kegiatan( $id, $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_kegiatan';
        
        return $wpdb->update( $table, $data, array( 'id' => $id ) );
    }

    public static function delete_kegiatan( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_kegiatan';
        return $wpdb->delete( $table, array( 'id' => $id ) );
    }

    public static function get_peserta_count( $kegiatan_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_pendaftaran';
        return $wpdb->get_var( $wpdb->prepare( 
            "SELECT COUNT(*) FROM $table WHERE kegiatan_id = %d", 
            $kegiatan_id 
        ));
    }

    // ============ PENDAFTARAN ============

    public static function get_pendaftaran( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_pendaftaran';
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ) );
    }

    public static function get_all_pendaftaran( $args = array() ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_pendaftaran';
        
        $where = array();
        $values = array();
        
        if ( ! empty( $args['kegiatan_id'] ) ) {
            $where[] = "kegiatan_id = %d";
            $values[] = $args['kegiatan_id'];
        }
        
        if ( ! empty( $args['status_kelengkapan'] ) ) {
            $where[] = "status_kelengkapan = %s";
            $values[] = $args['status_kelengkapan'];
        }
        
        if ( ! empty( $args['status_rekomendasi'] ) ) {
            $where[] = "status_rekomendasi = %s";
            $values[] = $args['status_rekomendasi'];
        }
        
        $sql = "SELECT * FROM $table";
        if ( ! empty( $where ) ) {
            $sql .= " WHERE " . implode( " AND ", $where );
        }
        $sql .= " ORDER BY created_at DESC";
        
        if ( ! empty( $values ) ) {
            return $wpdb->get_results( $wpdb->prepare( $sql, ...$values ) );
        }
        
        return $wpdb->get_results( $sql );
    }

    public static function insert_pendaftaran( $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_pendaftaran';
        
        $wpdb->insert( $table, array(
            'kegiatan_id' => $data['kegiatan_id'],
            'paket_muk_id' => ! empty( $data['paket_muk_id'] ) ? intval( $data['paket_muk_id'] ) : null,
            'nama_lengkap' => $data['nama_lengkap'],
            'nik' => $data['nik'],
            'tempat_lahir' => $data['tempat_lahir'],
            'tanggal_lahir' => $data['tanggal_lahir'],
            'jenis_kelamin' => $data['jenis_kelamin'],
            'kebangsaan' => $data['kebangsaan'] ?? 'Indonesia',
            'alamat' => $data['alamat'],
            'kode_pos' => $data['kode_pos'],
            'telepon' => $data['telepon'],
            'email' => $data['email'],
            'pendidikan' => $data['pendidikan'],
            'nama_perusahaan' => $data['nama_perusahaan'] ?? null,
            'jabatan' => $data['jabatan'] ?? null,
            'alamat_kantor' => $data['alamat_kantor'] ?? null,
            'kode_pos_kantor' => $data['kode_pos_kantor'] ?? null,
            'telepon_kantor' => $data['telepon_kantor'] ?? null,
            'fax_kantor' => $data['fax_kantor'] ?? null,
            'email_kantor' => $data['email_kantor'] ?? null,
            'masa_kerja' => $data['masa_kerja'] ?? null,
            'tujuan_asesmen' => $data['tujuan_asesmen'] ?? 'sertifikasi',
            'persyaratan_dasar' => is_array( $data['persyaratan_dasar'] ?? null ) ? json_encode( $data['persyaratan_dasar'] ) : $data['persyaratan_dasar'] ?? null,
            'persyaratan_admin' => is_array( $data['persyaratan_admin'] ?? null ) ? json_encode( $data['persyaratan_admin'] ) : $data['persyaratan_admin'] ?? null,
        ));
        
        return $wpdb->insert_id;
    }

    public static function update_pendaftaran( $id, $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_pendaftaran';
        
        // Convert arrays to JSON
        if ( isset( $data['persyaratan_dasar'] ) && is_array( $data['persyaratan_dasar'] ) ) {
            $data['persyaratan_dasar'] = json_encode( $data['persyaratan_dasar'] );
        }
        if ( isset( $data['persyaratan_admin'] ) && is_array( $data['persyaratan_admin'] ) ) {
            $data['persyaratan_admin'] = json_encode( $data['persyaratan_admin'] );
        }
        
        return $wpdb->update( $table, $data, array( 'id' => $id ) );
    }

    // ============ APL 02 JAWABAN ============

    public static function get_apl02_jawaban( $pendaftaran_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_apl02_jawaban';
        return $wpdb->get_results( $wpdb->prepare( 
            "SELECT * FROM $table WHERE pendaftaran_id = %d ORDER BY unit_kompetensi_kode, elemen_nomor", 
            $pendaftaran_id 
        ));
    }

    public static function insert_apl02_jawaban( $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_apl02_jawaban';
        
        $wpdb->insert( $table, array(
            'pendaftaran_id' => $data['pendaftaran_id'],
            'unit_kompetensi_kode' => $data['unit_kompetensi_kode'],
            'unit_kompetensi_judul' => $data['unit_kompetensi_judul'],
            'elemen_nomor' => $data['elemen_nomor'],
            'elemen_nama' => $data['elemen_nama'],
            'jawaban' => $data['jawaban'],
        ));
        
        return $wpdb->insert_id;
    }

    public static function delete_apl02_jawaban( $pendaftaran_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'lsp_apl02_jawaban';
        return $wpdb->delete( $table, array( 'pendaftaran_id' => $pendaftaran_id ) );
    }

    // ============ VALIDASI & LINKING SERTIFIKAT ============

    /**
     * Cari Data Asesi Berdasarkan Nama / ID untuk Validasi Penerbitan Sertifikat
     *
     * @param string $nama Nama lengkap peserta
     * @param string $skema Judul skema sertifikasi (opsional)
     * @param int    $pendaftaran_id ID pendaftaran jika sudah diketahui
     * @return array|null
     */
    public static function find_pendaftaran_by_name( $nama = '', $skema = '', $pendaftaran_id = 0 ) {
        global $wpdb;

        $table_pendaftaran = $wpdb->prefix . 'lsp_pendaftaran';
        $table_kegiatan    = $wpdb->prefix . 'lsp_kegiatan';

        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_pendaftaran'" ) !== $table_pendaftaran ) {
            return null;
        }

        // 1. Jika dicari spesifik dengan ID pendaftaran
        if ( intval( $pendaftaran_id ) > 0 ) {
            $sql = "SELECT p.*, k.skema_id, k.tanggal_pelaksanaan, s.post_title AS skema_title 
                    FROM {$table_pendaftaran} p
                    LEFT JOIN {$table_kegiatan} k ON p.kegiatan_id = k.id
                    LEFT JOIN {$wpdb->posts} s ON k.skema_id = s.ID
                    WHERE p.id = %d LIMIT 1";
            $row = $wpdb->get_row( $wpdb->prepare( $sql, intval( $pendaftaran_id ) ), ARRAY_A );
            return $row ?: null;
        }

        $nama_raw = trim( (string) $nama );
        if ( '' === $nama_raw ) {
            return null;
        }

        // Pembersihan gelar akademis depan & belakang
        $clean_gelar = preg_replace( '/\b(S\.Kom|S\.T|S\.E|S\.Pd|S\.Sos|S\.Si|S\.H|S\.Ked|M\.M|M\.Kom|M\.T|M\.Pd|M\.Si|Dr|Dra|Drs|Ir|Prof)\b\.?/i', '', $nama_raw );
        $nama_clean  = trim( preg_replace( '/[,\s\.]+$/', '', trim( $clean_gelar ) ) );

        $sql = "SELECT p.*, k.skema_id, k.tanggal_pelaksanaan, s.post_title AS skema_title 
                FROM {$table_pendaftaran} p
                LEFT JOIN {$table_kegiatan} k ON p.kegiatan_id = k.id
                LEFT JOIN {$wpdb->posts} s ON k.skema_id = s.ID
                WHERE LOWER(TRIM(p.nama_lengkap)) = LOWER(%s)
                   OR LOWER(TRIM(p.nama_lengkap)) = LOWER(%s)";

        $rows = $wpdb->get_results( $wpdb->prepare( $sql, $nama_raw, $nama_clean ), ARRAY_A );
        if ( empty( $rows ) ) {
            return null;
        }

        if ( 1 === count( $rows ) ) {
            return $rows[0];
        }

        // Jika terdapat peserta kembar, cocokkan dengan judul skema
        if ( ! empty( $skema ) ) {
            $skema_lower = strtolower( trim( $skema ) );
            foreach ( $rows as $row ) {
                if ( ! empty( $row['skema_title'] ) && strtolower( trim( $row['skema_title'] ) ) === $skema_lower ) {
                    return $row;
                }
            }
        }

        return $rows[0];
    }

    /**
     * Dapatkan data sertifikat (CPT data_sertifikat) yang terhubung dengan pendaftaran
     *
     * @param int    $pendaftaran_id
     * @param string $nama_lengkap
     * @return array|null
     */
    public static function get_sertifikat_by_pendaftaran( $pendaftaran_id, $nama_lengkap = '' ) {
        // Cari via post_meta 'pendaftaran_id'
        $certs = get_posts( array(
            'post_type'      => 'data_sertifikat',
            'post_status'    => array( 'publish', 'draft' ),
            'posts_per_page' => 1,
            'meta_query'     => array(
                array(
                    'key'     => 'pendaftaran_id',
                    'value'   => intval( $pendaftaran_id ),
                    'compare' => '=',
                ),
            ),
        ) );

        // Fallback: cari via nama pemegang jika belum tertaut meta pendaftaran_id
        if ( empty( $certs ) && ! empty( $nama_lengkap ) ) {
            $certs = get_posts( array(
                'post_type'      => 'data_sertifikat',
                'post_status'    => array( 'publish', 'draft' ),
                'posts_per_page' => 1,
                'meta_query'     => array(
                    array(
                        'key'     => 'nama_pemegang',
                        'value'   => trim( $nama_lengkap ),
                        'compare' => '=',
                    ),
                ),
            ) );
        }

        if ( empty( $certs ) ) {
            return null;
        }

        $c = $certs[0];
        $blanko = get_post_meta( $c->ID, 'kode_blanko', true );
        if ( empty( $blanko ) ) {
            $blanko = $c->post_title;
        }

        return array(
            'post_id'        => $c->ID,
            'status'         => $c->post_status,
            'kode_blanko'    => $blanko,
            'no_registrasi'  => get_post_meta( $c->ID, 'no_registrasi', true ),
            'nama_pemegang'  => get_post_meta( $c->ID, 'nama_pemegang', true ),
            'skema'          => get_post_meta( $c->ID, 'skema_sertifikasi', true ),
            'tanggal_terbit' => get_post_meta( $c->ID, 'tanggal_terbit', true ),
            'edit_url'       => get_edit_post_link( $c->ID ),
        );
    }
}
