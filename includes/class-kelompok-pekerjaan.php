<?php
/**
 * Pengelompokan Unit Kompetensi Berdasarkan Kelompok Pekerjaan
 * Untuk Formulir FR.IA.01, FR.IA.02, dan FR.IA.03
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LSP_Cert_Kelompok_Pekerjaan {

    /**
     * Inisialisasi hook
     */
    public static function init() {
        if ( is_admin() ) {
            add_action( 'admin_init', array( __CLASS__, 'handle_save_action' ) );
        }
    }

    /**
     * Ambil daftar kelompok pekerjaan untuk skema tertentu (atau paket MUK tertentu)
     *
     * @param int $skema_id
     * @param int $paket_muk_id
     * @return array
     */
    public static function get_kelompok( $skema_id, $paket_muk_id = 0 ) {
        $skema_id = intval( $skema_id );
        if ( $skema_id <= 0 ) {
            return array();
        }

        // Cek jika spesifik paket MUK diminta
        if ( $paket_muk_id > 0 && class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            $paket = LSP_Cert_Paket_MUK::get_paket( $paket_muk_id );
            if ( $paket && ! empty( $paket->kelompok_pekerjaan ) && is_array( $paket->kelompok_pekerjaan ) ) {
                $saved = $paket->kelompok_pekerjaan;
                usort( $saved, function( $a, $b ) {
                    return intval( $a['urutan'] ?? 0 ) <=> intval( $b['urutan'] ?? 0 );
                } );
                return $saved;
            }
        } elseif ( class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            // Cek paket default untuk skema ini
            $def_paket = LSP_Cert_Paket_MUK::get_default_paket( $skema_id );
            if ( $def_paket && ! empty( $def_paket->kelompok_pekerjaan ) && is_array( $def_paket->kelompok_pekerjaan ) ) {
                $saved = $def_paket->kelompok_pekerjaan;
                usort( $saved, function( $a, $b ) {
                    return intval( $a['urutan'] ?? 0 ) <=> intval( $b['urutan'] ?? 0 );
                } );
                return $saved;
            }
        }

        $option_key = 'lsp_cert_kelompok_pekerjaan_skema_' . $skema_id;
        $saved      = get_option( $option_key, null );

        if ( ! is_array( $saved ) ) {
            return array();
        }

        // Pastikan terurut berdasarkan urutan
        usort( $saved, function( $a, $b ) {
            $ord_a = isset( $a['urutan'] ) ? intval( $a['urutan'] ) : 0;
            $ord_b = isset( $b['urutan'] ) ? intval( $b['urutan'] ) : 0;
            return $ord_a <=> $ord_b;
        } );

        return $saved;
    }

    /**
     * Simpan konfigurasi kelompok pekerjaan untuk skema tertentu (atau paket MUK)
     *
     * @param int   $skema_id
     * @param array $kelompok_list
     * @param int   $paket_muk_id
     * @return bool
     */
    public static function save_kelompok( $skema_id, $kelompok_list, $paket_muk_id = 0 ) {
        $skema_id = intval( $skema_id );
        if ( $skema_id <= 0 ) {
            return false;
        }

        $clean_list = array();
        if ( is_array( $kelompok_list ) ) {
            $group_counter = 1;
            foreach ( $kelompok_list as $grp ) {
                $nama = isset( $grp['nama'] ) ? sanitize_text_field( $grp['nama'] ) : '';
                if ( empty( $nama ) ) {
                    continue;
                }

                $id = ! empty( $grp['id'] ) ? sanitize_key( $grp['id'] ) : ( 'kp_' . $skema_id . '_' . $group_counter );
                $urutan = isset( $grp['urutan'] ) ? intval( $grp['urutan'] ) : $group_counter;

                // Sanitasi daftar unit anggota kelompok
                $clean_units = array();
                if ( ! empty( $grp['units'] ) && is_array( $grp['units'] ) ) {
                    foreach ( $grp['units'] as $u_code ) {
                        $u_code = sanitize_text_field( $u_code );
                        if ( ! empty( $u_code ) && ! in_array( $u_code, $clean_units, true ) ) {
                            $clean_units[] = $u_code;
                        }
                    }
                }

                $clean_list[] = array(
                    'id'     => $id,
                    'nama'   => $nama,
                    'urutan' => $urutan,
                    'units'  => $clean_units,
                );

                $group_counter++;
            }
        }

        // Urutkan kembali berdasarkan urutan sebelum disimpan
        usort( $clean_list, function( $a, $b ) {
            return intval( $a['urutan'] ?? 0 ) <=> intval( $b['urutan'] ?? 0 );
        } );

        if ( $paket_muk_id > 0 && class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            global $wpdb;
            $table = $wpdb->prefix . 'lsp_paket_muk';
            $wpdb->update( $table, array( 'kelompok_pekerjaan' => wp_json_encode( $clean_list ) ), array( 'id' => $paket_muk_id ) );
        } elseif ( class_exists( 'LSP_Cert_Paket_MUK' ) ) {
            $def_p = LSP_Cert_Paket_MUK::get_default_paket( $skema_id );
            if ( $def_p ) {
                global $wpdb;
                $table = $wpdb->prefix . 'lsp_paket_muk';
                $wpdb->update( $table, array( 'kelompok_pekerjaan' => wp_json_encode( $clean_list ) ), array( 'id' => $def_p->id ) );
            }
        }

        $option_key = 'lsp_cert_kelompok_pekerjaan_skema_' . $skema_id;
        return update_option( $option_key, $clean_list );
    }

    /**
     * Validasi cakupan unit kompetensi terhadap Master Skema
     * Menghitung total master, unit terkelompok unik, unit belum terkelompok, dan persentase cakupan
     *
     * @param int $skema_id
     * @param int $paket_muk_id
     * @return array
     */
    public static function validate_coverage( $skema_id, $paket_muk_id = 0 ) {
        $skema_id     = intval( $skema_id );
        $master_units = LSP_Cert_Integration::get_unit_kompetensi( $skema_id );
        $kelompok     = self::get_kelompok( $skema_id, $paket_muk_id );

        // Map seluruh unit master: kode => judul
        $master_map = array();
        if ( is_array( $master_units ) ) {
            foreach ( $master_units as $u ) {
                $kode  = is_object( $u ) ? ( $u->kode_unit ?? ( $u->kode ?? '' ) ) : ( $u['kode'] ?? ( $u['kode_unit'] ?? '' ) );
                $judul = is_object( $u ) ? ( $u->judul_unit ?? ( $u->judul ?? ( $u->title ?? '' ) ) ) : ( $u['judul'] ?? ( $u['judul_unit'] ?? ( $u['title'] ?? '' ) ) );
                if ( ! empty( $kode ) ) {
                    $master_map[ $kode ] = $judul;
                }
            }
        }

        $total_master = count( $master_map );

        // Kumpulkan seluruh unit yang dimasukkan ke kelompok pekerjaan
        $assigned_codes = array();
        foreach ( $kelompok as $grp ) {
            if ( ! empty( $grp['units'] ) && is_array( $grp['units'] ) ) {
                foreach ( $grp['units'] as $u_code ) {
                    if ( isset( $master_map[ $u_code ] ) ) {
                        $assigned_codes[] = $u_code;
                    }
                }
            }
        }

        // Unit yang muncul pada beberapa kelompok hanya dihitung sekali untuk persentase cakupan
        $unique_assigned = array_unique( $assigned_codes );
        $total_grouped_unique = count( $unique_assigned );

        // Identifikasi unit yang belum dikelompokkan
        $unassigned_units = array();
        foreach ( $master_map as $kode => $judul ) {
            if ( ! in_array( $kode, $unique_assigned, true ) ) {
                $unassigned_units[] = array(
                    'kode'  => $kode,
                    'judul' => $judul,
                );
            }
        }

        $total_unassigned = count( $unassigned_units );
        $coverage_percent = $total_master > 0 ? round( ( $total_grouped_unique / $total_master ) * 100, 1 ) : 0;
        $is_complete      = ( $total_master > 0 && $total_unassigned === 0 );

        return array(
            'skema_id'             => $skema_id,
            'total_master'         => $total_master,
            'total_grouped_unique' => $total_grouped_unique,
            'total_unassigned'     => $total_unassigned,
            'coverage_percent'     => $coverage_percent,
            'is_complete'          => $is_complete,
            'unassigned_units'     => $unassigned_units,
            'unique_assigned'      => array_values( $unique_assigned ),
            'master_map'           => $master_map,
        );
    }

    /**
     * Ambil data unit terkelompok siap cetak untuk formulir FR.IA.01, FR.IA.02, dan FR.IA.03
     * Menghasilkan struktur kelompok dengan rowspan dan detail unit master
     *
     * @param int $skema_id
     * @param int $paket_muk_id
     * @return array
     */
    public static function get_grouped_units_for_print( $skema_id, $paket_muk_id = 0 ) {
        $skema_id  = intval( $skema_id );
        $kelompok  = self::get_kelompok( $skema_id, $paket_muk_id );
        $coverage  = self::validate_coverage( $skema_id, $paket_muk_id );
        $master_map = $coverage['master_map'];

        $print_groups = array();
        $global_row_no = 1;

        foreach ( $kelompok as $grp ) {
            $group_units = array();
            if ( ! empty( $grp['units'] ) && is_array( $grp['units'] ) ) {
                foreach ( $grp['units'] as $u_code ) {
                    if ( isset( $master_map[ $u_code ] ) ) {
                        $group_units[] = array(
                            'no'         => $global_row_no++,
                            'kode_unit'  => $u_code,
                            'judul_unit' => $master_map[ $u_code ],
                        );
                    }
                }
            }

            if ( ! empty( $group_units ) ) {
                $print_groups[] = array(
                    'id'      => $grp['id'],
                    'nama'    => $grp['nama'],
                    'urutan'  => $grp['urutan'],
                    'rowspan' => count( $group_units ),
                    'units'   => $group_units,
                );
            }
        }

        return array(
            'skema_id'     => $skema_id,
            'groups'       => $print_groups,
            'total_rows'   => $global_row_no - 1,
            'coverage'     => $coverage,
            'is_complete'  => $coverage['is_complete'],
        );
    }

    /**
     * Helper merender tabel unit kompetensi dengan Kelompok Pekerjaan sebagai kolom pertama
     * Digunakan secara seragam oleh FR.IA.01, FR.IA.02, dan FR.IA.03
     *
     * @param int  $skema_id
     * @param bool $echo
     * @param int  $paket_muk_id
     * @return string
     */
    public static function render_unit_table_html( $skema_id, $echo = true, $paket_muk_id = 0 ) {
        $data = self::get_grouped_units_for_print( $skema_id, $paket_muk_id );
        $groups = $data['groups'];
        $coverage = $data['coverage'];

        ob_start();
        ?>
        <?php if ( ! $coverage['is_complete'] && $coverage['total_unassigned'] > 0 ) : ?>
            <div class="lsp-warning-box lsp-no-print" style="background: #fffbeb; border: 1px solid #f59e0b; color: #b45309; padding: 10px 14px; border-radius: 4px; margin-bottom: 12px; font-size: 8.5pt;">
                <strong>⚠️ Peringatan Cakupan:</strong> Masih terdapat <strong><?php echo $coverage['total_unassigned']; ?></strong> unit kompetensi dari Master Skema yang belum masuk ke dalam kelompok pekerjaan (Cakupan: <?php echo $coverage['coverage_percent']; ?>%).
            </div>
        <?php endif; ?>

        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
            <thead>
                <tr style="background-color: #f1f5f9; text-align: center;">
                    <th style="width: 32%; padding: 6px 8px; font-size: 9pt;">Kelompok Pekerjaan</th>
                    <th style="width: 6%; padding: 6px 4px; font-size: 9pt;">No.</th>
                    <th style="width: 24%; padding: 6px 8px; font-size: 9pt;">Kode Unit</th>
                    <th style="width: 38%; padding: 6px 10px; font-size: 9pt; text-align: left;">Judul Unit</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $groups ) ) : ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 14px; font-style: italic; color: #64748b; font-size: 9pt;">
                            Kelompok pekerjaan belum diatur untuk skema ini. Silakan atur melalui menu <em>Sertifikasi &gt; Kelompok Pekerjaan</em>.
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ( $groups as $grp ) : 
                        $first = true;
                        $rowspan = $grp['rowspan'];
                        foreach ( $grp['units'] as $u ) :
                    ?>
                        <tr>
                            <?php if ( $first ) : ?>
                                <td rowspan="<?php echo $rowspan; ?>" style="vertical-align: middle; padding: 8px 10px; font-size: 9pt; font-weight: bold; background: #fafafa;">
                                    <?php echo esc_html( $grp['nama'] ); ?>
                                </td>
                                <?php $first = false; ?>
                            <?php endif; ?>
                            <td style="text-align: center; vertical-align: middle; padding: 5px 4px; font-size: 9pt;">
                                <?php echo $u['no']; ?>
                            </td>
                            <td style="font-family: monospace; font-size: 9pt; padding: 5px 8px; vertical-align: middle; text-align: center;">
                                <?php echo esc_html( $u['kode_unit'] ); ?>
                            </td>
                            <td style="padding: 5px 10px; font-size: 9pt; vertical-align: middle;">
                                <?php echo esc_html( $u['judul_unit'] ); ?>
                            </td>
                        </tr>
                    <?php endforeach; endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
        $html = ob_get_clean();

        if ( $echo ) {
            echo $html;
        }
        return $html;
    }

    /**
     * Handle proses penyimpanan dari form admin
     */
    public static function handle_save_action() {
        if ( ! isset( $_POST['action'] ) || 'lsp_save_kelompok_pekerjaan' !== $_POST['action'] ) {
            return;
        }

        if ( ! current_user_can( 'lsp_cert_manage_instruments' ) ) {
            wp_die( 'Akses ditolak: Anda tidak memiliki izin untuk menyimpan konfigurasi kelompok pekerjaan.', 'Akses Ditolak', array( 'response' => 403 ) );
        }

        check_admin_referer( 'lsp_kelompok_save_nonce', 'lsp_kelompok_nonce' );

        $skema_id = isset( $_POST['skema_id'] ) ? intval( $_POST['skema_id'] ) : 0;
        if ( $skema_id <= 0 ) {
            wp_redirect( add_query_arg( array(
                'page'  => 'lsp-kelompok-pekerjaan',
                'error' => 'invalid_skema',
            ), admin_url( 'admin.php' ) ) );
            exit;
        }

        $paket_muk_id = isset( $_POST['paket_muk_id'] ) ? intval( $_POST['paket_muk_id'] ) : 0;
        $raw_groups   = isset( $_POST['kelompok'] ) && is_array( $_POST['kelompok'] ) ? wp_unslash( $_POST['kelompok'] ) : array();

        self::save_kelompok( $skema_id, $raw_groups, $paket_muk_id );

        $redirect_args = array(
            'page'     => 'lsp-kelompok-pekerjaan',
            'skema_id' => $skema_id,
            'saved'    => '1',
        );
        if ( $paket_muk_id ) {
            $redirect_args['paket_muk_id'] = $paket_muk_id;
        }

        wp_redirect( add_query_arg( $redirect_args, admin_url( 'admin.php' ) ) );
        exit;
    }

    /**
     * Render halaman admin Kelompok Pekerjaan
     */
    public static function admin_page() {
        if ( ! current_user_can( 'lsp_cert_manage_instruments' ) ) {
            wp_die( 'Anda tidak memiliki hak akses ke halaman ini.' );
        }

        $all_skema = LSP_Cert_Integration::get_all_skema();
        $selected_skema_id = isset( $_GET['skema_id'] ) ? intval( $_GET['skema_id'] ) : 0;

        // Default skema pertama jika belum dipilih
        if ( ! $selected_skema_id && ! empty( $all_skema ) ) {
            $selected_skema_id = $all_skema[0]['id'];
        }

        $paket_muk_id = isset( $_GET['paket_muk_id'] ) ? intval( $_GET['paket_muk_id'] ) : 0;

        $skema_data   = $selected_skema_id ? LSP_Cert_Integration::get_skema( $selected_skema_id ) : null;
        $master_units = $selected_skema_id ? LSP_Cert_Integration::get_unit_kompetensi( $selected_skema_id ) : array();
        $kelompok     = self::get_kelompok( $selected_skema_id, $paket_muk_id );
        $coverage     = self::validate_coverage( $selected_skema_id, $paket_muk_id );

        include LSP_CERT_PLUGIN_DIR . 'admin/views/kelompok-pekerjaan-settings.php';
    }
}
