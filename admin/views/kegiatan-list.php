<div class="wrap">
    <h1>
        Jadwal Sertifikasi
        <a href="<?php echo admin_url( 'admin.php?page=lsp-sertifikasi&action=add' ); ?>" class="page-title-action">Tambah Jadwal Baru</a>
    </h1>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Skema</th>
                <th>Tanggal</th>
                <th>TUK</th>
                <th>Asesor Bertugas</th>
                <th style="width: 60px;">Kuota</th>
                <th style="width: 70px;">Terdaftar</th>
                <th style="width: 80px;">Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $kegiatan_list ) ) : ?>
                <tr>
                    <td colspan="9">Belum ada jadwal sertifikasi</td>
                </tr>
            <?php else : ?>
                <?php foreach ( $kegiatan_list as $kegiatan ) : 
                    $skema = LSP_Cert_Integration::get_skema( $kegiatan->skema_id );
                    $tuk = LSP_Cert_Integration::get_tuk( $kegiatan->tuk_id );
                    $peserta_count = LSP_Cert_Database::get_peserta_count( $kegiatan->id );

                    $asesor_names = array();
                    if ( ! empty( $kegiatan->asesor_ids ) ) {
                        $a_ids = json_decode( $kegiatan->asesor_ids, true );
                        if ( is_array( $a_ids ) ) {
                            foreach ( $a_ids as $aid ) {
                                $a_obj = LSP_Cert_Integration::get_asesor( $aid );
                                if ( $a_obj ) {
                                    $asesor_names[] = $a_obj['name'];
                                }
                            }
                        }
                    }
                    if ( empty( $asesor_names ) && ! empty( $kegiatan->penyusun_nama ) ) {
                        $asesor_names[] = $kegiatan->penyusun_nama;
                    }
                ?>
                    <tr>
                        <td><?php echo $kegiatan->id; ?></td>
                        <td><strong><?php echo esc_html( $skema['title'] ?? '-' ); ?></strong></td>
                        <td><?php echo date_i18n( 'd/m/Y', strtotime( $kegiatan->tanggal_pelaksanaan ) ); ?></td>
                        <td><?php echo esc_html( $tuk['title'] ?? '-' ); ?></td>
                        <td><?php echo ! empty( $asesor_names ) ? esc_html( implode( ', ', $asesor_names ) ) : '<span style="color:#94a3b8; font-style: italic;">Belum ditentukan</span>'; ?></td>
                        <td><?php echo $kegiatan->kuota; ?></td>
                        <td><?php echo $peserta_count; ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $kegiatan->status; ?>">
                                <?php echo ucfirst( $kegiatan->status ); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo admin_url( 'admin.php?page=lsp-sertifikasi&action=edit&id=' . $kegiatan->id ); ?>">Edit</a> |
                            <a href="<?php echo admin_url( 'admin.php?page=lsp-pendaftaran&kegiatan_id=' . $kegiatan->id ); ?>">Peserta</a> |
                            <span class="lsp-dropdown-wrap" style="position: relative; display: inline-block;">
                                <a href="#" class="lsp-dropdown-trigger" style="font-weight: 600; color: #0284c7;" onclick="event.preventDefault(); toggleDocMenu(<?php echo $kegiatan->id; ?>);" title="Cetak Dokumen Kegiatan">
                                    🖨️ Cetak Dokumen &#9662;
                                </a>
                                <div id="lsp-doc-menu-<?php echo $kegiatan->id; ?>" class="lsp-doc-dropdown-menu" style="display: none; position: absolute; left: 0; top: 100%; z-index: 9999; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2), 0 8px 10px -6px rgba(0,0,0,0.1); width: 220px; text-align: left; padding: 6px 0; margin-top: 4px;">
                                    <div style="padding: 4px 12px; font-size: 10.5px; font-weight: bold; color: #64748b; text-transform: uppercase;">FR.MAPA.01</div>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=mapa01_obs' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #1e293b; text-decoration: none; font-size: 12px;">📋 FR.MAPA.01 — Observasi</a>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=mapa01_dit' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #1e293b; text-decoration: none; font-size: 12px;">📋 FR.MAPA.01 — DIT</a>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=mapa01_vp' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #1e293b; text-decoration: none; font-size: 12px;">📋 FR.MAPA.01 — Portofolio</a>
                                    <div style="border-top: 1px solid #e2e8f0; margin: 4px 0;"></div>
                                    <div style="padding: 4px 12px; font-size: 10.5px; font-weight: bold; color: #64748b; text-transform: uppercase;">FR.MAPA.02</div>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=mapa02' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #16a34a; text-decoration: none; font-size: 12px; font-weight: 600;">🗺️ FR.MAPA.02 — Peta Instrumen</a>
                                    <div style="border-top: 1px solid #e2e8f0; margin: 4px 0;"></div>
                                    <div style="padding: 4px 12px; font-size: 10.5px; font-weight: bold; color: #64748b; text-transform: uppercase;">FR.AK</div>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=ak05' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #0284c7; text-decoration: none; font-size: 12px; font-weight: 600;">📊 FR.AK.05 — Laporan Asesmen</a>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=ak06' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #7c3aed; text-decoration: none; font-size: 12px; font-weight: 600;">🔍 FR.AK.06 — Meninjau Asesmen</a>
                                    <div style="border-top: 1px solid #e2e8f0; margin: 4px 0;"></div>
                                    <div style="padding: 4px 12px; font-size: 10.5px; font-weight: bold; color: #64748b; text-transform: uppercase;">FR.IA.01 - 03</div>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=ia01' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #0284c7; text-decoration: none; font-size: 12px; font-weight: 600;">👁️ FR.IA.01 — Observasi</a>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=ia02' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #16a34a; text-decoration: none; font-size: 12px; font-weight: 600;">🛠️ FR.IA.02 — Praktik</a>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=ia03' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #7c3aed; text-decoration: none; font-size: 12px; font-weight: 600;">💬 FR.IA.03 — PMO</a>
                                    <div style="border-top: 1px solid #e2e8f0; margin: 4px 0;"></div>
                                    <div style="padding: 4px 12px; font-size: 10.5px; font-weight: bold; color: #64748b; text-transform: uppercase;">FR.IA.04</div>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=ia04a' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #0284c7; text-decoration: none; font-size: 12px; font-weight: 600;">📋 FR.IA.04A — DIT Proyek</a>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=ia04b' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #16a34a; text-decoration: none; font-size: 12px; font-weight: 600;">⚖️ FR.IA.04B — Penilaian Proyek</a>
                                    <div style="border-top: 1px solid #e2e8f0; margin: 4px 0;"></div>
                                    <div style="padding: 4px 12px; font-size: 10.5px; font-weight: bold; color: #64748b; text-transform: uppercase;">FR.IA.05</div>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=ia05a' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #0284c7; text-decoration: none; font-size: 12px; font-weight: 600;">📝 FR.IA.05A — Soal PG</a>
                                    <?php if ( current_user_can( 'manage_options' ) || current_user_can( 'asesor' ) || LSP_Cert_Integration::is_asesor( get_current_user_id() ) ) : ?>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=ia05b' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #dc2626; text-decoration: none; font-size: 12px; font-weight: 600;">🔒 FR.IA.05B — Kunci PG (Khusus)</a>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $kegiatan->id . '&doc=ia05c' ) ); ?>" target="_blank" style="display: block; padding: 5px 12px; color: #16a34a; text-decoration: none; font-size: 12px; font-weight: 600;">✍️ FR.IA.05C — Lembar Jawaban</a>
                                </div>
                            </span> |
                            <a href="#" class="delete-kegiatan" data-id="<?php echo $kegiatan->id; ?>">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function toggleDocMenu(id) {
    var menu = document.getElementById('lsp-doc-menu-' + id);
    if (!menu) return;
    var isShown = menu.style.display === 'block';
    document.querySelectorAll('.lsp-doc-dropdown-menu').forEach(function(el) {
        el.style.display = 'none';
    });
    if (!isShown) {
        menu.style.display = 'block';
    }
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.lsp-dropdown-wrap')) {
        document.querySelectorAll('.lsp-doc-dropdown-menu').forEach(function(el) {
            el.style.display = 'none';
        });
    }
});

jQuery(document).ready(function($) {
    $('.delete-kegiatan').on('click', function(e) {
        e.preventDefault();
        
        if ( ! confirm( 'Yakin ingin menghapus kegiatan ini?' ) ) {
            return;
        }
        
        var id = $(this).data('id');
        
        $.post(lspCert.ajax_url, {
            action: 'lsp_cert_delete_kegiatan',
            nonce: lspCert.nonce,
            id: id
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data);
            }
        });
    });
});
</script>
