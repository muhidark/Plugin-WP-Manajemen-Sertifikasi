# PH-10 — Paket Produksi & Manifest Rilis

**Tanggal:** 27 Oktober 2026 (revisi: Certification 1.1.1 — PH-11)
**Status:** SELESAI — gerbang PH-01…PH-10 terpenuhi, siap STG-01

---

## 1. Versi

| Plugin | Versi lama | Versi rilis | Alasan |
|---|---|---|---|
| LSP Core Management | 1.1.0-RC1 | **1.1.1** | perbaikan fallback SKKNI, menu Migrasi SKKNI, persyaratan global |
| LSP Certification Management | 1.0.1 | **1.1.1** | PH-01…PH-11 (keamanan, capability, konkurensi, migrasi DB, kelengkapan APL.02) |

`LSP_CERT_DB_VERSION` naik ke **1.1.0** (PH-11 menambah kolom `konfirmasi_bk` pada tabel pendaftaran).

---

## 2. Berkas paket

| Berkas | Ukuran | Berkas di dalam | SHA-256 |
|---|---|---|---|
| `lsp-core-management-1.1.1.zip` | 81 KB | 6 | `382533ff26a61736e35cf207198aced76982fabe29c3222b981107e4d9952c51` |
| `lsp-certification-management-1.1.1.zip` | 124 KB | 39 | `15d621ce9b72e0d0d8d61bc6dbb36f09b1fd97e7cd6a0570fb0c104cf8673dd8` |

Lokasi: `/home/muhadi/lsp-deploy-tools/dist/`

**Verifikasi isi:** daftar berkas di ZIP dibandingkan dua arah dengan folder sumber
(setelah pengecualian) — **selisih 0 di kedua arah** untuk kedua paket.

---

## 3. Yang dikeluarkan dari paket

`.git/`, `docs/`, `tests/`, `node_modules/`, `*.zip`, `*.tar.gz`, `*.log`, `*.bak`, `*.orig`,
`.gitignore`, `.DS_Store`.

Skrip build memeriksa ulang hasilnya: **"bersih — tidak ada .git/docs/tests/zip/log"**.

---

## 4. Hasil preflight

| Pemeriksaan | Core 1.1.1 | Cert 1.1.0 |
|---|---|---|
| Header plugin terbaca | ✔ | ✔ (1.1.1) |
| Versi header = konstanta | ✔ | ✔ (sebelumnya dilaporkan tidak sinkron karena `LSP_CERT_DB_VERSION` — skrip preflight diperbaiki) |
| Sintaks PHP bersih | ✔ 18 berkas | ✔ 66 berkas |
| Fungsi berisiko di paket | ✔ tidak ada (hanya di `tests/`, dikeluarkan) | ✔ tidak ada |

Sisa peringatan preflight (`.git`, `tests/`, `docs/`, berkas belum di-commit) hanya berlaku untuk
**folder kerja**, bukan isi ZIP — dijawab oleh pemeriksaan isi ZIP di bagian 2.

---

## 5. Urutan pemasangan

1. **Core 1.1.1 lebih dulu.** Certification 1.1.0 memeriksa keberadaan kelas Core; bila Core belum
   ada, aktivasi Certification ditolak dengan pesan jelas (bukan fatal error).
2. Unggah ZIP lewat *Plugins → Add New → Upload*, atau timpa folder `wp-content/plugins/…`.
3. Aktifkan Core, lalu Certification.
4. Buka satu halaman admin — migrasi database berjalan sekali, versi tercatat di opsi
   `lsp_cert_db_version`.
5. Periksa **LSP Core → Pengaturan**: isi persyaratan administratif (dipakai semua skema).
6. Periksa **LSP Core → Migrasi SKKNI**: status kelengkapan Elemen/KUK per skema.

---

## 6. Rollback

- Simpan ZIP versi lama (`lsp-core-management-1.1.0-RC1.zip`, `lsp-certification-management-1.0.1.zip`, `lsp-certification-management-1.1.0.zip`)
  yang masih ada di `dist/`.
- Rollback = timpa folder plugin dengan isi ZIP lama, tanpa menyentuh tabel. Struktur tabel 1.1.0
  kompatibel dengan 1.0.1 (hanya penambahan kolom & indeks).
- Opsi `lsp_cert_db_version` boleh dibiarkan; versi lama tidak membacanya.

---

## 7. Catatan

- **Jangan** menaikkan `LSP_CERT_DB_VERSION` tanpa menambah langkah migrasi — versi adalah penanda
  "sudah diterapkan", bukan nomor rilis.
- Tabel **tidak** dihapus saat uninstall (belum ada `uninstall.php`); ini disengaja agar data
  sertifikasi tidak hilang saat plugin diganti versi.

---

## 8. Langkah berikutnya

**STG-01 — Deployment ke Staging** (uji alur 17 langkah) lalu **STG-02 — Manual Print Acceptance**
(oleh pengguna).
