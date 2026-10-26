# LAPORAN AUDIT PHASE AK-01B
**Finalisasi Generator FR.AK.01 (Persetujuan Asesmen dan Kerahasiaan)**

- **Project**: LSP Certification Management
- **Fase**: Phase AK-01B
- **Tanggal Audit & Pengujian**: 24 Oktober 2026
- **Status Evaluasi**: COMPLETED & VERIFIED (17/17 Test Passed)

---

## 1. Komponen yang Sudah Ada Sebelumnya

Sebelum Phase AK-01B dijalankan, fondasi sistem telah disiapkan pada Phase AK-01A:
1. **Struktur Database Kegiatan**:
   - Kolom `lokasi_uji` (VARCHAR 255) dan `alamat_lokasi_uji` (TEXT) sudah berhasil dimigrasi pada tabel `{$wpdb->prefix}lsp_kegiatan` secara idempoten.
2. **Formulir Kegiatan**:
   - Input Nama Lokasi Uji dan Alamat Lokasi Uji telah aktif di admin form kegiatan.
3. **Integrasi Jenis TUK**:
   - Pengambilan metadata `jenis_tuk` dari `LSP_Core_Management` dan normalisasinya (`sewaktu`, `tempat_kerja`, `mandiri`) sudah tersedia di `LSP_Cert_Integration`.
4. **Registrasi Dokumen Printer**:
   - Key `ak01` telah didaftarkan ke Document Printer registry.

---

## 2. Perubahan yang Dilakukan pada Phase AK-01B

Pada fase finalisasi ini, penyempurnaan difokuskan pada presisi konten dan kepatuhan terhadap format acuan dokumen resmi BNSP:

1. **Penyempurnaan Teks Bukti yang Dikumpulkan (`templates/docs/form-ak01.php`)**:
   - Menghilangkan singkatan pada instrumen tanya jawab, sehingga tertulis lengkap: `Daftar Pertanyaan Tertulis`, `Daftar Pertanyaan Lisan`, dan `Daftar Pertanyaan Wawancara`.
   - Menjaga seluruh checklist bukti tetap kosong (`☐`).
2. **Penyempurnaan Area Tanda Tangan & Tanggal Penandatanganan (Task 4 & Task 6)**:
   - Sesuai dengan instruksi Task 4 (*"Tanggal penandatanganan tetap kosong untuk pengisian manual jika tidak ada aturan atau data sah yang menetapkannya"*), label tanggal tanda tangan pada area bawah diubah menjadi format titik-titik manual (`Tanggal : ………………………………`).
   - Bagian kesepakatan asesmen (*"Pelaksanaan asesmen disepakati pada"*) tetap terisi otomatis dengan **Hari dan Tanggal pelaksanaan kegiatan sertifikasi (Bukan H-2)**, misalnya: `Jumat, 30 Oktober 2026`.
3. **Pemberian Ruang Tanda Tangan Basah**:
   - Area tanda tangan Asesor dan Asesi memiliki ruang vertikal yang cukup (tinggi ~95px) tanpa gambar tanda tangan otomatis, siap ditandatangani manual pasca cetak.
4. **Isolasi Data Antar-Peserta**:
   - Memastikan generator membaca data asesi dan rekomendasi asesor secara ketat berbasis `pendaftaran_id` sehingga dokumen peserta tidak saling tertukar.

---

## 3. Hasil Pengujian Unit & Integrasi (17/17 PASS)

Pengujian komprehensif dieksekusi secara otomatis melalui test suite `scratch/test_ak01b_suite.php`:

| Kode Uji | Skenario Uji | Status | Bukti & Catatan Verifikasi |
|---|---|:---:|---|
| **AK01B-01** | Identitas skema sesuai Master Skema | **PASS** | Judul: *Social Media Marketing*, Nomor: *SKM/LSP-KOM/005/2026* |
| **AK01B-02** | Nama asesi sesuai pendaftaran | **PASS** | Nama Asesi: *Awan Putih* tercetak akurat |
| **AK01B-03** | Nama asesor sesuai penugasan | **PASS** | Asesor penugasan: *asesor_integrasi* |
| **AK01B-04** | Jenis TUK sesuai Master TUK saat terisi | **PASS** | Centang `☑ Mandiri*` ketika master bernilai *TUK Mandiri* |
| **AK01B-05** | Jenis TUK kosong tidak ditebak | **PASS** | Ketiga checkbox tetap kosong: `☐ Sewaktu ☐ Tempat Kerja ☐ Mandiri*` |
| **AK01B-06** | TUK penyelenggara sesuai kegiatan | **PASS** | *TUK Mandiri LSP Komunikasi — Jakarta Pusat* |
| **AK01B-07** | Lokasi Uji tidak tertukar dengan TUK | **PASS** | Terpetakan terpisah: *Gedung Kesenian Jakarta Lt. 2* (Lokasi Uji) vs *TUK Mandiri LSP Komunikasi* (TUK Penyelenggara) |
| **AK01B-08** | Tanggal pelaksanaan sesuai jadwal | **PASS** | Menggunakan *Jumat, 30 Oktober 2026* (Bukan H-2 *28 Oktober 2026*) |
| **AK01B-09** | Seluruh checklist bukti kosong | **PASS** | 10 checkbox bukti berstatus `☐` (0 checked `☑`) |
| **AK01B-10** | Kelengkapan seluruh klausul pernyataan | **PASS** | 3 klausul lengkap: hak banding, kerahasiaan asesor, dan persetujuan data asesi |
| **AK01B-11** | Tanda tangan & tanggal ttd manual | **PASS** | Area tanda tangan manual tersedia, tanggal ttd berupa isian kosong `………………………………` |
| **AK01B-12** | Tata letak cetak A4 sesuai dokumen acuan | **PASS** | Menggunakan CSS A4 (`210mm 297mm`), bordered table hitam 1px, padding proporsional |
| **AK01B-13** | Keamanan hak akses dokumen | **PASS** | Akses tanpa otorisasi ditolak dengan respons HTTP 403 (*Akses Ditolak*) |
| **AK01B-14** | Isolasi data antar-peserta | **PASS** | Data peserta A dan B terbukti tidak saling mencemari/tertukar |
| **AK01B-15** | Regresi Cetak APL.01 & APL.02 | **PASS** | Dokumen APL.01 & APL.02 tetap berfungsi 100% normal |
| **AK01B-16** | Regresi Cetak MAPA.01 (3 Metode) | **PASS** | Versi Observasi, DIT, dan Portofolio tetap berfungsi 100% normal |
| **AK01B-17** | Regresi Cetak MAPA.02 | **PASS** | Dokumen Peta Instrumen Asesmen tetap berfungsi 100% normal |

---

## 4. Bukti Pemeriksaan Hasil Cetak (Visual Print Layout)

1. **Struktur Header**:
   - `FR.AK.01.` berdampingan dengan `PERSETUJUAN ASESMEN DAN KERAHASIAAN` dengan latar belakang header netral (`#f8fafc`).
   - Sub-keterangan miring: *"Persetujuan Asesmen ini untuk menjamin bahwa Asesi telah diberi arahan secara rinci tentang perencanaan dan proses asesmen"*.
2. **Tabel Identitas & Jenis TUK**:
   - Skema Sertifikasi, Nomor Skema, Nama Asesor, dan Nama Asesi terpasang rapi dengan titik dua `:` sejajar.
   - Pilihan TUK menampilkan tiga kotak Unicode proporsional (`☐ Sewaktu  ☐ Tempat Kerja  ☐ Mandiri*`).
3. **Tabel Kesepakatan Pelaksanaan Asesmen**:
   - Hari / Tanggal terformat dalam bahasa Indonesia: `[Hari], [Tanggal] [Bulan] [Tahun]`.
   - Waktu: `08.00 WIB - Selesai`.
   - Menampilkan Lokasi Uji fisik beserta alamatnya (jika diisi) dengan keterangan TUK Penyelenggara di bawahnya.
4. **Area Tanda Tangan**:
   - Menggunakan tabel 2 kolom seimbang (50% : 50%) untuk Tanda Tangan Asesor dan Tanda Tangan Asesi dengan ruang tanda tangan basah dan baris tanggal tanda tangan manual.
5. **Kepatuhan Kertas**:
   - Masuk dalam satu lembar halaman A4 (tidak meluber atau terpotong ke halaman 2).

---

## 5. Keterbatasan yang Masih Ada

1. **Metadata Jenis TUK pada Database Master**:
   - Post meta `jenis_tuk` pada Master TUK lama (ID 30) masih bernilai string kosong. Checkbox pada FR.AK.01 akan tercentang secara otomatis seketika Admin memilih jenis TUK pada menu Tempat Uji Kompetensi di WP-Admin.
2. **Pengisian Checklist Bukti Pasca Cetak**:
   - Seluruh bukti yang akan dikumpulkan disengaja tetap kosong (`☐`) sesuai ketentuan standar agar asesor dapat menentukan centang metode secara langsung saat sesi tatap muka awal persetujuan asesmen.

---

## 6. Status Kesiapan Produksi

- Generator **FR.AK.01** telah selesai 100%, teruji menyeluruh tanpa regresi terhadap dokumen lain (APL.01, APL.02, MAPA.01, MAPA.02).
- Sesuai dengan **STOP CONDITION**, implementasi fase ini dihentikan dan sistem siap untuk dievaluasi sebelum melangkah ke fase berikutnya.
