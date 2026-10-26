<?php
/**
 * Template FR.AK.06 — Meninjau Proses Asesmen
 * Standar BNSP & Dokumen Resmi LSP Komunikasi Indonesia
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Tanggal pelaksanaan kegiatan
$tanggal_raw = ! empty( $kegiatan->tanggal_pelaksanaan ) ? $kegiatan->tanggal_pelaksanaan : '';
$tgl_pelaksanaan = ! empty( $tanggal_raw ) ? date_i18n( 'd F Y', strtotime( $tanggal_raw ) ) : '';

// Kebijakan TUK (Phase AK-01C)
$is_sewaktu      = true;
$is_tempat_kerja = false;
$is_mandiri      = false;

// Asesor
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

<div class="lsp-doc-page lsp-doc-ak06">

    <!-- Header Dokumen Sesuai Template Resmi DOCX -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.AK.06.&nbsp;&nbsp;&nbsp;&nbsp;MENINJAU PROSES ASESMEN
            </td>
        </tr>
    </table>

    <!-- Tabel 1: Identitas Skema, TUK, Asesor, Tanggal -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: -1px; margin-bottom: 8px;">
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
            <td colspan="2"><strong>Tanggal</strong></td>
            <td align="center">:</td>
            <td colspan="3"><?php echo esc_html( $tgl_pelaksanaan ); ?></td>
        </tr>
    </table>

    <!-- Penjelasan Pengisian Dokumen -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
        <tr>
            <td style="padding: 8px 10px; font-size: 10.5px; line-height: 1.5; background: #ffffff;">
                <strong>Penjelasan:</strong><br>
                1. Peninjauan dapat dilakukan oleh lead asesor atau asesor yang melaksanakan asesmen.<br>
                2. Peninjauan dapat dilakukan secara terpadu dalam skema sertifikasi dan / atau peserta kelompok yang homogen.<br>
                3. Isilah pemenuhan dimensi kompetensi dengan menulis kode rekaman formulir yang membuktikan terpenuhinya dimensi kompetensi.
            </td>
        </tr>
    </table>

    <!-- Tabel 2: Kesesuaian dengan Prinsip Asesmen -->
    <table class="lsp-doc-table lsp-table-bordered lsp-ak06-avoid-break" style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
        <thead>
            <tr style="background: #f8fafc; font-size: 11px;">
                <th rowspan="2" style="width: 36%; text-align: left; vertical-align: middle; padding: 6px 8px;">Aspek yang ditinjau</th>
                <th colspan="4" style="text-align: center; padding: 4px;">Kesesuaian dengan prinsip asesmen</th>
            </tr>
            <tr style="background: #f8fafc; font-size: 10.5px;">
                <th style="width: 16%; text-align: center; padding: 4px;">Validitas</th>
                <th style="width: 16%; text-align: center; padding: 4px;">Reliabel</th>
                <th style="width: 16%; text-align: center; padding: 4px;">Fleksibel</th>
                <th style="width: 16%; text-align: center; padding: 4px;">Adil</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background: #f1f5f9;">
                <td colspan="5" style="padding: 4px 8px; font-weight: bold; font-size: 11px;">Prosedur asesmen:</td>
            </tr>
            <tr>
                <td style="padding: 5px 8px;">Rencana asesmen</td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
            </tr>
            <tr>
                <td style="padding: 5px 8px;">Persiapan asesmen</td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
            </tr>
            <tr>
                <td style="padding: 5px 8px;">Implementasi asesmen</td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
            </tr>
            <tr>
                <td style="padding: 5px 8px;">Keputusan asesmen</td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="background: #f8fafc;"></td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
            </tr>
            <tr>
                <td style="padding: 5px 8px;">Umpan balik asesmen</td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="background: #f8fafc;"></td>
                <td align="center" style="padding: 5px 4px;"><span class="lsp-cb">☐</span></td>
            </tr>
            <tr>
                <td colspan="5" style="height: 50px; vertical-align: top; padding: 8px;">
                    <strong>Rekomendasi untuk peningkatan :</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel 3: Pemenuhan Dimensi Kompetensi -->
    <table class="lsp-doc-table lsp-table-bordered lsp-ak06-avoid-break" style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
        <thead>
            <tr style="background: #f8fafc; font-size: 11px;">
                <th rowspan="2" style="width: 35%; text-align: left; vertical-align: middle; padding: 6px 8px;">Aspek yang ditinjau</th>
                <th colspan="5" style="text-align: center; padding: 4px;">Pemenuhan dimensi kompetensi</th>
            </tr>
            <tr style="background: #f8fafc; font-size: 10.5px;">
                <th style="width: 13%; text-align: center; padding: 4px;">Task Skills</th>
                <th style="width: 13%; text-align: center; padding: 4px;">Task Management Skills</th>
                <th style="width: 13%; text-align: center; padding: 4px;">Contingency Management Skills</th>
                <th style="width: 13%; text-align: center; padding: 4px;">Job Role/ Environment Skills</th>
                <th style="width: 13%; text-align: center; padding: 4px;">Transfer Skills</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 6px 8px;">
                    <strong>Konsistensi keputusan asesmen</strong><br>
                    <span style="font-size: 10px; color: #475569;">Bukti dari berbagai asesmen diperiksa untuk konsistensi dimensi kompetensi</span>
                </td>
                <td align="center" style="padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td align="center" style="padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
            </tr>
            <tr>
                <td style="height: 35px; padding: 6px 8px; font-style: italic; font-size: 10.5px; color: #64748b;">(Tuliskan kode rekaman formulir pembukti dimensi)</td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="6" style="height: 50px; vertical-align: top; padding: 8px;">
                    <strong>Rekomendasi untuk peningkatan:</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel 4: Pengesahan Lead Asesor / Asesor -->
    <table class="lsp-doc-table lsp-table-bordered lsp-ak06-avoid-break" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <thead>
            <tr style="background: #f8fafc; font-size: 11px;">
                <th style="width: 35%; text-align: center; padding: 6px 8px;">Nama Lead Asesor/Asesor</th>
                <th style="width: 35%; text-align: center; padding: 6px 8px;">Tanggal & Tanda Tangan</th>
                <th style="width: 30%; text-align: center; padding: 6px 8px;">Komentar</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 10px 8px; vertical-align: middle;">
                    <strong><?php echo esc_html( $nama_asesor ); ?></strong>
                    <?php if ( ! empty( $no_reg_met ) ) : ?>
                        <div style="font-size: 10.5px; margin-top: 4px;">No. Reg. MET: <code><?php echo esc_html( $no_reg_met ); ?></code></div>
                    <?php endif; ?>
                </td>
                <td style="height: 65px; vertical-align: bottom; padding: 8px;">
                    <div style="font-size: 11px;">Tanggal : &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</div>
                </td>
                <td style="padding: 8px;"></td>
            </tr>
        </tbody>
    </table>

</div>
