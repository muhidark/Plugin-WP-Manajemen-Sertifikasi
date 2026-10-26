<?php
/**
 * Template FR.MAPA.02 — Peta Instrumen Asesmen Hasil Pendekatan Asesmen dan Perencanaan Asesmen
 * Standar BNSP & Dokumen Resmi LSP Komunikasi Indonesia
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Tanggal penyusunan dokumen MAPA.02: Backdate otomatis H-2 dari tanggal pelaksanaan kegiatan
$base_mapa_date = ! empty( $kegiatan->tanggal_pelaksanaan ) 
    ? $kegiatan->tanggal_pelaksanaan 
    : ( ! empty( $pendaftaran->created_at ) ? $pendaftaran->created_at : current_time( 'mysql' ) );

$tgl_mapa02 = date_i18n( 'd F Y', strtotime( '-2 days', strtotime( $base_mapa_date ) ) );
?>

<div class="lsp-doc-page lsp-doc-mapa02">

    <!-- Header Dokumen FR.MAPA.02 -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.MAPA.02.&nbsp;&nbsp;&nbsp;&nbsp;PETA INSTRUMEN ASESMEN HASIL PENDEKATAN ASESMEN DAN PERENCANAAN ASESMEN*
            </td>
        </tr>
    </table>

    <!-- Header Skema Sertifikasi -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
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
        <?php if ( ! empty( $nama_paket_muk ) ) : ?>
        <tr>
            <td colspan="4" style="background: #f8fafc; font-size: 11px; padding: 4px 10px; color: #334155;">
                <strong>📦 Paket MUK:</strong> <?php echo esc_html( $nama_paket_muk ); ?> (Versi: <code><?php echo esc_html( $kode_versi_muk ); ?></code>)
            </td>
        </tr>
        <?php endif; ?>
    </table>

    <!-- Daftar Unit Kompetensi -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <thead>
            <tr style="background: #f1f5f9; text-align: center; font-weight: bold;">
                <th width="8%" style="padding: 6px; text-align: center;">No.</th>
                <th width="28%" style="padding: 6px; text-align: center;">Kode Unit</th>
                <th width="64%" style="padding: 6px; text-align: left;">Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $units_skkni ) ) : ?>
                <tr>
                    <td colspan="3" style="text-align: center; color: #dc2626; padding: 12px;">
                        <em>Data Unit Kompetensi untuk skema ini belum tersedia di Master Skema. Silakan lengkapi di Master Skema terlebih dahulu.</em>
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ( $units_skkni as $idx => $unit ) : ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $idx + 1; ?></td>
                        <td style="text-align: center; font-family: monospace; font-size: 11.5px;"><?php echo esc_html( $unit['kode'] ); ?></td>
                        <td><?php echo esc_html( $unit['judul'] ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tabel Instrumen Asesmen -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
        <thead>
            <tr style="background: #f1f5f9; text-align: center; font-weight: bold;">
                <th rowspan="2" width="6%" style="text-align: center; vertical-align: middle; padding: 6px 4px;">No.</th>
                <th rowspan="2" width="64%" style="text-align: center; vertical-align: middle; padding: 6px 8px;">INSTRUMEN ASESMEN</th>
                <th colspan="5" width="30%" style="text-align: center; padding: 6px 4px;">Potensi Asesi **</th>
            </tr>
            <tr style="background: #f8fafc; text-align: center; font-weight: bold;">
                <th width="6%" style="text-align: center; padding: 4px 2px;">1</th>
                <th width="6%" style="text-align: center; padding: 4px 2px;">2</th>
                <th width="6%" style="text-align: center; padding: 4px 2px;">3</th>
                <th width="6%" style="text-align: center; padding: 4px 2px;">4</th>
                <th width="6%" style="text-align: center; padding: 4px 2px;">5</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center; vertical-align: middle;">1.</td>
                <td>FR.IA.01. CL - Ceklis Observasi Aktivitas Di Tempat Kerja atau Tempat Kerja Simulasi</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: middle;">2.</td>
                <td>FR.IA.02. TPD - Tugas Praktik Demonstrasi</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: middle;">3.</td>
                <td>FR.IA.03. PMO - Pertanyaan Untuk Mendukung Observasi</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
            </tr>
            <tr>
                <td rowspan="2" style="text-align: center; vertical-align: middle;">4.</td>
                <td>4. a FR.IA.04A. DIT - Daftar Instruksi Terstruktur ( Penjelasan Singkat Proyek Terkait Pekerjaan / Kegiatan Terstruktur Lainnya )</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
            </tr>
            <tr>
                <td>4.b FR.IA.04B. DIT - Daftar Instruksi Terstruktur ( Penjelasan Kegiatan Terstruktur Lainnya )</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: middle;">5.</td>
                <td>FR.IA.05. DPT - Daftar Pertanyaan Tertulis Pilihan Ganda</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: middle;">6.</td>
                <td>FR.IA.06. DPT – Daftar Pertanyaan Tertulis Pilihan Esai</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: middle;">7.</td>
                <td>FR.IA.07. DPL – Daftar Pertanyaan Lisan</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: middle;">8.</td>
                <td>FR.IA.08. CVP - Ceklis Verifikasi Portofolio</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: middle;">9.</td>
                <td>FR.IA.09. PW - Pertanyaan Wawancara</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: middle;">10.</td>
                <td>FR.IA.10. VPK - Verifikasi Pihak Ketiga</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: middle;">11.</td>
                <td>FR.IA.11. CRP - Ceklis Reviu Produk</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
                <td style="text-align: center; font-size: 14px; vertical-align: middle;">☐</td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 11px; margin-top: 2px; margin-bottom: 12px; color: #475569; font-style: italic;">
        *) diisi berdasarkan hasil penentuan pendekatan asesmen dan perencanaan asesmen
    </div>

    <!-- Penyusun dan Validator -->
    <div style="font-weight: bold; margin-top: 10px; margin-bottom: 6px; font-size: 12.5px;">
        Penyusun dan Validator
    </div>
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 12px; page-break-inside: avoid;">
        <tr>
            <td width="28%" style="padding: 6px 10px;">Nama Penyusun</td>
            <td width="3%" style="text-align: center;">:</td>
            <td style="padding: 6px 10px;"><strong><?php echo esc_html( $penyusun['name'] ?? 'Masriah' ); ?></strong></td>
        </tr>
        <tr>
            <td style="padding: 6px 10px;">No. Reg.MET.</td>
            <td style="text-align: center;">:</td>
            <td style="padding: 6px 10px; font-family: monospace;"><?php echo esc_html( $penyusun['no_reg'] ?? 'MET.000.032423 2019' ); ?></td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; vertical-align: middle;">Tandatangan dan Tanggal</td>
            <td style="text-align: center; vertical-align: middle;">:</td>
            <td style="padding: 6px 10px; height: 50px; vertical-align: middle;">
                <div style="display: flex; align-items: center; justify-content: space-between; min-height: 40px;">
                    <span style="color: #94a3b8; font-size: 10.5px;">( Tanda Tangan Penyusun )</span>
                    <strong style="margin-right: 25px;"><?php echo esc_html( $tgl_mapa02 ); ?></strong>
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding: 6px 10px;">Nama Validator</td>
            <td style="text-align: center;">:</td>
            <td style="padding: 6px 10px;"><strong><?php echo esc_html( $validator['name'] ?? 'Eko Prasyanto' ); ?></strong></td>
        </tr>
        <tr>
            <td style="padding: 6px 10px;">No. Reg.MET.</td>
            <td style="text-align: center;">:</td>
            <td style="padding: 6px 10px; font-family: monospace;"><?php echo esc_html( $validator['no_reg'] ?? 'MET.000.014249 2019' ); ?></td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; vertical-align: middle;">Tandatangan dan Tanggal</td>
            <td style="text-align: center; vertical-align: middle;">:</td>
            <td style="padding: 6px 10px; height: 50px; vertical-align: middle;">
                <div style="display: flex; align-items: center; justify-content: space-between; min-height: 40px;">
                    <span style="color: #94a3b8; font-size: 10.5px;">( Tanda Tangan Validator )</span>
                    <strong style="margin-right: 25px;"><?php echo esc_html( $tgl_mapa02 ); ?></strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Keterangan Potensi Asesi -->
    <div style="page-break-inside: avoid; margin-top: 8px; font-size: 11px; line-height: 1.45; color: #1e293b;">
        <div style="font-weight: bold; margin-bottom: 4px;">**) Keterangan</div>
        <ol style="margin: 0; padding-left: 18px;">
            <li style="margin-bottom: 2px;">Hasil pelatihan dan / atau pendidikan, dimana Kurikulum dan fasilitas praktek mampu telusur terhadap standar kompetensi.</li>
            <li style="margin-bottom: 2px;">Hasil pelatihan dan / atau pendidikan, dimana kurikulum belum berbasis kompetensi.</li>
            <li style="margin-bottom: 2px;">Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya mampu telusur dengan standar kompetensi.</li>
            <li style="margin-bottom: 2px;">Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya belum berbasis kompetensi.</li>
            <li style="margin-bottom: 2px;">Pelatihan / belajar mandiri atau otodidak.</li>
        </ol>
    </div>

</div>
