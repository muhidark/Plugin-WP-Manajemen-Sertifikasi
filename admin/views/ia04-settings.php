<?php
/**
 * View Pengaturan Instrumen FR.IA.04
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h1>
        Instrumen Asesmen FR.IA.04
        <span style="font-size: 14px; font-weight: normal; color: #64748b; margin-left: 10px;">
            Kegiatan Terstruktur (DIT) & Penilaian Proyek Singkat
        </span>
    </h1>

    <?php if ( isset( $_GET['saved'] ) && '1' === $_GET['saved'] ) : ?>
        <div class="notice notice-success is-dismissible" style="margin-top: 15px;">
            <p><strong>Berhasil:</strong> Konfigurasi instrumen FR.IA.04 (Materi STAR & Daftar Pertanyaan Penilaian) berhasil disimpan.</p>
        </div>
    <?php endif; ?>

    <?php if ( isset( $_GET['error'] ) ) : ?>
        <div class="notice notice-error is-dismissible" style="margin-top: 15px;">
            <p><strong>Gagal:</strong> Terjadi kesalahan saat menyimpan instrumen FR.IA.04.</p>
        </div>
    <?php endif; ?>

    <?php 
    $active_paket = ! empty( $paket_muk_id ) && class_exists( 'LSP_Cert_Paket_MUK' ) ? LSP_Cert_Paket_MUK::get_paket( $paket_muk_id ) : null;
    if ( $active_paket ) : 
    ?>
        <div style="background: #fef3c7; border: 1px solid #f59e0b; padding: 12px 18px; border-radius: 6px; margin-top: 15px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
                <strong style="color: #92400e; font-size: 14px;">📦 Mengedit Paket MUK: <?php echo esc_html( $active_paket->nama_paket ); ?> (Versi: <?php echo esc_html( $active_paket->kode_versi ); ?>)</strong>
                <p style="margin: 3px 0 0; color: #78350f; font-size: 12.5px;">Instrumen Proyek Singkat (IA.04) ini disimpan khusus untuk paket bundling versi ini.</p>
            </div>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=paket_muk&skema_id=' . $selected_skema_id ) ); ?>" class="button button-secondary">
                &larr; Kembali ke Bank Data Paket MUK
            </a>
        </div>
    <?php endif; ?>

    <!-- Pemilihan Skema Sertifikasi -->
    <div class="card" style="max-width: 100%; margin-top: 20px; padding: 15px 20px;">
        <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <input type="hidden" name="page" value="lsp-ia04">
            <label for="skema_selector" style="font-weight: 600; font-size: 14px; color: #1e293b;">
                🎯 Pilih Skema Sertifikasi:
            </label>
            <select name="skema_id" id="skema_selector" style="min-width: 350px; font-size: 13.5px; padding: 5px 10px;" onchange="this.form.submit();">
                <?php if ( empty( $all_skema ) ) : ?>
                    <option value="">-- Tidak ada skema tersedia --</option>
                <?php else : ?>
                    <?php foreach ( $all_skema as $s ) : ?>
                        <option value="<?php echo esc_attr( $s['id'] ); ?>" <?php selected( $selected_skema_id, $s['id'] ); ?>>
                            <?php echo esc_html( $s['title'] ); ?> (<?php echo esc_html( $s['kode'] ); ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <button type="submit" class="button">Ganti Skema</button>
        </form>
    </div>

    <?php if ( $selected_skema_id && ! empty( $skema_data ) ) : ?>
        <div style="display: grid; grid-template-columns: 2.2fr 1fr; gap: 20px; margin-top: 20px; align-items: start;">

            <!-- Form Utama -->
            <div class="card" style="padding: 20px;">
                <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia04' ) ); ?>" id="form-ia04">
                    <?php wp_nonce_field( 'lsp_ia04_save_nonce', 'lsp_ia04_nonce' ); ?>
                    <input type="hidden" name="action" value="lsp_save_ia04">
                    <input type="hidden" name="skema_id" value="<?php echo esc_attr( $selected_skema_id ); ?>">
                    <?php if ( ! empty( $paket_muk_id ) ) : ?>
                        <input type="hidden" name="paket_muk_id" value="<?php echo esc_attr( $paket_muk_id ); ?>">
                    <?php endif; ?>

                    <!-- ========================================== -->
                    <!-- BAGIAN A — MATERI FR.IA.04A (METODE STAR)  -->
                    <!-- ========================================== -->
                    <div style="background: #f8fafc; border: 1px solid #0284c7; border-left-width: 5px; padding: 16px 20px; border-radius: 6px; margin-bottom: 25px;">
                        <h2 style="margin: 0 0 12px 0; font-size: 16px; color: #0369a1; display: flex; align-items: center; gap: 8px;">
                            📋 BAGIAN A: Materi FR.IA.04A — Penjelasan Proyek Singkat (DIT)
                        </h2>
                        <p style="margin: 0 0 15px 0; font-size: 12px; color: #475569;">
                            Petunjuk penugasan proyek terstruktur untuk asesi berbasis kerangka <strong>STAR (Situation, Task, Action, Result)</strong>.
                        </p>

                        <!-- 1. Judul Kegiatan Terstruktur -->
                        <div style="margin-bottom: 16px;">
                            <label for="judul_kegiatan" style="display: block; font-weight: 600; margin-bottom: 5px; color: #1e293b;">
                                1. Judul Proyek / Kegiatan Terstruktur:
                            </label>
                            <input type="text" id="judul_kegiatan" name="judul_kegiatan" 
                                   value="<?php echo esc_attr( $instrument['judul_kegiatan'] ); ?>" 
                                   class="large-text" style="font-size: 13.5px; padding: 6px 10px;" 
                                   placeholder="Contoh: Pembuatan Video Profil Perusahaan UMKM Kuliner">
                        </div>

                        <!-- 2. Situasi (Situation) -->
                        <div style="margin-bottom: 16px;">
                            <label for="situasi" style="display: block; font-weight: 600; margin-bottom: 5px; color: #1e293b;">
                                2. S — Situation (Situasi / Latar Belakang Studi Kasus):
                            </label>
                            <textarea id="situasi" name="situasi" rows="4" class="large-text" style="font-size: 13px; line-height: 1.5;"
                                      placeholder="Uraikan latar belakang klien, konteks bisnis, atau situasi masalah yang dihadapi..."><?php echo esc_textarea( $instrument['situasi'] ); ?></textarea>
                        </div>

                        <!-- 3. Tugas (Task) -->
                        <div style="margin-bottom: 16px;">
                            <label for="tugas" style="display: block; font-weight: 600; margin-bottom: 5px; color: #1e293b;">
                                3. T — Task (Tugas / Peran Asesi):
                            </label>
                            <textarea id="tugas" name="tugas" rows="4" class="large-text" style="font-size: 13px; line-height: 1.5;"
                                      placeholder="Tugaskan peran asesi secara spesifik dalam menyelesaikan proyek ini..."><?php echo esc_textarea( $instrument['tugas'] ); ?></textarea>
                        </div>

                        <!-- 4. Tindakan (Action) -->
                        <div style="margin-bottom: 16px;">
                            <label for="tindakan" style="display: block; font-weight: 600; margin-bottom: 5px; color: #1e293b;">
                                4. A — Action (Tindakan / Instruksi Kerja untuk Asesi):
                            </label>
                            <textarea id="tindakan" name="tindakan" rows="7" class="large-text" style="font-size: 13px; line-height: 1.5;"
                                      placeholder="Daftar tindakan konkret langkah demi langkah yang wajib dijalankan asesi..."><?php echo esc_textarea( $instrument['tindakan'] ); ?></textarea>
                        </div>

                        <!-- 5. Data / Informasi yang Disediakan -->
                        <div style="margin-bottom: 16px;">
                            <label for="data_informasi" style="display: block; font-weight: 600; margin-bottom: 5px; color: #1e293b;">
                                5. Data / Informasi yang Disediakan:
                            </label>
                            <textarea id="data_informasi" name="data_informasi" rows="3" class="large-text" style="font-size: 13px; line-height: 1.5;"
                                      placeholder="Contoh: Brief klien, denah lokasi, daftar peralatan yang tersedia..."><?php echo esc_textarea( $instrument['data_informasi'] ); ?></textarea>
                        </div>

                        <!-- 6. Hasil yang Diharapkan (Result) -->
                        <div style="margin-bottom: 16px;">
                            <label for="hasil" style="display: block; font-weight: 600; margin-bottom: 5px; color: #1e293b;">
                                6. R — Result (Hasil / Keluaran yang Diharapkan):
                            </label>
                            <textarea id="hasil" name="hasil" rows="5" class="large-text" style="font-size: 13px; line-height: 1.5;"
                                      placeholder="Daftar dokumen fisik, storyboard, laporan, atau berkas final yang wajib dikumpulkan..."><?php echo esc_textarea( $instrument['hasil'] ); ?></textarea>
                        </div>

                        <!-- 7. Waktu Pelaksanaan Proyek -->
                        <div style="margin-bottom: 16px;">
                            <label for="waktu_proyek" style="display: block; font-weight: 600; margin-bottom: 5px; color: #1e293b;">
                                7. Alokasi Waktu Pengerjaan Proyek:
                            </label>
                            <input type="text" id="waktu_proyek" name="waktu_proyek" 
                                   value="<?php echo esc_attr( $instrument['waktu_proyek'] ); ?>" 
                                   class="regular-text" style="font-weight: bold; width: 180px;" placeholder="Contoh: 240 menit">
                        </div>

                        <!-- 8. Hal yang Perlu Didemonstrasikan -->
                        <div style="margin-bottom: 16px;">
                            <label for="hal_demonstrasi" style="display: block; font-weight: 600; margin-bottom: 5px; color: #1e293b;">
                                8. Hal yang Perlu Didemonstrasikan / Dipresentasikan kepada Asesor:
                            </label>
                            <textarea id="hal_demonstrasi" name="hal_demonstrasi" rows="5" class="large-text" style="font-size: 13px; line-height: 1.5;"
                                      placeholder="Asesi mempresentasikan kepada asesor: konsep, dokumentasi teknis, hasil karya akhir..."><?php echo esc_textarea( $instrument['hal_demonstrasi'] ); ?></textarea>
                        </div>

                        <!-- 9. Waktu Presentasi / Demonstrasi -->
                        <div>
                            <label for="waktu_presentasi" style="display: block; font-weight: 600; margin-bottom: 5px; color: #1e293b;">
                                9. Alokasi Waktu Presentasi / Demonstrasi:
                            </label>
                            <input type="text" id="waktu_presentasi" name="waktu_presentasi" 
                                   value="<?php echo esc_attr( $instrument['waktu_presentasi'] ); ?>" 
                                   class="regular-text" style="font-weight: bold; width: 180px;" placeholder="Contoh: 30 menit">
                        </div>
                    </div>

                    <!-- ========================================== -->
                    <!-- BAGIAN B — MATERI FR.IA.04B (PERTANYAAN)   -->
                    <!-- ========================================== -->
                    <div style="background: #fdfefe; border: 1px solid #16a34a; border-left-width: 5px; padding: 16px 20px; border-radius: 6px; margin-bottom: 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                            <div>
                                <h2 style="margin: 0; font-size: 16px; color: #15803d; display: flex; align-items: center; gap: 8px;">
                                    ⚖️ BAGIAN B: Materi FR.IA.04B — Daftar Pertanyaan Penilaian Asesor
                                </h2>
                                <p style="margin: 4px 0 0 0; font-size: 12px; color: #475569;">
                                    Susun pertanyaan presentasi/wawancara asesor berdasarkan lingkup unit kompetensi dan dimensi kompetensi.
                                </p>
                            </div>
                            <button type="button" id="btn-add-question" class="button button-secondary" style="font-weight: 600; color: #15803d; border-color: #15803d;">
                                ➕ Tambah Pertanyaan
                            </button>
                        </div>

                        <div id="questions-container">
                            <?php 
                            $questions = ! empty( $instrument['questions'] ) ? $instrument['questions'] : array();
                            if ( empty( $questions ) ) {
                                // Default baris pertama jika masih kosong
                                $questions[] = array(
                                    'order'      => 1,
                                    'unit_id'    => '',
                                    'lingkup'    => '',
                                    'pertanyaan' => '',
                                    'elemen_kuk' => '',
                                    'dimensi'    => 'TS',
                                );
                            }
                            ?>
                            <?php foreach ( $questions as $idx => $q ) : ?>
                                <div class="ia04b-question-row" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 14px 16px; margin-bottom: 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
                                        <strong style="color: #1e293b; font-size: 13.5px;">
                                            Pertanyaan #<span class="row-number"><?php echo ( $idx + 1 ); ?></span>
                                        </strong>
                                        <button type="button" class="button-link-delete btn-remove-row" style="color: #dc2626; cursor: pointer; text-decoration: none; font-size: 12px;">
                                            ✕ Hapus Baris
                                        </button>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 10px;">
                                        <!-- Lingkup Penyajian / Pilihan Unit Master Skema -->
                                        <div>
                                            <label style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 3px;">
                                                Pilih Unit Kompetensi (Master Skema):
                                            </label>
                                            <select class="sel-unit-prefill" style="width: 100%; font-size: 12px;">
                                                <option value="">-- Pilih dari Master Unit atau Ketik Bebas --</option>
                                                <?php foreach ( $units_skkni as $u ) : 
                                                    $u_k = is_object( $u ) ? $u->kode_unit : ( $u['kode'] ?? '' );
                                                    $u_j = is_object( $u ) ? $u->judul_unit : ( $u['title'] ?? '' );
                                                ?>
                                                    <option value="<?php echo esc_attr( $u_k ); ?>" data-title="<?php echo esc_attr( $u_j ); ?>" <?php selected( $q['unit_id'] ?? '', $u_k ); ?>>
                                                        <?php echo esc_html( $u_k . ' — ' . $u_j ); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="hidden" name="questions[<?php echo $idx; ?>][unit_id]" class="input-unit-id" value="<?php echo esc_attr( $q['unit_id'] ?? '' ); ?>">
                                        </div>

                                        <div>
                                            <label style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 3px;">
                                                Lingkup Penyajian / Topik (Teks Cetak):
                                            </label>
                                            <input type="text" name="questions[<?php echo $idx; ?>][lingkup]" class="input-lingkup" 
                                                   value="<?php echo esc_attr( $q['lingkup'] ?? '' ); ?>" 
                                                   style="width: 100%; font-size: 12px;" placeholder="Contoh: Mengelola Gagasan Konsumen">
                                        </div>
                                    </div>

                                    <!-- Pertanyaan Asesor -->
                                    <div style="margin-bottom: 10px;">
                                        <label style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 3px;">
                                            Pertanyaan Asesor:
                                        </label>
                                        <textarea name="questions[<?php echo $idx; ?>][pertanyaan]" rows="3" style="width: 100%; font-size: 12.5px; line-height: 1.4;" 
                                                  placeholder="Masukkan pertanyaan wawancara / presentasi untuk asesi..."><?php echo esc_textarea( $q['pertanyaan'] ?? '' ); ?></textarea>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                        <!-- Referensi Elemen / KUK -->
                                        <div>
                                            <label style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 3px;">
                                                Kesesuaian Standar (Unit / Elemen / KUK):
                                            </label>
                                            <input type="text" name="questions[<?php echo $idx; ?>][elemen_kuk]" 
                                                   value="<?php echo esc_attr( $q['elemen_kuk'] ?? '' ); ?>" 
                                                   style="width: 100%; font-size: 12px;" placeholder="Contoh: E1/KUK/1.2 atau E2/KUK/2.1; E3/KUK/3.1">
                                        </div>

                                        <!-- Dimensi Kompetensi -->
                                        <div>
                                            <label style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 3px;">
                                                Dimensi Kompetensi:
                                            </label>
                                            <input type="text" name="questions[<?php echo $idx; ?>][dimensi]" 
                                                   value="<?php echo esc_attr( $q['dimensi'] ?? '' ); ?>" 
                                                   style="width: 100%; font-size: 12px;" placeholder="Contoh: TS, TMS, CMS, JRES, TRS">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="button" id="btn-add-question-bottom" class="button button-secondary" style="font-weight: 600; color: #15803d; border-color: #15803d; margin-top: 5px;">
                            ➕ Tambah Pertanyaan Penilaian
                        </button>
                    </div>

                    <!-- Tombol Simpan -->
                    <p class="submit" style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                        <button type="submit" class="button button-primary button-large" style="font-weight: 700; padding: 6px 24px; font-size: 14px;">
                            💾 Simpan Konfigurasi Instrumen FR.IA.04
                        </button>
                    </p>
                </form>
            </div>

            <!-- Panel Samping: Info & Pratinjau Cetak -->
            <div>
                <!-- Pratinjau Cetak Dokumen -->
                <div class="card" style="padding: 18px; margin-bottom: 20px; border-left: 4px solid #16a34a;">
                    <h3 style="margin-top: 0; font-size: 14.5px; color: #166534;">
                        🖨️ Pratinjau Dokumen Cetak
                    </h3>
                    <p style="font-size: 12px; color: #475569; margin-bottom: 12px;">
                        Cetak atau periksa hasil layout cetak formulir FR.IA.04 untuk skema ini:
                    </p>

                    <?php if ( $sample_kegiatan ) : ?>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $sample_kegiatan->id . '&doc=ia04a' ) ); ?>" 
                               target="_blank" class="button" style="text-align: left; font-weight: 600; color: #0284c7;">
                                📋 Cetak FR.IA.04A (DIT Proyek Singkat)
                            </a>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $sample_kegiatan->id . '&doc=ia04b' ) ); ?>" 
                               target="_blank" class="button" style="text-align: left; font-weight: 600; color: #15803d;">
                                ⚖️ Cetak FR.IA.04B (Penilaian Proyek Singkat)
                            </a>
                        </div>
                        <p style="font-size: 11px; color: #64748b; margin-top: 10px;">
                            *Menggunakan data kegiatan #<?php echo $sample_kegiatan->id; ?> (<?php echo esc_html( $sample_kegiatan->judul_kegiatan ?? '' ); ?>).
                        </p>
                    <?php else : ?>
                        <p style="font-size: 12px; color: #b45309; background: #fffbeb; padding: 8px; border-radius: 4px; border: 1px solid #fde68a;">
                            Belum ada kegiatan yang menggunakan skema ini. Buat kegiatan baru pada menu <strong>Kegiatan</strong> untuk mengaktifkan cetak langsung.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Petunjuk BNSP Kerangka STAR & 5 Dimensi -->
                <div class="card" style="padding: 18px; border-left: 4px solid #0284c7;">
                    <h3 style="margin-top: 0; font-size: 14px; color: #0369a1;">
                        ℹ️ Petunjuk Standar BNSP (IA.04)
                    </h3>
                    <ul style="font-size: 12px; color: #475569; padding-left: 18px; line-height: 1.6; margin-bottom: 0;">
                        <li><strong>FR.IA.04A</strong>: Daftar Instruksi Terstruktur (DIT) berbasis kerangka <strong>STAR</strong> (Situation, Task, Action, Result) yang dibagikan kepada asesi untuk memandu pengerjaan proyek singkat.</li>
                        <li><strong>FR.IA.04B</strong>: Lembar penilaian asesor selama presentasi atau wawancara proyek. Pertanyaan mencakup <strong>5 Dimensi Kompetensi</strong> (TS, TMS, CMS, JRES, TRS).</li>
                        <li>Seluruh tanggapan asesi, checkbox Ya/Tidak, dan rekomendasi Kompeten/Belum Kompeten <strong>tercetak kosong</strong> untuk penilaian fisik manual.</li>
                    </ul>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>

<!-- Template HTML untuk Baris Pertanyaan Baru -->
<template id="ia04b-row-template">
    <div class="ia04b-question-row" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 14px 16px; margin-bottom: 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
            <strong style="color: #1e293b; font-size: 13.5px;">
                Pertanyaan #<span class="row-number">__INDEX_PLUS_1__</span>
            </strong>
            <button type="button" class="button-link-delete btn-remove-row" style="color: #dc2626; cursor: pointer; text-decoration: none; font-size: 12px;">
                ✕ Hapus Baris
            </button>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 10px;">
            <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 3px;">
                    Pilih Unit Kompetensi (Master Skema):
                </label>
                <select class="sel-unit-prefill" style="width: 100%; font-size: 12px;">
                    <option value="">-- Pilih dari Master Unit atau Ketik Bebas --</option>
                    <?php foreach ( $units_skkni as $u ) : 
                        $u_k = is_object( $u ) ? $u->kode_unit : ( $u['kode'] ?? '' );
                        $u_j = is_object( $u ) ? $u->judul_unit : ( $u['title'] ?? '' );
                    ?>
                        <option value="<?php echo esc_attr( $u_k ); ?>" data-title="<?php echo esc_attr( $u_j ); ?>">
                            <?php echo esc_html( $u_k . ' — ' . $u_j ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="questions[__INDEX__][unit_id]" class="input-unit-id" value="">
            </div>

            <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 3px;">
                    Lingkup Penyajian / Topik (Teks Cetak):
                </label>
                <input type="text" name="questions[__INDEX__][lingkup]" class="input-lingkup" 
                       value="" style="width: 100%; font-size: 12px;" placeholder="Contoh: Mengelola Gagasan Konsumen">
            </div>
        </div>

        <div style="margin-bottom: 10px;">
            <label style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 3px;">
                Pertanyaan Asesor:
            </label>
            <textarea name="questions[__INDEX__][pertanyaan]" rows="3" style="width: 100%; font-size: 12.5px; line-height: 1.4;" 
                      placeholder="Masukkan pertanyaan wawancara / presentasi untuk asesi..."></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 3px;">
                    Kesesuaian Standar (Unit / Elemen / KUK):
                </label>
                <input type="text" name="questions[__INDEX__][elemen_kuk]" 
                       value="" style="width: 100%; font-size: 12px;" placeholder="Contoh: E1/KUK/1.2">
            </div>

            <div>
                <label style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 3px;">
                    Dimensi Kompetensi:
                </label>
                <input type="text" name="questions[__INDEX__][dimensi]" 
                       value="TS" style="width: 100%; font-size: 12px;" placeholder="Contoh: TS, TMS, CMS, JRES, TRS">
            </div>
        </div>
    </div>
</template>

<script>
jQuery(document).ready(function($) {
    function reindexRows() {
        $('#questions-container .ia04b-question-row').each(function(i, row) {
            $(row).find('.row-number').text(i + 1);
            $(row).find('input, select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    var newName = name.replace(/questions\[\d+\]/, 'questions[' + i + ']');
                    $(this).attr('name', newName);
                }
            });
        });
    }

    function addRow() {
        var count = $('#questions-container .ia04b-question-row').length;
        var tpl = $('#ia04b-row-template').html();
        tpl = tpl.replace(/__INDEX__/g, count);
        tpl = tpl.replace(/__INDEX_PLUS_1__/g, count + 1);
        $('#questions-container').append(tpl);
    }

    $('#btn-add-question, #btn-add-question-bottom').on('click', function(e) {
        e.preventDefault();
        addRow();
    });

    $(document).on('click', '.btn-remove-row', function(e) {
        e.preventDefault();
        if ($('#questions-container .ia04b-question-row').length <= 1) {
            alert('Minimal harus ada 1 baris pertanyaan.');
            return;
        }
        $(this).closest('.ia04b-question-row').remove();
        reindexRows();
    });

    $(document).on('change', '.sel-unit-prefill', function() {
        var $row = $(this).closest('.ia04b-question-row');
        var unitId = $(this).val();
        var unitTitle = $(this).find(':selected').data('title') || '';
        $row.find('.input-unit-id').val(unitId);
        if (unitTitle && !$row.find('.input-lingkup').val()) {
            $row.find('.input-lingkup').val(unitTitle);
        }
    });
});
</script>
