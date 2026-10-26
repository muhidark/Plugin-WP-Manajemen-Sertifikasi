# PH-04 — Role & Capability Hardening

**Plugin:** LSP Certification Management 1.0.1
**Tanggal:** 26 Oktober 2026
**Lingkungan uji:** WordPress 7.1.3, PHP 8.5.4
**Status:** SELESAI — menunggu review sebelum PH-05

---

## 1. Ringkasan

Model hak akses sekarang memakai empat capability eksplisit, bukan nama role. Administrator mendapat
seluruhnya; asesor hanya `view_docs` + `print_docs`; pengguna lain tidak mendapat apa pun. Menu admin
ikut menyesuaikan, dan isolasi data asesor (hanya peserta yang ditugaskan) tetap berjalan.

**Koreksi penting atas audit awal:** premis H-01 tidak sepenuhnya benar. Lihat bagian 2.

---

## 2. Temuan

### PH04-01 — Koreksi: `current_user_can( 'asesor' )` ternyata BUKAN selalu false

Audit awal menyatakan pola ini membuat asesor gagal mengakses. Hasil uji nyata menunjukkan sebaliknya:
WordPress menyimpan **nama role di array capability user**, sehingga `current_user_can( 'asesor' )`
bernilai `true` bagi pemilik role tersebut. Sudah dibuktikan pada test B6.

Jadi kode lama *bekerja*, tetapi dengan tiga kelemahan nyata:

1. **Tidak ada granularitas** — tidak bisa membedakan "boleh melihat" dari "boleh mencetak", atau
   "boleh mencetak" dari "boleh mengelola kegiatan".
2. **Rapuh** — bergantung pada detail internal WP; berubah bila role dipecah, diganti nama, atau
   bila ada plugin lain yang memetakan capability.
3. **Menu tidak bisa dipisah** — semua halaman memakai `manage_options`, sehingga asesor tidak punya
   jalan masuk yang sah ke halaman cetak.

### PH04-02 — Tidak ada jalur sah bagi asesor

Seluruh halaman admin memakai `manage_options` (administrator saja). Asesor hanya bisa mencetak bila
menebak URL `?page=lsp-pendaftaran&action=print&…`, dan URL itu sendiri dijaga capability pengelola.

### PH04-03 — Halaman pengelola tanpa penjagaan internal

`LSP_Cert_Kegiatan::admin_page()` dan `LSP_Cert_Pendaftaran::admin_page()` tidak memeriksa capability
sendiri; satu-satunya penjaga adalah capability pada pendaftaran menu. Menu adalah tampilan, bukan
penegakan.

---

## 3. Model capability baru

| Capability | Administrator | Asesor | Arti |
|---|---|---|---|
| `lsp_cert_manage_activities` | ✅ | — | Kelola kegiatan & pendaftaran (jadwal, verifikasi, status) |
| `lsp_cert_manage_instruments` | ✅ | — | Kelola instrumen FR.IA.02–05, kelompok pekerjaan, paket MUK |
| `lsp_cert_view_docs` | ✅ | ✅ | Melihat area dokumen (menu utama) |
| `lsp_cert_print_docs` | ✅ | ✅ | Mencetak dokumen, termasuk FR.IA.05B |

Pemberian dilakukan sekali per versi (`lsp_cert_caps_version`), bukan setiap request, dan juga
dijalankan pada aktivasi plugin.

---

## 4. Perubahan

| Berkas | Isi |
|---|---|
| `includes/class-capabilities.php` (baru) | Definisi capability, `maybe_grant()` berbasis versi, helper `can_*()`, filter `allow_print_route` |
| `lsp-certification-management.php` | Muat + inisialisasi capability; grant saat aktivasi |
| `admin/class-admin.php` | Menu induk `view_docs`; submenu per capability; pengguna cetak-saja mendapat menu sendiri "Cetak Dokumen"; `route_default_page()` |
| `admin/class-kegiatan.php`, `admin/class-pendaftaran.php` | Guard capability di `admin_page()`; handler AJAX → `manage_activities` |
| `includes/class-ia02..05.php`, `class-kelompok-pekerjaan.php`, `class-paket-muk.php` | AJAX & halaman → `manage_instruments` |
| `admin/class-cetak-dokumen.php`, `admin/views/kegiatan-list.php` | → `print_docs` |
| `includes/class-document-printer.php` | Otorisasi cetak → `print_docs`; isolasi asesor memakai `is_asesor()` (bukan role slug sebagai capability) |
| `templates/docs/form-ak05.php`, `form-ia05b.php`, `document-layout.php` | → `print_docs` |
| `tests/ph04-capability.php` (baru) | 25 assertion |

### Catatan desain: `allow_print_route`

Tautan cetak yang sudah ada memakai URL `?page=lsp-pendaftaran&action=print&…` dan
`?page=lsp-sertifikasi&action=print&…`, halaman yang capability-nya `manage_activities`. Untuk
menghindari penulisan ulang ±20 tautan, filter `user_has_cap` memberi jalan **hanya** pada request
`action=print` untuk tiga slug itu, dan hanya kepada pemegang `print_docs`.

Aman karena handler cetak selalu berakhir sendiri: `render_print_page()` diakhiri `exit`, dan bila
data tidak valid berhenti dengan `wp_die`. Diuji: asesor membuka `?page=lsp-pendaftaran` **tanpa**
`action=print` tetap 403.

---

## 5. Hasil Gate

| Gate roadmap | Cara uji | Hasil |
|---|---|---|
| Admin tetap akses penuh | HTTP 5 halaman admin + cetak | **PASS** — semua 200 |
| Asesor sah dapat bekerja | sesi asesor (user 39), halaman + cetak | **PASS** — `lsp-cetak-dokumen` 200, FR.IA.01 200 (132.167 byte), FR.MAPA.02 200 (30.675 byte) |
| Asesor lain ditolak | pendaftaran #1 ditugaskan ke user 38, dicetak oleh 39 | **PASS** — HTTP 403 + pesan "tidak memiliki hak akses untuk mencetak dokumen peserta ini" |
| Guest/subscriber ditolak | user subscriber sementara | **PASS** — semua capability false, cetak 403 |
| Menu muncul sesuai capability | inspeksi `$menu`/`$submenu` setelah registrasi | **PASS** — induk `view_docs`; pendaftaran `manage_activities`; IA.02 & kelompok `manage_instruments`; cetak `print_docs` |
| Sidebar asesor | HTTP dengan sesi asesor | **PASS** — hanya "Cetak Dokumen" yang tampil |
| Halaman pengelola tidak bisa dibuka asesor | HTTP `lsp-sertifikasi`, `lsp-pendaftaran`, `lsp-ia02` | **PASS** — 403 semua |
| Role slug tidak dipakai lagi sebagai capability | pemindaian kode | **PASS** — hanya tersisa di komentar penjelas |
| Data uji dipulihkan | `asesor_id` pendaftaran #1 | **PASS** — kembali 39 |
| Regresi suite lain | PH-01, PH-02, PH-03, PH-03b, 2 suite Core | **PASS** — 7/7 "SEMUA LULUS" |

---

## 6. Catatan operasional

- Saat plugin di-update, capability otomatis terpasang pada request admin pertama (versi
  `lsp_cert_caps_version`). Tidak perlu aktivasi ulang.
- Asesor tidak melihat menu "Jadwal"/"Pendaftaran"; mereka masuk lewat menu **Cetak Dokumen**.
- Bila kelak ada peran baru (mis. admin LSP non-administrator), cukup beri capability yang sesuai —
  tidak perlu mengubah kode pemeriksaan.

---

## 7. Temuan di luar scope (untuk fase berikutnya)

| # | Temuan | Fase |
|---|---|---|
| 1 | `render_kegiatan_print_page()` memakai `$pendaftaran->paket_muk_id` sebelum `$pendaftaran` diisi (`null`) | PH-09 |
| 2 | `page_dashboard()` masih tidak terdaftar di menu (PH01-05) | UI/PH-10 |
| 3 | Menu "Migrasi SKKNI" (Core) memakai `manage_options`, belum capability khusus | pertimbangan Core |

---

## 8. Langkah berikutnya

**PH-05 — Public Registration Hardening.** Validasi server-side untuk pendaftaran publik: kegiatan
ditemukan, status dibuka, tanggal valid, kuota tersedia, skema valid, NIK/email tidak duplikat, nonce
dan rate limit aktif.
