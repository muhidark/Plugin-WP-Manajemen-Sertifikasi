<div class="wrap">
    <h1>Pendaftaran Sertifikasi</h1>
    
    <div class="tablenav top">
        <form method="get">
            <input type="hidden" name="page" value="lsp-pendaftaran">
            
            <select name="kegiatan_id">
                <option value="">Semua Kegiatan</option>
                <?php foreach ( $kegiatan_list as $keg ) : 
                    $skema = LSP_Cert_Integration::get_skema( $keg->skema_id );
                ?>
                    <option value="<?php echo $keg->id; ?>" <?php selected( $kegiatan_id, $keg->id ); ?>>
                        <?php echo esc_html( $skema['title'] ?? 'Kegiatan #' . $keg->id ); ?> - <?php echo date_i18n( 'd/m/Y', strtotime( $keg->tanggal_pelaksanaan ) ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="status">
                <option value="">Semua Status</option>
                <option value="pending" <?php selected( $status_filter, 'pending' ); ?>>Pending</option>
                <option value="lengkap" <?php selected( $status_filter, 'lengkap' ); ?>>Lengkap</option>
                <option value="disetujui" <?php selected( $status_filter, 'disetujui' ); ?>>Disetujui</option>
            </select>
            
            <button type="submit" class="button">Filter</button>
        </form>
    </div>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 45px;">ID</th>
                <th>Nama Asesi</th>
                <th>Skema</th>
                <th>Tanggal</th>
                <th>TUK</th>
                <th>No. WA</th>
                <th>Status</th>
                <th>Sertifikat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( empty( $pendaftaran_list ) ) : ?>
                <tr>
                    <td colspan="9">Belum ada pendaftaran</td>
                </tr>
            <?php else : ?>
                <?php foreach ( $pendaftaran_list as $pendaftaran ) : 
                    $keg   = LSP_Cert_Database::get_kegiatan( $pendaftaran->kegiatan_id );
                    $skema = LSP_Cert_Integration::get_skema( $keg->skema_id );
                    $tuk   = LSP_Cert_Integration::get_tuk( $keg->tuk_id );
                    $cert  = LSP_Cert_Database::get_sertifikat_by_pendaftaran( $pendaftaran->id, $pendaftaran->nama_lengkap );
                ?>
                    <tr>
                        <td><?php echo $pendaftaran->id; ?></td>
                        <td><strong><?php echo esc_html( $pendaftaran->nama_lengkap ); ?></strong></td>
                        <td><?php echo esc_html( $skema['title'] ?? '-' ); ?></td>
                        <td><?php echo date_i18n( 'd/m/Y', strtotime( $keg->tanggal_pelaksanaan ) ); ?></td>
                        <td><?php echo esc_html( $tuk['title'] ?? '-' ); ?></td>
                        <td><?php echo esc_html( $pendaftaran->telepon ); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $pendaftaran->status_rekomendasi; ?>">
                                <?php echo ucfirst( $pendaftaran->status_rekomendasi ); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ( $cert ) : ?>
                                <a href="<?php echo esc_url( $cert['edit_url'] ); ?>" title="No. Reg: <?php echo esc_attr( $cert['no_registrasi'] ); ?>" style="text-decoration: none;">
                                    <span style="background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                        🏅 <?php echo esc_html( $cert['kode_blanko'] ); ?>
                                    </span>
                                </a>
                            <?php else : ?>
                                <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=data_sertifikat&pendaftaran_id=' . $pendaftaran->id ) ); ?>" class="button button-small" style="font-size: 11px; color: #475569;" title="Klik untuk menerbitkan sertifikat">
                                    + Terbitkan
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=view&id=' . $pendaftaran->id ) ); ?>" class="button button-small">Lihat</a>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=all' ) ); ?>" target="_blank" class="button button-small" title="Cetak Formulir Asesmen">🖨️ Cetak</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
