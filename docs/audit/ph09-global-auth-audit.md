# PH-09 — Global Authorization & Endpoint Audit

**Plugin:** LSP Certification Management 1.0.1
**Tanggal:** 26 Oktober 2026
**Lingkungan uji:** WordPress 7.1.3, PHP 8.5.4 — uji HTTP dengan lima identitas
**Status:** SELESAI — menunggu review sebelum PH-10

---

## 1. Ringkasan

Seluruh endpoint dokumen dan instrumen diaudit terhadap IDOR, manipulasi parameter, capability,
kepemilikan data, nonce, dan kebocoran data. Dua celah nyata ditemukan dan ditutup:

1. **Dokumen kegiatan tanpa isolasi** — asesor dapat mencetak FR.MAPA.02 kegiatan mana pun hanya dengan
   menebak `id`.
2. **Isolasi peserta gagal-terbuka (fail-open)** — bila `pendaftaran.asesor_id` kosong, pemeriksaan
   dilewati sehingga semua asesor bisa mencetak peserta tersebut.

Ditambah satu endpoint baca AJAX tanpa pemeriksaan capability.

---

## 2. Matriks endpoint

| Endpoint | Jenis | Nonce | Capability | Isolasi data | Status |
|---|---|---|---|---|---|
| `admin.php?page=lsp-pendaftaran&action=print` | Cetak dokumen peserta | — (halaman) | `lsp_cert_print_docs` | asesor → peserta/kegiatan tugasnya | **diperbaiki** |
| `admin.php?page=lsp-sertifikasi&action=print` | Cetak dokumen kegiatan | — (halaman) | `lsp_cert_print_docs` | asesor → kegiatan tugasnya | **diperbaiki** |
| `admin.php?page=lsp-cetak-dokumen` | Portal cetak | — | `lsp_cert_print_docs` | — | OK |
| `admin.php?page=lsp-sertifikasi` | Jadwal | — | `lsp_cert_manage_activities` | — | OK |
| `admin.php?page=lsp-pendaftaran` | Daftar peserta | — | `lsp_cert_manage_activities` | — | OK |
| `admin.php?page=lsp-ia02…ia05` | Instrumen | — | `lsp_cert_manage_instruments` | — | OK |
| `admin.php?page=lsp-kelompok-pekerjaan` | Kelompok pekerjaan | — | `lsp_cert_manage_instruments` | — | OK |
| `wp_ajax_lsp_cert_save_kegiatan` | Tulis | ✅ | `manage_activities` | — | OK |
| `wp_ajax_lsp_cert_delete_kegiatan` | Hapus | ✅ | `manage_activities` | — | OK |
| `wp_ajax_lsp_cert_update_status` | Tulis | ✅ | `manage_activities` | — | OK |
| `wp_ajax_lsp_cert_save_verifikasi` | Tulis | ✅ | `manage_activities` | — | OK |
| `wp_ajax_lsp_cert_update_kelengkapan` | Tulis | ✅ | `manage_activities` | — | OK |
| `wp_ajax_lsp_cert_update_rekomendasi` | Tulis | ✅ | `manage_activities` | — | OK |
| `wp_ajax_lsp_cert_save_paket` | Tulis | ✅ | `manage_instruments` | — | OK |
| `wp_ajax_lsp_cert_clone_paket` | Tulis | ✅ | `manage_instruments` | — | OK |
| `wp_ajax_lsp_cert_delete_paket` | Hapus | ✅ | `manage_instruments` | — | OK |
| `wp_ajax_lsp_cert_set_default_paket` | Tulis | ✅ | `manage_instruments` | — | OK |
| `wp_ajax_lsp_cert_get_paket_by_skema` | **Baca** | ✅ | — → **`manage_instruments`** | — | **diperbaiki** |
| `wp_ajax(_nopriv)_lsp_cert_submit_pendaftaran` | Publik | ✅ | publik (by design) | rate limit + validasi PH-05 | OK |

Seluruh handler tulis memakai nonce; satu-satunya handler baca yang terbuka untuk semua akun login
(`get_paket_by_skema`) sekarang dibatasi ke pengelola instrumen.

---

## 3. Perubahan

### `includes/class-document-printer.php`

1. **`render_print_page()`** — isolasi peserta diubah menjadi *fail-closed* bertingkat:
   - bila `pendaftaran.asesor_id` **terisi**, hanya asesor itu (atau admin) yang boleh;
   - bila **kosong**, asesor harus terdaftar pada `kegiatan.asesor_ids` kegiatan peserta tersebut;
   - tidak memenuhi keduanya → HTTP 403.
   Sebelumnya, `asesor_id` kosong berarti **lolos tanpa pemeriksaan sama sekali**.

2. **`render_kegiatan_print_page()`** — pemeriksaan baru: asesor non-admin hanya boleh mencetak
   dokumen kegiatan tempat ia ditugaskan (`kegiatan.asesor_ids`).

### `includes/class-integration.php`

Fungsi baru `asesor_bertugas_di_kegiatan( $user_id, $kegiatan )` — membaca `asesor_ids` (JSON array),
tetap toleran terhadap format lama yang dipisah koma.

### `includes/class-paket-muk.php`

`ajax_get_paket_by_skema()` menambahkan `current_user_can( 'lsp_cert_manage_instruments' )` →
HTTP 403 untuk akun yang tidak berhak.

### `tests/ph09-authz-audit.php` (baru)

20 assertion: matriks lima identitas (admin, asesor A, asesor B, subscriber, anonim) pada endpoint
cetak peserta, cetak kegiatan, dan AJAX instrumen.

---

## 4. Hasil Gate

Gate roadmap: *"Mengetahui ID saja tidak boleh memberikan akses ke dokumen."*

| Uji | Identitas | Hasil |
|---|---|---|
| Cetak peserta miliknya | asesor A | **200** |
| Cetak peserta asesor lain (tebak ID) | asesor A | **403** |
| Cetak peserta kegiatan sendiri yang belum punya asesor | asesor A | **200** |
| Cetak peserta kegiatan lain yang belum punya asesor | asesor A | **403** ← sebelumnya 200 |
| Cetak peserta yang sudah ditugaskan ke asesor lain (walau kegiatan sama) | asesor A | **403** |
| Cetak kegiatan tugasnya (FR.MAPA.02) | asesor A | **200** |
| Cetak kegiatan asesor lain (tebak ID) | asesor A | **403** ← sebelumnya 200 |
| Cetak dokumen apa pun | subscriber | **403** |
| Cetak dokumen apa pun | anonim | **302** (login) |
| Cetak dokumen peserta & kegiatan | admin | **200** |
| Baca daftar paket MUK | admin | **200** |
| Baca daftar paket MUK | asesor / subscriber | **403** |
| Handler tulis dengan nonce palsu | asesor | ditolak |
| Pemindaian 11 handler AJAX | — | **0 tanpa nonce**, **0 tanpa capability** |
| Data uji dibersihkan | — | 0 sisa |

---

## 5. Catatan

- **Dua tingkat penugasan.** Penugasan per peserta (`pendaftaran.asesor_id`) lebih kuat daripada
  penugasan per kegiatan. Bila peserta sudah punya asesor, asesor lain di kegiatan yang sama tetap
  ditolak.
- **`asesor_ids` adalah sumber kebenaran penugasan kegiatan.** Mengosongkan kolom itu berarti tidak ada
  asesor yang bisa membuka dokumen kegiatan tersebut (kecuali admin). Ini disengaja: gagal tertutup.
- **Kunci jawaban FR.IA.05B** tetap memakai `lsp_cert_print_docs` (admin + asesor resmi), dengan
  isolasi yang sama seperti dokumen lain. Bila nanti perlu khusus admin, cukup ubah satu pemeriksaan.
- **Endpoint publik** (`submit_pendaftaran`) memang tanpa capability; pengamannya nonce, rate limit,
  validasi PH-05, dan operasi atomik PH-07.
- Dokumen **tidak** punya URL publik permanen; semua cetak melalui `admin.php` dengan sesi login.

---

## 6. Temuan di luar scope

| # | Temuan | Fase |
|---|---|---|
| 1 | `render_kegiatan_print_page()` masih memakai `$pendaftaran->paket_muk_id` sebelum `$pendaftaran` diisi (`null`) — warning PHP 8 | PH-10 / perbaikan kecil |
| 2 | Tidak ada pembatasan jumlah cetak / jejak audit siapa mencetak dokumen apa | pertimbangan rilis |

---

## 7. Langkah berikutnya

**PH-10 — Production Packaging.** Paket ZIP bersih (tanpa `.git`, `tests/`, `docs/`), versi dan
changelog dinaikkan, manifest rilis, lalu siap staging.
