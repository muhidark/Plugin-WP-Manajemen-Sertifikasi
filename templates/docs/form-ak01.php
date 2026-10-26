<?php
/**
 * Template FR.AK.01 — Persetujuan Asesmen dan Kerahasiaan
 * Standar BNSP & Dokumen Resmi LSP Komunikasi Indonesia
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Tanggal pelaksanaan asesmen (BUKAN H-2)
$tanggal_raw = ! empty( $kegiatan->tanggal_pelaksanaan ) ? $kegiatan->tanggal_pelaksanaan : current_time( 'mysql' );
$tgl_pelaksanaan = date_i18n( 'd F Y', strtotime( $tanggal_raw ) );
$hari_tanggal_pelaksanaan = date_i18n( 'l, d F Y', strtotime( $tanggal_raw ) );

// Kebijakan Tampilan Dokumen Cetak (Phase AK-01C):
// Menyeragamkan pilihan jenis TUK pada dokumen cetak:
// ☑ Sewaktu  ☐ Tempat Kerja  ☐ Mandiri*
$is_sewaktu      = true;
$is_tempat_kerja = false;
$is_mandiri      = false;

// Asesor & Asesi
$nama_asesor = ! empty( $asesor['name'] ) ? $asesor['name'] : ( $penyusun['name'] ?? '-' );
$nama_asesi  = ! empty( $pendaftaran->nama_lengkap ) ? $pendaftaran->nama_lengkap : '-';
?>

<div class="lsp-doc-page lsp-doc-ak01">

    <!-- Header FR.AK.01 -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td colspan="2" style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.AK.01.&nbsp;&nbsp;&nbsp;&nbsp;PERSETUJUAN ASESMEN DAN KERAHASIAAN
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 6px 10px; font-size: 11px; background: #ffffff; color: #334155; font-style: italic;">
                Persetujuan Asesmen ini untuk menjamin bahwa Asesi telah diberi arahan secara rinci tentang perencanaan dan proses asesmen
            </td>
        </tr>
    </table>

    <!-- Rincian Skema, TUK, Asesor, Asesi, Bukti & Kesepakatan -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: -1px; margin-bottom: 0;">
        <tr>
            <td width="30%" rowspan="2" valign="middle"><strong>Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)*</strong></td>
            <td width="12%">Judul</td>
            <td width="3%">:</td>
            <td><strong><?php echo esc_html( $skema['title'] ?? '-' ); ?></strong></td>
        </tr>
        <tr>
            <td>Nomor</td>
            <td>:</td>
            <td><code><?php echo esc_html( $skema['kode'] ?? '-' ); ?></code></td>
        </tr>
        <tr>
            <td colspan="2"><strong>TUK</strong></td>
            <td>:</td>
            <td>
                <span style="font-size: 13px;"><?php echo $is_sewaktu ? '☑' : '☐'; ?></span> Sewaktu &nbsp;&nbsp;&nbsp;
                <span style="font-size: 13px;"><?php echo $is_tempat_kerja ? '☑' : '☐'; ?></span> Tempat Kerja &nbsp;&nbsp;&nbsp;
                <span style="font-size: 13px;"><?php echo $is_mandiri ? '☑' : '☐'; ?></span> Mandiri*
            </td>
        </tr>
        <tr>
            <td colspan="2"><strong>Nama Asesor</strong></td>
            <td>:</td>
            <td><strong><?php echo esc_html( $nama_asesor ); ?></strong></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Nama Asesi</strong></td>
            <td>:</td>
            <td><strong><?php echo esc_html( $nama_asesi ); ?></strong></td>
        </tr>
        <tr>
            <td colspan="2" rowspan="4" valign="top"><strong>Bukti yang dikumpulkan</strong></td>
            <td rowspan="4" valign="top">:</td>
            <td style="padding: 3px 6px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                    <tr>
                        <td width="50%" style="border: none; padding: 2px 0;"><span style="font-size: 13px;">☐</span> Hasil Verifikasi Portofolio</td>
                        <td width="50%" style="border: none; padding: 2px 0;"><span style="font-size: 13px;">☐</span> Hasil Reviu Produk</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 3px 6px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                    <tr>
                        <td width="50%" style="border: none; padding: 2px 0;"><span style="font-size: 13px;">☐</span> Hasil Observasi Langsung</td>
                        <td width="50%" style="border: none; padding: 2px 0;"><span style="font-size: 13px;">☐</span> Hasil Kegiatan Terstruktur</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 3px 6px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                    <tr>
                        <td width="30%" style="border: none; padding: 2px 0;"><span style="font-size: 13px;">☐</span> Hasil Tanya Jawab</td>
                        <td width="70%" style="border: none; padding: 2px 0;">
                            <span style="font-size: 13px;">☐</span> Daftar Pertanyaan Tertulis &nbsp;&nbsp;
                            <span style="font-size: 13px;">☐</span> Daftar Pertanyaan Lisan &nbsp;&nbsp;
                            <span style="font-size: 13px;">☐</span> Daftar Pertanyaan Wawancara
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 3px 6px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                    <tr>
                        <td width="50%" style="border: none; padding: 2px 0;"><span style="font-size: 13px;">☐</span> Lainnya: …….…</td>
                        <td width="50%" style="border: none; padding: 2px 0;"><span style="font-size: 13px;">☐</span> ….…………..…………….</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" rowspan="3" valign="top"><strong>Pelaksanaan asesmen disepakati pada:</strong></td>
            <td colspan="2" style="padding: 4px 8px;">
                <strong>Hari / Tanggal :</strong> &nbsp; <?php echo esc_html( $hari_tanggal_pelaksanaan ); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 4px 8px;">
                <strong>Waktu :</strong> &nbsp; 08.00 WIB - Selesai
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 4px 8px;">
                <strong>TUK :</strong> &nbsp; 
                <?php if ( ! empty( $kegiatan->lokasi_uji ) ) : ?>
                    <strong><?php echo esc_html( $kegiatan->lokasi_uji ); ?></strong>
                    <?php if ( ! empty( $kegiatan->alamat_lokasi_uji ) ) : ?>
                        <div style="font-size: 10.5px; color: #475569; margin-top: 1px;"><?php echo esc_html( $kegiatan->alamat_lokasi_uji ); ?></div>
                    <?php endif; ?>
                    <div style="font-size: 10px; color: #64748b; margin-top: 2px;">(TUK Penyelenggara: <?php echo esc_html( $tuk['title'] ?? '-' ); ?>)</div>
                <?php else : ?>
                    <strong><?php echo esc_html( $tuk['title'] ?? '-' ); ?></strong>
                    <?php if ( ! empty( $tuk['alamat'] ) ) : ?>
                        <div style="font-size: 10.5px; color: #475569; margin-top: 1px;"><?php echo esc_html( $tuk['alamat'] ); ?></div>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- Klausul Asesi & Asesor -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: -1px; margin-bottom: 0; font-size: 11px;">
        <tr>
            <td style="padding: 7px 10px; line-height: 1.45;">
                <strong>Asesi :</strong><br>
                Bahwa saya telah mendapatkan penjelasan terkait hak dan prosedur banding asesmen dari asesor.
            </td>
        </tr>
        <tr>
            <td style="padding: 7px 10px; line-height: 1.45;">
                <strong>Asesor :</strong><br>
                Menyatakan tidak akan membuka hasil pekerjaan yang saya peroleh karena penugasan saya sebagai Asesor dalam pekerjaan Asesmen kepada siapapun atau organisasi apapun selain kepada pihak yang berwenang sehubungan dengan kewajiban saya sebagai Asesor yang ditugaskan oleh LSP.
            </td>
        </tr>
        <tr>
            <td style="padding: 7px 10px; line-height: 1.45;">
                <strong>Asesi :</strong><br>
                Saya setuju mengikuti asesmen dengan pemahaman bahwa informasi yang dikumpulkan hanya digunakan untuk pengembangan profesional dan hanya dapat diakses oleh orang tertentu saja.
            </td>
        </tr>
    </table>

    <!-- Tanda Tangan Asesor & Asesi -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: -1px; margin-bottom: 8px; page-break-inside: avoid;">
        <tr style="text-align: center; background: #f8fafc; font-size: 11px;">
            <th width="50%" style="padding: 6px 10px;">Tanda Tangan Asesor</th>
            <th width="50%" style="padding: 6px 10px;">Tanda Tangan Asesi</th>
        </tr>
        <tr>
            <td style="padding: 8px 14px; vertical-align: top; height: 95px;">
                <div style="font-size: 10px; color: #64748b; margin-bottom: 45px;">( Tanda Tangan Asesor )</div>
                <div style="font-weight: bold; font-size: 11.5px;"><?php echo esc_html( $nama_asesor ); ?></div>
                <div style="font-size: 10.5px; margin-top: 6px;">Tanggal : ………………………………</div>
            </td>
            <td style="padding: 8px 14px; vertical-align: top; height: 95px;">
                <div style="font-size: 10px; color: #64748b; margin-bottom: 45px;">( Tanda Tangan Asesi )</div>
                <div style="font-weight: bold; font-size: 11.5px;"><?php echo esc_html( $nama_asesi ); ?></div>
                <div style="font-size: 10.5px; margin-top: 6px;">Tanggal : ………………………………</div>
            </td>
        </tr>
    </table>

    <div style="font-size: 11px; margin-top: 4px; color: #475569; font-style: italic;">
        * Coret yang tidak perlu
    </div>

</div>
