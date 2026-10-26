<?php
/**
 * Template FR.IA.04A — DIT: Penjelasan Proyek Singkat / Kegiatan Terstruktur Lainnya
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

// Data Penyusun & Validator
$nama_penyusun  = ! empty( $penyusun['name'] ) ? $penyusun['name'] : 'Masriah';
$met_penyusun   = ! empty( $penyusun['no_reg'] ) ? $penyusun['no_reg'] : 'MET.000.032423 2019';
$nama_validator = ! empty( $validator['name'] ) ? $validator['name'] : 'Eko Prasyanto';
$met_validator  = ! empty( $validator['no_reg'] ) ? $validator['no_reg'] : 'MET.000.014249 2019';

// Ambil instrumen materi IA.04 berdasarkan skema_id
$skema_id   = ! empty( $kegiatan->skema_id ) ? (int) $kegiatan->skema_id : ( ! empty( $skema['id'] ) ? (int) $skema['id'] : 0 );
$instrument = ! empty( $ia04_override ) ? $ia04_override : LSP_Cert_IA04::get_instrument( $skema_id, $paket_muk_id ?? 0 );

// Cek ketersediaan materi IA.04A
$has_materi_ia04a = ! empty( $instrument['situasi'] ) || ! empty( $instrument['tugas'] ) || ! empty( $instrument['tindakan'] );

// Ambil daftar unit kompetensi
$total_units = ! empty( $units_skkni ) && is_array( $units_skkni ) ? count( $units_skkni ) : 0;
?>

<div class="lsp-doc-page lsp-doc-ia04a">

    <!-- Header Dokumen -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.IA.04A.&nbsp;&nbsp;&nbsp;&nbsp;DIT – DAFTAR INSTRUKSI TERSTRUKTUR (PENJELASAN PROYEK SINGKAT/ KEGIATAN TERSTRUKTUR LAINNYA*)
            </td>
        </tr>
    </table>

    <!-- Tabel 1: Identitas Skema, TUK, Asesor, Asesi, dan Tanggal -->
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
                <td style="padding: 8px 12px; font-size: 9pt; line-height: 1.5; text-align: justify;">
                    Tentukan proyek singkat atau kegiatan terstruktur lainnya yang harus dipersiapkan dan dipresentasikan oleh asesi. Proyek singkat atau kegiatan terstruktur lainnya dibuat untuk keseluruhan unit kompetensi dalam Skema Sertifikasi atau untuk masing-masing kelompok pekerjaan. Kumpulkan hasil proyek singkat atau kegiatan terstruktur lainnya sesuai dengan hasil keluaran yang telah ditetapkan.
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel Daftar Unit Kompetensi (3 Kolom: No, Kode Unit, Judul Unit) -->
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
                            <?php echo ( $idx + 1 ); ?>
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

    <!-- Materi Kegiatan Terstruktur (STAR) -->
    <?php if ( ! $has_materi_ia04a ) : ?>
        <div class="lsp-alert-box" style="border: 1px solid #dc2626; background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 4px; margin: 15px 0; font-size: 10pt;">
            <strong>Perhatian:</strong> Materi kegiatan terstruktur FR.IA.04A untuk skema ini belum diatur. Silakan masukkan materi instrumen terlebih dahulu pada menu <em>Sertifikasi &gt; Instrumen FR.IA.04</em>.
        </div>
    <?php else : ?>
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 16px;">
            <tr>
                <td style="width: 32%; vertical-align: top; font-weight: bold; padding: 10px; font-size: 9pt; background: #f8fafc;">
                    Hal yang harus disiapkan atau dilakukan atau dihasilkan untuk suatu proyek singkat/kegiatan terstruktur
                </td>
                <td style="width: 68%; vertical-align: top; padding: 10px 14px; font-size: 9pt; line-height: 1.6;">
                    <div style="font-weight: bold; margin-bottom: 8px;">
                        Skenario studi kasus/kegiatan terstruktur yang berisikan data informasi, lingkup bahasan dan instruksi untuk asesi
                    </div>

                    <?php if ( ! empty( $instrument['judul_kegiatan'] ) ) : ?>
                        <div style="font-weight: bold; color: #0369a1; margin-bottom: 8px;">
                            Judul Proyek: <?php echo esc_html( $instrument['judul_kegiatan'] ); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Situation -->
                    <div style="margin-bottom: 10px;">
                        <strong>S – Situation (Situasi)</strong><br>
                        <div style="white-space: pre-wrap; margin-top: 3px; text-align: justify;"><?php echo esc_html( $instrument['situasi'] ); ?></div>
                    </div>

                    <!-- Task -->
                    <div style="margin-bottom: 10px;">
                        <strong>T – Task (Tugas)</strong><br>
                        <div style="white-space: pre-wrap; margin-top: 3px; text-align: justify;"><?php echo esc_html( $instrument['tugas'] ); ?></div>
                    </div>

                    <!-- Action -->
                    <div style="margin-bottom: 10px;">
                        <strong>A – Action (Tindakan/Instruksi untuk Asesi)</strong><br>
                        <div style="white-space: pre-wrap; margin-top: 3px; text-align: justify;"><?php echo esc_html( $instrument['tindakan'] ); ?></div>
                    </div>

                    <!-- Data / Informasi -->
                    <?php if ( ! empty( $instrument['data_informasi'] ) ) : ?>
                        <div style="margin-bottom: 10px;">
                            <strong>Data/informasi yang disediakan:</strong><br>
                            <div style="white-space: pre-wrap; margin-top: 3px; text-align: justify;"><?php echo esc_html( $instrument['data_informasi'] ); ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Result -->
                    <div style="margin-bottom: 10px;">
                        <strong>R – Result (Hasil/Keluaran yang Diharapkan)</strong><br>
                        <div style="white-space: pre-wrap; margin-top: 3px; text-align: justify;"><?php echo esc_html( $instrument['hasil'] ); ?></div>
                    </div>

                    <!-- Waktu Proyek -->
                    <div style="margin-top: 8px; font-weight: bold;">
                        Waktu: <?php echo esc_html( $instrument['waktu_proyek'] ?: '240 menit' ); ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 32%; vertical-align: top; font-weight: bold; padding: 10px; font-size: 9pt; background: #f8fafc;">
                    Hal yang perlu didemonstrasikan
                </td>
                <td style="width: 68%; vertical-align: top; padding: 10px 14px; font-size: 9pt; line-height: 1.6;">
                    <strong>Asesi mempresentasikan kepada asesor:</strong><br>
                    <div style="white-space: pre-wrap; margin-top: 4px; text-align: justify;"><?php echo esc_html( $instrument['hal_demonstrasi'] ); ?></div>
                    <div style="margin-top: 8px; font-weight: bold;">
                        Waktu : <?php echo esc_html( $instrument['waktu_presentasi'] ?: '30 menit' ); ?>
                    </div>
                </td>
            </tr>
        </table>
    <?php endif; ?>

    <!-- Bagian Umpan Balik & Tanda Tangan -->
    <div class="lsp-ia04a-avoid-break">
        <!-- Tabel Umpan Balik -->
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
            <tr>
                <td style="padding: 8px 10px; font-size: 9pt; font-weight: bold; background: #f8fafc; border-bottom: none;">
                    Umpan Balik Untuk Asesi:
                </td>
            </tr>
            <tr>
                <td style="height: 60px; padding: 10px; border-top: none; vertical-align: top;">
                    <div style="border-bottom: 1px dotted #94a3b8; height: 18px;"></div>
                    <div style="border-bottom: 1px dotted #94a3b8; height: 18px; margin-top: 6px;"></div>
                </td>
            </tr>
        </table>

        <!-- Tabel Tanda Tangan -->
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
            <thead>
                <tr style="background-color: #f1f5f9; text-align: center;">
                    <th style="width: 34%; padding: 6px 10px; font-size: 9pt;">Tanda Tangan Asesi</th>
                    <th style="width: 33%; padding: 6px 10px; font-size: 9pt;">Tanda Tangan Asesor</th>
                    <th style="width: 33%; padding: 6px 10px; font-size: 9pt;">Nama &amp; Tanda Tangan Supervisor (Jika ada)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px 14px; vertical-align: top; font-size: 9pt; line-height: 1.6; height: 90px;">
                        <strong>Nama :</strong> <?php echo esc_html( $nama_asesi ); ?><br><br>
                        <strong>Tanda tangan / Tanggal :</strong><br><br><br>
                        <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                    </td>
                    <td style="padding: 10px 14px; vertical-align: top; font-size: 9pt; line-height: 1.6; height: 90px;">
                        <strong>Nama :</strong> <?php echo esc_html( $nama_asesor ); ?><br><br>
                        <strong>Tanda tangan / Tanggal :</strong><br><br><br>
                        <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                    </td>
                    <td style="padding: 10px 14px; vertical-align: top; font-size: 9pt; line-height: 1.6; height: 90px;">
                        <strong>Nama :</strong> ...................................................<br><br>
                        <strong>Tanda tangan / Tanggal :</strong><br><br><br>
                        <div style="border-bottom: 1px dotted #000; width: 85%;"></div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="font-size: 8pt; font-style: italic; color: #475569; margin-bottom: 18px;">
            *) Apabila asesi pada Level 4 ke atas, berikan tugas proyek yang meliputi tentang pemecahan masalah dan analisa.
        </div>

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
