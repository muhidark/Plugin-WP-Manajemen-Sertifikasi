# PH-02 — SKKNI / Elemen / KUK Integrity

**Plugin:** LSP Certification Management 1.0.1 + LSP Core Management 1.1.0-RC1
**Tanggal:** 26 Oktober 2026
**Lingkungan uji:** WordPress 7.1.3, PHP 8.5.4, `/var/www/html/wordpress`
**Status:** SELESAI — menunggu review sebelum PH-03

---

## 1. Ringkasan

Elemen/KUK karangan (C-02) berhasil dihapus. Temuan penting: **sumber karangannya bukan di
`class-integration.php`, melainkan di plugin Core** — `LSP_Core_Skema::get_skema_skkni_data()`.
Certification memiliki salinan logika yang sama, sehingga keduanya diperbaiki.

Setelah perbaikan: data master lengkap → dokumen tercetak normal dengan Elemen/KUK asli.
Data master tidak lengkap → **pencetakan dihentikan** di sisi server dengan pesan yang menyebut unit
mana yang bermasalah. Tidak ada lagi jalur yang menghasilkan Elemen/KUK tebakan.

---

## 2. Temuan

### PH02-01 — Generator Elemen/KUK ada di Core (akar C-02)

`lsp-core-management/includes/class-skema.php:800` — `get_skema_skkni_data()`:

```php
$skkni_data = get_post_meta( $post_id, 'skkni_data', true );
if ( ! empty( $skkni_data ) && is_array( $skkni_data ) ) {
    return $skkni_data;
}
// Fallback: parse unit_kompetensi (KODE | JUDUL)  ← di sini karangan dibuat
'elemen' => array( array(
    'nomor' => 1,
    'nama'  => 'Melaksanakan unit kompetensi ' . trim( $parts[1] ),
    'kuk'   => array( '1.1 Menerapkan prosedur kerja sesuai standar yang berlaku.' ),
) ),
```

Fungsi ini dipakai tiga konsumen di Core: meta box Master Skema, kolom admin "Total Unit",
dan template publik halaman skema. Artinya Elemen/KUK karangan tidak hanya masuk dokumen cetak,
tetapi juga **tampil di halaman publik** untuk setiap skema yang belum punya data SKKNI terstruktur.

Tingkat: **CRITICAL** (integritas dokumen resmi + paparan publik).

### PH02-02 — Salinan logika yang sama di Certification

`lsp-certification-management/includes/class-integration.php` — `get_unit_kompetensi()` memuat
fallback identik (elemen "Melaksanakan unit kompetensi …", KUK "1.1 Menerapkan prosedur kerja …").
Selama ini tidak terlihat karena data Core selalu lebih dulu mengembalikan elemen karangan.

Tingkat: **CRITICAL**.

### PH02-03 — Tidak ada gate saat data kompetensi tidak lengkap

Template `form-apl02.php` dan `form-ia01.php` sudah punya tampilan "Belum ada elemen…" untuk data
kosong, tetapi tidak ada mekanisme yang menahan pencetakan. Akibatnya kekosongan data berujung pada
dokumen resmi yang bolong tanpa peringatan.

Tingkat: **HIGH**.

---

## 3. Perubahan

### `lsp-core-management/includes/class-skema.php`

Fallback `get_skema_skkni_data()` sekarang hanya mengembalikan **kode + judul unit** (data master yang
sah) dengan `'elemen' => array()` dan penanda `'sumber' => 'meta_skema'`. Tidak ada lagi elemen/KUK
buatan. Komentar menjelaskan alasan dan cara konsumen memakai penanda tersebut.

### `lsp-certification-management/includes/class-integration.php`

1. Fallback `get_unit_kompetensi()` dihapus karangannya: hanya kode + judul, `elemen` kosong,
   `sumber` diteruskan dari Core (`meta_skema` / `skkni_terstruktur`).
2. Fungsi baru `get_competency_completeness( $skema_id )` mengembalikan:
   `is_complete`, `total_units`, `units_lengkap`, `missing[]` (kode, judul, alasan), `elemen`, `kuk`.
   Unit dianggap bermasalah bila tanpa elemen, atau elemennya tanpa KUK.

### `lsp-certification-management/includes/class-document-printer.php`

Fungsi baru `enforce_skkni_integrity( $skema_id, $doc_type )` — gerbang sebelum cetak, mengikuti pola
`enforce_kelompok_pekerjaan_coverage()` yang sudah ada:

- berlaku untuk dokumen yang memuat Elemen/KUK: `apl02`, `ia01`, `all`, `bundle_all`;
- data lengkap → lolos;
- data tidak lengkap → `wp_die()` 403 dengan pesan "Pencetakan Ditolak: Elemen/KUK Belum Lengkap",
  daftar unit bermasalah beserta alasannya, status master (x dari y unit, n elemen, n KUK), dan tombol
  menuju Master Skema untuk admin;
- dipanggil di `render_print_page()` **dan** `render_kegiatan_print_page()`.

### `lsp-certification-management/tests/ph02-skkni-integrity.php` (baru)

32 assertion: data lengkap, data meta-only, skema tanpa unit, dokumen yang tidak butuh Elemen/KUK,
provenance `sumber`, dan pembersihan fixture.

---

## 4. Hasil Gate

| Gate roadmap | Cara uji | Hasil |
|---|---|---|
| Tidak ada Elemen/KUK buatan | uji unit (A4, A5) + render nyata | **PASS** — 0 kemunculan "Melaksanakan unit kompetensi" dan "Menerapkan prosedur kerja sesuai standar" di APL.02, IA.01, dan halaman publik skema |
| Data master lengkap → generator berjalan | render FR.APL.02 & FR.IA.01 pendaftaran #1 | **PASS** — APL.02 60.942 byte (4 unit, 21 elemen), IA.01 132.162 byte, memuat teks master asli (`J.63OPR00.004.2`, "Membuat dokumen", "1.1 Aplikasi pengolah kata dibuka") |
| Data master tidak lengkap → generator berhenti + warning | hapus sementara meta `skkni_data` skema #224, lalu cetak APL.02 | **PASS** — keluaran 5.051 byte berisi "Pencetakan Ditolak — Elemen/KUK Belum Lengkap", daftar unit tanpa elemen, bukan dokumen 60 KB |
| Tidak ada silent fallback | uji unit B12–B17, D3 | **PASS** — 6 jalur dokumen ditolak, 0 lolos diam-diam |
| Dokumen non-Elemen/KUK tidak terpengaruh | uji unit C (apl01, mapa01_obs, mapa02, ak01) | **PASS** |
| Regresi Core (meta box, kolom admin, halaman publik) | HTTP admin `post.php?post=224` (503 KB), halaman skema publik | **PASS** — HTTP 200, tanpa fatal, unit asli tetap tampil |
| Pemulihan data setelah uji | `update_post_meta` dari backup `var_export` | **PASS** — array identik (`===`), completeness kembali 4 unit / 21 elemen / 79 KUK |
| Frontend & admin normal | curl beranda, halaman skema, edit skema | **PASS** — semua HTTP 200 |

Uji "data tidak lengkap" dilakukan pada jalur cetak yang sebenarnya dengan menghapus meta
`skkni_data` skema #224 sementara; backup `var_export` tersimpan di `/tmp/skkni_224_backup.php`
dan sudah dipulihkan serta diverifikasi identik.

---

## 5. Dampak lintas plugin & catatan produksi

- **Core sudah live di `lspkomunikasi.id`.** Perubahan ini mengubah perilaku untuk skema yang belum
  punya `skkni_data`: sebelumnya halaman publik dan dokumen menampilkan Elemen/KUK karangan, sekarang
  menampilkan unit tanpa elemen (dan pencetakan APL.02/IA.01 ditahan sampai master dilengkapi).
  Ini perbaikan yang diinginkan, tetapi **terlihat oleh pengguna** dan perlu keputusan kapan naik.
- Versi plugin **belum dinaikkan**. Sesuai roadmap, penomoran versi dilakukan pada PH-10/REL-01.
  Sebelum paket produksi dibuat, header `Version:` dan konstanta versi Core wajib dinaikkan.
- Certification 1.0.1 bergantung pada penanda `sumber` dari Core. Bila Core versi lama dipasang
  bersama Certification baru, penanda tidak ada — Certification akan menganggap sumber
  `skkni_terstruktur`, tetapi kelengkapan tetap dinilai dari ada/tidaknya elemen, sehingga gate tetap
  bekerja. **Urutan deploy tetap: Core dulu, lalu Certification.**

---

## 6. Temuan di luar scope (untuk fase berikutnya)

| # | Temuan | Fase |
|---|---|---|
| 1 | Identitas contoh masih dipakai sebagai fallback: `'Masriah'`, `'Eko Prasyanto'`, `MET.000.032423 2019`, `MET.000.014249 2019` di `class-document-printer.php` | PH-03 |
| 2 | `get_persyaratan_dasar()` / `get_persyaratan_admin()` mengembalikan daftar persyaratan generik bila meta kosong — konten buatan pada FR.APL.01 | PH-03 |
| 3 | `render_kegiatan_print_page()` memakai `$pendaftaran->paket_muk_id` sebelum `$pendaftaran` diisi (`null`) — memicu warning PHP 8 dan bergantung pada nilai null | PH-09 |

---

## 7. Langkah berikutnya

**PH-03 — Identity & Document Metadata Hardening.** Menghapus seluruh identitas contoh (nama asesor,
penyusun, validator, Nomor MET) dan memastikan template tetap berfungsi saat metadata kosong.

Gate PH-03 menunggu review hasil PH-02.
