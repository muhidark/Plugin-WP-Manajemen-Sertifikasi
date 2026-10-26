<?php
/**
 * Template FR.IA.01 — Ceklis Observasi Aktivitas Praktik di Tempat Kerja atau Simulasi
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

<div class="lsp-doc-page lsp-doc-ia01">

    <!-- Header Dokumen Sesuai Format BNSP -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.IA.01.&nbsp;&nbsp;&nbsp;&nbsp;CL - CEKLIS OBSERVASI AKTIVITAS DI TEMPAT KERJA ATAU TEMPAT KERJA SIMULASI
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
                <td style="padding: 10px 14px; font-size: 9pt; line-height: 1.6;">
                    <ol style="margin: 0; padding-left: 18px;">
                        <li>Lengkapi nama unit kompetensi, elemen, dan kriteria unjuk kerja sesuai kolom dalam tabel.</li>
                        <li>Isilah standar industri atau tempat kerja.</li>
                        <li>Beri tanda centang (&radic;) pada kolom &ldquo;YA&rdquo; jika Anda yakin asesi dapat melakukan/mendemonstrasikan tugas sesuai KUK, atau centang (&radic;) pada kolom &ldquo;Tidak&rdquo; bila sebaliknya.</li>
                        <li>Penilaian Lanjut diisi bila hasil belum dapat disimpulkan, untuk itu gunakan metode lain sehingga keputusan dapat dibuat.</li>
                        <li>Isilah kolom KUK sesuai dengan Unit Kompetensi / SKKNI.</li>
                    </ol>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel Daftar Unit Kompetensi Berbasis Kelompok Pekerjaan (Col 1: Kelompok Pekerjaan) -->
    <div style="font-weight: bold; font-size: 10pt; margin-bottom: 6px; text-transform: uppercase;">
        Daftar Unit Kompetensi Berdasarkan Kelompok Pekerjaan
    </div>
    <?php LSP_Cert_Kelompok_Pekerjaan::render_unit_table_html( $skema_id, true, $paket_muk_id ?? 0 ); ?>

    <!-- Tabel Ceklis Observasi Elemen dan KUK per Unit Kompetensi -->
    <div style="font-weight: bold; font-size: 10pt; margin-top: 20px; margin-bottom: 8px; text-transform: uppercase;">
        Ceklis Observasi Aktivitas Praktik (Elemen &amp; KUK)
    </div>

    <?php if ( empty( $units_skkni ) ) : ?>
        <p style="font-style: italic; color: #64748b;">Data unit kompetensi tidak ditemukan untuk skema ini.</p>
    <?php else : ?>
        <?php foreach ( $units_skkni as $u_idx => $u ) : 
            $u_kode   = is_object( $u ) ? ( $u->kode_unit ?? ( $u->kode ?? '' ) ) : ( $u['kode'] ?? ( $u['kode_unit'] ?? '' ) );
            $u_judul  = is_object( $u ) ? ( $u->judul_unit ?? ( $u->judul ?? '' ) ) : ( $u['judul'] ?? ( $u['judul_unit'] ?? '' ) );
            $elem_list = is_object( $u ) ? ( $u->elemen ?? array() ) : ( $u['elemen'] ?? array() );
        ?>
            <!-- Header Unit Kompetensi -->
            <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: 14px; margin-bottom: 0; background: #f8fafc;">
                <tr>
                    <td rowspan="2" style="width: 22%; font-weight: bold; padding: 6px 10px; vertical-align: middle; font-size: 9pt;">
                        Unit Kompetensi <?php echo ( $u_idx + 1 ); ?>
                    </td>
                    <td style="width: 15%; font-weight: bold; padding: 6px 10px; font-size: 9pt;">Kode Unit</td>
                    <td style="width: 2%; text-align: center; padding: 6px 2px;">:</td>
                    <td style="padding: 6px 10px; font-family: monospace; font-weight: bold; font-size: 9pt;">
                        <?php echo esc_html( $u_kode ); ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 6px 10px; font-size: 9pt;">Judul Unit</td>
                    <td style="text-align: center; padding: 6px 2px;">:</td>
                    <td style="padding: 6px 10px; font-weight: bold; font-size: 9pt;">
                        <?php echo esc_html( $u_judul ); ?>
                    </td>
                </tr>
            </table>

            <!-- Tabel Detail Elemen & KUK -->
            <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 16px; border-top: none;">
                <thead>
                    <tr style="background: #f1f5f9; text-align: center;">
                        <th rowspan="2" style="width: 5%; padding: 6px 4px; font-size: 8.5pt;">No.</th>
                        <th rowspan="2" style="width: 28%; padding: 6px 8px; font-size: 8.5pt;">Elemen</th>
                        <th rowspan="2" style="width: 37%; padding: 6px 8px; font-size: 8.5pt;">Kriteria Unjuk Kerja</th>
                        <th rowspan="2" style="width: 14%; padding: 6px 6px; font-size: 8.5pt;">Standar Industri atau Tempat Kerja</th>
                        <th colspan="2" style="width: 8%; padding: 4px; font-size: 8.5pt;">Pencapaian</th>
                        <th rowspan="2" style="width: 8%; padding: 6px 4px; font-size: 8.5pt;">Penilaian Lanjut</th>
                    </tr>
                    <tr style="background: #f1f5f9; text-align: center;">
                        <th style="width: 4%; padding: 4px; font-size: 8pt;">Ya</th>
                        <th style="width: 4%; padding: 4px; font-size: 8pt;">Tidak</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $elem_list ) ) : ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 12px; font-style: italic; color: #64748b; font-size: 8.5pt;">
                                Belum ada elemen dan KUK terdaftar untuk unit ini di Master Skema.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $elem_list as $e_idx => $elem ) : 
                            $e_nomor = $elem['nomor'] ?? ( $e_idx + 1 );
                            $e_nama  = $elem['nama'] ?? '';
                            $kuk_arr = $elem['kuk'] ?? array();
                            $kuk_count = count( $kuk_arr );
                            $e_rowspan = $kuk_count > 0 ? $kuk_count : 1;
                        ?>
                            <?php if ( empty( $kuk_arr ) ) : ?>
                                <tr>
                                    <td style="text-align: center; padding: 6px 4px; font-size: 8.5pt;"><?php echo esc_html( $e_nomor ); ?></td>
                                    <td style="padding: 6px 8px; font-size: 8.5pt;"><?php echo esc_html( $e_nama ); ?></td>
                                    <td style="padding: 6px 8px; font-size: 8.5pt; font-style: italic; color: #64748b;">(Belum ada KUK)</td>
                                    <td style="padding: 6px 6px;"></td>
                                    <td style="text-align: center; padding: 6px 2px;">☐</td>
                                    <td style="text-align: center; padding: 6px 2px;">☐</td>
                                    <td style="padding: 6px 4px;"></td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ( $kuk_arr as $k_idx => $kuk_text ) : ?>
                                    <tr>
                                        <?php if ( 0 === $k_idx ) : ?>
                                            <td rowspan="<?php echo $e_rowspan; ?>" style="text-align: center; vertical-align: top; padding: 6px 4px; font-size: 8.5pt;">
                                                <?php echo esc_html( $e_nomor ); ?>
                                            </td>
                                            <td rowspan="<?php echo $e_rowspan; ?>" style="vertical-align: top; padding: 6px 8px; font-size: 8.5pt; font-weight: 500;">
                                                <?php echo esc_html( $e_nama ); ?>
                                            </td>
                                        <?php endif; ?>
                                        <td style="vertical-align: top; padding: 6px 8px; font-size: 8.5pt;">
                                            <?php echo esc_html( $kuk_text ); ?>
                                        </td>
                                        <td style="vertical-align: top; padding: 6px 6px; font-size: 8.5pt;"></td>
                                        <td style="text-align: center; vertical-align: middle; padding: 6px 2px; font-size: 9pt;">☐</td>
                                        <td style="text-align: center; vertical-align: middle; padding: 6px 2px; font-size: 9pt;">☐</td>
                                        <td style="vertical-align: top; padding: 6px 4px; font-size: 8.5pt;"></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Kotak Umpan Balik untuk Asesi -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
        <tr>
            <td style="padding: 10px 14px;">
                <div style="font-weight: bold; font-size: 9pt; margin-bottom: 35px;">Umpan Balik untuk asesi:</div>
            </td>
        </tr>
    </table>

    <!-- Rekomendasi & Tanda Tangan -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding: 10px 14px;">
                <div style="font-weight: bold; font-size: 9.5pt; margin-bottom: 8px;">Rekomendasi:</div>
                <div style="margin-bottom: 8px; font-size: 8.5pt; line-height: 1.5;">
                    ☐ Asesi telah memenuhi pencapaian seluruh kriteria unjuk kerja, direkomendasikan <strong>KOMPETEN</strong>
                </div>
                <div style="font-size: 8.5pt; line-height: 1.5;">
                    ☐ Asesi belum memenuhi pencapaian seluruh kriteria unjuk kerja, direkomendasikan <strong>BELUM KOMPETEN</strong> pada:
                    <div style="margin-top: 6px; font-size: 8.5pt; color: #334155; line-height: 1.6;">
                        Kelompok Pekerjaan: &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;<br>
                        Unit: &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;<br>
                        Elemen: &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;<br>
                        KUK: &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;
                    </div>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding: 10px 14px;">
                <div style="font-weight: bold; font-size: 9pt; margin-bottom: 4px;">Asesi:</div>
                <table style="width: 100%; border: none; margin-bottom: 10px; font-size: 8.5pt;">
                    <tr>
                        <td style="width: 38%; border: none; padding: 2px 0;">Nama</td>
                        <td style="width: 4%; border: none; padding: 2px 0;">:</td>
                        <td style="border: none; padding: 2px 0; font-weight: bold;"><?php echo esc_html( $nama_asesi ); ?></td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px 0; vertical-align: bottom;">Tanda tangan &amp; Tanggal</td>
                        <td style="border: none; padding: 2px 0; vertical-align: bottom;">:</td>
                        <td style="border: none; padding: 20px 0 2px 0;">................................................</td>
                    </tr>
                </table>
                <div style="font-weight: bold; font-size: 9pt; margin-bottom: 4px;">Asesor:</div>
                <table style="width: 100%; border: none; font-size: 8.5pt;">
                    <tr>
                        <td style="width: 38%; border: none; padding: 2px 0;">Nama</td>
                        <td style="width: 4%; border: none; padding: 2px 0;">:</td>
                        <td style="border: none; padding: 2px 0; font-weight: bold;"><?php echo esc_html( $nama_asesor ); ?></td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px 0;">No. Reg</td>
                        <td style="border: none; padding: 2px 0;">:</td>
                        <td style="border: none; padding: 2px 0; font-family: monospace;"><?php echo esc_html( $no_reg_met ); ?></td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px 0; vertical-align: bottom;">Tanda tangan &amp; Tanggal</td>
                        <td style="border: none; padding: 2px 0; vertical-align: bottom;">:</td>
                        <td style="border: none; padding: 20px 0 2px 0;">................................................</td>
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
