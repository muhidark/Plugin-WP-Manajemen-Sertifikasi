<?php
/**
 * Template FR.AK.05 — Laporan Asesmen
 * Standar BNSP & Dokumen Resmi LSP Komunikasi Indonesia
 * Mendukung pembagian maksimal 10 peserta per halaman dengan nomor urut berlanjut.
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;

// Tanggal pelaksanaan kegiatan (mengikuti jadwal kegiatan, bukan H-2)
$tanggal_raw = ! empty( $kegiatan->tanggal_pelaksanaan ) ? $kegiatan->tanggal_pelaksanaan : '';
$tgl_pelaksanaan = ! empty( $tanggal_raw ) ? date_i18n( 'd F Y', strtotime( $tanggal_raw ) ) : '';

// Kebijakan TUK (Phase AK-01C):
// ☑ Sewaktu  ☐ Tempat Kerja  ☐ Mandiri*
$is_sewaktu      = true;
$is_tempat_kerja = false;
$is_mandiri      = false;

// Ambil seluruh pendaftaran yang valid (tidak ditolak/batal) untuk kegiatan ini
$kegiatan_id = ! empty( $kegiatan->id ) ? (int) $kegiatan->id : 0;
$table_pendaftaran = $wpdb->prefix . 'lsp_pendaftaran';

$all_peserta = array();
if ( ! empty( $peserta_kegiatan_override ) && is_array( $peserta_kegiatan_override ) ) {
    $all_peserta = $peserta_kegiatan_override;
} elseif ( $kegiatan_id ) {
    $all_peserta = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$table_pendaftaran}
         WHERE kegiatan_id = %d
           AND (status_rekomendasi IS NULL OR status_rekomendasi != 'ditolak')
           AND (status_kelengkapan IS NULL OR status_kelengkapan != 'batal')
         ORDER BY id ASC",
        $kegiatan_id
    ) );
}

// Jika dibuka dari konteks pendaftaran individual tunggal yang tidak melalui kegiatan_id query
if ( empty( $all_peserta ) && ! empty( $pendaftaran ) ) {
    $all_peserta = array( $pendaftaran );
}

// Analisis Penugasan Asesor pada Peserta
$asesor_groups = array(); // [ asesor_id => array of peserta ]
$unassigned_peserta = array();

foreach ( $all_peserta as $p ) {
    $a_id = ! empty( $p->asesor_id ) ? (int) $p->asesor_id : 0;
    if ( $a_id ) {
        if ( ! isset( $asesor_groups[ $a_id ] ) ) {
            $asesor_groups[ $a_id ] = array();
        }
        $asesor_groups[ $a_id ][] = $p;
    } else {
        $unassigned_peserta[] = $p;
    }
}

// Tentukan Asesor Aktif untuk Laporan Ini
$active_asesor_id = 0;
if ( isset( $_GET['asesor_id'] ) && intval( $_GET['asesor_id'] ) > 0 ) {
    $active_asesor_id = intval( $_GET['asesor_id'] );
} elseif ( ! empty( $pendaftaran->asesor_id ) ) {
    $active_asesor_id = (int) $pendaftaran->asesor_id;
} elseif ( count( $asesor_groups ) === 1 ) {
    $group_ids = array_keys( $asesor_groups );
    $active_asesor_id = $group_ids[0];
} elseif ( current_user_can( 'lsp_cert_print_docs' ) && ! current_user_can( 'manage_options' ) ) {
    $active_asesor_id = get_current_user_id();
} elseif ( ! empty( $asesor['id'] ) ) {
    $active_asesor_id = (int) $asesor['id'];
}

// Filter Peserta Sesuai Asesor Aktif
$peserta_terpilih = array();
$nama_asesor = '';
$no_reg_met  = '';

if ( $active_asesor_id && isset( $asesor_groups[ $active_asesor_id ] ) ) {
    $peserta_terpilih = $asesor_groups[ $active_asesor_id ];
    $asesor_info = LSP_Cert_Integration::get_asesor( $active_asesor_id );
    if ( $asesor_info ) {
        $nama_asesor = $asesor_info['name'] ?? '';
        $no_reg_met  = $asesor_info['no_reg'] ?? '';
    }
} elseif ( $active_asesor_id ) {
    // Asesor ditunjuk tapi tidak ada peserta yang terpetakan kepadanya
    $asesor_info = LSP_Cert_Integration::get_asesor( $active_asesor_id );
    if ( $asesor_info ) {
        $nama_asesor = $asesor_info['name'] ?? '';
        $no_reg_met  = $asesor_info['no_reg'] ?? '';
    }
    $peserta_terpilih = array();
} else {
    // Bila hanya ada 1 kelompok asesor atau belum ada penugasan terpisah
    if ( ! empty( $asesor['name'] ) ) {
        $nama_asesor = $asesor['name'];
        $no_reg_met  = $asesor['no_reg'] ?? '';
    }
    $peserta_terpilih = ! empty( $all_peserta ) ? $all_peserta : array();
}

// Pembagian Peserta: MAKSIMAL 10 PESERTA PER HALAMAN
$total_peserta = count( $peserta_terpilih );
$chunks = array();
if ( $total_peserta > 0 ) {
    $chunks = array_chunk( $peserta_terpilih, 10 );
} else {
    // Jika tidak ada peserta, hasilkan 1 halaman kosong (10 baris kosong)
    $chunks = array( array() );
}
$total_halaman = count( $chunks );
?>

<!-- Panel Pilihan Asesor untuk Admin jika terdapat multi-asesor -->
<?php if ( count( $asesor_groups ) > 1 ) : ?>
    <div class="lsp-no-print" style="width: 210mm; margin: 10px auto; background: #f0f9ff; border: 1px solid #bae6fd; padding: 10px 15px; border-radius: 4px; font-size: 12px;">
        <strong style="color: #0369a1;">Pilih Asesor untuk Mencetak Laporan FR.AK.05:</strong>
        <div style="margin-top: 6px; display: flex; gap: 8px; flex-wrap: wrap;">
            <?php foreach ( $asesor_groups as $a_id => $group_p ) : 
                $a_info = LSP_Cert_Integration::get_asesor( $a_id );
                $a_label = ( $a_info ? $a_info['name'] : 'Asesor #' . $a_id ) . ' (' . count( $group_p ) . ' Peserta)';
                $btn_class = ( $a_id === $active_asesor_id ) ? 'button-primary' : 'button';
                $url_params = $_GET;
                $url_params['asesor_id'] = $a_id;
                $switch_url = add_query_arg( $url_params );
            ?>
                <a href="<?php echo esc_url( $switch_url ); ?>" class="button button-small <?php echo $btn_class; ?>">
                    <?php echo esc_html( $a_label ); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php if ( ! empty( $unassigned_peserta ) ) : ?>
            <div style="margin-top: 6px; color: #b45309;">
                ⚠️ Perhatian: Terdapat <?php echo count( $unassigned_peserta ); ?> peserta yang belum memiliki penugasan asesor.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ( empty( $all_peserta ) ) : ?>
    <div class="lsp-no-print" style="width: 210mm; margin: 10px auto; background: #fffbeb; border: 1px solid #fde68a; padding: 10px 15px; border-radius: 4px; font-size: 12px; color: #92400e;">
        ℹ️ <strong>Pemberitahuan:</strong> Belum ada data peserta yang terdaftar pada kegiatan ini. Menampilkan formulir kosong (10 baris) untuk keperluan pengisian manual.
    </div>
<?php endif; ?>

<?php 
// Render Setiap Halaman FR.AK.05 (Maksimal 10 Peserta per Halaman)
foreach ( $chunks as $page_idx => $chunk_peserta ) : 
    $page_num = $page_idx + 1;
    $start_num = ( $page_idx * 10 ) + 1;
?>

    <?php if ( $page_idx > 0 ) : ?>
        <div class="lsp-page-break"></div>
    <?php endif; ?>

    <div class="lsp-page-sheet lsp-page-ak05">
        <div class="lsp-doc-page lsp-doc-ak05">

            <!-- 1. Header FR.AK.05 -->
            <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
                <tr>
                    <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                        FR.AK.05.&nbsp;&nbsp;&nbsp;&nbsp;LAPORAN ASESMEN
                        <?php if ( $total_halaman > 1 ) : ?>
                            <span style="font-size: 11px; font-weight: normal; float: right; color: #475569; text-transform: none;">
                                Halaman <?php echo $page_num; ?> dari <?php echo $total_halaman; ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <!-- 2-6. Tabel Identitas Skema, TUK, Asesor, Tanggal -->
            <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: -1px; margin-bottom: 8px;">
                <tr>
                    <td width="30%" rowspan="2" valign="middle"><strong>Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)</strong></td>
                    <td width="12%">Judul</td>
                    <td width="3%" align="center">:</td>
                    <td colspan="3"><strong><?php echo esc_html( $skema['title'] ?? '' ); ?></strong></td>
                </tr>
                <tr>
                    <td>Nomor</td>
                    <td align="center">:</td>
                    <td colspan="3"><code><?php echo esc_html( $skema['kode'] ?? '' ); ?></code></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>TUK</strong></td>
                    <td align="center">:</td>
                    <td style="width: 20%;"><span class="lsp-cb checked">☑</span> Sewaktu</td>
                    <td style="width: 20%;"><span class="lsp-cb">☐</span> Tempat Kerja</td>
                    <td style="width: 20%;"><span class="lsp-cb">☐</span> Mandiri*</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Nama Asesor</strong></td>
                    <td align="center">:</td>
                    <td colspan="3"><?php echo esc_html( $nama_asesor ); ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Tanggal</strong></td>
                    <td align="center">:</td>
                    <td colspan="3"><?php echo esc_html( $tgl_pelaksanaan ); ?></td>
                </tr>
            </table>

            <!-- 7. Tabel Rekomendasi Asesi (Tepat 10 Baris per Halaman) -->
            <table class="lsp-doc-table lsp-table-bordered lsp-ak05-avoid-break" style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
                <thead>
                    <tr style="background: #f8fafc; font-size: 11px;">
                        <th rowspan="2" style="width: 6%; text-align: center; vertical-align: middle; padding: 5px 4px;">No.</th>
                        <th rowspan="2" style="width: 44%; text-align: left; vertical-align: middle; padding: 5px 8px;">Nama Asesi</th>
                        <th colspan="2" style="width: 16%; text-align: center; padding: 4px;">Rekomendasi</th>
                        <th rowspan="2" style="width: 34%; text-align: left; vertical-align: middle; padding: 5px 8px;">Keterangan**</th>
                    </tr>
                    <tr style="background: #f8fafc; font-size: 10.5px;">
                        <th style="width: 8%; text-align: center; padding: 3px;">K</th>
                        <th style="width: 8%; text-align: center; padding: 3px;">BK</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ( $r = 0; $r < 10; $r++ ) :
                        $curr_no = $start_num + $r;
                        $peserta_item = isset( $chunk_peserta[ $r ] ) ? $chunk_peserta[ $r ] : null;
                        $nama_item = $peserta_item ? $peserta_item->nama_lengkap : '';
                    ?>
                        <tr>
                            <td align="center" style="padding: 5px 4px;"><?php echo $curr_no . '.'; ?></td>
                            <td style="padding: 5px 8px;">
                                <?php if ( ! empty( $nama_item ) ) : ?>
                                    <strong><?php echo esc_html( $nama_item ); ?></strong>
                                <?php else : ?>
                                    &nbsp;
                                <?php endif; ?>
                            </td>
                            <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                            <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                            <td style="padding: 5px 8px;"></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div style="font-size: 10px; margin: 3px 0 6px 0; font-style: italic; color: #475569;">
                ** tuliskan Kode dan Judul Unit Kompetensi yang dinyatakan BK bila mengases satu skema
            </div>

            <!-- 8-10. Aspek Negatif/Positif, Penolakan, dan Saran Perbaikan -->
            <table class="lsp-doc-table lsp-table-bordered lsp-ak05-avoid-break" style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
                <tr>
                    <td width="38%" style="padding: 6px 8px; font-weight: bold; vertical-align: top; font-size: 11px;">
                        Aspek Negatif dan Positif dalam Asesemen
                    </td>
                    <td style="height: 40px; padding: 6px 8px; vertical-align: top;"></td>
                </tr>
                <tr>
                    <td style="padding: 6px 8px; font-weight: bold; vertical-align: top; font-size: 11px;">
                        Pencatatan Penolakan Hasil Asesmen
                    </td>
                    <td style="height: 40px; padding: 6px 8px; vertical-align: top;"></td>
                </tr>
                <tr>
                    <td style="padding: 6px 8px; font-weight: bold; vertical-align: top; font-size: 11px;">
                        Saran Perbaikan :<br><span style="font-size: 9.5px; font-weight: normal; color: #475569;">(Asesor/Personil Terkait)</span>
                    </td>
                    <td style="height: 40px; padding: 6px 8px; vertical-align: top;"></td>
                </tr>
            </table>

            <!-- 11-12. Catatan dan Blok Pengesahan Asesor -->
            <table class="lsp-doc-table lsp-table-bordered lsp-ak05-avoid-break" style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
                <tr>
                    <td rowspan="4" width="38%" style="vertical-align: top; padding: 6px 8px;">
                        <strong>Catatan :</strong>
                    </td>
                    <td colspan="2" style="background: #f8fafc; padding: 5px 8px; font-size: 11px;">
                        <strong>Asesor</strong>
                    </td>
                </tr>
                <tr>
                    <td width="20%" style="padding: 5px 8px;">Nama</td>
                    <td style="padding: 5px 8px;"><strong><?php echo esc_html( $nama_asesor ); ?></strong></td>
                </tr>
                <tr>
                    <td style="padding: 5px 8px;">No. Reg. MET.</td>
                    <td style="padding: 5px 8px;"><?php echo ! empty( $no_reg_met ) ? '<code>' . esc_html( $no_reg_met ) . '</code>' : ''; ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px 8px; vertical-align: top;">Tanda tangan/ Tanggal</td>
                    <td style="height: 55px; vertical-align: bottom; padding: 5px 8px;">
                        <div style="font-size: 10.5px;">Tanggal : &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</div>
                    </td>
                </tr>
            </table>

        </div>
    </div>

<?php endforeach; ?>
