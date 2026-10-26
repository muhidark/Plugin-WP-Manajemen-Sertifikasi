<?php
/**
 * Template FR.IA.03 — Pertanyaan untuk Mendukung Observasi (PMO)
 * Standar BNSP & LSP Komunikasi Indonesia
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Tanggal pelaksanaan kegiatan
$tanggal_raw     = ! empty( $kegiatan->tanggal_pelaksanaan ) ? $kegiatan->tanggal_pelaksanaan : '';
$tgl_pelaksanaan = ! empty( $tanggal_raw ) ? date_i18n( 'd F Y', strtotime( $tanggal_raw ) ) : '';
$tgl_penyusunan  = ! empty( $tanggal_raw ) ? date_i18n( 'd F Y', strtotime( '-2 days', strtotime( $tanggal_raw ) ) ) : date_i18n( 'd F Y' );

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

if ( empty( $nama_asesor ) ) {
    $nama_asesor = ! empty( $penyusun['name'] ) ? $penyusun['name'] : '...................................................';
}

// Tim Penyusun & Validator
$nama_penyusun  = ! empty( $penyusun['name'] ) ? $penyusun['name'] : 'Masriah';
$met_penyusun   = ! empty( $penyusun['no_reg'] ) ? $penyusun['no_reg'] : 'MET.000.032423 2019';
$nama_validator = ! empty( $validator['name'] ) ? $validator['name'] : 'Eko Prasyanto';
$met_validator  = ! empty( $validator['no_reg'] ) ? $validator['no_reg'] : 'MET.000.014249 2019';

$skema_id    = ! empty( $kegiatan->skema_id ) ? (int) $kegiatan->skema_id : ( ! empty( $skema['id'] ) ? (int) $skema['id'] : 0 );
$units_skkni = ! empty( $units_skkni ) && is_array( $units_skkni ) ? $units_skkni : LSP_Cert_Integration::get_unit_kompetensi( $skema_id );
?>

<div class="lsp-doc-page lsp-doc-ia03">

    <!-- Header Dokumen Sesuai Format BNSP -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.IA.03.&nbsp;&nbsp;&nbsp;&nbsp;PERTANYAAN UNTUK MENDUKUNG OBSERVASI
            </td>
        </tr>
    </table>

    <!-- Tabel 1: Identitas Skema Sertifikasi, TUK, Asesor, Asesi, dan Tanggal -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; border-top: none; margin-bottom: 8px;">
        <tbody>
            <tr>
                <td rowspan="2" style="width: 35%; vertical-align: middle; font-weight: bold; padding: 6px 10px;">
                    Skema Sertifikasi<br>
                    <span style="font-weight: normal; font-size: 9pt;">(KKNI/Okupasi/Klaster)*</span>
                </td>
                <td style="width: 15%; padding: 6px 10px; font-weight: bold;">Judul</td>
                <td style="width: 2%; padding: 6px 4px; text-align: center;">:</td>
                <td style="padding: 6px 10px; font-weight: bold; font-size: 10pt;">
                    <?php echo esc_html( $skema['title'] ?? '-' ); ?>
                </td>
            </tr>
            <tr>
                <td style="padding: 6px 10px; font-weight: bold;">Nomor</td>
                <td style="padding: 6px 4px; text-align: center;">:</td>
                <td style="padding: 6px 10px; font-family: monospace; font-size: 9.5pt;">
                    <?php echo esc_html( $skema['kode'] ?? '-' ); ?>
                </td>
            </tr>
            <?php if ( ! empty( $nama_paket_muk ) ) : ?>
            <tr>
                <td colspan="2" style="font-weight: bold; padding: 6px 10px; background: #f8fafc;">Paket MUK / Versi</td>
                <td style="padding: 6px 4px; text-align: center; background: #f8fafc;">:</td>
                <td style="padding: 6px 10px; background: #f8fafc; font-size: 9pt;">
                    <strong><?php echo esc_html( $nama_paket_muk ); ?></strong> (<code><?php echo esc_html( $kode_versi_muk ); ?></code>)
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <td colspan="2" style="font-weight: bold; padding: 6px 10px;">TUK</td>
                <td style="padding: 6px 4px; text-align: center;">:</td>
                <td style="padding: 6px 10px;">
                    ☑ Sewaktu &nbsp;&nbsp;&nbsp; ☐ Tempat Kerja &nbsp;&nbsp;&nbsp; ☐ Mandiri*
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight: bold; padding: 6px 10px;">Nama Asesor</td>
                <td style="padding: 6px 4px; text-align: center;">:</td>
                <td style="padding: 6px 10px; font-weight: bold;">
                    <?php echo esc_html( $nama_asesor ); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight: bold; padding: 6px 10px;">Nama Asesi</td>
                <td style="padding: 6px 4px; text-align: center;">:</td>
                <td style="padding: 6px 10px; font-weight: bold;">
                    <?php echo esc_html( $nama_asesi ); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight: bold; padding: 6px 10px;">Tanggal</td>
                <td style="padding: 6px 4px; text-align: center;">:</td>
                <td style="padding: 6px 10px;">
                    <?php echo esc_html( $tgl_pelaksanaan ); ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 8.5pt; font-style: italic; color: #475569; margin-bottom: 12px;">
        *Coret yang tidak perlu
    </div>

    <!-- Kotak Panduan Bagi Asesor -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
        <thead>
            <tr style="background: #f1f5f9;">
                <th style="padding: 6px 10px; font-size: 9.5pt; text-align: left; text-transform: uppercase;">
                    PANDUAN BAGI ASESOR
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 10px 14px; font-size: 8.5pt; line-height: 1.6;">
                    <ul style="margin: 0; padding-left: 18px; list-style-type: disc;">
                        <li>Formulir ini diisi oleh asesor kompetensi sebelum, pada saat, atau setelah melakukan asesmen dengan metode observasi demonstrasi.</li>
                        <li>Pertanyaan dibuat dengan tujuan untuk menggali dimensi kompetensi, batasan variabel, dan aspek kritis yang relevan dengan skenario tugas dan praktik demonstrasi.</li>
                        <li>Jika pertanyaan disampaikan sebelum asesi melakukan praktik demonstrasi, maka pertanyaan dibuat berkaitan dengan aspek K3L, SOP, penggunaan peralatan dan perlengkapan.</li>
                        <li>Jika setelah asesi melakukan praktik demonstrasi terdapat item pertanyaan pendukung observasi telah terpenuhi, maka pertanyaan tersebut tidak perlu ditanyakan lagi dan cukup memberi catatan bahwa sudah terpenuhi pada saat tugas praktek demonstrasi pada kolom tanggapan.</li>
                        <li>Jika pada saat observasi ada hal yang perlu dikonfirmasi sedangkan di instrumen daftar pertanyaan pendukung observasi tidak ada, maka asesor dapat memberikan pertanyaan dengan syarat pertanyaan harus berkaitan dengan tugas praktek demonstrasi. Jika dilakukan, asesor harus mencatat dalam instrumen pertanyaan pendukung observasi.</li>
                        <li>Tanggapan asesi ditulis pada kolom tanggapan.</li>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel Daftar Unit Kompetensi Berbasis Kelompok Pekerjaan (Col 1: Kelompok Pekerjaan) -->
    <div style="font-weight: bold; font-size: 10pt; margin-bottom: 6px; text-transform: uppercase;">
        Daftar Unit Kompetensi Berdasarkan Kelompok Pekerjaan
    </div>
    <?php LSP_Cert_Kelompok_Pekerjaan::render_unit_table_html( $skema_id, true, $paket_muk_id ?? 0 ); ?>

    <!-- Tabel Pertanyaan Pendukung Observasi Berdasarkan Dimensi Kompetensi -->
    <?php
    $ia03_data = class_exists( 'LSP_Cert_IA03' ) ? LSP_Cert_IA03::get_instrument( $skema_id, $paket_muk_id ?? 0 ) : array();
    $questions = ! empty( $ia03_data['questions'] ) ? $ia03_data['questions'] : array();
    if ( empty( $questions ) && class_exists( 'LSP_Cert_IA03' ) ) {
        $def = LSP_Cert_IA03::get_default_instrument( $skema_id );
        $questions = $def['questions'];
    }

    $dim_labels = array(
        'CMS'  => 'CMS - Contingency Management Skills',
        'TRS'  => 'TRS - Transfer Skills',
        'JRES' => 'JRES - Job/Role Environment Skills',
        'TS'   => 'TS - Task Skills',
        'TMS'  => 'TMS - Task Management Skills',
    );
    ?>
    <div style="font-weight: bold; font-size: 10pt; margin-top: 18px; margin-bottom: 6px; text-transform: uppercase;">
        Daftar Pertanyaan Mendukung Observasi
    </div>
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
        <thead>
            <tr style="background: #f1f5f9; text-align: center;">
                <th rowspan="2" style="width: 5%; padding: 6px 4px; font-size: 8.5pt;">No.</th>
                <th rowspan="2" style="width: 83%; padding: 6px 10px; font-size: 8.5pt; text-align: left;">Pertanyaan &amp; Tanggapan Asesi</th>
                <th colspan="2" style="width: 12%; padding: 4px; font-size: 8.5pt;">Pencapaian</th>
            </tr>
            <tr style="background: #f1f5f9; text-align: center;">
                <th style="width: 6%; padding: 4px; font-size: 8pt;">Ya</th>
                <th style="width: 6%; padding: 4px; font-size: 8pt;">Tdk</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $q_num = 1;
            foreach ( $questions as $q_item ) : 
                $dim_code = $q_item['dimensi'] ?? 'TS';
                $dim_name = $dim_labels[ $dim_code ] ?? $dim_code;
                $pertanyaan_text = $q_item['pertanyaan'] ?? '';
            ?>
                <tr>
                    <td rowspan="2" style="text-align: center; vertical-align: top; padding: 8px 4px; font-weight: bold; font-size: 9pt;"><?php echo esc_html( $q_num++ ); ?>.</td>
                    <td style="padding: 8px 10px; font-size: 9pt;">
                        <strong>(<?php echo esc_html( $dim_name ); ?>)</strong><br>
                        <?php echo nl2br( esc_html( $pertanyaan_text ) ); ?>
                    </td>
                    <td rowspan="2" style="text-align: center; vertical-align: middle; padding: 6px; font-size: 10pt;">☐</td>
                    <td rowspan="2" style="text-align: center; vertical-align: middle; padding: 6px; font-size: 10pt;">☐</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; background: #fafafa; font-size: 8.5pt; color: #475569;">
                        <em>Tanggapan Asesi:</em><br>
                        <div style="height: 38px;"></div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Kotak Umpan Balik untuk Asesi -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
        <tr>
            <td style="padding: 10px 14px;">
                <div style="font-weight: bold; font-size: 9pt; margin-bottom: 35px;">Umpan balik untuk asesi:</div>
            </td>
        </tr>
    </table>

    <!-- Bagian Pengesahan Asesi dan Asesor -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding: 10px 14px;">
                <div style="font-weight: bold; font-size: 9pt; margin-bottom: 6px;">Asesi :</div>
                <table style="width: 100%; border: none; font-size: 8.5pt;">
                    <tr>
                        <td style="width: 40%; border: none; padding: 3px 0;">Nama</td>
                        <td style="width: 4%; border: none; padding: 3px 0;">:</td>
                        <td style="border: none; padding: 3px 0; font-weight: bold;"><?php echo esc_html( $nama_asesi ); ?></td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 3px 0; vertical-align: bottom;">Tanda tangan &amp; Tanggal</td>
                        <td style="border: none; padding: 3px 0; vertical-align: bottom;">:</td>
                        <td style="border: none; padding: 25px 0 2px 0;">................................................</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding: 10px 14px;">
                <div style="font-weight: bold; font-size: 9pt; margin-bottom: 6px;">Asesor :</div>
                <table style="width: 100%; border: none; font-size: 8.5pt;">
                    <tr>
                        <td style="width: 40%; border: none; padding: 3px 0;">Nama</td>
                        <td style="width: 4%; border: none; padding: 3px 0;">:</td>
                        <td style="border: none; padding: 3px 0; font-weight: bold;"><?php echo esc_html( $nama_asesor ); ?></td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 3px 0;">No. Reg</td>
                        <td style="border: none; padding: 3px 0;">:</td>
                        <td style="border: none; padding: 3px 0; font-family: monospace;"><?php echo esc_html( $no_reg_met ); ?></td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 3px 0; vertical-align: bottom;">Tanda tangan &amp; Tanggal</td>
                        <td style="border: none; padding: 3px 0; vertical-align: bottom;">:</td>
                        <td style="border: none; padding: 25px 0 2px 0;">................................................</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Tabel Penyusun dan Validator Sesuai Standar Resmi LSP -->
    <div style="font-weight: bold; font-size: 9.5pt; margin-bottom: 6px; text-transform: uppercase;">
        PENYUSUN DAN VALIDATOR
    </div>
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 8.5pt;">
        <thead>
            <tr style="background: #f1f5f9; text-align: center;">
                <th style="width: 18%; padding: 6px 8px;">STATUS</th>
                <th style="width: 6%; padding: 6px 4px;">NO</th>
                <th style="width: 32%; padding: 6px 10px; text-align: left;">NAMA</th>
                <th style="width: 24%; padding: 6px 8px;">NOMOR MET</th>
                <th style="width: 20%; padding: 6px 8px;">TANDA TANGAN &amp; TANGGAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center; font-weight: bold; padding: 6px 8px;">PENYUSUN</td>
                <td style="text-align: center; padding: 6px 4px;">1</td>
                <td style="padding: 6px 10px; font-weight: 500;"><?php echo esc_html( $nama_penyusun ); ?></td>
                <td style="text-align: center; font-family: monospace; padding: 6px 8px;"><?php echo esc_html( $met_penyusun ); ?></td>
                <td style="text-align: center; padding: 6px 8px; font-size: 8pt;"><?php echo esc_html( $tgl_penyusunan ); ?></td>
            </tr>
            <tr>
                <td style="text-align: center; font-weight: bold; padding: 6px 8px;">VALIDATOR</td>
                <td style="text-align: center; padding: 6px 4px;">1</td>
                <td style="padding: 6px 10px; font-weight: 500;"><?php echo esc_html( $nama_validator ); ?></td>
                <td style="text-align: center; font-family: monospace; padding: 6px 8px;"><?php echo esc_html( $met_validator ); ?></td>
                <td style="text-align: center; padding: 6px 8px; font-size: 8pt;"><?php echo esc_html( $tgl_penyusunan ); ?></td>
            </tr>
        </tbody>
    </table>

</div>
