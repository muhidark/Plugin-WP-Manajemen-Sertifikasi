# LAPORAN AUDIT PHASE AK-01A
**Penyempurnaan Data Kegiatan Sertifikasi, Integrasi TUK, dan Generator FR.AK.01**

- **Project**: LSP Certification Management
- **Fase**: Phase AK-01A
- **Tanggal Audit**: 24 Oktober 2026
- **Status Evaluasi**: COMPLETED & VERIFIED (16/16 Test Passed)

---

## 1. Ringkasan Implementasi

Phase AK-01A bertujuan untuk menyempurnakan manajemen kegiatan sertifikasi dengan memisahkan identitas **TUK Penyelenggara** dan **Lokasi Fisik Uji**, mengintegrasikan metadata **Jenis TUK** dari Master TUK, serta membangun generator dokumen **FR.AK.01 (Persetujuan Asesmen dan Kerahasiaan)** sesuai format resmi BNSP.

Pencapaian utama:
1. **Migrasi Database Idempoten**: Penambahan kolom `lokasi_uji` (VARCHAR 255) dan `alamat_lokasi_uji` (TEXT) pada tabel `{$wpdb->prefix}lsp_kegiatan` menggunakan prefix dinamis tanpa menghapus data kegiatan lama dan aman dijalankan berulang kali.
2. **Formulir Kegiatan**: Admin dapat menginput Nama Lokasi Uji dan Alamat Lokasi Uji fisik tepat setelah dropdown pilihan TUK Penyelenggara.
3. **Integrasi Jenis TUK**: Mengambil metadata `jenis_tuk` dari Master TUK (`lsp-core-management`). Melakukan normalisasi aman (`sewaktu`, `tempat_kerja`, `mandiri`). Jika kosong atau tidak dikenal, sistem **tidak menebak** dan membiarkan ketiga checkbox tetap kosong.
4. **Generator FR.AK.01**: Template cetak siap pakai (`templates/docs/form-ak01.php`) sesuai standar format BNSP dengan tanggal asesmen mengikuti **tanggal kegiatan (bukan H-2)**, serta pemisahan jelas antara Lokasi Uji fisik dan TUK Penyelenggara.
5. **Alur Pendaftaran Tetap Murni**: Asesi tetap memilih paket kegiatan tanpa adanya input TUK tambahan pada form pendaftaran.

---

## 2. Perubahan Database

Tabel: `{$wpdb->prefix}lsp_kegiatan`
- **Kolom Baru**:
  - `lokasi_uji` (`varchar(255) DEFAULT NULL`) diletakkan setelah `tuk_id`.
  - `alamat_lokasi_uji` (`text DEFAULT NULL`) diletakkan setelah `lokasi_uji`.
- **Mekanisme Migrasi**:
  - Dikelola oleh `LSP_Cert_Database::maybe_upgrade_database()` yang mengecek keberadaan kolom via `SHOW COLUMNS FROM {$wpdb->prefix}lsp_kegiatan LIKE '...'`.
  - Didaftarkan juga pada skema aktivasi tabel di `includes/class-activator.php`.

---

## 3. Berkas Baru & Berkas yang Dimodifikasi

### A. Berkas Baru
1. **`templates/docs/form-ak01.php`**
   - Template dokumen FR.AK.01 (Persetujuan Asesmen dan Kerahasiaan).
   - Memuat data skema, jenis TUK (`Sewaktu`, `Tempat Kerja`, `Mandiri`), nama asesor, nama asesi, bukti yang dikumpulkan, hari/tanggal pelaksanaan (bukan H-2), waktu, lokasi uji & TUK penyelenggara, klausul hak banding/kerahasiaan, serta area tanda tangan asesor & asesi.
2. **`docs/audit/ak01a-kegiatan-tuk-integration.md`**
   - Laporan audit dan verifikasi Phase AK-01A.

### B. Berkas yang Dimodifikasi
1. **`includes/class-activator.php`**
   - Menambahkan kolom `lokasi_uji` dan `alamat_lokasi_uji` pada definisi DDL `sql_kegiatan`.
2. **`includes/class-database.php`**
   - Menambahkan method migrasi idempoten `maybe_upgrade_database()`.
   - Menambahkan penanganan `lokasi_uji` dan `alamat_lokasi_uji` pada `insert_kegiatan()`.
3. **`includes/class-integration.php`**
   - Memperbarui `get_all_tuk()` dan `get_tuk()` agar menyertakan metadata `jenis` (`jenis_tuk`) dan `kode` (`kode_tuk`).
   - Menambahkan fungsi helper `normalize_jenis_tuk( $raw_jenis )`.
4. **`admin/views/kegiatan-form.php`**
   - Menambahkan input field "Nama Lokasi Uji" dan "Alamat Lokasi Uji" setelah dropdown pemilih TUK.
5. **`admin/class-kegiatan.php`**
   - Menyimpan dan mensanitasi input `lokasi_uji` dan `alamat_lokasi_uji` pada AJAX handler `ajax_save_kegiatan()`.
6. **`includes/class-document-printer.php`**
   - Mendaftarkan dokumen `ak01` pada `get_available_documents()`.
7. **`templates/docs/document-layout.php`**
   - Menambahkan container cetak `lsp-page-ak01` dan opsi FR.AK.01 pada dropdown document switcher.
8. **`admin/views/pendaftaran-detail.php`**
   - Menambahkan tombol akses cepat cetak `🔏 Persetujuan Asesmen (AK.01)`.

---

## 4. Hasil Pengujian Unit & Integrasi (16/16 PASS)

Pengujian dilakukan secara otomatis menggunakan test runner CLI:

| Kode Uji | Skenario Pengujian | Hasil | Keterangan Verifikasi |
|---|---|:---:|---|
| **TEST-01** | TUK lama tetap dapat dipilih | **PASS** | TUK ID 30 ditemukan dan dapat dipilih |
| **TEST-02** | Normalisasi `jenis_tuk` valid | **PASS** | 'TUK Sewaktu', 'TUK Tempat Kerja', 'TUK Mandiri' terpetakan tepat |
| **TEST-03** | `jenis_tuk` kosong/tidak dikenal tidak ditebak | **PASS** | String kosong dan nilai acak mengembalikan `null` |
| **TEST-04** | Simpan Nama Lokasi Uji | **PASS** | Berhasil disimpan pada database kegiatan |
| **TEST-05** | Simpan Alamat Lokasi Uji | **PASS** | Berhasil disimpan pada database kegiatan |
| **TEST-06** | Perbarui Data Lokasi Uji | **PASS** | Berhasil diperbarui via update form/database |
| **TEST-07** | Integritas data kegiatan lama | **PASS** | Record kegiatan lama tidak hilang |
| **TEST-08** | Idempotensi migrasi database | **PASS** | Menjalankan migrasi ulang tidak menimbulkan duplikasi kolom atau error |
| **TEST-09** | Tanggal FR.AK.01 = Tanggal Kegiatan | **PASS** | Menggunakan 30 Oktober 2026 (bukan H-2 28 Oktober 2026) |
| **TEST-10** | Tanggal MAPA tetap H-2 | **PASS** | MAPA.01 & MAPA.02 tetap konsisten 28 Oktober 2026 |
| **TEST-11** | Regression Test APL.01 & APL.02 | **PASS** | Kedua formulir APL tetap berfungsi normal |
| **TEST-12** | Regression Test MAPA.01 & MAPA.02 | **PASS** | Seluruh varian MAPA tetap berfungsi normal |
| **AK01-TUK-05** | TUK Penyelenggara vs Lokasi Uji | **PASS** | Lokasi fisik dan TUK Penyelenggara terdefinisi terpisah tanpa tertukar |
| **AK01-TUK-04** | Checkbox kosong jika `jenis_tuk` kosong | **PASS** | Menghasilkan `☐ Sewaktu  ☐ Tempat Kerja  ☐ Mandiri*` |
| **AK01-TUK-03** | Checkbox centang jika `jenis_tuk` terisi | **PASS** | Teruji centang `☑` akurat untuk masing-masing tipe TUK |
| **AK01-TUK-02** | Form pendaftaran asesi tetap murni | **PASS** | Tidak ada input TUK baru pada pendaftaran peserta |

---

## 5. Analisis Risiko & Keterbatasan

1. **Kelengkapan Metadata TUK pada LSP Core Management**:
   - Master TUK saat ini (ID: 30) memiliki metadata `jenis_tuk` yang masih bernilai string kosong (`''`).
   - Sesuai dengan batasan *read-only* terhadap `lsp-core-management`, sistem tidak mengubah data inti tersebut secara paksa dan membiarkan checkbox kosong pada cetakan dokumen.
   - **Rekomendasi**: Admin dapat membuka menu Tempat Uji Kompetensi (TUK) di WP-Admin dan memilih jenis TUK yang sesuai (Sewaktu/Tempat Kerja/Mandiri) agar tercetak centang otomatis pada dokumen FR.AK.01.
2. **Lokasi Uji Bersifat Opsional**:
   - Jika `lokasi_uji` tidak diisi pada kegiatan, dokumen FR.AK.01 otomatis menggunakan nama dan alamat dari TUK Penyelenggara yang dipilih.

---

## 6. Status Kepatuhan & Rekomendasi

Sesuai instruksi **STOP CONDITION**, seluruh pekerjaan implementasi dan pengujian Phase AK-01A telah selesai dieksekusi dengan hasil 100% lulus (PASS). Tidak ada perubahan pada tema maupun kode `lsp-core-management`. Sistem berada dalam status siap untuk evaluasi dan fase berikutnya.
