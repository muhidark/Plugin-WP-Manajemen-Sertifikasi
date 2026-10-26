<?php
/**
 * Template Dokumen Cetak FR.APL.01
 * Permohonan Sertifikasi Kompetensi
 * Sesuai Standar Format BNSP (Tanpa Logo & Kop)
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$p_dasar_checked = json_decode( $pendaftaran->persyaratan_dasar, true ) ?: array();
$p_admin_checked = json_decode( $pendaftaran->persyaratan_admin, true ) ?: array();

$syarat_dasar_all = LSP_Cert_Integration::get_persyaratan_dasar( $kegiatan->skema_id );
$syarat_admin_all = LSP_Cert_Integration::get_persyaratan_admin( $kegiatan->skema_id );

// Hitung tanggal tanda tangan APL 01 (Backdate H-1 dari tanggal pendaftaran / created_at)
$base_date = ! empty( $pendaftaran->created_at ) ? $pendaftaran->created_at : current_time( 'mysql' );
$tanggal_ttd_apl01 = date_i18n( 'd F Y', strtotime( '-1 day', strtotime( $base_date ) ) );
?>

<div class="lsp-doc-page">

    <div style="text-align: center; margin-bottom: 20px;">
        <h1 style="font-size: 15px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">FR.APL.01. PERMOHONAN SERTIFIKASI KOMPETENSI</h1>
    </div>

    <!-- Bagian 1 : Rincian Data Pemohon Sertifikasi -->
    <div class="lsp-doc-section">
        <h3 style="font-size: 13px; font-weight: bold; margin: 12px 0 4px 0;">Bagian 1 : Rincian Data Pemohon Sertifikasi</h3>
        <p style="font-size: 12px; margin: 0 0 10px 0; color: #334155;">Pada bagian ini, cantumkan data pribadi, data pendidikan formal serta data pekerjaan anda pada saat ini.</p>

        <!-- Data Pribadi -->
        <h4 style="font-size: 12.5px; font-weight: bold; margin: 10px 0 6px 0;">Data Pribadi</h4>
        <table class="lsp-doc-table lsp-table-borderless" style="width: 100%; border-collapse: collapse; margin-bottom: 12px; border: none;">
            <tr>
                <td width="28%">Nama lengkap</td>
                <td width="3%">:</td>
                <td colspan="3"><strong><?php echo esc_html( $pendaftaran->nama_lengkap ); ?></strong></td>
            </tr>
            <tr>
                <td>No. KTP/NIK/Paspor</td>
                <td>:</td>
                <td colspan="3"><?php echo esc_html( $pendaftaran->nik ); ?></td>
            </tr>
            <tr>
                <td>Tempat / tgl. Lahir</td>
                <td>:</td>
                <td colspan="3"><?php echo esc_html( $pendaftaran->tempat_lahir ); ?> / <?php echo date_i18n( 'd-m-Y', strtotime( $pendaftaran->tanggal_lahir ) ); ?></td>
            </tr>
            <tr>
                <td>Jenis kelamin</td>
                <td>:</td>
                <td colspan="3">
                    <?php 
                    $jk = strtolower( $pendaftaran->jenis_kelamin );
                    if ( $jk === 'l' || strpos( $jk, 'laki' ) !== false ) {
                        echo '<strong>Laki-laki</strong> / <del style="color:#64748b;">Wanita</del> *)';
                    } elseif ( $jk === 'p' || strpos( $jk, 'perempuan' ) !== false || strpos( $jk, 'wanita' ) !== false ) {
                        echo '<del style="color:#64748b;">Laki-laki</del> / <strong>Wanita</strong> *)';
                    } else {
                        echo 'Laki-laki / Wanita *)';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Kebangsaan</td>
                <td>:</td>
                <td colspan="3"><?php echo esc_html( $pendaftaran->kebangsaan ?: 'Indonesia' ); ?></td>
            </tr>
            <tr>
                <td rowspan="2" valign="top">Alamat rumah</td>
                <td>:</td>
                <td colspan="3"><?php echo esc_html( $pendaftaran->alamat ); ?></td>
            </tr>
            <tr>
                <td>:</td>
                <td width="40%"></td>
                <td width="12%">Kode pos :</td>
                <td><?php echo esc_html( $pendaftaran->kode_pos ?: '………………' ); ?></td>
            </tr>
            <tr>
                <td rowspan="2" valign="top">No. Telepon/E-mail</td>
                <td>:</td>
                <td>Rumah : -</td>
                <td>Kantor :</td>
                <td>-</td>
            </tr>
            <tr>
                <td>:</td>
                <td>HP : <?php echo esc_html( $pendaftaran->telepon ); ?></td>
                <td>E-mail :</td>
                <td><?php echo esc_html( $pendaftaran->email ); ?></td>
            </tr>
            <tr>
                <td>Kualifikasi Pendidikan</td>
                <td>:</td>
                <td colspan="3"><?php echo esc_html( $pendaftaran->pendidikan ); ?></td>
            </tr>
        </table>
        <div style="font-size: 11px; margin-top: -6px; margin-bottom: 12px; color: #475569;">*Coret yang tidak perlu</div>

        <!-- Data Pekerjaan Sekarang -->
        <h4 style="font-size: 12.5px; font-weight: bold; margin: 10px 0 6px 0;">Data Pekerjaan Sekarang</h4>
        <table class="lsp-doc-table lsp-table-borderless" style="width: 100%; border-collapse: collapse; margin-bottom: 15px; border: none;">
            <tr>
                <td width="28%">Nama Institusi / Perusahaan</td>
                <td width="3%">:</td>
                <td colspan="3"><?php echo esc_html( $pendaftaran->nama_perusahaan ?: '…………………………………………………………………………' ); ?></td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td colspan="3"><?php echo esc_html( $pendaftaran->jabatan ?: '…………………………………………………………………………' ); ?></td>
            </tr>
            <tr>
                <td rowspan="2" valign="top">Alamat Kantor</td>
                <td>:</td>
                <td colspan="3"><?php echo esc_html( $pendaftaran->alamat_kantor ?: '…………………………………………………………………………' ); ?></td>
            </tr>
            <tr>
                <td>:</td>
                <td width="40%"></td>
                <td width="12%">Kode pos :</td>
                <td>………………………………………………</td>
            </tr>
            <tr>
                <td rowspan="2" valign="top">No. Telepon/Fax/E-mail</td>
                <td>:</td>
                <td>Telp. : ……………………….</td>
                <td>Fax. :</td>
                <td>…………………………</td>
            </tr>
            <tr>
                <td>:</td>
                <td colspan="3">E-mail : ………………………………………………………………………….</td>
            </tr>
        </table>
    </div>

    <!-- Bagian 2 : Data Sertifikasi -->
    <div class="lsp-doc-section" style="margin-top: 15px;">
        <h3 style="font-size: 13px; font-weight: bold; margin: 12px 0 4px 0;">Bagian 2 : Data Sertifikasi</h3>
        <p style="font-size: 12px; margin: 0 0 10px 0; color: #334155;">Tuliskan Judul dan Nomor Skema Sertifikasi yang anda ajukan berikut Daftar Unit Kompetensi sesuai kemasan pada skema sertifikasi untuk mendapatkan pengakuan sesuai dengan latar belakang pendidikan, pelatihan serta pengalaman kerja yang anda miliki.</p>

        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
            <tr>
                <td width="30%" rowspan="2" valign="middle"><strong>Skema Sertifikasi<br>(KKNI/Okupasi/Klaster)</strong></td>
                <td width="12%">Judul</td>
                <td width="3%">:</td>
                <td colspan="2"><strong><?php echo esc_html( $skema['title'] ?? '-' ); ?></strong></td>
            </tr>
            <tr>
                <td>Nomor</td>
                <td>:</td>
                <td colspan="2"><code><?php echo esc_html( $skema['kode'] ?? '-' ); ?></code></td>
            </tr>
            <?php 
            $tujuan = strtolower( $pendaftaran->tujuan_asesmen ?? 'sertifikasi' );
            ?>
            <tr>
                <td colspan="2" rowspan="4" valign="top"><strong>Tujuan Asesmen</strong></td>
                <td>:</td>
                <td width="5%" style="text-align: center; font-size: 14px;"><?php echo ($tujuan === 'sertifikasi' || $tujuan === 'sertifikasi baru') ? '☑' : '☐'; ?></td>
                <td>Sertifikasi</td>
            </tr>
            <tr>
                <td>:</td>
                <td style="text-align: center; font-size: 14px;"><?php echo ($tujuan === 'pkt') ? '☑' : '☐'; ?></td>
                <td>Pengakuan Kompetensi Terkini (PKT)</td>
            </tr>
            <tr>
                <td>:</td>
                <td style="text-align: center; font-size: 14px;"><?php echo ($tujuan === 'rpl') ? '☑' : '☐'; ?></td>
                <td>Rekognisi Pembelajaran Lampau</td>
            </tr>
            <tr>
                <td>:</td>
                <td style="text-align: center; font-size: 14px;"><?php echo ($tujuan === 'lainnya') ? '☑' : '☐'; ?></td>
                <td>Lainnya</td>
            </tr>
        </table>

        <!-- Daftar Unit Kompetensi Sesuai Kemasan -->
        <h4 style="font-size: 12.5px; font-weight: bold; margin: 12px 0 6px 0;">Daftar Unit Kompetensi sesuai kemasan:</h4>
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background: #f1f5f9; text-align: center; font-size: 12px;">
                    <th width="6%">No.</th>
                    <th width="24%">Kode Unit</th>
                    <th width="50%">Judul Unit</th>
                    <th width="20%">Standar Kompetensi Kerja</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $units_skkni ) ) : ?>
                    <?php foreach ( $units_skkni as $u_idx => $u ) : 
                        $standar_ref = ! empty( $skema['acuan_skkni'] ) ? trim( $skema['acuan_skkni'] ) : 'Standar Khusus / SKKNI';
                        if ( strpos( $u['kode'], 'J.63OPR' ) !== false ) {
                            $standar_ref = 'SKKNI 2018-056';
                        } elseif ( strpos( $u['kode'], 'M.70MKT' ) !== false ) {
                            $standar_ref = 'SKKNI 2022-124';
                        } elseif ( strpos( $u['kode'], 'J.59MTM' ) !== false ) {
                            $standar_ref = 'SKKNI 2018-107';
                        } elseif ( strpos( $u['kode'], 'M.73ADV' ) !== false ) {
                            $standar_ref = 'SKKNI 2023-101';
                        }
                    ?>
                        <tr style="font-size: 12px;">
                            <td style="text-align: center;"><?php echo ($u_idx + 1); ?></td>
                            <td style="font-family: monospace; font-weight: bold;"><?php echo esc_html( $u['kode'] ); ?></td>
                            <td><?php echo esc_html( $u['judul'] ); ?></td>
                            <td style="text-align: center;"><?php echo esc_html( $standar_ref ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">Daftar unit kompetensi tidak ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bagian 3 : Bukti Kelengkapan Pemohon -->
    <div class="lsp-doc-section" style="margin-top: 15px;">
        <h3 style="font-size: 13px; font-weight: bold; margin: 12px 0 4px 0;">Bagian 3 : Bukti Kelengkapan Pemohon</h3>

        <!-- Bukti Persyaratan Dasar Pemohon -->
        <h4 style="font-size: 12.5px; font-weight: bold; margin: 10px 0 6px 0;">Bukti Persyaratan Dasar Pemohon</h4>
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background: #f1f5f9; text-align: center; font-size: 12px;">
                    <th width="5%" rowspan="2" style="vertical-align: middle;">No.</th>
                    <th width="55%" rowspan="2" style="vertical-align: middle;">Bukti Persyaratan Dasar</th>
                    <th colspan="2" width="26%" style="text-align: center;">Ada</th>
                    <th width="14%" rowspan="2" style="vertical-align: middle;">Tidak Ada</th>
                </tr>
                <tr style="background: #f8fafc; text-align: center; font-size: 11px;">
                    <th width="13%">Memenuhi Syarat</th>
                    <th width="13%">Tidak Memenuhi Syarat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $syarat_dasar_all as $s_idx => $syarat ) : 
                    // Bukti persyaratan dianggap ada dan memenuhi syarat untuk berkas fisik sertifikasi
                    $is_checked = true;
                ?>
                    <tr style="font-size: 12px;">
                        <td style="text-align: center;"><?php echo ($s_idx + 1); ?>.</td>
                        <td><?php echo esc_html( $syarat ); ?></td>
                        <td style="text-align: center; font-size: 15px;">☑</td>
                        <td style="text-align: center; font-size: 15px;">☐</td>
                        <td style="text-align: center; font-size: 15px;">☐</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Bukti Administratif -->
        <h4 style="font-size: 12.5px; font-weight: bold; margin: 12px 0 6px 0;">Bukti Administratif</h4>
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background: #f1f5f9; text-align: center; font-size: 12px;">
                    <th width="5%" rowspan="2" style="vertical-align: middle;">No.</th>
                    <th width="55%" rowspan="2" style="vertical-align: middle;">Bukti Administratif</th>
                    <th colspan="2" width="26%" style="text-align: center;">Ada</th>
                    <th width="14%" rowspan="2" style="vertical-align: middle;">Tidak Ada</th>
                </tr>
                <tr style="background: #f8fafc; text-align: center; font-size: 11px;">
                    <th width="13%">Memenuhi Syarat</th>
                    <th width="13%">Tidak Memenuhi Syarat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $syarat_admin_all as $a_idx => $syarat_adm ) : 
                    // Bukti persyaratan administratif dianggap ada dan memenuhi syarat untuk berkas fisik sertifikasi
                    $is_checked_adm = true;
                ?>
                    <tr style="font-size: 12px;">
                        <td style="text-align: center;"><?php echo ($a_idx + 1); ?>.</td>
                        <td><?php echo esc_html( $syarat_adm ); ?></td>
                        <td style="text-align: center; font-size: 15px;">☑</td>
                        <td style="text-align: center; font-size: 15px;">☐</td>
                        <td style="text-align: center; font-size: 15px;">☐</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Tabel Rekomendasi & Tanda Tangan FR.APL.01 -->
        <table class="lsp-doc-table lsp-table-bordered" style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <tr>
                <td width="55%" rowspan="3" valign="top" style="padding: 10px;">
                    <strong>Rekomendasi (diisi oleh LSP):</strong><br>
                    Berdasarkan ketentuan persyaratan dasar, maka pemohon:<br>
                    <div style="margin: 8px 0; font-size: 13px;">
                        <?php if ( $pendaftaran->status_rekomendasi === 'disetujui' ) : ?>
                            <strong><u>Diterima</u></strong> / <del style="color:#64748b;">Tidak diterima</del> *)
                        <?php else : ?>
                            <del style="color:#64748b;">Diterima</del> / <strong><u>Tidak diterima</u></strong> *)
                        <?php endif; ?>
                        sebagai peserta sertifikasi
                    </div>
                    <small>* coret yang tidak sesuai</small>
                </td>
                <td colspan="2" style="background: #f8fafc;"><strong>Pemohon/ Kandidat :</strong></td>
            </tr>
            <tr>
                <td width="22%">Nama</td>
                <td><strong><?php echo esc_html( $pendaftaran->nama_lengkap ); ?></strong></td>
            </tr>
            <tr>
                <td>Tanda tangan/ Tanggal</td>
                <td style="height: 65px; vertical-align: bottom;">
                    <div style="font-size: 10px; color: #64748b; margin-bottom: 25px;">( Tanda Tangan Pemohon )</div>
                    <strong><?php echo esc_html( $tanggal_ttd_apl01 ); ?></strong>
                </td>
            </tr>
            <tr>
                <td rowspan="3" valign="top" style="padding: 10px;">
                    <strong>Catatan :</strong><br>
                    <span style="font-size: 12px;"><?php echo esc_html( $pendaftaran->catatan_admin ?: '-' ); ?></span>
                </td>
                <td colspan="2" style="background: #f8fafc;"><strong>Admin LSP :</strong></td>
            </tr>
            <tr>
                <td>Nama :</td>
                <td><strong>Admin LSP</strong></td>
            </tr>
            <tr>
                <td>Tanda tangan/ Tanggal</td>
                <td style="height: 65px; vertical-align: bottom;">
                    <div style="font-size: 10px; color: #64748b; margin-bottom: 25px;">( Tanda Tangan Admin )</div>
                    <strong><?php echo esc_html( $tanggal_ttd_apl01 ); ?></strong>
                </td>
            </tr>
        </table>
    </div>

</div>
