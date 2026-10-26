# REL-01 — Release Candidate

**Tanggal:** 27 Oktober 2026
**Syarat masuk:** STG-01 lulus (74 pemeriksaan) dan STG-02 dinyatakan **LULUS** oleh pengguna
**Status:** SELESAI — paket produksi siap, menunggu PROD-01

---

## 1. Freeze kode

| Repo | HEAD | Status kerja |
|---|---|---|
| `muhidark/Plugin-WP-Manajemen-Sertifikasi` | `9d74892` | bersih (0 perubahan) |
| `muhidark/plugin-WP-LSP-Core` | `63dab58` | bersih (0 perubahan) |

Sejak titik ini tidak ada perubahan kode. Perubahan berikutnya hanya boleh berupa perbaikan darurat
yang dicatat di changelog.

---

## 2. Versi

| Plugin | Versi | `readme.txt` | Changelog |
|---|---|---|---|
| LSP Core Management | **1.1.1** | ada (baru) | 1.1.1 / 1.1.0 / 1.0.x |
| LSP Certification Management | **1.1.1** | ada | 1.1.1 / 1.1.0 / 1.0.1 / 1.0.0 |

Header `Version:` = konstanta `*_VERSION` = `Stable tag` (diperiksa preflight, tidak ada peringatan).

`LSP_CERT_DB_VERSION` = **1.1.0** (dipisah dari versi rilis; migrasi menambah kolom `konfirmasi_bk`).

---

## 3. Regression test final

Dijalankan pada kode beku, situs terpasang (`http://localhost/wordpress`):

| Rangkaian | Pemeriksaan | Hasil |
|---|---|---|
| PH-01 dependency guard | 8 | Lulus |
| PH-02 integritas SKKNI | 34 | Lulus |
| PH-03 identitas dokumen | 13 | Lulus |
| PH-03b persyaratan administrasi | 12 | Lulus |
| PH-04 capability | 27 | Lulus |
| PH-05 pendaftaran publik | 28 | Lulus |
| PH-06 integritas APL.02 | 19 | Lulus |
| PH-07 konkurensi pendaftaran | 14 | Lulus |
| PH-08 migrasi database | 17 | Lulus |
| PH-09 otorisasi endpoint | 20 | Lulus |
| PH-11 kelengkapan APL.02 | 33 | Lulus |
| STG-01 acceptance | 74 | Lulus |
| Core: parser impor SKKNI | 29 | Lulus |
| Core: jembatan SKKNI Extractor | 18 | Lulus |
| **Total** | **346** | **0 gagal** |

---

## 4. Paket produksi

| Berkas | Berkas di dalam | Ukuran | SHA-256 |
|---|---|---|---|
| `lsp-core-management-1.1.1.zip` | 7 | 82 KB | `1c8b4b6ca4da0be41b66675588932f292e9a4c1b467e7ea801d4b3131aed4a3e` |
| `lsp-certification-management-1.1.1.zip` | 39 | 124 KB | `047e04ef08130604497d67831918198e3d9455958935e64c07f01483ee11ff6b` |

Lokasi: `/home/muhadi/lsp-deploy-tools/dist/`
Berkas checksum: `/home/muhadi/lsp-deploy-tools/dist/SHA256SUMS-1.1.1.txt`

**Verifikasi isi:** daftar berkas ZIP dibandingkan dua arah dengan folder sumber setelah
pengecualian (`.git`, `docs`, `tests`, arsip, log) → **selisih 0 di kedua arah** untuk kedua paket.
Pemeriksaan isi: "bersih — tidak ada .git/docs/tests/zip/log".

> Catatan: hash berubah setiap kali ZIP dibangun ulang karena stempel waktu di dalam arsip.
> Yang menjadi acuan adalah hash pada tabel di atas — cocokkan dengan `SHA256SUMS-1.1.1.txt`.

---

## 5. Smoke test

`smoke-test.sh http://localhost/wordpress`:

| Pemeriksaan | Hasil |
|---|---|
| Halaman utama | HTTP 200 |
| Aset plugin (CSS/JS) | HTTP 200 |
| Handler `lsp_verify_cert` | hidup |
| Handler `lsp_cert_submit_pendaftaran` | hidup (nonce ditolak — wajar) |
| Handler `lsp_cert_get_paket_by_skema` | peringatan: hanya untuk pengguna login |
| Halaman `/berita/`, `/cek-sertifikat/`, `/skema/` | peringatan: belum ada di instalasi lokal |

**Perbaikan alat ukur:** versi lama `smoke-test.sh` menganggap balasan `-1` sebagai "handler tidak
ada" (padahal itu nonce ditolak = handler hidup) dan menganggap halaman 404 sebagai kerusakan.
Keduanya menghasilkan alarm palsu yang bisa memicu rollback yang tidak perlu. Sekarang:
`-1` → hidup, `0` → peringatan (khusus login), 404 → peringatan, 5xx / fatal error → gagal.
Kesimpulan pada instalasi lokal: **tidak ada kerusakan**, 4 peringatan.

---

## 6. PROD-01 — persiapan

### Yang harus ada sebelum menyentuh produksi

| # | Berkas / data | Catatan |
|---|---|---|
| 1 | Backup database `lspkomunikasi.id` | dari panel hosting; verifikasi hasilnya bisa dibuka |
| 2 | Backup folder plugin lama (Core versi saat ini) | ZIP atau unduh lewat File Manager |
| 3 | Catat versi Core yang sedang aktif | bukti titik balik bila perlu rollback |
| 4 | `wp-config.php` tersalin | jangan diubah; hanya untuk pembanding |

### Urutan eksekusi (dari roadmap)

1. Update **Core 1.1.1** → smoke test → 2. Update **Certification 1.1.1** → smoke test
3. Periksa error log → 4. Test satu kegiatan → 5. Test satu pendaftaran
6. Test satu dokumen per kelompok (APL.01, APL.02, MAPA, AK, IA) → 7. Test akses asesor
8. Baru buka operasional normal

### Catatan khusus untuk produksi

- **Core sudah live** di produksi dengan data nyata. Perubahan Core 1.1.1 menghapus Elemen/KUK
  karangan; skema yang belum punya data SKKNI akan menampilkan badge "—" (bukan elemen palsu).
  Ini disengaja, tetapi terlihat pengguna — laporkan dulu bila ada pihak yang keberatan.
- **Certification belum pernah dipasang** di produksi. Setelah aktivasi, migrasi membuat 4 tabel
  dan kolom/indeks tambahan pada request admin pertama.
- Isi **LSP Core → Pengaturan** (persyaratan administratif) sebelum pendaftaran pertama dibuka,
  karena nilainya dipakai bersama semua skema.
- Data uji lokal (#76/#88/#55) **tidak ikut** ke produksi — tidak perlu dihapus di sana.

---

## 7. Langkah berikutnya

**PROD-01** — deployment ke `https://lspkomunikasi.id` mengikuti urutan di atas, setelah backup
database dan plugin lama tersedia.
