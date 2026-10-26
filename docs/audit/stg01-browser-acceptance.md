# STG-01 — Deployment & Browser Acceptance (instalasi lokal sebagai staging)

**Tanggal:** 26 Oktober 2026
**Lingkungan:** WordPress 7.1.3, PHP 8.5.4, MySQL/MariaDB — `http://localhost/wordpress`
**Paket diuji:** Core 1.1.1 + Certification 1.1.0 (ZIP bersih PH-10)
**Status:** **LULUS — 73 pemeriksaan, 0 gagal**

> Catatan: server staging terpisah belum tersedia. Instalasi lokal dipakai sebagai padanan staging.
> Langkah deploy ke server staging sungguhan tetap perlu diulang di sana (unggah ZIP, aktivasi,
> uji alur) — hasilnya akan sama karena paket dan basis data identik.

---

## 1. Hasil per langkah roadmap

| # | Langkah | Hasil | Keterangan |
|---|---|---|---|
| 1 | Aktivasi Core | **PASS** | dinonaktifkan lalu diaktifkan; kelas `LSP_Core_Management` tersedia |
| 2 | Aktivasi Certification | **PASS** | aktivasi setelah Core; 4 tabel tersedia; versi skema `1.0.0` tercatat |
| 3 | Master Skema | **PASS** | skema #224 — 4 unit / 21 elemen / 79 KUK; halaman publik 200; editor admin 200 |
| 4 | Master TUK | **PASS** | TUK tersedia dan terhubung ke kegiatan |
| 5 | Master Asesor | **PASS** | akun role `asesor`, Nomor MET tersimpan, punya `lsp_cert_print_docs`, **tidak** punya `manage_activities` |
| 6 | Kelompok Pekerjaan | **PASS** | 2 kelompok tersimpan per skema; halaman admin 200 |
| 7 | Instrumen | **PASS** | halaman IA.02–IA.05 semua 200; editor menampilkan daftar unit |
| 8 | Pembuatan Kegiatan | **PASS** | dibuat lewat AJAX admin (bukan SQL langsung); muncul di daftar |
| 9 | Pendaftaran Asesi | **PASS** | dikirim lewat endpoint publik dengan nonce; tersimpan; muncul di admin |
| 10 | APL.01 | **PASS** | 31,3 KB |
| 11 | APL.02 | **PASS** | 59,5 KB — Elemen/KUK dari master, tanpa penolakan integritas |
| 12 | MAPA | **PASS** | MAPA.01 obs/dit/vp/gabungan 38–84 KB; MAPA.02 kegiatan 30,0 KB |
| 13 | AK | **PASS** | AK.01–AK.07 (20–36 KB) |
| 14 | IA | **PASS** | IA.01 129,1 KB; IA.02 34,5; IA.03 37,4; IA.04a/b; IA.05a/b/c |
| — | Gabungan | **PASS** | `all` 75,8 KB; `bundle_all` 376,4 KB |
| 15 | Hak akses Admin | **PASS** | 6 halaman utama 200 |
| 16 | Hak akses Asesor | **PASS** | cetak dokumen tugasnya 200; kelola kegiatan/peserta/instrumen **403** |
| 17 | Pengguna tidak berwenang | **PASS** | subscriber 403; anonim 302 (login); AJAX tanpa nonce ditolak; asesor lain 403 |

Setiap dokumen juga diverifikasi **tidak** memuat teks "Pencetakan Ditolak" atau "Akses Ditolak".

---

## 2. Data uji yang dipertahankan (untuk STG-02)

| Objek | ID | Catatan |
|---|---|---|
| Skema | #224 | SPESIALIS MANAJEMEN MEDIA SOSIAL |
| Kegiatan | #76 | kuota 5, status dibuka, tanggal +14 hari |
| Peserta | #88 | "Peserta Uji STG-01", asesor sudah ditugaskan |
| Asesor | #55 | `stg01_asesor_7901`, MET.STG.000001.2026 |

URL cetak untuk uji manual:
```
http://localhost/wordpress/wp-admin/admin.php?page=lsp-pendaftaran&action=print&id=88&doc=apl01
http://localhost/wordpress/wp-admin/admin.php?page=lsp-sertifikasi&action=print&id=76&doc=mapa02
```

> Data ini **data uji**, bukan peserta sungguhan. Hapus kegiatan #76 dan peserta #88 sebelum
> kegiatan nyata dimulai, agar tidak ikut terhitung kuota atau muncul di laporan.

Akun uji sementara (subscriber dan asesor kedua) sudah dihapus pada akhir pengujian.

---

## 3. Temuan selama pengujian

| # | Temuan | Dampak | Tindakan |
|---|---|---|---|
| 1 | Tidak ada | — | — |

Tidak ada kegagalan, warning PHP, atau dokumen yang ditolak pada seluruh 73 pemeriksaan.

---

## 4. Yang belum tercakup (memang di luar STG-01)

- **Tampilan cetak sesungguhnya** (ukuran A4, margin, page break, checkbox, pagination) → **STG-02**,
  harus dinilai manusia, bukan skrip.
- **Server staging/produksi sungguhan** — unggah ZIP, aktivasi, dan pengaturan PHP/hosting.
- **Kiriman email/WhatsApp** dan integrasi eksternal lain.

---

## 5. Langkah berikutnya

**STG-02 — Manual Print Acceptance** oleh pengguna, memakai data uji di bagian 2.
Checklist tersedia di `docs/audit/stg02-print-acceptance-checklist.md`.
