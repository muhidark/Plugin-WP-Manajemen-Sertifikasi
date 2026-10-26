# LAPORAN PERBAIKAN STRUKTUR TABEL UNIT KOMPETENSI (PHASE AK-02D)
**Dokumen**: `FR.AK.02 — Rekaman Asesmen Kompetensi`  
**Proyek**: LSP Certification Management (`lsp-certification-management`)  
**Mode**: MINIMAL CORRECTION + REGRESSION TEST  
**Tanggal**: 24 Oktober 2026  
**Status**: SELESAI & TERVERIFIKASI — 22/22 PENGUJIAN PASS

---

## 1. DESKRIPSI PERUBAHAN

Berdasarkan evaluasi visual cetak, kolom *"Kelompok Pekerjaan"* yang menggunakan `rowspan` pada Tabel Daftar Unit Kompetensi (Tabel 2) menyebabkan tampilan kolom menjadi tidak proporsional dan memakan ruang horizontal yang tidak diperlukan.

Sesuai arahan **Phase AK-02D**:
1. Kolom *"Kelompok Pekerjaan"* beserta seluruh baris sel kosong dan atribut `rowspan` telah **DIHAPUS** sepenuhnya dari Tabel 2 FR.AK.02.
2. Struktur Tabel 2 disederhanakan menjadi **3 kolom proporsional**:
   - **No.**: `8%` (Rata tengah)
   - **Kode Unit**: `25%` (Rata tengah, `white-space: nowrap;` mencegah teks terpotong/pindah baris)
   - **Judul Unit**: `67%` (Rata kiri, ruang luas untuk judul kompetensi panjang)
3. Tabel 3 (Matriks Bukti Asesmen) tetap dipertahankan murni tanpa perubahan dan tetap menggunakan array `$units_skkni` yang sama.

---

## 2. FILE YANG DIUBAH

- [`templates/docs/form-ak02.php`](file:///var/www/html/wordpress/wp-content/plugins/lsp-certification-management/templates/docs/form-ak02.php): Mengganti struktur `thead` dan `tbody` Tabel 2 ke format 3 kolom bersih tanpa `rowspan`.
- `scratch/test_ak02b_suite.php`: Memperbarui butir uji `AK02-11` dan `AK02-20` untuk memvalidasi ketiadaan kolom Kelompok Pekerjaan dan penerapan rasio 8% / 25% / 67%.

---

## 3. HASIL VERIFIKASI

- **Suite AK-02B** (`test_ak02b_suite.php`): **22/22 PASS**
  - `AK02-10`: Seluruh unit kompetensi tampil lengkap.
  - `AK02-11`: Kolom Kelompok Pekerjaan terhapus, format 3 kolom 8%/25%/67% terverifikasi.
  - `AK02-12`: Tabel 3 tetap sinkron dengan jumlah unit.
  - `AK02-20`: Uji lintas skema (10 unit) menghasilkan tabel proporsional sempurna.
- **Suite Regresi AK-01C** (`test_ak01c_suite.php`): **18/18 PASS**
- **Suite Regresi MAPA-02** (`test_mapa02_suite.php`): **16/16 PASS**
- **Status Database & Eksternal**: Nol perubahan pada struktur database, `lsp-core-management`, maupun theme.
