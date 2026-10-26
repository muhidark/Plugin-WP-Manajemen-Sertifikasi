<?php
/**
 * View Pengaturan Instrumen FR.IA.02 — Tugas Praktik Demonstrasi (TPD)
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h1>
        Instrumen Asesmen FR.IA.02
        <span style="font-size: 14px; font-weight: normal; color: #64748b; margin-left: 10px;">
            TPD — Tugas Praktik Demonstrasi
        </span>
    </h1>

    <?php if ( isset( $_GET['saved'] ) && '1' === $_GET['saved'] ) : ?>
        <div class="notice notice-success is-dismissible" style="margin-top: 15px;">
            <p><strong>Berhasil:</strong> Konfigurasi instrumen FR.IA.02 (Skenario, Langkah Kerja, Peralatan & Perlengkapan) berhasil disimpan.</p>
        </div>
    <?php endif; ?>

    <?php 
    $active_paket = ! empty( $paket_muk_id ) && class_exists( 'LSP_Cert_Paket_MUK' ) ? LSP_Cert_Paket_MUK::get_paket( $paket_muk_id ) : null;
    if ( $active_paket ) : 
    ?>
        <div style="background: #fef3c7; border: 1px solid #f59e0b; padding: 12px 18px; border-radius: 6px; margin-top: 15px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
                <strong style="color: #92400e; font-size: 14px;">📦 Mengedit Paket MUK: <?php echo esc_html( $active_paket->nama_paket ); ?> (Versi: <?php echo esc_html( $active_paket->kode_versi ); ?>)</strong>
                <p style="margin: 3px 0 0; color: #78350f; font-size: 12.5px;">Instrumen Tugas Praktik (IA.02) ini disimpan khusus untuk paket bundling versi ini.</p>
            </div>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=paket_muk&skema_id=' . $selected_skema_id ) ); ?>" class="button button-secondary">
                &larr; Kembali ke Bank Data Paket MUK
            </a>
        </div>
    <?php endif; ?>

    <!-- Pemilihan Skema Sertifikasi -->
    <div class="card" style="max-width: 100%; margin-top: 20px; padding: 15px 20px;">
        <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <input type="hidden" name="page" value="lsp-ia02">
            <?php if ( ! empty( $paket_muk_id ) ) : ?>
                <input type="hidden" name="paket_muk_id" value="<?php echo esc_attr( $paket_muk_id ); ?>">
            <?php endif; ?>
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
        <div style="display: grid; grid-template-columns: 2.2fr 1fr; gap: 20px; margin-top: 20px; align-items: start; max-width: 1280px;">

            <!-- Form Utama -->
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 22px; width: 100%; box-sizing: border-box;">
                <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia02' ) ); ?>" id="form-ia02">
                    <?php wp_nonce_field( 'lsp_ia02_save_nonce', 'lsp_ia02_nonce' ); ?>
                    <input type="hidden" name="action" value="lsp_save_ia02">
                    <input type="hidden" name="skema_id" value="<?php echo esc_attr( $selected_skema_id ); ?>">
                    <?php if ( ! empty( $paket_muk_id ) ) : ?>
                        <input type="hidden" name="paket_muk_id" value="<?php echo esc_attr( $paket_muk_id ); ?>">
                    <?php endif; ?>

                    <div style="background: #f0fdf4; border: 1px solid #16a34a; border-left-width: 5px; padding: 14px 18px; border-radius: 6px; margin-bottom: 22px;">
                        <h2 style="margin: 0 0 6px 0; font-size: 15px; color: #15803d; font-weight: 700;">
                            🛠️ Formulir Input Manual FR.IA.02 (Tugas Praktik Demonstrasi)
                        </h2>
                        <p style="margin: 0; font-size: 12.5px; color: #166534;">
                            Data yang Anda simpan di bawah ini akan langsung tampil secara otomatis pada lembar cetak dokumen <strong>FR.IA.02</strong> asesi.
                        </p>
                    </div>

                    <!-- 1. Skenario Tugas Praktik Demonstrasi -->
                    <div style="margin-bottom: 20px;">
                        <label for="skenario" style="display: block; font-weight: 600; margin-bottom: 6px; color: #1e293b; font-size: 13.5px;">
                            A. Skenario Tugas Praktik Demonstrasi:
                        </label>
                        <p style="margin: 0 0 8px 0; font-size: 12px; color: #64748b;">
                            Uraikan skenario atau konteks pekerjaan yang harus diperagakan/diselesaikan oleh asesi di hadapan asesor.
                        </p>
                        <textarea id="skenario" name="skenario" rows="5" 
                                  style="width: 100%; box-sizing: border-box; font-size: 13px; line-height: 1.5; font-family: inherit; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; resize: vertical;" 
                                  placeholder="Contoh: Anda seorang staf akuntansi/programmer yang ditugaskan untuk..."><?php echo esc_textarea( $instrument['skenario'] ); ?></textarea>
                    </div>

                    <!-- 2. Langkah-langkah Kerja yang Perlu Dilakukan -->
                    <div style="margin-bottom: 20px;">
                        <label for="langkah_kerja" style="display: block; font-weight: 600; margin-bottom: 6px; color: #1e293b; font-size: 13.5px;">
                            B. Langkah-langkah Kerja yang Perlu Dilakukan:
                        </label>
                        <p style="margin: 0 0 8px 0; font-size: 12px; color: #64748b;">
                            Tuliskan instruksi langkah kerja berurutan (pisahkan tiap langkah dengan baris baru).
                        </p>
                        <textarea id="langkah_kerja" name="langkah_kerja" rows="6" 
                                  style="width: 100%; box-sizing: border-box; font-size: 13px; line-height: 1.5; font-family: inherit; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; resize: vertical;" 
                                  placeholder="1. Mempersiapkan seluruh perangkat kerja...&#10;2. Melaksanakan tahapan pekerjaan..."><?php echo esc_textarea( $instrument['langkah_kerja'] ); ?></textarea>
                    </div>

                    <!-- 3. Hasil Pekerjaan -->
                    <div style="margin-bottom: 20px;">
                        <label for="hasil_kerja" style="display: block; font-weight: 600; margin-bottom: 6px; color: #1e293b; font-size: 13.5px;">
                            C. Hasil Pekerjaan:
                        </label>
                        <p style="margin: 0 0 8px 0; font-size: 12px; color: #64748b;">
                            Spesifikasi output, produk, dokumen, atau demonstrasi fisik yang harus diserahkan kepada asesor.
                        </p>
                        <textarea id="hasil_kerja" name="hasil_kerja" rows="3" 
                                  style="width: 100%; box-sizing: border-box; font-size: 13px; line-height: 1.5; font-family: inherit; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; resize: vertical;" 
                                  placeholder="Contoh: Hasil pekerjaan berupa laporan keuangan, program software yang siap uji, dll..."><?php echo esc_textarea( $instrument['hasil_kerja'] ); ?></textarea>
                    </div>

                    <!-- 4. Perlengkapan & Peralatan -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div>
                            <label for="perlengkapan" style="display: block; font-weight: 600; margin-bottom: 6px; color: #1e293b; font-size: 13.5px;">
                                D.1 Perlengkapan (Bahan Habis Pakai / APD):
                            </label>
                            <p style="margin: 0 0 8px 0; font-size: 11.5px; color: #64748b;">
                                Tuliskan satu perlengkapan per baris.
                            </p>
                            <textarea id="perlengkapan" name="perlengkapan" rows="5" 
                                      style="width: 100%; box-sizing: border-box; font-size: 13px; line-height: 1.5; font-family: inherit; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; resize: vertical;" 
                                      placeholder="Alat Pelindung Diri (APD)&#10;Kertas kerja / formulir&#10;Media penyimpanan..."><?php echo esc_textarea( $instrument['perlengkapan'] ); ?></textarea>
                        </div>
                        <div>
                            <label for="peralatan" style="display: block; font-weight: 600; margin-bottom: 6px; color: #1e293b; font-size: 13.5px;">
                                D.2 Peralatan (Perangkat / Mesin / Software):
                            </label>
                            <p style="margin: 0 0 8px 0; font-size: 11.5px; color: #64748b;">
                                Tuliskan satu peralatan per baris.
                            </p>
                            <textarea id="peralatan" name="peralatan" rows="5" 
                                      style="width: 100%; box-sizing: border-box; font-size: 13px; line-height: 1.5; font-family: inherit; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; resize: vertical;" 
                                      placeholder="Komputer / Laptop&#10;Software spreadsheet / IDE&#10;Alat Tulis Kantor (ATK)..."><?php echo esc_textarea( $instrument['peralatan'] ); ?></textarea>
                        </div>
                    </div>

                    <!-- 5. Durasi Waktu -->
                    <div style="margin-bottom: 25px;">
                        <label for="durasi_waktu" style="display: block; font-weight: 600; margin-bottom: 6px; color: #1e293b; font-size: 13.5px;">
                            E. Durasi Waktu Pengerjaan Praktik:
                        </label>
                        <input type="text" id="durasi_waktu" name="durasi_waktu" 
                               value="<?php echo esc_attr( $instrument['durasi_waktu'] ); ?>" 
                               style="width: 250px; font-size: 13.5px; padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 5px;" 
                               placeholder="Contoh: 120 Menit">
                        <span style="font-size: 12px; color: #64748b; margin-left: 8px;">(Contoh: 90 Menit, 120 Menit, atau 2 Jam)</span>
                    </div>

                    <div style="border-top: 1px solid #e2e8f0; padding-top: 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                        <button type="submit" class="button button-primary button-large" style="padding: 0 25px; height: 40px; font-weight: 700; background: #2563eb; border-color: #1d4ed8;">
                            💾 Simpan Perubahan FR.IA.02
                        </button>
                        <?php if ( ! empty( $instrument['updated_at'] ) ) : ?>
                            <span style="font-size: 12px; color: #64748b;">
                                Terakhir diperbarui: <strong><?php echo esc_html( date_i18n( 'd M Y H:i', strtotime( $instrument['updated_at'] ) ) ); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Sidebar Informasi -->
            <div style="display: flex; flex-direction: column; gap: 18px;">
                <!-- Info Skema -->
                <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 18px; width: 100%; box-sizing: border-box;">
                    <h3 style="margin-top: 0; margin-bottom: 12px; font-size: 14.5px; color: #1e293b; font-weight: 700;">
                        📋 Informasi Skema
                    </h3>
                    <div style="font-size: 13px; line-height: 1.6; color: #334155;">
                        <p style="margin: 0 0 6px 0;"><strong>Judul:</strong><br><?php echo esc_html( $skema_data['title'] ); ?></p>
                        <p style="margin: 0 0 6px 0;"><strong>Kode:</strong><br><code><?php echo esc_html( $skema_data['kode'] ); ?></code></p>
                        <p style="margin: 0;"><strong>Total Unit SKKNI:</strong> <?php echo count( $units_skkni ); ?> Unit</p>
                    </div>
                </div>

                <!-- Petunjuk BNSP -->
                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 18px; width: 100%; box-sizing: border-box;">
                    <h3 style="margin-top: 0; margin-bottom: 10px; font-size: 14px; color: #0f172a; font-weight: 700;">
                        ℹ️ Petunjuk BNSP
                    </h3>
                    <p style="font-size: 12px; color: #475569; line-height: 1.5; margin-bottom: 10px;">
                        FR.IA.02 (Tugas Praktik Demonstrasi) digunakan oleh asesi untuk memahami skenario instruksi kerja nyata serta standar hasil yang harus dicapai.
                    </p>
                    <p style="font-size: 12px; color: #475569; line-height: 1.5; margin-bottom: 0;">
                        Daftar unit kompetensi pada dokumen cetak FR.IA.02 tetap otomatis dikelompokkan berdasarkan menu <strong>Kelompok Pekerjaan</strong>.
                    </p>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>
