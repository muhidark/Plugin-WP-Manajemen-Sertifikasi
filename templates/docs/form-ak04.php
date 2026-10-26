<?php
/**
 * Template FR.AK.04 — Banding Asesmen
 * Standar BNSP & Dokumen Resmi LSP Komunikasi Indonesia
 * Form blank / tanpa isian otomatis untuk pengajuan banding manual oleh Asesi.
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$nama_asesi      = ! empty( $pendaftaran->nama_lengkap ) ? $pendaftaran->nama_lengkap : '';
$nama_asesor     = ! empty( $asesor['name'] ) ? $asesor['name'] : '';
$tgl_pelaksanaan = ! empty( $kegiatan->tanggal_pelaksanaan ) ? date_i18n( 'd F Y', strtotime( $kegiatan->tanggal_pelaksanaan ) ) : '';
$judul_skema     = ! empty( $skema['title'] ) ? $skema['title'] : '';
$kode_skema      = ! empty( $skema['kode'] ) ? $skema['kode'] : '';
?>

<div class="lsp-doc-page lsp-doc-ak04">

    <!-- Header Dokumen Sesuai Template Resmi DOCX -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.AK.04.&nbsp;&nbsp;&nbsp;&nbsp;BANDING ASESMEN
            </td>
        </tr>
    </table>

    <!-- Tabel 1: Identitas Asesi, Asesor, Tanggal Asesmen -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: -1px; margin-bottom: 12px;">
        <tr>
            <td width="25%" style="padding: 6px 8px;"><strong>Nama Asesi:</strong></td>
            <td colspan="2" style="padding: 6px 8px;">
                <?php if ( ! empty( $nama_asesi ) ) : ?>
                    <strong><?php echo esc_html( $nama_asesi ); ?></strong>
                <?php else : ?>
                    &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td style="padding: 6px 8px;"><strong>Nama Asesor:</strong></td>
            <td colspan="2" style="padding: 6px 8px;">
                <?php if ( ! empty( $nama_asesor ) ) : ?>
                    <strong><?php echo esc_html( $nama_asesor ); ?></strong>
                <?php else : ?>
                    &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td style="padding: 6px 8px;"><strong>Tanggal Asesmen:</strong></td>
            <td colspan="2" style="padding: 6px 8px;">
                <?php if ( ! empty( $tgl_pelaksanaan ) ) : ?>
                    <strong><?php echo esc_html( $tgl_pelaksanaan ); ?></strong>
                <?php else : ?>
                    &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- Tabel 2: Pertanyaan Banding -->
    <table class="lsp-doc-table lsp-table-bordered lsp-ak04-avoid-break" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <thead>
            <tr style="background: #f8fafc; font-size: 11px;">
                <th style="width: 76%; text-align: left; padding: 6px 8px;">Jawablah dengan Ya atau Tidak pertanyaan-pertanyaan berikut ini :</th>
                <th style="width: 12%; text-align: center; padding: 6px 4px;">YA</th>
                <th style="width: 12%; text-align: center; padding: 6px 4px;">TIDAK</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 8px 10px;">Apakah Proses Banding telah dijelaskan kepada Anda?</td>
                <td align="center" style="padding: 8px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 8px 4px;"><span class="lsp-cb">☐</span></td>
            </tr>
            <tr>
                <td style="padding: 8px 10px;">Apakah Anda telah mendiskusikan Banding dengan Asesor?</td>
                <td align="center" style="padding: 8px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 8px 4px;"><span class="lsp-cb">☐</span></td>
            </tr>
            <tr>
                <td style="padding: 8px 10px;">Apakah Anda mau melibatkan “orang lain” membantu Anda dalam Proses Banding?</td>
                <td align="center" style="padding: 8px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 8px 4px;"><span class="lsp-cb">☐</span></td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel 3: Pernyataan Skema, Alasan Banding & Pengesahan (Blank) -->
    <table class="lsp-doc-table lsp-table-bordered lsp-ak04-avoid-break" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            <td style="padding: 10px 12px; line-height: 1.8;">
                Banding ini diajukan atas Keputusan Asesmen yang dibuat terhadap Skema Sertifikasi (Kualifikasi/Klaster/Okupasi) berikut :<br><br>
                <strong>Skema Sertifikasi :</strong> <?php echo ! empty( $judul_skema ) ? '<strong>' . esc_html( $judul_skema ) . '</strong>' : '&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;'; ?><br>
                <strong>No. Skema Sertifikasi :</strong> <?php echo ! empty( $kode_skema ) ? '<code>' . esc_html( $kode_skema ) . '</code>' : '&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;'; ?>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 12px; height: 140px; vertical-align: top;">
                <strong>Banding ini diajukan atas alasan sebagai berikut :</strong>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 12px; font-size: 11px; font-style: italic; background: #f8fafc; color: #334155;">
                Anda mempunyai hak mengajukan banding jika Anda menilai Proses Asesmen tidak sesuai SOP dan tidak memenuhi Prinsip Asesmen.
            </td>
        </tr>
        <tr>
            <td style="padding: 20px 12px 15px 12px; height: 70px; vertical-align: bottom;">
                Tanda tangan Asesi : &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Tanggal : &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;
            </td>
        </tr>
    </table>

</div>
