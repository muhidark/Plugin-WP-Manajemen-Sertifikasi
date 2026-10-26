# LAPORAN IMPLEMENTASI GENERATOR FR.MAPA.02
**Peta Instrumen Asesmen Hasil Pendekatan Asesmen dan Perencanaan Asesmen**

- **Plugin**: `lsp-certification-management`
- **Fase**: Phase MAPA-02
- **Tanggal Audit & Pengujian**: 24 Oktober 2026
- **Status Evaluasi**: COMPLETED & VERIFIED (16/16 Test Passed)

---

## 1. Ringkasan Implementasi

Generator **FR.MAPA.02 (Peta Instrumen Asesmen)** telah berhasil diimplementasikan sesuai standar BNSP dan dokumen acuan resmi LSP Komunikasi Indonesia. Dokumen ini berfungsi untuk memetakan instrumen asesmen yang akan digunakan terhadap potensi asesi (Kategori 1 sampai 5).

Fitur utama yang diimplementasikan:
1. **Berbasis Kegiatan Sertifikasi (`kegiatan_id`)**: Dokumen dapat dicetak langsung pada tingkat kegiatan sertifikasi tanpa mewajibkan adanya ID pendaftaran atau peserta tertentu, namun tetap kompatibel jika diakses melalui konteks peserta.
2. **Koneksi Dinamis Master Skema**: Mengambil judul skema, nomor skema, serta daftar unit kompetensi (kode unit dan judul unit) secara otomatis dan dinamis dari Master Skema tanpa nilai statis (hardcoded).
3. **Tabel Instrumen Asesmen Standar**: Memuat ke-11 instrumen asesmen standar BNSP (termasuk sub-butir 4.a dan 4.b untuk DIT), dengan seluruh kolom checklist Potensi Asesi berstatus **kosong** (`☐`) untuk pengisian manual pasca-cetak.
4. **Penanggalan H-2 Otomatis**: Menghitung tanggal penyusunan secara konsisten mundur 2 hari (`H-2`) dari tanggal pelaksanaan sertifikasi.
5. **Bagian Penyusun dan Validator**: Menampilkan nama, nomor registrasi MET, serta area tanda tangan dan tanggal pengesahan sesuai data kegiatan atau peserta.
6. **Integrasi Menu Cetak**: Tombol cetak FR.MAPA.02 tersedia di daftar kegiatan, form edit kegiatan, serta rincian pendaftaran.

---

## 2. File Baru

1. **`templates/docs/form-mapa02.php`**
   - Template inti untuk rendering dokumen FR.MAPA.02.
   - Berisi tabel judul dokumen, identitas skema sertifikasi, tabel dinamis unit kompetensi, tabel instrumen asesmen (12 baris dengan checkbox kosong `☐`), tabel Penyusun dan Validator, serta keterangan potensi asesi 1-5.

---

## 3. File yang Dimodifikasi

1. **`includes/class-document-printer.php`**
   - Menambahkan registrasi dokumen `mapa02` pada `get_available_documents()`.
   - Menambahkan method baru `render_kegiatan_print_page( $kegiatan_id, $doc_type = 'mapa02' )` untuk mencetak dokumen berbasis kegiatan tanpa pendaftaran.
   - Memastikan otorisasi (`manage_options` atau `asesor`) dan sanitasi input.
2. **`templates/docs/document-layout.php`**
   - Memperbarui title tag dan topbar agar dapat menangani kondisi dokumen berbasis kegiatan (di mana `$pendaftaran` null).
   - Menambahkan opsi `mapa02` pada pemilih dokumen (document switcher).
   - Menambahkan container render `lsp-page-mapa02`.
   - Menyesuaikan tombol kembali (&larr; Kembali ke Daftar Kegiatan jika dibuka dari level kegiatan).
3. **`admin/class-kegiatan.php`**
   - Menambahkan hook `admin_init` untuk `handle_print_action()`.
   - Menangani URL request `admin.php?page=lsp-kegiatan&action=print&id={id}&doc=mapa02`.
4. **`admin/views/kegiatan-list.php`**
   - Menambahkan tautan aksi `🖨️ Cetak FR.MAPA.02` pada setiap baris kegiatan.
5. **`admin/views/kegiatan-form.php`**
   - Menambahkan tombol aksi `🖨️ Cetak FR.MAPA.02` pada header form edit kegiatan.
6. **`admin/views/pendaftaran-detail.php`**
   - Menambahkan tombol pintas `🗺️ Peta Instrumen Asesmen (FR.MAPA.02)` pada baris navigasi cetak admin.

---

## 4. Sumber Data yang Digunakan

| Komponen Dokumen | Sumber Data Database / Service | Keterangan Fallback |
|---|---|---|
| **Judul Skema** | `LSP_Cert_Integration::get_skema($skema_id)['title']` | '-' jika data skema tidak lengkap |
| **Nomor Skema** | `LSP_Cert_Integration::get_skema($skema_id)['kode']` | '-' jika data skema tidak lengkap |
| **Daftar Unit Kompetensi** | `LSP_Cert_Integration::get_unit_kompetensi($skema_id)` | Pesan peringatan jika data unit kosong di master |
| **Penyusun (Nama & MET)** | `$kegiatan->penyusun_nama` / `$penyusun_met` (atau `$pendaftaran`) | Asesor terdaftar &rarr; Fallback default `Masriah` |
| **Validator (Nama & MET)** | `$kegiatan->validator_nama` / `$validator_met` (atau `$pendaftaran`) | Asesor terdaftar &rarr; Fallback default `Eko Prasyanto` |
| **Tanggal Penyusunan** | `$kegiatan->tanggal_pelaksanaan` (H-2) | `$pendaftaran->created_at` (H-2) &rarr; `current_time` (H-2) |

---

## 5. Mekanisme Penanggalan H-2

Penanggalan dihitung menggunakan fungsi standar WordPress dan PHP:
```php
$base_mapa_date = ! empty( $kegiatan->tanggal_pelaksanaan ) 
    ? $kegiatan->tanggal_pelaksanaan 
    : ( ! empty( $pendaftaran->created_at ) ? $pendaftaran->created_at : current_time( 'mysql' ) );

$tgl_mapa02 = date_i18n( 'd F Y', strtotime( '-2 days', strtotime( $base_mapa_date ) ) );
```

Pengujian kasus batas penanggalan:
- **Jadwal 30 Oktober 2026**: Menghasilkan `28 October 2026` (PASS).
- **Pergantian Bulan (1 Mei 2026)**: Menghasilkan `29 April 2026` (PASS).
- **Februari Tahun Biasa (1 Maret 2026)**: Menghasilkan `27 February 2026` (PASS).
- **Februari Tahun Kabisat (1 Maret 2024)**: Menghasilkan `28 February 2024` (PASS).
- **Pergantian Tahun (1 Januari 2027)**: Menghasilkan `30 December 2026` (PASS).

---

## 6. Hasil Pengujian Unit & Integrasi

Seluruh butir uji dari spesifikasi Phase MAPA-02 telah dieksekusi secara otomatis melalui test suite CLI:

| Kode Uji | Deskripsi Skenario Uji | Status | Bukti / Catatan Verifikasi |
|---|---|:---:|---|
| **MAPA02-01** | Judul skema sesuai Master Skema | **PASS** | Ditemukan: *Social Media Marketing* |
| **MAPA02-02** | Nomor skema sesuai Master Skema | **PASS** | Ditemukan: *SKM/LSP-KOM/005/2026* |
| **MAPA02-03** | Jumlah unit kompetensi sesuai skema | **PASS** | Tepat 7 unit untuk skema ID 26 |
| **MAPA02-04** | Kode & judul unit sesuai serta urut | **PASS** | Seluruh 7 kode dan judul tercetak urut |
| **MAPA02-05** | Seluruh daftar instrumen ditampilkan | **PASS** | 12 baris instrumen (1 s/d 11 termasuk 4.a & 4.b) |
| **MAPA02-06** | Checklist Potensi Asesi kosong | **PASS** | Tepat 60 checkbox `☐` (12x5) dan 0 checked `☑` |
| **MAPA02-07** | Tanggal penyusunan = Kegiatan - 2 hari | **PASS** | Jadwal 30 Okt 2026 &rarr; Tercetak 28 Okt 2026 |
| **MAPA02-08** | Tanggal H-2 pergantian bulan & kabisat | **PASS** | 1 Mei &rarr; 29 Apr, 1 Mar &rarr; 27 Feb, Kabisat &rarr; 28 Feb |
| **MAPA02-09** | Tanggal H-2 pergantian tahun | **PASS** | 1 Jan 2027 &rarr; 30 Des 2026 |
| **MAPA02-10** | Data penyusun & validator valid | **PASS** | Teruji pada tingkat kegiatan dan override pendaftaran |
| **MAPA02-11** | Format cetak A4 & tabel bordered | **PASS** | CSS `@page { size: 210mm 297mm }`, border 1px solid |
| **MAPA02-12** | Pembatasan hak akses (Security) | **PASS** | User non-admin/non-asesor ditolak HTTP 403 |
| **MAPA02-13** | Regression: Cetak FR.APL.01 | **PASS** | Dokumen APL.01 tetap berfungsi sempurna |
| **MAPA02-14** | Regression: Cetak FR.APL.02 | **PASS** | Dokumen APL.02 tetap berfungsi sempurna |
| **MAPA02-15** | Regression: Cetak 3 Versi FR.MAPA.01 | **PASS** | Versi Observasi, DIT, VP, dan Paket All tetap normal |
| **CROSS-SCHEMA** | Uji skema berbeda (Content Creator) | **PASS** | Skema 117 (10 Unit) tercetak lengkap 10 baris |

---

## 7. Pemeriksaan Visual Cetak (Print View)

1. **Ukuran Kertas**: Standar A4 (21 x 29,7 cm) dengan padding tepi aman 15mm.
2. **Border Garis**: Menggunakan `.lsp-doc-table .lsp-table-bordered` dengan border hitam solid 1px tegas.
3. **Kerapian Kolom**:
   - Kolom No. unit: 8% (center)
   - Kolom Kode unit: 28% (center monospace)
   - Kolom Judul unit: 64% (left)
   - Kolom Instrumen Asesmen: 64% (left)
   - Kolom Potensi Asesi 1 s/d 5: masing-masing 6% (center)
4. **Checkbox**: Karakter Unicode `☐` berukuran proporsional (14px) berada tepat di tengah kolom.
5. **Area Tanda Tangan**: Memiliki ruang vertikal minimal 50px untuk penandatanganan manual basah atau cap pengesahan.

---

## 8. Hasil Regression Testing

Pengujian regresi dilakukan terhadap modul cetak yang telah dibangun pada fase-fase sebelumnya:
- **FR.APL.01 Permohonan Sertifikasi**: Berfungsi penuh, data diri dan pekerjaan borderless, tanggal backdate H-1 tetap terjaga.
- **FR.APL.02 Asesmen Mandiri**: Berfungsi penuh, rekapitulasi bukti dan unit kompetensi tercetak tanpa perubahan.
- **FR.MAPA.01 Tiga Versi (Observasi, DIT, VP)**: Berfungsi penuh, switcher interaktif filter kategori latar belakang asesi tetap responsif, penanggalan H-2 sinkron.

---

## 9. Risiko dan Keterbatasan

1. **Kelengkapan Master Skema**: Dokumen FR.MAPA.02 bergantung langsung pada ketersediaan unit kompetensi di Master Skema (`lsp-core-management`). Apabila skema baru belum diisi unit kompetensinya, sistem akan menampilkan pesan peringatan agar unit diinput terlebih dahulu pada Master Skema.
2. **Karakter Unicode Checkbox**: Simbol `☐` menggunakan UTF-8 standar yang didukung oleh semua web browser modern dan printer driver.

---

## 10. Rekomendasi Kesiapan Deployment

- Modul **FR.MAPA.02** telah memenuhi seluruh kriteria penerimaan, lulus pengujian fungsional, pengujian kasus batas tanggal, otorisasi keamanan, dan pengujian regresi.
- Sesuai dengan **STOP CONDITION**, sistem berada pada status siap dan menunggu instruksi untuk fase berikutnya tanpa melakukan tindakan deployment produksi.
