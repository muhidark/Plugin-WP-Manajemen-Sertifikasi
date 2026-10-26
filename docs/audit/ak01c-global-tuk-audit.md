# LAPORAN AUDIT PHASE AK-01C
**Audit dan Penyeragaman Tampilan Jenis TUK pada Dokumen Cetak Asesmen**

- **Project**: LSP Certification Management
- **Fase**: Phase AK-01C
- **Tanggal Audit & Pengujian**: 24 Oktober 2026
- **Status Evaluasi**: COMPLETED & VERIFIED (18/18 Test Passed)

---

## 1. Daftar Seluruh Dokumen yang Diperiksa

Inventarisasi menyeluruh dilakukan terhadap seluruh berkas template dokumen cetak pada direktori `templates/docs/` serta berkas terkait:

1. **FR.APL.01** (`templates/docs/form-apl01.php`) — Formulir Permohonan Sertifikasi Kompetensi
2. **FR.APL.02** (`templates/docs/form-apl02.php`) — Formulir Asesmen Mandiri
3. **FR.MAPA.01 (Observasi)** (`templates/docs/form-mapa01.php` metode 'observasi') — Perencanaan Asesmen Observasi
4. **FR.MAPA.01 (DIT)** (`templates/docs/form-mapa01.php` metode 'dit') — Perencanaan Asesmen Daftar Instruksi Terstruktur
5. **FR.MAPA.01 (Portofolio)** (`templates/docs/form-mapa01.php` metode 'portofolio') — Perencanaan Asesmen Verifikasi Portofolio
6. **FR.MAPA.02** (`templates/docs/form-mapa02.php`) — Peta Instrumen Asesmen
7. **FR.AK.01** (`templates/docs/form-ak01.php`) — Persetujuan Asesmen dan Kerahasiaan
8. **Master Layout** (`templates/docs/document-layout.php`) — Layout Pembungkus & Stylesheet Cetak
9. **Header Kop** (`templates/docs/part-kop.php`) — Kop Surat Resmi LSP

---

## 2. Matriks Keberadaan Field Pilihan Jenis TUK

| Dokumen Cetak | Memiliki Pilihan Jenis TUK | Implementasi Sebelumnya | Perlu Perbaikan | Keterangan Rinci |
|---|:---:|---|:---:|---|
| **FR.APL.01** | **Tidak Ada** | Tidak memuat field jenis TUK | **Tidak** | Hanya memuat data pribadi, pekerjaan, unit SKKNI, bukti syarat & rekomendasi. Tidak ada elemen baru ditambahkan. |
| **FR.APL.02** | **Tidak Ada** | Tidak memuat field jenis TUK | **Tidak** | Hanya memuat tabel asesmen mandiri (K/BK) per elemen kompetensi & bukti pendukung. |
| **FR.MAPA.01 (Obs)** | **Tidak Ada** | Tidak memuat field jenis TUK | **Tidak** | Memuat pilihan lingkungan asesmen (*Tempat kerja nyata / simulasi*), bukan jenis TUK. |
| **FR.MAPA.01 (DIT)** | **Tidak Ada** | Tidak memuat field jenis TUK | **Tidak** | Memuat pilihan lingkungan asesmen (*Tempat kerja nyata / simulasi*), bukan jenis TUK. |
| **FR.MAPA.01 (VP)** | **Tidak Ada** | Tidak memuat field jenis TUK | **Tidak** | Memuat pilihan lingkungan asesmen (*Tempat kerja nyata / simulasi*), bukan jenis TUK. |
| **FR.MAPA.02** | **Tidak Ada** | Tidak memuat field jenis TUK | **Tidak** | Memuat tabel unit kompetensi & 11 instrumen asesmen terhadap Potensi Asesi 1–5. |
| **FR.AK.01** | **Ada** | Mengikuti metadata dinamis `jenis_tuk` dari master | **Ya** | Diseragamkan menjadi kebijakan tampilan tetap: `☑ Sewaktu  ☐ Tempat Kerja  ☐ Mandiri*`. |

---

## 3. Berkas yang Perlu dan Benar-benar Diubah

- **Berkas yang Perlu Diubah**: Hanya **`templates/docs/form-ak01.php`**.
- **Berkas yang Benar-benar Diubah**: **`templates/docs/form-ak01.php`**.
- **Berkas Lain**: Seluruh berkas lainnya (`form-apl01.php`, `form-apl02.php`, `form-mapa01.php`, `form-mapa02.php`, `document-layout.php`, dll.) **tidak diubah sama sekali**, menjaga stabilitas 100%.

---

## 4. Perbandingan Sebelum dan Sesudah (`form-ak01.php`)

### Sebelum Perubahan (Phase AK-01A):
```php
// Mengambil dan menormalkan dari metadata Master TUK
$raw_jenis_tuk = $tuk['jenis'] ?? '';
$jenis_tuk_norm = LSP_Cert_Integration::normalize_jenis_tuk( $raw_jenis_tuk );

$is_sewaktu      = ( 'sewaktu' === $jenis_tuk_norm );
$is_tempat_kerja = ( 'tempat_kerja' === $jenis_tuk_norm );
$is_mandiri      = ( 'mandiri' === $jenis_tuk_norm );
```
*Dampak sebelumnya*: Jika data Master TUK belum diisi jenisnya, ketiga kotak menjadi kosong (`☐ ☐ ☐`). Jika diisi Mandiri, tercentang Mandiri.

### Sesudah Perubahan (Kebijakan Tampilan Phase AK-01C):
```php
// Kebijakan Tampilan Dokumen Cetak (Phase AK-01C):
// Menyeragamkan pilihan jenis TUK pada dokumen cetak:
// ☑ Sewaktu  ☐ Tempat Kerja  ☐ Mandiri*
$is_sewaktu      = true;
$is_tempat_kerja = false;
$is_mandiri      = false;
```
*Tampilan HTML yang dihasilkan*:
```html
<span style="font-size: 13px;">☑</span> Sewaktu &nbsp;&nbsp;&nbsp;
<span style="font-size: 13px;">☐</span> Tempat Kerja &nbsp;&nbsp;&nbsp;
<span style="font-size: 13px;">☐</span> Mandiri*
```
*Dampak*: Pada setiap cetakan FR.AK.01, checkbox **Sewaktu selalu tercentang (`☑`)**, sementara **Tempat Kerja (`☐`)** dan **Mandiri (`☐`)** selalu kosong, tanpa mengubah data asli pada database atau Master TUK.

---

## 5. Hasil Pengujian Otomatis Suite AK-01C (18/18 PASS)

Pengujian komprehensif dieksekusi secara otomatis melalui test runner `scratch/test_ak01c_suite.php`:

| Kode Uji | Skenario Pengujian | Hasil | Keterangan Verifikasi |
|---|---|:---:|---|
| **TUK-01** | Inventarisasi dokumen dengan field jenis TUK | **PASS** | Tepat teridentifikasi: hanya `form-ak01.php` yang memiliki field jenis TUK |
| **TUK-02** | Checkbox Sewaktu selalu tercentang | **PASS** | `☑ Sewaktu` hadir pada dokumen cetak |
| **TUK-03** | Checkbox Tempat Kerja selalu kosong | **PASS** | `☐ Tempat Kerja` (tidak tercentang) |
| **TUK-04** | Checkbox Mandiri selalu kosong | **PASS** | `☐ Mandiri*` (tidak tercentang) |
| **TUK-05** | Kebijakan konsisten terhadap perubahan metadata master | **PASS** | Diuji dengan meta 'Mandiri', 'Tempat Kerja', dan '', hasil cetak tetap seragam: `☑ Sewaktu  ☐ Tempat Kerja  ☐ Mandiri*` |
| **TUK-06** | Dokumen tanpa pilihan jenis TUK tidak ditambah elemen | **PASS** | APL.01, APL.02, MAPA.01, MAPA.02 bersih dari modifikasi |
| **TUK-07** | Nama TUK Penyelenggara tetap sesuai data kegiatan | **PASS** | Tetap memuat *TUK Mandiri LSP Komunikasi — Jakarta Pusat* |
| **TUK-08** | Lokasi Uji fisik tetap sesuai data kegiatan | **PASS** | Tetap memuat *Gedung Kesenian Jakarta Lt. 2* |
| **TUK-09** | Tidak ada perubahan struktur database | **PASS** | 16 kolom tabel kegiatan identik dengan baseline |
| **TUK-10** | Regresi: FR.APL.01 tetap berfungsi normal | **PASS** | Berhasil dirender tanpa kendala |
| **TUK-11** | Regresi: FR.APL.02 tetap berfungsi normal | **PASS** | Berhasil dirender tanpa kendala |
| **TUK-12** | Regresi: FR.MAPA.01 Observasi tetap berfungsi normal | **PASS** | Berhasil dirender tanpa kendala |
| **TUK-13** | Regresi: FR.MAPA.01 DIT tetap berfungsi normal | **PASS** | Berhasil dirender tanpa kendala |
| **TUK-14** | Regresi: FR.MAPA.01 Portofolio tetap berfungsi normal | **PASS** | Berhasil dirender tanpa kendala |
| **TUK-15** | Regresi: FR.MAPA.02 tetap berfungsi normal | **PASS** | Berhasil dirender tanpa kendala |
| **TUK-16** | FR.AK.01 tetap berfungsi normal | **PASS** | Berhasil dirender dengan data lengkap |
| **TUK-17** | Layout cetak A4 tidak berubah di luar checkbox | **PASS** | Ukuran kertas A4, tabel bergaris, dan margin tetap presisi |
| **TUK-18** | Keamanan hak akses dokumen tetap terlindungi | **PASS** | Pengguna guest/unauthorized ditolak HTTP 403 (*Akses Ditolak*) |

---

## 6. Pemeriksaan Visual Cetak (Print View)

1. **Baris TUK pada FR.AK.01**:
   - Menampilkan: `☑ Sewaktu    ☐ Tempat Kerja    ☐ Mandiri*`
   - Spasi antar-pilihan rapi menggunakan non-breaking space `&nbsp;&nbsp;&nbsp;`.
   - Ukuran font tanda centang Unicode `13px` serasi dengan ukuran teks label formulir (`11.5px`).
2. **Kesesuaian TUK Penyelenggara & Lokasi Uji**:
   - Kolom kesepakatan pelaksanaan asesmen tetap menyajikan nama Lokasi Uji fisik secara jelas dengan rincian TUK Penyelenggara di baris bawahnya, sehingga tidak ada kekeliruan pemahaman antara jenis TUK cetak dan identitas fisik TUK.

---

## 7. Analisis Risiko yang Tersisa

- **Risiko Nol terhadap Master Data**: Kebijakan tampilan ini diterapkan murni pada layer rendering dokumen cetak, sehingga nilai asli pada database WordPress atau post meta TUK tidak terdistorsi.
- **Risiko Nol terhadap Dokumen Lain**: Karena formulir lain tidak memiliki pilihan jenis TUK, tidak ada risiko *side-effect* atau regresi visual pada formulir APL maupun MAPA.

---

## 8. Status Kesiapan & Rekomendasi

- Fase **AK-01C** telah diselesaikan dengan akurasi 100% dan seluruh 18 pengujian dinyatakan **PASS**.
- Sesuai dengan instruksi **STOP CONDITION**, seluruh pekerjaan fase ini dihentikan dan sistem menunggu arahan Anda untuk fase berikutnya.
