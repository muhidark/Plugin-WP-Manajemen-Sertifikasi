# LAPORAN IMPLEMENTASI PHASE AK-05C
## Penyempurnaan Cetak Dokumen Kegiatan & Generator FR.AK.05

**Project:** LSP Certification Management (`lsp-certification-management`)  
**Status:** IMPLEMENTASI SELESAI & SELURUH PENGUJIAN PASS (16/16)  
**Tanggal:** 24 Oktober 2026

---

### 1. Ringkasan Eksekutif
Phase AK-05C berfokus pada dua penyempurnaan utama:
1. **Penataan Menu Cetak Kegiatan (`admin/views/kegiatan-list.php`)**:
   Mengubah tombol tunggal `Cetak FR.MAPA.02` menjadi dropdown **"🖨️ Cetak Dokumen ▾"** yang menyediakan akses langsung ke seluruh 6 dokumen tingkat kegiatan/asesmen:
   - FR.MAPA.01 — Observasi Langsung
   - FR.MAPA.01 — Pertanyaan Tertulis (DIT)
   - FR.MAPA.01 — Verifikasi Portofolio
   - FR.MAPA.02 — Peta Asesmen
   - FR.AK.05 — Laporan Asesmen
   - FR.AK.06 — Meninjau Proses Asesmen
2. **Penyempurnaan Generator FR.AK.05 (`templates/docs/form-ak05.php`)**:
   - Pengambilan otomatis nama peserta dari pendaftaran kegiatan (`wp_lsp_pendaftaran`).
   - Eksklusi peserta berstatus `rejected`, `batal`, atau `cancelled`.
   - Isolasi data per asesor jika kegiatan memiliki penugasan multi-asesor.
   - Pembagian peserta **maksimal 10 peserta per halaman** (`array_chunk(..., 10)`).
   - Penomoran peserta berlanjut antar halaman (contoh: Hal 1: 1–10, Hal 2: 11–20, Hal 3: 21–23 + 7 baris kosong).
   - Penomoran halaman dinamis (`Halaman X dari Y`) di pojok kanan atas lembar cetak jika total halaman > 1.
   - Format tetap presisi A4 dengan CSS print page breaks (`.lsp-page-break`).

---

### 2. File yang Dimodifikasi

1. **`admin/views/kegiatan-list.php`**:
   - Mengganti link tunggal `Cetak FR.MAPA.02` dengan dropdown UI rapi bergaya native WordPress admin.
   - Mempertahankan ID kegiatan pada masing-masing URL cetak:
     - `admin.php?page=lsp-cert-print-kegiatan&kegiatan_id=...&doc=mapa01_obs`
     - `admin.php?page=lsp-cert-print-kegiatan&kegiatan_id=...&doc=mapa01_dit`
     - `admin.php?page=lsp-cert-print-kegiatan&kegiatan_id=...&doc=mapa01_vp`
     - `admin.php?page=lsp-cert-print-kegiatan&kegiatan_id=...&doc=mapa02`
     - `admin.php?page=lsp-cert-print-kegiatan&kegiatan_id=...&doc=ak05`
     - `admin.php?page=lsp-cert-print-kegiatan&kegiatan_id=...&doc=ak06`

2. **`templates/docs/form-ak05.php`**:
   - Menambahkan query pengambilan daftar asesi yang terdaftar pada kegiatan dan disetujui.
   - Mendukung isolasi per asesor (mengelompokkan peserta berdasarkan `asesor_id` penugasan).
   - Menerapkan pembagian peserta 10 orang per lembar cetak A4.
   - Menjaga 12 elemen dokumen wajib BNSP pada setiap lembaran:
     1. Judul Formulir: `FR.AK.05. LAPORAN ASESMEN`
     2. Judul Skema Sertifikasi
     3. Nomor Skema Sertifikasi
     4. TUK (dengan penandaan `☑ Sewaktu  ☐ Tempat Kerja  ☐ Mandiri*`)
     5. Nama Asesor
     6. Tanggal Pelaksanaan
     7. Tabel Hasil Asesmen (10 baris per halaman, nomor berlanjut, checkbox K / BK kosong)
     8. Catatan Pelaksanaan Asesmen (Aspek Negatif / Positif, Penolakan, Saran Perbaikan)
     9. Kolom tanda tangan Asesor
     10. Catatan/Pengesahan LSP
     11. Kolom tanda tangan Manajemen LSP
     12. Header penomoran halaman `Halaman X dari Y`

3. **`templates/docs/document-layout.php`**:
   - Memastikan generator dokumen kegiatan seperti `ak05` yang merender multi-sheet ditangani secara khusus tanpa double container sheet.

---

### 3. Hasil Pengujian Otomatis

Suite pengujian komprehensif dijalankan via `scratch/test_ak05c_suite.php`:
- **AK05C-01**: Menu Cetak Dokumen pada halaman kegiatan memuat 6 dokumen lengkap (PASS)
- **AK05C-02**: URL tombol cetak mempertahankan parameter kegiatan_id & doc yang benar (PASS)
- **AK05C-03**: Query peserta FR.AK.05 mengambil nama peserta otomatis dari kegiatan (PASS)
- **AK05C-04**: Peserta berstatus rejected/cancelled diabaikan dari daftar peserta AK.05 (PASS)
- **AK05C-05**: Pembagian peserta 23 orang terbagi menjadi 3 halaman sheet (10, 10, 3) (PASS)
- **AK05C-06**: Penomoran baris berlanjut secara kontinu antar halaman (1-10, 11-20, 21-30) (PASS)
- **AK05C-07**: Terdapat indikator 'Halaman X dari Y' saat dokumen memiliki lebih dari 1 halaman (PASS)
- **AK05C-08**: Baris kosong sisa pada halaman terakhir tetap diberi nomor urut dan kolom kosong (PASS)
- **AK05C-09**: Kolom rekomendasi K dan BK pada seluruh baris tetap kosong (PASS)
- **AK05C-10**: Isolasi per asesor aktif saat kegiatan memiliki peserta dengan asesor berbeda (PASS)
- **AK05C-11**: Filter asesor_id pada parameter URL membatasi peserta hanya untuk asesor tersebut (PASS)
- **AK05C-12**: Struktur 12 elemen resmi FR.AK.05 hadir lengkap di setiap halaman (PASS)
- **AK05C-13**: Checkbox jenis TUK seragam: [x] Sewaktu, [ ] Tempat Kerja, [ ] Mandiri (PASS)
- **AK05C-14**: CSS cetak A4 dan page-break disisipkan dengan benar antar halaman (PASS)
- **AK05C-15**: Dokumen FR.MAPA.02 tetap berfungsi normal dari menu kegiatan (Regression Test) (PASS)
- **AK05C-16**: Dokumen FR.AK.06 tetap berfungsi normal dari menu kegiatan (Regression Test) (PASS)

**Hasil: 16/16 LULUS (PASS).**

---

### 4. Hasil Uji Regresi Menyeluruh (Full Regression Test)
- `test_ak05c_suite.php`: **16/16 PASS**
- `test_ak03_06_suite.php`: **28/28 PASS**
- `test_ak02b_suite.php`: **22/22 PASS**
- `test_ak01c_suite.php`: **18/18 PASS**
- `test_mapa02_suite.php`: **16/16 PASS**
- **Total: 100/100 PASS (Zero Regression)**
