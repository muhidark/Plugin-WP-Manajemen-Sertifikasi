<div class="lsp-kegiatan-list">
    <h2>Daftar Kegiatan Sertifikasi</h2>
    
    <?php if ( empty( $kegiatan_list ) ) : ?>
        <p>Belum ada kegiatan sertifikasi yang tersedia.</p>
    <?php else : ?>
        <table class="lsp-table">
            <thead>
                <tr>
                    <th>Skema Sertifikasi</th>
                    <th>Tanggal</th>
                    <th>TUK</th>
                    <th>Kuota</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $kegiatan_list as $kegiatan ) : 
                    $skema = LSP_Cert_Integration::get_skema( $kegiatan->skema_id );
                    $tuk = LSP_Cert_Integration::get_tuk( $kegiatan->tuk_id );
                    $peserta_count = LSP_Cert_Database::get_peserta_count( $kegiatan->id );
                    $sisa_kuota = $kegiatan->kuota - $peserta_count;
                ?>
                    <tr>
                        <td><?php echo esc_html( $skema['title'] ?? '-' ); ?></td>
                        <td><?php echo date_i18n( 'd/m/Y', strtotime( $kegiatan->tanggal_pelaksanaan ) ); ?></td>
                        <td><?php echo esc_html( $tuk['title'] ?? '-' ); ?></td>
                        <td><?php echo $sisa_kuota; ?> / <?php echo $kegiatan->kuota; ?></td>
                        <td>
                            <?php if ( $sisa_kuota > 0 ) : ?>
                                <a href="<?php echo esc_url( add_query_arg( 'kegiatan_id', $kegiatan->id, get_permalink() ) ); ?>#form-pendaftaran" class="lsp-button">Daftar</a>
                            <?php else : ?>
                                <span class="lsp-disabled">Penuh</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
