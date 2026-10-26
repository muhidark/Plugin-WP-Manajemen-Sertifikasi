# LAPORAN IMPLEMENTASI PHASE IA-01-03B
## PENGELOMPOKAN UNIT KOMPETENSI BERDASARKAN KELOMPOK PEKERJAAN
### Standar Format Formulir: FR.IA.01, FR.IA.02, dan FR.IA.03

---

### 1. RINGKASAN EKSEKUTIF
Phase IA-01-03B telah berhasil diselesaikan secara menyeluruh dengan tingkat kelulusan pengujian **100% (19/19 pengujian baru lulus dan 173/173 total suite regresi plugin lulus)**.

Sistem pengelompokan unit kompetensi berbasis Kelompok Pekerjaan ini dibangun khusus untuk formulir:
1. **FR.IA.01 — Ceklis Observasi Aktivitas Praktik**
2. **FR.IA.02 — Tugas Praktik Demonstrasi**
3. **FR.IA.03 — Pertanyaan Mendukung Observasi (PMO)**

Sesuai ketentuan BNSP:
- **Kelompok Pekerjaan menjadi Kolom Pertama** dengan struktur `rowspan` yang mencakup unit-unit anggota kelompok.
- Satu kelompok dapat memuat satu atau lebih unit kompetensi.
- Satu unit kompetensi diperbolehkan digunakan dalam lebih dari satu kelompok pekerjaan (multi-group reuse) tanpa menduplikasi data master.
- Semua unit kompetensi dari Master Skema wajib tercakup minimal dalam satu kelompok pekerjaan (Cakupan 100%).
- Sistem menyediakan indikator cakupan otomatis dan menampilkan kotak peringatan (warning banner) apabila ada unit yang belum dikelompokkan.
- Seluruh formulir lain (`FR.AK.02`, `FR.IA.04A`, `FR.IA.04B`, `FR.IA.05B`, `FR.IA.05C`, dll.) **tetap mempertahankan format tabel 3 kolom (No., Kode Unit, Judul Unit) tanpa kolom Kelompok Pekerjaan**.

---

### 2. ARSITEKTUR & KOMPONEN TEKNIS YANG DIIMPLEMENTASIKAN

#### A. Kelas Pengelola: `includes/class-kelompok-pekerjaan.php`
- `LSP_Cert_Kelompok_Pekerjaan::init()`: Registrasi hook admin dan action handler `lsp_save_kelompok_pekerjaan`.
- `LSP_Cert_Kelompok_Pekerjaan::get_kelompok( $skema_id )`: Mengambil daftar kelompok pekerjaan terurut berdasarkan `urutan` dari option WordPress `lsp_cert_kelompok_pekerjaan_skema_{$skema_id}`.
- `LSP_Cert_Kelompok_Pekerjaan::save_kelompok( $skema_id, $kelompok_list )`: Sanitasi nama kelompok, key ID, unit codes, pengurutan, dan penyimpanan data terisolasi per skema.
- `LSP_Cert_Kelompok_Pekerjaan::validate_coverage( $skema_id )`: 
  - Memetakan unit master dari `LSP_Cert_Integration::get_unit_kompetensi( $skema_id )`.
  - Menghitung `total_master`, `total_grouped_unique`, `total_unassigned`, dan `coverage_percent`.
  - Mengidentifikasi array `unassigned_units` secara spesifik (kode dan judul unit yang belum masuk kelompok).
  - Menentukan flag boolean `is_complete` (true jika 100% dan unassigned = 0).
- `LSP_Cert_Kelompok_Pekerjaan::get_grouped_units_for_print( $skema_id )`: Menghitung `rowspan` per kelompok pekerjaan, mencocokkan kode unit ke judul master, dan menghitung penomoran urut fisik (`no`) berkesinambungan (1 s.d. N).
- `LSP_Cert_Kelompok_Pekerjaan::render_unit_table_html( $skema_id, $echo = true )`: Merender tabel HTML 4-kolom resmi BNSP dengan atribut `rowspan` pada kolom pertama dan menampilkan kotak peringatan cakupan apabila ada unit belum terkelompok.
- `LSP_Cert_Kelompok_Pekerjaan::handle_save_action()`: Validasi kapabilitas `manage_options`, verifikasi CSRF nonce `lsp_kelompok_save_nonce`, sanitasi input, dan redirect status.
- `LSP_Cert_Kelompok_Pekerjaan::admin_page()`: Menyiapkan data skema dan memanggil template antarmuka admin.

#### B. Antarmuka Admin: `admin/views/kelompok-pekerjaan-settings.php`
- Terintegrasi pada menu admin: **Sertifikasi > Kelompok Pekerjaan**.
- Dropdown pemilih skema aktif yang langsung memuat data master unit dari skema terkait.
- 4 Kartu Metrik Cakupan Interaktif:
  1. *Total Unit Master Skema*
  2. *Unit Terkelompok (Unik)*
  3. *Unit Belum Dikelompokkan* (Warna merah jika > 0, hijau jika 0)
  4. *Status Cakupan Dokumen* (Badge 100% Lengkap / Warning % Belum Lengkap)
- Kotak peringatan detail jika cakupan < 100% yang menampilkan daftar unit yang belum dikelompokkan.
- Repeater dinamis Kelompok Pekerjaan (Nama Kelompok, Urutan, Checkbox Unit Kompetensi Master).
- Tombol penambahan kelompok baru berbasis client-side JavaScript template.
- Panel Pratinjau Langsung (*Live Print Preview*) tabel A4 siap cetak.

#### C. Integrasi Plugin & Admin Menu
- Diinisialisasi pada `lsp-certification-management.php` via `LSP_Cert_Kelompok_Pekerjaan::init()`.
- Didaftarkan sebagai submenu pada `admin/class-admin.php`.

---

### 3. STRUKTUR TABEL CETAK (STANDAR BNSP FR.IA.01 / FR.IA.02 / FR.IA.03)

| Kelompok Pekerjaan (Col 1 - 32%) | No. (Col 2 - 6%) | Kode Unit (Col 3 - 24%) | Judul Unit (Col 4 - 38%) |
| :--- | :---: | :---: | :--- |
| **Kelompok Pekerjaan 1: Perencanaan Konten** *(rowspan=3)* | 1 | M.70MKT00.009.2 | Mengidentifikasi Elemen Pemasaran Perusahaan |
| *(rowspan sambungan)* | 2 | M.70MKT00.010.2 | Mengembangkan Perencanaan Pemasaran |
| *(rowspan sambungan)* | 3 | M.70MKT00.011.2 | Melakukan Pendekatan pada Konsumen Baru |
| **Kelompok Pekerjaan 2: Eksekusi & Optimasi** *(rowspan=2)* | 4 | M.70MKT00.010.2 *(Reuse)* | Mengembangkan Perencanaan Pemasaran |
| *(rowspan sambungan)* | 5 | M.70MKT00.012.2 | Mengelola Layanan Nasabah/Pelanggan |

---

### 4. HASIL PENGUJIAN OTOMATIS (TEST SUITE VERIFICATION)

File pengujian: `scratch/test_ia01_03b_suite.php`

| Kode Test | Deskripsi Skenario Pengujian | Hasil |
| :--- | :--- | :---: |
| **IA0103B-01** | Kelas `LSP_Cert_Kelompok_Pekerjaan` terdefinisi | **PASS** |
| **IA0103B-02** | Semua metode inti `LSP_Cert_Kelompok_Pekerjaan` tersedia | **PASS** |
| **IA0103B-03** | Data Master Skema 26 (SMM) dan Skema 117 (Content Creator) terhubung | **PASS** |
| **IA0103B-04** | Cakupan awal kosong terdeteksi dengan tepat (0%, belum lengkap) | **PASS** |
| **IA0103B-05** | Penyimpanan kelompok parsial berhasil disimpan dan diambil kembali | **PASS** |
| **IA0103B-06** | Validasi cakupan parsial mendeteksi unit belum terkelompok dan peringatan aktif | **PASS** |
| **IA0103B-07** | HTML tabel pada cakupan parsial menampilkan kotak peringatan (warning box) | **PASS** |
| **IA0103B-08** | Penyimpanan multi-kelompok dengan unit ganda (reuse) berhasil | **PASS** |
| **IA0103B-09** | Unit pada multiple kelompok dihitung 1x untuk kalkulasi persentase cakupan | **PASS** |
| **IA0103B-10** | Data siap cetak menghitung rowspan dan baris berulang dengan tepat (8 baris) | **PASS** |
| **IA0103B-11** | Penomoran urut baris berjalan kontinu dari kelompok 1 sampai akhir (1..8) | **PASS** |
| **IA0103B-12** | HTML tabel 100% lengkap TIDAK menampilkan kotak peringatan | **PASS** |
| **IA0103B-13** | HTML tabel memuat struktur header 4 kolom resmi | **PASS** |
| **IA0103B-14** | HTML tabel menggunakan rowspan untuk kolom pertama (Kelompok Pekerjaan) | **PASS** |
| **IA0103B-15** | Konfigurasi Skema 26 terisolasi dan tidak mempengaruhi Skema 117 | **PASS** |
| **IA0103B-16** | [REGRESI] FR.AK.02 tetap menggunakan tabel 3-kolom TANPA Kelompok Pekerjaan | **PASS** |
| **IA0103B-17** | [REGRESI] FR.IA.04A dan FR.IA.04B tetap menggunakan tabel 3-kolom TANPA Kelompok Pekerjaan | **PASS** |
| **IA0103B-18** | [REGRESI] FR.IA.05B dan FR.IA.05C tetap menggunakan tabel 3-kolom TANPA Kelompok Pekerjaan | **PASS** |
| **IA0103B-19** | [REGRESI] FR.MAPA.02 tetap berfungsi normal | **PASS** |

---

### 5. HASIL PENGUJIAN REGRESI TOTAL (ZERO REGRESSION)

| Test Suite | Modul / Dokumen | Total Uji | Status |
| :--- | :--- | :---: | :---: |
| `test_ia01_03b_suite.php` | Pengelompokan Kelompok Pekerjaan (IA.01, IA.02, IA.03) | 19 / 19 | **100% PASS** |
| `test_ia04_suite.php` | FR.IA.04A & FR.IA.04B (Kegiatan Terstruktur & STAR) | 31 / 31 | **100% PASS** |
| `test_ia05_suite.php` | FR.IA.05A, FR.IA.05B, FR.IA.05C (Pilihan Ganda) | 23 / 23 | **100% PASS** |
| `test_ak05c_suite.php` | Cetak Dokumen Kegiatan & FR.AK.05 Pagination | 16 / 16 | **100% PASS** |
| `test_ak03_06_suite.php` | FR.AK.03, FR.AK.04, FR.AK.05, FR.AK.06 | 28 / 28 | **100% PASS** |
| `test_ak02b_suite.php` | FR.AK.02 Rekaman Asesmen Kompetensi | 22 / 22 | **100% PASS** |
| `test_ak01c_suite.php` | Kebijakan Seragam TUK Sewaktu & Dokumen APL/MAPA/AK | 18 / 18 | **100% PASS** |
| `test_mapa02_suite.php` | FR.MAPA.02 Peta Instrumen Asesmen | 16 / 16 | **100% PASS** |
| **TOTAL KESELURUHAN** | **Seluruh Suite Plugin LSP Certification Management** | **173 / 173** | **100% PASS** |

---

### 6. KESIMPULAN
Phase IA-01-03B selesai dengan sempurna sesuai prinsip BNSP dan batasan arsitektur plugin. Generator siap digunakan sebagai fondasi bersama untuk implementasi formulir FR.IA.01, FR.IA.02, dan FR.IA.03 berikutnya.
