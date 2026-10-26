<?php
/**
 * Master Document Layout & Print Stylesheet
 * Mendukung FR.APL.01, FR.APL.02, dan 3 Versi FR.MAPA.01
 *
 * @package LSP_Certification_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$doc_title = ! empty( $pendaftaran ) 
    ? 'Dokumen Asesmen #' . $pendaftaran->id . ' - ' . $pendaftaran->nama_lengkap
    : 'FR.MAPA.02 - ' . ( $skema['title'] ?? ( 'Kegiatan #' . $kegiatan->id ) );
$is_mapa = ( strpos( $doc_type, 'mapa' ) !== false );
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( $doc_title ); ?> &bull; LSP Komunikasi Indonesia</title>
    <style>
        /* Base Screen Styling */
        * { box-sizing: border-box; }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            background-color: #0f172a;
            margin: 0;
            padding: 0;
            color: #1e293b;
            font-size: 12.5px;
            line-height: 1.4;
        }

        /* Non-Printable Top Action Bar */
        .lsp-print-topbar {
            background: #1e293b;
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 99999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            border-bottom: 2px solid #0284c7;
            flex-wrap: wrap;
            gap: 10px;
        }
        .lsp-print-topbar-title {
            font-size: 13.5px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .lsp-badge-doc {
            background: #0284c7;
            color: #fff;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .lsp-print-topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .lsp-btn-print {
            background: #16a34a;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .lsp-btn-print:hover { background: #15803d; }
        .lsp-btn-back {
            background: #475569;
            color: #ffffff;
            border: none;
            padding: 8px 14px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
        }
        .lsp-btn-back:hover { background: #334155; }

        .lsp-doc-switcher select,
        .lsp-kat-switcher select {
            padding: 7px 12px;
            border-radius: 4px;
            border: 1px solid #475569;
            background: #334155;
            color: #fff;
            font-size: 12.5px;
            max-width: 320px;
        }
        .lsp-kat-switcher {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #0f172a;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #38bdf8;
        }
        .lsp-kat-switcher label {
            font-size: 11.5px;
            color: #38bdf8;
            font-weight: bold;
        }

        /* Printable Paper Container (A4 Preview: 21 x 29.7 cm) */
        .lsp-paper-container {
            width: 210mm;
            margin: 25px auto 50px;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        .lsp-page-sheet {
            width: 210mm;
            min-height: 297mm;
            box-sizing: border-box;
            background: #ffffff;
            padding: 15mm 15mm;
            box-shadow: 0 4px 18px rgba(0,0,0,0.35);
            border-radius: 2px;
            position: relative;
        }

        /* Tables & Typography */
        .lsp-doc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            margin-bottom: 12px;
        }

        /* Table Bordered */
        .lsp-table-bordered td, .lsp-table-bordered th {
            border: 1px solid #000000;
            padding: 4.5px 7px;
            vertical-align: top;
        }

        /* Table Borderless (Khusus Data Diri & Data Pekerjaan) */
        .lsp-table-borderless {
            border: none !important;
            border-collapse: collapse !important;
        }
        .lsp-table-borderless td, .lsp-table-borderless th {
            border: none !important;
            padding: 3.5px 5px !important;
            vertical-align: top;
            background: transparent !important;
        }

        .lsp-tr-head {
            background-color: #f1f5f9;
            font-weight: 700;
        }

        .lsp-ak02-avoid-break,
        .lsp-ak03-avoid-break,
        .lsp-ak04-avoid-break,
        .lsp-ak05-avoid-break,
        .lsp-ak06-avoid-break,
        .lsp-ia04a-avoid-break,
        .lsp-ia04b-avoid-break,
        .lsp-ia05a-avoid-break,
        .lsp-ia05b-avoid-break,
        .lsp-ia05c-avoid-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* Page break helper */
        .lsp-page-break {
            page-break-before: always;
            break-before: page;
            height: 0;
            margin: 0;
            padding: 0;
            border: none;
            display: block;
        }

        /* Print Media Styles (Standar Kertas A4: 21 x 29.7 cm) */
        @media print {
            html, body {
                width: 210mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 9.8pt;
            }
            .lsp-no-print {
                display: none !important;
            }
            .lsp-paper-container {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                gap: 0 !important;
                display: block !important;
            }
            .lsp-page-sheet {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                background: transparent !important;
                page-break-after: always !important;
                break-after: page !important;
            }
            .lsp-page-sheet:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }
            .lsp-page-break {
                page-break-before: always !important;
                break-before: page !important;
                height: 0 !important;
                display: block !important;
            }
            .lsp-table-borderless, .lsp-table-borderless td, .lsp-table-borderless th {
                border: none !important;
                border-color: transparent !important;
            }
            .lsp-table-bordered td, .lsp-table-bordered th {
                border: 1px solid #000000 !important;
            }
            .lsp-ak02-avoid-break,
            .lsp-ak03-avoid-break,
            .lsp-ak04-avoid-break,
            .lsp-ak05-avoid-break,
            .lsp-ak06-avoid-break {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            @page {
                size: 210mm 297mm; /* A4: 21 x 29,7 cm */
                margin: 15mm 15mm 15mm 15mm;
            }
        }
    </style>
</head>
<body>

    <!-- Non-Printable Top Action Bar -->
    <div class="lsp-print-topbar lsp-no-print">
        <div class="lsp-print-topbar-title">
            <span>🖨️ Cetak Dokumen Sertifikasi</span>
            <?php if ( ! empty( $pendaftaran ) ) : ?>
                <span class="lsp-badge-doc">Asesi: <?php echo esc_html( $pendaftaran->nama_lengkap ); ?></span>
            <?php else : ?>
                <span class="lsp-badge-doc">Kegiatan #<?php echo esc_html( $kegiatan->id ); ?> (<?php echo esc_html( $skema['title'] ?? '-' ); ?>)</span>
            <?php endif; ?>
        </div>

        <div class="lsp-print-topbar-actions">
            <!-- Document Switcher -->
            <div class="lsp-doc-switcher">
                <select onchange="switchDocument(this.value);">
                    <?php if ( ! empty( $pendaftaran ) ) : ?>
                    <optgroup label="Paket Berkas Terpadu">
                        <option value="bundle_all" <?php selected( $doc_type, 'bundle_all' ); ?>>
                            📦 Paket Lengkap Asesi (1 s/d 25 All-in-One)
                        </option>
                    </optgroup>
                    <optgroup label="Formulir Pendaftaran & Asesmen Mandiri">
                        <option value="apl01" <?php selected( $doc_type, 'apl01' ); ?>>
                            FR.APL.01 Permohonan Sertifikasi
                        </option>
                        <option value="apl02" <?php selected( $doc_type, 'apl02' ); ?>>
                            FR.APL.02 Asesmen Mandiri
                        </option>
                        <option value="all" <?php selected( $doc_type, 'all' ); ?>>
                            📄 Paket Lengkap (FR.APL.01 & 02)
                        </option>
                    </optgroup>
                    <?php endif; ?>
                    <optgroup label="FR.MAPA.01 - Rencana Asesmen">
                        <option value="mapa01_obs" <?php selected( $doc_type, 'mapa01_obs' ); ?>>
                            FR.MAPA.01 Observasi Langsung
                        </option>
                        <option value="mapa01_dit" <?php selected( $doc_type, 'mapa01_dit' ); ?>>
                            FR.MAPA.01 Daftar Instruksi Terstruktur (DIT)
                        </option>
                        <option value="mapa01_vp" <?php selected( $doc_type, 'mapa01_vp' ); ?>>
                            FR.MAPA.01 Verifikasi Portofolio
                        </option>
                        <option value="mapa01_all" <?php selected( $doc_type, 'mapa01_all' ); ?>>
                            📑 FR.MAPA.01 Semua Versi (Observasi, DIT, Portofolio)
                        </option>
                    </optgroup>
                    <optgroup label="FR.MAPA.02 - Peta Instrumen">
                        <option value="mapa02" <?php selected( $doc_type, 'mapa02' ); ?>>
                            FR.MAPA.02 Peta Instrumen Asesmen
                        </option>
                    </optgroup>
                    <optgroup label="FR.AK - Rekaman & Pelaksanaan Asesmen">
                        <?php if ( ! empty( $pendaftaran ) ) : ?>
                        <option value="ak01" <?php selected( $doc_type, 'ak01' ); ?>>
                            FR.AK.01 Persetujuan Asesmen & Kerahasiaan
                        </option>
                        <option value="ak02" <?php selected( $doc_type, 'ak02' ); ?>>
                            FR.AK.02 Rekaman Asesmen Kompetensi
                        </option>
                        <option value="ak03" <?php selected( $doc_type, 'ak03' ); ?>>
                            FR.AK.03 Umpan Balik & Catatan Asesmen
                        </option>
                        <option value="ak04" <?php selected( $doc_type, 'ak04' ); ?>>
                            FR.AK.04 Banding Asesmen
                        </option>
                        <option value="ak07" <?php selected( $doc_type, 'ak07' ); ?>>
                            FR.AK.07 Penyesuaian Wajar &amp; Beralasan
                        </option>
                        <?php endif; ?>
                        <option value="ak05" <?php selected( $doc_type, 'ak05' ); ?>>
                            FR.AK.05 Laporan Asesmen
                        </option>
                        <option value="ak06" <?php selected( $doc_type, 'ak06' ); ?>>
                            FR.AK.06 Meninjau Proses Asesmen
                        </option>
                    </optgroup>
                    <optgroup label="FR.IA.04 - Kegiatan Terstruktur (DIT)">
                        <option value="ia04a" <?php selected( $doc_type, 'ia04a' ); ?>>
                            FR.IA.04A DIT Proyek Singkat
                        </option>
                        <option value="ia04b" <?php selected( $doc_type, 'ia04b' ); ?>>
                            FR.IA.04B Penilaian Proyek Singkat
                        </option>
                    </optgroup>
                    <optgroup label="FR.IA.01 - 03 (Observasi &amp; Praktik)">
                        <option value="ia01" <?php selected( $doc_type, 'ia01' ); ?>>
                            FR.IA.01 Ceklis Observasi Praktik
                        </option>
                        <option value="ia02" <?php selected( $doc_type, 'ia02' ); ?>>
                            FR.IA.02 Tugas Praktik Demonstrasi
                        </option>
                        <option value="ia03" <?php selected( $doc_type, 'ia03' ); ?>>
                            FR.IA.03 Pertanyaan Mendukung Observasi
                        </option>
                    </optgroup>
                    <optgroup label="FR.IA.05 - Pilihan Ganda">
                        <option value="ia05a" <?php selected( $doc_type, 'ia05a' ); ?>>
                            FR.IA.05A Soal Pilihan Ganda
                        </option>
                        <?php if ( current_user_can( 'manage_options' ) || current_user_can( 'asesor' ) || LSP_Cert_Integration::is_asesor( get_current_user_id() ) ) : ?>
                        <option value="ia05b" <?php selected( $doc_type, 'ia05b' ); ?>>
                            FR.IA.05B Kunci Jawaban PG (Terbatas)
                        </option>
                        <?php endif; ?>
                        <option value="ia05c" <?php selected( $doc_type, 'ia05c' ); ?>>
                            FR.IA.05C Lembar Jawaban Pilihan Ganda
                        </option>
                    </optgroup>
                </select>
            </div>

            <!-- Kontrol Pemilih Latar Belakang Asesi (Khusus FR.MAPA.01) -->
            <?php if ( $is_mapa ) : ?>
                <?php if ( $doc_type === 'mapa01_obs' || $doc_type === 'mapa01_dit' ) : ?>
                    <div class="lsp-kat-switcher">
                        <label for="sel_kat_obs">Latar Belakang:</label>
                        <select id="sel_kat_obs" onchange="updateMapaKategoriSingle(this.value, 1);">
                            <option value="0" <?php selected( $selected_kat_obs, 0 ); ?>>-- Belum Dipilih (Checkbox Kosong) --</option>
                            <option value="1" <?php selected( $selected_kat_obs, 1 ); ?>>Kategori 1: Pelatihan/Pendidikan Kurikulum Mampu Telusur</option>
                            <option value="2" <?php selected( $selected_kat_obs, 2 ); ?>>Kategori 2: Pelatihan/Pendidikan Kurikulum Belum Berbasis Kompetensi</option>
                        </select>
                    </div>
                <?php elseif ( $doc_type === 'mapa01_vp' ) : ?>
                    <div class="lsp-kat-switcher">
                        <label for="sel_kat_vp">Latar Belakang:</label>
                        <select id="sel_kat_vp" onchange="updateMapaKategoriSingle(this.value, 1);">
                            <option value="0" <?php selected( $selected_kat_vp, 0 ); ?>>-- Belum Dipilih (Checkbox Kosong) --</option>
                            <option value="3" <?php selected( $selected_kat_vp, 3 ); ?>>Kategori 3: Pekerja Berpengalaman Industri Mampu Telusur</option>
                            <option value="4" <?php selected( $selected_kat_vp, 4 ); ?>>Kategori 4: Pekerja Berpengalaman Industri Belum Berbasis Kompetensi</option>
                            <option value="5" <?php selected( $selected_kat_vp, 5 ); ?>>Kategori 5: Pelatihan / Belajar Mandiri atau Otodidak</option>
                        </select>
                    </div>
                <?php elseif ( $doc_type === 'mapa01_all' ) : ?>
                    <div class="lsp-kat-switcher">
                        <label>Observasi/DIT:</label>
                        <select id="sel_kat_all_obs" onchange="updateMapaKategoriMulti(this.value, 'obs');">
                            <option value="0" <?php selected( $selected_kat_obs, 0 ); ?>>-- Kosong --</option>
                            <option value="1" <?php selected( $selected_kat_obs, 1 ); ?>>Kat 1 (Mampu Telusur)</option>
                            <option value="2" <?php selected( $selected_kat_obs, 2 ); ?>>Kat 2 (Belum Berbasis)</option>
                        </select>
                    </div>
                    <div class="lsp-kat-switcher">
                        <label>Portofolio:</label>
                        <select id="sel_kat_all_vp" onchange="updateMapaKategoriMulti(this.value, 'vp');">
                            <option value="0" <?php selected( $selected_kat_vp, 0 ); ?>>-- Kosong --</option>
                            <option value="3" <?php selected( $selected_kat_vp, 3 ); ?>>Kat 3 (Pekerja Mampu Telusur)</option>
                            <option value="4" <?php selected( $selected_kat_vp, 4 ); ?>>Kat 4 (Pekerja Belum Berbasis)</option>
                            <option value="5" <?php selected( $selected_kat_vp, 5 ); ?>>Kat 5 (Otodidak/Mandiri)</option>
                        </select>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <button type="button" class="lsp-btn-print" onclick="window.print();">
                🖨️ Cetak / Print Sekarang
            </button>

            <?php if ( ! empty( $pendaftaran ) ) : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-pendaftaran&action=view&id=' . $pendaftaran->id ) ); ?>" class="lsp-btn-back">
                    &larr; Kembali ke Detail Peserta
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=lsp-kegiatan' ) ); ?>" class="lsp-btn-back">
                    &larr; Kembali ke Daftar Kegiatan
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Printable Paper Area -->
    <div class="lsp-paper-container">
        <?php if ( 'bundle_all' === $doc_type ) : ?>
            <!-- ============================================== -->
            <!-- BUNDLE LENGKAP SELURUH DOKUMEN ASESI (1 s/d 25)-->
            <!-- ============================================== -->
            <!-- 1. FR.APL.01 -->
            <div class="lsp-page-sheet lsp-page-apl01">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-apl01.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 2. FR.APL.02 -->
            <div class="lsp-page-sheet lsp-page-apl02">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-apl02.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 3. FR.MAPA.01 (Observasi Langsung) -->
            <div class="lsp-page-sheet lsp-page-mapa lsp-sheet-mapa-1">
                <?php 
                $metode = 'observasi';
                $mapa_idx = 1;
                $selected_kategori = $selected_kat_obs;
                include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-mapa01.php'; 
                ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 4. FR.MAPA.02 -->
            <div class="lsp-page-sheet lsp-page-mapa lsp-page-mapa02">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-mapa02.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 5. FR.AK.01 -->
            <div class="lsp-page-sheet lsp-page-ak01">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak01.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 6. FR.AK.04 -->
            <div class="lsp-page-sheet lsp-page-ak04">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak04.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 7. FR.AK.07 (Penyesuaian Wajar) -->
            <div class="lsp-page-sheet lsp-page-ak07">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak07.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 10. FR.IA.01 (CL) -->
            <div class="lsp-page-sheet lsp-page-ia01">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia01.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 11. FR.IA.02 (TPD) -->
            <div class="lsp-page-sheet lsp-page-ia02">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia02.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 12. FR.IA.03 (PMO) -->
            <div class="lsp-page-sheet lsp-page-ia03">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia03.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 13.a FR.IA.04A (DIT STAR) -->
            <div class="lsp-page-sheet lsp-page-ia04a">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia04a.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 13.b FR.IA.04B (Penilaian Proyek) -->
            <div class="lsp-page-sheet lsp-page-ia04b">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia04b.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 14.a FR.IA.05A (Soal PG) -->
            <div class="lsp-page-sheet lsp-page-ia05a">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia05a.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 14.c FR.IA.05C (Lembar Jawaban PG Asesi) -->
            <div class="lsp-page-sheet lsp-page-ia05c">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia05c.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 21. FR.AK.02 (Rekaman Asesmen) -->
            <div class="lsp-page-sheet lsp-page-ak02">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak02.php'; ?>
            </div>
            <div class="lsp-page-break"></div>

            <!-- 22. FR.AK.03 (Umpan Balik Asesmen) -->
            <div class="lsp-page-sheet lsp-page-ak03">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak03.php'; ?>
            </div>

        <?php elseif ( 'ia01' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ia01">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia01.php'; ?>
            </div>
        <?php elseif ( 'ia02' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ia02">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia02.php'; ?>
            </div>
        <?php elseif ( 'ia03' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ia03">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia03.php'; ?>
            </div>
        <?php elseif ( 'ia04a' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ia04a">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia04a.php'; ?>
            </div>
        <?php elseif ( 'ia04b' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ia04b">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia04b.php'; ?>
            </div>
        <?php elseif ( 'ia05c' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ia05c">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia05c.php'; ?>
            </div>
        <?php elseif ( 'ia05b' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ia05b">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia05b.php'; ?>
            </div>
        <?php elseif ( 'ia05a' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ia05a">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ia05a.php'; ?>
            </div>
        <?php elseif ( 'ak06' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ak06">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak06.php'; ?>
            </div>
        <?php elseif ( 'ak05' === $doc_type ) : ?>
            <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak05.php'; ?>
        <?php elseif ( 'ak04' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ak04">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak04.php'; ?>
            </div>
        <?php elseif ( 'ak07' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ak07">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak07.php'; ?>
            </div>
        <?php elseif ( 'ak03' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ak03">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak03.php'; ?>
            </div>
        <?php elseif ( 'ak02' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ak02">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak02.php'; ?>
            </div>
        <?php elseif ( 'ak01' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-ak01">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-ak01.php'; ?>
            </div>
        <?php elseif ( 'mapa02' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-mapa lsp-page-mapa02">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-mapa02.php'; ?>
            </div>
        <?php elseif ( 'apl01' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-apl01">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-apl01.php'; ?>
            </div>
        <?php elseif ( 'apl02' === $doc_type ) : ?>
            <div class="lsp-page-sheet lsp-page-apl02">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-apl02.php'; ?>
            </div>
        <?php elseif ( 'mapa01_obs' === $doc_type ) : ?>
            <!-- Versi 1: Observasi Langsung -->
            <div class="lsp-page-sheet lsp-page-mapa lsp-sheet-mapa-1">
                <?php 
                $metode = 'observasi';
                $mapa_idx = 1;
                $selected_kategori = $selected_kat_obs;
                include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-mapa01.php'; 
                ?>
            </div>
        <?php elseif ( 'mapa01_dit' === $doc_type ) : ?>
            <!-- Versi 2: Daftar Instruksi Terstruktur (DIT) -->
            <div class="lsp-page-sheet lsp-page-mapa lsp-sheet-mapa-1">
                <?php 
                $metode = 'dit';
                $mapa_idx = 1;
                $selected_kategori = $selected_kat_dit;
                include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-mapa01.php'; 
                ?>
            </div>
        <?php elseif ( 'mapa01_vp' === $doc_type ) : ?>
            <!-- Versi 3: Verifikasi Portofolio -->
            <div class="lsp-page-sheet lsp-page-mapa lsp-sheet-mapa-1">
                <?php 
                $metode = 'portofolio';
                $mapa_idx = 1;
                $selected_kategori = $selected_kat_vp;
                include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-mapa01.php'; 
                ?>
            </div>
        <?php elseif ( 'mapa01_all' === $doc_type ) : ?>
            <!-- Ketiga Versi FR.MAPA.01 Sekaligus di Halaman Terpisah -->
            <div class="lsp-page-sheet lsp-page-mapa lsp-sheet-mapa-1">
                <?php 
                $metode = 'observasi';
                $mapa_idx = 1;
                $selected_kategori = $selected_kat_obs;
                include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-mapa01.php'; 
                ?>
            </div>
            <div class="lsp-page-break"></div>
            <div class="lsp-page-sheet lsp-page-mapa lsp-sheet-mapa-2">
                <?php 
                $metode = 'dit';
                $mapa_idx = 2;
                $selected_kategori = $selected_kat_dit;
                include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-mapa01.php'; 
                ?>
            </div>
            <div class="lsp-page-break"></div>
            <div class="lsp-page-sheet lsp-page-mapa lsp-sheet-mapa-3">
                <?php 
                $metode = 'portofolio';
                $mapa_idx = 3;
                $selected_kategori = $selected_kat_vp;
                include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-mapa01.php'; 
                ?>
            </div>
        <?php else : ?>
            <!-- Default: Paket Lengkap APL 01 & 02 -->
            <div class="lsp-page-sheet lsp-page-apl01">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-apl01.php'; ?>
            </div>
            <div class="lsp-page-break"></div>
            <div class="lsp-page-sheet lsp-page-apl02">
                <?php include LSP_CERT_PLUGIN_DIR . 'templates/docs/form-apl02.php'; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Script Pengaturan Latar Belakang & Switcher Dokumen -->
    <script>
    function switchDocument(doc) {
        var url = new URL(window.location.href);
        url.searchParams.set('doc', doc);
        // Hapus query kat saat ganti dokumen agar divalidasi segar sesuai dokumen
        url.searchParams.delete('kat');
        window.location.href = url.toString();
    }

    function setKategoriMarks(mapaIdx, katNum) {
        katNum = parseInt(katNum, 10) || 0;
        for (var i = 1; i <= 5; i++) {
            var el = document.getElementById('mapa_' + mapaIdx + '_kat_' + i + '_mark');
            if (el) {
                el.textContent = (i === katNum) ? '☑' : '☐';
            }
        }
    }

    function updateMapaKategoriSingle(katNum, mapaIdx) {
        katNum = parseInt(katNum, 10) || 0;
        setKategoriMarks(mapaIdx, katNum);
        
        // Update URL query string tanpa reload halaman
        var url = new URL(window.location.href);
        if (katNum > 0) {
            url.searchParams.set('kat', katNum);
        } else {
            url.searchParams.delete('kat');
        }
        window.history.replaceState({}, '', url.toString());
    }

    function updateMapaKategoriMulti(katNum, group) {
        katNum = parseInt(katNum, 10) || 0;
        var url = new URL(window.location.href);

        if (group === 'obs') {
            // Mapa 1 (Observasi) & Mapa 2 (DIT)
            setKategoriMarks(1, katNum);
            setKategoriMarks(2, katNum);
            if (katNum > 0) {
                url.searchParams.set('kat_obs', katNum);
                url.searchParams.set('kat_dit', katNum);
            } else {
                url.searchParams.delete('kat_obs');
                url.searchParams.delete('kat_dit');
            }
        } else if (group === 'vp') {
            // Mapa 3 (Portofolio)
            setKategoriMarks(3, katNum);
            if (katNum > 0) {
                url.searchParams.set('kat_vp', katNum);
            } else {
                url.searchParams.delete('kat_vp');
            }
        }
        window.history.replaceState({}, '', url.toString());
    }
    </script>

</body>
</html>
