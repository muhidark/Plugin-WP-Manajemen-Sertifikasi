<?php
/**
 * View Pengaturan Kelompok Pekerjaan (FR.IA.01, FR.IA.02, FR.IA.03)
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h1>
        Pengelompokan Unit Kompetensi Berdasarkan Kelompok Pekerjaan
        <span style="font-size: 14px; font-weight: normal; color: #64748b; margin-left: 10px;">
            Standar Format FR.IA.01, FR.IA.02, dan FR.IA.03
        </span>
    </h1>

    <?php if ( isset( $_GET['saved'] ) && '1' === $_GET['saved'] ) : ?>
        <div class="notice notice-success is-dismissible" style="margin-top: 15px;">
            <p><strong>Berhasil:</strong> Konfigurasi Kelompok Pekerjaan berhasil disimpan.</p>
        </div>
    <?php endif; ?>

    <?php if ( isset( $_GET['error'] ) ) : ?>
        <div class="notice notice-error is-dismissible" style="margin-top: 15px;">
            <p><strong>Gagal:</strong> Terjadi kesalahan saat menyimpan konfigurasi kelompok pekerjaan.</p>
        </div>
    <?php endif; ?>

    <?php 
    $active_paket = ! empty( $paket_muk_id ) && class_exists( 'LSP_Cert_Paket_MUK' ) ? LSP_Cert_Paket_MUK::get_paket( $paket_muk_id ) : null;
    if ( $active_paket ) : 
    ?>
        <div style="background: #fef3c7; border: 1px solid #f59e0b; padding: 12px 18px; border-radius: 6px; margin-top: 15px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
                <strong style="color: #92400e; font-size: 14px;">📦 Mengedit Paket MUK: <?php echo esc_html( $active_paket->nama_paket ); ?> (Versi: <?php echo esc_html( $active_paket->kode_versi ); ?>)</strong>
                <p style="margin: 3px 0 0; color: #78350f; font-size: 12.5px;">Perubahan kelompok pekerjaan ini disimpan khusus untuk paket bundling versi ini.</p>
            </div>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=paket_muk&skema_id=' . $selected_skema_id ) ); ?>" class="button button-secondary">
                &larr; Kembali ke Bank Data Paket MUK
            </a>
        </div>
    <?php endif; ?>

    <!-- Pemilihan Skema Sertifikasi -->
    <div class="card" style="max-width: 100%; margin-top: 20px; padding: 15px 20px;">
        <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <input type="hidden" name="page" value="lsp-kelompok-pekerjaan">
            <label for="skema_selector" style="font-weight: 600; font-size: 14px; color: #1e293b;">
                🎯 Pilih Skema Sertifikasi:
            </label>
            <select name="skema_id" id="skema_selector" style="min-width: 360px; font-size: 13.5px; padding: 5px 10px;" onchange="this.form.submit();">
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
        <!-- Indikator Cakupan Unit Kompetensi -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-top: 20px;">
            <div class="card" style="margin: 0; padding: 15px; border-left: 4px solid #0284c7;">
                <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Total Unit Master Skema</div>
                <div style="font-size: 26px; font-weight: bold; color: #0f172a; margin-top: 4px;">
                    <?php echo intval( $coverage['total_master'] ); ?> <span style="font-size: 14px; font-weight: normal; color: #64748b;">Unit</span>
                </div>
            </div>

            <div class="card" style="margin: 0; padding: 15px; border-left: 4px solid #10b981;">
                <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Unit Terkelompok (Unik)</div>
                <div style="font-size: 26px; font-weight: bold; color: #047857; margin-top: 4px;">
                    <?php echo intval( $coverage['total_grouped_unique'] ); ?> <span style="font-size: 14px; font-weight: normal; color: #64748b;">Unit</span>
                </div>
            </div>

            <div class="card" style="margin: 0; padding: 15px; border-left: 4px solid <?php echo $coverage['total_unassigned'] > 0 ? '#ef4444' : '#10b981'; ?>;">
                <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Unit Belum Dikelompokkan</div>
                <div style="font-size: 26px; font-weight: bold; color: <?php echo $coverage['total_unassigned'] > 0 ? '#b91c1c' : '#047857'; ?>; margin-top: 4px;">
                    <?php echo intval( $coverage['total_unassigned'] ); ?> <span style="font-size: 14px; font-weight: normal; color: #64748b;">Unit</span>
                </div>
            </div>

            <div class="card" style="margin: 0; padding: 15px; border-left: 4px solid <?php echo $coverage['is_complete'] ? '#10b981' : '#f59e0b'; ?>;">
                <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Status Cakupan Dokumen</div>
                <div style="font-size: 20px; font-weight: bold; color: <?php echo $coverage['is_complete'] ? '#047857' : '#b45309'; ?>; margin-top: 7px;">
                    <?php if ( $coverage['is_complete'] ) : ?>
                        ✅ Lengkap (100%)
                    <?php else : ?>
                        ⚠️ <?php echo esc_html( $coverage['coverage_percent'] ); ?>% (Belum Lengkap)
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ( ! $coverage['is_complete'] && ! empty( $coverage['unassigned_units'] ) ) : ?>
            <div class="notice notice-warning" style="margin-top: 20px; padding: 12px 16px; border-left-color: #f59e0b;">
                <p style="margin: 0 0 8px 0; font-weight: bold; color: #b45309;">
                    ⚠️ Peringatan: Masih ada <?php echo count( $coverage['unassigned_units'] ); ?> unit kompetensi dari Master Skema yang belum dimasukkan ke dalam kelompok pekerjaan manapun:
                </p>
                <ul style="margin: 0; padding-left: 20px; list-style-type: disc; color: #78350f; font-size: 13px;">
                    <?php foreach ( $coverage['unassigned_units'] as $u_unassigned ) : ?>
                        <li>
                            <strong style="font-family: monospace;"><?php echo esc_html( $u_unassigned['kode'] ); ?></strong> — <?php echo esc_html( $u_unassigned['judul'] ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p style="margin: 8px 0 0 0; font-size: 12px; color: #92400e;">
                    <em>Catatan: Sesuai panduan BNSP, semua unit kompetensi dalam skema wajib tercakup dalam formulir FR.IA.01, FR.IA.02, dan FR.IA.03.</em>
                </p>
            </div>
        <?php endif; ?>

        <!-- Konten Pengaturan Kelompok & Preview -->
        <div style="display: grid; grid-template-columns: 2fr 1.3fr; gap: 20px; margin-top: 20px; align-items: start;">

            <!-- Form Utama Pengaturan Kelompok -->
            <div class="card" style="padding: 20px;">
                <h2 style="margin: 0 0 10px 0; font-size: 16px; color: #1e293b; display: flex; align-items: center; justify-content: space-between;">
                    <span>📂 Daftar Kelompok Pekerjaan</span>
                    <button type="button" id="btn-add-kelompok" class="button button-secondary" style="font-size: 13px;">
                        ➕ Tambah Kelompok
                    </button>
                </h2>
                <p style="margin: 0 0 20px 0; font-size: 12.5px; color: #64748b;">
                    Buat kelompok pekerjaan dan pilih unit kompetensi yang tercakup. Satu unit kompetensi dapat digunakan dalam lebih dari satu kelompok pekerjaan.
                </p>

                <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=lsp-kelompok-pekerjaan' ) ); ?>" id="form-kelompok">
                    <?php wp_nonce_field( 'lsp_kelompok_save_nonce', 'lsp_kelompok_nonce' ); ?>
                    <input type="hidden" name="action" value="lsp_save_kelompok_pekerjaan">
                    <input type="hidden" name="skema_id" value="<?php echo esc_attr( $selected_skema_id ); ?>">
                    <?php if ( ! empty( $paket_muk_id ) ) : ?>
                        <input type="hidden" name="paket_muk_id" value="<?php echo esc_attr( $paket_muk_id ); ?>">
                    <?php endif; ?>

                    <div id="kelompok-container">
                        <?php if ( empty( $kelompok ) ) : ?>
                            <!-- Group Item Default 1 -->
                            <div class="kelompok-card" data-index="0" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 16px; margin-bottom: 20px; position: relative;">
                                <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 12px;">
                                    <div style="flex: 1;">
                                        <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px; color: #1e293b;">Nama Kelompok Pekerjaan:</label>
                                        <input type="text" name="kelompok[0][nama]" value="" class="large-text" placeholder="Contoh: Kelompok Pekerjaan 1: Perencanaan Konten" required style="font-size: 13px;">
                                    </div>
                                    <div style="width: 80px;">
                                        <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px; color: #1e293b;">Urutan:</label>
                                        <input type="number" name="kelompok[0][urutan]" value="1" min="1" class="small-text" style="width: 100%; text-align: center; font-size: 13px;">
                                    </div>
                                    <div style="padding-top: 22px;">
                                        <button type="button" class="button button-link-delete btn-remove-kelompok" style="color: #ef4444; text-decoration: none; font-size: 13px;">Hapus</button>
                                    </div>
                                </div>
                                <div>
                                    <label style="display: block; font-weight: 600; font-size: 12.5px; margin-bottom: 6px; color: #334155;">Pilih Unit Kompetensi Anggota Kelompok:</label>
                                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px 14px; max-height: 220px; overflow-y: auto;">
                                        <?php foreach ( $coverage['master_map'] as $u_kode => $u_judul ) : ?>
                                            <label style="display: block; margin-bottom: 6px; font-size: 12.5px; color: #1e293b; cursor: pointer;">
                                                <input type="checkbox" name="kelompok[0][units][]" value="<?php echo esc_attr( $u_kode ); ?>">
                                                <strong style="font-family: monospace; font-size: 12px;"><?php echo esc_html( $u_kode ); ?></strong> — <?php echo esc_html( $u_judul ); ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else : ?>
                            <?php foreach ( $kelompok as $idx => $grp ) : 
                                $current_units = isset( $grp['units'] ) && is_array( $grp['units'] ) ? $grp['units'] : array();
                            ?>
                                <div class="kelompok-card" data-index="<?php echo $idx; ?>" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 16px; margin-bottom: 20px; position: relative;">
                                    <input type="hidden" name="kelompok[<?php echo $idx; ?>][id]" value="<?php echo esc_attr( $grp['id'] ?? '' ); ?>">
                                    <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 12px;">
                                        <div style="flex: 1;">
                                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px; color: #1e293b;">Nama Kelompok Pekerjaan:</label>
                                            <input type="text" name="kelompok[<?php echo $idx; ?>][nama]" value="<?php echo esc_attr( $grp['nama'] ); ?>" class="large-text" placeholder="Contoh: Kelompok Pekerjaan 1: Perencanaan Konten" required style="font-size: 13px;">
                                        </div>
                                        <div style="width: 80px;">
                                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px; color: #1e293b;">Urutan:</label>
                                            <input type="number" name="kelompok[<?php echo $idx; ?>][urutan]" value="<?php echo esc_attr( $grp['urutan'] ?? ( $idx + 1 ) ); ?>" min="1" class="small-text" style="width: 100%; text-align: center; font-size: 13px;">
                                        </div>
                                        <div style="padding-top: 22px;">
                                            <button type="button" class="button button-link-delete btn-remove-kelompok" style="color: #ef4444; text-decoration: none; font-size: 13px;">Hapus</button>
                                        </div>
                                    </div>
                                    <div>
                                        <label style="display: block; font-weight: 600; font-size: 12.5px; margin-bottom: 6px; color: #334155;">Pilih Unit Kompetensi Anggota Kelompok (<?php echo count( $current_units ); ?> terpilih):</label>
                                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px 14px; max-height: 220px; overflow-y: auto;">
                                            <?php foreach ( $coverage['master_map'] as $u_kode => $u_judul ) : 
                                                $is_checked = in_array( $u_kode, $current_units, true );
                                            ?>
                                                <label style="display: block; margin-bottom: 6px; font-size: 12.5px; color: #1e293b; cursor: pointer;">
                                                    <input type="checkbox" name="kelompok[<?php echo $idx; ?>][units][]" value="<?php echo esc_attr( $u_kode ); ?>" <?php checked( $is_checked ); ?>>
                                                    <strong style="font-family: monospace; font-size: 12px;"><?php echo esc_html( $u_kode ); ?></strong> — <?php echo esc_html( $u_judul ); ?>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 12px; align-items: center; margin-top: 20px;">
                        <button type="submit" class="button button-primary button-large" style="padding: 4px 20px; font-size: 14px;">
                            💾 Simpan Pengelompokan Unit
                        </button>
                        <button type="button" id="btn-add-kelompok-bottom" class="button button-secondary" style="font-size: 13px;">
                            ➕ Tambah Kelompok Lain
                        </button>
                    </div>
                </form>
            </div>

            <!-- Kolom Kanan: Informasi & Pratinjau Cetak -->
            <div>
                <!-- Panduan Format -->
                <div class="card" style="padding: 16px 20px; margin-bottom: 20px; border-left: 4px solid #3b82f6;">
                    <h3 style="margin: 0 0 8px 0; font-size: 14px; color: #1d4ed8;">📌 Aturan BNSP FR.IA.01 / FR.IA.02 / FR.IA.03</h3>
                    <ul style="margin: 0; padding-left: 18px; font-size: 12.5px; color: #475569; line-height: 1.6;">
                        <li><strong>Kolom 1:</strong> Kelompok Pekerjaan (rowspan mencakup seluruh unit di kelompoknya).</li>
                        <li><strong>Kolom 2:</strong> No. urut berurutan.</li>
                        <li><strong>Kolom 3 &amp; 4:</strong> Kode Unit dan Judul Unit.</li>
                        <li>Satu unit kompetensi boleh digunakan di lebih dari satu kelompok jika relevan dengan aktivitas praktik.</li>
                        <li>Semua unit dari Master Skema wajib masuk minimal ke salah satu kelompok agar cakupan 100%.</li>
                    </ul>
                </div>

                <!-- Live Preview Tabel Dokumen Cetak -->
                <div class="card" style="padding: 16px 20px;">
                    <h3 style="margin: 0 0 12px 0; font-size: 14px; color: #0f172a; display: flex; align-items: center; justify-content: space-between;">
                        <span>👁️ Pratinjau Format Tabel Cetak</span>
                        <span style="font-size: 11px; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; color: #475569;">A4 Output</span>
                    </h3>
                    <div style="background: #ffffff; border: 1px solid #cbd5e1; padding: 12px; border-radius: 4px; overflow-x: auto;">
                        <?php LSP_Cert_Kelompok_Pekerjaan::render_unit_table_html( $selected_skema_id ); ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Template JS untuk Tambah Kelompok Baru -->
        <script type="text/template" id="tmpl-kelompok-row">
            <div class="kelompok-card" data-index="{{INDEX}}" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 16px; margin-bottom: 20px; position: relative;">
                <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 12px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px; color: #1e293b;">Nama Kelompok Pekerjaan:</label>
                        <input type="text" name="kelompok[{{INDEX}}][nama]" value="" class="large-text" placeholder="Contoh: Kelompok Pekerjaan {{ORDER}}: ..." required style="font-size: 13px;">
                    </div>
                    <div style="width: 80px;">
                        <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px; color: #1e293b;">Urutan:</label>
                        <input type="number" name="kelompok[{{INDEX}}][urutan]" value="{{ORDER}}" min="1" class="small-text" style="width: 100%; text-align: center; font-size: 13px;">
                    </div>
                    <div style="padding-top: 22px;">
                        <button type="button" class="button button-link-delete btn-remove-kelompok" style="color: #ef4444; text-decoration: none; font-size: 13px;">Hapus</button>
                    </div>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; font-size: 12.5px; margin-bottom: 6px; color: #334155;">Pilih Unit Kompetensi Anggota Kelompok:</label>
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px 14px; max-height: 220px; overflow-y: auto;">
                        <?php foreach ( $coverage['master_map'] as $u_kode => $u_judul ) : ?>
                            <label style="display: block; margin-bottom: 6px; font-size: 12.5px; color: #1e293b; cursor: pointer;">
                                <input type="checkbox" name="kelompok[{{INDEX}}][units][]" value="<?php echo esc_attr( $u_kode ); ?>">
                                <strong style="font-family: monospace; font-size: 12px;"><?php echo esc_html( $u_kode ); ?></strong> — <?php echo esc_html( $u_judul ); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </script>

        <script>
            (function() {
                var container = document.getElementById('kelompok-container');
                var btnAdd = document.getElementById('btn-add-kelompok');
                var btnAddBottom = document.getElementById('btn-add-kelompok-bottom');
                var tmpl = document.getElementById('tmpl-kelompok-row').innerHTML;

                function addKelompokRow() {
                    var cards = container.querySelectorAll('.kelompok-card');
                    var nextIndex = cards.length;
                    var nextOrder = nextIndex + 1;

                    var html = tmpl.replace(/{{INDEX}}/g, nextIndex).replace(/{{ORDER}}/g, nextOrder);
                    var temp = document.createElement('div');
                    temp.innerHTML = html;
                    var newCard = temp.firstElementChild;
                    container.appendChild(newCard);
                    newCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }

                if (btnAdd) {
                    btnAdd.addEventListener('click', addKelompokRow);
                }
                if (btnAddBottom) {
                    btnAddBottom.addEventListener('click', addKelompokRow);
                }

                container.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('btn-remove-kelompok')) {
                        var card = e.target.closest('.kelompok-card');
                        if (card) {
                            var cards = container.querySelectorAll('.kelompok-card');
                            if (cards.length <= 1) {
                                alert('Minimal harus ada satu kelompok pekerjaan.');
                                return;
                            }
                            if (confirm('Yakin ingin menghapus kelompok pekerjaan ini?')) {
                                card.remove();
                            }
                        }
                    }
                });
            })();
        </script>

    <?php endif; ?>
</div>
