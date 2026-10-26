<div class="wrap">
    <h1>
        <?php echo $kegiatan ? 'Edit Kegiatan' : 'Tambah Kegiatan Baru'; ?>
        <a href="<?php echo admin_url( 'admin.php?page=lsp-sertifikasi' ); ?>" class="page-title-action">Kembali</a>
        <?php if ( ! empty( $kegiatan->id ) ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=mapa02' ) ); ?>" target="_blank" class="page-title-action" style="color: #0284c7; border-color: #0284c7; font-weight: bold;">🖨️ Cetak FR.MAPA.02</a>
        <?php endif; ?>
    </h1>
    
    <form id="kegiatan-form" method="post">
        <input type="hidden" name="id" value="<?php echo $kegiatan ? $kegiatan->id : 0; ?>">
        
        <table class="form-table">
            <tr>
                <th><label for="skema_id">Skema Sertifikasi *</label></th>
                <td>
                    <select name="skema_id" id="skema_id" required>
                        <option value="">-- Pilih Skema --</option>
                        <?php foreach ( $skema_list as $skema ) : ?>
                            <option value="<?php echo $skema['id']; ?>" <?php selected( $kegiatan->skema_id ?? '', $skema['id'] ); ?>>
                                <?php echo esc_html( $skema['title'] ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="paket_muk_id">Paket MUK (Versi Instrumen) *</label></th>
                <td>
                    <select name="paket_muk_id" id="paket_muk_id" style="min-width: 380px;">
                        <option value="">-- Pilih Paket MUK --</option>
                        <?php 
                        $curr_skema_id = $kegiatan->skema_id ?? ( ! empty( $skema_list ) ? $skema_list[0]['id'] : 0 );
                        if ( $curr_skema_id && class_exists( 'LSP_Cert_Paket_MUK' ) ) {
                            $available_paket = LSP_Cert_Paket_MUK::get_paket_by_skema( $curr_skema_id, 'aktif' );
                            foreach ( $available_paket as $pk ) {
                                $is_sel = ( isset( $kegiatan->paket_muk_id ) && $kegiatan->paket_muk_id == $pk->id ) || ( empty( $kegiatan->paket_muk_id ) && $pk->is_default );
                                echo '<option value="' . esc_attr( $pk->id ) . '" ' . ( $is_sel ? 'selected' : '' ) . '>' . esc_html( $pk->nama_paket ) . ( $pk->is_default ? ' ★ (Default)' : '' ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <p class="description">Paket materi uji terpadu (MAPA + IA.01–05) yang digunakan pada kegiatan sertifikasi ini.</p>
                </td>
            </tr>
            <tr>
                <th><label for="tanggal_pelaksanaan">Tanggal Pelaksanaan *</label></th>
                <td>
                    <input type="date" name="tanggal_pelaksanaan" id="tanggal_pelaksanaan" 
                           value="<?php echo $kegiatan ? $kegiatan->tanggal_pelaksanaan : ''; ?>" required>
                </td>
            </tr>
            <tr>
                <th><label for="tuk_id">Tempat Uji Kompetensi (TUK) *</label></th>
                <td>
                    <select name="tuk_id" id="tuk_id" required>
                        <option value="">-- Pilih TUK --</option>
                        <?php foreach ( $tuk_list as $tuk ) : ?>
                            <option value="<?php echo $tuk['id']; ?>" <?php selected( $kegiatan->tuk_id ?? '', $tuk['id'] ); ?>>
                                <?php echo esc_html( $tuk['title'] ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">TUK penyelenggara yang bertanggung jawab atas kegiatan sertifikasi.</p>
                </td>
            </tr>
            <tr>
                <th><label for="lokasi_uji">Nama Lokasi Uji</label></th>
                <td>
                    <input type="text" name="lokasi_uji" id="lokasi_uji" class="regular-text"
                           value="<?php echo esc_attr( $kegiatan->lokasi_uji ?? '' ); ?>" 
                           placeholder="Contoh: Hotel Grand Sahid Jaya / Lab Komputer Kampus B">
                    <p class="description">Tempat fisik ujian/asesmen dilaksanakan jika berbeda dengan kantor TUK.</p>
                </td>
            </tr>
            <tr>
                <th><label for="alamat_lokasi_uji">Alamat Lokasi Uji</label></th>
                <td>
                    <textarea name="alamat_lokasi_uji" id="alamat_lokasi_uji" rows="3" class="large-text"
                              placeholder="Contoh: Jl. Jenderal Sudirman Kav. 86, Jakarta Pusat"><?php echo esc_textarea( $kegiatan->alamat_lokasi_uji ?? '' ); ?></textarea>
                    <p class="description">Alamat lengkap tempat fisik asesmen diselenggarakan.</p>
                </td>
            </tr>
            <tr>
                <th><label for="kuota">Kuota Peserta *</label></th>
                <td>
                    <input type="number" name="kuota" id="kuota" min="1" 
                           value="<?php echo $kegiatan ? $kegiatan->kuota : 20; ?>" required>
                </td>
            </tr>
            <tr>
                <th><label for="status">Status *</label></th>
                <td>
                    <select name="status" id="status" required>
                        <option value="draft" <?php selected( $kegiatan->status ?? 'draft', 'draft' ); ?>>Draft</option>
                        <option value="dibuka" <?php selected( $kegiatan->status ?? '', 'dibuka' ); ?>>Dibuka</option>
                        <option value="ditutup" <?php selected( $kegiatan->status ?? '', 'ditutup' ); ?>>Ditutup</option>
                        <option value="selesai" <?php selected( $kegiatan->status ?? '', 'selesai' ); ?>>Selesai</option>
                    </select>
                </td>
            </tr>
        </table>

        <?php
        $assigned_asesor_ids = array();
        if ( ! empty( $kegiatan->asesor_ids ) ) {
            $decoded = json_decode( $kegiatan->asesor_ids, true );
            if ( is_array( $decoded ) ) {
                $assigned_asesor_ids = $decoded;
            }
        }
        if ( empty( $assigned_asesor_ids ) ) {
            $assigned_asesor_ids = array( '' );
        }
        ?>

        <hr style="margin: 25px 0 20px 0; border: none; border-top: 1px solid #e2e8f0;">

        <h2>1. Asesor yang Bertugas</h2>
        <p class="description">Tentukan Asesor Kompetensi yang bertugas menguji pada kegiatan sertifikasi ini (Asesor 1, Asesor 2, dst).</p>
        
        <div id="asesor-bertugas-list" style="margin-top: 12px; max-width: 650px;">
            <?php foreach ( $assigned_asesor_ids as $index => $curr_asesor_id ) : ?>
                <div class="asesor-item-row" style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; background: #f8fafc; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <span class="asesor-index-label" style="font-weight: 600; width: 85px; color: #1e293b;">Asesor <?php echo ( $index + 1 ); ?>:</span>
                    <select name="asesor_ids[]" class="asesor-dropdown" style="flex: 1; max-width: 400px;">
                        <option value="">-- Pilih Asesor Terdaftar --</option>
                        <?php foreach ( $asesor_list as $a ) : ?>
                            <option value="<?php echo $a['id']; ?>" <?php selected( $curr_asesor_id, $a['id'] ); ?>>
                                <?php echo esc_html( $a['name'] ); ?> <?php echo $a['no_reg'] ? '(' . esc_html( $a['no_reg'] ) . ')' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="button button-link-delete btn-remove-asesor" style="<?php echo ( count( $assigned_asesor_ids ) <= 1 && $index === 0 ) ? 'display: none;' : ''; ?>" title="Hapus Asesor ini">
                        🗑️ Hapus
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
        <p style="margin-top: 8px;">
            <button type="button" class="button" id="btn-tambah-asesor">➕ Tambah Asesor</button>
        </p>

        <hr style="margin: 25px 0 20px 0; border: none; border-top: 1px solid #e2e8f0;">

        <h2>2. Penyusun dan Validator MUK</h2>
        <p class="description">Pilih Asesor Penyusun dan Validator MUK. Nama dan Nomor MET otomatis diambil dari data asesor tanpa perlu input manual.</p>
        <table class="form-table">
            <!-- Asesor Penyusun MUK -->
            <tr>
                <th><label for="penyusun_id">Asesor Penyusun MUK</label></th>
                <td>
                    <select name="penyusun_id" id="penyusun_id" style="min-width: 380px;">
                        <option value="">-- Pilih Asesor Penyusun MUK --</option>
                        <?php foreach ( $asesor_list as $a ) : ?>
                            <option value="<?php echo $a['id']; ?>" <?php selected( $kegiatan->penyusun_id ?? '', $a['id'] ); ?>>
                                <?php echo esc_html( $a['name'] ); ?> <?php echo $a['no_reg'] ? '(' . esc_html( $a['no_reg'] ) . ')' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Nama dan Nomor MET otomatis digunakan pada dokumen FR.MAPA.01, FR.MAPA.02, dan instrumen terkait.</p>
                </td>
            </tr>

            <!-- Validator MUK -->
            <tr>
                <th><label for="validator_id">Validator MUK</label></th>
                <td>
                    <select name="validator_id" id="validator_id" style="min-width: 380px;">
                        <option value="">-- Pilih Validator MUK --</option>
                        <?php foreach ( $asesor_list as $a ) : ?>
                            <option value="<?php echo $a['id']; ?>" <?php selected( $kegiatan->validator_id ?? '', $a['id'] ); ?>>
                                <?php echo esc_html( $a['name'] ); ?> <?php echo $a['no_reg'] ? '(' . esc_html( $a['no_reg'] ) . ')' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Nama dan Nomor MET validator otomatis digunakan pada proses validasi materi uji.</p>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <button type="submit" class="button button-primary">Simpan</button>
            <a href="<?php echo admin_url( 'admin.php?page=lsp-sertifikasi' ); ?>" class="button">Batal</a>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Dynamic load Paket MUK on Skema change
    $('#skema_id').on('change', function() {
        var skemaId = $(this).val();
        var $paketSelect = $('#paket_muk_id');
        if (!skemaId) {
            $paketSelect.html('<option value="">-- Pilih Paket MUK --</option>');
            return;
        }
        $paketSelect.html('<option value="">Memuat paket MUK...</option>');
        $.get(lspCert.ajax_url, {
            action: 'lsp_cert_get_paket_by_skema',
            nonce: lspCert.nonce,
            skema_id: skemaId
        }, function(res) {
            if (res.success && res.data && res.data.length > 0) {
                var html = '';
                res.data.forEach(function(p) {
                    html += '<option value="' + p.id + '" ' + (p.is_default ? 'selected' : '') + '>' + p.nama_paket + (p.is_default ? ' ★ (Default)' : '') + '</option>';
                });
                $paketSelect.html(html);
            } else {
                $paketSelect.html('<option value="">Belum ada paket MUK untuk skema ini</option>');
            }
        });
    });

    function refreshAsesorLabels() {
        var $rows = $('#asesor-bertugas-list .asesor-item-row');
        $rows.each(function(idx) {
            $(this).find('.asesor-index-label').text('Asesor ' + (idx + 1) + ':');
            if ($rows.length > 1) {
                $(this).find('.btn-remove-asesor').show();
            } else {
                $(this).find('.btn-remove-asesor').hide();
            }
        });
    }

    $('#btn-tambah-asesor').on('click', function(e) {
        e.preventDefault();
        var $lastRow = $('#asesor-bertugas-list .asesor-item-row:last');
        var $newRow = $lastRow.clone();
        $newRow.find('select').val('');
        $('#asesor-bertugas-list').append($newRow);
        refreshAsesorLabels();
    });

    $(document).on('click', '.btn-remove-asesor', function(e) {
        e.preventDefault();
        if ($('#asesor-bertugas-list .asesor-item-row').length > 1) {
            $(this).closest('.asesor-item-row').remove();
            refreshAsesorLabels();
        }
    });

    $('#kegiatan-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.post(lspCert.ajax_url, formData + '&action=lsp_cert_save_kegiatan&nonce=' + lspCert.nonce, function(response) {
            if (response.success) {
                alert(response.data.message);
                window.location.href = '<?php echo admin_url( 'admin.php?page=lsp-sertifikasi' ); ?>';
            } else {
                alert('Error: ' + response.data);
            }
        });
    });
});
</script>
