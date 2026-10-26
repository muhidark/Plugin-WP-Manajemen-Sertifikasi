<?php
/**
 * Template FR.IA.05A — Pertanyaan Tertulis Pilihan Ganda
 * Standar BNSP & Dokumen Resmi LSP Komunikasi Indonesia
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Tanggal pelaksanaan kegiatan
$tanggal_raw     = ! empty( $kegiatan->tanggal_pelaksanaan ) ? $kegiatan->tanggal_pelaksanaan : '';
$tgl_pelaksanaan = ! empty( $tanggal_raw ) ? date_i18n( 'd F Y', strtotime( $tanggal_raw ) ) : '';

// Tanggal penyusunan dokumen (H-2 dari tanggal kegiatan)
$tgl_penyusunan = ! empty( $tanggal_raw ) ? date_i18n( 'd F Y', strtotime( '-2 days', strtotime( $tanggal_raw ) ) ) : date_i18n( 'd F Y' );

// Asesor & Asesi
$nama_asesi  = ! empty( $pendaftaran->nama_lengkap ) ? $pendaftaran->nama_lengkap : '...................................................';
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

// Fallback nama asesor jika belum ditentukan
if ( empty( $nama_asesor ) ) {
    $nama_asesor = ! empty( $penyusun['name'] ) ? $penyusun['name'] : '...................................................';
}

// Data Penyusun & Validator
$nama_penyusun  = ! empty( $penyusun['name'] ) ? $penyusun['name'] : 'Masriah';
$met_penyusun   = ! empty( $penyusun['no_reg'] ) ? $penyusun['no_reg'] : 'MET.000.032423 2019';
$nama_validator = ! empty( $validator['name'] ) ? $validator['name'] : 'Eko Prasyanto';
$met_validator  = ! empty( $validator['no_reg'] ) ? $validator['no_reg'] : 'MET.000.014249 2019';

// Ambil instrumen soal berdasarkan skema_id
$skema_id   = ! empty( $kegiatan->skema_id ) ? (int) $kegiatan->skema_id : ( ! empty( $skema['id'] ) ? (int) $skema['id'] : 0 );
$instrument = ! empty( $ia05_override ) ? $ia05_override : LSP_Cert_IA05::get_instrument( $skema_id, $paket_muk_id ?? 0 );
$pertanyaan = ! empty( $instrument['pertanyaan'] ) ? $instrument['pertanyaan'] : '';
?>

<div class="lsp-doc-page lsp-doc-ia05a">

    <!-- Header Dokumen -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.IA.05A.&nbsp;&nbsp;&nbsp;&nbsp;DPT - PERTANYAAN TERTULIS PILIHAN GANDA
            </td>
        </tr>
    </table>

    <!-- Tabel 1: Identitas Skema, TUK, Asesor, Asesi, dan Waktu -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; border-top: none; margin-bottom: 12px;">
        <tr>
            <td rowspan="2" style="width: 35%; vertical-align: middle; font-weight: bold; padding: 6px 10px;">
                Skema Sertifikasi<br>
                <span style="font-weight: normal; font-size: 9pt;">(KKNI/Okupasi/Klaster)</span>
            </td>
            <td style="width: 15%; padding: 6px 10px; font-weight: bold;">Judul</td>
            <td style="width: 3%; text-align: center; padding: 6px 0;">:</td>
            <td style="padding: 6px 10px; font-weight: bold;"><?php echo esc_html( $skema['title'] ?? '-' ); ?></td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; font-weight: bold;">Nomor</td>
            <td style="text-align: center; padding: 6px 0;">:</td>
            <td style="padding: 6px 10px; font-family: monospace; font-size: 9.5pt;"><?php echo esc_html( $skema['kode'] ?? '-' ); ?></td>
        </tr>
        <?php if ( ! empty( $nama_paket_muk ) ) : ?>
        <tr>
            <td colspan="2" style="font-weight: bold; padding: 6px 10px; background: #f8fafc;">Paket MUK / Versi</td>
            <td style="padding: 6px 0; text-align: center; background: #f8fafc;">:</td>
            <td style="padding: 6px 10px; background: #f8fafc; font-size: 9pt;">
                <strong><?php echo esc_html( $nama_paket_muk ); ?></strong> (<code><?php echo esc_html( $kode_versi_muk ); ?></code>)
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <td colspan="2" style="padding: 6px 10px; font-weight: bold;">TUK</td>
            <td style="text-align: center; padding: 6px 0;">:</td>
            <td style="padding: 6px 10px;">
                <span style="display: inline-block; margin-right: 18px;">☑ Sewaktu</span>
                <span style="display: inline-block; margin-right: 18px;">☐ Tempat Kerja</span>
                <span style="display: inline-block;">☐ Mandiri</span>*
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 6px 10px; font-weight: bold;">Nama Asesor</td>
            <td style="text-align: center; padding: 6px 0;">:</td>
            <td style="padding: 6px 10px;"><?php echo esc_html( $nama_asesor ); ?></td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 6px 10px; font-weight: bold;">Nama Asesi</td>
            <td style="text-align: center; padding: 6px 0;">:</td>
            <td style="padding: 6px 10px; font-weight: bold;"><?php echo esc_html( $nama_asesi ); ?></td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 6px 10px; font-weight: bold;">Tanggal</td>
            <td style="text-align: center; padding: 6px 0;">:</td>
            <td style="padding: 6px 10px;"><?php echo esc_html( $tgl_pelaksanaan ?: '...................................................' ); ?></td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 6px 10px; font-weight: bold;">Waktu</td>
            <td style="text-align: center; padding: 6px 0;">:</td>
            <td style="padding: 6px 10px;">......... Menit</td>
        </tr>
    </table>

    <div style="font-size: 8.5pt; font-style: italic; color: #475569; margin-bottom: 12px;">
        *Coret yang tidak perlu
    </div>

    <!-- Instruksi Soal -->
    <div style="font-weight: bold; font-size: 10.5pt; margin-bottom: 8px;">
        Jawab semua pertanyaan berikut:
    </div>

    <!-- Konten Pertanyaan Pilihan Ganda -->
    <?php if ( empty( $pertanyaan ) ) : ?>
        <div class="lsp-alert-box" style="border: 1px solid #dc2626; background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 4px; margin: 15px 0; font-size: 10pt;">
            <strong>Perhatian:</strong> Naskah pertanyaan tertulis pilihan ganda untuk skema ini belum diatur. Silakan masukkan materi soal terlebih dahulu pada menu <em>Sertifikasi &gt; Instrumen FR.IA.05</em>.
        </div>
    <?php else : ?>
        <div class="lsp-ia05-content" style="border: 1px solid #000; padding: 14px 18px; margin-bottom: 20px; font-size: 10pt; line-height: 1.6; white-space: pre-wrap; font-family: inherit;">
<?php echo esc_html( $pertanyaan ); ?>
        </div>
    <?php endif; ?>

    <!-- Bagian Penyusun dan Validator Sesuai Format BNSP -->
    <div class="lsp-ia05a-avoid-break" style="margin-top: 25px;">
        <div style="font-weight: bold; font-size: 10pt; margin-bottom: 6px; text-transform: uppercase;">
            PENYUSUN DAN VALIDATOR
        </div>
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f1f5f9; text-align: center;">
                    <th style="width: 16%; padding: 6px 8px; font-size: 9pt;">STATUS</th>
                    <th style="width: 6%; padding: 6px 4px; font-size: 9pt;">NO</th>
                    <th style="width: 28%; padding: 6px 8px; font-size: 9pt;">NAMA</th>
                    <th style="width: 25%; padding: 6px 8px; font-size: 9pt;">NOMOR MET</th>
                    <th style="width: 25%; padding: 6px 8px; font-size: 9pt;">TANDA TANGAN DAN TANGGAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 6px 8px; font-weight: bold; text-align: center; vertical-align: middle;">PENYUSUN</td>
                    <td style="padding: 6px 4px; text-align: center; vertical-align: middle;">1</td>
                    <td style="padding: 6px 8px; vertical-align: middle; font-weight: 600;"><?php echo esc_html( $nama_penyusun ); ?></td>
                    <td style="padding: 6px 8px; font-family: monospace; font-size: 9pt; vertical-align: middle;"><?php echo esc_html( $met_penyusun ); ?></td>
                    <td style="padding: 6px 8px; font-size: 9pt; vertical-align: middle;"><?php echo esc_html( $tgl_penyusunan ); ?></td>
                </tr>
                <tr>
                    <td style="padding: 6px 8px; font-weight: bold; text-align: center; vertical-align: middle;">VALIDATOR</td>
                    <td style="padding: 6px 4px; text-align: center; vertical-align: middle;">1</td>
                    <td style="padding: 6px 8px; vertical-align: middle; font-weight: 600;"><?php echo esc_html( $nama_validator ); ?></td>
                    <td style="padding: 6px 8px; font-family: monospace; font-size: 9pt; vertical-align: middle;"><?php echo esc_html( $met_validator ); ?></td>
                    <td style="padding: 6px 8px; font-size: 9pt; vertical-align: middle;"><?php echo esc_html( $tgl_penyusunan ); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
