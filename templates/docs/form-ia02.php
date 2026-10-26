<?php
/**
 * Template FR.IA.02 — TPD: Tugas Praktik Demonstrasi
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
$nama_penyusun  = ! empty( $penyusun['name'] ) ? $penyusun['name'] : '';
$met_penyusun   = ! empty( $penyusun['no_reg'] ) ? $penyusun['no_reg'] : '';
$nama_validator = ! empty( $validator['name'] ) ? $validator['name'] : '';
$met_validator  = ! empty( $validator['no_reg'] ) ? $validator['no_reg'] : '';

$skema_id    = ! empty( $kegiatan->skema_id ) ? (int) $kegiatan->skema_id : ( ! empty( $skema['id'] ) ? (int) $skema['id'] : 0 );
$units_skkni = ! empty( $units_skkni ) && is_array( $units_skkni ) ? $units_skkni : LSP_Cert_Integration::get_unit_kompetensi( $skema_id );
?>

<div class="lsp-doc-page lsp-doc-ia02">

    <!-- Header Dokumen Sesuai Format BNSP -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.IA.02.&nbsp;&nbsp;&nbsp;&nbsp;TPD - TUGAS PRAKTIK DEMONSTRASI
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

    <!-- Kotak Petunjuk Pengerjaan Praktik -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
        <thead>
            <tr style="background: #f1f5f9;">
                <th style="padding: 6px 10px; font-size: 9.5pt; text-align: left; text-transform: uppercase;">
                    PETUNJUK
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 10px 14px; font-size: 9pt; line-height: 1.6;">
                    <ol style="margin: 0; padding-left: 18px;">
                        <li>Baca dan pelajari setiap instruksi kerja di bawah ini dengan cermat sebelum melaksanakan praktek.</li>
                        <li>Klarifikasi kepada asesor kompetensi apabila ada hal-hal yang belum jelas.</li>
                        <li>Laksanakan pekerjaan sesuai dengan urutan proses yang sudah ditetapkan.</li>
                        <li>Seluruh proses kerja mengacu kepada SOP/WI yang dipersyaratkan (Jika Ada).</li>
                    </ol>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Skenario Tugas Praktik Demonstrasi -->
    <div style="font-weight: bold; font-size: 10pt; margin-bottom: 6px; text-transform: uppercase;">
        Skenario Tugas Praktik Demonstrasi
    </div>

    <!-- Tabel Daftar Unit Kompetensi Berbasis Kelompok Pekerjaan (Col 1: Kelompok Pekerjaan) -->
    <?php LSP_Cert_Kelompok_Pekerjaan::render_unit_table_html( $skema_id, true, $paket_muk_id ?? 0 ); ?>

    <!-- Uraian Skenario, Langkah Kerja, dan Kebutuhan Alat -->
    <?php
    $ia02_data = class_exists( 'LSP_Cert_IA02' ) ? LSP_Cert_IA02::get_instrument( $skema_id, $paket_muk_id ?? 0 ) : array();
    $skenario_text    = ! empty( $ia02_data['skenario'] ) ? $ia02_data['skenario'] : sprintf( 'Anda seorang asesi sedang mengajukan sertifikasi skema %s. Anda diharapkan mampu mendemonstrasikan seluruh tugas praktik sesuai dengan kelompok pekerjaan di atas secara profesional, terstruktur, dan memenuhi standar keselamatan kerja yang berlaku di tempat kerja.', $skema['title'] ?? 'Sertifikasi Kompetensi' );
    $langkah_text     = ! empty( $ia02_data['langkah_kerja'] ) ? $ia02_data['langkah_kerja'] : '';
    $hasil_text       = ! empty( $ia02_data['hasil_kerja'] ) ? $ia02_data['hasil_kerja'] : 'Hasil pekerjaan berupa demonstrasi fisik/praktik langsung, bukti karya/dokumen pendukung, dan lembar verifikasi hasil pekerjaan yang diserahkan kepada asesor.';
    $perlengkapan_raw = ! empty( $ia02_data['perlengkapan'] ) ? $ia02_data['perlengkapan'] : "Alat Keselamatan & Kesehatan Kerja (K3)\nPerangkat media penyimpanan / flashdisk / cloud\nBahan penunjang demonstrasi / dokumen kerja\nFormulir checklist laporan pekerjaan";
    $peralatan_raw    = ! empty( $ia02_data['peralatan'] ) ? $ia02_data['peralatan'] : "Komputer / laptop / gawai penunjang\nPerangkat kerja spesifik bidang profesi\nAksesori / peripheral pendukung teknis\nAlat Tulis Kantor (ATK)";
    $durasi_text      = ! empty( $ia02_data['durasi_waktu'] ) ? $ia02_data['durasi_waktu'] : '120 Menit';

    $perlengkapan_lines = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $perlengkapan_raw ) ) );
    $peralatan_lines    = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $peralatan_raw ) ) );
    $langkah_lines      = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $langkah_text ) ) );
    ?>
    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 14px 18px; margin-bottom: 18px; font-size: 9pt; line-height: 1.6;">
        <div style="font-weight: bold; margin-bottom: 6px; color: #1e293b;">
            A. Skenario Tugas Praktik Demonstrasi:
        </div>
        <p style="margin: 0 0 10px 0; color: #334155;">
            <?php echo nl2br( esc_html( $skenario_text ) ); ?>
        </p>

        <div style="font-weight: bold; margin-bottom: 6px; color: #1e293b;">
            B. Langkah-langkah Kerja yang Perlu Dilakukan:
        </div>
        <?php if ( ! empty( $langkah_lines ) ) : ?>
            <ol style="margin: 0 0 12px 0; padding-left: 20px; color: #334155;">
                <?php foreach ( $langkah_lines as $ll ) : 
                    // Bersihkan nomor di awal jika user menulis "1. Langkah"
                    $cleaned_ll = preg_replace( '/^[0-9]+[\.\)]\s*/', '', $ll );
                ?>
                    <li><?php echo esc_html( $cleaned_ll ); ?></li>
                <?php endforeach; ?>
            </ol>
        <?php elseif ( ! empty( $units_skkni ) ) : ?>
            <ol style="margin: 0 0 12px 0; padding-left: 20px; color: #334155;">
                <?php foreach ( $units_skkni as $u_item ) : 
                    $u_title = is_object( $u_item ) ? ( $u_item->judul_unit ?? ( $u_item->judul ?? '' ) ) : ( $u_item['judul'] ?? ( $u_item['judul_unit'] ?? '' ) );
                ?>
                    <li>Melaksanakan aktivitas: <strong><?php echo esc_html( $u_title ); ?></strong> sesuai prosedur operasional standar.</li>
                <?php endforeach; ?>
            </ol>
        <?php else : ?>
            <ol style="margin: 0 0 12px 0; padding-left: 20px; color: #334155;">
                <li>Mempersiapkan seluruh peralatan dan perangkat kerja pendukung sesuai instruksi.</li>
                <li>Melaksanakan tahapan pekerjaan sesuai SOP dan standar keselamatan kerja (K3).</li>
                <li>Mendokumentasikan hasil pekerjaan dan melaporkan hasil kerja kepada asesor.</li>
            </ol>
        <?php endif; ?>

        <div style="font-weight: bold; margin-bottom: 6px; color: #1e293b;">
            C. Hasil Pekerjaan:
        </div>
        <p style="margin: 0 0 12px 0; color: #334155;">
            <?php echo nl2br( esc_html( $hasil_text ) ); ?>
        </p>

        <div style="font-weight: bold; margin-bottom: 6px; color: #1e293b;">
            D. Perlengkapan dan Peralatan:
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 12px;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px 14px;">
                <strong style="color: #0f172a;">Perlengkapan:</strong>
                <ul style="margin: 6px 0 0 0; padding-left: 18px; color: #475569;">
                    <?php if ( ! empty( $perlengkapan_lines ) ) : ?>
                        <?php foreach ( $perlengkapan_lines as $pline ) : ?>
                            <li><?php echo esc_html( preg_replace( '/^[-*•]\s*/', '', $pline ) ); ?></li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li>Alat Keselamatan &amp; Kesehatan Kerja (K3)</li>
                        <li>Perangkat angkut / media penyimpanan</li>
                        <li>Bahan penunjang demonstrasi / studi kasus</li>
                        <li>Formulir checklist laporan pekerjaan</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px 14px;">
                <strong style="color: #0f172a;">Peralatan:</strong>
                <ul style="margin: 6px 0 0 0; padding-left: 18px; color: #475569;">
                    <?php if ( ! empty( $peralatan_lines ) ) : ?>
                        <?php foreach ( $peralatan_lines as $pline ) : ?>
                            <li><?php echo esc_html( preg_replace( '/^[-*•]\s*/', '', $pline ) ); ?></li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li>Komputer / laptop / gawai penunjang</li>
                        <li>Perangkat kerja spesifik bidang profesi</li>
                        <li>Aksesori / peripheral pendukung teknis</li>
                        <li>Alat Tulis Kantor (ATK)</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div style="font-weight: bold; color: #1e293b;">
            E. Durasi Waktu: <span style="font-weight: normal; color: #0369a1;"><?php echo esc_html( $durasi_text ); ?></span>
        </div>
    </div>

    <!-- Bagian Pengesahan Asesi dan Asesor -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding: 10px 14px;">
                <div style="font-weight: bold; font-size: 9pt; margin-bottom: 6px;">ASESI :</div>
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
                <div style="font-weight: bold; font-size: 9pt; margin-bottom: 6px;">ASESOR :</div>
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
