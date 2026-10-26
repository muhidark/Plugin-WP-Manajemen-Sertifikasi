# REL-01 — Final Release Preparation

**Proyek:** LSP Core Management + LSP Certification Management
**Target produksi:** `https://lspkomunikasi.id`
**Tanggal:** 27 Oktober 2026
**Status:** **GO WITH NOTES** — paket & checksum valid; smoke test produksi belum dijalankan (memang belum waktunya)

---

## 1. Code freeze

| Plugin | Commit | Working tree |
|---|---|---|
| LSP Certification Management | `19e387c` | bersih (0 perubahan) |
| LSP Core Management | `63dab58` | bersih (0 perubahan) |

Selama REL-01 **tidak ada perubahan kode fungsional**. Satu-satunya perubahan adalah
penambahan entri ringkasan rilis pada `readme.txt` Certification (dokumentasi, bukan perilaku) —
diperlukan oleh §10 brief REL-01. Paket dibangun ulang setelah perubahan itu dan checksum
dihitung ulang.

Tidak ditemukan bug baru yang memaksa perubahan kode. Tidak ada release blocker.

---

## 2. Verifikasi versi

### LSP Certification Management

| Penanda | Nilai | Status |
|---|---|---|
| Header `Version:` | 1.1.2 | sesuai |
| Konstanta `LSP_CERT_VERSION` | 1.1.2 | sesuai |
| `readme.txt` → `Stable tag` | 1.1.2 | sesuai |
| Entri changelog `= 1.1.2 =` | ada | sesuai |
| Konstanta `LSP_CERT_DB_VERSION` | 1.1.0 | **tidak dinaikkan** — tidak ada perubahan skema |
| Header di dalam ZIP | `Version: 1.1.2` | sesuai (dibaca dari hasil ekstrak) |

### LSP Core Management

| Penanda | Nilai | Status |
|---|---|---|
| Header `Version:` | 1.1.1 | sesuai |
| Konstanta versi plugin | **tidak ada** — Core hanya memakai header | dicatat apa adanya, tidak dikarang |
| `readme.txt` → `Stable tag` | 1.1.1 | sesuai |
| Header di dalam ZIP | `Version: 1.1.1` | sesuai |

Core tidak dinaikkan hanya untuk menyamakan angka dengan Certification.

---

## 3. Verifikasi dependency

| Aspek | Hasil | Bukti |
|---|---|---|
| Core harus aktif lebih dulu | Ya | `lsp_cert_core_available()` memeriksa `class_exists('LSP_Core_Management')` + `method_exists('get_skema_skkni_data')` |
| Gagal secara aman bila Core tidak ada | Ya | bootstrap dihentikan + `admin_notices` ("memerlukan LSP Core Management…") + `error_log`, bukan fatal error |
| Aktivasi ditolak bila Core tidak ada | Ya | `lsp_cert_activate()` → `deactivate_plugins()` + pesan `wp_die` yang terbaca |
| Notice dependency benar | Ya | teks menyebut nama plugin yang harus diaktifkan lebih dulu |
| Aktivasi otomatis plugin lain | **Tidak ada** | hanya `deactivate_plugins()` milik sendiri |
| Circular dependency | **Tidak ada** | Core hanya menyebut Certification di komentar dan integrasi opsional (`class_exists`), tidak mewajibkannya |

### Minimum version Core

**Tidak ditetapkan oleh source.** Tidak ada pemeriksaan versi Core di kode Certification — yang
diperiksa adalah kontrak API (kelas + metode). Karena itu laporan ini **tidak mengarang** nomor versi
minimum.

Rekomendasi operasional (berdasarkan fitur yang dipakai, bukan aturan teknis yang dipaksakan):
pasang **Core 1.1.1** — versi yang diuji bersama Certification 1.1.2 pada REL-01 ini.

Kalimat dependency untuk dokumentasi:

> LSP Certification Management 1.1.2 membutuhkan LSP Core Management yang menyediakan kelas
> `LSP_Core_Management` dengan metode `get_skema_skkni_data()`. Core harus aktif lebih dahulu.
> Versi yang diuji: Core 1.1.1.

---

## 4. Paket produksi

| Plugin | Berkas ZIP | Ukuran | Berkas di dalam | SHA-256 |
|---|---|---|---|---|
| Core | `lsp-core-management-1.1.1.zip` | 82 KB | 7 | `f225043529230d062c879e2410a90440cc9fc05602e66957debd1c240b02ab9c` |
| Certification | `lsp-certification-management-1.1.2.zip` | 124 KB | 39 | `7f3763d2f28a8497a588685fd6c5f6371f8cb07e3303d773853a7d6eab65af13` |

Lokasi: `/home/muhadi/lsp-deploy-tools/dist/`
Checksum: `/home/muhadi/lsp-deploy-tools/dist/SHA256SUMS.txt`

Checksum dihitung **setelah** ZIP final selesai dan tidak ada build ulang sesudahnya.

---

## 5. Verifikasi isi paket

Diperiksa langsung pada ZIP (bukan pada folder sumber):

| Pemeriksaan | Core 1.1.1 | Cert 1.1.2 |
|---|---|---|
| Integritas ZIP (`testzip`) | OK | OK |
| Folder akar tunggal | `lsp-core-management/` | `lsp-certification-management/` |
| File utama di akar folder | ADA | ADA |
| Nested folder salah (`slug/slug/`) | 0 | 0 |
| Berkas development (`.git`, tests, docs, log, .env, .sql, .map) | 0 | 0 |
| Folder runtime hilang dibanding sumber | 0 | 0 |
| Berkas berbeda dari sumber (hash per berkas) | 0 | 0 |
| PHP lint hasil ekstrak | 6 berkas, 0 error | 33 berkas, 0 error |
| Header versi di dalam ZIP | 1.1.1 | 1.1.2 |

Struktur contoh yang benar (terbukti):

```
lsp-certification-management/
lsp-certification-management/lsp-certification-management.php
```

---

## 6. Pemeriksaan statis

`php -l` pada seluruh berkas PHP kedua plugin: **0 syntax error**.

Pencarian cepat dan klasifikasinya:

| Pola | Temuan | Klasifikasi |
|---|---|---|
| `var_dump(` | 0 | — |
| `print_r(` | 0 | — |
| `dd(` | 0 | — |
| `die(` polos | 0 | semua penghentian memakai `wp_die()` (standar WordPress) |
| `console.log(` | 0 | — |
| `WP_DEBUG = true` | 0 | — |
| Kredensial/token/password literal | 0 | — |
| Nama contoh (`Masriah`, `Eko Prasyanto`) | 0 | — |
| Nomor MET contoh | 1 hit: `placeholder="Contoh: MET.000.001234.2024"` di form asesor Core | **Bukan temuan** — atribut petunjuk input, tidak pernah masuk dokumen |

---

## 7. Changelog final

Entri `= 1.1.2 =` pada `readme.txt` kini memuat ringkasan rilis:

- rilis produksi pertama (paket bersih, checksum, Core harus aktif lebih dulu);
- micro-fix 1.1.2: nama asesor contoh diganti netral, sumber Paket MUK dokumen kegiatan.

Entri `= 1.1.1 =` dan `= 1.1.0 =` memuat area yang diminta brief:

| Area | Tercatat di |
|---|---|
| dependency/bootstrap hardening | 1.1.0 |
| SKKNI integrity (Elemen/KUK karangan dihapus) | 1.1.0 |
| role/capability hardening | 1.1.0 |
| registration hardening (validasi server-side) | 1.1.0 |
| APL.02 integrity (dibangun dari master) | 1.1.0 |
| APL.02 kelengkapan (tidak boleh kosong) | 1.1.1 |
| concurrency protection | 1.1.0 |
| database migration versioning | 1.1.0, 1.1.1 |
| authorization hardening (isolasi dokumen) | 1.1.0 |
| production packaging | 1.1.2 |
| micro-fix 1.1.2 | 1.1.2 |

Changelog tidak memuat nama orang maupun data pribadi.

---

## 8. Rencana backup (sebelum deployment)

| # | Objek | Cara | Yang dicatat |
|---|---|---|---|
| 1 | Database WordPress lengkap | Panel hosting (phpMyAdmin/backup tool) | nama berkas, tanggal, ukuran, checksum bila tersedia |
| 2 | Folder `wp-content/plugins/lsp-core-management` | File Manager → kompres → unduh | nama berkas, tanggal, ukuran |
| 3 | Folder `wp-content/plugins/lsp-certification-management` (bila sudah ada) | idem | idem |
| 4 | Theme aktif `lspkomunikasi` | idem (dianjurkan, tidak wajib) | idem |
| 5 | Konfigurasi server | pastikan backup hosting tersedia | **jangan** menyalin credential ke laporan |

**Verifikasi backup sebelum lanjut:** buka arsip database (harus ada pernyataan "Dump completed"
atau tabel terbaca) dan pastikan ZIP folder terpasang bisa diekstrak.

---

## 9. Rollback plan

### Bila Certification bermasalah setelah deployment

1. Nonaktifkan **Certification** (bukan menghapus).
2. Pulihkan versi Certification sebelumnya dari backup, atau hapus foldernya bila ini pemasangan pertama.
3. Verifikasi Core tetap aktif dan master data terbaca.
4. **Database hanya dipulihkan bila** memang ada perubahan data yang harus dibatalkan. v1.1.2 tidak
   mengubah skema, jadi rollback plugin saja sudah cukup.

### Bila Core bermasalah

1. Nonaktifkan **Certification** lebih dulu (agar dokumen tidak memakai Core yang rusak).
2. Pulihkan Core dari backup.
3. Verifikasi master data (skema, TUK, asesor, unit/SKKNI).
4. Aktifkan kembali Certification hanya setelah Core normal.

**Jangan** melakukan rollback database otomatis. Setiap pemulihan database harus keputusan sadar,
karena akan menghapus pendaftaran yang masuk setelah backup.

---

## 10. Urutan deployment

| Langkah | Tindakan | Kriteria lanjut |
|---|---|---|
| 1 | Backup produksi (§8) | backup terverifikasi bisa dibuka |
| 2 | Unggah/perbarui **Core 1.1.1** | WordPress menawarkan "Replace current with uploaded" |
| 3 | Verifikasi Core | plugin aktif; master skema, asesor, TUK, unit/SKKNI terbaca; tidak ada fatal error |
| 4 | Unggah **Certification 1.1.2** | — |
| 5 | Aktifkan/verifikasi Certification | menu admin muncul; dependency terpenuhi; migrasi berjalan sekali; tidak ada fatal error |
| 6 | Smoke test (§11) | seluruh butir A–G diperiksa |
| 7 | Buka operasional penuh | hanya setelah smoke test selesai |

---

## 11. Smoke test produksi (checklist)

### A. Plugin
- [ ] Core aktif
- [ ] Certification aktif
- [ ] Tidak ada fatal error
- [ ] Tidak ada PHP warning baru di halaman admin/frontend

### B. Master data
- [ ] Skema terbaca
- [ ] Unit kompetensi terbaca
- [ ] Elemen/KUK terbaca
- [ ] Asesor terbaca
- [ ] TUK terbaca

### C. Certification
- [ ] Kegiatan dapat dibuka
- [ ] Peserta dapat ditampilkan
- [ ] Kelompok Pekerjaan terbaca
- [ ] Paket MUK terbaca

### D. Registration
- [ ] Form pendaftaran tampil
- [ ] Submit berhasil (pakai kegiatan uji yang aman)
- [ ] Proteksi duplikat bekerja
- [ ] Kuota dihitung benar
- [ ] Status kegiatan menolak pendaftaran yang tidak semestinya

### E. Dokumen (satu per kelompok)
- [ ] APL (FR.APL.01 / FR.APL.02)
- [ ] MAPA (FR.MAPA.01 / FR.MAPA.02)
- [ ] AK (FR.AK.05 / FR.AK.06)
- [ ] IA (FR.IA.01 / FR.IA.02)

### F. Authorization
- [ ] Administrator: akses penuh
- [ ] Asesor: dapat melihat dokumen yang ditugaskan
- [ ] Asesor: **tidak** dapat mengakses kegiatan/peserta yang bukan tugasnya

### G. Error log
- [ ] WordPress debug log (bila aktif) bersih dari fatal
- [ ] PHP error log: tidak ada warning baru dari plugin
- [ ] Server error log: sama

---

## 12. Observasi setelah deployment

Catat minimal: waktu deployment, versi plugin terpasang, hasil smoke test, error yang muncul,
tindakan koreksi. Tidak perlu sistem monitoring baru — gunakan fasilitas hosting yang ada.

---

## 13. Keputusan rilis

### GO WITH NOTES

| Kriteria GO | Status |
|---|---|
| Paket valid | ✔ (isi, struktur, lint, header versi) |
| Checksum valid | ✔ (SHA256SUMS.txt, dihitung setelah build final) |
| Backup tersedia | ✘ **belum** — dibuat oleh pengguna pada PROD-01 langkah 1 |
| Smoke test PASS | ✘ **belum** — dijalankan setelah unggah di produksi |
| Tidak ada error blocking | ✔ pada lingkungan uji |

Dua butir yang belum terpenuhi **tidak bisa** dipenuhi pada fase ini: keduanya hanya bisa dilakukan
di server produksi. Karena itu statusnya **GO WITH NOTES**, bukan GO — dan bukan NO-GO, karena tidak
ada temuan fatal, data SKKNI yang salah, kegagalan otorisasi, pendaftaran rusak, dokumen penting
gagal, migrasi error, atau kehilangan data.

### Yang sudah divalidasi

| Validasi | Hasil |
|---|---|
| PH-01 … PH-11 (otomatis) | PASS — 299 pemeriksaan Certification + 47 Core |
| STG-01 acceptance 17 langkah (instalasi lokal) | PASS — 74 pemeriksaan |
| Micro-fix 1.1.2 (MF-01…MF-18) | PASS — 26 pemeriksaan |
| Manual browser acceptance (localhost) | PASS — dinyatakan pengguna, 27 Okt 2026 |
| Manual print acceptance (STG-02) | PASS — dinyatakan pengguna, 27 Okt 2026 |
| Smoke test produksi | **belum dijalankan** |

---

## 14. Risiko tersisa

| # | Risiko | Dampak | Mitigasi |
|---|---|---|---|
| 1 | Core produksi saat ini masih versi lama (1.1.0-RC1) | Elemen/KUK karangan masih tampil sampai Core diperbarui | Langkah 2 urutan deployment memaksa Core lebih dulu |
| 2 | Certification belum pernah terpasang di produksi | Aktivasi pertama menjalankan migrasi pembuatan 4 tabel | Backup database sebelum langkah 4; migrasi idempoten |
| 3 | Skema tanpa data SKKNI akan menampilkan badge "—" dan menahan cetak APL.02/IA.01 | Terlihat pengguna | Disengaja (PH-02); lengkapi lewat menu Migrasi SKKNI |
| 4 | `wp_lsp_*` tabel tidak dihapus saat uninstall | Data tertinggal bila plugin dilepas | Disengaja agar data sertifikasi tidak hilang |
| 5 | Kuota tetap terpakai oleh pendaftar yang kemudian ditolak | Kuota tampak penuh | Keputusan bisnis, belum diubah |
| 6 | Smoke test produksi belum ada | Risiko lingkungan (PHP/hosting berbeda dari lokal) | Checklist §11 wajib dijalankan sebelum operasional |

---

## 15. Dokumentasi terkait

- `docs/release/release-manifest.md`
- `docs/audit/00-laporan-hardening-ph01-ph11.md`
- `docs/audit/ph01` … `ph11`, `stg01`, `stg02`, `microfix-1.1.2.md`
- Alat: `/home/muhadi/lsp-deploy-tools/` (`preflight.sh`, `build.sh`, `smoke-test.sh`)

---

## 16. STOP

REL-01 selesai. Tidak ada deployment, tidak ada unggahan ke server, tidak ada perubahan pada
`lspkomunikasi.id`. Menunggu instruksi PROD-01.
