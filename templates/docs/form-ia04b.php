<?php
/**
 * Template FR.IA.04B — Penilaian Proyek Singkat atau Kegiatan Terstruktur Lainnya
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
if ( empty( $no_reg_met ) ) {
    $no_reg_met = ! empty( $penyusun['no_reg'] ) ? $penyusun['no_reg'] : '...................................................';
}

// Data Penyusun & Validator
$nama_penyusun  = ! empty( $penyusun['name'] ) ? $penyusun['name'] : 'Masriah';
$met_penyusun   = ! empty( $penyusun['no_reg'] ) ? $penyusun['no_reg'] : 'MET.000.032423 2019';
$nama_validator = ! empty( $validator['name'] ) ? $validator['name'] : 'Eko Prasyanto';
$met_validator  = ! empty( $validator['no_reg'] ) ? $validator['no_reg'] : 'MET.000.014249 2019';

// Ambil instrumen materi IA.04 berdasarkan skema_id
$skema_id       = ! empty( $kegiatan->skema_id ) ? (int) $kegiatan->skema_id : ( ! empty( $skema['id'] ) ? (int) $skema['id'] : 0 );
$instrument     = ! empty( $ia04_override ) ? $ia04_override : LSP_Cert_IA04::get_instrument( $skema_id, $paket_muk_id ?? 0 );
$judul_kegiatan = ! empty( $instrument['judul_kegiatan'] ) ? $instrument['judul_kegiatan'] : '...................................................';
$questions      = ! empty( $instrument['questions'] ) ? $instrument['questions'] : array();
?>

<div class="lsp-doc-page lsp-doc-ia04b">

    <!-- Header Dokumen -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.IA.04B.&nbsp;&nbsp;&nbsp;&nbsp;PENILAIAN PROYEK SINGKAT ATAU KEGIATAN TERSTRUKTUR LAINNYA
            </td>
        </tr>
    </table>

    <!-- Tabel 1: Identitas Skema, TUK, Judul Kegiatan, Asesor, Asesi, dan Tanggal -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; border-top: none; margin-bottom: 12px;">
        <tr>
            <td rowspan="2" style="width: 35%; vertical-align: middle; font-weight: bold; padding: 6px 10px;">
                Skema Sertifikasi<br>
                <span style="font-weight: normal; font-size: 9pt;">(KKNI/Okupasi/Klaster)*</span>
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
            <td colspan="2" style="padding: 6px 10px; font-weight: bold;">Judul Kegiatan Terstruktur</td>
            <td style="text-align: center; padding: 6px 0;">:</td>
            <td style="padding: 6px 10px; font-weight: 600;"><?php echo esc_html( $judul_kegiatan ); ?></td>
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
    </table>

    <div style="font-size: 8.5pt; font-style: italic; color: #475569; margin-bottom: 12px;">
        *Coret yang tidak perlu
    </div>

    <!-- Kotak Panduan Bagi Asesor Sesuai Template BNSP -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 14px;">
        <thead>
            <tr style="background-color: #f1f5f9;">
                <th style="padding: 6px 10px; font-size: 9.5pt; text-align: left;">PANDUAN BAGI ASESOR</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 8px 12px; font-size: 8.8pt; line-height: 1.5; text-align: justify;">
                    Lakukan penilaian pencapaian hasil proyek singkat atau kegiatan terstruktur lainnya melalui presentasi. Penilaian dilakukan sesuai dengan FR IA 04A. DIT. Daftar Instruksi Terstruktur (Penjelasan Proyek Singkat/ Kegiatan Terstruktur Lainnya). Pertanyaan disampaikan oleh asesor setelah asesi melakukan presentasi proyek singkat/ kegiatan terstruktur lainnya. Pertanyaan dapat dikembangkan oleh asesor berdasarkan dokumen presentasi dan atau hasil presentasi. Pertanyaan yang disampaikan untuk pemenuhan pencapaian 5 dimensi kompetensi. Isilah kolom lingkup penyajian proyek atau kegiatan terstruktur lainnya sesuai sektor/ sub-sektor/ profesi. Berikan keputusan pencapaian berdasarkan kesimpulan jawaban asesi.
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel Penilaian Aspek Proyek Singkat -->
    <div style="font-weight: bold; font-size: 10pt; margin-bottom: 6px; text-transform: uppercase;">
        Tabel Aspek Penilaian Proyek Singkat
    </div>

    <?php if ( empty( $questions ) ) : ?>
        <div class="lsp-alert-box" style="border: 1px solid #dc2626; background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 4px; margin: 15px 0; font-size: 10pt;">
            <strong>Perhatian:</strong> Daftar pertanyaan penilaian FR.IA.04B untuk skema ini belum diatur. Silakan masukkan pertanyaan pada menu <em>Sertifikasi &gt; Instrumen FR.IA.04</em>.
        </div>
    <?php else : ?>
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
            <thead>
                <tr style="background-color: #f1f5f9; text-align: center;">
                    <th colspan="4" style="padding: 6px 8px; font-size: 9pt;">Aspek Penilaian</th>
                    <th colspan="2" style="width: 12%; padding: 6px 4px; font-size: 9pt;">Pencapaian</th>
                </tr>
                <tr style="background-color: #f8fafc; text-align: center;">
                    <th style="width: 5%; padding: 6px 4px; font-size: 8.5pt;">No.</th>
                    <th style="width: 22%; padding: 6px 8px; font-size: 8.5pt;">Lingkup Penyajian proyek atau kegiatan terstruktur lainnya</th>
                    <th style="width: 47%; padding: 6px 8px; font-size: 8.5pt;">Daftar Pertanyaan</th>
                    <th style="width: 14%; padding: 6px 6px; font-size: 8.5pt;">Kesesuaian dengan standar kompetensi kerja (unit/elemen/KUK)</th>
                    <th style="width: 6%; padding: 6px 2px; font-size: 8.5pt;">Ya</th>
                    <th style="width: 6%; padding: 6px 2px; font-size: 8.5pt;">Tdk</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $questions as $idx => $q ) : 
                    $no_urutan   = $idx + 1;
                    $lingkup     = ! empty( $q['lingkup'] ) ? $q['lingkup'] : ( $q['unit_id'] ?? '-' );
                    $pertanyaan  = $q['pertanyaan'] ?? '';
                    $dimensi_str = ! empty( $q['dimensi'] ) ? ' ' . $q['dimensi'] : '';
                    $elemen_kuk  = $q['elemen_kuk'] ?? '';
                ?>
                    <tr>
                        <td style="text-align: center; vertical-align: top; padding: 8px 4px; font-size: 9pt; font-weight: bold;">
                            <?php echo $no_urutan; ?>.
                        </td>
                        <td style="vertical-align: top; padding: 8px 10px; font-size: 9pt; font-weight: 600;">
                            <?php echo esc_html( $lingkup ); ?>
                        </td>
                        <td style="vertical-align: top; padding: 8px 10px; font-size: 9pt; line-height: 1.5;">
                            <strong>Pertanyaan :</strong> <?php echo esc_html( $pertanyaan ); ?><?php echo esc_html( $dimensi_str ); ?><br><br>
                            <strong>Tanggapan :</strong><br>
                            <div style="height: 35px; border-bottom: 1px dotted #94a3b8; margin-top: 5px;"></div>
                        </td>
                        <td style="vertical-align: top; text-align: center; padding: 8px 6px; font-size: 8.5pt; font-family: monospace;">
                            <?php echo esc_html( $elemen_kuk ); ?>
                        </td>
                        <td style="text-align: center; vertical-align: middle; padding: 6px 2px; font-size: 13pt;">
                            ☐
                        </td>
                        <td style="text-align: center; vertical-align: middle; padding: 6px 2px; font-size: 13pt;">
                            ☐
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Rekomendasi Asesor & Pengesahan -->
    <div class="lsp-ia04b-avoid-break">
        <!-- Rekomendasi Asesor -->
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
            <tr>
                <td style="width: 25%; font-weight: bold; vertical-align: middle; padding: 10px; font-size: 9pt; background: #f8fafc;">
                    Rekomendasi Asesor:
                </td>
                <td style="padding: 10px 14px; font-size: 9pt; line-height: 1.6;">
                    Asesi telah memenuhi/belum memenuhi pencapaian seluruh kriteria unjuk kerja, direkomendasikan:<br>
                    <span style="display: inline-block; margin-right: 30px; margin-top: 6px; font-size: 9.5pt;">
                        ☐ &nbsp;<strong>Kompeten</strong>
                    </span>
                    <span style="display: inline-block; font-size: 9.5pt;">
                        ☐ &nbsp;<strong>Belum Kompeten</strong>
                    </span>
                </td>
            </tr>
        </table>

        <!-- Kolom Tanda Tangan Asesi dan Asesor -->
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f1f5f9; text-align: center;">
                    <th style="width: 50%; padding: 6px 10px; font-size: 9pt;">Asesi</th>
                    <th style="width: 50%; padding: 6px 10px; font-size: 9pt;">Asesor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px 14px; vertical-align: top; font-size: 9pt; line-height: 1.6; height: 95px;">
                        <strong>Nama :</strong> <?php echo esc_html( $nama_asesi ); ?><br><br>
                        <strong>Tanda tangan / Tanggal :</strong><br><br><br>
                        <div style="border-bottom: 1px dotted #000; width: 75%; margin-top: 5px;"></div>
                    </td>
                    <td style="padding: 10px 14px; vertical-align: top; font-size: 9pt; line-height: 1.6; height: 95px;">
                        <strong>Nama :</strong> <?php echo esc_html( $nama_asesor ); ?><br>
                        <strong>No. Reg :</strong> <?php echo esc_html( $no_reg_met ); ?><br><br>
                        <strong>Tanda tangan / Tanggal :</strong><br><br><br>
                        <div style="border-bottom: 1px dotted #000; width: 75%; margin-top: 5px;"></div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Bagian Penyusun dan Validator Sesuai Format BNSP -->
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
