# PH-08 — Database Versioning & Migration

**Plugin:** LSP Certification Management 1.0.1
**Tanggal:** 26 Oktober 2026
**Lingkungan uji:** WordPress 7.1.3, PHP 8.5.4, MySQL/MariaDB InnoDB
**Status:** SELESAI — menunggu review sebelum PH-09

---

## 1. Ringkasan

Seluruh perubahan skema sekarang dikumpulkan dalam satu mekanisme berversi
(`LSP_CERT_DB_VERSION`). Pemeriksaan skema tidak lagi berjalan pada setiap request — hanya satu
pembacaan opsi. Instalasi baru, instalasi lama, dan migrasi berulang diuji pada database nyata.

---

## 2. Temuan

| # | Temuan | Tingkat |
|---|---|---|
| PH08-01 | `maybe_upgrade_database()` menjalankan `SHOW TABLES`, `SHOW COLUMNS` **pada setiap request** — biaya query tetap meski skema tidak berubah | **MEDIUM** |
| PH08-02 | Tidak ada versi skema. Penambahan kolom/indeks berikutnya hanya bisa mengandalkan pemeriksaan berulang, tanpa jejak apa yang sudah diterapkan | **HIGH** |
| PH08-03 | Pembuatan tabel hanya terjadi saat aktivasi. Instalasi yang tabelnya hilang/terhapus sebagian tidak bisa memulihkan diri | **MEDIUM** |

---

## 3. Rancangan

```
LSP_CERT_DB_VERSION (konstanta)  →  target skema
lsp_cert_db_version (opsi)       →  versi yang sudah diterapkan
```

- `maybe_migrate()` hanya membandingkan dua nilai itu. Sama → **berhenti tanpa query skema**.
- Berbeda → jalankan seluruh langkah, lalu simpan versi.
- Setiap langkah **wajib idempoten**, sehingga aman diulang dan aman untuk instalasi baru
  (tidak ada percabangan "fresh vs upgrade").
- Menaikkan versi = mengubah konstanta; langkah lama tidak dihapus agar instalasi tertinggal
  tetap bisa menyusul.

Langkah saat ini (versi `1.0.0`):

| # | Langkah | Idempoten karena |
|---|---|---|
| 1 | Buat 4 tabel dasar | `CREATE TABLE IF NOT EXISTS` + `dbDelta` |
| 2 | Kolom `lokasi_uji`, `alamat_lokasi_uji` pada `wp_lsp_kegiatan` | dicek lewat `SHOW COLUMNS` |
| 3 | Indeks unik `uniq_kegiatan_nik`, `uniq_kegiatan_email` | dicek lewat `SHOW INDEX` |

---

## 4. Perubahan

| Berkas | Isi |
|---|---|
| `includes/class-migrations.php` (baru) | `target()`, `installed()`, `maybe_migrate()`, `run()` |
| `lsp-certification-management.php` | Konstanta `LSP_CERT_DB_VERSION = '1.0.0'`; muat kelas migrasi; `maybe_migrate()` dipanggil sebelum inisialisasi database |
| `includes/class-activator.php` | `create_tables()` jadi publik; `upgrade_columns()` baru (dipindah dari Database); aktivasi menjalankan migrasi + mencatat versi |
| `includes/class-database.php` | `maybe_upgrade_database()` hanya mendelegasikan ke migrasi; `ensure_unique_indexes( $force = false )` bisa dipaksa saat migrasi |
| `tests/ph08-db-migration.php` (baru) | 18 assertion |

---

## 5. Hasil Gate

| Gate roadmap | Uji | Hasil |
|---|---|---|
| Fresh install berhasil | buat tabel pada prefix sementara (`wptest_xxxx_`), lalu hapus | **PASS** — 4 tabel + kolom `lokasi_uji` terbentuk, sisa 0 setelah dibersihkan |
| Upgrade dari skema lama berhasil | hapus opsi versi + `DROP INDEX` kedua indeks unik, lalu jalankan migrasi | **PASS** — kedua indeks kembali, versi tercatat |
| Migrasi idempoten | jalankan ulang migrasi, bandingkan `SHOW CREATE TABLE` | **PASS** — struktur identik, data utuh |
| Tidak kehilangan data | hitung baris `wp_lsp_pendaftaran` sebelum/sesudah | **PASS** — jumlah sama di setiap langkah |
| Tidak ada pemeriksaan skema berat setiap request | hitung `$wpdb->num_queries` saat versi sudah sama | **PASS** — ≤ 1 query (hanya baca opsi) |
| Data produksi tetap utuh | prefix dikembalikan, tabel asli diperiksa | **PASS** |

---

## 6. Catatan operasional

- **Naikkan `LSP_CERT_DB_VERSION`** setiap kali skema berubah, dan tambahkan langkah baru di `run()`.
  Tidak menaikkan versi berarti perubahan skema tidak akan diterapkan pada instalasi yang sudah ada.
- Migrasi berjalan pada request pertama setelah update (biasanya halaman admin). Tidak perlu
  aktivasi ulang.
- Bila sebuah langkah gagal (mis. `ALTER` ditolak hosting), versi **tidak** dicatat sehingga migrasi
  dicoba lagi pada request berikutnya. Kegagalan indeks unik dicatat ke `error_log`.
- `SHOW COLUMNS` pada langkah 2 masih berjalan **hanya saat migrasi**, bukan tiap request.

---

## 7. Temuan di luar scope (untuk fase berikutnya)

| # | Temuan | Fase |
|---|---|---|
| 1 | Tabel tidak pernah dihapus saat plugin di-uninstall (tidak ada `uninstall.php`) | pertimbangan rilis |
| 2 | `wp_lsp_pendaftaran` menyimpan `persyaratan_*` sebagai JSON teks, bukan tabel relasional | pertimbangan jangka panjang |

---

## 8. Langkah berikutnya

**PH-09 — Global Authorization & Endpoint Audit.** Audit seluruh endpoint dokumen & instrumen
terhadap IDOR, manipulasi parameter URL, capability, ownership, nonce, dan kebocoran data.
