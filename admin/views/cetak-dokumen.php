<?php
/**
 * View Cetak Dokumen Asesmen Sertifikasi
 * Portal Terpadu Dokumen APL, MAPA, AK, dan IA
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h1 style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
        <span>Cetak Dokumen Asesmen Sertifikasi</span>
        <span style="font-size: 13.5px; font-weight: normal; color: #64748b; background: #e2e8f0; padding: 3px 10px; border-radius: 4px;">
            Portal Terpadu APL &bull; MAPA &bull; AK &bull; IA
        </span>
    </h1>
    <p style="margin: 0 0 18px 0; color: #475569; font-size: 13px;">
        Kelola standar instrumen, konfigurasi kelompok pekerjaan, dan cetak seluruh formulir asesmen standar BNSP secara terintegrasi.
    </p>

    <!-- TAB UTAMA DOKUMEN SESUAI URUTAN BNSP -->
    <nav class="nav-tab-wrapper wp-clearfix" style="margin-bottom: 20px; border-bottom: 2px solid #cbd5e1;">
        <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'paket_muk' ), admin_url( 'admin.php' ) ) ); ?>" 
           class="nav-tab <?php echo 'paket_muk' === $active_tab ? 'nav-tab-active' : ''; ?>" style="font-size: 13px; font-weight: 700; background: #fef3c7; color: #92400e; border-color: #f59e0b;">
            📦 Bank Data Paket MUK
        </a>
        <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'apl' ), admin_url( 'admin.php' ) ) ); ?>" 
           class="nav-tab <?php echo 'apl' === $active_tab ? 'nav-tab-active' : ''; ?>" style="font-size: 13px; font-weight: 700;">
            👥 Berkas Peserta (APL &bull; MAPA &bull; IA &bull; AK)
        </a>
        <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'mapa1', 'subtab' => 'skkni' ), admin_url( 'admin.php' ) ) ); ?>" 
           class="nav-tab <?php echo 'mapa1' === $active_tab ? 'nav-tab-active' : ''; ?>" style="font-size: 13px; font-weight: 600;">
            📋 3. MAPA 1 (SKKNI &amp; Kelompok)
        </a>
        <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'mapa2' ), admin_url( 'admin.php' ) ) ); ?>" 
           class="nav-tab <?php echo 'mapa2' === $active_tab ? 'nav-tab-active' : ''; ?>" style="font-size: 13px; font-weight: 600;">
            🗺️ 4. MAPA 2
        </a>
        <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'ak01' ), admin_url( 'admin.php' ) ) ); ?>" 
           class="nav-tab <?php echo 'ak01' === $active_tab ? 'nav-tab-active' : ''; ?>" style="font-size: 13px; font-weight: 600;">
            🔏 5. FR.AK.01 (Persetujuan)
        </a>
        <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'ak04' ), admin_url( 'admin.php' ) ) ); ?>" 
           class="nav-tab <?php echo 'ak04' === $active_tab ? 'nav-tab-active' : ''; ?>" style="font-size: 13px; font-weight: 600;">
            ⚖️ 6. FR.AK.04 (Banding)
        </a>
        <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'ak07' ), admin_url( 'admin.php' ) ) ); ?>" 
           class="nav-tab <?php echo 'ak07' === $active_tab ? 'nav-tab-active' : ''; ?>" style="font-size: 13px; font-weight: 600; background: #fefce8; color: #854d0e;">
            ⚖️ 7. FR.AK.07 (Penyesuaian Wajar)
        </a>
        <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'ia' ), admin_url( 'admin.php' ) ) ); ?>" 
           class="nav-tab <?php echo 'ia' === $active_tab ? 'nav-tab-active' : ''; ?>" style="font-size: 13px; font-weight: 600;">
            🛠️ 10-20. Instrumen (FR.IA)
        </a>
        <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'keputusan' ), admin_url( 'admin.php' ) ) ); ?>" 
           class="nav-tab <?php echo 'keputusan' === $active_tab ? 'nav-tab-active' : ''; ?>" style="font-size: 13px; font-weight: 600;">
            ⚖️ 21-22. Keputusan (FR.AK)
        </a>
        <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'laporan' ), admin_url( 'admin.php' ) ) ); ?>" 
           class="nav-tab <?php echo 'laporan' === $active_tab ? 'nav-tab-active' : ''; ?>" style="font-size: 13px; font-weight: 600;">
            📑 23-25. Laporan &amp; Validasi
        </a>
        <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'roadmap' ), admin_url( 'admin.php' ) ) ); ?>" 
           class="nav-tab <?php echo 'roadmap' === $active_tab ? 'nav-tab-active' : ''; ?>" style="font-size: 13px; font-weight: 600; background: #e0f2fe; color: #0284c7;">
            📊 Matriks Lengkap BNSP (1-25)
        </a>
    </nav>

    <!-- ======================================================== -->
    <!-- TAB 0: BANK DATA PAKET MUK (MULTI-VERSI BUNDLING)        -->
    <!-- ======================================================== -->
    <?php if ( 'paket_muk' === $active_tab ) : ?>
        <?php include LSP_CERT_PLUGIN_DIR . 'admin/views/part-bank-data-muk.php'; ?>

    <!-- ======================================================== -->
    <!-- TAB 1: APL 01 & 02                                       -->
    <!-- ======================================================== -->
    <?php elseif ( 'apl' === $active_tab ) : ?>
        <div class="card" style="padding: 18px 22px; max-width: 100%; margin-bottom: 20px;">
            <h2 style="margin: 0 0 10px 0; font-size: 15px; color: #1e293b;">🎯 Pilih Jadwal &amp; Peserta Sertifikasi</h2>
            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <input type="hidden" name="page" value="lsp-cetak-dokumen">
                <input type="hidden" name="tab" value="apl">

                <label for="kegiatan_selector" style="font-weight: 600; font-size: 13px;">Jadwal Sertifikasi:</label>
                <select name="kegiatan_id" id="kegiatan_selector" style="min-width: 320px; padding: 5px 10px;" onchange="this.form.submit();">
                    <?php if ( empty( $all_kegiatan ) ) : ?>
                        <option value="">-- Belum ada jadwal tersedia --</option>
                    <?php else : ?>
                        <?php foreach ( $all_kegiatan as $kg ) : ?>
                            <option value="<?php echo esc_attr( $kg->id ); ?>" <?php selected( $selected_kegiatan_id, $kg->id ); ?>>
                                <?php echo esc_html( $kg->judul_kegiatan ); ?> (<?php echo esc_html( $kg->kode_kegiatan ); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <button type="submit" class="button">Pilih Jadwal</button>
            </form>
        </div>

        <?php if ( ! empty( $selected_kegiatan ) ) : ?>
            <div class="card" style="padding: 20px; max-width: 100%;">
                <h3 style="margin-top: 0; color: #0f172a; font-size: 15px; display: flex; align-items: center; justify-content: space-between;">
                    <span>Daftar Peserta (Asesi) — Jadwal: <?php echo esc_html( $selected_kegiatan->judul_kegiatan ); ?></span>
                    <span style="font-size: 12px; color: #64748b; font-weight: normal;">Total: <?php echo count( $peserta_list ); ?> Peserta</span>
                </h3>

                <?php if ( empty( $peserta_list ) ) : ?>
                    <p style="font-style: italic; color: #64748b; margin: 15px 0;">
                        Belum ada peserta yang terdaftar pada jadwal sertifikasi ini. 
                        Silakan daftarkan peserta melalui menu <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran' ) ); ?>">Pendaftaran</a>.
                    </p>
                <?php else : ?>
                    <table class="widefat striped" style="margin-top: 12px; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
                        <thead>
                            <tr style="background: #f1f5f9;">
                                <th style="width: 4%; text-align: center;">No.</th>
                                <th style="width: 22%;">Nama Asesi</th>
                                <th style="width: 20%;">Asesor Penugasan</th>
                                <th style="width: 14%;">Status Verifikasi</th>
                                <th style="width: 40%; text-align: center;">Aksi Cetak Dokumen Terhubung</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $peserta_list as $p_idx => $p ) : 
                                $asesor_p = ! empty( $p->asesor_id ) ? LSP_Cert_Integration::get_asesor( $p->asesor_id ) : null;
                            ?>
                                <tr>
                                    <td style="text-align: center; font-weight: 600; color: #64748b;"><?php echo ( $p_idx + 1 ); ?></td>
                                    <td>
                                        <strong style="font-size: 13.5px; color: #0f172a;"><?php echo esc_html( $p->nama_lengkap ); ?></strong><br>
                                        <span style="font-size: 11px; color: #64748b;">NIK: <?php echo esc_html( $p->nik ?? '-' ); ?></span> &bull; 
                                        <span style="font-size: 11px; color: #64748b;"><?php echo esc_html( $p->email ); ?></span>
                                    </td>
                                    <td>
                                        <?php if ( $asesor_p ) : ?>
                                            <strong style="color: #047857;"><?php echo esc_html( $asesor_p['name'] ); ?></strong><br>
                                            <span style="font-size: 11px; color: #64748b; font-family: monospace;"><?php echo esc_html( $asesor_p['no_reg'] ?? '-' ); ?></span>
                                        <?php else : ?>
                                            <span style="color: #dc2626; font-style: italic; font-size: 11.5px;">Belum Ditugaskan</span><br>
                                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=view&id=' . $p->id ) ); ?>" style="font-size: 11px;">⚙️ Assign Asesor &rarr;</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 3px;">
                                            <div>
                                                <span style="font-size: 10.5px; color: #64748b;">APL.01:</span>
                                                <span class="badge" style="background:#e0f2fe; color:#0369a1; padding:1px 6px; border-radius:3px; font-size:10.5px; font-weight:600;">
                                                    <?php echo esc_html( ucfirst( $p->status_kelengkapan ?? 'Pending' ) ); ?>
                                                </span>
                                            </div>
                                            <div>
                                                <span style="font-size: 10.5px; color: #64748b;">APL.02:</span>
                                                <span class="badge" style="background:#fef3c7; color:#92400e; padding:1px 6px; border-radius:3px; font-size:10.5px; font-weight:600;">
                                                    <?php echo esc_html( ucfirst( $p->status_rekomendasi ?? 'Pending' ) ); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; align-items: center;">
                                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $p->id . '&doc=bundle_all' ) ); ?>" target="_blank" class="button button-primary" style="background: #0284c7; border-color: #0284c7; font-weight: 700; font-size: 12px; padding: 2px 10px;" title="Cetak Seluruh Berkas 1 s/d 25 untuk Asesi Ini">
                                                📦 Paket Lengkap (1-25)
                                            </a>
                                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $p->id . '&doc=apl01' ) ); ?>" target="_blank" class="button button-small" title="Cetak Formulir FR.APL.01">
                                                📄 APL.01
                                            </a>
                                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $p->id . '&doc=apl02' ) ); ?>" target="_blank" class="button button-small" title="Cetak Formulir FR.APL.02">
                                                📄 APL.02
                                            </a>
                                            <button type="button" class="button button-small btn-modal-pilih-dokumen" 
                                                    data-id="<?php echo $p->id; ?>" 
                                                    data-nama="<?php echo esc_attr( $p->nama_lengkap ); ?>" 
                                                    data-nik="<?php echo esc_attr( $p->nik ?? '-' ); ?>"
                                                    data-asesor="<?php echo esc_attr( $asesor_p['name'] ?? 'Belum Ditugaskan' ); ?>"
                                                    style="font-weight: 600; color: #0f172a; border-color: #94a3b8;">
                                                📑 Dokumen Lainnya ▾
                                            </button>
                                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=view&id=' . $p->id ) ); ?>" class="button button-small" title="Buka Detail &amp; Verifikasi Peserta">
                                                🔍 Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <!-- ======================================================== -->
    <!-- TAB 2: MAPA 1 (SKKNI & KELOMPOK PEKERJAAN)                -->
    <!-- ======================================================== -->
    <?php elseif ( 'mapa1' === $active_tab ) : ?>
        <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px;">
            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <span style="font-weight: bold; font-size: 13px; color: #1e293b; margin-right: 10px;">Sub-Navigasi MAPA 1:</span>
                <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'mapa1', 'subtab' => 'skkni', 'skema_id' => $selected_skema_id ), admin_url( 'admin.php' ) ) ); ?>" 
                   class="button <?php echo 'skkni' === $active_subtab ? 'button-primary' : ''; ?>">
                    📚 1. SKKNI (Unit, Elemen &amp; KUK)
                </a>
                <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'mapa1', 'subtab' => 'kelompok', 'skema_id' => $selected_skema_id ), admin_url( 'admin.php' ) ) ); ?>" 
                   class="button <?php echo 'kelompok' === $active_subtab ? 'button-primary' : ''; ?>">
                    📂 2. Input Kelompok Pekerjaan
                </a>
                <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'lsp-cetak-dokumen', 'tab' => 'mapa1', 'subtab' => 'print', 'skema_id' => $selected_skema_id ), admin_url( 'admin.php' ) ) ); ?>" 
                   class="button <?php echo 'print' === $active_subtab ? 'button-primary' : ''; ?>">
                    🖨️ 3. Cetak FR.MAPA.01
                </a>
            </div>
        </div>

        <!-- Pemilihan Skema Sertifikasi untuk MAPA 1 -->
        <div class="card" style="padding: 15px 20px; max-width: 100%; margin-bottom: 20px;">
            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <input type="hidden" name="page" value="lsp-cetak-dokumen">
                <input type="hidden" name="tab" value="mapa1">
                <input type="hidden" name="subtab" value="<?php echo esc_attr( $active_subtab ); ?>">

                <label for="skema_selector_mapa1" style="font-weight: 600; font-size: 13px;">🎯 Pilih Skema Sertifikasi:</label>
                <select name="skema_id" id="skema_selector_mapa1" style="min-width: 350px; padding: 5px 10px;" onchange="this.form.submit();">
                    <?php if ( empty( $all_skema ) ) : ?>
                        <option value="">-- Belum ada skema --</option>
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

        <?php if ( 'skkni' === $active_subtab ) : ?>
            <!-- SUBTAB A: SKKNI DAFTAR UNIT, ELEMEN & KUK -->
            <div class="card" style="padding: 20px; max-width: 100%;">
                <h3 style="margin-top: 0; color: #0f172a; font-size: 15px;">
                    Daftar Standar Kompetensi Kerja Nasional Indonesia (SKKNI) &bull; <?php echo esc_html( $skema_data['title'] ?? '-' ); ?>
                </h3>
                <p style="margin: 0 0 15px 0; color: #64748b; font-size: 12.5px;">
                    Sumber data unit kompetensi, elemen, dan kriteria unjuk kerja (KUK) resmi dari Master Skema.
                </p>

                <?php if ( empty( $master_units ) ) : ?>
                    <p style="font-style: italic; color: #64748b;">Belum ada unit kompetensi terdaftar untuk skema ini.</p>
                <?php else : ?>
                    <table class="widefat striped" style="margin-top: 10px;">
                        <thead>
                            <tr style="background: #f1f5f9;">
                                <th style="width: 5%;">No.</th>
                                <th style="width: 25%;">Kode Unit</th>
                                <th style="width: 45%;">Judul Unit Kompetensi</th>
                                <th style="width: 25%;">Jumlah Elemen &amp; KUK</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $master_units as $u_idx => $u ) : 
                                $u_kode  = is_object( $u ) ? ( $u->kode_unit ?? ( $u->kode ?? '' ) ) : ( $u['kode'] ?? ( $u['kode_unit'] ?? '' ) );
                                $u_judul = is_object( $u ) ? ( $u->judul_unit ?? ( $u->judul ?? '' ) ) : ( $u['judul'] ?? ( $u['judul_unit'] ?? '' ) );
                                $elem_list = is_object( $u ) ? ( $u->elemen ?? array() ) : ( $u['elemen'] ?? array() );
                                $total_kuk = 0;
                                if ( ! empty( $elem_list ) ) {
                                    foreach ( $elem_list as $el ) {
                                        $total_kuk += count( $el['kuk'] ?? array() );
                                    }
                                }
                            ?>
                                <tr>
                                    <td><?php echo ( $u_idx + 1 ); ?></td>
                                    <td><strong style="font-family: monospace; font-size: 12px;"><?php echo esc_html( $u_kode ); ?></strong></td>
                                    <td><strong><?php echo esc_html( $u_judul ); ?></strong></td>
                                    <td>
                                        <span class="badge" style="background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:4px; font-size:11.5px; font-weight:600;">
                                            <?php echo count( $elem_list ); ?> Elemen
                                        </span>
                                        <span class="badge" style="background:#f1f5f9; color:#475569; padding:2px 8px; border-radius:4px; font-size:11.5px; margin-left: 4px;">
                                            <?php echo $total_kuk; ?> KUK
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        <?php elseif ( 'kelompok' === $active_subtab ) : ?>
            <!-- SUBTAB B: INPUT KELOMPOK PEKERJAAN -->
            <?php include LSP_CERT_PLUGIN_DIR . 'admin/views/kelompok-pekerjaan-settings.php'; ?>

        <?php elseif ( 'print' === $active_subtab ) : ?>
            <!-- SUBTAB C: CETAK FR.MAPA.01 -->
            <div class="card" style="padding: 20px; max-width: 100%;">
                <h3 style="margin-top: 0; color: #0f172a; font-size: 15px;">
                    Cetak Dokumen FR.MAPA.01 &bull; Merencanakan Aktivitas dan Proses Asesmen
                </h3>
                <p style="margin: 0 0 15px 0; color: #64748b; font-size: 12.5px;">
                    Pilih versi metode perencanaan asesmen yang ingin dicetak:
                </p>

                <!-- Filter Jadwal & Asesi -->
                <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; background: #f8fafc; padding: 12px 16px; border-radius: 6px; border: 1px solid #e2e8f0;">
                    <input type="hidden" name="page" value="lsp-cetak-dokumen">
                    <input type="hidden" name="tab" value="mapa1">
                    <input type="hidden" name="subtab" value="print">
                    <input type="hidden" name="skema_id" value="<?php echo esc_attr( $selected_skema_id ); ?>">

                    <div>
                        <label for="kegiatan_mapa1_print" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 4px;">🎯 Jadwal Sertifikasi:</label>
                        <select name="kegiatan_id" id="kegiatan_mapa1_print" style="min-width: 300px; padding: 5px 10px;" onchange="this.form.submit();">
                            <?php if ( empty( $all_kegiatan ) ) : ?>
                                <option value="">-- Belum ada jadwal tersedia --</option>
                            <?php else : ?>
                                <?php foreach ( $all_kegiatan as $kg ) : ?>
                                    <option value="<?php echo esc_attr( $kg->id ); ?>" <?php selected( $selected_kegiatan_id, $kg->id ); ?>>
                                        <?php echo esc_html( $kg->judul_kegiatan ); ?> (<?php echo esc_html( $kg->kode_kegiatan ); ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label for="pendaftaran_mapa1_print" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 4px;">👤 Pilih Asesi (Opsional):</label>
                        <select name="pendaftaran_id" id="pendaftaran_mapa1_print" style="min-width: 280px; padding: 5px 10px;" onchange="this.form.submit();">
                            <?php if ( empty( $peserta_list ) ) : ?>
                                <option value="0">-- Tingkat Jadwal (Umum) --</option>
                            <?php else : ?>
                                <?php foreach ( $peserta_list as $ps ) : ?>
                                    <option value="<?php echo esc_attr( $ps->id ); ?>" <?php selected( $selected_pendaftaran_id, $ps->id ); ?>>
                                        👤 <?php echo esc_html( $ps->nama_lengkap ); ?> (NIK: <?php echo esc_html( $ps->nik ?? '-' ); ?>)
                                    </option>
                                <?php endforeach; ?>
                                <option value="0" <?php selected( $selected_pendaftaran_id, 0 ); ?>>-- 🏛️ Tingkat Jadwal / Blank Asesi --</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div style="align-self: flex-end;">
                        <button type="submit" class="button">Terapkan Filter</button>
                    </div>
                </form>

                <?php 
                $base_mapa_url = ! empty( $selected_pendaftaran_id )
                    ? admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $selected_pendaftaran_id )
                    : ( ! empty( $selected_kegiatan_id ) ? admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $selected_kegiatan_id ) : '' );
                
                if ( $selected_pendaftaran ) : 
                    $asesor_mapa = ! empty( $selected_pendaftaran->asesor_id ) ? LSP_Cert_Integration::get_asesor( $selected_pendaftaran->asesor_id ) : null;
                ?>
                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 13px; color: #1e40af;">
                        👤 <strong>Dicetak Terhubung Dengan Asesi:</strong> <?php echo esc_html( $selected_pendaftaran->nama_lengkap ); ?> 
                        &bull; <strong>Asesor:</strong> <?php echo esc_html( $asesor_mapa['name'] ?? 'Masriah Hebat' ); ?>
                    </div>
                <?php endif; ?>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 15px; margin-top: 15px;">
                    <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 16px; background: #f8fafc;">
                        <h4 style="margin: 0 0 8px 0; color: #0369a1;">📋 Versi Observasi Langsung</h4>
                        <p style="font-size: 12px; color: #475569; margin: 0 0 12px 0;">Metode asesmen praktik demonstrasi langsung di tempat kerja atau simulasi.</p>
                        <?php if ( ! empty( $base_mapa_url ) ) : ?>
                            <a href="<?php echo esc_url( $base_mapa_url . '&doc=mapa01_obs' ); ?>" target="_blank" class="button button-primary">
                                🖨️ Cetak FR.MAPA.01 Observasi
                            </a>
                        <?php else : ?>
                            <button class="button" disabled>Pilih Jadwal Terlebih Dahulu</button>
                        <?php endif; ?>
                    </div>

                    <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 16px; background: #f8fafc;">
                        <h4 style="margin: 0 0 8px 0; color: #0369a1;">📋 Versi DIT (Daftar Instruksi Terstruktur)</h4>
                        <p style="font-size: 12px; color: #475569; margin: 0 0 12px 0;">Metode tugas proyek terstruktur berbasis kerangka STAR.</p>
                        <?php if ( ! empty( $base_mapa_url ) ) : ?>
                            <a href="<?php echo esc_url( $base_mapa_url . '&doc=mapa01_dit' ); ?>" target="_blank" class="button button-primary">
                                🖨️ Cetak FR.MAPA.01 DIT
                            </a>
                        <?php else : ?>
                            <button class="button" disabled>Pilih Jadwal Terlebih Dahulu</button>
                        <?php endif; ?>
                    </div>

                    <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 16px; background: #f8fafc;">
                        <h4 style="margin: 0 0 8px 0; color: #0369a1;">📋 Versi Verifikasi Portofolio</h4>
                        <p style="font-size: 12px; color: #475569; margin: 0 0 12px 0;">Metode asesmen portofolio bukti kerja terdahulu asesi.</p>
                        <?php if ( ! empty( $base_mapa_url ) ) : ?>
                            <a href="<?php echo esc_url( $base_mapa_url . '&doc=mapa01_vp' ); ?>" target="_blank" class="button button-primary">
                                🖨️ Cetak FR.MAPA.01 Portofolio
                            </a>
                        <?php else : ?>
                            <button class="button" disabled>Pilih Jadwal Terlebih Dahulu</button>
                        <?php endif; ?>
                    </div>

                    <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 16px; background: #f0fdf4; border-color: #86efac;">
                        <h4 style="margin: 0 0 8px 0; color: #15803d;">📑 Cetak Semua Versi Sekaligus</h4>
                        <p style="font-size: 12px; color: #475569; margin: 0 0 12px 0;">Mencetak ketiga versi FR.MAPA.01 dalam satu paket lembar terpisah.</p>
                        <?php if ( ! empty( $base_mapa_url ) ) : ?>
                            <a href="<?php echo esc_url( $base_mapa_url . '&doc=mapa01_all' ); ?>" target="_blank" class="button button-primary" style="background: #16a34a; border-color: #16a34a;">
                                📑 Cetak Paket 3 Versi MAPA.01
                            </a>
                        <?php else : ?>
                            <button class="button" disabled>Pilih Jadwal Terlebih Dahulu</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <!-- ======================================================== -->
    <!-- TAB 3: MAPA 2                                            -->
    <!-- ======================================================== -->
    <?php elseif ( 'mapa2' === $active_tab ) : ?>
        <div class="card" style="padding: 20px; max-width: 100%;">
            <h2 style="margin: 0 0 10px 0; font-size: 16px; color: #0f172a;">🗺️ FR.MAPA.02 — Peta Instrumen Asesmen</h2>
            <p style="margin: 0 0 18px 0; color: #64748b; font-size: 13px;">
                Peta instrumen asesmen hasil pendekatan asesmen dan perencanaan asesmen untuk seluruh unit kompetensi dalam skema.
            </p>

            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; background: #f8fafc; padding: 12px 16px; border-radius: 6px; border: 1px solid #e2e8f0;">
                <input type="hidden" name="page" value="lsp-cetak-dokumen">
                <input type="hidden" name="tab" value="mapa2">

                <div>
                    <label for="kegiatan_mapa2" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 4px;">🎯 Jadwal Sertifikasi:</label>
                    <select name="kegiatan_id" id="kegiatan_mapa2" style="min-width: 320px; padding: 5px 10px;" onchange="this.form.submit();">
                        <?php if ( empty( $all_kegiatan ) ) : ?>
                            <option value="">-- Belum ada jadwal tersedia --</option>
                        <?php else : ?>
                            <?php foreach ( $all_kegiatan as $kg ) : ?>
                                <option value="<?php echo esc_attr( $kg->id ); ?>" <?php selected( $selected_kegiatan_id, $kg->id ); ?>>
                                    <?php echo esc_html( $kg->judul_kegiatan ); ?> (<?php echo esc_html( $kg->kode_kegiatan ); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label for="pendaftaran_mapa2" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 4px;">👤 Pilih Asesi (Opsional):</label>
                    <select name="pendaftaran_id" id="pendaftaran_mapa2" style="min-width: 280px; padding: 5px 10px;" onchange="this.form.submit();">
                        <?php if ( empty( $peserta_list ) ) : ?>
                            <option value="0">-- Tingkat Jadwal (Umum) --</option>
                        <?php else : ?>
                            <?php foreach ( $peserta_list as $ps ) : ?>
                                <option value="<?php echo esc_attr( $ps->id ); ?>" <?php selected( $selected_pendaftaran_id, $ps->id ); ?>>
                                    👤 <?php echo esc_html( $ps->nama_lengkap ); ?> (NIK: <?php echo esc_html( $ps->nik ?? '-' ); ?>)
                                </option>
                            <?php endforeach; ?>
                            <option value="0" <?php selected( $selected_pendaftaran_id, 0 ); ?>>-- 🏛️ Tingkat Jadwal / Blank Asesi --</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div style="align-self: flex-end;">
                    <button type="submit" class="button">Terapkan Filter</button>
                </div>
            </form>

            <?php 
            $base_mapa2_url = ! empty( $selected_pendaftaran_id )
                ? admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $selected_pendaftaran_id . '&doc=mapa02' )
                : ( ! empty( $selected_kegiatan_id ) ? admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $selected_kegiatan_id . '&doc=mapa02' ) : '' );
            
            if ( $selected_pendaftaran ) : 
                $asesor_mapa2 = ! empty( $selected_pendaftaran->asesor_id ) ? LSP_Cert_Integration::get_asesor( $selected_pendaftaran->asesor_id ) : null;
            ?>
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 13px; color: #1e40af;">
                    👤 <strong>Dicetak Terhubung Dengan Asesi:</strong> <?php echo esc_html( $selected_pendaftaran->nama_lengkap ); ?> 
                    &bull; <strong>Asesor:</strong> <?php echo esc_html( $asesor_mapa2['name'] ?? 'Masriah Hebat' ); ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $base_mapa2_url ) ) : ?>
                <a href="<?php echo esc_url( $base_mapa2_url ); ?>" target="_blank" class="button button-primary button-large">
                    🖨️ Cetak FR.MAPA.02 Sekarang
                </a>
            <?php endif; ?>

            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 16px; margin-top: 15px;">
                <h4 style="margin: 0 0 8px 0; color: #1e293b;">Karakteristik Dokumen FR.MAPA.02:</h4>
                <ul style="margin: 0; padding-left: 20px; font-size: 12.5px; color: #475569; line-height: 1.6;">
                    <li>Dokumen tingkat kegiatan/jadwal (tidak memerlukan data pendaftaran perorangan).</li>
                    <li>Memetakan 12 jenis instrumen asesmen terhadap unit-unit kompetensi skema.</li>
                    <li>Checklist potensi asesi berstatus kosong (☐) siap untuk verifikasi asesor.</li>
                    <li>Tanggal penetapan otomatis H-2 sebelum pelaksanaan kegiatan sertifikasi.</li>
                </ul>
            </div>
        </div>

    <!-- ======================================================== -->
    <!-- TAB 4: FR.AK.04 FORMULIR BANDING                          -->
    <!-- ======================================================== -->
    <?php elseif ( 'ak04' === $active_tab ) : ?>
        <div class="card" style="padding: 20px; max-width: 100%;">
            <h2 style="margin: 0 0 10px 0; font-size: 16px; color: #0f172a;">⚖️ FR.AK.04 — Formulir Banding Asesmen</h2>
            <p style="margin: 0 0 18px 0; color: #64748b; font-size: 13px;">
                Formulir pengajuan banding asesmen resmi BNSP oleh asesi yang mengajukan keberatan terhadap keputusan rekomendasi hasil asesmen.
            </p>

            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; background: #f8fafc; padding: 12px 16px; border-radius: 6px; border: 1px solid #e2e8f0;">
                <input type="hidden" name="page" value="lsp-cetak-dokumen">
                <input type="hidden" name="tab" value="ak04">

                <div>
                    <label for="kegiatan_ak04" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 4px;">🎯 Jadwal Sertifikasi:</label>
                    <select name="kegiatan_id" id="kegiatan_ak04" style="min-width: 320px; padding: 5px 10px;" onchange="this.form.submit();">
                        <?php if ( empty( $all_kegiatan ) ) : ?>
                            <option value="">-- Belum ada jadwal tersedia --</option>
                        <?php else : ?>
                            <?php foreach ( $all_kegiatan as $kg ) : ?>
                                <option value="<?php echo esc_attr( $kg->id ); ?>" <?php selected( $selected_kegiatan_id, $kg->id ); ?>>
                                    <?php echo esc_html( $kg->judul_kegiatan ); ?> (<?php echo esc_html( $kg->kode_kegiatan ); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label for="pendaftaran_ak04" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 4px;">👤 Pilih Asesi (Opsional):</label>
                    <select name="pendaftaran_id" id="pendaftaran_ak04" style="min-width: 280px; padding: 5px 10px;" onchange="this.form.submit();">
                        <?php if ( empty( $peserta_list ) ) : ?>
                            <option value="0">-- Form Blank (Tanpa Nama Asesi) --</option>
                        <?php else : ?>
                            <?php foreach ( $peserta_list as $ps ) : ?>
                                <option value="<?php echo esc_attr( $ps->id ); ?>" <?php selected( $selected_pendaftaran_id, $ps->id ); ?>>
                                    👤 <?php echo esc_html( $ps->nama_lengkap ); ?> (NIK: <?php echo esc_html( $ps->nik ?? '-' ); ?>)
                                </option>
                            <?php endforeach; ?>
                            <option value="0" <?php selected( $selected_pendaftaran_id, 0 ); ?>>-- 📝 Form Blank (Tanpa Nama Asesi) --</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div style="align-self: flex-end;">
                    <button type="submit" class="button">Terapkan Filter</button>
                </div>
            </form>

            <?php 
            $base_ak04_url = ! empty( $selected_pendaftaran_id )
                ? admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $selected_pendaftaran_id . '&doc=ak04' )
                : ( ! empty( $selected_kegiatan_id ) ? admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $selected_kegiatan_id . '&doc=ak04' ) : '' );
            
            if ( $selected_pendaftaran ) : 
                $asesor_ak04 = ! empty( $selected_pendaftaran->asesor_id ) ? LSP_Cert_Integration::get_asesor( $selected_pendaftaran->asesor_id ) : null;
            ?>
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 13px; color: #1e40af;">
                    👤 <strong>Dicetak Terhubung Dengan Asesi:</strong> <?php echo esc_html( $selected_pendaftaran->nama_lengkap ); ?> 
                    &bull; <strong>Asesor:</strong> <?php echo esc_html( $asesor_ak04['name'] ?? 'Belum Ditugaskan' ); ?>
                    <div style="font-size: 11.5px; color: #3b82f6; margin-top: 3px;">
                        Kolom Nama Asesi, Nama Asesor, dan Skema otomatis terisi, klausul banding &amp; alasan tetap blank untuk diisi manual asesi.
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $base_ak04_url ) ) : ?>
                <a href="<?php echo esc_url( $base_ak04_url ); ?>" target="_blank" class="button button-primary button-large">
                    🖨️ Cetak FR.AK.04 Sekarang
                </a>
            <?php endif; ?>

            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 16px;">
                <h4 style="margin: 0 0 8px 0; color: #1e293b;">Ketentuan Formulir Banding FR.AK.04:</h4>
                <ul style="margin: 0; padding-left: 20px; font-size: 12.5px; color: #475569; line-height: 1.6;">
                    <li>Sesuai keputusan BNSP, formulir banding dihasilkan berupa <strong>form blank (tanpa isian otomatis)</strong> agar asesi dapat mengisinya secara bebas dan objektif.</li>
                    <li>Memuat 3 butir pertanyaan hak banding dengan pilihan YA / TIDAK kosong (☐).</li>
                    <li>Menyediakan kolom alasan banding dan penegasan klausul hak banding asesi.</li>
                </ul>
            </div>
        </div>

    <!-- ======================================================== -->
    <!-- TAB 5: FR.AK.01 PERSETUJUAN & KERAHASIAAN                 -->
    <!-- ======================================================== -->
    <?php elseif ( 'ak01' === $active_tab ) : ?>
        <div class="card" style="padding: 20px; max-width: 100%;">
            <h2 style="margin: 0 0 10px 0; font-size: 16px; color: #0f172a;">🔏 FR.AK.01 — Persetujuan Asesmen dan Kerahasiaan</h2>
            <p style="margin: 0 0 18px 0; color: #64748b; font-size: 13px;">
                Persetujuan pelaksanaan asesmen, komitmen kerahasiaan materi uji, dan penetapan jenis TUK Sewaktu antara asesi dan asesor.
            </p>

            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px; flex-wrap: wrap;">
                <input type="hidden" name="page" value="lsp-cetak-dokumen">
                <input type="hidden" name="tab" value="ak01">

                <label for="kegiatan_ak01" style="font-weight: 600; font-size: 13px;">Jadwal Sertifikasi:</label>
                <select name="kegiatan_id" id="kegiatan_ak01" style="min-width: 350px; padding: 5px 10px;" onchange="this.form.submit();">
                    <?php if ( empty( $all_kegiatan ) ) : ?>
                        <option value="">-- Belum ada jadwal tersedia --</option>
                    <?php else : ?>
                        <?php foreach ( $all_kegiatan as $kg ) : ?>
                            <option value="<?php echo esc_attr( $kg->id ); ?>" <?php selected( $selected_kegiatan_id, $kg->id ); ?>>
                                <?php echo esc_html( $kg->judul_kegiatan ); ?> (<?php echo esc_html( $kg->kode_kegiatan ); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </form>

            <?php if ( ! empty( $peserta_list ) ) : ?>
                <table class="widefat striped" style="margin-top: 10px;">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No.</th>
                            <th style="width: 35%;">Nama Asesi</th>
                            <th style="width: 30%;">Asesor Penugasan</th>
                            <th style="width: 30%; text-align: center;">Aksi Cetak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $peserta_list as $p_idx => $p ) : 
                            $asesor_p = ! empty( $p->asesor_id ) ? LSP_Cert_Integration::get_asesor( $p->asesor_id ) : null;
                        ?>
                            <tr>
                                <td><?php echo ( $p_idx + 1 ); ?></td>
                                <td><strong><?php echo esc_html( $p->nama_lengkap ); ?></strong></td>
                                <td><?php echo esc_html( $asesor_p['name'] ?? 'Belum Ditugaskan' ); ?></td>
                                <td style="text-align: center;">
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $p->id . '&doc=ak01' ) ); ?>" target="_blank" class="button button-primary button-small">
                                        🖨️ Cetak FR.AK.01
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p style="font-style: italic; color: #64748b;">Belum ada peserta terdaftar pada jadwal ini.</p>
            <?php endif; ?>
        </div>

    <!-- ======================================================== -->
    <!-- TAB 5B: FR.AK.07 PENYESUAIAN YANG WAJAR DAN BERALASAN     -->
    <!-- ======================================================== -->
    <?php elseif ( 'ak07' === $active_tab ) : ?>
        <div class="card" style="padding: 22px; max-width: 100%; box-sizing: border-box;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                <div>
                    <h2 style="margin: 0; font-size: 16.5px; color: #0f172a; font-weight: 700;">
                        ⚖️ FR.AK.07 — Ceklis Penyesuaian yang Wajar dan Beralasan
                    </h2>
                    <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">
                        Ceklis penyesuaian materi dan metode asesmen bagi asesi dengan kebutuhan khusus atau kondisi tertentu sesuai standar resmi BNSP.
                    </p>
                </div>
                <?php if ( ! empty( $selected_kegiatan_id ) ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-kegiatan&action=print&id=' . $selected_kegiatan_id . '&doc=ak07' ) ); ?>" target="_blank" class="button" style="font-weight: 600; padding: 4px 12px; height: auto;">
                        📄 Cetak Form Kosong (Blank) FR.AK.07
                    </a>
                <?php endif; ?>
            </div>

            <!-- Selector Jadwal Sertifikasi -->
            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; background: #f8fafc; padding: 12px 16px; border-radius: 6px; border: 1px solid #e2e8f0;">
                <input type="hidden" name="page" value="lsp-cetak-dokumen">
                <input type="hidden" name="tab" value="ak07">

                <label for="kegiatan_ak07" style="font-weight: 600; font-size: 13px; color: #1e293b;">🎯 Pilih Jadwal Sertifikasi:</label>
                <select name="kegiatan_id" id="kegiatan_ak07" style="min-width: 350px; padding: 6px 12px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px;" onchange="this.form.submit();">
                    <?php if ( empty( $all_kegiatan ) ) : ?>
                        <option value="">-- Belum ada jadwal tersedia --</option>
                    <?php else : ?>
                        <?php foreach ( $all_kegiatan as $kg ) : ?>
                            <option value="<?php echo esc_attr( $kg->id ); ?>" <?php selected( $selected_kegiatan_id, $kg->id ); ?>>
                                <?php echo esc_html( $kg->judul_kegiatan ); ?> (<?php echo esc_html( $kg->kode_kegiatan ); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <button type="submit" class="button">Ganti Jadwal</button>
            </form>

            <?php if ( ! empty( $peserta_list ) ) : ?>
                <table class="widefat striped" style="margin-top: 10px; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
                    <thead>
                        <tr style="background: #f1f5f9;">
                            <th style="width: 5%; text-align: center;">No.</th>
                            <th style="width: 35%;">Nama Asesi</th>
                            <th style="width: 30%;">Asesor Penugasan</th>
                            <th style="width: 30%; text-align: center;">Aksi Cetak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $peserta_list as $p_idx => $p ) : 
                            $asesor_p = ! empty( $p->asesor_id ) ? LSP_Cert_Integration::get_asesor( $p->asesor_id ) : null;
                        ?>
                            <tr>
                                <td style="text-align: center; font-weight: 600; color: #64748b;"><?php echo ( $p_idx + 1 ); ?></td>
                                <td>
                                    <strong style="font-size: 13.5px; color: #0f172a;"><?php echo esc_html( $p->nama_lengkap ); ?></strong><br>
                                    <span style="font-size: 11px; color: #64748b;">NIK: <?php echo esc_html( $p->nik ?? '-' ); ?></span> &bull; 
                                    <span style="font-size: 11px; color: #64748b;"><?php echo esc_html( $p->email ); ?></span>
                                </td>
                                <td>
                                    <?php if ( $asesor_p ) : ?>
                                        <strong style="color: #047857;"><?php echo esc_html( $asesor_p['name'] ); ?></strong><br>
                                        <span style="font-size: 11px; color: #64748b; font-family: monospace;"><?php echo esc_html( $asesor_p['no_reg'] ?? '-' ); ?></span>
                                    <?php else : ?>
                                        <span style="color: #dc2626; font-style: italic; font-size: 11.5px;">Belum Ditugaskan</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $p->id . '&doc=ak07' ) ); ?>" target="_blank" class="button button-primary" style="background: #0284c7; border-color: #0284c7; font-weight: 600; font-size: 12.5px; padding: 2px 14px;">
                                        🖨️ Cetak FR.AK.07
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p style="font-style: italic; color: #64748b; padding: 15px 0;">Belum ada peserta terdaftar pada jadwal sertifikasi ini.</p>
            <?php endif; ?>

            <!-- Informasi Standar BNSP FR.AK.07 -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px 18px; margin-top: 25px;">
                <h4 style="margin: 0 0 8px 0; color: #1e293b; font-size: 13.5px;">ℹ️ Cakupan Formulir Standar BNSP FR.AK.07:</h4>
                <ul style="margin: 0; padding-left: 20px; font-size: 12.5px; color: #475569; line-height: 1.6;">
                    <li><strong>Panduan Asesor:</strong> Petunjuk identifikasi asesi dengan kebutuhan khusus (disabilitas, literasi, bahasa, atau kondisi lingkungan).</li>
                    <li><strong>Tabel Potensi Asesi:</strong> Checklist identifikasi kondisi spesifik asesi.</li>
                    <li><strong>8 Item Penyesuaian &amp; Kontekstualisasi:</strong> Verifikasi perubahan waktu, media, penerjemah, pendampingan, dan alat bantu.</li>
                    <li><strong>Keputusan &amp; Tanda Tangan:</strong> Pernyataan persetujuan penyesuaian yang beralasan yang ditandatangani oleh Asesi dan Asesor.</li>
                </ul>
            </div>
        </div>

    <!-- ======================================================== -->
    <!-- TAB 6: INSTRUMEN ASESMEN (FR.IA.01 - FR.IA.20)           -->
    <!-- ======================================================== -->
    <?php elseif ( 'ia' === $active_tab ) : ?>
        <div class="card" style="padding: 20px; max-width: 100%;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h2 style="margin: 0; font-size: 16px; color: #0f172a;">🛠️ Instrumen Asesmen (No. 10 s/d 20 &bull; FR.IA)</h2>
                    <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">
                        Koleksi instrumen asesmen langsung, tidak langsung, dan tambahan sesuai portofolio BNSP.
                    </p>
                </div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-kelompok-pekerjaan' ) ); ?>" class="button">⚙️ Kelompok (IA.01)</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia02' ) ); ?>" class="button">🛠️ Praktik (IA.02)</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia03' ) ); ?>" class="button">❓ Pertanyaan (IA.03)</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia04' ) ); ?>" class="button">📝 Materi (IA.04)</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia05' ) ); ?>" class="button">✍️ Soal PG (IA.05)</a>
                </div>
            </div>

            <!-- Panduan Pengisian Konten FR.IA -->
            <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px; padding: 14px 18px; margin-bottom: 20px;">
                <div style="font-weight: 700; color: #166534; font-size: 13.5px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                    <span>📍</span>
                    <span>Lokasi Menu Input Isi / Konten Formulir FR.IA:</span>
                </div>
                <div style="font-size: 12.5px; color: #334155; line-height: 1.8;">
                    <div>• <strong>FR.IA.01 (Ceklis Observasi):</strong> Diisi dari pemetaan <strong>Kelompok Pekerjaan &amp; KUK</strong> di tab MAPA 1 atau menu <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-kelompok-pekerjaan' ) ); ?>" style="font-weight: 600;">Kelompok Pekerjaan &rarr;</a></div>
                    <div>• <strong>FR.IA.02 (Tugas Praktik Demonstrasi):</strong> Input manual skenario, langkah kerja, hasil, dan alat/perlengkapan di menu <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia02' ) ); ?>" class="button button-small" style="margin-left: 4px; font-weight: 600;">🛠️ Input Tugas Praktik IA.02 &rarr;</a></div>
                    <div>• <strong>FR.IA.03 (Pertanyaan Mendukung Observasi):</strong> Input manual daftar pertanyaan dimensi kompetensi (TS, TMS, CMS, JRES, TRS) &amp; kunci jawaban di menu <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia03' ) ); ?>" class="button button-small" style="margin-left: 4px; font-weight: 600;">❓ Input Pertanyaan IA.03 &rarr;</a></div>
                    <div>• <strong>FR.IA.04A &amp; 04B (Proyek Singkat Terstruktur / DIT):</strong> Input skenario studi kasus STAR &amp; rubrik penilaian di menu <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia04' ) ); ?>" class="button button-small" style="margin-left: 4px; font-weight: 600;">📝 Input Materi FR.IA.04 &rarr;</a></div>
                    <div>• <strong>FR.IA.05A, 05B, 05C (Soal Ujian &amp; Lembar Jawaban PG):</strong> Input bank soal pilihan ganda &amp; kunci jawaban di menu <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-ia05' ) ); ?>" class="button button-small" style="margin-left: 4px; font-weight: 600;">✍️ Input Soal FR.IA.05 &rarr;</a></div>
                </div>
            </div>

            <!-- Selector Jadwal & Asesi -->
            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; background: #f8fafc; padding: 12px 16px; border-radius: 6px; border: 1px solid #e2e8f0;">
                <input type="hidden" name="page" value="lsp-cetak-dokumen">
                <input type="hidden" name="tab" value="ia">

                <div>
                    <label for="kegiatan_ia" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 4px;">🎯 Jadwal Sertifikasi:</label>
                    <select name="kegiatan_id" id="kegiatan_ia" style="min-width: 300px; padding: 5px 10px;" onchange="this.form.submit();">
                        <?php if ( empty( $all_kegiatan ) ) : ?>
                            <option value="">-- Belum ada jadwal tersedia --</option>
                        <?php else : ?>
                            <?php foreach ( $all_kegiatan as $kg ) : ?>
                                <option value="<?php echo esc_attr( $kg->id ); ?>" <?php selected( $selected_kegiatan_id, $kg->id ); ?>>
                                    <?php echo esc_html( $kg->judul_kegiatan ); ?> (<?php echo esc_html( $kg->kode_kegiatan ); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label for="pendaftaran_ia" style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 4px;">👤 Pilih Asesi (Peserta):</label>
                    <select name="pendaftaran_id" id="pendaftaran_ia" style="min-width: 280px; padding: 5px 10px;" onchange="this.form.submit();">
                        <?php if ( empty( $peserta_list ) ) : ?>
                            <option value="0">-- Belum ada peserta terdaftar --</option>
                        <?php else : ?>
                            <?php foreach ( $peserta_list as $ps ) : ?>
                                <option value="<?php echo esc_attr( $ps->id ); ?>" <?php selected( $selected_pendaftaran_id, $ps->id ); ?>>
                                    👤 <?php echo esc_html( $ps->nama_lengkap ); ?> (NIK: <?php echo esc_html( $ps->nik ?? '-' ); ?>)
                                </option>
                            <?php endforeach; ?>
                            <option value="0" <?php selected( $selected_pendaftaran_id, 0 ); ?>>-- 📝 Form Blank (Tanpa Nama Asesi) --</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div style="align-self: flex-end;">
                    <button type="submit" class="button">Terapkan Filter</button>
                </div>
            </form>

            <?php 
            $base_ia_url = ! empty( $selected_pendaftaran_id )
                ? admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $selected_pendaftaran_id )
                : ( ! empty( $selected_kegiatan_id ) ? admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $selected_kegiatan_id ) : '' );
            
            if ( $selected_pendaftaran ) : 
                $asesor_p = ! empty( $selected_pendaftaran->asesor_id ) ? LSP_Cert_Integration::get_asesor( $selected_pendaftaran->asesor_id ) : null;
            ?>
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 10px 14px; margin-bottom: 14px; font-size: 13px; color: #1e40af; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                    <div>
                        <strong>Dokumen Dicetak Khusus Untuk Asesi:</strong> <?php echo esc_html( $selected_pendaftaran->nama_lengkap ); ?> 
                        &bull; <strong>Asesor Penguji:</strong> <?php echo esc_html( $asesor_p['name'] ?? 'Belum Ditugaskan' ); ?>
                    </div>
                    <div>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $selected_pendaftaran->id . '&doc=bundle_all' ) ); ?>" target="_blank" class="button button-small button-primary" style="background: #0284c7; border-color: #0284c7;">
                            📦 Cetak Paket Lengkap Asesi Ini (1-25)
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <table class="widefat striped" style="margin-top: 10px;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="width: 5%;">No.</th>
                        <th style="width: 15%;">Kode Dokumen</th>
                        <th style="width: 40%;">Nama Instrumen Asesmen</th>
                        <th style="width: 15%;">Status Sistem</th>
                        <th style="width: 25%; text-align: center;">Aksi Cetak Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- 10. FR.IA.01 -->
                    <tr>
                        <td><strong>10</strong></td>
                        <td><code style="font-weight: bold; color: #0284c7;">FR.IA.01</code></td>
                        <td>
                            <strong>CL &bull; Ceklis Observasi Aktivitas di Tempat Kerja / Tempat Kerja Simulasi</strong>
                            <div style="font-size: 12px; color: #64748b;">Observasi KUK berbasis 4 kolom kelompok pekerjaan</div>
                        </td>
                        <td><span class="badge" style="background:#dcfce7; color:#15803d; padding:3px 8px; border-radius:4px; font-weight:600; font-size:11px;">✅ Siap Cetak</span></td>
                        <td style="text-align: center;">
                            <?php if ( ! empty( $base_ia_url ) ) : ?>
                                <a href="<?php echo esc_url( $base_ia_url . '&doc=ia01' ); ?>" target="_blank" class="button button-small button-primary">🖨️ Cetak FR.IA.01</a>
                            <?php else : ?>—<?php endif; ?>
                        </td>
                    </tr>
                    <!-- 11. FR.IA.02 -->
                    <tr>
                        <td><strong>11</strong></td>
                        <td><code style="font-weight: bold; color: #0284c7;">FR.IA.02</code></td>
                        <td>
                            <strong>TPD &bull; Tugas Praktik Demonstrasi</strong>
                            <div style="font-size: 12px; color: #64748b;">Instruksi tugas demonstrasi dan skenario kerja</div>
                        </td>
                        <td><span class="badge" style="background:#dcfce7; color:#15803d; padding:3px 8px; border-radius:4px; font-weight:600; font-size:11px;">✅ Siap Cetak</span></td>
                        <td style="text-align: center;">
                            <?php if ( ! empty( $base_ia_url ) ) : ?>
                                <a href="<?php echo esc_url( $base_ia_url . '&doc=ia02' ); ?>" target="_blank" class="button button-small button-primary">🖨️ Cetak FR.IA.02</a>
                            <?php else : ?>—<?php endif; ?>
                        </td>
                    </tr>
                    <!-- 12. FR.IA.03 -->
                    <tr>
                        <td><strong>12</strong></td>
                        <td><code style="font-weight: bold; color: #0284c7;">FR.IA.03</code></td>
                        <td>
                            <strong>PMO &bull; Pertanyaan Untuk Mendukung Observasi</strong>
                            <div style="font-size: 12px; color: #64748b;">Pertanyaan lisan pendukung observasi &amp; tanggapan memuaskan</div>
                        </td>
                        <td><span class="badge" style="background:#dcfce7; color:#15803d; padding:3px 8px; border-radius:4px; font-weight:600; font-size:11px;">✅ Siap Cetak</span></td>
                        <td style="text-align: center;">
                            <?php if ( ! empty( $base_ia_url ) ) : ?>
                                <a href="<?php echo esc_url( $base_ia_url . '&doc=ia03' ); ?>" target="_blank" class="button button-small button-primary">🖨️ Cetak FR.IA.03</a>
                            <?php else : ?>—<?php endif; ?>
                        </td>
                    </tr>
                    <!-- 13. FR.IA.04 -->
                    <tr>
                        <td rowspan="2"><strong>13</strong></td>
                        <td><code style="font-weight: bold; color: #0284c7;">FR.IA.04A</code></td>
                        <td>
                            <strong>DIT &bull; Penjelasan Proyek Singkat / Kegiatan Terstruktur (STAR)</strong>
                            <div style="font-size: 12px; color: #64748b;">Instruksi tugas terstruktur asesi berbasis Situation-Task-Action-Result</div>
                        </td>
                        <td><span class="badge" style="background:#dcfce7; color:#15803d; padding:3px 8px; border-radius:4px; font-weight:600; font-size:11px;">✅ Siap Cetak</span></td>
                        <td style="text-align: center;">
                            <?php if ( ! empty( $base_ia_url ) ) : ?>
                                <a href="<?php echo esc_url( $base_ia_url . '&doc=ia04a' ); ?>" target="_blank" class="button button-small">🖨️ Cetak IA.04A</a>
                            <?php else : ?>—<?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code style="font-weight: bold; color: #0284c7;">FR.IA.04B</code></td>
                        <td>
                            <strong>DIT &bull; Penilaian Proyek Singkat atau Kegiatan Terstruktur Lainnya</strong>
                            <div style="font-size: 12px; color: #64748b;">Lembar evaluasi indikator bukti kegiatan terstruktur oleh asesor</div>
                        </td>
                        <td><span class="badge" style="background:#dcfce7; color:#15803d; padding:3px 8px; border-radius:4px; font-weight:600; font-size:11px;">✅ Siap Cetak</span></td>
                        <td style="text-align: center;">
                            <?php if ( ! empty( $base_ia_url ) ) : ?>
                                <a href="<?php echo esc_url( $base_ia_url . '&doc=ia04b' ); ?>" target="_blank" class="button button-small">🖨️ Cetak IA.04B</a>
                            <?php else : ?>—<?php endif; ?>
                        </td>
                    </tr>
                    <!-- 14. FR.IA.05 -->
                    <tr>
                        <td rowspan="3"><strong>14</strong></td>
                        <td><code style="font-weight: bold; color: #0284c7;">FR.IA.05A</code></td>
                        <td>
                            <strong>DPT &bull; Pertanyaan Tertulis Pilihan Ganda</strong>
                            <div style="font-size: 12px; color: #64748b;">Naskah soal ujian pilihan ganda untuk asesi</div>
                        </td>
                        <td><span class="badge" style="background:#dcfce7; color:#15803d; padding:3px 8px; border-radius:4px; font-weight:600; font-size:11px;">✅ Siap Cetak</span></td>
                        <td style="text-align: center;">
                            <?php if ( ! empty( $base_ia_url ) ) : ?>
                                <a href="<?php echo esc_url( $base_ia_url . '&doc=ia05a' ); ?>" target="_blank" class="button button-small">🖨️ Cetak Soal PG</a>
                            <?php else : ?>—<?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code style="font-weight: bold; color: #dc2626;">FR.IA.05B</code></td>
                        <td>
                            <strong>Lembar Kunci Jawaban Pilihan Ganda (Khusus Asesor)</strong>
                            <div style="font-size: 12px; color: #dc2626;">Dokumen rahasia kunci jawaban DPT</div>
                        </td>
                        <td><span class="badge" style="background:#dcfce7; color:#15803d; padding:3px 8px; border-radius:4px; font-weight:600; font-size:11px;">✅ Siap Cetak</span></td>
                        <td style="text-align: center;">
                            <?php if ( ! empty( $base_ia_url ) ) : ?>
                                <a href="<?php echo esc_url( $base_ia_url . '&doc=ia05b' ); ?>" target="_blank" class="button button-small" style="color: #dc2626;">🔒 Cetak Kunci PG</a>
                            <?php else : ?>—<?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><code style="font-weight: bold; color: #16a34a;">FR.IA.05C</code></td>
                        <td>
                            <strong>Lembar Jawaban Pilihan Ganda</strong>
                            <div style="font-size: 12px; color: #64748b;">Lembar jawaban kosong untuk diisi asesi saat uji tertulis</div>
                        </td>
                        <td><span class="badge" style="background:#dcfce7; color:#15803d; padding:3px 8px; border-radius:4px; font-weight:600; font-size:11px;">✅ Siap Cetak</span></td>
                        <td style="text-align: center;">
                            <?php if ( ! empty( $base_ia_url ) ) : ?>
                                <a href="<?php echo esc_url( $base_ia_url . '&doc=ia05c' ); ?>" target="_blank" class="button button-small">✍️ Cetak Lembar Jawab</a>
                            <?php else : ?>—<?php endif; ?>
                        </td>
                    </tr>
                    <!-- 15. FR.IA.06 s/d 20. FR.IA.11 -->
                    <tr>
                        <td rowspan="3"><strong>15</strong></td>
                        <td><code>FR.IA.06A</code></td>
                        <td><strong>DPT &bull; Pertanyaan Tertulis Esai</strong></td>
                        <td><span class="badge" style="background:#f1f5f9; color:#64748b; padding:3px 8px; border-radius:4px; font-size:11px;">⏳ Belum Dibuat</span></td>
                        <td style="text-align: center; color:#94a3b8;">Tahap Selanjutnya</td>
                    </tr>
                    <tr>
                        <td><code>FR.IA.06B</code></td>
                        <td><strong>Lembar Kunci Jawaban Pertanyaan Tertulis Esai</strong></td>
                        <td><span class="badge" style="background:#f1f5f9; color:#64748b; padding:3px 8px; border-radius:4px; font-size:11px;">⏳ Belum Dibuat</span></td>
                        <td style="text-align: center; color:#94a3b8;">Tahap Selanjutnya</td>
                    </tr>
                    <tr>
                        <td><code>FR.IA.06C</code></td>
                        <td><strong>Lembar Jawab Pertanyaan Esai</strong></td>
                        <td><span class="badge" style="background:#f1f5f9; color:#64748b; padding:3px 8px; border-radius:4px; font-size:11px;">⏳ Belum Dibuat</span></td>
                        <td style="text-align: center; color:#94a3b8;">Tahap Selanjutnya</td>
                    </tr>
                    <tr>
                        <td><strong>16</strong></td>
                        <td><code>FR.IA.07</code></td>
                        <td><strong>DPL &bull; Pertanyaan Lisan</strong></td>
                        <td><span class="badge" style="background:#f1f5f9; color:#64748b; padding:3px 8px; border-radius:4px; font-size:11px;">⏳ Belum Dibuat</span></td>
                        <td style="text-align: center; color:#94a3b8;">Tahap Selanjutnya</td>
                    </tr>
                    <tr>
                        <td><strong>17</strong></td>
                        <td><code>FR.IA.08</code></td>
                        <td><strong>CVP &bull; Ceklis Verifikasi Portofolio</strong></td>
                        <td><span class="badge" style="background:#f1f5f9; color:#64748b; padding:3px 8px; border-radius:4px; font-size:11px;">⏳ Belum Dibuat</span></td>
                        <td style="text-align: center; color:#94a3b8;">Tahap Selanjutnya</td>
                    </tr>
                    <tr>
                        <td><strong>18</strong></td>
                        <td><code>FR.IA.09</code></td>
                        <td><strong>PW &bull; Pertanyaan Wawancara</strong></td>
                        <td><span class="badge" style="background:#f1f5f9; color:#64748b; padding:3px 8px; border-radius:4px; font-size:11px;">⏳ Belum Dibuat</span></td>
                        <td style="text-align: center; color:#94a3b8;">Tahap Selanjutnya</td>
                    </tr>
                    <tr>
                        <td><strong>19</strong></td>
                        <td><code>FR.IA.10</code></td>
                        <td><strong>VPK &bull; Verifikasi Pihak Ketiga</strong></td>
                        <td><span class="badge" style="background:#f1f5f9; color:#64748b; padding:3px 8px; border-radius:4px; font-size:11px;">⏳ Belum Dibuat</span></td>
                        <td style="text-align: center; color:#94a3b8;">Tahap Selanjutnya</td>
                    </tr>
                    <tr>
                        <td><strong>20</strong></td>
                        <td><code>FR.IA.11</code></td>
                        <td><strong>CRP &bull; Ceklis Reviu Produk</strong></td>
                        <td><span class="badge" style="background:#f1f5f9; color:#64748b; padding:3px 8px; border-radius:4px; font-size:11px;">⏳ Belum Dibuat</span></td>
                        <td style="text-align: center; color:#94a3b8;">Tahap Selanjutnya</td>
                    </tr>
                </tbody>
            </table>
        </div>

    <!-- ======================================================== -->
    <!-- TAB 7: B. KEPUTUSAN ASESMEN (FR.AK.02 & FR.AK.03)        -->
    <!-- ======================================================== -->
    <?php elseif ( 'keputusan' === $active_tab ) : ?>
        <div class="card" style="padding: 20px; max-width: 100%;">
            <h2 style="margin: 0 0 10px 0; font-size: 16px; color: #0f172a;">⚖️ B. Keputusan Asesmen (FR.AK.02 &amp; FR.AK.03)</h2>
            <p style="margin: 0 0 18px 0; color: #64748b; font-size: 13px;">
                Dokumen rekaman asesmen kompetensi dan pencatatan umpan balik per asesi.
            </p>

            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px; flex-wrap: wrap;">
                <input type="hidden" name="page" value="lsp-cetak-dokumen">
                <input type="hidden" name="tab" value="keputusan">

                <label for="kegiatan_keputusan" style="font-weight: 600; font-size: 13px;">Jadwal Sertifikasi:</label>
                <select name="kegiatan_id" id="kegiatan_keputusan" style="min-width: 350px; padding: 5px 10px;" onchange="this.form.submit();">
                    <?php if ( empty( $all_kegiatan ) ) : ?>
                        <option value="">-- Belum ada jadwal tersedia --</option>
                    <?php else : ?>
                        <?php foreach ( $all_kegiatan as $kg ) : ?>
                            <option value="<?php echo esc_attr( $kg->id ); ?>" <?php selected( $selected_kegiatan_id, $kg->id ); ?>>
                                <?php echo esc_html( $kg->judul_kegiatan ); ?> (<?php echo esc_html( $kg->kode_kegiatan ); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </form>

            <?php if ( ! empty( $peserta_list ) ) : ?>
                <table class="widefat striped" style="margin-top: 10px;">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No.</th>
                            <th style="width: 30%;">Nama Asesi</th>
                            <th style="width: 25%;">Asesor Penugasan</th>
                            <th style="width: 20%; text-align: center;">21. FR.AK.02</th>
                            <th style="width: 20%; text-align: center;">22. FR.AK.03</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $peserta_list as $p_idx => $p ) : 
                            $asesor_p = ! empty( $p->asesor_id ) ? LSP_Cert_Integration::get_asesor( $p->asesor_id ) : null;
                        ?>
                            <tr>
                                <td><?php echo ( $p_idx + 1 ); ?></td>
                                <td><strong><?php echo esc_html( $p->nama_lengkap ); ?></strong></td>
                                <td><?php echo esc_html( $asesor_p['name'] ?? 'Belum Ditugaskan' ); ?></td>
                                <td style="text-align: center;">
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $p->id . '&doc=ak02' ) ); ?>" target="_blank" class="button button-small button-primary">
                                        🖨️ Cetak FR.AK.02
                                    </a>
                                </td>
                                <td style="text-align: center;">
                                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $p->id . '&doc=ak03' ) ); ?>" target="_blank" class="button button-small button-primary">
                                        💬 Cetak FR.AK.03
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p style="font-style: italic; color: #64748b;">Belum ada peserta terdaftar pada jadwal ini.</p>
            <?php endif; ?>
        </div>

    <!-- ======================================================== -->
    <!-- TAB 8: C. LAPORAN & D. VALIDASI (FR.AK.05, 06 & FR.VA)   -->
    <!-- ======================================================== -->
    <?php elseif ( 'laporan' === $active_tab ) : ?>
        <div class="card" style="padding: 20px; max-width: 100%;">
            <h2 style="margin: 0 0 10px 0; font-size: 16px; color: #0f172a;">📑 C. Laporan Asesmen &amp; D. Validasi (No. 23 s/d 25)</h2>
            <p style="margin: 0 0 18px 0; color: #64748b; font-size: 13px;">
                Dokumen rekapitulasi akhir pelaksanaan sertifikasi tingkat kegiatan / jadwal.
            </p>

            <form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px; flex-wrap: wrap;">
                <input type="hidden" name="page" value="lsp-cetak-dokumen">
                <input type="hidden" name="tab" value="laporan">

                <label for="kegiatan_laporan" style="font-weight: 600; font-size: 13px;">Jadwal Sertifikasi:</label>
                <select name="kegiatan_id" id="kegiatan_laporan" style="min-width: 350px; padding: 5px 10px;" onchange="this.form.submit();">
                    <?php if ( empty( $all_kegiatan ) ) : ?>
                        <option value="">-- Belum ada jadwal tersedia --</option>
                    <?php else : ?>
                        <?php foreach ( $all_kegiatan as $kg ) : ?>
                            <option value="<?php echo esc_attr( $kg->id ); ?>" <?php selected( $selected_kegiatan_id, $kg->id ); ?>>
                                <?php echo esc_html( $kg->judul_kegiatan ); ?> (<?php echo esc_html( $kg->kode_kegiatan ); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </form>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px;">
                <!-- 23. FR.AK.05 -->
                <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 18px; background: #ffffff;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <span class="badge" style="background:#0284c7; color:#fff; font-size:11px; padding:2px 6px; border-radius:3px; font-weight:bold;">No. 23</span>
                            <h3 style="margin: 8px 0 4px 0; font-size: 15px; color: #0f172a;">FR.AK.05 Laporan Asesmen</h3>
                        </div>
                        <span class="badge" style="background:#dcfce7; color:#15803d; font-size:11px; padding:3px 6px; border-radius:3px; font-weight:bold;">✅ Siap Cetak</span>
                    </div>
                    <p style="font-size: 12.5px; color: #64748b; line-height: 1.5; margin: 8px 0 16px 0;">
                        Laporan hasil rekomendasi asesmen seluruh peserta per kegiatan. Dibagi rapi maksimal 10 peserta per halaman cetak.
                    </p>
                    <?php if ( ! empty( $selected_kegiatan_id ) ) : ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $selected_kegiatan_id . '&doc=ak05' ) ); ?>" target="_blank" class="button button-primary" style="width: 100%; text-align: center;">
                            🖨️ Cetak FR.AK.05 (Laporan Asesmen)
                        </a>
                    <?php else : ?>
                        <button class="button" disabled style="width: 100%;">Pilih Jadwal Dahulu</button>
                    <?php endif; ?>
                </div>

                <!-- 24. FR.AK.06 -->
                <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 18px; background: #ffffff;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <span class="badge" style="background:#0284c7; color:#fff; font-size:11px; padding:2px 6px; border-radius:3px; font-weight:bold;">No. 24</span>
                            <h3 style="margin: 8px 0 4px 0; font-size: 15px; color: #0f172a;">FR.AK.06 Meninjau Proses Asesmen</h3>
                        </div>
                        <span class="badge" style="background:#dcfce7; color:#15803d; font-size:11px; padding:3px 6px; border-radius:3px; font-weight:bold;">✅ Siap Cetak</span>
                    </div>
                    <p style="font-size: 12.5px; color: #64748b; line-height: 1.5; margin: 8px 0 16px 0;">
                        Peninjauan dimensi kompetensi (TS, TMS, CMS, JRES, TRS) dan pemenuhan prinsip-prinsip asesmen dan aturan bukti.
                    </p>
                    <?php if ( ! empty( $selected_kegiatan_id ) ) : ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-sertifikasi&action=print&id=' . $selected_kegiatan_id . '&doc=ak06' ) ); ?>" target="_blank" class="button button-primary" style="width: 100%; text-align: center;">
                            🖨️ Cetak FR.AK.06 (Meninjau Proses)
                        </a>
                    <?php else : ?>
                        <button class="button" disabled style="width: 100%;">Pilih Jadwal Dahulu</button>
                    <?php endif; ?>
                </div>

                <!-- 25. FR.VA -->
                <div style="border: 1px dashed #cbd5e1; border-radius: 6px; padding: 18px; background: #f8fafc;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <span class="badge" style="background:#64748b; color:#fff; font-size:11px; padding:2px 6px; border-radius:3px; font-weight:bold;">No. 25</span>
                            <h3 style="margin: 8px 0 4px 0; font-size: 15px; color: #64748b;">FR.VA Validasi Asesmen</h3>
                        </div>
                        <span class="badge" style="background:#f1f5f9; color:#64748b; font-size:11px; padding:3px 6px; border-radius:3px;">⏳ Belum Dibuat</span>
                    </div>
                    <p style="font-size: 12.5px; color: #94a3b8; line-height: 1.5; margin: 8px 0 16px 0;">
                        Formulir Memberikan Kontribusi dalam Validasi Asesmen oleh tim validator / master asesor LSP.
                    </p>
                    <button class="button" disabled style="width: 100%; color: #94a3b8;">Tahap Pengembangan Selanjutnya</button>
                </div>
            </div>
        </div>

    <!-- ======================================================== -->
    <!-- TAB 9: ROADMAP & MATRIKS URUTAN LENGKAP BNSP (1 - 25)    -->
    <!-- ======================================================== -->
    <?php elseif ( 'roadmap' === $active_tab ) : ?>
        <div class="card" style="padding: 20px; max-width: 100%;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap;">
                <div>
                    <h2 style="margin: 0; font-size: 17px; color: #0f172a;">📊 Matriks Urutan Lengkap Dokumen Asesmen BNSP (1 s/d 25)</h2>
                    <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">
                        Status kelengkapan generator dokumen sistem LSP Certification Management sesuai standar BNSP.
                    </p>
                </div>
                <div style="display: flex; gap: 8px;">
                    <span style="font-size: 12px; background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 4px; font-weight: bold;">
                        ✅ 15 Formulir Selesai &amp; Siap Cetak
                    </span>
                    <span style="font-size: 12px; background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 4px; font-weight: bold;">
                        ⏳ 7 Formulir Belum Dibuat
                    </span>
                </div>
            </div>

            <table class="widefat striped" style="margin-top: 10px; font-size: 12.5px;">
                <thead>
                    <tr style="background: #0f172a; color: #ffffff;">
                        <th style="width: 5%; color:#fff;">No.</th>
                        <th style="width: 15%; color:#fff;">Kode BNSP</th>
                        <th style="width: 40%; color:#fff;">Nama Formulir Dokumen</th>
                        <th style="width: 15%; color:#fff;">Tingkat Cetak</th>
                        <th style="width: 12%; color:#fff;">Status</th>
                        <th style="width: 13%; color:#fff; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Bagian A: Perencanaan & Asesmen -->
                    <tr style="background: #e2e8f0; font-weight: bold;"><td colspan="6">A. TAHAP PERMOHONAN &amp; PERENCANAAN ASESMEN</td></tr>
                    <tr><td>1</td><td><code>FR.APL.01</code></td><td>Permohonan Sertifikasi Kompetensi</td><td>Peserta</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=apl' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>2</td><td><code>FR.APL.02</code></td><td>Asesmen Mandiri</td><td>Peserta</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=apl' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>3</td><td><code>FR.MAPA.01</code></td><td>Merencanakan Aktivitas dan Proses Asesmen (Obs, DIT, Portofolio)</td><td>Jadwal</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=mapa1' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>4</td><td><code>FR.MAPA.02</code></td><td>Peta Instrumen Asesmen</td><td>Jadwal</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=mapa2' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>5</td><td><code>FR.AK.01</code></td><td>Persetujuan Asesmen dan Kerahasiaan</td><td>Peserta</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=ak01' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>6</td><td><code>FR.AK.04</code></td><td>Formulir Banding Asesmen (Blank Form)</td><td>Jadwal</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=ak04' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>7</td><td><code>FR.AK.07</code></td><td>Ceklis Penyesuaian yang Wajar dan Beralasan</td><td>Peserta</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=ak07' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    
                    <!-- Bagian Instrumen: FR.IA -->
                    <tr style="background: #e2e8f0; font-weight: bold;"><td colspan="6">INSTRUMEN ASESMEN (FR.IA)</td></tr>
                    <tr><td>10</td><td><code>FR.IA.01</code></td><td>CL &bull; Ceklis Observasi Aktivitas Praktik / Simulasi (4 Kolom)</td><td>Jadwal / Peserta</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=ia' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>11</td><td><code>FR.IA.02</code></td><td>TPD &bull; Tugas Praktik Demonstrasi</td><td>Jadwal / Peserta</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=ia' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>12</td><td><code>FR.IA.03</code></td><td>PMO &bull; Pertanyaan Untuk Mendukung Observasi</td><td>Jadwal / Peserta</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=ia' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>13.a</td><td><code>FR.IA.04A</code></td><td>DIT &bull; Penjelasan Proyek Singkat / Kegiatan Terstruktur (STAR)</td><td>Jadwal</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=ia' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>13.b</td><td><code>FR.IA.04B</code></td><td>DIT &bull; Penilaian Proyek Singkat / Kegiatan Terstruktur</td><td>Jadwal</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=ia' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>14.a</td><td><code>FR.IA.05A</code></td><td>DPT &bull; Pertanyaan Tertulis Pilihan Ganda (Naskah Soal)</td><td>Jadwal</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=ia' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>14.b</td><td><code>FR.IA.05B</code></td><td>DPT &bull; Lembar Kunci Jawaban Pilihan Ganda (Rahasia)</td><td>Jadwal</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=ia' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>14.c</td><td><code>FR.IA.05C</code></td><td>DPT &bull; Lembar Jawaban Pilihan Ganda Asesi</td><td>Jadwal / Peserta</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=ia' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>15</td><td><code>FR.IA.06</code></td><td>DPT &bull; Pertanyaan Tertulis Esai (Soal, Kunci, Lembar Jawab)</td><td>Jadwal</td><td><span style="color:#64748b;">⏳ Belum Dibuat</span></td><td style="text-align:center;">—</td></tr>
                    <tr><td>16</td><td><code>FR.IA.07</code></td><td>DPL &bull; Pertanyaan Lisan</td><td>Jadwal / Peserta</td><td><span style="color:#64748b;">⏳ Belum Dibuat</span></td><td style="text-align:center;">—</td></tr>
                    <tr><td>17</td><td><code>FR.IA.08</code></td><td>CVP &bull; Ceklis Verifikasi Portofolio</td><td>Peserta</td><td><span style="color:#64748b;">⏳ Belum Dibuat</span></td><td style="text-align:center;">—</td></tr>
                    <tr><td>18</td><td><code>FR.IA.09</code></td><td>PW &bull; Pertanyaan Wawancara</td><td>Peserta</td><td><span style="color:#64748b;">⏳ Belum Dibuat</span></td><td style="text-align:center;">—</td></tr>
                    <tr><td>19</td><td><code>FR.IA.10</code></td><td>VPK &bull; Verifikasi Pihak Ketiga</td><td>Peserta</td><td><span style="color:#64748b;">⏳ Belum Dibuat</span></td><td style="text-align:center;">—</td></tr>
                    <tr><td>20</td><td><code>FR.IA.11</code></td><td>CRP &bull; Ceklis Reviu Produk</td><td>Peserta</td><td><span style="color:#64748b;">⏳ Belum Dibuat</span></td><td style="text-align:center;">—</td></tr>

                    <!-- Bagian B: Keputusan -->
                    <tr style="background: #e2e8f0; font-weight: bold;"><td colspan="6">B. KEPUTUSAN ASESMEN</td></tr>
                    <tr><td>21</td><td><code>FR.AK.02</code></td><td>Rekaman Asesmen Kompetensi</td><td>Peserta</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=keputusan' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>22</td><td><code>FR.AK.03</code></td><td>Umpan Balik dan Catatan Asesmen</td><td>Peserta</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=keputusan' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>

                    <!-- Bagian C: Laporan -->
                    <tr style="background: #e2e8f0; font-weight: bold;"><td colspan="6">C. LAPORAN ASESMEN</td></tr>
                    <tr><td>23</td><td><code>FR.AK.05</code></td><td>Laporan Asesmen (Maks 10 Peserta/Halaman)</td><td>Jadwal</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=laporan' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>
                    <tr><td>24</td><td><code>FR.AK.06</code></td><td>Meninjau Proses Asesmen</td><td>Jadwal</td><td><span style="color:#15803d; font-weight:600;">✅ Siap Cetak</span></td><td style="text-align:center;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=laporan' ) ); ?>" class="button button-small">Buka Tab</a></td></tr>

                    <!-- Bagian D: Validasi -->
                    <tr style="background: #e2e8f0; font-weight: bold;"><td colspan="6">D. VALIDASI ASESMEN</td></tr>
                    <tr><td>25</td><td><code>FR.VA</code></td><td>Memberikan Kontribusi dalam Validasi Asesmen</td><td>Jadwal / Tim Validasi</td><td><span style="color:#64748b;">⏳ Belum Dibuat</span></td><td style="text-align:center;">—</td></tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- ======================================================== -->
    <!-- MODAL INTERAKTIF PILIH DOKUMEN ASESI                      -->
    <!-- ======================================================== -->
    <div id="modal-pilih-dokumen-peserta" style="display: none; position: fixed; z-index: 100000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(2px); align-items: center; justify-content: center;">
        <div style="background: #ffffff; width: 92%; max-width: 820px; max-height: 90vh; border-radius: 10px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3); display: flex; flex-direction: column; overflow: hidden;">
            <!-- Header Modal -->
            <div style="background: #0f172a; color: #ffffff; padding: 16px 22px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0; font-size: 16px; color: #ffffff; display: flex; align-items: center; gap: 8px;">
                        <span>🖨️ Cetak Berkas Asesi:</span>
                        <span id="modal-doc-nama-asesi" style="color: #38bdf8;">-</span>
                    </h3>
                    <div style="font-size: 11.5px; color: #94a3b8; margin-top: 4px;">
                        NIK: <span id="modal-doc-nik" style="color: #cbd5e1;">-</span> &bull; Asesor Penugasan: <span id="modal-doc-asesor" style="color: #6ee7b7; font-weight: 600;">-</span>
                    </div>
                </div>
                <button type="button" id="btn-close-modal-doc" style="background: transparent; border: none; color: #94a3b8; font-size: 26px; cursor: pointer; line-height: 1; padding: 0 6px;" title="Tutup Modal">&times;</button>
            </div>

            <!-- Body Modal (Scrollable) -->
            <div style="padding: 22px; overflow-y: auto; flex: 1;">
                <!-- Tombol Bundle All Utama -->
                <div style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); padding: 16px 20px; border-radius: 8px; margin-bottom: 22px; color: #ffffff; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(2, 132, 199, 0.3);">
                    <div>
                        <h4 style="margin: 0; font-size: 15px; font-weight: bold; color: #ffffff;">📦 Cetak Seluruh Berkas Asesi (1 s/d 25 All-in-One)</h4>
                        <p style="margin: 3px 0 0 0; font-size: 12px; color: #e0f2fe;">APL.01, APL.02, MAPA.01, MAPA.02, AK.01, AK.04, IA.01-IA.05C, AK.02, AK.03 lengkap terintegrasi.</p>
                    </div>
                    <a href="#" target="_blank" class="button button-primary btn-print-dynamic" data-doc="bundle_all" style="background: #ffffff; color: #0369a1; border: none; font-weight: 700; font-size: 13px; padding: 7px 18px; white-space: nowrap;">
                        🖨️ Cetak Paket Lengkap
                    </a>
                </div>

                <!-- Bagian 1: Permohonan & Perencanaan -->
                <div style="margin-bottom: 20px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 13.5px; font-weight: 700; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                        📋 1. Tahap Permohonan &amp; Perencanaan Asesmen
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 8px;">
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="all" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            📄 <strong>APL.01 &amp; APL.02</strong> (Sepaket)
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="apl01" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            📄 <strong>FR.APL.01</strong> Permohonan
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="apl02" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            📄 <strong>FR.APL.02</strong> Asesmen Mandiri
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="mapa01_obs" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            📋 <strong>FR.MAPA.01</strong> Observasi
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="mapa01_dit" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            📋 <strong>FR.MAPA.01</strong> DIT (STAR)
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="mapa01_vp" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            📋 <strong>FR.MAPA.01</strong> Portofolio
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="mapa02" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            🗺️ <strong>FR.MAPA.02</strong> Peta Instrumen
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="ak01" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            🔏 <strong>FR.AK.01</strong> Persetujuan
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="ak04" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            ⚖️ <strong>FR.AK.04</strong> Formulir Banding
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="ak07" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px; background: #fefce8; border-color: #fef08a;">
                            ⚖️ <strong>FR.AK.07</strong> Penyesuaian Wajar
                        </a>
                    </div>
                </div>

                <!-- Bagian 2: Instrumen FR.IA -->
                <div style="margin-bottom: 20px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 13.5px; font-weight: 700; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                        🛠️ 2. Instrumen Asesmen (FR.IA Standar BNSP)
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 8px;">
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="ia01" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            🛠️ <strong>FR.IA.01</strong> Ceklis Observasi (CL)
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="ia02" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            🛠️ <strong>FR.IA.02</strong> Tugas Praktik (TPD)
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="ia03" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            🛠️ <strong>FR.IA.03</strong> Pertanyaan Obs (PMO)
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="ia04a" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            📝 <strong>FR.IA.04A</strong> Soal Proyek DIT
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="ia04b" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            📝 <strong>FR.IA.04B</strong> Penilaian DIT
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="ia05a" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            ✍️ <strong>FR.IA.05A</strong> Soal Pilihan Ganda (DPT)
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="ia05c" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            ✍️ <strong>FR.IA.05C</strong> Lembar Jawab PG
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="ia05b" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px; color: #dc2626;">
                            🔒 <strong>FR.IA.05B</strong> Kunci Jawaban PG
                        </a>
                    </div>
                </div>

                <!-- Bagian 3: Keputusan Asesmen FR.AK -->
                <div>
                    <h4 style="margin: 0 0 10px 0; font-size: 13.5px; font-weight: 700; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px;">
                        ⚖️ 3. Rekaman &amp; Keputusan Asesmen (FR.AK)
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 8px;">
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="ak02" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            ⚖️ <strong>FR.AK.02</strong> Rekaman Asesmen
                        </a>
                        <a href="#" target="_blank" class="button btn-print-dynamic" data-doc="ak03" style="text-align: left; font-size: 12px; height: auto; padding: 6px 12px;">
                            💬 <strong>FR.AK.03</strong> Umpan Balik Asesmen
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 12px 22px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 12px; color: #64748b;">
                    💡 Seluruh formulir otomatis menyertakan Nama Asesi, NIK, dan Asesor Penugasan.
                </span>
                <button type="button" class="button button-secondary" id="btn-cancel-modal-doc">Tutup</button>
            </div>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var $modal = $('#modal-pilih-dokumen-peserta');
        var basePrintUrl = '<?php echo esc_js( admin_url( 'admin.php?page=lsp-pendaftaran&action=print' ) ); ?>';

        $(document).on('click', '.btn-modal-pilih-dokumen', function(e) {
            e.preventDefault();
            var id     = $(this).data('id');
            var nama   = $(this).data('nama');
            var nik    = $(this).data('nik');
            var asesor = $(this).data('asesor');

            $('#modal-doc-nama-asesi').text(nama);
            $('#modal-doc-nik').text(nik);
            $('#modal-doc-asesor').text(asesor);

            $modal.find('.btn-print-dynamic').each(function() {
                var doc = $(this).data('doc');
                $(this).attr('href', basePrintUrl + '&id=' + id + '&doc=' + doc);
            });

            $modal.css('display', 'flex');
        });

        $('#btn-close-modal-doc, #btn-cancel-modal-doc').on('click', function() {
            $modal.hide();
        });

        $(window).on('click', function(e) {
            if ($(e.target).is($modal)) {
                $modal.hide();
            }
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $modal.is(':visible')) {
                $modal.hide();
            }
        });
    });
    </script>

</div>
