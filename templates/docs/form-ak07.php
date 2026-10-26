<?php
/**
 * Template FR.AK.07 — Ceklis Penyesuaian yang Wajar dan Beralasan
 * Standar BNSP & LSP Komunikasi Indonesia
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Tanggal pelaksanaan kegiatan
$tanggal_raw     = ! empty( $kegiatan->tanggal_pelaksanaan ) ? $kegiatan->tanggal_pelaksanaan : '';
$tgl_pelaksanaan = ! empty( $tanggal_raw ) ? date_i18n( 'd F Y', strtotime( $tanggal_raw ) ) : date_i18n( 'd F Y' );

// Asesi & Asesor
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

$jenis_tuk       = strtolower( $tuk['type'] ?? ( $tuk['jenis'] ?? 'sewaktu' ) );
$is_sewaktu     = ( false !== strpos( $jenis_tuk, 'sewaktu' ) );
$is_tempat_kerja = ( false !== strpos( $jenis_tuk, 'kerja' ) );
$is_mandiri     = ( false !== strpos( $jenis_tuk, 'mandiri' ) );
?>

<div class="lsp-doc-page lsp-doc-ak07">

    <!-- Header Dokumen Sesuai Format BNSP -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
        <tr>
            <td style="font-size: 13px; font-weight: bold; padding: 7px 10px; background: #f8fafc; vertical-align: middle; text-transform: uppercase;">
                FR.AK.07.&nbsp;&nbsp;&nbsp;&nbsp;CEKLIS PENYESUAIAN YANG WAJAR DAN BERALASAN
            </td>
        </tr>
    </table>

    <!-- Tabel 1: Identitas Skema, TUK, Asesor, Asesi -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: -1px; margin-bottom: 10px;">
        <tr>
            <td width="26%" rowspan="2" valign="middle"><strong>Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)</strong></td>
            <td width="14%">Judul</td>
            <td width="2%">:</td>
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
                <span class="lsp-cb"><?php echo $is_sewaktu ? '☑' : '☐'; ?></span> Sewaktu &nbsp;&nbsp;&nbsp;
                <span class="lsp-cb"><?php echo $is_tempat_kerja ? '☑' : '☐'; ?></span> Tempat Kerja &nbsp;&nbsp;&nbsp;
                <span class="lsp-cb"><?php echo $is_mandiri ? '☑' : '☐'; ?></span> Mandiri*
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
            <td colspan="2"><strong>Tanggal</strong></td>
            <td>:</td>
            <td><?php echo esc_html( $tgl_pelaksanaan ); ?></td>
        </tr>
    </table>
    <div style="font-size: 8.5pt; font-style: italic; color: #475569; margin-top: -6px; margin-bottom: 12px;">
        *Coret yang tidak perlu
    </div>

    <!-- Kotak Panduan Bagi Asesor -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
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
                        <li>Formulir ini dapat digunakan (sebelum pra asesmen, saat pelaksanaan pra asesmen, setelah pra asesmen)* jika ada asesi yang mempunyai keterbatasan sesuai karakteristik yang dimilikinya sehingga diperlukan penyesuaian yang wajar dan beralasan, jika rencana asesmen dan perangkat asesmen tidak sesuai dengan acuan pembanding, potensi asesi dan konteks asesi, jika asesi merasa keletihan, sakit, serta jika kondisi alam, listrik padam, dll.</li>
                        <li>Coretlah pada tanda * yang tidak sesuai.</li>
                        <li>Berilah tanda &radic; pada kotak &lsquo;☐&rsquo; pada kolom potensi asesi.</li>
                        <li>Berilah tanda &radic; Ya atau Tidak pada tanda ** sesuai pilihan, jika jawaban Ya selanjutnya pada kolom keterangan berilah tanda &radic; di kotak &lsquo;☐&rsquo; yang tersedia, pilihan boleh lebih dari satu.</li>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel Potensi Asesi -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 14px;">
        <tr>
            <td width="20%" valign="top" style="padding: 10px; background: #f8fafc; font-weight: bold; font-size: 9pt;">
                Potensi Asesi
            </td>
            <td style="padding: 8px 12px; font-size: 8.5pt; line-height: 1.9;">
                <div><span class="lsp-cb">☐</span> Hasil pelatihan dan / atau pendidikan, dimana Kurikulum dan fasilitas praktek mampu telusur terhadap standar kompetensi</div>
                <div><span class="lsp-cb">☐</span> Hasil pelatihan dan / atau pendidikan, dimana kurikulum belum berbasiskompetensi.</div>
                <div><span class="lsp-cb">☐</span> Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya mampu telusur dengan standar kompetensi</div>
                <div><span class="lsp-cb">☐</span> Pekerja berpengalaman, dimana berasal dari industri/tempat kerja yang dalam operasionalnya belum berbasis kompetensi.</div>
                <div><span class="lsp-cb">☐</span> Pelatihan / belajar mandiri atau otodidak.</div>
            </td>
        </tr>
    </table>

    <!-- Tabel Utama Identifikasi Karakteristik & Penyesuaian -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 14px;">
        <thead>
            <tr style="background: #f1f5f9; text-align: center; font-size: 8.5pt;">
                <th rowspan="2" style="width: 4%; padding: 6px 4px;">No</th>
                <th rowspan="2" style="width: 38%; padding: 6px 8px; text-align: left;">
                    Mengidentifikasi Persyaratan Modifikasi dan Kontekstualisasi (karakteristik asesi):
                </th>
                <th colspan="2" style="width: 14%; padding: 4px;">Diperlukan penyesuaian)**</th>
                <th rowspan="2" style="width: 44%; padding: 6px 8px; text-align: left;">Keterangan</th>
            </tr>
            <tr style="background: #f1f5f9; text-align: center; font-size: 8pt;">
                <th style="width: 7%; padding: 4px;">Ya</th>
                <th style="width: 7%; padding: 4px;">Tidak</th>
            </tr>
        </thead>
        <tbody style="font-size: 8.5pt; line-height: 1.5;">
            <!-- 1 -->
            <tr>
                <td style="text-align: center; vertical-align: top; font-weight: bold; padding: 6px 4px;">1</td>
                <td style="vertical-align: top; padding: 6px 8px;">
                    Keterbatasan asesi terhadap persyaratan bahasa, literasi, numerasi.
                </td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="padding: 6px 8px;">
                    <div><span class="lsp-cb">☐</span> Memerlukan dukungan pembaca, penerjemah, pelayan, penulis untuk merekam jawaban asesi.</div>
                    <div><span class="lsp-cb">☐</span> Melakukan asesmen verbal (gunakan pertanyaan lisan/pertanyaan wawancara) dengan dilengkapi gambar diagram dan bentuk-bentuk visual.</div>
                    <div><span class="lsp-cb">☐</span> Menggunakan Hasil produksi</div>
                    <div><span class="lsp-cb">☐</span> Menggunakan Ceklis observasi/demonstrasi.</div>
                    <div><span class="lsp-cb">☐</span> Menggunakan daftar instruksi terstruktur.</div>
                    <div><span class="lsp-cb">☐</span> &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</div>
                </td>
            </tr>

            <!-- 2 -->
            <tr>
                <td style="text-align: center; vertical-align: top; font-weight: bold; padding: 6px 4px;">2</td>
                <td style="vertical-align: top; padding: 6px 8px;">
                    Penyediaan dukungan pembaca, penerjemah, pelayan, penulis.
                </td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="padding: 6px 8px;">
                    <div><span class="lsp-cb">☐</span> Menggunakan pertanyaan lisan dengan dilengkapi gambar diagram dan bentuk-bentuk visual.</div>
                    <div><span class="lsp-cb">☐</span> Menggunakan pertanyaan wawancara dengan dilengkapi gambar diagram dan bentuk-bentuk visual.</div>
                    <div><span class="lsp-cb">☐</span> &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</div>
                </td>
            </tr>

            <!-- 3 -->
            <tr>
                <td style="text-align: center; vertical-align: top; font-weight: bold; padding: 6px 4px;">3</td>
                <td style="vertical-align: top; padding: 6px 8px;">
                    Penggunaan teknologi adaptif atau peralatan khusus. (Tidak dapat menggunakan teknologi adaptif (misal: mengoperasikan komputer dan printer, peralatan digital dsb).
                </td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="padding: 6px 8px;">
                    <div><span class="lsp-cb">☐</span> Ceklis observasi/demonstrasi.</div>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <span><span class="lsp-cb">☐</span> Pertanyaan lisan</span>
                        <span><span class="lsp-cb">☐</span> Pertanyaan tertulis.</span>
                    </div>
                    <div><span class="lsp-cb">☐</span> Pertanyaan wawancara.</div>
                    <div><span class="lsp-cb">☐</span> Daftar instruksi terstruktur.</div>
                    <div><span class="lsp-cb">☐</span> Ceklis verifikasi portofolio.</div>
                    <div><span class="lsp-cb">☐</span> Menggunakan dukungan operator komputer.</div>
                    <div><span class="lsp-cb">☐</span> &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</div>
                </td>
            </tr>

            <!-- 4 -->
            <tr>
                <td style="text-align: center; vertical-align: top; font-weight: bold; padding: 6px 4px;">4</td>
                <td style="vertical-align: top; padding: 6px 8px;">
                    Pelaksanaan asesmen secara fleksibel karena alasan keletihan atau keperluan pengobatan.
                </td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="padding: 6px 8px;">
                    <div><span class="lsp-cb">☐</span> Menggunakan juru tulis.</div>
                    <div><span class="lsp-cb">☐</span> Menggunakan kameramen perekam video/audio.</div>
                    <div><span class="lsp-cb">☐</span> Memperbolehkan periode waktu yang lebih panjang untuk menyelesaikan tugas pekerjaan dalam asesmen.</div>
                    <div><span class="lsp-cb">☐</span> Melakukan tugas pekerjaan dalam asesmen dengan waktu lebih pendek.</div>
                    <div><span class="lsp-cb">☐</span> Menggunakan instruksi-instruksi spesifik pada proyek yang dapat dilakukan pada berbagai tingkatan.</div>
                    <div><span class="lsp-cb">☐</span> &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</div>
                </td>
            </tr>

            <!-- 5 -->
            <tr>
                <td style="text-align: center; vertical-align: top; font-weight: bold; padding: 6px 4px;">5</td>
                <td style="vertical-align: top; padding: 6px 8px;">
                    Penyediaan peralatan asesmen berupa braille, audio/video-tape.
                </td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="padding: 6px 8px;">
                    <div><span class="lsp-cb">☐</span> Menggunakan pertanyaan lisan.</div>
                    <div><span class="lsp-cb">☐</span> Menggunakan pertanyaan wawancara.</div>
                    <div><span class="lsp-cb">☐</span> &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</div>
                </td>
            </tr>

            <!-- 6 -->
            <tr>
                <td style="text-align: center; vertical-align: top; font-weight: bold; padding: 6px 4px;">6</td>
                <td style="vertical-align: top; padding: 6px 8px;">
                    Penyesuaian tempat fisik/lingkungan asesmen
                </td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="padding: 6px 8px;">
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <span><span class="lsp-cb">☐</span> Pertanyaan lisan.</span>
                        <span><span class="lsp-cb">☐</span> Pertanyaan tulis.</span>
                    </div>
                    <div><span class="lsp-cb">☐</span> Pertanyaan wawancara. &nbsp;&nbsp; <span class="lsp-cb">☐</span> Ceklis Verifikasi portofolio.</div>
                    <div><span class="lsp-cb">☐</span> Ceklis reviu produk.</div>
                    <div><span class="lsp-cb">☐</span> Daftar instruksi terstruktur.</div>
                    <div><span class="lsp-cb">☐</span> &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</div>
                </td>
            </tr>

            <!-- 7 -->
            <tr>
                <td style="text-align: center; vertical-align: top; font-weight: bold; padding: 6px 4px;">7</td>
                <td style="vertical-align: top; padding: 6px 8px;">
                    Pertimbangan umur/usia lanjut/gender asesi. (Adanya perbedaan usia dengan asesor yang lebih muda).
                </td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="padding: 6px 8px;">
                    <div><span class="lsp-cb">☐</span> Menggunakan studi kasus/daftar instruksi terstruktur</div>
                    <div><span class="lsp-cb">☐</span> Menggunakan instrumen asesmen dengan huruf normal jangan terlalu kecil.</div>
                    <div><span class="lsp-cb">☐</span> Menggunakan asesor dengan jenis kelamin yang sama dengan asesi.</div>
                    <div><span class="lsp-cb">☐</span> Menggunakan instrumen asesmen yang sama walaupun berbeda jenis kelamin (tidak boleh memberi tanda tambahan pada instrumen asesmen yang digunakan dengan tujuan untuk membedakan jenis kelamin).</div>
                    <div><span class="lsp-cb">☐</span> &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</div>
                </td>
            </tr>

            <!-- 8 -->
            <tr>
                <td style="text-align: center; vertical-align: top; font-weight: bold; padding: 6px 4px;">8</td>
                <td style="vertical-align: top; padding: 6px 8px;">
                    Pertimbangan budaya/tradisi/agama.
                </td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="text-align: center; vertical-align: top; padding: 6px 4px;"><span class="lsp-cb">☐</span></td>
                <td style="padding: 6px 8px;">
                    <div><span class="lsp-cb">☐</span> Menggunakan studi kasus daftar instruksi terstruktur</div>
                    <div><span class="lsp-cb">☐</span> Menggunakan asesor tanpa pertimbangan budaya/tradisi/agama.</div>
                    <div><span class="lsp-cb">☐</span> Menggunakan instrumen asesmen yang sama walaupun berbeda budaya/tradisi/agama).</div>
                    <div><span class="lsp-cb">☐</span> &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;</div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Kesepakatan Hasil Penyesuaian -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: 12px; margin-bottom: 14px;">
        <tr>
            <td style="padding: 10px 14px; font-size: 8.5pt; line-height: 1.9;">
                <strong>Hasil Penyesuaian yang wajar dan beralasan disepakati menggunakan :</strong><br>
                <strong>1) Acuan Pembanding Asesmen:</strong> &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;<br>
                <span style="font-size: 8pt; color: #64748b; font-style: italic; padding-left: 18px;">(tuliskan nama acuan pembanding)</span><br>
                <strong>2) Metode Asesmen:</strong> &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;<br>
                <span style="font-size: 8pt; color: #64748b; font-style: italic; padding-left: 18px;">(tuliskan nama metode asesmen)</span><br>
                <strong>3) Instrumen Asesmen:</strong> &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;<br>
                <span style="font-size: 8pt; color: #64748b; font-style: italic; padding-left: 18px;">(tuliskan nama formulir instrumen asesmen)</span>
            </td>
        </tr>
    </table>

    <!-- Tanda Tangan Asesor & Asesi -->
    <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: 10px; page-break-inside: avoid;">
        <tr style="background: #f8fafc; font-weight: bold; font-size: 9pt;">
            <td width="50%" style="padding: 6px 10px;">Nama Asesor : &nbsp; <strong><?php echo esc_html( $nama_asesor ); ?></strong></td>
            <td width="50%" style="padding: 6px 10px;">Tanggal dan Tanda Tangan Asesor:</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; height: 55px; vertical-align: top;">
                <div style="font-size: 8pt; color: #64748b;">No. Reg / MET: <code><?php echo esc_html( $no_reg_met ?: '-' ); ?></code></div>
            </td>
            <td style="padding: 6px 10px; height: 55px; vertical-align: bottom; text-align: center;">
                <div style="font-size: 8pt; color: #64748b; margin-bottom: 2px;">Tanggal: <?php echo esc_html( $tgl_pelaksanaan ); ?></div>
            </td>
        </tr>
        <tr style="background: #f8fafc; font-weight: bold; font-size: 9pt;">
            <td style="padding: 6px 10px;">Nama Asesi: &nbsp; <strong><?php echo esc_html( $nama_asesi ); ?></strong></td>
            <td style="padding: 6px 10px;">Tanggal dan Tanda Tangan Asesi:</td>
        </tr>
        <tr>
            <td style="padding: 6px 10px; height: 55px; vertical-align: top;">
                <div style="font-size: 8pt; color: #64748b;">NIK: <?php echo esc_html( $pendaftaran->nik ?? '-' ); ?></div>
            </td>
            <td style="padding: 6px 10px; height: 55px; vertical-align: bottom; text-align: center;">
                <div style="font-size: 8pt; color: #64748b; margin-bottom: 2px;">Tanggal: <?php echo esc_html( $tgl_pelaksanaan ); ?></div>
            </td>
        </tr>
    </table>

</div>
