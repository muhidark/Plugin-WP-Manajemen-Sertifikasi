<div class="wrap">
    <h1>Dashboard Sertifikasi LSP</h1>
    
    <div class="lsp-dashboard-stats">
        <div class="stat-box">
            <h3><?php echo $total_kegiatan; ?></h3>
            <p>Total Kegiatan</p>
        </div>
        <div class="stat-box">
            <h3><?php echo $total_pendaftaran; ?></h3>
            <p>Total Pendaftaran</p>
        </div>
        <div class="stat-box pending">
            <h3><?php echo $pending_verifikasi; ?></h3>
            <p>Menunggu Verifikasi</p>
        </div>
        <div class="stat-box approved">
            <h3><?php echo $disetujui; ?></h3>
            <p>Disetujui</p>
        </div>
    </div>
    
    <div class="lsp-dashboard-actions">
        <h2>Aksi Cepat</h2>
        <a href="<?php echo admin_url( 'admin.php?page=lsp-sertifikasi&action=add' ); ?>" class="button button-primary">
            + Tambah Kegiatan Baru
        </a>
        <a href="<?php echo admin_url( 'admin.php?page=lsp-pendaftaran' ); ?>" class="button">
            Lihat Semua Pendaftaran
        </a>
    </div>
    
    <div class="lsp-dashboard-info">
        <h2>Informasi Plugin</h2>
        <table class="widefat">
            <tr>
                <th>Versi Plugin</th>
                <td><?php echo LSP_CERT_VERSION; ?></td>
            </tr>
            <tr>
                <th>Core Management</th>
                <td>
                    <?php 
                    if ( is_plugin_active( 'lsp-core-management/lsp-core-management.php' ) ) {
                        echo '<span style="color:green;">✓ Aktif</span>';
                    } else {
                        echo '<span style="color:red;">✗ Tidak Aktif</span>';
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>
</div>
