<?php
/**
 * Template FR.AK.02 — Rekaman Asesmen Kompetensi
 * Standar BNSP & Dokumen Resmi LSP Komunikasi Indonesia
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Tanggal pelaksanaan kegiatan (BUKAN H-2)
$tanggal_raw = ! empty( $kegiatan->tanggal_pelaksanaan ) ? $kegiatan->tanggal_pelaksanaan : '';
$tgl_mulai   = ! empty( $tanggal_raw ) ? date_i18n( 'd F Y', strtotime( $tanggal_raw ) ) : '';
$tgl_selesai = ! empty( $tanggal_raw ) ? date_i18n( 'd F Y', strtotime( $tanggal_raw ) ) : '';

// Kebijakan Tampilan Dokumen Cetak (Phase AK-01C & AK-02):
// Menyeragamkan pilihan jenis TUK pada dokumen cetak:
// ☑ Sewaktu  ☐ Tempat Kerja  ☐ Mandiri*
$is_sewaktu      = true;
$is_tempat_kerja = false;
$is_mandiri      = false;

// Asesor & Asesi
// "Jika asesor belum ditugaskan, jangan memilih asesor secara acak.
// Jika nomor registrasi MET belum tersedia, biarkan kosong dan jangan membuat nomor contoh."
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

$total_units = ! empty( $units_skkni ) && is_array( $units_skkni ) ? count( $units_skkni ) : 0;
?>

<div class="lsp-doc-page lsp-doc-ak02">

    <!-- Header Dokumen Sesuai Template Resmi DOCX -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.AK.02.&nbsp;&nbsp;&nbsp;&nbsp;REKAMAN ASESMEN KOMPETENSI
            </td>
        </tr>
    </table>

    <!-- Tabel 1: Identitas Skema, TUK, Asesor, Asesi, dan Tanggal Asesmen -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: -1px; margin-bottom: 0;">
        <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)*</strong></td>
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

    <div style="font-size: 11px; margin-top: 4px; margin-bottom: 8px; color: #334155;">
        *) Coret yang tidak perlu
    </div>

    <!-- Tabel 2: Daftar Unit Kompetensi -->
    <?php if ( empty( $units_skkni ) ) : ?>
        <div style="color: #b91c1c; background: #fee2e2; border: 1px solid #ef4444; padding: 12px; margin: 10px 0; border-radius: 4px; font-weight: bold;">
            Perhatian: Daftar unit kompetensi untuk skema ini belum dikonfigurasi atau tidak ditemukan pada Master Skema. Dokumen tidak dapat dilengkapi secara otomatis.
        </div>
    <?php else : ?>
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
            <thead>
                <tr style="background: #f8fafc;">
                    <th style="width: 8%; text-align: center; padding: 6px 4px;">No.</th>
                    <th style="width: 25%; text-align: center; padding: 6px 8px; white-space: nowrap;">Kode Unit</th>
                    <th style="width: 67%; text-align: left; padding: 6px 10px;">Judul Unit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $units_skkni as $idx => $unit ) : ?>
                    <tr>
                        <td align="center" style="padding: 5px 4px;"><?php echo ( $idx + 1 ) . '.'; ?></td>
                        <td align="center" style="padding: 5px 8px; white-space: nowrap;"><code><?php echo esc_html( $unit['kode'] ); ?></code></td>
                        <td style="padding: 5px 10px;"><?php echo esc_html( $unit['judul'] ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p style="font-size: 11px; margin: 8px 0 6px 0; color: #1e293b;">
        Beri tanda centang (√) di kolom yang sesuai untuk mencerminkan bukti yang sesuai untuk setiap Unit Kompetensi.
    </p>

    <!-- Tabel 3: Matriks Bukti Asesmen per Unit Kompetensi -->
    <table class="lsp-doc-table lsp-table-bordered lsp-ak02-avoid-break" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <thead>
            <tr style="background: #f8fafc; font-size: 10.5px;">
                <th style="width: 13%; text-align: center; padding: 6px 4px; vertical-align: middle;">Unit Kompetensi</th>
                <th style="width: 12%; text-align: center; padding: 6px 4px; vertical-align: middle;">Observasi Demonstrasi</th>
                <th style="width: 11%; text-align: center; padding: 6px 4px; vertical-align: middle;">Portofolio</th>
                <th style="width: 18%; text-align: center; padding: 6px 4px; vertical-align: middle;">Pernyataan Pihak Ketiga Pertanyaan Wawancara</th>
                <th style="width: 11%; text-align: center; padding: 6px 4px; vertical-align: middle;">Pertanyaan Lisan</th>
                <th style="width: 11%; text-align: center; padding: 6px 4px; vertical-align: middle;">Pertanyaan Tertulis</th>
                <th style="width: 12%; text-align: center; padding: 6px 4px; vertical-align: middle;">Proyek Kerja</th>
                <th style="width: 12%; text-align: center; padding: 6px 4px; vertical-align: middle;">Lainnya</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $units_skkni ) ) : ?>
                <?php foreach ( $units_skkni as $idx => $unit ) : ?>
                    <tr>
                        <td align="center" style="padding: 4px; font-weight: bold;"><?php echo ( $idx + 1 ) . '.'; ?></td>
                        <td align="center" style="padding: 4px;"><span class="lsp-cb">☐</span></td>
                        <td align="center" style="padding: 4px;"><span class="lsp-cb">☐</span></td>
                        <td align="center" style="padding: 4px;"><span class="lsp-cb">☐</span></td>
                        <td align="center" style="padding: 4px;"><span class="lsp-cb">☐</span></td>
                        <td align="center" style="padding: 4px;"><span class="lsp-cb">☐</span></td>
                        <td align="center" style="padding: 4px;"><span class="lsp-cb">☐</span></td>
                        <td align="center" style="padding: 4px;"><span class="lsp-cb">☐</span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Rekomendasi Hasil Asesmen -->
            <tr>
                <td style="font-weight: bold; padding: 8px;">Rekomendasi hasil asesmen</td>
                <td colspan="7" style="padding: 8px 12px; font-size: 11.5px;">
                    <span class="lsp-cb">☐</span> <strong>Kompeten</strong> &nbsp;&nbsp;/&nbsp;&nbsp; <span class="lsp-cb">☐</span> <strong>Belum kompeten</strong>
                </td>
            </tr>

            <!-- Tindak Lanjut yang Dibutuhkan -->
            <tr>
                <td style="padding: 8px; vertical-align: top;">
                    <strong>Tindak lanjut yang dibutuhkan</strong><br>
                    <span style="font-size: 10px; font-style: italic; color: #475569;">(Masukkan pekerjaan tambahan dan asesmen yang diperlukan untuk mencapai kompetensi)</span>
                </td>
                <td colspan="7" style="height: 60px; vertical-align: top; padding: 8px;"></td>
            </tr>

            <!-- Komentar / Observasi oleh Asesor -->
            <tr>
                <td style="padding: 8px; vertical-align: top;">
                    <strong>Komentar/Observasi oleh asesor</strong>
                </td>
                <td colspan="7" style="height: 60px; vertical-align: top; padding: 8px;"></td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel 4: Lembar Tanda Tangan Asesi dan Asesor -->
    <table class="lsp-doc-table lsp-table-bordered lsp-ak02-avoid-break" style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 12px;">
        <!-- Blok Asesi -->
        <tr style="background: #f8fafc;">
            <th colspan="3" style="text-align: left; padding: 6px 10px; font-size: 11.5px;"><strong>Asesi</strong></th>
        </tr>
        <tr>
            <td width="26%" style="padding: 6px 8px;">Nama</td>
            <td width="3%" align="center">:</td>
            <td style="padding: 6px 8px;"><strong><?php echo esc_html( $nama_asesi ); ?></strong></td>
        </tr>
        <tr>
            <td style="padding: 6px 8px; vertical-align: top;">Tanda tangan dan Tanggal</td>
            <td align="center" style="vertical-align: top;">:</td>
            <td style="height: 65px; vertical-align: bottom; padding: 6px 8px;">
                <div style="font-size: 11px;">Tanggal : &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</div>
            </td>
        </tr>

        <!-- Blok Asesor -->
        <tr style="background: #f8fafc;">
            <th colspan="3" style="text-align: left; padding: 6px 10px; font-size: 11.5px;"><strong>Asesor</strong></th>
        </tr>
        <tr>
            <td style="padding: 6px 8px;">Nama</td>
            <td align="center">:</td>
            <td style="padding: 6px 8px;"><strong><?php echo esc_html( $nama_asesor ); ?></strong></td>
        </tr>
        <tr>
            <td style="padding: 6px 8px;">No. Reg.MET.</td>
            <td align="center">:</td>
            <td style="padding: 6px 8px;"><?php echo ! empty( $no_reg_met ) ? '<code>' . esc_html( $no_reg_met ) . '</code>' : ''; ?></td>
        </tr>
        <tr>
            <td style="padding: 6px 8px; vertical-align: top;">Tanda tangan dan Tanggal</td>
            <td align="center" style="vertical-align: top;">:</td>
            <td style="height: 65px; vertical-align: bottom; padding: 6px 8px;">
                <div style="font-size: 11px;">Tanggal : &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</div>
            </td>
        </tr>
    </table>

    <!-- Lampiran Dokumen -->
    <div class="lsp-ak02-lampiran lsp-ak02-avoid-break" style="margin-top: 10px; font-size: 11px; color: #1e293b;">
        <strong>LAMPIRAN DOKUMEN:</strong>
        <div style="margin-top: 5px; padding-left: 5px; line-height: 1.8;">
            <div><span class="lsp-cb">☐</span> Dokumen APL 01 peserta</div>
            <div><span class="lsp-cb">☐</span> Dokumen APL 02 peserta</div>
            <div><span class="lsp-cb">☐</span> Bukti-bukti berkualitas peserta</div>
            <div><span class="lsp-cb">☐</span> Tinjauan proses asesmen</div>
        </div>
    </div>

</div>
