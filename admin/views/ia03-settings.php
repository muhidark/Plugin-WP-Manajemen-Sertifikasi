<?php
/**
 * View Pengaturan Instrumen FR.IA.03 — Pertanyaan Mendukung Observasi (PMO)
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap" id="lsp-ia03-wrap" style="max-width: 1280px;">
    <h1 style="font-size: 23px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">
        Instrumen Asesmen FR.IA.03
        <span style="font-size: 14px; font-weight: 500; color: #64748b; margin-left: 10px;">
            PMO — Pertanyaan untuk Mendukung Observasi
        </span>
    </h1>

    <?php if ( isset( $_GET['saved'] ) && '1' === $_GET['saved'] ) : ?>
        <div class="notice notice-success is-dismissible" style="margin-top: 15px;">
            <p><strong>Berhasil:</strong> Daftar pertanyaan pendukung observasi (FR.IA.03) berhasil disimpan.</p>
        </div>
    <?php endif; ?>

    <?php 
    $active_paket = ! empty( $paket_muk_id ) && class_exists( 'LSP_Cert_Paket_MUK' ) ? LSP_Cert_Paket_MUK::get_paket( $paket_muk_id ) : null;
    if ( $active_paket ) : 
    ?>
        <div style="background: #fef3c7; border: 1px solid #f59e0b; padding: 12px 18px; border-radius: 6px; margin-top: 15px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
                <strong style="color: #92400e; font-size: 14px;">📦 Mengedit Paket MUK: <?php echo esc_html( $active_paket->nama_paket ); ?> (Versi: <?php echo esc_html( $active_paket->kode_versi ); ?>)</strong>
                <p style="margin: 3px 0 0; color: #78350f; font-size: 12.5px;">Daftar pertanyaan (IA.03) ini disimpan khusus untuk paket bundling versi ini.</p>
            </div>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=paket_muk&skema_id=' . $selected_skema_id ) ); ?>" class="button button-secondary">
                &larr; Kembali ke Bank Data Paket MUK
            </a>
        </div>
    <?php endif; ?>

    <!-- Pemilihan Skema Sertifikasi -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 16px 20px; margin-top: 18px;">
        <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <input type="hidden" name="page" value="lsp-ia03">
            <?php if ( ! empty( $paket_muk_id ) ) : ?>
                <input type="hidden" name="paket_muk_id" value="<?php echo esc_attr( $paket_muk_id ); ?>">
            <?php endif; ?>
            <label for="skema_selector" style="font-weight: 600; font-size: 14px; color: #1e293b;">
                🎯 Pilih Skema Sertifikasi:
            </label>
            <select name="skema_id" id="skema_selector" style="min-width: 360px; font-size: 13.5px; padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 4px;" onchange="this.form.submit();">
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
        <div style="margin-top: 20px;">
            <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia03' ) ); ?>" id="form-ia03">
                <?php wp_nonce_field( 'lsp_ia03_save_nonce', 'lsp_ia03_nonce' ); ?>
                <input type="hidden" name="action" value="lsp_save_ia03">
                <input type="hidden" name="skema_id" value="<?php echo esc_attr( $selected_skema_id ); ?>">
                <?php if ( ! empty( $paket_muk_id ) ) : ?>
                    <input type="hidden" name="paket_muk_id" value="<?php echo esc_attr( $paket_muk_id ); ?>">
                <?php endif; ?>

                <!-- Banner Header Card -->
                <div style="background: #f0fdf4; border: 1px solid #86efac; border-left: 5px solid #16a34a; padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    <div>
                        <h2 style="margin: 0 0 4px 0; font-size: 15px; color: #15803d; font-weight: 700;">
                            ❓ Formulir Pertanyaan Mendukung Observasi (FR.IA.03)
                        </h2>
                        <p style="margin: 0; font-size: 12.5px; color: #166534;">
                            Tambahkan, ubah, atau hapus pertanyaan untuk menggali 5 Dimensi Kompetensi (TS, TMS, CMS, JRES, TRS).
                        </p>
                    </div>
                    <button type="button" class="button button-primary" id="btn-add-question" style="background: #16a34a; border-color: #15803d; font-weight: 600; padding: 4px 16px; height: 36px; display: inline-flex; align-items: center; gap: 6px;">
                        + Tambah Pertanyaan Baru
                    </button>
                </div>

                <!-- Container Pertanyaan Dinamis (Full Width, No cramped card constraint) -->
                <div id="questions-container" style="display: flex; flex-direction: column; gap: 16px; width: 100%; box-sizing: border-box;">
                    <?php 
                    $questions = ! empty( $instrument['questions'] ) ? $instrument['questions'] : array();
                    if ( empty( $questions ) ) {
                        $def = LSP_Cert_IA03::get_default_instrument( $selected_skema_id );
                        $questions = $def['questions'];
                    }

                    $dimensi_options = array(
                        'CMS'  => 'CMS — Contingency Management Skills',
                        'TRS'  => 'TRS — Transfer Skills',
                        'JRES' => 'JRES — Job/Role Environment Skills',
                        'TS'   => 'TS — Task Skills',
                        'TMS'  => 'TMS — Task Management Skills',
                    );

                    $q_idx = 0;
                    foreach ( $questions as $q ) :
                        $q_dimensi = $q['dimensi'] ?? 'TS';
                    ?>
                        <div class="lsp-question-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-left: 4px solid #2563eb; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); padding: 18px 22px; width: 100%; box-sizing: border-box; margin: 0;">
                            
                            <!-- Header Row: Nomor, Dimensi Selector, Tombol Hapus -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; flex-wrap: wrap; gap: 12px;">
                                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                    <span class="q-badge" style="background: #0f172a; color: #ffffff; font-weight: 700; border-radius: 4px; padding: 4px 10px; font-size: 12px; letter-spacing: 0.5px;">
                                        #<span class="q-num"><?php echo esc_html( $q_idx + 1 ); ?></span>
                                    </span>
                                    <label style="font-weight: 600; font-size: 13px; color: #334155;">Dimensi Kompetensi:</label>
                                    <select name="questions[<?php echo esc_attr( $q_idx ); ?>][dimensi]" style="font-size: 13px; padding: 5px 12px; border: 1px solid #cbd5e1; border-radius: 5px; min-width: 320px; max-width: 100%; background: #ffffff;">
                                        <?php foreach ( $dimensi_options as $k_dim => $lbl_dim ) : ?>
                                            <option value="<?php echo esc_attr( $k_dim ); ?>" <?php selected( $q_dimensi, $k_dim ); ?>>
                                                <?php echo esc_html( $lbl_dim ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="button" class="btn-remove-question" style="color: #dc2626; background: #fef2f2; border: 1px solid #fecaca; border-radius: 5px; padding: 5px 12px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
                                    ✕ Hapus Pertanyaan
                                </button>
                            </div>

                            <!-- Body Grid: Butir Pertanyaan & Tanggapan yang Diharapkan -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; width: 100%; box-sizing: border-box;">
                                <div>
                                    <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">
                                        Butir Pertanyaan:
                                    </label>
                                    <textarea name="questions[<?php echo esc_attr( $q_idx ); ?>][pertanyaan]" rows="4" 
                                              style="width: 100%; box-sizing: border-box; font-size: 13px; line-height: 1.5; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; font-family: inherit; resize: vertical;" 
                                              placeholder="Tuliskan pertanyaan observasi di sini..."><?php echo esc_textarea( $q['pertanyaan'] ?? '' ); ?></textarea>
                                </div>
                                <div>
                                    <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">
                                        Tanggapan yang Diharapkan / Kunci Jawaban:
                                    </label>
                                    <textarea name="questions[<?php echo esc_attr( $q_idx ); ?>][kunci_jawaban]" rows="4" 
                                              style="width: 100%; box-sizing: border-box; font-size: 13px; line-height: 1.5; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; font-family: inherit; resize: vertical;" 
                                              placeholder="Asesi mampu menjelaskan..."><?php echo esc_textarea( $q['kunci_jawaban'] ?? '' ); ?></textarea>
                                </div>
                            </div>
                        </div>
                    <?php 
                        $q_idx++;
                    endforeach; 
                    ?>
                </div>

                <!-- Action Footer Bar -->
                <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-top: 20px; padding: 16px 22px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; width: 100%; box-sizing: border-box;">
                    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                        <button type="submit" class="button button-primary button-large" style="padding: 0 25px; height: 40px; font-weight: 700; font-size: 13.5px; background: #2563eb; border-color: #1d4ed8;">
                            💾 Simpan Daftar Pertanyaan FR.IA.03
                        </button>
                        <button type="button" class="button button-secondary" id="btn-add-question-bottom" style="height: 40px; padding: 0 16px; font-weight: 600;">
                            + Tambah Pertanyaan
                        </button>
                    </div>
                    <?php if ( ! empty( $instrument['updated_at'] ) ) : ?>
                        <span style="font-size: 12px; color: #64748b;">
                            Terakhir disimpan: <strong><?php echo esc_html( date_i18n( 'd M Y H:i', strtotime( $instrument['updated_at'] ) ) ); ?></strong>
                        </span>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <script>
        (function() {
            var container = document.getElementById('questions-container');
            var btnAddTop = document.getElementById('btn-add-question');
            var btnAddBtm = document.getElementById('btn-add-question-bottom');

            var dimensiHtml = `
                <option value="CMS">CMS — Contingency Management Skills</option>
                <option value="TRS">TRS — Transfer Skills</option>
                <option value="JRES">JRES — Job/Role Environment Skills</option>
                <option value="TS" selected>TS — Task Skills</option>
                <option value="TMS">TMS — Task Management Skills</option>
            `;

            function reindexQuestions() {
                var cards = container.querySelectorAll('.lsp-question-card');
                cards.forEach(function(card, idx) {
                    var numSpan = card.querySelector('.q-num');
                    if (numSpan) numSpan.textContent = idx + 1;

                    var select = card.querySelector('select');
                    if (select) select.name = 'questions[' + idx + '][dimensi]';

                    var textareas = card.querySelectorAll('textarea');
                    if (textareas.length >= 2) {
                        textareas[0].name = 'questions[' + idx + '][pertanyaan]';
                        textareas[1].name = 'questions[' + idx + '][kunci_jawaban]';
                    }
                });
            }

            function addQuestion() {
                var currentCount = container.querySelectorAll('.lsp-question-card').length;
                var card = document.createElement('div');
                card.className = 'lsp-question-card';
                card.style.cssText = 'background: #ffffff; border: 1px solid #cbd5e1; border-left: 4px solid #2563eb; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); padding: 18px 22px; width: 100%; box-sizing: border-box; margin: 0;';
                card.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <span class="q-badge" style="background: #0f172a; color: #ffffff; font-weight: 700; border-radius: 4px; padding: 4px 10px; font-size: 12px; letter-spacing: 0.5px;">
                                #<span class="q-num">${currentCount + 1}</span>
                            </span>
                            <label style="font-weight: 600; font-size: 13px; color: #334155;">Dimensi Kompetensi:</label>
                            <select name="questions[${currentCount}][dimensi]" style="font-size: 13px; padding: 5px 12px; border: 1px solid #cbd5e1; border-radius: 5px; min-width: 320px; max-width: 100%; background: #ffffff;">
                                ${dimensiHtml}
                            </select>
                        </div>
                        <button type="button" class="btn-remove-question" style="color: #dc2626; background: #fef2f2; border: 1px solid #fecaca; border-radius: 5px; padding: 5px 12px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
                            ✕ Hapus Pertanyaan
                        </button>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; width: 100%; box-sizing: border-box;">
                        <div>
                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">
                                Butir Pertanyaan:
                            </label>
                            <textarea name="questions[${currentCount}][pertanyaan]" rows="4" 
                                      style="width: 100%; box-sizing: border-box; font-size: 13px; line-height: 1.5; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; font-family: inherit; resize: vertical;" 
                                      placeholder="Tuliskan pertanyaan observasi di sini..."></textarea>
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">
                                Tanggapan yang Diharapkan / Kunci Jawaban:
                            </label>
                            <textarea name="questions[${currentCount}][kunci_jawaban]" rows="4" 
                                      style="width: 100%; box-sizing: border-box; font-size: 13px; line-height: 1.5; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 14px; font-family: inherit; resize: vertical;" 
                                      placeholder="Asesi mampu menjelaskan..."></textarea>
                        </div>
                    </div>
                `;

                container.appendChild(card);
                reindexQuestions();
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            if (btnAddTop) btnAddTop.addEventListener('click', addQuestion);
            if (btnAddBtm) btnAddBtm.addEventListener('click', addQuestion);

            container.addEventListener('click', function(e) {
                var btn = e.target.closest('.btn-remove-question');
                if (btn) {
                    if (confirm('Hapus butir pertanyaan ini?')) {
                        var card = btn.closest('.lsp-question-card');
                        if (card) {
                            card.remove();
                            reindexQuestions();
                        }
                    }
                }
            });
        })();
        </script>
    <?php endif; ?>
</div>
