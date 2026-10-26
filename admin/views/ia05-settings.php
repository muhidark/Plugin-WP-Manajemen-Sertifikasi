<?php
/**
 * View Pengaturan Instrumen FR.IA.05
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h1>
        Instrumen Asesmen FR.IA.05
        <span style="font-size: 14px; font-weight: normal; color: #64748b; margin-left: 10px;">
            Pertanyaan Tertulis Pilihan Ganda, Kunci Jawaban & Lembar Jawaban
        </span>
    </h1>

    <?php if ( isset( $_GET['saved'] ) && '1' === $_GET['saved'] ) : ?>
        <div class="notice notice-success is-dismissible" style="margin-top: 15px;">
            <p><strong>Berhasil:</strong> Konfigurasi instrumen FR.IA.05 untuk skema sertifikasi berhasil disimpan.</p>
        </div>
    <?php endif; ?>

    <?php if ( isset( $_GET['error'] ) ) : ?>
        <div class="notice notice-error is-dismissible" style="margin-top: 15px;">
            <p><strong>Gagal:</strong> Skema sertifikasi tidak valid atau terjadi kesalahan saat menyimpan.</p>
        </div>
    <?php endif; ?>

    <?php 
    $active_paket = ! empty( $paket_muk_id ) && class_exists( 'LSP_Cert_Paket_MUK' ) ? LSP_Cert_Paket_MUK::get_paket( $paket_muk_id ) : null;
    if ( $active_paket ) : 
    ?>
        <div style="background: #fef3c7; border: 1px solid #f59e0b; padding: 12px 18px; border-radius: 6px; margin-top: 15px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
                <strong style="color: #92400e; font-size: 14px;">📦 Mengedit Paket MUK: <?php echo esc_html( $active_paket->nama_paket ); ?> (Versi: <?php echo esc_html( $active_paket->kode_versi ); ?>)</strong>
                <p style="margin: 3px 0 0; color: #78350f; font-size: 12.5px;">Instrumen Tes Pilihan Ganda (IA.05) ini disimpan khusus untuk paket bundling versi ini.</p>
            </div>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=paket_muk&skema_id=' . $selected_skema_id ) ); ?>" class="button button-secondary">
                &larr; Kembali ke Bank Data Paket MUK
            </a>
        </div>
    <?php endif; ?>

    <!-- Pemilihan Skema Sertifikasi -->
    <div class="card" style="max-width: 100%; margin-top: 20px; padding: 15px 20px;">
        <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <input type="hidden" name="page" value="lsp-ia05">
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
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 20px; align-items: start;">

            <!-- Form Input Utama -->
            <div class="card" style="padding: 20px;">
                <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; font-size: 16px;">
                    📝 Materi Instrumen Soal & Kunci Jawaban: <span style="color: #0284c7;"><?php echo esc_html( $skema_data['title'] ); ?></span>
                </h2>

                <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia05' ) ); ?>">
                    <?php wp_nonce_field( 'lsp_ia05_save_nonce', 'lsp_ia05_nonce' ); ?>
                    <input type="hidden" name="action" value="lsp_save_ia05">
                    <input type="hidden" name="skema_id" value="<?php echo esc_attr( $selected_skema_id ); ?>">
                    <?php if ( ! empty( $paket_muk_id ) ) : ?>
                        <input type="hidden" name="paket_muk_id" value="<?php echo esc_attr( $paket_muk_id ); ?>">
                    <?php endif; ?>

                    <!-- Field C: Jumlah Soal -->
                    <div style="margin-bottom: 20px; background: #f8fafc; padding: 12px 15px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <label for="jumlah_soal" style="display: block; font-weight: 600; margin-bottom: 6px; color: #1e293b;">
                            1. Jumlah Soal (Angka Positif):
                        </label>
                        <input type="number" id="jumlah_soal" name="jumlah_soal" min="1" max="200" 
                               value="<?php echo esc_attr( $instrument['jumlah_soal'] ?: 10 ); ?>" 
                               class="small-text" style="font-size: 15px; font-weight: bold; width: 90px; padding: 4px 8px;">
                        <p class="description" style="margin-top: 4px; font-size: 12px;">
                            Jumlah soal ini menentukan banyaknya baris jawaban yang otomatis tercetak pada formulir <strong>FR.IA.05C (Lembar Jawaban)</strong>.
                        </p>
                    </div>

                    <!-- Field A: Pertanyaan Pilihan Ganda -->
                    <div style="margin-bottom: 25px;">
                        <label for="pertanyaan" style="display: block; font-weight: 600; margin-bottom: 6px; color: #1e293b;">
                            2. Naskah Pertanyaan Pilihan Ganda (Multiline Textarea):
                        </label>
                        <textarea id="pertanyaan" name="pertanyaan" rows="18" class="large-text" 
                                  style="font-family: Consolas, Monaco, monospace; font-size: 13px; line-height: 1.5; padding: 10px;" 
                                  placeholder="Masukkan seluruh pertanyaan beserta pilihan a, b, c, d sekaligus...&#10;&#10;Contoh:&#10;1. Apa yang dimaksud dengan ...?&#10;a. Opsi 1&#10;b. Opsi 2&#10;c. Opsi 3&#10;d. Opsi 4&#10;&#10;2. Pertanyaan kedua...?&#10;a. Opsi 1&#10;b. Opsi 2&#10;c. Opsi 3&#10;d. Opsi 4"><?php echo esc_textarea( $instrument['pertanyaan'] ); ?></textarea>
                        <p class="description" style="margin-top: 4px; font-size: 12px;">
                            Masukkan teks pertanyaan dan pilihan jawaban sekaligus. Susunan baris, penomoran, dan indentasi akan dipertahankan persis sesuai teks yang Anda masukkan saat mencetak <strong>FR.IA.05A</strong>.
                        </p>
                    </div>

                    <!-- Field B: Kunci Jawaban -->
                    <div style="margin-bottom: 25px;">
                        <label for="kunci" style="display: block; font-weight: 600; margin-bottom: 6px; color: #1e293b;">
                            3. Kunci Jawaban (Multiline Textarea):
                        </label>
                        <textarea id="kunci" name="kunci" rows="10" class="regular-text" 
                                  style="font-family: Consolas, Monaco, monospace; font-size: 13px; line-height: 1.5; padding: 10px; width: 100%; max-width: 400px;" 
                                  placeholder="1. B&#10;2. C&#10;3. A&#10;4. D&#10;5. B&#10;..."><?php echo esc_textarea( $instrument['kunci'] ); ?></textarea>
                        <p class="description" style="margin-top: 4px; font-size: 12px;">
                            Masukkan daftar nomor dan opsi kunci jawaban (contoh: <code>1. B</code>, <code>2. C</code>). Dokumen ini bersifat terbatas untuk asesor/admin pada <strong>FR.IA.05B</strong>.
                        </p>
                    </div>

                    <p class="submit" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                        <button type="submit" class="button button-primary button-large" style="font-weight: 700; padding: 6px 20px;">
                            💾 Simpan Konfigurasi Instrumen
                        </button>
                    </p>
                </form>
            </div>

            <!-- Panel Samping: Info & Pratinjau Cetak -->
            <div>
                <!-- Pratinjau & Cetak Dokumen -->
                <div class="card" style="padding: 18px; margin-bottom: 20px; border-left: 4px solid #16a34a;">
                    <h3 style="margin-top: 0; font-size: 14.5px; color: #166534;">
                        🖨️ Pratinjau Dokumen Cetak
                    </h3>
                    <p style="font-size: 12px; color: #475569; margin-bottom: 12px;">
                        Gunakan tombol di bawah untuk mencetak atau melihat pratinjau format formulir FR.IA.05 untuk skema ini:
                    </p>

                    <?php if ( $sample_kegiatan ) : ?>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $sample_kegiatan->id . '&doc=ia05a' ) ); ?>" 
                               target="_blank" class="button" style="text-align: left; font-weight: 600; color: #0284c7;">
                                📝 Cetak FR.IA.05A (Soal PG)
                            </a>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $sample_kegiatan->id . '&doc=ia05b' ) ); ?>" 
                               target="_blank" class="button" style="text-align: left; font-weight: 600; color: #dc2626;">
                                🔒 Cetak FR.IA.05B (Kunci Jawaban)
                            </a>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $sample_kegiatan->id . '&doc=ia05c' ) ); ?>" 
                               target="_blank" class="button" style="text-align: left; font-weight: 600; color: #16a34a;">
                                ✍️ Cetak FR.IA.05C (Lembar Jawaban)
                            </a>
                        </div>
                        <p style="font-size: 11px; color: #64748b; margin-top: 10px;">
                            *Menggunakan data kegiatan #<?php echo $sample_kegiatan->id; ?> (<?php echo esc_html( $sample_kegiatan->judul_kegiatan ?? '' ); ?>).
                        </p>
                    <?php else : ?>
                        <p style="font-size: 12px; color: #b45309; background: #fffbeb; padding: 8px; border-radius: 4px; border: 1px solid #fde68a;">
                            Belum ada kegiatan yang menggunakan skema ini. Buat kegiatan baru pada menu <strong>Kegiatan</strong> untuk mengaktifkan tautan cetak langsung.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Petunjuk Standar BNSP -->
                <div class="card" style="padding: 18px; border-left: 4px solid #0284c7;">
                    <h3 style="margin-top: 0; font-size: 14px; color: #0369a1;">
                        ℹ️ Petunjuk Standar BNSP
                    </h3>
                    <ul style="font-size: 12px; color: #475569; padding-left: 18px; line-height: 1.6; margin-bottom: 0;">
                        <li><strong>FR.IA.05A</strong>: Naskah pertanyaan tertulis pilihan ganda untuk asesi. Kunci jawaban tidak ditampilkan.</li>
                        <li><strong>FR.IA.05B</strong>: Kunci jawaban resmi. <em>Dokumen terbatas</em> yang hanya dapat dibuka oleh Admin dan Asesor.</li>
                        <li><strong>FR.IA.05C</strong>: Lembar isian jawaban asesi. Seluruh jawaban dan checklist K/BK tercetak kosong untuk diisi secara fisik.</li>
                    </ul>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>
