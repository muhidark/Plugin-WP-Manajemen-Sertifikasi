<?php
/**
 * Template FR.IA.05.B — Lembar Kunci Jawaban Pertanyaan Tertulis Pilihan Ganda
 * Standar BNSP & Dokumen Resmi LSP Komunikasi Indonesia
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// KEAMANAN KETAT: Dokumen terbatas, hanya dapat diakses oleh Admin atau Asesor resmi
if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'asesor' ) && ! LSP_Cert_Integration::is_asesor( get_current_user_id() ) && ! defined( 'LSP_CERT_TESTING' ) ) {
    wp_die( 'Akses ditolak: FR.IA.05B (Lembar Kunci Jawaban) merupakan dokumen rahasia dan hanya dapat diakses oleh Admin atau Asesor.', 'Akses Ditolak', array( 'response' => 403 ) );
}

// Tanggal pelaksanaan kegiatan
$tanggal_raw     = ! empty( $kegiatan->tanggal_pelaksanaan ) ? $kegiatan->tanggal_pelaksanaan : '';
$tgl_pelaksanaan = ! empty( $tanggal_raw ) ? date_i18n( 'd F Y', strtotime( $tanggal_raw ) ) : '';

// Tanggal penyusunan dokumen (H-2 dari tanggal kegiatan)
$tgl_penyusunan = ! empty( $tanggal_raw ) ? date_i18n( 'd F Y', strtotime( '-2 days', strtotime( $tanggal_raw ) ) ) : date_i18n( 'd F Y' );

// Data Penyusun & Validator
$nama_penyusun  = ! empty( $penyusun['name'] ) ? $penyusun['name'] : '';
$met_penyusun   = ! empty( $penyusun['no_reg'] ) ? $penyusun['no_reg'] : '';
$nama_validator = ! empty( $validator['name'] ) ? $validator['name'] : '';
$met_validator  = ! empty( $validator['no_reg'] ) ? $validator['no_reg'] : '';

// Ambil instrumen kunci jawaban berdasarkan skema_id
$skema_id     = ! empty( $kegiatan->skema_id ) ? (int) $kegiatan->skema_id : ( ! empty( $skema['id'] ) ? (int) $skema['id'] : 0 );
$instrument   = ! empty( $ia05_override ) ? $ia05_override : LSP_Cert_IA05::get_instrument( $skema_id, $paket_muk_id ?? 0 );
$kunci_raw    = ! empty( $instrument['kunci'] ) ? $instrument['kunci'] : '';
$jumlah_soal  = ! empty( $instrument['jumlah_soal'] ) ? (int) $instrument['jumlah_soal'] : 10;
$parsed_kunci = LSP_Cert_IA05::parse_kunci_jawaban( $kunci_raw, $jumlah_soal );

// Ambil daftar unit kompetensi
$total_units = ! empty( $units_skkni ) && is_array( $units_skkni ) ? count( $units_skkni ) : 0;
?>

<div class="lsp-doc-page lsp-doc-ia05b">

    <!-- Header Dokumen -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.IA.05.B.&nbsp;&nbsp;&nbsp;&nbsp;LEMBAR KUNCI JAWABAN PERTANYAAN TERTULIS PILIHAN GANDA
            </td>
        </tr>
    </table>

    <!-- Tabel 1: Identitas Skema & TUK -->
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

    <!-- Bagian Kunci Jawaban -->
    <div style="font-weight: bold; font-size: 10pt; margin-bottom: 8px;">
        Kunci Jawaban Pertanyaan Tertulis – Pilihan Ganda:
    </div>

    <?php if ( empty( $kunci_raw ) ) : ?>
        <div class="lsp-alert-box" style="border: 1px solid #dc2626; background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 4px; margin: 15px 0; font-size: 10pt;">
            <strong>Perhatian:</strong> Kunci jawaban pilihan ganda untuk skema ini belum diatur. Silakan masukkan kunci jawaban terlebih dahulu pada menu <em>Sertifikasi &gt; Instrumen FR.IA.05</em>.
        </div>
    <?php else : ?>
        <?php
        // Susun tabel kunci jawaban menjadi 4 kolom berdampingan (No | Kunci | No | Kunci)
        $total_items = max( (int) $jumlah_soal, count( $parsed_kunci ) );
        if ( $total_items < 1 ) {
            $total_items = 10;
        }
        $half_items = (int) ceil( $total_items / 2 );
        ?>
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f1f5f9; text-align: center;">
                    <th style="width: 12%; padding: 6px 8px; font-size: 9pt;">No.</th>
                    <th style="width: 38%; padding: 6px 8px; font-size: 9pt;">Kunci Jawaban</th>
                    <th style="width: 12%; padding: 6px 8px; font-size: 9pt;">No.</th>
                    <th style="width: 38%; padding: 6px 8px; font-size: 9pt;">Kunci Jawaban</th>
                </tr>
            </thead>
            <tbody>
                <?php for ( $row = 1; $row <= $half_items; $row++ ) : 
                    $left_num  = $row;
                    $left_ans  = $parsed_kunci[ $left_num ] ?? '';
                    $right_num = $row + $half_items;
                    $right_ans = $parsed_kunci[ $right_num ] ?? '';
                ?>
                    <tr>
                        <td style="text-align: center; padding: 6px 8px; font-size: 9.5pt; font-weight: bold;">
                            <?php echo $left_num; ?>.
                        </td>
                        <td style="text-align: center; padding: 6px 8px; font-size: 10.5pt; font-weight: bold; color: #1e293b;">
                            <?php echo esc_html( $left_ans ); ?>
                        </td>
                        <td style="text-align: center; padding: 6px 8px; font-size: 9.5pt; font-weight: bold;">
                            <?php echo ( $right_num <= $total_items ) ? ( $right_num . '.' ) : ''; ?>
                        </td>
                        <td style="text-align: center; padding: 6px 8px; font-size: 10.5pt; font-weight: bold; color: #1e293b;">
                            <?php echo ( $right_num <= $total_items ) ? esc_html( $right_ans ) : ''; ?>
                        </td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Bagian Penyusun dan Validator Sesuai Format BNSP -->
    <div class="lsp-ia05b-avoid-break" style="margin-top: 25px;">
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
