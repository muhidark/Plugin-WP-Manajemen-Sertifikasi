<?php
/**
 * Template Dokumen Cetak FR.APL.02
 * Asesmen Mandiri
 * Sesuai Standar Format BNSP (Tanpa Logo & Kop)
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Index jawaban APL 02 dari asesi
$jawaban_map = array();
if ( ! empty( $apl02_jawaban ) ) {
    foreach ( $apl02_jawaban as $j ) {
        $jawaban_map[ $j->unit_kompetensi_kode ][ (int) $j->elemen_nomor ] = strtoupper( trim( $j->jawaban ) );
    }
}

// Tanggal tanda tangan APL 02 (Hari H asesmen / tanggal pendaftaran)
$tanggal_ttd_apl02 = ! empty( $pendaftaran->created_at ) ? date_i18n( 'd F Y', strtotime( $pendaftaran->created_at ) ) : date_i18n( 'd F Y', current_time( 'timestamp' ) );
?>

<div class="lsp-doc-page">

    <div style="text-align: center; margin-bottom: 20px;">
        <h1 style="font-size: 15px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">FR.APL.02. ASESMEN MANDIRI</h1>
    </div>

    <!-- Header Skema Sertifikasi -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
        <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)</strong></td>
            <td width="12%">Judul</td>
            <td width="3%">:</td>
            <td><strong><?php echo esc_html( $skema['title'] ?? '-' ); ?></strong></td>
        </tr>
        <tr>
            <td>Nomor</td>
            <td>:</td>
            <td><code><?php echo esc_html( $skema['kode'] ?? '-' ); ?></code></td>
        </tr>
    </table>

    <!-- Panduan Asesmen Mandiri -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
        <tr style="background: #f8fafc;">
            <td style="padding: 8px 12px;"><strong>PANDUAN ASESMEN MANDIRI</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px 12px; font-size: 12px; line-height: 1.5;">
                <strong>Instruksi:</strong><br>
                1. Baca setiap pertanyaan di kolom sebelah kiri.<br>
                2. Beri tanda centang (√) pada kotak jika Anda yakin dapat melakukan tugas yang dijelaskan.<br>
                3. Isi kolom di sebelah kanan dengan menuliskan bukti yang relevan anda miliki untuk menunjukkan bahwa anda melakukan pekerjaan.
            </td>
        </tr>
    </table>

    <!-- Tabel Rincian per Unit Kompetensi -->
    <?php if ( ! empty( $units_skkni ) ) : ?>
        <?php foreach ( $units_skkni as $u_idx => $u ) : 
            $u_kode = $u['kode'] ?? '';
            $u_judul = $u['judul'] ?? '';
            $elemen_list = ! empty( $u['elemen'] ) ? $u['elemen'] : array();
        ?>
            <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 18px; page-break-inside: avoid;">
                <tr style="background: #f8fafc;">
                    <td width="20%" rowspan="2" valign="middle"><strong>Unit Kompetensi <?php echo ($u_idx + 1); ?></strong></td>
                    <td width="12%">Kode Unit</td>
                    <td width="3%">:</td>
                    <td colspan="3"><strong><?php echo esc_html( $u_kode ); ?></strong></td>
                </tr>
                <tr style="background: #f8fafc;">
                    <td>Judul Unit</td>
                    <td>:</td>
                    <td colspan="3"><strong><?php echo esc_html( $u_judul ); ?></strong></td>
                </tr>
                <tr style="background: #f1f5f9; text-align: center; font-size: 12px;">
                    <th colspan="3" width="60%" style="text-align: left; padding: 6px 10px;">Dapatkah Saya?</th>
                    <th width="8%">K</th>
                    <th width="8%">BK</th>
                    <th width="24%">Bukti yang relevan</th>
                </tr>
                <?php if ( ! empty( $elemen_list ) ) : ?>
                    <?php foreach ( $elemen_list as $e_idx => $el ) : 
                        $e_nomor = $el['nomor'] ?? ($e_idx + 1);
                        $e_nama = $el['nama'] ?? '';
                        $kuks = $el['kuk'] ?? array();
                        // Tidak ada jawaban tersimpan → biarkan kosong (☐), jangan diasumsikan Kompeten.
                        $ans = $jawaban_map[ $u_kode ][ (int) $e_nomor ] ?? '';
                    ?>
                        <tr>
                            <td colspan="3" valign="top" style="padding: 6px 10px; font-size: 11.5px; line-height: 1.4;">
                                <strong>Elemen: <?php echo esc_html( $e_nama ); ?></strong><br>
                                <strong>Kriteria Unjuk Kerja:</strong>
                                <?php if ( ! empty( $kuks ) && is_array( $kuks ) ) : ?>
                                    <div style="padding-left: 2px; margin-top: 2px;">
                                        <?php foreach ( $kuks as $kuk_line ) : ?>
                                            <div><?php echo esc_html( $kuk_line ); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; vertical-align: middle; font-size: 15px;">
                                <?php echo ($ans === 'K') ? '☑' : '☐'; ?>
                            </td>
                            <td style="text-align: center; vertical-align: middle; font-size: 15px;">
                                <?php echo ($ans === 'BK') ? '☑' : '☐'; ?>
                            </td>
                            <td style="vertical-align: middle; font-size: 11px; color: #334155;">
                                Portofolio / Bukti yang relevan
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" style="text-align: center; font-size: 12px;">Belum ada elemen kompetensi untuk unit ini.</td>
                    </tr>
                <?php endif; ?>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Bagian Rekomendasi Asesor & Tanda Tangan -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: 20px; page-break-inside: avoid;">
        <tr>
            <td width="50%" rowspan="3" valign="top" style="padding: 10px;">
                <strong>Rekomendasi Untuk Asesi:</strong><br><br>
                Asesmen <strong><?php echo ($pendaftaran->status_rekomendasi === 'disetujui') ? '<u>dapat</u> / <del style="color:#64748b;">tidak dapat</del>' : '<del style="color:#64748b;">dapat</del> / <u>tidak dapat</u>'; ?></strong> dilanjutkan
            </td>
            <td colspan="2" style="background: #f8fafc;"><strong>Asesi :</strong></td>
        </tr>
        <tr>
            <td width="25%">Nama</td>
            <td><strong><?php echo esc_html( $pendaftaran->nama_lengkap ); ?></strong></td>
        </tr>
        <tr>
            <td>Tanda tangan/ Tanggal</td>
            <td style="height: 60px; vertical-align: bottom;">
                <div style="font-size: 10px; color: #64748b; margin-bottom: 22px;">( Tanda Tangan Asesi )</div>
                <strong><?php echo esc_html( $tanggal_ttd_apl02 ); ?></strong>
            </td>
        </tr>
        <tr>
            <td rowspan="4" valign="top" style="padding: 10px;">
                <em>Catatan: Semua asesmen mandiri telah diverifikasi sesuai persyaratan.</em>
            </td>
            <td colspan="2" style="background: #f8fafc;"><strong>Ditinjau Oleh Asesor :</strong></td>
        </tr>
        <tr>
            <td>Nama :</td>
            <td><strong><?php echo esc_html( $asesor['name'] ?? 'Asesor Kompetensi' ); ?></strong></td>
        </tr>
        <tr>
            <td>No. Reg:</td>
            <td><strong><?php echo esc_html( $asesor['no_reg'] ?? '-' ); ?></strong></td>
        </tr>
        <tr>
            <td>Tanda tangan/ Tanggal</td>
            <td style="height: 60px; vertical-align: bottom;">
                <div style="font-size: 10px; color: #64748b; margin-bottom: 22px;">( Tanda Tangan Asesor )</div>
                <strong><?php echo esc_html( $tanggal_ttd_apl02 ); ?></strong>
            </td>
        </tr>
    </table>

</div>
