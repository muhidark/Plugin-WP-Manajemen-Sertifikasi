# PH-07 — Registration Concurrency & Uniqueness

**Plugin:** LSP Certification Management 1.0.1
**Tanggal:** 26 Oktober 2026
**Lingkungan uji:** WordPress 7.1.3, PHP 8.5.4, MySQL/MariaDB InnoDB
**Status:** SELESAI — menunggu review sebelum PH-08

---

## 1. Ringkasan

Pendaftaran sekarang atomik: kuota, duplikasi, dan insert dieksekusi di dalam satu mutex per kegiatan,
ditambah indeks unik di database sebagai pengaman terakhir. Uji paralel membuktikan kuota tidak pernah
terlampaui dan satu NIK tidak pernah tersimpan dua kali.

---

## 2. Temuan

| # | Temuan | Tingkat |
|---|---|---|
| PH07-01 | Pemeriksaan kuota (`COUNT`) dan `INSERT` terpisah. Dua request paralel dapat sama-sama melihat sisa kuota 1 dan sama-sama menyimpan | **HIGH** |
| PH07-02 | Pemeriksaan duplikasi NIK/email (`SELECT` lalu `INSERT`) tanpa pengaman database. Request paralel dengan NIK sama bisa lolos keduanya | **HIGH** |
| PH07-03 | Tabel `wp_lsp_pendaftaran` tidak punya indeks unik pada `(kegiatan_id, nik)` maupun `(kegiatan_id, email)` | **MEDIUM** |

Catatan audit: seluruh tabel `wp_lsp_*` sudah **InnoDB**, sehingga `GET_LOCK` dan transaksi tersedia.
Data saat audit tidak mengandung duplikat, jadi indeks unik bisa dipasang langsung.

---

## 3. Strategi yang dipilih

Tiga lapis, dari yang paling murah:

1. **Mutex per kegiatan — MySQL `GET_LOCK`** (`lsp_cert_daftar_<kegiatan_id>`, timeout 5 detik).
   Lock bersifat per koneksi dan otomatis dilepas saat koneksi tutup, sehingga request yang berhenti
   di tengah jalan tidak meninggalkan lock permanen.
2. **Pemeriksaan ulang di dalam lock** — kuota dan duplikasi dibaca setelah lock didapat, bukan sebelum.
3. **Indeks unik database** — `uniq_kegiatan_nik (kegiatan_id, nik)` dan
   `uniq_kegiatan_email (kegiatan_id, email)`. Bila dua request tetap lolos (mis. `GET_LOCK` dimatikan
   hosting), `INSERT` kedua gagal dengan `Duplicate entry` dan diterjemahkan menjadi 409.

Alternatif yang dipertimbangkan dan ditolak:

- **Transaksi + `SELECT … FOR UPDATE`**: benar, tetapi menahan lock baris lebih lama dan memerlukan
  pengelolaan transaksi manual di seluruh jalur. `GET_LOCK` lebih sederhana dan cakupannya jelas per
  kegiatan.
- **`INSERT … SELECT` dengan subquery kuota**: tampak atomik, tetapi tanpa gap lock pemeriksaan tetap
  bisa balapan, dan lebih sulit dibaca.

---

## 4. Perubahan

### `includes/class-database.php`

1. **`daftar_peserta( $kegiatan_id, $data )`** — operasi pendaftaran atomik:
   ambil lock → baca kegiatan → cek kuota → cek duplikasi → insert → lepas lock (`finally`).
   Status yang dikembalikan: `ok`, `penuh`, `ganda` (dengan `field`), `sibuk`, `invalid`, `gagal`.
2. **`ensure_unique_indexes()`** — memasang dua indeks unik bila belum ada, idempoten, dijalankan
   sekali per versi (`lsp_cert_index_version`), dengan `error_log` bila gagal (mis. data duplikat lama).
3. `maybe_upgrade_database()` memanggil `ensure_unique_indexes()`.

### `public/class-form-handler.php`

Blok cek-kuota → cek-duplikasi → insert diganti satu panggilan `daftar_peserta()`, lalu statusnya
dipetakan ke HTTP: `penuh`/`ganda` → **409**, `sibuk` → **429**, `invalid` → **404**, `gagal` → **500**.
Validasi PH-05 dan rebuild APL.02 PH-06 tetap berjalan sebelum pemanggilan ini.

### `tests/ph07-concurrency.php` + `tests/ph07-worker.php` (baru)

Uji paralel nyata: 10 dan 8 proses PHP terpisah yang memanggil `daftar_peserta()` serentak.

---

## 5. Hasil Gate

| Gate roadmap | Uji | Hasil |
|---|---|---|
| Tidak ada peserta ganda | 8 proses paralel dengan NIK & email identik | **PASS** — 1 `ok`, 7 `ganda`, 1 baris di database |
| Kuota tidak terlampaui | 10 proses paralel pada kegiatan kuota 1 | **PASS** — 1 `ok`, 9 `penuh`, 1 baris di database |
| Indeks unik terpasang | `SHOW INDEX` | **PASS** — `uniq_kegiatan_nik`, `uniq_kegiatan_email`, `Non_unique = 0` |
| Jalur HTTP tetap berfungsi | POST `admin-ajax.php` | **PASS** — HTTP 200, `success:true` |
| Validasi PH-05 tetap berlaku | suite PH-05 (26 assertion) | **PASS** |
| Integritas APL.02 tetap berlaku | suite PH-06 (15 assertion) | **PASS** |
| Capability tidak berubah | suite PH-04 (25 assertion) | **PASS** |
| Data uji dibersihkan | hitung ulang baris | **PASS** |

---

## 6. Batasan dan catatan operasional

- **`GET_LOCK` bergantung pada MySQL/MariaDB.** Bila hosting menonaktifkannya, fungsi mengembalikan
  `sibuk` (429) dan pendaftaran ditolak — bukan dilanjutkan tanpa proteksi. Indeks unik tetap menjadi
  lapisan terakhir.
- **Indeks unik bisa gagal dipasang bila data lama sudah duplikat.** Saat ini tidak ada duplikat;
  bila nanti ada, `error_log` mencatat dan pemeriksaan aplikasi tetap berjalan. Pembersihan data lama
  masuk lingkup PH-08 (migrasi) bila diperlukan.
- **Rate limit 3/menit tetap berlaku lebih dulu.** Pada trafik nyata, batas ini yang lebih sering
  menahan percobaan beruntun; lock menangani sisa balapan yang lolos.
- **Belum ada pembersihan peserta batal.** Kuota dihitung dari seluruh baris, termasuk pendaftaran yang
  nantinya ditolak admin. Perlu keputusan bisnis bila kuota harus melepas peserta yang gugur.

---

## 7. Langkah berikutnya

**PH-08 — Database Versioning & Migration.** Menyatukan seluruh perubahan skema (kolom, indeks) ke
dalam mekanisme `LSP_CERT_DB_VERSION` dengan langkah migrasi bernomor, sehingga upgrade berikutnya
tidak bergantung pada urutan `SHOW COLUMNS` di setiap request.
