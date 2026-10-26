<?php
/**
 * Template FR.AK.03 — Umpan Balik dan Catatan Asesmen
 * Standar BNSP & Dokumen Resmi LSP Komunikasi Indonesia
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Tanggal pelaksanaan asesmen
$tanggal_raw = ! empty( $kegiatan->tanggal_pelaksanaan ) ? $kegiatan->tanggal_pelaksanaan : '';
$tgl_mulai   = ! empty( $tanggal_raw ) ? date_i18n( 'd F Y', strtotime( $tanggal_raw ) ) : '';
$tgl_selesai = ! empty( $tanggal_raw ) ? date_i18n( 'd F Y', strtotime( $tanggal_raw ) ) : '';

// Kebijakan TUK (Phase AK-01C)
$is_sewaktu      = true;
$is_tempat_kerja = false;
$is_mandiri      = false;

// Asesor & Asesi
$nama_asesi  = ! empty( $pendaftaran->nama_lengkap ) ? $pendaftaran->nama_lengkap : '';
$nama_asesor = '';
$no_reg_met  = '';

if ( ! empty( $asesor ) && is_array( $asesor ) ) {
    $nama_asesor = ! empty( $asesor['name'] ) ? $asesor['name'] : '';
    $no_reg_met  = ! empty( $asesor['no_reg'] ) ? $asesor['no_reg'] : '';
} elseif ( ! empty( $pendaftaran->asesor_id ) ) {
    $asesor_data = LSP_Cert_Integration::get_asesor( $pendaftaran->asesor_id );
    if ( $asesor_data ) {
        $nama_asesor = $asesor_data['name'] ?? '';
        $no_reg_met  = $asesor_data['no_reg'] ?? '';
    }
}
?>

<div class="lsp-doc-page lsp-doc-ak03">

    <!-- Header Dokumen Sesuai Template Resmi DOCX -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.AK.03.&nbsp;&nbsp;&nbsp;&nbsp;UMPAN BALIK DAN CATATAN ASESMEN
            </td>
        </tr>
    </table>

    <!-- Tabel 1: Identitas Skema, TUK, Asesor, Asesi, dan Tanggal Asesmen -->
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
            <td colspan="2"><strong>Nama Asesi</strong></td>
            <td align="center">:</td>
            <td colspan="3"><strong><?php echo esc_html( $nama_asesi ); ?></strong></td>
        </tr>
        <tr>
            <td rowspan="2" valign="middle"><strong>Tanggal Asesmen</strong></td>
            <td>Mulai</td>
            <td align="center">:</td>
            <td colspan="3"><?php echo esc_html( $tgl_mulai ); ?></td>
        </tr>
        <tr>
            <td>Selesai</td>
            <td align="center">:</td>
            <td colspan="3"><?php echo esc_html( $tgl_selesai ); ?></td>
        </tr>
    </table>

    <p style="font-size: 11px; margin: 8px 0 6px 0; color: #1e293b; font-style: italic;">
        Umpan balik dari Asesi (diisi oleh Asesi setelah pengambilan keputusan):
    </p>

    <!-- Tabel 2: Kuesioner Komponen Umpan Balik Asesi -->
    <table class="lsp-doc-table lsp-table-bordered lsp-ak03-avoid-break" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <thead>
            <tr style="background: #f8fafc; font-size: 11px;">
                <th rowspan="2" style="width: 54%; text-align: center; vertical-align: middle; padding: 6px 8px;">KOMPONEN</th>
                <th colspan="2" style="width: 14%; text-align: center; padding: 4px;">Hasil</th>
                <th rowspan="2" style="width: 32%; text-align: center; vertical-align: middle; padding: 6px 8px;">Catatan/Komentar Asesi</th>
            </tr>
            <tr style="background: #f8fafc; font-size: 10.5px;">
                <th style="width: 7%; text-align: center; padding: 4px;">Ya</th>
                <th style="width: 7%; text-align: center; padding: 4px;">Tidak</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $pertanyaan_umpan_balik = array(
                'Saya mendapatkan penjelasan yang cukup memadai mengenai proses asesmen/uji kompetensi',
                'Saya diberikan kesempatan untuk mempelajari standar kompetensi yang akan diujikan dan menilai diri sendiri terhadap pencapaiannya',
                'Asesor memberikan kesempatan untuk mendiskusikan/ menegosiasikan metoda, instrumen dan sumber asesmen serta jadwal asesmen',
                'Asesor berusaha menggali seluruh bukti pendukung yang sesuai dengan latar belakang pelatihan dan pengalaman yang saya miliki',
                'Saya sepenuhnya diberikan kesempatan untuk mendemonstrasikan kompetensi yang saya miliki selama asesmen',
                'Saya mendapatkan penjelasan yang memadai mengenai keputusan asesmen',
                'Asesor memberikan umpan balik yang mendukung setelah asesmen serta tindak lanjutnya',
                'Asesor bersama saya mempelajari semua dokumen asesmen serta menandatanganinya',
                'Saya mendapatkan jaminan kerahasiaan hasil asesmen serta penjelasan penanganan dokumen asesmen',
                'Asesor menggunakan keterampilan komunikasi yang efektif selama asesmen',
            );

            foreach ( $pertanyaan_umpan_balik as $pertanyaan ) :
            ?>
                <tr>
                    <td style="padding: 5px 8px;"><?php echo esc_html( $pertanyaan ); ?></td>
                    <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                    <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                    <td style="padding: 5px 8px;"></td>
                </tr>
            <?php endforeach; ?>

            <!-- Baris Catatan/Komentar Lainnya -->
            <tr>
                <td colspan="4" style="height: 65px; vertical-align: top; padding: 8px;">
                    <strong>Catatan/komentar lainnya (apabila ada) :</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Bagian Tanda Tangan Asesi -->
    <div class="lsp-ak03-ttd lsp-ak03-avoid-break" style="margin-top: 15px; display: flex; justify-content: flex-end;">
        <div style="width: 250px; text-align: center;">
            <div style="font-size: 11px;">Tanggal : &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</div>
            <div style="font-size: 11px; margin-top: 5px; font-weight: bold;">Tanda Tangan Asesi :</div>
            <div style="height: 55px;"></div>
            <div style="font-size: 11.5px; font-weight: bold; border-top: 1px solid #000000; padding-top: 4px;">
                <?php echo esc_html( $nama_asesi ); ?>
            </div>
        </div>
    </div>

</div>
