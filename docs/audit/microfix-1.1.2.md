# MICRO-FIX v1.1.2 — Koreksi Final Pra-Staging

**Plugin:** LSP Certification Management
**Baseline:** v1.1.1 · **Target:** v1.1.2
**Tanggal:** 27 Oktober 2026
**Status akhir:** **PASS WITH NOTES** (lihat §10 — satu catatan kecil, tidak menghalangi)

---

## 1. Baseline v1.1.1

| Hal | Nilai |
|---|---|
| Commit baseline (Certification) | `0ff3dc3` |
| Versi plugin | 1.1.1 (`Version:`, `LSP_CERT_VERSION`, `Stable tag` seragam) |
| Versi skema DB | 1.1.0 (tidak berubah pada micro-fix ini) |
| Regression baseline | 346 pemeriksaan, 0 gagal |
| Paket | `lsp-certification-management-1.1.1.zip` (39 berkas) |

---

## 2. Dua temuan yang diperbaiki

### FIX-01 — Fallback nama asesor contoh pada tampilan admin

**Temuan:** literal `Masriah Hebat` dipakai sebagai fallback nama asesor pada tampilan admin
*Cetak Dokumen* (FR.MAPA.01 dan FR.MAPA.02). Bila peserta belum punya asesor, dokumen pratinjau
menampilkan nama orang yang tidak terkait.

**Perbaikan:** fallback diganti netral **"Belum ditentukan"**. Bila asesor tersedia, nama asesor
aktual tetap ditampilkan.

| Letak | Sebelum | Sesudah |
|---|---|---|
| `admin/views/cetak-dokumen.php:361` | `esc_html( $asesor_mapa['name'] ?? 'Masriah Hebat' )` | `esc_html( ! empty( $asesor_mapa['name'] ) ? $asesor_mapa['name'] : 'Belum ditentukan' )` |
| `admin/views/cetak-dokumen.php:477` | `esc_html( $asesor_mapa2['name'] ?? 'Masriah Hebat' )` | `esc_html( ! empty( $asesor_mapa2['name'] ) ? $asesor_mapa2['name'] : 'Belum ditentukan' )` |

Sumber data asesor tidak diubah (`$selected_pendaftaran->asesor_id` → `LSP_Cert_Integration::get_asesor()`).

### FIX-02 — Referensi `$pendaftaran` pada dokumen tingkat kegiatan

**Temuan:** `render_kegiatan_print_page()` membaca `$pendaftaran->paket_muk_id`, padahal method itu
bekerja pada level kegiatan dan `$pendaftaran` tidak pernah diisi di sana. Pada PHP 8 ini memicu
"Attempt to read property on null" (warning tersembunyi karena nilai jatuh ke fallback kegiatan).

**Perbaikan:** sumber Paket MUK untuk dokumen kegiatan hanya dari kegiatan.

```php
// sebelum
$paket_muk_id = ! empty( $pendaftaran->paket_muk_id )
    ? (int) $pendaftaran->paket_muk_id
    : ( ! empty( $kegiatan->paket_muk_id ) ? (int) $kegiatan->paket_muk_id : 0 );

// sesudah
$paket_muk_id = ! empty( $kegiatan->paket_muk_id ) ? (int) $kegiatan->paket_muk_id : 0;
```

Kolom `wp_lsp_kegiatan.paket_muk_id` **ada** pada struktur data aktual (diverifikasi dengan
`SHOW COLUMNS`), jadi `$kegiatan->paket_muk_id` adalah sumber yang sah.

Penggunaan `$pendaftaran->paket_muk_id` pada `render_print_page()` (dokumen peserta) **dibiarkan** —
di sana konteksnya benar.

---

## 3. File yang berubah

| Berkas | Perubahan |
|---|---|
| `admin/views/cetak-dokumen.php` | 2 fallback nama asesor → netral |
| `includes/class-document-printer.php` | sumber Paket MUK dokumen kegiatan = `$kegiatan` |
| `lsp-certification-management.php` | versi 1.1.1 → 1.1.2 |
| `readme.txt` | `Stable tag` 1.1.2 + entri changelog 1.1.2 |
| `tests/mf-112-microfix.php` | uji MF-01…MF-18 (baru, tidak ikut paket) |
| `docs/audit/microfix-1.1.2.md` | laporan ini (tidak ikut paket) |

**Jumlah berkas berubah: 6** (4 runtime, 2 development).

---

## 4. Diff ringkas

```diff
--- a/admin/views/cetak-dokumen.php
+++ b/admin/views/cetak-dokumen.php
@@ -361 +361 @@
-  &bull; <strong>Asesor:</strong> <?php echo esc_html( $asesor_mapa['name'] ?? 'Masriah Hebat' ); ?>
+  &bull; <strong>Asesor:</strong> <?php echo esc_html( ! empty( $asesor_mapa['name'] ) ? $asesor_mapa['name'] : 'Belum ditentukan' ); ?>
@@ -477 +477 @@
-  &bull; <strong>Asesor:</strong> <?php echo esc_html( $asesor_mapa2['name'] ?? 'Masriah Hebat' ); ?>
+  &bull; <strong>Asesor:</strong> <?php echo esc_html( ! empty( $asesor_mapa2['name'] ) ? $asesor_mapa2['name'] : 'Belum ditentukan' ); ?>

--- a/includes/class-document-printer.php
+++ b/includes/class-document-printer.php
@@ render_kegiatan_print_page()
-        $paket_muk_id = ! empty( $pendaftaran->paket_muk_id )
-            ? (int) $pendaftaran->paket_muk_id
-            : ( ! empty( $kegiatan->paket_muk_id ) ? (int) $kegiatan->paket_muk_id : 0 );
+        // Ambil Paket MUK level KEGIATAN ... `$pendaftaran` tidak boleh dipakai di sini.
+        $paket_muk_id = ! empty( $kegiatan->paket_muk_id ) ? (int) $kegiatan->paket_muk_id : 0;
```

---

## 5. Hasil pengujian (MF-01 … MF-18)

| Uji | Yang diperiksa | Hasil |
|---|---|---|
| MF-01 | MAPA.01 admin + asesor ada → nama aktual tampil | PASS |
| MF-02 | MAPA.01 admin + asesor kosong → "Belum ditentukan", tanpa nama contoh | PASS |
| MF-03 | MAPA.02 admin + asesor ada → nama aktual tampil | PASS |
| MF-04 | MAPA.02 admin + asesor kosong → fallback netral | PASS |
| MF-05 | Tidak ada literal nama contoh di berkas runtime | PASS (0 temuan) |
| MF-06 | `render_kegiatan_print_page()` tidak membaca `$pendaftaran->paket_muk_id` | PASS |
| MF-07 | Dokumen kegiatan memakai `$kegiatan->paket_muk_id` | PASS |
| MF-08 | Dokumen peserta tetap memakai `$pendaftaran->paket_muk_id` | PASS |
| MF-09–MF-13 | MAPA.01 (obs/dit/vp/all), AK.05, AK.06, IA.01–IA.05C dapat dicetak | PASS (14 dokumen) |
| MF-10b–10d | FR.MAPA.02 kegiatan 200, 30,4 KB, tanpa warning PHP | PASS |
| MF-14 | Skema DB tidak berubah (1.1.0); kolom `wp_lsp_kegiatan` tetap 18 | PASS |
| MF-15 | Capability/role tidak berubah | PASS |
| MF-16 | Pendaftaran publik tetap berjalan (200) | PASS |
| MF-17 | Gerbang APL.02 tetap aktif (cetak ditahan 409) | PASS |
| MF-18 | PHP lint seluruh plugin | PASS (0 error) |

Total uji micro-fix: **26 pemeriksaan, 0 gagal**.

---

## 6. Hasil regression

Seluruh rangkaian yang ada dijalankan ulang pada kode 1.1.2:

| Rangkaian | Hasil |
|---|---|
| PH-01 dependency guard | LULUS |
| PH-02 integritas SKKNI | LULUS |
| PH-03 identitas dokumen | LULUS |
| PH-03b persyaratan administrasi | LULUS |
| PH-04 capability | LULUS |
| PH-05 pendaftaran publik | LULUS |
| PH-06 integritas APL.02 | LULUS |
| PH-07 konkurensi pendaftaran | LULUS |
| PH-08 migrasi database | LULUS |
| PH-09 otorisasi endpoint | LULUS |
| PH-11 kelengkapan APL.02 | LULUS |
| STG-01 acceptance | LULUS |
| MF-1.1.2 micro-fix | LULUS |
| Core: parser impor SKKNI | LULUS |
| Core: jembatan SKKNI Extractor | LULUS |

**15 dari 15 rangkaian lulus.**

---

## 7. PHP lint

`php -l` pada seluruh berkas PHP plugin: **0 error** (dijalankan sebagai bagian MF-18 dan sebelum build).
Build script juga melakukan lint dan menyatakan paket bersih.

---

## 8. Version bump

| Lokasi | Nilai |
|---|---|
| Header `Version:` | 1.1.2 |
| Konstanta `LSP_CERT_VERSION` | 1.1.2 |
| `readme.txt` → `Stable tag` | 1.1.2 |
| Changelog | entri 1.1.2 ditambahkan |
| `LSP_CERT_DB_VERSION` | **1.1.0** (sengaja tidak dinaikkan — tidak ada perubahan skema) |
| Core | **tidak diubah** (tetap 1.1.1) |

---

## 9. Package final

| Hal | Nilai |
|---|---|
| Berkas | `lsp-certification-management-1.1.2.zip` |
| Lokasi | `/home/muhadi/lsp-deploy-tools/dist/` |
| Berkas di dalam | 39 (sama seperti 1.1.1) |
| Ukuran | 124 KB |
| SHA-256 | `178f1ac911421722c8e35740aac8292280333dd942f3e1ba0d956899a5fde6fa` |
| Checksum file | `/home/muhadi/lsp-deploy-tools/dist/SHA256SUMS-1.1.2.txt` |

Pemeriksaan paket:

- daftar berkas ZIP vs folder sumber (setelah pengecualian) → **selisih 0 di kedua arah**
- pemeriksaan isi: "bersih — tidak ada .git/docs/tests/zip/log"
- header di dalam ZIP: `Version: 1.1.2`, `Stable tag: 1.1.2`

---

## 10. Risiko tersisa

| # | Risiko | Catatan |
|---|---|---|
| 1 | **Catatan kecil (sebab status PASS WITH NOTES):** pada putaran pertama, changelog di `readme.txt` memuat nama contoh yang sedang dihapus, sehingga uji MF-05 gagal (1 temuan). Changelog diubah agar tidak memuat nama contoh; uji ulang PASS (0 temuan). Tidak ada nama contoh tersisa di berkas runtime. | Sudah diperbaiki |
| 2 | FR.MAPA.02 kegiatan kini memakai Paket MUK kegiatan. Kegiatan lama dengan `paket_muk_id = 0` akan memakai Paket MUK default skema — perilaku ini sama seperti sebelumnya (jalur fallback yang dulu terpakai). | Pantau saat STG-01 |
| 3 | `render_print_page()` (peserta) masih memakai `$pendaftaran->paket_muk_id` — memang benar untuk konteks peserta, tetapi perlu diingat bila kelak method ini dipakai ulang untuk level kegiatan. | Catatan desain |
| 4 | Nama contoh lain (`Eko Prasyanto`, Nomor MET contoh) sudah tidak ada di runtime sejak PH-03; MF-05 memeriksa keduanya. | — |
| 5 | Build ZIP menyegarkan stempel waktu sehingga hash berubah tiap build; acuan resmi adalah `SHA256SUMS-1.1.2.txt`. | — |

---

## 11. Git

| Hal | Nilai |
|---|---|
| Commit sebelum | `0ff3dc3` |
| Commit sesudah | commit tunggal v1.1.2 — `git log -1 --format='%h %s'` |
| Jumlah berkas berubah | 6 |
| Berkas | `admin/views/cetak-dokumen.php`, `includes/class-document-printer.php`, `lsp-certification-management.php`, `readme.txt`, `tests/mf-112-microfix.php`, `docs/audit/microfix-1.1.2.md` |
| Pesan commit | `v1.1.2: fix admin identity fallback and activity package source` |

---

## 12. Status

**PASS WITH NOTES** — kedua perbaikan selesai, seluruh uji dan regression lulus, versi 1.1.2,
paket produksi dibuat, laporan ini dibuat. Catatan pada §10 butir 1 sudah ditutup dalam putaran
yang sama.

Berhenti di sini: tidak melanjutkan STG-01, tidak melakukan deployment.
