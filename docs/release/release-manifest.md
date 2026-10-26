# Release Manifest — LSP Core Management & LSP Certification Management

## Release

| Hal | Nilai |
|---|---|
| Tanggal release | 27 Oktober 2026 |
| LSP Certification Management | **1.1.2** |
| LSP Core Management | **1.1.1** |
| Skema database Certification | **1.1.0** |
| Target environment | Produksi `https://lspkomunikasi.id` (WordPress) |
| Commit Certification | `ea89774` (tag `v1.1.2`) — kode micro-fix di `19e387c`, dokumen+changelog di `ea89774` |
| Commit Core | `63dab58` |
| Status rilis | **GO WITH NOTES** — lihat bagian Validation Status |

## Files

| Plugin | Berkas | Ukuran | Berkas di dalam | SHA-256 |
|---|---|---|---|---|
| Core | `lsp-core-management-1.1.1.zip` | 82 KB | 7 | `f225043529230d062c879e2410a90440cc9fc05602e66957debd1c240b02ab9c` |
| Certification | `lsp-certification-management-1.1.2.zip` | 124 KB | 39 | `7f3763d2f28a8497a588685fd6c5f6371f8cb07e3303d773853a7d6eab65af13` |

Lokasi: `/home/muhadi/lsp-deploy-tools/dist/`
Berkas checksum: `/home/muhadi/lsp-deploy-tools/dist/SHA256SUMS.txt`

Verifikasi cepat sebelum unggah:

```bash
cd /home/muhadi/lsp-deploy-tools/dist && sha256sum -c SHA256SUMS.txt
```

## Dependency

- **LSP Core Management harus aktif lebih dulu.** Certification menolak aktivasi bila kelas
  `LSP_Core_Management` dengan metode `get_skema_skkni_data()` tidak tersedia.
- Certification bergantung pada Core; Core **tidak** bergantung pada Certification (tidak ada
  circular dependency).
- Source **tidak menetapkan** nomor versi minimum Core. Yang diperiksa adalah kontrak API.
  Versi yang diuji bersama rilis ini: **Core 1.1.1**.

## Validation Status

| Validasi | Lingkungan | Hasil |
|---|---|---|
| PH-01 … PH-11 (hardening otomatis) | lokal | PASS — 299 pemeriksaan Certification |
| Uji Core (parser impor SKKNI, jembatan ekstraktor) | lokal | PASS — 47 pemeriksaan |
| STG-01 acceptance 17 langkah | instalasi lokal (padanan staging) | PASS — 74 pemeriksaan |
| Micro-fix 1.1.2 (MF-01…MF-18) | lokal | PASS — 26 pemeriksaan |
| Manual browser acceptance | `http://localhost/wordpress` | PASS — dinyatakan pengguna, 27 Okt 2026 |
| Manual print acceptance (STG-02) | lokal, cetak/pratinjau | PASS — dinyatakan pengguna, 27 Okt 2026 |
| Verifikasi isi paket (struktur, lint, hash per berkas) | ZIP final | PASS — 0 selisih |
| Smoke test produksi | `https://lspkomunikasi.id` | **BELUM dijalankan** |
| Backup produksi | server produksi | **BELUM dibuat** |
| Rollback teruji di produksi | server produksi | **BELUM** (prosedur tertulis, belum dieksekusi) |

> Yang belum dilakukan tidak diklaim PASS. Dua butir terakhir hanya bisa dikerjakan pada PROD-01.

## Catatan rilis

1. **Core lebih dulu, baru Certification.** Mengunggah Certification lebih dulu akan ditolak
   aktivatornya dengan pesan yang jelas (bukan fatal error).
2. **Migrasi berjalan sekali** pada request admin pertama setelah aktivasi Certification
   (membuat tabel `wp_lsp_*`, kolom `lokasi_uji`/`alamat_lokasi_uji`/`konfirmasi_bk`, dan indeks unik).
3. **Perubahan yang terlihat pengguna di Core:** skema tanpa data SKKNI menampilkan badge "—"
   (sebelumnya menampilkan Elemen/KUK karangan). Cetak FR.APL.02/FR.IA.01 ditahan sampai master lengkap.
4. **Persyaratan administratif** diisi sekali di LSP Core → Pengaturan dan berlaku untuk semua skema.
5. **Data uji** tidak ikut ke produksi — kegiatan #76 / peserta #88 / asesor #55 hanya ada di lokal.

## Rollback ringkas

| Skenario | Tindakan |
|---|---|
| Certification bermasalah | nonaktifkan Certification → pulihkan folder versi sebelumnya → verifikasi Core |
| Core bermasalah | nonaktifkan Certification → pulihkan Core → verifikasi master data → aktifkan Certification |
| Perlu rollback database | hanya bila ada perubahan data yang harus dibatalkan; **tidak otomatis** |

## Dokumen terkait

- `docs/release/REL-01-final-release-preparation.md` — verifikasi lengkap, backup, urutan deployment,
  smoke test, keputusan rilis.
- `docs/audit/00-laporan-hardening-ph01-ph11.md` — ringkasan seluruh fase hardening.
- `docs/audit/microfix-1.1.2.md` — laporan micro-fix terakhir.
