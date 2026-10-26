<div class="wrap">
    <h1 style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
        <span>
            Verifikasi Pendaftaran: <strong><?php echo esc_html( $pendaftaran->nama_lengkap ); ?></strong>
            <span style="font-size: 14px; font-weight: normal; color: #64748b; margin-left: 8px;">(ID #<?php echo $pendaftaran->id; ?>)</span>
        </span>
        <div>
            <a href="<?php echo admin_url( 'admin.php?page=lsp-pendaftaran' ); ?>" class="page-title-action">&larr; Kembali ke Daftar</a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=apl&kegiatan_id=' . $pendaftaran->kegiatan_id ) ); ?>" class="page-title-action" style="color: #0284c7; border-color: #0284c7; font-weight: 600;">
                🖨️ Cetak Dokumen di Menu Cetak
            </a>
        </div>
    </h1>

    <!-- Notice Tautan Cetak Terpusat -->
    <div class="notice notice-info inline" style="margin: 0 0 20px 0; border-left-color: #0284c7; display: flex; align-items: center; justify-content: space-between;">
        <p style="margin: 8px 0; font-size: 13px;">
            ℹ️ Halaman ini difokuskan untuk <strong>Verifikasi Dokumen APL.01</strong> dan <strong>Validasi Asesmen Mandiri APL.02</strong>. Untuk mencetak seluruh berkas fisik standar BNSP (APL, MAPA, AK, dan IA), silakan gunakan menu <strong><a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen' ) ); ?>">Cetak Dokumen</a></strong> di sidebar kiri.
        </p>
    </div>

    <!-- Ringkasan Status Pendaftaran -->
    <?php
    $status_kel_labels = array(
        'pending'       => array( 'label' => 'Menunggu Verifikasi', 'bg' => '#fef3c7', 'color' => '#b45309', 'icon' => '⏳' ),
        'lengkap'       => array( 'label' => 'Memenuhi Syarat (Lengkap)', 'bg' => '#dcfce7', 'color' => '#15803d', 'icon' => '✓' ),
        'tidak_lengkap' => array( 'label' => 'Belum Memenuhi Syarat', 'bg' => '#fee2e2', 'color' => '#b91c1c', 'icon' => '✕' ),
    );
    $curr_kel = $status_kel_labels[ $pendaftaran->status_kelengkapan ] ?? $status_kel_labels['pending'];

    $status_rek_labels = array(
        'pending'   => array( 'label' => 'Menunggu Keputusan', 'bg' => '#fef3c7', 'color' => '#b45309', 'icon' => '⏳' ),
        'disetujui' => array( 'label' => 'Disetujui — Lanjut Asesmen', 'bg' => '#dcfce7', 'color' => '#15803d', 'icon' => '✓' ),
        'ditolak'   => array( 'label' => 'Ditolak', 'bg' => '#fee2e2', 'color' => '#b91c1c', 'icon' => '✕' ),
    );
    $curr_rek = $status_rek_labels[ $pendaftaran->status_rekomendasi ] ?? $status_rek_labels['pending'];

    // Hitung status jawaban APL 02
    $total_k = 0;
    $total_bk = 0;
    if ( ! empty( $apl02_jawaban ) ) {
        foreach ( $apl02_jawaban as $j ) {
            if ( strtoupper( trim( $j->jawaban ) ) === 'K' ) {
                $total_k++;
            } else {
                $total_bk++;
            }
        }
    }
    $total_elemen = $total_k + $total_bk;

    // Asesor terpilih
    $current_asesor_name = '-';
    if ( ! empty( $pendaftaran->asesor_id ) ) {
        $a_info = LSP_Cert_Integration::get_asesor( $pendaftaran->asesor_id );
        if ( $a_info ) {
            $current_asesor_name = $a_info['name'] . ( $a_info['no_reg'] ? ' (' . $a_info['no_reg'] . ')' : '' );
        }
    } elseif ( ! empty( $pendaftaran->penyusun_nama ) ) {
        $current_asesor_name = $pendaftaran->penyusun_nama . ( $pendaftaran->penyusun_met ? ' (' . $pendaftaran->penyusun_met . ')' : '' );
    }

    // Ambil daftar asesor yang ditugaskan pada kegiatan ini
    $kegiatan_asesors = array();
    if ( ! empty( $kegiatan->asesor_ids ) ) {
        $k_a_ids = json_decode( $kegiatan->asesor_ids, true );
        if ( is_array( $k_a_ids ) ) {
            foreach ( $k_a_ids as $aid ) {
                $a_obj = LSP_Cert_Integration::get_asesor( $aid );
                if ( $a_obj ) {
                    $kegiatan_asesors[] = $a_obj;
                }
            }
        }
    }

    // Cek status sertifikat kompetensi untuk asesi ini
    $sertifikat_info = LSP_Cert_Database::get_sertifikat_by_pendaftaran( $pendaftaran->id, $pendaftaran->nama_lengkap );
    ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <!-- Card 1: Kelengkapan Berkas APL 01 -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Dokumen APL.01</div>
            <div style="display: inline-flex; align-items: center; gap: 6px; margin-top: 8px; padding: 4px 10px; border-radius: 20px; font-size: 13px; font-weight: 600; background: <?php echo $curr_kel['bg']; ?>; color: <?php echo $curr_kel['color']; ?>;">
                <span><?php echo $curr_kel['icon']; ?></span>
                <span><?php echo esc_html( $curr_kel['label'] ); ?></span>
            </div>
        </div>

        <!-- Card 2: Validasi Asesmen Mandiri APL 02 -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Asesmen Mandiri APL.02</div>
            <div style="margin-top: 8px;">
                <?php if ( $total_elemen > 0 ) : ?>
                    <?php if ( $total_bk === 0 ) : ?>
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 13px; font-weight: 600; background: #dcfce7; color: #15803d;">
                            ✓ <?php echo $total_k; ?> Elemen Dinilai K (Kompeten)
                        </span>
                    <?php else : ?>
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 13px; font-weight: 600; background: #fee2e2; color: #b91c1c;">
                            ⚠️ <?php echo $total_bk; ?> Elemen BK dari <?php echo $total_elemen; ?>
                        </span>
                    <?php endif; ?>
                <?php else : ?>
                    <span style="color: #64748b; font-size: 13px; font-style: italic;">Belum ada jawaban</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card 3: Status Rekomendasi -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Rekomendasi Asesmen</div>
            <div style="display: inline-flex; align-items: center; gap: 6px; margin-top: 8px; padding: 4px 10px; border-radius: 20px; font-size: 13px; font-weight: 600; background: <?php echo $curr_rek['bg']; ?>; color: <?php echo $curr_rek['color']; ?>;">
                <span><?php echo $curr_rek['icon']; ?></span>
                <span><?php echo esc_html( $curr_rek['label'] ); ?></span>
            </div>
        </div>

        <!-- Card 4: Asesor Penugasan -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Asesor Ditugaskan</div>
            <div style="margin-top: 8px; font-size: 13px; font-weight: 600; color: #1e293b;">
                👨‍💼 <?php echo esc_html( $current_asesor_name ); ?>
            </div>
        </div>

        <!-- Card 5: Status Sertifikat BNSP -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Sertifikat BNSP</div>
            <div style="margin-top: 8px;">
                <?php if ( $sertifikat_info ) : ?>
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #dcfce7; color: #15803d;">
                        🏅 <?php echo esc_html( $sertifikat_info['kode_blanko'] ); ?>
                    </span>
                <?php else : ?>
                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #fef3c7; color: #92400e;">
                        ⏳ Belum Terbit
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Layout 2 Kolom: Kiri Berkas APL.01 & APL.02, Kanan Form Keputusan Verifikasi -->
    <div style="display: flex; gap: 24px; align-items: flex-start; flex-wrap: wrap;">
        
        <!-- KOLOM KIRI (Konten Berkas & Asesmen Mandiri) -->
        <div style="flex: 1 1 64%; min-width: 320px;">
            
            <!-- SECTION 1: VERIFIKASI DOKUMEN APL 01 -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <h2 style="margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9; font-size: 16px; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                    <span>📋</span>
                    <span>1. Verifikasi Berkas Permohonan (FR.APL.01)</span>
                </h2>

                <!-- Info Skema & TUK -->
                <table class="widefat striped" style="margin-bottom: 20px;">
                    <tr>
                        <th style="width: 25%; font-weight: 600;">Skema Sertifikasi</th>
                        <td><strong><?php echo esc_html( $skema['title'] ); ?></strong> (<?php echo esc_html( $skema['kode'] ?? '-' ); ?>)</td>
                    </tr>
                    <tr>
                        <th style="font-weight: 600;">Tempat Uji (TUK)</th>
                        <td><?php echo esc_html( $tuk['title'] ); ?> (<?php echo esc_html( $kegiatan->lokasi_uji ?: 'Sesuai Alamat TUK' ); ?>)</td>
                    </tr>
                    <tr>
                        <th style="font-weight: 600;">Tanggal Asesmen</th>
                        <td>📅 <?php echo date_i18n( 'l, d F Y', strtotime( $kegiatan->tanggal_pelaksanaan ) ); ?></td>
                    </tr>
                </table>

                <!-- Data Pribadi Pemohon -->
                <h3 style="font-size: 14px; margin: 15px 0 10px 0; color: #334155;">Data Pribadi Asesi</h3>
                <table class="widefat striped" style="margin-bottom: 20px;">
                    <tr>
                        <th style="width: 25%;">Nama Lengkap</th>
                        <td><strong><?php echo esc_html( $pendaftaran->nama_lengkap ); ?></strong></td>
                    </tr>
                    <tr>
                        <th>NIK / No. KTP</th>
                        <td><code><?php echo esc_html( $pendaftaran->nik ); ?></code></td>
                    </tr>
                    <tr>
                        <th>Tempat, Tanggal Lahir</th>
                        <td><?php echo esc_html( $pendaftaran->tempat_lahir ); ?>, <?php echo date_i18n( 'd/m/Y', strtotime( $pendaftaran->tanggal_lahir ) ); ?> (<?php echo esc_html( $pendaftaran->jenis_kelamin ); ?>)</td>
                    </tr>
                    <tr>
                        <th>Kontak Pemohon</th>
                        <td>📞 <?php echo esc_html( $pendaftaran->telepon ); ?> &bull; ✉️ <?php echo esc_html( $pendaftaran->email ); ?></td>
                    </tr>
                    <tr>
                        <th>Alamat Domisili</th>
                        <td><?php echo esc_html( $pendaftaran->alamat ); ?>, Kode Pos: <?php echo esc_html( $pendaftaran->kode_pos ); ?></td>
                    </tr>
                    <tr>
                        <th>Pendidikan Terakhir</th>
                        <td><?php echo esc_html( $pendaftaran->pendidikan ); ?></td>
                    </tr>
                    <?php if ( ! empty( $pendaftaran->nama_perusahaan ) ) : ?>
                    <tr>
                        <th>Pekerjaan / Instansi</th>
                        <td><?php echo esc_html( $pendaftaran->jabatan ); ?> di <strong><?php echo esc_html( $pendaftaran->nama_perusahaan ); ?></strong> (Masa kerja: <?php echo esc_html( $pendaftaran->masa_kerja ); ?>)</td>
                    </tr>
                    <?php endif; ?>
                </table>

                <!-- Berkas Persyaratan Pemohon -->
                <h3 style="font-size: 14px; margin: 15px 0 10px 0; color: #334155;">Kelengkapan Dokumen Bukti Pemohon</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <!-- Persyaratan Dasar -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px;">
                        <strong style="color: #0f172a; font-size: 13px; display: block; margin-bottom: 8px;">Bukti Persyaratan Dasar:</strong>
                        <?php 
                        $p_dasar = json_decode( $pendaftaran->persyaratan_dasar, true );
                        if ( ! empty( $p_dasar ) && is_array( $p_dasar ) ) : ?>
                            <ul style="margin: 0; padding-left: 18px; font-size: 12.5px; color: #334155; line-height: 1.6;">
                                <?php foreach ( $p_dasar as $pd ) : ?>
                                    <li><span style="color: #16a34a; font-weight: bold;">✓</span> <?php echo esc_html( $pd ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; font-style: italic;">Tidak ada berkas persyaratan dasar yang dipilih.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Persyaratan Administratif -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px;">
                        <strong style="color: #0f172a; font-size: 13px; display: block; margin-bottom: 8px;">Bukti Persyaratan Administratif:</strong>
                        <?php 
                        $p_admin = json_decode( $pendaftaran->persyaratan_admin, true );
                        if ( ! empty( $p_admin ) && is_array( $p_admin ) ) : ?>
                            <ul style="margin: 0; padding-left: 18px; font-size: 12.5px; color: #334155; line-height: 1.6;">
                                <?php foreach ( $p_admin as $pa ) : ?>
                                    <li><span style="color: #16a34a; font-weight: bold;">✓</span> <?php echo esc_html( $pa ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; font-style: italic;">Tidak ada berkas administratif yang dipilih.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: VALIDASI ASESMEN MANDIRI APL 02 -->
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9;">
                    <h2 style="margin: 0; font-size: 16px; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                        <span>✍️</span>
                        <span>2. Validasi Asesmen Mandiri (FR.APL.02)</span>
                    </h2>
                    <div>
                        <?php if ( $total_elemen > 0 ) : ?>
                            <span style="font-size: 12.5px; color: #475569; font-weight: 500;">
                                Total Elemen: <strong><?php echo $total_elemen; ?></strong> (K: <strong style="color: #15803d;"><?php echo $total_k; ?></strong>, BK: <strong style="color: #b91c1c;"><?php echo $total_bk; ?></strong>)
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ( ! empty( $apl02_jawaban ) ) : ?>
                    <p style="font-size: 13px; color: #64748b; margin-top: 0;">
                        Periksa pengakuan kompetensi mandiri asesi di bawah ini. Pastikan seluruh elemen kompetensi telah dinilai <strong>Kompeten (K)</strong> agar asesmen dapat dilanjutkan.
                    </p>

                    <?php 
                    $current_unit = '';
                    foreach ( $apl02_jawaban as $jawaban ) :
                        if ( $current_unit !== $jawaban->unit_kompetensi_kode ) :
                            $current_unit = $jawaban->unit_kompetensi_kode;
                            if ( $current_unit !== $apl02_jawaban[0]->unit_kompetensi_kode ) echo '</tbody></table><br>';
                    ?>
                        <div style="background: #f1f5f9; padding: 8px 12px; border-radius: 4px 4px 0 0; border: 1px solid #cbd5e1; border-bottom: none; font-weight: 600; font-size: 13px; color: #1e293b;">
                            🏷️ <?php echo esc_html( $jawaban->unit_kompetensi_kode ); ?> &mdash; <?php echo esc_html( $jawaban->unit_kompetensi_judul ); ?>
                        </div>
                        <table class="widefat striped" style="border-top: none; margin-bottom: 15px;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th style="width: 80px; text-align: center;">Elemen</th>
                                    <th>Kriteria Unjuk Kerja / Elemen</th>
                                    <th style="width: 130px; text-align: center;">Penilaian Mandiri</th>
                                </tr>
                            </thead>
                            <tbody>
                    <?php endif; ?>
                        <tr>
                            <td style="text-align: center; color: #64748b; font-weight: 600;"><?php echo esc_html( $jawaban->elemen_nomor ); ?></td>
                            <td style="font-size: 13px;"><?php echo esc_html( $jawaban->elemen_nama ); ?></td>
                            <td style="text-align: center;">
                                <?php if ( strtoupper( trim( $jawaban->jawaban ) ) === 'K' ) : ?>
                                    <span style="display: inline-block; padding: 3px 12px; background: #dcfce7; color: #15803d; font-weight: bold; border-radius: 12px; font-size: 12px;">
                                        ✓ Kompeten (K)
                                    </span>
                                <?php else : ?>
                                    <span style="display: inline-block; padding: 3px 12px; background: #fee2e2; color: #b91c1c; font-weight: bold; border-radius: 12px; font-size: 12px;">
                                        ✕ Belum (BK)
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                        </tbody></table>
                <?php else : ?>
                    <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 25px; text-align: center; color: #64748b;">
                        <p style="margin: 0; font-size: 14px;">Asesi belum mengisi lembar asesmen mandiri (FR.APL.02).</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- KOLOM KANAN (Form Tindakan Verifikasi & Validasi) -->
        <div style="flex: 1 1 33%; min-width: 300px; position: sticky; top: 32px;">
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-top: 4px solid #16a34a; border-radius: 8px; padding: 22px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06);">
                <h2 style="margin: 0 0 16px 0; font-size: 16px; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <span>⚖️</span>
                    <span>Keputusan Verifikasi &amp; Asesor</span>
                </h2>
                <p style="font-size: 12.5px; color: #64748b; margin-top: 0; line-height: 1.5;">
                    Lakukan verifikasi kelengkapan berkas APL.01 dan validasi APL.02, lalu tetapkan asesor penguji untuk melanjutkan proses asesmen.
                </p>

                <form id="form-verifikasi-pendaftaran">
                    <input type="hidden" name="id" value="<?php echo $pendaftaran->id; ?>">

                    <!-- 1. Status Kelengkapan APL.01 -->
                    <div style="margin-bottom: 16px;">
                        <label for="status_kelengkapan" style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">
                            1. Kelengkapan Berkas (APL.01) *
                        </label>
                        <select name="status_kelengkapan" id="status_kelengkapan" style="width: 100%; height: 38px; font-size: 13px;" required>
                            <option value="pending" <?php selected( $pendaftaran->status_kelengkapan, 'pending' ); ?>>⏳ Menunggu Verifikasi</option>
                            <option value="lengkap" <?php selected( $pendaftaran->status_kelengkapan, 'lengkap' ); ?>>✅ Memenuhi Syarat (Lengkap)</option>
                            <option value="tidak_lengkap" <?php selected( $pendaftaran->status_kelengkapan, 'tidak_lengkap' ); ?>>❌ Belum Memenuhi Syarat</option>
                        </select>
                    </div>

                    <!-- 2. Asesor Penugasan -->
                    <div style="margin-bottom: 16px;">
                        <label for="asesor_id" style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">
                            2. Asesor Kompetensi (Penguji) *
                        </label>
                        <select name="asesor_id" id="asesor_id" style="width: 100%; height: 38px; font-size: 13px;">
                            <option value="">-- Pilih Asesor Penguji --</option>
                            
                            <?php if ( ! empty( $kegiatan_asesors ) ) : ?>
                                <optgroup label="⭐ Asesor Bertugas pada Jadwal Ini">
                                    <?php foreach ( $kegiatan_asesors as $ka ) : ?>
                                        <option value="<?php echo $ka['id']; ?>" <?php selected( $pendaftaran->asesor_id, $ka['id'] ); ?>>
                                            <?php echo esc_html( $ka['name'] ); ?> <?php echo $ka['no_reg'] ? '(' . esc_html( $ka['no_reg'] ) . ')' : ''; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>

                            <optgroup label="Semua Asesor Terdaftar">
                                <?php foreach ( $asesor_list as $a ) : ?>
                                    <option value="<?php echo $a['id']; ?>" <?php selected( $pendaftaran->asesor_id, $a['id'] ); ?>>
                                        <?php echo esc_html( $a['name'] ); ?> <?php echo $a['no_reg'] ? '(' . esc_html( $a['no_reg'] ) . ')' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                        <p class="description" style="font-size: 11.5px; margin: 4px 0 0 0;">Asesor yang bertanggung jawab melaksanakan uji asesmen untuk asesi ini.</p>
                    </div>

                    <!-- 2b. Paket MUK (Bank Data MAPA & Instrumen) -->
                    <div style="margin-bottom: 16px;">
                        <label for="paket_muk_id" style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">
                            📦 Paket MUK (Bank Data MAPA)
                        </label>
                        <select name="paket_muk_id" id="paket_muk_id" style="width: 100%; height: 38px; font-size: 13px;">
                            <option value="0">-- Mengikuti Jadwal Sertifikasi (Default) --</option>
                            <?php if ( ! empty( $all_paket_muk ) ) : ?>
                                <?php foreach ( $all_paket_muk as $pm ) : ?>
                                    <option value="<?php echo esc_attr( $pm->id ); ?>" <?php selected( $pendaftaran->paket_muk_id, $pm->id ); ?>>
                                        <?php echo esc_html( $pm->nama_paket ); ?> (<?php echo esc_html( $pm->kode_versi ); ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <p class="description" style="font-size: 11.5px; margin: 4px 0 0 0;">Kaitkan asesi ini dengan versi Paket MUK / MAPA tertentu.</p>
                    </div>

                    <!-- 3. Rekomendasi Asesmen -->
                    <div style="margin-bottom: 16px;">
                        <label for="status_rekomendasi" style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">
                            3. Keputusan Rekomendasi Asesmen *
                        </label>
                        <select name="status_rekomendasi" id="status_rekomendasi" style="width: 100%; height: 38px; font-size: 13px;" required>
                            <option value="pending" <?php selected( $pendaftaran->status_rekomendasi, 'pending' ); ?>>⏳ Menunggu Keputusan</option>
                            <option value="disetujui" <?php selected( $pendaftaran->status_rekomendasi, 'disetujui' ); ?>>✅ Diterima — Lanjut Asesmen</option>
                            <option value="ditolak" <?php selected( $pendaftaran->status_rekomendasi, 'ditolak' ); ?>>❌ Ditolak (Tidak Memenuhi)</option>
                        </select>
                    </div>

                    <!-- 3b. Status FR.APL.02 (asesmen mandiri) -->
                    <?php
                    $apl02_kep = LSP_Cert_Integration::get_apl02_kelengkapan( (int) $pendaftaran->id );

                    if ( ! $apl02_kep['lengkap'] ) {
                        $apl02_warna  = '#b91c1c';
                        $apl02_latar  = '#fef2f2';
                        $apl02_teks   = '#991b1b';
                    } elseif ( $apl02_kep['bk'] > 0 ) {
                        $apl02_warna  = '#b45309';
                        $apl02_latar  = '#fffbeb';
                        $apl02_teks   = '#92400e';
                    } else {
                        $apl02_warna  = '#15803d';
                        $apl02_latar  = '#f0fdf4';
                        $apl02_teks   = '#166534';
                    }
                    ?>
                    <div style="margin-bottom: 16px; border-left: 4px solid <?php echo esc_attr( $apl02_warna ); ?>; background: <?php echo esc_attr( $apl02_latar ); ?>; padding: 12px 16px; border-radius: 4px;">
                        <div style="font-size: 12.5px; font-weight: 700; color: <?php echo esc_attr( $apl02_teks ); ?>; margin-bottom: 4px;">
                            FR.APL.02 &mdash; Asesmen Mandiri
                        </div>
                        <div style="font-size: 12.5px; line-height: 1.55; color: #374151;">
                            <?php if ( 0 === (int) $apl02_kep['total'] ) : ?>
                                Master SKKNI skema ini belum memiliki elemen kompetensi &mdash; FR.APL.02 tidak dapat dinilai.
                            <?php else : ?>
                                Terisi <strong><?php echo (int) $apl02_kep['terjawab']; ?></strong> dari
                                <strong><?php echo (int) $apl02_kep['total']; ?></strong> elemen
                                (K: <?php echo (int) $apl02_kep['k']; ?>, BK: <?php echo (int) $apl02_kep['bk']; ?>).
                                <?php if ( ! $apl02_kep['lengkap'] ) : ?>
                                    <br><strong><?php echo (int) $apl02_kep['total'] - (int) $apl02_kep['terjawab']; ?> elemen masih kosong</strong>
                                    &mdash; pendaftaran belum bisa diterima sampai dilengkapi.
                                    <?php
                                    $contoh = array();
                                    foreach ( array_slice( $apl02_kep['kosong'], 0, 3 ) as $k ) {
                                        $contoh[] = $k['kode'] . ' elemen ' . $k['nomor'];
                                    }
                                    if ( $contoh ) {
                                        echo '<br><span style="font-size: 11.5px; color: #7f1d1d;">Contoh: ' . esc_html( implode( '; ', $contoh ) ) . '</span>';
                                    }
                                    ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ( (int) $apl02_kep['bk'] > 0 ) : ?>
                    <div style="margin-bottom: 16px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 4px; padding: 12px 16px;">
                        <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 12.5px; color: #92400e; cursor: pointer;">
                            <input type="checkbox" name="konfirmasi_bk" id="konfirmasi_bk" value="1" style="margin-top: 2px;"
                                <?php checked( ! empty( $pendaftaran->konfirmasi_bk ) ); ?>>
                            <span>
                                <strong>Sudah dikonfirmasi ke asesi</strong> &mdash; wajib dicentang karena
                                <?php echo (int) $apl02_kep['bk']; ?> elemen dijawab <strong>BK</strong> (belum kompeten).
                                Tanpa konfirmasi, pendaftaran tidak akan diterima.
                            </span>
                        </label>
                    </div>
                    <?php endif; ?>

                    <!-- 4. Catatan Admin -->
                    <div style="margin-bottom: 20px;">
                        <label for="catatan" style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">
                            Catatan Verifikator / Admin
                        </label>
                        <textarea name="catatan" id="catatan" rows="3" style="width: 100%; font-size: 13px;" placeholder="Tuliskan catatan verifikasi berkas atau rekomendasi..."><?php echo esc_textarea( $pendaftaran->catatan_admin ); ?></textarea>
                    </div>

                    <!-- Tombol Simpan -->
                    <div>
                        <button type="submit" class="button button-primary" id="btn-save-verifikasi" style="width: 100%; height: 42px; font-size: 14px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 6px; background: #16a34a; border-color: #16a34a;">
                            <span>💾</span>
                            <span>Simpan Verifikasi &amp; Validasi</span>
                        </button>
                    </div>

                    <div style="margin-top: 14px; text-align: center;">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-cetak-dokumen&tab=apl&kegiatan_id=' . $pendaftaran->kegiatan_id . '&pendaftaran_id=' . $pendaftaran->id ) ); ?>" target="_blank" style="font-size: 12px; color: #0284c7; text-decoration: none; font-weight: 600;">
                            🖨️ Buka Pusat Cetak Dokumen Jadwal Ini &rarr;
                        </a>
                    </div>
                </form>
            </div>

            <!-- Card Status Sertifikat Kompetensi -->
            <div class="card" style="padding: 18px 20px; border-radius: 8px; border: 1px solid #cbd5e1; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h3 style="margin-top: 0; font-size: 14.5px; color: #0f172a; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                    <span>🏅 Sertifikat Kompetensi BNSP</span>
                    <?php if ( $sertifikat_info ) : ?>
                        <span style="font-size: 11px; background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 4px; font-weight: 700;">TERBIT</span>
                    <?php else : ?>
                        <span style="font-size: 11px; background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; font-weight: 700;">BELUM TERBIT</span>
                    <?php endif; ?>
                </h3>
                <?php if ( $sertifikat_info ) : ?>
                    <table style="width: 100%; font-size: 12px; margin-bottom: 12px; line-height: 1.7;">
                        <tr>
                            <td style="color: #64748b; width: 42%;">Nomor Blanko:</td>
                            <td><strong style="color: #0f172a;"><code><?php echo esc_html( $sertifikat_info['kode_blanko'] ); ?></code></strong></td>
                        </tr>
                        <tr>
                            <td style="color: #64748b;">No. Registrasi:</td>
                            <td><code><?php echo esc_html( $sertifikat_info['no_registrasi'] ); ?></code></td>
                        </tr>
                        <tr>
                            <td style="color: #64748b;">Tanggal Terbit:</td>
                            <td><?php echo esc_html( ! empty( $sertifikat_info['tanggal_terbit'] ) ? date_i18n( 'd F Y', strtotime( $sertifikat_info['tanggal_terbit'] ) ) : '-' ); ?></td>
                        </tr>
                    </table>
                    <div style="display: flex; gap: 6px;">
                        <a href="<?php echo esc_url( $sertifikat_info['edit_url'] ); ?>" class="button button-small" style="flex: 1; text-align: center;">✏️ Kelola Data</a>
                        <a href="<?php echo esc_url( home_url( '/cek-sertifikat/' ) ); ?>" target="_blank" class="button button-small" style="flex: 1; text-align: center;">🔍 Cek Publik</a>
                    </div>
                <?php else : ?>
                    <p style="margin: 0 0 12px 0; font-size: 12px; color: #64748b; line-height: 1.4;">
                        Sertifikat belum diterbitkan untuk asesi ini. Sertifikat yang diinput atau diimpor wajib terhubung dengan pendaftaran ini.
                    </p>
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=data_sertifikat&pendaftaran_id=' . $pendaftaran->id ) ); ?>" 
                       class="button button-primary" 
                       style="display: block; width: 100%; text-align: center; font-size: 12px; font-weight: 600; background: #16a34a; border-color: #15803d; text-decoration: none; box-sizing: border-box;">
                        + Terbitkan Sertifikat Asesi Ini
                    </a>
                <?php endif; ?>
            </div>

            <!-- Card Cetak Dokumen Terhubung untuk Asesi Ini -->
            <div class="card" style="padding: 18px 20px; border-radius: 8px; border: 1px solid #cbd5e1; margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h3 style="margin-top: 0; font-size: 14.5px; color: #0f172a; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                    <span>🖨️ Cetak Berkas Asesi Ini</span>
                    <span style="font-size: 11px; background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 4px; font-weight: 600;">Data Terhubung</span>
                </h3>
                <p style="margin: 0 0 12px 0; font-size: 12px; color: #64748b; line-height: 1.4;">
                    Semua dokumen di bawah otomatis terisi data Asesi (<strong><?php echo esc_html( $pendaftaran->nama_lengkap ); ?></strong>), Asesor yang ditugaskan, TUK, dan Paket MUK.
                </p>

                <!-- Tombol Utama: Cetak Seluruh Berkas Sekaligus -->
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=bundle_all' ) ); ?>" 
                   target="_blank" class="button button-primary" 
                   style="width: 100%; height: 38px; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; font-weight: 700; background: #0284c7; border-color: #0284c7; margin-bottom: 12px; text-decoration: none;">
                    <span>📦</span>
                    <span>Cetak Paket Lengkap Asesi (1 s/d 25)</span>
                </a>

                <!-- Kategori Dokumen Terkait Asesi -->
                <div style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase;">1. Permohonan &amp; Asesmen Mandiri</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 10px;">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=apl01' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">📄 FR.APL.01</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=apl02' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">📄 FR.APL.02</a>
                </div>

                <div style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase;">2. Perencanaan &amp; Persetujuan</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 10px;">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=mapa01_obs' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">📋 MAPA.01 (Obs)</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=mapa02' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">🗺️ FR.MAPA.02</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=ak01' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">🔏 FR.AK.01</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=ak04' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">⚖️ FR.AK.04</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=ak07' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px; grid-column: span 2; background: #f8fafc;">⚖️ FR.AK.07 (Penyesuaian Wajar)</a>
                </div>

                <div style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase;">3. Instrumen Uji (FR.IA)</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 10px;">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=ia01' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">🛠️ FR.IA.01 (CL)</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=ia02' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">🛠️ FR.IA.02 (TPD)</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=ia03' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">🛠️ FR.IA.03 (PMO)</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=ia04a' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">🛠️ FR.IA.04A</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=ia04b' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">🛠️ FR.IA.04B</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=ia05a' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">✍️ IA.05A (Soal)</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=ia05c' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">✍️ IA.05C (Jawab)</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=ia05b' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px; color: #b91c1c;">🔐 IA.05B (Kunci)</a>
                </div>

                <div style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase;">4. Keputusan &amp; Rekaman</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 12px;">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=ak02' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">⚖️ FR.AK.02</a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=print&id=' . $pendaftaran->id . '&doc=ak03' ) ); ?>" target="_blank" class="button" style="text-align: center; font-size: 11px;">⚖️ FR.AK.03</a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#form-verifikasi-pendaftaran').on('submit', function(e) {
        e.preventDefault();
        
        var $btn = $('#btn-save-verifikasi');
        var originalText = $btn.html();
        
        var statusKelengkapan = $('#status_kelengkapan').val();
        var statusRekomendasi = $('#status_rekomendasi').val();
        var asesorId = $('#asesor_id').val();
        
        if (statusRekomendasi === 'disetujui' && !asesorId) {
            alert('Perhatian: Untuk menyetujui asesi lanjut ke tahap asesmen, silakan tetapkan Asesor Penguji terlebih dahulu.');
            $('#asesor_id').focus();
            return;
        }

        $btn.prop('disabled', true).html('<span>⏳</span> Menyimpan...');

        var data = {
            action: 'lsp_cert_save_verifikasi',
            nonce: lspCert.nonce,
            id: $('input[name="id"]').val(),
            status_kelengkapan: statusKelengkapan,
            status_rekomendasi: statusRekomendasi,
            asesor_id: asesorId,
            paket_muk_id: $('#paket_muk_id').val(),
            catatan: $('#catatan').val(),
            konfirmasi_bk: $('#konfirmasi_bk').is(':checked') ? 1 : 0
        };

        $.post(lspCert.ajax_url, data, function(response) {
            $btn.prop('disabled', false).html(originalText);
            if (response.success) {
                alert(response.data.message || 'Verifikasi berhasil disimpan');
                window.location.reload();
            } else {
                alert('Gagal: ' + (response.data || 'Terjadi kesalahan'));
            }
        }).fail(function(xhr) {
            $btn.prop('disabled', false).html(originalText);

            var pesan = 'Koneksi terputus atau server error.';
            if (xhr && xhr.responseJSON && xhr.responseJSON.data) {
                pesan = xhr.responseJSON.data.message || pesan;
                if (Array.isArray(xhr.responseJSON.data.belum_dijawab) && xhr.responseJSON.data.belum_dijawab.length) {
                    pesan += '\n\nBelum dijawab:\n- ' + xhr.responseJSON.data.belum_dijawab.join('\n- ');
                }
            }
            alert(pesan);
        });
    });
});
</script>