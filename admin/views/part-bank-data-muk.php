<?php
/**
 * Part View: Bank Data Paket MUK (Multi-Versi Bundling)
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$all_skema = LSP_Cert_Integration::get_all_skema();
$active_skema_id = isset( $_GET['skema_id'] ) ? intval( $_GET['skema_id'] ) : ( ! empty( $all_skema ) ? $all_skema[0]['id'] : 0 );
$paket_list = LSP_Cert_Paket_MUK::get_paket_by_skema( $active_skema_id );
$active_skema = LSP_Cert_Integration::get_skema( $active_skema_id );
?>

<div class="card" style="padding: 24px; max-width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    
    <!-- Header & Info -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #f1f5f9;">
        <div>
            <h2 style="margin: 0; font-size: 18px; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <span>📦</span>
                <span>Bank Data Paket MUK (Bundling MAPA &amp; Instrumen IA)</span>
            </h2>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">
                Kelola paket materi uji kompetensi terpadu (MAPA + IA.01–05). Setiap skema dapat memiliki beragam versi / paket bundling untuk kerahasiaan &amp; rotasi soal.
            </p>
        </div>
        <div>
            <button type="button" class="button button-primary" id="btn-modal-tambah-paket" style="background: #16a34a; border-color: #16a34a; font-weight: 600; padding: 4px 16px; height: 38px;">
                ➕ Buat Paket / Versi MUK Baru
            </button>
        </div>
    </div>

    <!-- Filter Skema -->
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
        <label for="skema_muk_filter" style="font-weight: 600; font-size: 13px; color: #1e293b;">Pilih Skema Sertifikasi:</label>
        <select id="skema_muk_filter" style="min-width: 380px; padding: 4px 10px; height: 36px;" onchange="window.location.href='<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=paket_muk&skema_id=' ) ); ?>' + this.value;">
            <?php foreach ( $all_skema as $sk ) : ?>
                <option value="<?php echo esc_attr( $sk['id'] ); ?>" <?php selected( $active_skema_id, $sk['id'] ); ?>>
                    <?php echo esc_html( $sk['title'] ); ?> (<?php echo esc_html( $sk['kode'] ?? '-' ); ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <span style="color: #64748b; font-size: 12px;">(Total <?php echo count( $paket_list ); ?> Paket Versi Tersedia)</span>
    </div>

    <!-- Tabel Daftar Paket MUK -->
    <table class="wp-list-table widefat fixed striped" style="border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
        <thead>
            <tr style="background: #f1f5f9;">
                <th style="width: 50px; text-align: center;">ID</th>
                <th style="width: 110px;">Kode Versi</th>
                <th>Nama Bundling Paket MUK</th>
                <th style="width: 170px;">Kelompok Pekerjaan</th>
                <th style="width: 130px;">Instrumen IA.04</th>
                <th style="width: 130px;">Instrumen IA.05</th>
                <th style="width: 100px; text-align: center;">Default</th>
                <th style="width: 220px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $paket_list ) ) : ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 30px; color: #64748b;">
                        Belum ada Paket MUK untuk skema ini. Klik tombol <strong>+ Buat Paket / Versi MUK Baru</strong> di atas untuk membuat paket bundling.
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ( $paket_list as $p ) : 
                    $kel_count = ! empty( $p->kelompok_pekerjaan ) && is_array( $p->kelompok_pekerjaan ) ? count( $p->kelompok_pekerjaan ) : 0;
                    $ia04_has  = ! empty( $p->ia04_data['judul_kegiatan'] ) || ! empty( $p->ia04_data['questions'] );
                    $ia05_soal = ! empty( $p->ia05_data['jumlah_soal'] ) ? $p->ia05_data['jumlah_soal'] : 0;
                ?>
                    <tr>
                        <td style="text-align: center; color: #64748b; font-weight: 600;"><?php echo $p->id; ?></td>
                        <td>
                            <span style="display: inline-block; padding: 3px 8px; background: #e0f2fe; color: #0284c7; border-radius: 4px; font-weight: 700; font-size: 11.5px;">
                                🏷️ <?php echo esc_html( $p->kode_versi ); ?>
                            </span>
                        </td>
                        <td>
                            <strong style="font-size: 13.5px; color: #0f172a;"><?php echo esc_html( $p->nama_paket ); ?></strong>
                            <?php if ( ! empty( $p->deskripsi ) ) : ?>
                                <p style="margin: 3px 0 0 0; color: #64748b; font-size: 12px;"><?php echo esc_html( $p->deskripsi ); ?></p>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( $kel_count > 0 ) : ?>
                                <span style="color: #16a34a; font-weight: 600;">✓ <?php echo $kel_count; ?> Kelompok</span>
                            <?php else : ?>
                                <span style="color: #94a3b8; font-style: italic;">Belum diatur</span>
                            <?php endif; ?>
                            <div style="margin-top: 4px; display: flex; flex-direction: column; gap: 3px;">
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-kelompok-pekerjaan&skema_id=' . $active_skema_id . '&paket_muk_id=' . $p->id ) ); ?>" style="font-size: 11px; text-decoration: none;">
                                    ⚙️ Kelompok (IA.01) &rarr;
                                </a>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia02&skema_id=' . $active_skema_id . '&paket_muk_id=' . $p->id ) ); ?>" style="font-size: 11px; text-decoration: none; color: #15803d; font-weight: 600;">
                                    🛠️ Praktik (IA.02) &rarr;
                                </a>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia03&skema_id=' . $active_skema_id . '&paket_muk_id=' . $p->id ) ); ?>" style="font-size: 11px; text-decoration: none; color: #1d4ed8; font-weight: 600;">
                                    ❓ Pertanyaan (IA.03) &rarr;
                                </a>
                            </div>
                        </td>
                        <td>
                            <?php if ( $ia04_has ) : ?>
                                <span style="color: #16a34a; font-weight: 600;">✓ DIT Proyek Ada</span>
                            <?php else : ?>
                                <span style="color: #94a3b8; font-style: italic;">Belum diatur</span>
                            <?php endif; ?>
                            <br>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia04&skema_id=' . $active_skema_id . '&paket_muk_id=' . $p->id ) ); ?>" style="font-size: 11.5px; text-decoration: none;">
                                📝 Atur IA.04 &rarr;
                            </a>
                        </td>
                        <td>
                            <?php if ( $ia05_soal > 0 ) : ?>
                                <span style="color: #16a34a; font-weight: 600;">✓ <?php echo $ia05_soal; ?> Soal PG</span>
                            <?php else : ?>
                                <span style="color: #94a3b8; font-style: italic;">Belum diatur</span>
                            <?php endif; ?>
                            <br>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia05&skema_id=' . $active_skema_id . '&paket_muk_id=' . $p->id ) ); ?>" style="font-size: 11.5px; text-decoration: none;">
                                ✍️ Atur IA.05 &rarr;
                            </a>
                        </td>
                        <td style="text-align: center;">
                            <?php if ( $p->is_default ) : ?>
                                <span style="display: inline-block; padding: 2px 8px; background: #dcfce7; color: #15803d; border-radius: 12px; font-weight: 700; font-size: 11px;">
                                    ★ Default
                                </span>
                            <?php else : ?>
                                <button type="button" class="button button-small btn-set-default" data-id="<?php echo $p->id; ?>" title="Jadikan paket default untuk skema ini">
                                    Set Default
                                </button>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <button type="button" class="button button-small btn-clone-paket" data-id="<?php echo $p->id; ?>" data-versi="<?php echo esc_attr( $p->kode_versi ); ?>" data-nama="<?php echo esc_attr( $p->nama_paket ); ?>" title="Gandakan materi paket ini menjadi versi baru" style="color: #0284c7; font-weight: 600;">
                                📋 Duplikasi (Clone)
                            </button>
                            <?php if ( ! $p->is_default ) : ?>
                                <button type="button" class="button button-small button-link-delete btn-delete-paket" data-id="<?php echo $p->id; ?>" style="color: #dc2626; margin-left: 4px;" title="Hapus paket ini">
                                    🗑️ Hapus
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Paket MUK Baru -->
<div id="modal-tambah-paket" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 99999; align-items: center; justify-content: center;">
    <div style="background: #ffffff; width: 550px; max-width: 90%; border-radius: 8px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <h3 style="margin-top: 0; font-size: 16px; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
            📦 Buat Paket / Versi MUK Baru
        </h3>
        <form id="form-create-paket">
            <input type="hidden" name="skema_id" value="<?php echo esc_attr( $active_skema_id ); ?>">

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Skema Sertifikasi:</label>
                <input type="text" class="widefat" value="<?php echo esc_attr( $active_skema['title'] ?? '' ); ?>" readonly style="background: #f1f5f9;">
            </div>

            <div style="margin-bottom: 14px;">
                <label for="new_kode_versi" style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Kode Versi / Paket *:</label>
                <input type="text" name="kode_versi" id="new_kode_versi" class="widefat" placeholder="Contoh: Versi 02 (Paket B)" required>
                <p class="description" style="font-size: 11.5px; margin: 3px 0 0 0;">Contoh: Versi 02, Paket B, atau Versi 2026.1</p>
            </div>

            <div style="margin-bottom: 14px;">
                <label for="new_nama_paket" style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Nama Lengkap Bundling Paket:</label>
                <input type="text" name="nama_paket" id="new_nama_paket" class="widefat" placeholder="Otomatis terisi standar: MAPA + Judul Skema + Kode Versi">
            </div>

            <div style="margin-bottom: 14px;">
                <label for="new_deskripsi" style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Deskripsi / Catatan Paket:</label>
                <textarea name="deskripsi" id="new_deskripsi" rows="3" class="widefat" placeholder="Catatan mengenai peruntukan atau revisi paket materi uji ini..."></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                    <input type="checkbox" name="is_default" value="1">
                    <span>Jadikan paket ini sebagai default untuk skema ini</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="button btn-close-modal">Batal</button>
                <button type="submit" class="button button-primary" style="background: #16a34a; border-color: #16a34a;">Simpan Paket Baru</button>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var ajaxUrl = typeof lspCert !== 'undefined' && lspCert.ajax_url ? lspCert.ajax_url : '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';
    var certNonce = typeof lspCert !== 'undefined' && lspCert.nonce ? lspCert.nonce : '<?php echo esc_js( wp_create_nonce( 'lsp_cert_nonce' ) ); ?>';

    // Buka Modal Tambah Paket
    $('#btn-modal-tambah-paket').on('click', function(e) {
        e.preventDefault();
        $('#modal-tambah-paket').css('display', 'flex');
        $('#new_kode_versi').focus();
    });

    // Tutup Modal
    $('.btn-close-modal').on('click', function(e) {
        e.preventDefault();
        $('#modal-tambah-paket').hide();
    });

    // Auto generate nama paket saat kode versi diketik
    $('#new_kode_versi').on('input', function() {
        var versi = $(this).val();
        var skemaTitle = '<?php echo esc_js( $active_skema['title'] ?? '' ); ?>';
        if (versi) {
            $('#new_nama_paket').val('MAPA — ' + skemaTitle + ' — ' + versi);
        } else {
            $('#new_nama_paket').val('');
        }
    });

    // Submit Tambah Paket Baru
    $('#form-create-paket').on('submit', function(e) {
        e.preventDefault();
        var $btn = $(this).find('button[type="submit"]');
        var originalBtnText = $btn.text();
        $btn.prop('disabled', true).text('Menyimpan...');

        var data = $(this).serialize() + '&action=lsp_cert_save_paket&nonce=' + encodeURIComponent(certNonce);

        $.post(ajaxUrl, data, function(res) {
            if (res.success) {
                alert(res.data.message || 'Paket MUK baru berhasil disimpan');
                window.location.reload();
            } else {
                alert('Gagal: ' + (res.data || 'Terjadi kesalahan'));
                $btn.prop('disabled', false).text(originalBtnText);
            }
        }).fail(function(xhr) {
            alert('Terjadi kesalahan jaringan atau server: ' + (xhr.statusText || 'Error'));
            $btn.prop('disabled', false).text(originalBtnText);
        });
    });

    // Duplikasi (Clone) Paket
    $('.btn-clone-paket').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var versiAsal = $(this).data('versi');
        
        var newVersi = prompt('Masukkan Kode Versi baru untuk duplikasi paket ini:', versiAsal + ' (Copy)');
        if (!newVersi) return;

        $.post(ajaxUrl, {
            action: 'lsp_cert_clone_paket',
            nonce: certNonce,
            source_id: id,
            new_kode_versi: newVersi
        }, function(res) {
            if (res.success) {
                alert(res.data.message || 'Paket MUK berhasil digandakan');
                window.location.reload();
            } else {
                alert('Gagal duplikasi: ' + (res.data || 'Terjadi kesalahan'));
            }
        });
    });

    // Set Default Paket
    $('.btn-set-default').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (!confirm('Jadikan paket ini sebagai default untuk skema ini?')) return;

        $.post(ajaxUrl, {
            action: 'lsp_cert_set_default_paket',
            nonce: certNonce,
            id: id
        }, function(res) {
            if (res.success) {
                alert(res.data.message);
                window.location.reload();
            } else {
                alert('Gagal: ' + res.data);
            }
        });
    });

    // Hapus Paket
    $('.btn-delete-paket').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (!confirm('Apakah Anda yakin ingin menghapus paket MUK ini?')) return;

        $.post(ajaxUrl, {
            action: 'lsp_cert_delete_paket',
            nonce: certNonce,
            id: id
        }, function(res) {
            if (res.success) {
                alert(res.data.message);
                window.location.reload();
            } else {
                alert('Gagal menghapus: ' + res.data);
            }
        });
    });
});
</script>
