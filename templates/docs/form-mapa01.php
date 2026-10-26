<?php
/**
 * Template Dokumen Cetak FR.MAPA.01
 * Merencanakan Aktivitas dan Proses Asesmen
 * Mendukung 3 Metode:
 * 1. Observasi Langsung ('observasi')
 * 2. Daftar Instruksi Terstruktur ('dit')
 * 3. Verifikasi Portofolio ('portofolio')
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Inisialisasi variabel metode & kategori
$metode = isset( $metode ) ? $metode : 'observasi';
$mapa_idx = isset( $mapa_idx ) ? (int) $mapa_idx : 1;
$selected_kategori = isset( $selected_kategori ) ? (int) $selected_kategori : 0;

// Validasi Addendum Latar Belakang Asesi
// Observasi Langsung & DIT: Hanya Kategori 1 dan Kategori 2
// Verifikasi Portofolio: Hanya Kategori 3, 4, dan 5
if ( $metode === 'observasi' || $metode === 'dit' ) {
    if ( ! in_array( $selected_kategori, array( 1, 2 ), true ) ) {
        $selected_kategori = 0; // Biarkan kosong jika tidak sesuai atau belum dipilih
    }
} elseif ( $metode === 'portofolio' ) {
    if ( ! in_array( $selected_kategori, array( 3, 4, 5 ), true ) ) {
        $selected_kategori = 0; // Biarkan kosong jika tidak sesuai atau belum dipilih
    }
}

// Tanggal penyusunan dokumen MAPA: Backdate otomatis H-2 dari tanggal pelaksanaan kegiatan
$base_mapa_date = ! empty( $kegiatan->tanggal_pelaksanaan ) 
    ? $kegiatan->tanggal_pelaksanaan 
    : ( ! empty( $pendaftaran->created_at ) ? $pendaftaran->created_at : current_time( 'mysql' ) );
$tgl_mapa = date_i18n( 'd F Y', strtotime( '-2 days', strtotime( $base_mapa_date ) ) );

// Label nama metode
$metode_labels = array(
    'observasi'  => 'Observasi Langsung',
    'dit'        => 'Daftar Instruksi Terstruktur (DIT)',
    'portofolio' => 'Verifikasi Portofolio',
);
$metode_label = $metode_labels[ $metode ] ?? 'Observasi Langsung';
?>

<div class="lsp-doc-page lsp-mapa-page" data-metode="<?php echo esc_attr( $metode ); ?>" data-mapa-idx="<?php echo esc_attr( $mapa_idx ); ?>">

    <!-- Judul Dokumen -->
    <div style="text-align: center; margin-bottom: 18px;">
        <h1 style="font-size: 14.5px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">
            FR.MAPA.01 - MERENCANAKAN AKTIVITAS DAN PROSES ASESMEN
        </h1>
        <div style="font-size: 12px; font-weight: 700; color: #0284c7; margin-top: 3px;">
            Metode: <?php echo esc_html( $metode_label ); ?>
        </div>
    </div>

    <!-- Header Skema Sertifikasi -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 14px;">
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

    <!-- 1. Menentukan Pendekatan Asesmen -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 14px;">
        <tr style="background: #f8fafc;">
            <th colspan="7" style="text-align: left; padding: 7px 10px; font-size: 12.5px;">
                1. Menentukan Pendekatan Asesmen
            </th>
        </tr>

        <!-- 1.1 Asesi (Latar Belakang Asesi) -->
        <tr>
            <td width="6%" rowspan="5" valign="top"><strong>1.1.</strong></td>
            <td width="22%" rowspan="5" valign="top"><strong>Asesi</strong></td>
            <td width="5%" style="text-align: center; font-size: 14px;" class="mapa-kat-box mapa-kat-1-box">
                <span id="mapa_<?php echo $mapa_idx; ?>_kat_1_mark"><?php echo ( $selected_kategori === 1 ) ? '☑' : '☐'; ?></span>
            </td>
            <td colspan="4">Hasil pelatihan dan / atau pendidikan, dimana Kurikulum dan fasilitas praktek mampu telusur terhadap standar kompetensi</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;" class="mapa-kat-box mapa-kat-2-box">
                <span id="mapa_<?php echo $mapa_idx; ?>_kat_2_mark"><?php echo ( $selected_kategori === 2 ) ? '☑' : '☐'; ?></span>
            </td>
            <td colspan="4">Hasil pelatihan dan / atau pendidikan, dimana kurikulum belum berbasis kompetensi.</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;" class="mapa-kat-box mapa-kat-3-box">
                <span id="mapa_<?php echo $mapa_idx; ?>_kat_3_mark"><?php echo ( $selected_kategori === 3 ) ? '☑' : '☐'; ?></span>
            </td>
            <td colspan="4">Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya mampu telusur dengan standar kompetensi</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;" class="mapa-kat-box mapa-kat-4-box">
                <span id="mapa_<?php echo $mapa_idx; ?>_kat_4_mark"><?php echo ( $selected_kategori === 4 ) ? '☑' : '☐'; ?></span>
            </td>
            <td colspan="4">Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya belum berbasis kompetensi.</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;" class="mapa-kat-box mapa-kat-5-box">
                <span id="mapa_<?php echo $mapa_idx; ?>_kat_5_mark"><?php echo ( $selected_kategori === 5 ) ? '☑' : '☐'; ?></span>
            </td>
            <td colspan="4">Pelatihan / belajar mandiri atau otodidak.</td>
        </tr>

        <!-- Tujuan Sertifikasi -->
        <?php
        $tujuan = strtolower( $pendaftaran->tujuan_asesmen ?? 'sertifikasi' );
        ?>
        <tr>
            <td rowspan="4" valign="top"></td>
            <td rowspan="4" valign="top"><strong>Tujuan Sertifikasi</strong></td>
            <td style="text-align: center; font-size: 14px;"><?php echo ($tujuan === 'sertifikasi' || $tujuan === 'sertifikasi baru') ? '☑' : '☐'; ?></td>
            <td colspan="4">Sertifikasi</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;"><?php echo ($tujuan === 'pkt') ? '☑' : '☐'; ?></td>
            <td colspan="4">Pengakuan Kompetensi Terkini (PKT)</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;"><?php echo ($tujuan === 'rpl') ? '☑' : '☐'; ?></td>
            <td colspan="4">Rekognisi Pembelajaran Lampau (RPL)</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;"><?php echo ($tujuan === 'lainnya') ? '☑' : '☐'; ?></td>
            <td colspan="4">Lainnya</td>
        </tr>

        <!-- Konteks Asesmen -->
        <tr>
            <td rowspan="8" valign="top"></td>
            <td rowspan="8" valign="top"><strong>Konteks Asesmen:</strong></td>
            <td colspan="2">Lingkungan</td>
            <td colspan="3">
                <span style="font-size: 14px;">☐</span> Tempat kerja nyata &nbsp;&nbsp;&nbsp;&nbsp;
                <span style="font-size: 14px;">☑</span> Tempat kerja simulasi
            </td>
        </tr>
        <tr>
            <td colspan="2">Peluang untuk mengumpulkan bukti dalam sejumlah situasi</td>
            <td colspan="3">
                <span style="font-size: 14px;">☐</span> Tersedia &nbsp;&nbsp;&nbsp;&nbsp;
                <span style="font-size: 14px;">☑</span> Terbatas
            </td>
        </tr>
        <tr>
            <td colspan="2" rowspan="3" valign="top">Hubungan antara standar kompetensi dan:</td>
            <td colspan="3"><span style="font-size: 14px;">☑</span> Bukti untuk mendukung asesmen : ☺ &nbsp; 😐 &nbsp; ☹</td>
        </tr>
        <tr>
            <td colspan="3"><span style="font-size: 14px;">☐</span> Aktivitas kerja di tempat kerja Asesi: ☺ &nbsp; 😐 &nbsp; ☹</td>
        </tr>
        <tr>
            <td colspan="3"><span style="font-size: 14px;">☐</span> Kegiatan Pembelajaran: ☺ &nbsp; 😐 &nbsp; ☹</td>
        </tr>
        <tr>
            <td colspan="2" rowspan="3" valign="top">Siapa yang melakukan asesmen</td>
            <td colspan="3"><span style="font-size: 14px;">☑</span> Lembaga Sertifikasi Profesi Komunikasi Indonesia</td>
        </tr>
        <tr>
            <td colspan="3"><span style="font-size: 14px;">☐</span> Organisasi Pelatihan</td>
        </tr>
        <tr>
            <td colspan="3"><span style="font-size: 14px;">☐</span> Asesor Perusahaan</td>
        </tr>

        <!-- Konfirmasi dengan orang yang relevan -->
        <tr>
            <td rowspan="4" valign="top"></td>
            <td rowspan="4" valign="top"><strong>Konfirmasi dengan orang yang relevan</strong></td>
            <td style="text-align: center; font-size: 14px;">☑</td>
            <td colspan="4">Manajer sertifikasi LSP</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;">☐</td>
            <td colspan="4">Master Asesor / Master Trainer / Lead Asesor Kompetensi</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;">☐</td>
            <td colspan="4">Manajer Pelatihan Lembaga Training terakreditasi / Lembaga Training terdaftar</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;">☐</td>
            <td colspan="4">Manajer atau Supervisor di tempat kerja</td>
        </tr>

        <!-- 1.2 Standar Industri atau Tempat Kerja -->
        <tr>
            <td rowspan="5" valign="top"><strong>1.2</strong></td>
            <td rowspan="5" valign="top"><strong>Standar Industri atau Tempat Kerja</strong></td>
            <td style="text-align: center; font-size: 14px;">☑</td>
            <td colspan="4">Standar Kompetensi</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;">☐</td>
            <td colspan="4">Kriteria asesmen dari kurikulum pelatihan</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;">☐</td>
            <td colspan="4">Spesifikasi kinerja suatu perusahaan atau industri:</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;">☐</td>
            <td colspan="4">Spesifikasi Produk:</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 14px;">☐</td>
            <td colspan="4">Pedoman khusus:</td>
        </tr>
    </table>

    <!-- 2. Mempersiapkan Rencana Asesmen -->
    <h3 style="font-size: 13px; font-weight: bold; margin: 16px 0 6px 0;">2. Mempersiapkan Rencana Asesmen</h3>

    <!-- Tabel Daftar Unit Kompetensi Sesuai Kemasan -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <thead>
            <tr style="background: #f1f5f9; text-align: center; font-size: 12px;">
                <th width="8%">No.</th>
                <th width="28%">Kode Unit</th>
                <th width="64%">Judul Unit</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $units_skkni ) ) : ?>
                <?php foreach ( $units_skkni as $u_idx => $u ) : ?>
                    <tr style="font-size: 12px;">
                        <td style="text-align: center;"><?php echo ($u_idx + 1); ?></td>
                        <td style="font-family: monospace; font-weight: bold;"><?php echo esc_html( $u['kode'] ); ?></td>
                        <td><?php echo esc_html( $u['judul'] ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="3" style="text-align: center;">Daftar unit kompetensi tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tabel Matriks Asesmen Berdasarkan Metode -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 10.5px;">
        <thead>
            <tr style="background: #f1f5f9; text-align: center;">
                <th width="20%" rowspan="2" style="vertical-align: middle;">Unit Kompetensi</th>
                <th width="25%" rowspan="2" style="vertical-align: middle;">
                    Bukti-Bukti (Kinerja, Produk, Portofolio, dan / atau Pengetahuan) diidentifikasi berdasarkan Kriteria Unjuk Kerja dan Pendekatan Asesmen.
                </th>
                <th colspan="3" width="9%" style="text-align: center; vertical-align: middle;">Jenis Bukti</th>
                <th colspan="6" width="46%" style="text-align: center; vertical-align: middle;">
                    Metode dan Perangkat Asesmen<br>
                    <span style="font-size: 9.5px; font-weight: normal;">CL (Ceklis Observasi), DIT (Daftar Instruksi Terstruktur), DPL (Daftar Pertanyaan Lisan), DPT (Daftar Pertanyaan Tertulis), VPK (Verifikasi Pihak Ketiga), CVP (Ceklis Verifikasi Portofolio), CRP (Ceklis Reviu Produk), PW (Pertanyaan Wawancara)</span>
                </th>
            </tr>
            <tr style="background: #f8fafc; text-align: center; font-size: 10px;">
                <th width="3%">L</th>
                <th width="3%">TL</th>
                <th width="3%">T</th>
                <th width="8%">Observasi langsung</th>
                <th width="8%">Kegiatan Terstruktur</th>
                <th width="7%">Tanya Jawab</th>
                <th width="8%">Verifikasi Portfolio</th>
                <th width="7%">Reviu Produk</th>
                <th width="8%">Verifikasi Pihak Ketiga</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $units_skkni ) ) : ?>
                <?php foreach ( $units_skkni as $u_idx => $u ) : 
                    $u_judul = $u['judul'];

                    // Penentuan kalimat bukti & perangkat asesmen sesuai metode
                    if ( $metode === 'observasi' ) {
                        $kalimat_bukti = "Hasil observasi dan hasil tanya jawab tentang " . $u_judul;
                        $bukti_l  = 'L';
                        $bukti_tl = '';
                        $bukti_t  = 'T';
                        $dev_obs  = 'CLO';
                        $dev_dit  = '';
                        $dev_tj   = 'DPT';
                        $dev_vp   = '';
                        $dev_rp   = '';
                        $dev_vpk  = '';
                    } elseif ( $metode === 'dit' ) {
                        $kalimat_bukti = "Hasil Kegiatan Terstruktur dan hasil tanya jawab tentang " . $u_judul;
                        $bukti_l  = 'L';
                        $bukti_tl = '';
                        $bukti_t  = 'T';
                        $dev_obs  = '';
                        $dev_dit  = 'DIT';
                        $dev_tj   = 'DPT';
                        $dev_vp   = '';
                        $dev_rp   = '';
                        $dev_vpk  = '';
                    } else {
                        // Verifikasi Portofolio
                        $kalimat_bukti = "Hasil verifikasi portfolio dan hasil tanya jawab tentang " . $u_judul;
                        $bukti_l  = '';
                        $bukti_tl = 'TL';
                        $bukti_t  = 'T';
                        $dev_obs  = '';
                        $dev_dit  = '';
                        $dev_tj   = 'DPT';
                        $dev_vp   = 'CVP';
                        $dev_rp   = '';
                        $dev_vpk  = '';
                    }
                ?>
                    <tr>
                        <td style="font-weight: 600; padding: 4px 6px;"><?php echo esc_html( $u_judul ); ?></td>
                        <td style="padding: 4px 6px;"><?php echo esc_html( $kalimat_bukti ); ?></td>
                        <td style="text-align: center; vertical-align: middle; font-weight: bold;"><?php echo esc_html( $bukti_l ); ?></td>
                        <td style="text-align: center; vertical-align: middle; font-weight: bold;"><?php echo esc_html( $bukti_tl ); ?></td>
                        <td style="text-align: center; vertical-align: middle; font-weight: bold;"><?php echo esc_html( $bukti_t ); ?></td>
                        <td style="text-align: center; vertical-align: middle; font-weight: bold;"><?php echo esc_html( $dev_obs ); ?></td>
                        <td style="text-align: center; vertical-align: middle; font-weight: bold;"><?php echo esc_html( $dev_dit ); ?></td>
                        <td style="text-align: center; vertical-align: middle; font-weight: bold;"><?php echo esc_html( $dev_tj ); ?></td>
                        <td style="text-align: center; vertical-align: middle; font-weight: bold;"><?php echo esc_html( $dev_vp ); ?></td>
                        <td style="text-align: center; vertical-align: middle; font-weight: bold;"><?php echo esc_html( $dev_rp ); ?></td>
                        <td style="text-align: center; vertical-align: middle; font-weight: bold;"><?php echo esc_html( $dev_vpk ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="11" style="text-align: center;">Daftar unit kompetensi tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- 3. Mengidentifikasi Persyaratan Modifikasi dan Kontekstualisasi -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 12px; page-break-inside: avoid;">
        <tr style="background: #f8fafc;">
            <th colspan="3" style="text-align: left; padding: 7px 10px; font-size: 12px;">
                3. Mengidentifikasi Persyaratan Modifikasi dan Kontekstualisasi:
            </th>
        </tr>
        <tr>
            <td width="6%" rowspan="2" valign="top"><strong>3.1</strong></td>
            <td width="44%">a. Karakteristik Kandidat:</td>
            <td width="50%">Ada / <u>tidak ada</u>* karakteristik khusus Kandidat. Jika Ada, tuliskan: -</td>
        </tr>
        <tr>
            <td>b. Kebutuhan kontekstualisasi terkait tempat kerja:</td>
            <td>Ada / <u>tidak ada</u>* kebutuhan kontekstualisasi. Jika Ada, tuliskan: -</td>
        </tr>
        <tr>
            <td><strong>3.2</strong></td>
            <td>Saran yang diberikan oleh paket pelatihan atau pengembang pelatihan:</td>
            <td>Ada / <u>tidak ada</u>* saran. Jika Ada, tuliskan: -</td>
        </tr>
        <tr>
            <td><strong>3.3</strong></td>
            <td>Penyesuaian perangkat asesmen terkait kebutuhan kontekstualisasi:</td>
            <td>Ada / <u>tidak ada</u>* penyesuaian perangkat. Jika Ada, tuliskan: -</td>
        </tr>
        <tr>
            <td><strong>3.4</strong></td>
            <td>Peluang untuk kegiatan asesmen terintegrasi dan mencatat setiap perubahan yang diperlukan untuk alat asesmen:</td>
            <td>Ada / <u>tidak ada</u>* peluang. Jika Ada, tuliskan: -</td>
        </tr>
    </table>
    <div style="font-size: 11px; margin-top: -8px; margin-bottom: 12px; color: #475569;">*Coret yang tidak perlu</div>

    <!-- Konfirmasi dengan orang yang relevan -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 14px; page-break-inside: avoid;">
        <tr style="background: #f8fafc;">
            <th colspan="3" style="text-align: left; padding: 6px 10px; font-size: 12px;">
                Konfirmasi dengan orang yang relevan
            </th>
        </tr>
        <tr style="background: #f1f5f9; text-align: center; font-size: 11px;">
            <th width="45%" style="text-align: left; padding-left: 8px;">Orang yang relevan</th>
            <th width="30%">Nama</th>
            <th width="25%">Tandatangan dan Tanggal</th>
        </tr>
        <tr>
            <td><span style="font-size: 14px;">☑</span> Manajer sertifikasi LSP</td>
            <td><strong>Lilis Indriani</strong></td>
            <td style="text-align: center;"><?php echo esc_html( $tgl_mapa ); ?></td>
        </tr>
        <tr>
            <td><span style="font-size: 14px;">☐</span> Master Asesor / Master Trainer / Lead Asesor Kompetensi</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><span style="font-size: 14px;">☐</span> Manajer pelatihan Lembaga Training terakreditasi / Lembaga Training terdaftar</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><span style="font-size: 14px;">☐</span> Manajer atau supervisor di tempat kerja</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <!-- PENYUSUN DAN VALIDATOR -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: 14px; page-break-inside: avoid;">
        <tr style="background: #f8fafc;">
            <th colspan="5" style="text-align: center; padding: 6px 10px; font-size: 12px;">
                PENYUSUN DAN VALIDATOR
            </th>
        </tr>
        <tr style="background: #f1f5f9; text-align: center; font-size: 11px;">
            <th width="15%">STATUS</th>
            <th width="5%">NO</th>
            <th width="30%">NAMA</th>
            <th width="25%">NOMOR MET</th>
            <th width="25%">TANDA TANGAN DAN TANGGAL</th>
        </tr>
        <tr>
            <td style="text-align: center; font-weight: bold; vertical-align: middle;">PENYUSUN</td>
            <td style="text-align: center; vertical-align: middle;">1</td>
            <td style="vertical-align: middle;"><strong><?php echo esc_html( $penyusun['name'] ?? ( $asesor['name'] ?? '' ) ); ?></strong></td>
            <td style="text-align: center; vertical-align: middle; font-family: monospace;">
                <?php echo esc_html( $penyusun['no_reg'] ?? ( $asesor['no_reg'] ?? '' ) ); ?>
            </td>
            <td style="text-align: center; vertical-align: middle; height: 50px;">
                <div style="font-size: 10px; color: #64748b; margin-bottom: 15px;">( Tanda Tangan Penyusun )</div>
                <strong><?php echo esc_html( $tgl_mapa ); ?></strong>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; font-weight: bold; vertical-align: middle;">VALIDATOR</td>
            <td style="text-align: center; vertical-align: middle;">1</td>
            <td style="vertical-align: middle;"><strong><?php echo esc_html( $validator['name'] ?? '' ); ?></strong></td>
            <td style="text-align: center; vertical-align: middle; font-family: monospace;">
                <?php echo esc_html( $validator['no_reg'] ?? '' ); ?>
            </td>
            <td style="text-align: center; vertical-align: middle; height: 50px;">
                <div style="font-size: 10px; color: #64748b; margin-bottom: 15px;">( Tanda Tangan Validator )</div>
                <strong><?php echo esc_html( $tgl_mapa ); ?></strong>
            </td>
        </tr>
    </table>

</div>
