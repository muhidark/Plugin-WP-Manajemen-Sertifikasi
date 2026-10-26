# PH-01 — Bootstrap & Dependency Hardening

**Plugin:** LSP Certification Management 1.0.1
**Tanggal:** 26 Oktober 2026
**Lingkungan uji:** WordPress 7.1.3, PHP 8.5.4, `/var/www/html/wordpress`
**Status:** SELESAI — menunggu review sebelum PH-02

---

## 1. Ringkasan

Audit bootstrap menemukan bahwa temuan **C-01 tidak sepenuhnya akurat** untuk WordPress modern, tetapi
tetap merupakan risiko nyata untuk versi lama. Selain itu ditemukan tiga masalah lain yang lebih berdampak
pada operasional: aktivasi tanpa guard dependency, kontrak dependency yang salah sasaran, dan pemanggilan
fungsi admin yang terduplikasi di dashboard.

Semua perbaikan bersifat minimal (2 file diubah, +53/−4 baris) dan tidak menyentuh logika bisnis.

---

## 2. Temuan

### PH01-01 — C-01: `is_plugin_active()` di frontend (status terkoreksi)

**Klaim audit awal:** dependency check memanggil fungsi administrasi pada request frontend sehingga
berpotensi fatal error.

**Hasil verifikasi kode inti:**

`wp-settings.php:579` memuat `wp-admin/includes/plugin.php` **tanpa syarat**, sebelum plugin aktif
dimuat dan sebelum `do_action( 'plugins_loaded' )` di baris 630.

```php
require_once ABSPATH . 'wp-admin/includes/plugin.php';   // baris 579
foreach ( wp_get_active_and_valid_plugins() as $plugin ) { ... }  // baris 581+
do_action( 'plugins_loaded' );                            // baris 630
```

**Kesimpulan:** pada WP 7.1.3 (dan versi yang memuat perubahan #62244) `is_plugin_active()` **tersedia
di frontend**, sehingga tidak ada fatal error. Terbukti juga lewat probe HTTP frontend:
`is_plugin_active = ADA`, frontend membalas HTTP 200 tanpa fatal.

**Namun tetap diperbaiki**, karena header plugin menyatakan `Requires at least: 5.8`. Pada WP 5.8–6.6
file tersebut tidak dimuat di frontend, sehingga fungsi menjadi *undefined* dan setiap request frontend
akan berhenti fatal. Risiko ini laten, bukan aktif — tetapi biaya perbaikannya kecil.

Tingkat: **HIGH (laten, versi-dependent)**, bukan CRITICAL pada instalasi saat ini.

### PH01-02 — Aktivasi tanpa guard dependency

`lsp_cert_activate()` memanggil `LSP_Cert_Activator::activate()` tanpa memeriksa Core. Jika Core tidak
aktif, empat tabel tetap dibuat, lalu `lsp_cert_init()` berhenti di `plugins_loaded`, sehingga pengguna
mendapat instalasi setengah jadi tanpa pesan kesalahan.

Tingkat: **HIGH**

### PH01-03 — Kontrak dependency salah sasaran

Pemeriksaan memakai keberadaan *file plugin* Core, bukan keberadaan *kelas* yang benar-benar dipakai.
`includes/class-integration.php:124` bergantung pada `LSP_Core_Management::get_skema_skkni_data()`.
Bila kelas gagal dimuat (fatal di Core, kelas di-rename, atau Core di-update), Certification tetap
melanjutkan bootstrap lalu gagal jauh di dalam proses dokumen.

Tingkat: **MEDIUM**

### PH01-04 — Duplikasi pemanggilan fungsi admin

`admin/views/dashboard.php:44` memanggil `is_plugin_active()` langsung, menduplikasi logika dependency
di luar helper resmi.

Tingkat: **LOW**

---

## 3. Perubahan

### `lsp-certification-management.php`

1. Fungsi baru `lsp_cert_core_available()`:
   - cek utama: `class_exists( 'LSP_Core_Management' )` **dan** `method_exists( ..., 'get_skema_skkni_data' )`;
   - cadangan: `is_plugin_active()` bila kelas belum tersedia;
   - `wp-admin/includes/plugin.php` hanya dimuat bila fungsi tersebut belum ada (aman untuk WP lama);
   - cache hanya untuk hasil `true`, agar tidak "meracuni" hasil negatif pada request yang sama.
2. `lsp_cert_check_dependencies()` memakai helper tersebut; notice hanya didaftarkan pada `is_admin()`,
   dan kegagalan dicatat sekali per request melalui `error_log`.
3. `lsp_cert_activate()` menolak aktivasi bila Core tidak tersedia: `deactivate_plugins()` +
   `wp_die()` dengan pesan yang jelas, sehingga tidak ada tabel yatim.

### `admin/views/dashboard.php`

Baris 44 memakai `lsp_cert_core_available()` (dengan `function_exists()` guard), bukan `is_plugin_active()`.

### `tests/ph01-dependency-guard.php` (baru)

Test gate mandiri (tanpa WordPress penuh) dengan 8 assertion untuk tiga kondisi: fungsi `is_plugin_active()`
belum ada, Core tidak tersedia, dan Core tersedia.

---

## 4. Hasil Gate

### 4.1 Kondisi normal (Core aktif)

| Gate roadmap | Cara uji | Hasil |
|---|---|---|
| Tidak ada fatal error | `php -l` seluruh file plugin; HTTP frontend | **PASS** — 0 syntax error, HTTP 200 |
| Frontend WordPress normal | `curl` beranda | **PASS** — 200, 78.038 byte, tanpa "Fatal error" |
| Admin Certification normal | HTTP terautentikasi ke 6 halaman admin | **PASS** — lsp-sertifikasi, lsp-pendaftaran, lsp-cetak-dokumen, lsp-ia02, lsp-ia05, lsp-kelompok-pekerjaan semua HTTP 200 |
| Form publik tetap dapat dibuka | `do_shortcode()` kedua shortcode | **PASS** — daftar 965 byte, form 101 byte, tanpa error |
| AJAX tetap terdaftar & terjaga | `POST admin-ajax.php action=lsp_cert_submit_pendaftaran` | **PASS** — HTTP 403 `-1` (nonce ditolak, sesuai harapan) |
| Generator dokumen lama tetap dapat dipanggil | render FR.APL.01 untuk pendaftaran #1 | **PASS** — 31.978 byte, judul "Dokumen Asesmen #1", 0 error |
| Handler cetak terdaftar | `has_action( 'admin_init', ... )` | **PASS** (kegiatan & pendaftaran) |
| Guard dependency (unit) | `tests/ph01-dependency-guard.php` | **PASS** — 8/8, exit 0 |

### 4.2 Kondisi Core dinonaktifkan (uji pada instalasi terpasang)

Uji dilakukan langsung pada site `http://localhost/wordpress` dengan menonaktifkan Core
sementara, lalu mengaktifkannya kembali. `active_plugins` diverifikasi **identik** dengan kondisi awal
setelah pengujian.

| Gate roadmap | Cara uji | Hasil |
|---|---|---|
| Frontend tetap normal saat Core mati | HTTP frontend | **PASS** — HTTP 200, 61.291 byte, 0 "Fatal error"/"critical error" |
| Admin tetap dapat dibuka | HTTP terautentikasi `wp-admin/index.php` | **PASS** — HTTP 200, 89.013 byte |
| Notice dependency muncul | cari teks di HTML admin | **PASS** — "memerlukan LSP Core Management untuk aktif" tampil di `notice notice-error` |
| Plugin berhenti dengan rapi | akses `admin.php?page=lsp-sertifikasi` | **PASS** — HTTP 403 "Sorry, you are not allowed to access this page" (menu & kelas admin tidak dimuat) |
| Aktivasi ditolak saat Core mati | panggil `lsp_cert_activate()` dengan handler `wp_die` pengganti | **PASS** — `wp_die` terpanggil: "LSP Certification Management tidak dapat diaktifkan. Aktifkan LSP Core Management terlebih dahulu…" |
| Plugin tidak tertinggal aktif | cek status setelah guard | **PASS** — Certification otomatis di-`deactivate_plugins()`, status kembali mati |
| Pemulihan setelah Core diaktifkan | aktivasi ulang Core lalu Certification | **PASS** — keduanya AKTIF, seluruh halaman admin kembali 200, menu lengkap |

Catatan metode: penonaktifan/aktivasi dilakukan lewat `deactivate_plugins()` / `activate_plugin()`
(API WordPress), bukan edit langsung tabel `wp_options`. Backup nilai `active_plugins` disimpan di
`/tmp/active_plugins_backup.txt` sebelum pengujian.

---

## 5. Temuan tambahan (di luar scope perbaikan)

### PH01-05 — `page_dashboard()` tidak terdaftar di menu mana pun

`LSP_Cert_Admin::page_dashboard()` (`admin/class-admin.php:166`) beserta
`admin/views/dashboard.php` tidak pernah didaftarkan lewat `add_menu_page()` / `add_submenu_page()`.
Menu utama `lsp-sertifikasi` diarahkan ke `LSP_Cert_Kegiatan::admin_page()`.

Akibatnya halaman dashboard — termasuk baris status Core yang diperbaiki pada PH01-04 — tidak dapat
diakses dari UI. Kode lama ini tidak berbahaya, tetapi:

- perbaikan PH01-04 di `dashboard.php` praktis dorman;
- statistik yang sudah dihitung di `page_dashboard()` tidak pernah tampil.

**Rekomendasi:** putuskan pada fase berikutnya — daftarkan sebagai submenu "Dashboard", atau hapus
dead code. Belum diubah pada PH-01 karena di luar scope hardening bootstrap.

---

## 6. Batasan pengujian

- Uji Core-nonaktif memakai site lokal, bukan staging. Perilaku produksi (cache, keamanan host,
  plugin lain) belum tercakup.
- Uji aktivasi memakai handler `wp_die` pengganti agar skrip tidak berhenti; pesan dan efek samping
  (`deactivate_plugins`) tetap nyata, tetapi tampilan layar `wp_die` tidak diverifikasi secara visual.
- `tests/` harus dikecualikan dari paket produksi (PH-10).

---

## 7. Langkah berikutnya

**PH-02 — SKKNI / Elemen / KUK Integrity.** Fokus: menghapus fallback Elemen/KUK buatan di
`includes/class-integration.php` dan memastikan generator berhenti dengan pesan jelas bila data master
tidak lengkap.

Gate PH-02 tidak boleh dimulai sebelum hasil PH-01 ini direview.
