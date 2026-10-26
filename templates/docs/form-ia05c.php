<?php
/**
 * Template FR.IA.05.C — Lembar Jawaban Pilihan Ganda
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

// Ambil jumlah soal dari instrumen berdasarkan skema_id
$skema_id    = ! empty( $kegiatan->skema_id ) ? (int) $kegiatan->skema_id : ( ! empty( $skema['id'] ) ? (int) $skema['id'] : 0 );
$instrument  = ! empty( $ia05_override ) ? $ia05_override : LSP_Cert_IA05::get_instrument( $skema_id, $paket_muk_id ?? 0 );
$jumlah_soal = ! empty( $instrument['jumlah_soal'] ) ? max( 1, (int) $instrument['jumlah_soal'] ) : 10;

// Ambil daftar unit kompetensi
$total_units = ! empty( $units_skkni ) && is_array( $units_skkni ) ? count( $units_skkni ) : 0;
?>

<div class="lsp-doc-page lsp-doc-ia05c">

    <!-- Header Dokumen -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.IA.05.C.&nbsp;&nbsp;&nbsp;&nbsp;LEMBAR JAWABAN PILIHAN GANDA
            </td>
        </tr>
    </table>

    <!-- Tabel 1: Identitas Skema, TUK, Asesor, Asesi, dan Waktu -->
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

    <!-- Tabel 2: Daftar Unit Kompetensi (3 Kolom: No, Kode Unit, Judul Unit) -->
    <div style="font-weight: bold; font-size: 10pt; margin-bottom: 6px; text-transform: uppercase;">
        Daftar Unit Kompetensi
    </div>
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 18px;">
        <thead>
            <tr style="background-color: #f1f5f9; text-align: center;">
                <th style="width: 8%; padding: 6px 4px; font-size: 9pt;">No.</th>
                <th style="width: 25%; padding: 6px 8px; font-size: 9pt;">Kode Unit</th>
                <th style="width: 67%; padding: 6px 10px; font-size: 9pt; text-align: left;">Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $units_skkni ) ) : ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 12px; font-style: italic; color: #64748b;">
                        Data unit kompetensi untuk skema ini belum tersedia.
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ( $units_skkni as $idx => $unit ) : 
                    $u_kode  = is_object( $unit ) ? ( $unit->kode_unit ?? ( $unit->kode ?? '' ) ) : ( $unit['kode'] ?? ( $unit['kode_unit'] ?? '' ) );
                    $u_judul = is_object( $unit ) ? ( $unit->judul_unit ?? ( $unit->judul ?? ( $unit->title ?? '' ) ) ) : ( $unit['judul'] ?? ( $unit['title'] ?? ( $unit['judul_unit'] ?? '' ) ) );
                ?>
                    <tr>
                        <td style="text-align: center; vertical-align: middle; padding: 5px 4px; font-size: 9pt;">
                            <?php echo ( $idx + 1 ); ?>.
                        </td>
                        <td style="font-family: monospace; font-size: 9pt; padding: 5px 8px; vertical-align: middle; text-align: center;">
                            <?php echo esc_html( $u_kode ); ?>
                        </td>
                        <td style="padding: 5px 10px; font-size: 9pt; vertical-align: middle;">
                            <?php echo esc_html( $u_judul ); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tabel 3: Lembar Jawaban Pertanyaan Tertulis – Pilihan Ganda -->
    <div style="font-weight: bold; font-size: 10pt; margin-bottom: 8px;">
        Lembar Jawaban Pertanyaan Tertulis – Pilihan Ganda:
    </div>

    <?php
    $half_rows = (int) ceil( $jumlah_soal / 2 );
    ?>
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 18px;">
        <thead>
            <tr style="background-color: #f1f5f9; text-align: center;">
                <th rowspan="2" style="width: 7%; padding: 5px 4px; font-size: 8.5pt;">No.</th>
                <th rowspan="2" style="width: 25%; padding: 5px 6px; font-size: 8.5pt;">Jawaban</th>
                <th colspan="2" style="width: 18%; padding: 5px 4px; font-size: 8.5pt;">Pencapaian</th>
                <th rowspan="2" style="width: 7%; padding: 5px 4px; font-size: 8.5pt;">No.</th>
                <th rowspan="2" style="width: 25%; padding: 5px 6px; font-size: 8.5pt;">Jawaban</th>
                <th colspan="2" style="width: 18%; padding: 5px 4px; font-size: 8.5pt;">Pencapaian</th>
            </tr>
            <tr style="background-color: #f8fafc; text-align: center;">
                <th style="width: 9%; padding: 4px 2px; font-size: 8pt;">Ya</th>
                <th style="width: 9%; padding: 4px 2px; font-size: 8pt;">Tidak</th>
                <th style="width: 9%; padding: 4px 2px; font-size: 8pt;">Ya</th>
                <th style="width: 9%; padding: 4px 2px; font-size: 8pt;">Tidak</th>
            </tr>
        </thead>
        <tbody>
            <?php for ( $row = 1; $row <= $half_rows; $row++ ) : 
                $left_idx  = $row;
                $right_idx = $row + $half_rows;
            ?>
                <tr>
                    <!-- Sisi Kiri -->
                    <td style="text-align: center; padding: 5px 4px; font-size: 9pt; font-weight: bold;">
                        <?php echo $left_idx; ?>.
                    </td>
                    <td style="padding: 5px 6px; text-align: center;">&nbsp;</td>
                    <td style="text-align: center; padding: 5px 2px; font-size: 12pt;">☐</td>
                    <td style="text-align: center; padding: 5px 2px; font-size: 12pt;">☐</td>

                    <!-- Sisi Kanan -->
                    <?php if ( $right_idx <= $jumlah_soal ) : ?>
                        <td style="text-align: center; padding: 5px 4px; font-size: 9pt; font-weight: bold;">
                            <?php echo $right_idx; ?>.
                        </td>
                        <td style="padding: 5px 6px; text-align: center;">&nbsp;</td>
                        <td style="text-align: center; padding: 5px 2px; font-size: 12pt;">☐</td>
                        <td style="text-align: center; padding: 5px 2px; font-size: 12pt;">☐</td>
                    <?php else : ?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    <?php endif; ?>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- Bagian Umpan Balik untuk Asesi & Pengesahan -->
    <div class="lsp-ia05c-avoid-break">
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
            <tr>
                <td style="width: 25%; font-weight: bold; vertical-align: top; padding: 8px 10px; font-size: 9pt; background: #f8fafc;">
                    Umpan balik untuk asesi
                </td>
                <td style="width: 3%; text-align: center; vertical-align: top; padding: 8px 0;">:</td>
                <td style="padding: 8px 10px; font-size: 9pt; line-height: 1.6;">
                    Aspek pengetahuan seluruh unit kompetensi yang diujikan (tercapai/ belum tercapai)*<br>
                    Tuliskan unit/elemen/KUK jika belum tercapai:<br>
                    <div style="border-bottom: 1px dotted #64748b; height: 18px; margin: 4px 0;"></div>
                    <div style="border-bottom: 1px dotted #64748b; height: 18px; margin: 4px 0;"></div>
                </td>
            </tr>
        </table>

        <!-- Kolom Tanda Tangan Asesi dan Asesor -->
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f1f5f9; text-align: center;">
                    <th style="width: 50%; padding: 6px 10px; font-size: 9pt;">Asesi</th>
                    <th style="width: 50%; padding: 6px 10px; font-size: 9pt;">Asesor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px 14px; vertical-align: top; font-size: 9pt; line-height: 1.6;">
                        <strong>Nama :</strong> <?php echo esc_html( $nama_asesi ); ?><br><br>
                        <strong>Tanda tangan / Tanggal :</strong><br><br><br><br>
                        <div style="border-bottom: 1px dotted #000; width: 75%; margin-top: 5px;"></div>
                    </td>
                    <td style="padding: 10px 14px; vertical-align: top; font-size: 9pt; line-height: 1.6;">
                        <strong>Nama :</strong> <?php echo esc_html( $nama_asesor ); ?><br>
                        <strong>No. Reg. MET :</strong> <?php echo esc_html( $no_reg_met ); ?><br><br>
                        <strong>Tanda tangan / Tanggal :</strong><br><br><br>
                        <div style="border-bottom: 1px dotted #000; width: 75%; margin-top: 5px;"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
