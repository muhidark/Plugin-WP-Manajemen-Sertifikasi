# Laporan Production Hardening
## LSP Core Management + LSP Certification Management
### PH-01 s.d. PH-11 dan STG-01

**Tanggal:** 27 Oktober 2026
**Lingkungan uji:** WordPress 7.1.3, PHP 8.5.4, MySQL/MariaDB — `http://localhost/wordpress`
**Hasil akhir:** 346 pemeriksaan otomatis, **0 gagal**; 14 dari 14 rangkaian uji lulus
**Versi rilis:** Core **1.1.1**, Certification **1.1.1** (skema database 1.1.0)

---

## 1. Ringkasan

Sebelas fase perbaikan dikerjakan bertahap pada dua plugin. Fokusnya bukan menambah fitur,
melainkan menutup celah yang bisa membuat dokumen sertifikasi resmi berisi data keliru atau bisa
diakses orang yang tidak berhak.

Tiga hal terpenting yang berubah:

1. **Dokumen tidak lagi memuat data karangan.** Elemen/KUK yang dulu dibuat otomatis ketika data
   SKKNI belum ada sekarang dihapus. Bila master belum lengkap, pencetakan ditahan dengan pesan jelas.
2. **Identitas contoh dihapus.** Nama dan Nomor MET contoh ("Masriah", "Eko Prasyanto",
   `MET.000.032423 2019`) tidak pernah lagi muncul di dokumen; field kosong tercetak kosong.
3. **Hak akses dipertegas.** Asesor hanya bisa membuka peserta dan kegiatan yang ditugaskan
   kepadanya; dokumen dengan kolom kosong (mis. FR.APL.02 tanpa jawaban) tidak bisa diterima
   maupun dicetak.

---

## 2. Peta fase

| Fase | Masalah yang ditutup | Perbaikan | Bukti uji | Commit (Cert) |
|---|---|---|---|---|
| PH-01 | Pemeriksaan dependency bisa fatal di frontend | Kontrak kelas Core (`class_exists` + `method_exists`), aktivasi aman | 8 pemeriksaan | `d3a2c8f` |
| PH-02 | Elemen/KUK dibuat otomatis (karangan) saat master kosong | Fallback karangan dihapus di **Core**; gerbang cetak `enforce_skkni_integrity()` | 34 pemeriksaan; dokumen 60,9 KB vs 5,1 KB saat ditolak | `7cd25fb` |
| PH-02b | Data SKKNI lama belum punya elemen/KUK | Menu **Migrasi SKKNI**: impor teks/JSON, uji coba, badge publik netral | 29 + 18 pemeriksaan | `9099b09` |
| PH-03 | 44 literal identitas contoh di 9 template + printer | Semua dihapus; banner admin untuk field kosong | 13 pemeriksaan | `6b105f5` |
| PH-03b | Persyaratan administrasi berbeda-beda per skema | Satu pengaturan global di Core, dipakai semua skema | 12 pemeriksaan | `305eae9` |
| PH-04 | `current_user_can('asesor')` — role dipakai sebagai capability | 4 capability eksplisit + menu terpisah untuk asesor | 27 pemeriksaan | `8ba7a6d` |
| PH-05 | Aturan kegiatan hanya dicek di tampilan | Validasi server-side: status, tanggal, skema, format identitas | 28 pemeriksaan | `7703595` |
| PH-06 | Jawaban APL.02 diambil mentah dari browser | Dibangun ulang dari Master SKKNI; hanya K/BK dari pengguna | 19 pemeriksaan | `f10a2e9` |
| PH-07 | Kuota bisa terlampaui & peserta ganda saat request paralel | Mutex `GET_LOCK` + cek ulang dalam lock + indeks unik NIK/email | 14 pemeriksaan; 10 proses paralel | `3c5391e` |
| PH-08 | Pemeriksaan skema jalan di setiap request; tidak ada versi DB | `LSP_CERT_DB_VERSION` + migrasi idempoten | 17 pemeriksaan | `d8efcf1` |
| PH-09 | Dua celah IDOR: dokumen kegiatan tanpa isolasi, isolasi peserta fail-open | Isolasi berlapis (peserta > kegiatan), capability pada endpoint baca | 20 pemeriksaan; 5 identitas | `fe98e8d` |
| PH-10 | Belum ada paket rilis bersih | ZIP tanpa `.git`/`tests`/`docs`, versi + changelog + manifest | 0 selisih isi ZIP | `c2b2493`, `a44a515` |
| STG-01 | Belum ada uji alur menyeluruh | 17 langkah roadmap dijalankan (aktivasi → dokumen → hak akses) | 74 pemeriksaan | `4db5f41` |
| PH-11 | FR.APL.02 tercetak kosong dan tetap bisa diterima | Empat lapis pengaman kelengkapan APL.02 | 33 pemeriksaan | `1e2df61` |

---

## 3. Celah keamanan yang ditutup

| # | Celah | Dampak bila dibiarkan | Status |
|---|---|---|---|
| 1 | Dependency check memanggil fungsi admin di frontend | Fatal error saat situs diakses | Ditutup |
| 2 | Elemen/KUK karangan masuk dokumen resmi | Dokumen asesmen memuat kompetensi palsu | Ditutup |
| 3 | Identitas penyusun/validator contoh | Dokumen resmi memuat nama orang yang tidak berhak | Ditutup |
| 4 | Role dipakai sebagai capability | Asesor bisa masuk halaman pengelolaan | Ditutup |
| 5 | Validasi kegiatan hanya di tampilan | Pendaftaran pada kegiatan tutup/lewat masa tetap masuk | Ditutup |
| 6 | Jawaban APL.02 dari browser mentah | Judul unit & nama elemen bisa dipalsukan | Ditutup |
| 7 | Balapan kuota & duplikasi NIK | Kuota terlampaui; peserta ganda | Ditutup |
| 8 | IDOR dokumen kegiatan & peserta | Asesor lain bisa mencetak dokumen siapa pun dengan menebak ID | Ditutup |
| 9 | Endpoint baca AJAX tanpa capability | Akun login mana pun bisa membaca daftar paket MUK | Ditutup |
| 10 | FR.APL.02 kosong tetap diterima & dicetak | Formulir kosong ditandatangani sebagai bukti resmi | Ditutup (PH-11) |

---

## 4. Perubahan basis data

| Objek | Perubahan |
|---|---|
| `wp_lsp_kegiatan` | tambah kolom `lokasi_uji`, `alamat_lokasi_uji` |
| `wp_lsp_pendaftaran` | tambah kolom `konfirmasi_bk`; indeks unik `uniq_kegiatan_nik`, `uniq_kegiatan_email` |
| Opsi baru | `lsp_cert_db_version`, `lsp_cert_index_version`, `lsp_core_persyaratan_admin` |

Migrasi berjalan **sekali** pada request pertama setelah update; tidak perlu aktivasi ulang.
Setiap langkah idempoten sehingga aman diulang dan aman untuk instalasi baru.

---

## 5. Paket rilis

| Berkas | Versi | Berkas di dalam | SHA-256 |
|---|---|---|---|
| `lsp-core-management-1.1.1.zip` | 1.1.1 | 6 | `382533ff26a61736e35cf207198aced76982fabe29c3222b981107e4d9952c51` |
| `lsp-certification-management-1.1.1.zip` | 1.1.1 | 39 | `15d621ce9b72e0d0d8d61bc6dbb36f09b1fd97e7cd6a0570fb0c104cf8673dd8` |

Lokasi: `/home/muhadi/lsp-deploy-tools/dist/`

**Urutan pasang:** Core dulu → Certification → buka satu halaman admin (migrasi jalan) →
isi **LSP Core → Pengaturan** (persyaratan administrasi) → periksa **Migrasi SKKNI**.

**Rollback:** timpa folder plugin dengan ZIP versi lama. Struktur tabel tetap kompatibel
(hanya penambahan kolom/indeks), jadi tidak perlu memulihkan basis data.

---

## 6. Hasil uji

| Rangkaian uji | Pemeriksaan | Hasil |
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
| STG-01 acceptance 17 langkah | 74 | Lulus |
| **Total Certification** | **299** | **0 gagal** |
| Core: parser impor SKKNI | 29 | Lulus |
| Core: jembatan SKKNI Extractor | 18 | Lulus |
| **Total keseluruhan** | **346** | **0 gagal** |

Uji dilakukan pada situs terpasang (HTTP nyata dengan cookie admin/asesor/pelanggan), bukan
hanya pembacaan kode.

---

## 7. Keputusan yang diambil

| Keputusan | Alasan |
|---|---|
| Perbaikan bertahap, satu fase per putaran | Sesuai metode kerja yang diminta; tiap fase punya gerbang uji |
| Core diperbaiki lebih dulu | Akar masalah Elemen/KUK ada di Core, bukan di Certification |
| Data tidak lengkap → cetak ditahan | Lebih baik menolak daripada mengisi dokumen resmi dengan tebakan |
| APL.02 sebagian ditolak, bukan disimpan | Setengah formulir lebih berbahaya karena terlihat sudah diisi |
| Pendaftaran tanpa APL.02 tetap boleh | Asesi bisa mengisi menyusul; yang dilarang adalah *melanjutkan* tanpa jawaban |
| Jawaban BK wajib konfirmasi | Konfirmasi ke asesi adalah bukti, bukan ingatan; waktunya dicatat |
| Peserta > kegiatan untuk isolasi | Penugasan per peserta lebih kuat daripada per kegiatan |
| Persyaratan administrasi global | Sama untuk semua skema, sesuai keputusan pemilik proses |

---

## 8. Yang belum selesai

| # | Hal | Catatan |
|---|---|---|
| 1 | **STG-02 uji cetak manual** | Harus dinilai manusia: A4, margin, page break, checkbox, pagination, tanda tangan. Checklist tersedia |
| 2 | **Server staging sungguhan** | STG-01 dijalankan di instalasi lokal; unggah ZIP ke staging masih perlu dilakukan |
| 3 | Data uji harus dihapus | Kegiatan #76, peserta #88, asesor #55 (data STG) — hapus sebelum kegiatan nyata |
| 4 | Kuota & peserta gugur | Pendaftaran yang ditolak admin tetap memakai kuota; perlu keputusan bisnis |
| 5 | Belum ada `uninstall.php` | Tabel sengaja tidak dihapus saat uninstall agar data sertifikasi tidak hilang |
| 6 | REL-01 & PROD-01 | Belum dijalankan; menunggu hasil STG-02 |

---

## 9. Berkas laporan per fase

Seluruh dokumen ada di
`/var/www/html/wordpress/wp-content/plugins/lsp-certification-management/docs/audit/`:

`ph01-bootstrap-dependency.md`, `ph02-skkni-integrity.md`, `ph02-migration-plan.md`,
`ph03-document-identity.md`, `ph04-role-capability.md`, `ph05-public-registration.md`,
`ph06-apl02-integrity.md`, `ph07-registration-concurrency.md`, `ph08-db-migration.md`,
`ph09-global-auth-audit.md`, `ph10-release-manifest.md`, `ph11-apl02-completeness.md`,
`stg01-browser-acceptance.md`, `stg02-print-acceptance-checklist.md`

---

## 10. Lampiran — riwayat commit

**Certification** (`muhidark/Plugin-WP-Manajemen-Sertifikasi`), 16 commit:

```
1e2df61 fix(ph11): FR.APL.02 tidak boleh kosong — 4 lapis pengaman
4db5f41 test(stg01): acceptance 17 langkah lulus (73 pemeriksaan) + checklist STG-02
a44a515 docs(ph10): manifest rilis 1.1.0 + hasil verifikasi paket
c2b2493 chore(release): naikkan versi ke 1.1.0 + changelog
fe98e8d fix(ph09): isolasi dokumen fail-closed + capability pada endpoint baca
d8efcf1 feat(ph08): versioning skema database + migrasi idempoten
3c5391e fix(ph07): pendaftaran atomik — mutex per kegiatan + indeks unik
f10a2e9 fix(ph06): jawaban APL.02 dibangun ulang dari Master SKKNI
7703595 feat(ph05): validasi server-side aturan kegiatan + format identitas pendaftaran
8ba7a6d feat(ph04): model capability eksplisit menggantikan role slug
305eae9 fix(ph03b): persyaratan admin dari pengaturan global, persyaratan dasar per skema
6b105f5 fix(ph03): hapus identitas contoh dari seluruh dokumen
967982a docs(ph02): catat dukungan berkas JSON SKKNI Extractor pada rencana migrasi
9099b09 docs(ph02): rencana migrasi pemasangan tanpa mengganggu produksi
7cd25fb fix(ph02): hapus fallback Elemen/KUK karangan + gate integritas SKKNI
d3a2c8f fix(ph01): guard dependency Core + aktivasi aman
```

**Core** (`muhidark/plugin-WP-LSP-Core`), 8 commit:

```
a918b5d chore(release): naikkan versi ke 1.1.1
713bc78 feat(core): pengaturan global persyaratan administratif untuk semua skema
4db588e feat(core): importer SKKNI menerima berkas JSON SKKNI Extractor
42b6507 feat(core): halaman Migrasi SKKNI + import Elemen/KUK + badge publik netral
c3faae5 fix(skema): hentikan Elemen/KUK karangan pada fallback get_skema_skkni_data
f47ca79 feat(security): enforce registered participant validation on manual cert save and CSV import
566f727 feat(skema): modularize skema into includes/class-skema.php with Visual SKKNI Builder & Smart Parser
778eccf feat(skema): add acuan_skkni and skkni_data (elemen and kuk) support
```

---

**Status akhir:** kode sudah di-push ke GitHub (HEAD lokal = remote). Paket produksi siap,
menunggu uji cetak manual (STG-02) sebelum REL-01/PROD-01.
