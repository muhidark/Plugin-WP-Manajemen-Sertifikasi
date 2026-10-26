<div class="lsp-form-container" id="form-pendaftaran">
    <div style="margin-bottom: 15px;">
        <a href="<?php echo esc_url( remove_query_arg( 'kegiatan_id' ) ); ?>" class="lsp-button" style="background:#64748b; font-size:13px; padding:6px 12px; text-decoration:none;">&larr; Kembali ke Daftar Kegiatan</a>
    </div>

    <h2>Form Pendaftaran Sertifikasi</h2>
    
    <div class="lsp-info-box">
        <h3>Informasi Kegiatan</h3>
        <p><strong>Skema:</strong> <?php echo esc_html( $skema['title'] ?? '-' ); ?> (<?php echo esc_html( $skema['kode'] ?? '-' ); ?>)</p>
        <p><strong>Tanggal Pelaksanaan:</strong> <?php echo date_i18n( 'd F Y', strtotime( $kegiatan->tanggal_pelaksanaan ) ); ?></p>
        <p><strong>TUK:</strong> <?php echo esc_html( $tuk['title'] ?? '-' ); ?></p>
        <?php if ( ! empty( $tuk['alamat'] ) ) : ?>
            <p><strong>Lokasi TUK:</strong> <?php echo esc_html( $tuk['alamat'] ); ?></p>
        <?php endif; ?>
    </div>
    
    <form id="lsp-pendaftaran-form">
        <input type="hidden" name="kegiatan_id" value="<?php echo esc_attr( $kegiatan->id ); ?>">
        
        <!-- Bagian 1: Data Pribadi -->
        <div class="lsp-form-section">
            <h3>Bagian 1: Data Pribadi Pemohon (FR.APL.01)</h3>
            
            <div class="lsp-form-group">
                <label>Nama Lengkap (Sesuai KTP) *</label>
                <input type="text" name="nama_lengkap" required placeholder="Nama lengkap beserta gelar (jika ada)">
            </div>
            
            <div class="lsp-form-row">
                <div class="lsp-form-group">
                    <label>NIK (No. KTP) *</label>
                    <input type="text" name="nik" required placeholder="16 digit NIK">
                </div>
                <div class="lsp-form-group">
                    <label>Tempat Lahir *</label>
                    <input type="text" name="tempat_lahir" required placeholder="Kota tempat lahir">
                </div>
            </div>
            
            <div class="lsp-form-row">
                <div class="lsp-form-group">
                    <label>Tanggal Lahir *</label>
                    <input type="date" name="tanggal_lahir" required>
                </div>
                <div class="lsp-form-group">
                    <label>Jenis Kelamin *</label>
                    <select name="jenis_kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
            </div>
            
            <div class="lsp-form-group">
                <label>Kebangsaan *</label>
                <input type="text" name="kebangsaan" value="Indonesia" required>
            </div>
            
            <div class="lsp-form-group">
                <label>Alamat Rumah Tinggal *</label>
                <textarea name="alamat" rows="3" required placeholder="Alamat lengkap tempat tinggal saat ini"></textarea>
            </div>
            
            <div class="lsp-form-row">
                <div class="lsp-form-group">
                    <label>Kode Pos *</label>
                    <input type="text" name="kode_pos" required placeholder="Contoh: 12920">
                </div>
                <div class="lsp-form-group">
                    <label>No. HP / WhatsApp Aktif *</label>
                    <input type="text" name="telepon" required placeholder="Contoh: 08123456789">
                </div>
            </div>
            
            <div class="lsp-form-row">
                <div class="lsp-form-group">
                    <label>Email Aktif *</label>
                    <input type="email" name="email" required placeholder="nama@email.com">
                </div>
                <div class="lsp-form-group">
                    <label>Pendidikan Terakhir *</label>
                    <input type="text" name="pendidikan" required placeholder="Contoh: SMA / D3 / S1 Ilmu Komunikasi">
                </div>
            </div>
        </div>
        
        <!-- Bagian 2: Data Pekerjaan (Opsional) -->
        <div class="lsp-form-section">
            <h3>Bagian 2: Data Pekerjaan Sekarang (Opsional)</h3>
            
            <div class="lsp-form-group">
                <label>Nama Institusi / Perusahaan</label>
                <input type="text" name="nama_perusahaan" placeholder="Tempat Anda bekerja saat ini">
            </div>
            
            <div class="lsp-form-group">
                <label>Jabatan / Posisi</label>
                <input type="text" name="jabatan" placeholder="Posisi pekerjaan Anda">
            </div>
            
            <div class="lsp-form-group">
                <label>Alamat Kantor</label>
                <textarea name="alamat_kantor" rows="2" placeholder="Alamat lengkap instansi/perusahaan"></textarea>
            </div>
            
            <div class="lsp-form-row">
                <div class="lsp-form-group">
                    <label>Kode Pos Kantor</label>
                    <input type="text" name="kode_pos_kantor">
                </div>
                <div class="lsp-form-group">
                    <label>Telepon Kantor</label>
                    <input type="text" name="telepon_kantor">
                </div>
            </div>
            
            <div class="lsp-form-row">
                <div class="lsp-form-group">
                    <label>Email Kantor</label>
                    <input type="email" name="email_kantor">
                </div>
                <div class="lsp-form-group">
                    <label>Masa Kerja</label>
                    <input type="text" name="masa_kerja" placeholder="Contoh: 2 tahun">
                </div>
            </div>
        </div>
        
        <!-- Bagian 3: Tujuan Asesmen -->
        <div class="lsp-form-section">
            <h3>Bagian 3: Data Permohonan Sertifikasi</h3>
            
            <div class="lsp-form-group">
                <label>Tujuan Asesmen *</label>
                <select name="tujuan_asesmen" required>
                    <option value="sertifikasi">Sertifikasi Baru</option>
                    <option value="sertifikasi_ulang">Sertifikasi Ulang (Resertifikasi)</option>
                    <option value="pkt">Pengakuan Kompetensi Terkini (PKT)</option>
                    <option value="rpl">Rekognisi Pembelajaran Lampau (RPL)</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
        </div>
        
        <!-- Bagian 4: Persyaratan (Checkbox) -->
        <div class="lsp-form-section">
            <h3>Bagian 4: Bukti Kelengkapan Pemohon</h3>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 15px;"><em>Centang dokumen kelengkapan yang Anda bawa/siapkan saat uji sertifikasi:</em></p>
            
            <?php 
            $syarat_dasar = LSP_Cert_Integration::get_persyaratan_dasar( $kegiatan->skema_id );
            $syarat_admin = LSP_Cert_Integration::get_persyaratan_admin( $kegiatan->skema_id );
            ?>

            <h4>1. Bukti Persyaratan Dasar:</h4>
            <div class="lsp-checkbox-group">
                <?php foreach ( $syarat_dasar as $idx => $syarat ) : ?>
                    <label>
                        <input type="checkbox" name="persyaratan_dasar[]" value="<?php echo esc_attr( $syarat ); ?>" checked> 
                        <?php echo esc_html( $syarat ); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            
            <h4 style="margin-top: 15px;">2. Bukti Persyaratan Administratif:</h4>
            <div class="lsp-checkbox-group">
                <?php foreach ( $syarat_admin as $idx => $syarat ) : ?>
                    <label>
                        <input type="checkbox" name="persyaratan_admin[]" value="<?php echo esc_attr( $syarat ); ?>" checked> 
                        <?php echo esc_html( $syarat ); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Bagian 5: APL 02 - Asesmen Mandiri -->
        <div class="lsp-form-section">
            <h3>Bagian 5: Asesmen Mandiri (FR.APL.02)</h3>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 15px;">
                <em>Lakukan penilaian mandiri atas kemampuan Anda pada setiap unit kompetensi di bawah ini.</em>
            </p>

            <!-- Bulk Selection Toolbar -->
            <div class="lsp-apl02-toolbar" style="margin-bottom: 20px; padding: 12px 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div>
                    <strong style="color: #166534; font-size: 14px;">Pilihan Cepat Asesmen Mandiri:</strong>
                    <div style="font-size: 13px; color: #15803d;">Pilih semua unit sekaligus untuk mempercepat pengisian.</div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="lsp-button" id="btn-apl02-all-k" style="background: #16a34a; font-size: 13px; padding: 6px 14px; border: none; cursor: pointer;">
                        ✓ Pilih Semua Kompeten (K)
                    </button>
                    <button type="button" class="lsp-button" id="btn-apl02-all-bk" style="background: #64748b; font-size: 13px; padding: 6px 14px; border: none; cursor: pointer;">
                        Pilih Semua Belum Kompeten (BK)
                    </button>
                </div>
            </div>
            
            <?php 
            $apl_counter = 0;
            if ( empty( $unit_kompetensi ) ) : ?>
                <p><em>Belum ada unit kompetensi yang terdaftar untuk skema ini.</em></p>
            <?php else : ?>
                <?php foreach ( $unit_kompetensi as $u_idx => $unit ) : ?>
                    <div class="lsp-unit-box">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <h4 style="margin: 0; color: #0284c7;">Unit <?php echo ($u_idx + 1); ?>: <?php echo esc_html( $unit['kode'] ); ?></h4>
                        </div>
                        <p style="margin: 0 0 10px 0; font-weight: 600; color: #334155;"><?php echo esc_html( $unit['judul'] ); ?></p>
                        
                        <?php 
                        $elemen_list = ! empty( $unit['elemen'] ) ? $unit['elemen'] : array(
                            array( 'nomor' => 1, 'nama' => 'Melaksanakan ' . $unit['judul'], 'kuk' => array() ),
                        );
                        
                        foreach ( $elemen_list as $elemen ) : 
                            $apl_counter++;
                        ?>
                            <div class="lsp-elemen-row" style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; display: flex; align-items: flex-start;">
                                <div class="lsp-elemen-nama" style="flex: 2; padding-right: 15px; font-size: 14px;">
                                    <strong>Elemen <?php echo esc_html( $elemen['nomor'] ); ?>:</strong> <?php echo esc_html( $elemen['nama'] ); ?>
                                    
                                    <?php if ( ! empty( $elemen['kuk'] ) && is_array( $elemen['kuk'] ) ) : ?>
                                        <div class="lsp-kuk-list" style="margin-top: 8px; font-size: 12.5px; color: #475569; background: #f8fafc; padding: 8px 12px; border-radius: 4px; border-left: 3px solid #94a3b8;">
                                            <span style="font-weight: 600; color: #1e293b; display: block; margin-bottom: 3px;">Kriteria Unjuk Kerja (KUK):</span>
                                            <ul style="margin: 0; padding-left: 16px; line-height: 1.5;">
                                                <?php foreach ( $elemen['kuk'] as $kuk_item ) : ?>
                                                    <li><?php echo esc_html( $kuk_item ); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="lsp-elemen-options" style="flex: 1; min-width: 170px; display: flex; gap: 12px; align-items: center; padding-top: 5px;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 4px; font-weight: 600; color: #166534; font-size: 13px;">
                                        <input type="radio" class="apl02-radio apl02-k" name="apl02[<?php echo $apl_counter; ?>][jawaban]" value="K" required> K (Kompeten)
                                    </label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 4px; font-weight: 600; color: #991b1b; font-size: 13px;">
                                        <input type="radio" class="apl02-radio apl02-bk" name="apl02[<?php echo $apl_counter; ?>][jawaban]" value="BK" required> BK
                                    </label>
                                </div>
                                <input type="hidden" name="apl02[<?php echo $apl_counter; ?>][kode]" value="<?php echo esc_attr( $unit['kode'] ); ?>">
                                <input type="hidden" name="apl02[<?php echo $apl_counter; ?>][judul]" value="<?php echo esc_attr( $unit['judul'] ); ?>">
                                <input type="hidden" name="apl02[<?php echo $apl_counter; ?>][elemen_nomor]" value="<?php echo esc_attr( $elemen['nomor'] ); ?>">
                                <input type="hidden" name="apl02[<?php echo $apl_counter; ?>][elemen_nama]" value="<?php echo esc_attr( $elemen['nama'] ); ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="lsp-form-submit">
            <button type="submit" class="lsp-button lsp-button-primary" style="padding: 12px 30px; font-size: 16px;">Kirim Pendaftaran & Asesmen Mandiri</button>
        </div>
    </form>
    
    <div id="lsp-form-message" class="lsp-message" style="display:none; margin-top: 20px;"></div>
</div>
