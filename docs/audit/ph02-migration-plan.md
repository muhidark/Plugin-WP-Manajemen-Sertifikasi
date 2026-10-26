# Rencana Migrasi: Menghapus Elemen/KUK Karangan Tanpa Mengganggu Produksi

**Konteks:** PH-02 menghentikan pembuatan Elemen/KUK karangan di Core. Core sudah live di
`lspkomunikasi.id`, sementara sebagian Master Skema di produksi belum punya `skkni_data`
(hanya daftar unit `KODE | JUDUL`).

**Tujuan dokumen:** urutan pemasangan yang tidak mengejutkan pengguna, tidak memutus layanan, dan
bisa dibalik.

---

## 1. Apa yang sebenarnya berubah di produksi

Ini bagian yang paling menentukan rasa aman. Perubahannya **lebih kecil dari yang terlihat di audit**:

| Permukaan | Sebelum | Sesudah |
|---|---|---|
| Halaman publik skema | Tabel unit + badge "**1 Elemen**" (angka dari elemen karangan) | Tabel unit + badge "**—**" (tooltip: "Data elemen belum tersedia") |
| Teks Elemen/KUK di halaman publik | **Tidak pernah ditampilkan** — template hanya menampilkan kode, judul unit, dan jumlah elemen | Sama, tidak ditampilkan |
| Cetak FR.APL.02 / FR.IA.01 | Mencetak elemen/KUK karangan | **Ditahan** + pesan "Elemen/KUK Belum Lengkap" berisi daftar unit + tombol ke Master Skema |
| Cetak dokumen lain (APL.01, MAPA, AK, IA.02–IA.05) | Normal | **Normal, tidak terpengaruh** |
| Halaman admin Master Skema | Normal | Normal |
| Meta box Visual SKKNI Builder | Normal | Normal |

Jadi untuk pengunjung website, satu-satunya perubahan adalah badge angka berubah menjadi "—".
Konten yang dulu "palsu" memang tidak pernah terlihat publik — hanya angkanya.

Dampak yang terasa hanya di **meja kerja asesor/admin**: pencetakan APL.02 dan IA.01 untuk skema yang
belum lengkap akan ditolak, dengan pesan yang menyebut unit mana yang harus dilengkapi.

---

## 2. Tiga alat yang sudah disiapkan

### a. Halaman **LSP Core → Migrasi SKKNI**

Menu baru di sidebar LSP Core. Isinya:

- ringkasan: total skema, berapa **Lengkap**, **Belum lengkap**, **Tanpa data SKKNI**;
- tabel per skema: jumlah Unit / Elemen / KUK, status berwarna, dan **jumlah kegiatan** yang memakai
  skema tersebut (untuk menentukan prioritas);
- form import Elemen & KUK.

Halaman ini bisa dibuka **sebelum** memperbarui apa pun, karena hanya membaca data.

### b. Import Elemen & KUK (massal, tanpa builder)

**Berkas JSON dari SKKNI Extractor bisa diunggah langsung.** Halaman ini menerima:

- berkas `.json` hasil `app/lsp_exporter.py` (`unit_ke_lsp_dict()`): satu unit, array unit, atau
  `skema_ke_lsp_data()` untuk seluruh skema sekaligus;
- bentuk mentah hasil ekstraksi (`elemen_kompetensi`, `nomor_elemen`, `nama_elemen`, KUK objek
  `{nomor_kuk, uraian_kuk}`) — alias ditangani otomatis;
- berkas `.txt` berformat baris, atau tempel teks/JSON langsung di kotak isian.

Selain itu, tempel data SKKNI juga bisa lewat teks baris, satu skema sekali import:

```
UNIT: J.63OPR00.004.2 | Menggunakan Perangkat Lunak Pengolah Kata Tingkat Dasar
ELEMEN: 1 | Membuat dokumen
KUK: 1.1 Aplikasi pengolah kata dibuka.
KUK: 1.2 Dokumen baru dibuat sesuai kebutuhan.
ELEMEN: 2 | Menyimpan dokumen
KUK: 2.1 Dokumen disimpan pada folder yang ditentukan.
```

- Format JSON juga diterima.
- Ada mode **Uji coba** (default tercentang): memvalidasi tanpa menyimpan.
- Validasi menolak import jika ada unit tanpa elemen, elemen tanpa KUK, baris tak dikenal, atau
  format UNIT salah — dengan nomor baris. Data lama tidak tertimpa saat validasi gagal.
- Setelah simpan, meta `unit_kompetensi` ikut disinkronkan (kompatibel dengan halaman publik lama).

### c. Badge publik yang netral

Badge "N Elemen" menjadi "—" bila data elemen belum ada. Halaman publik tidak terlihat rusak, dan
tidak ada klaim angka yang salah.

---

## 3. Dua opsi pemasangan

### Opsi A — Pasang sekarang, lengkapi bertahap **(disarankan)**

```
1. Backup database + plugin lama
2. Upload Core baru → smoke test halaman publik & admin
3. Buka LSP Core → Migrasi SKKNI, catat skema "Tanpa data" yang punya kegiatan aktif
4. Lengkapi skema prioritas lewat import (10–30 menit per skema)
5. Upload Certification baru → cetak satu dokumen uji
```

Cocok bila: sedang **tidak ada kegiatan asesmen yang mendesak** dan tidak ada peserta yang menunggu
dokumen APL.02/IA.01 dalam beberapa hari ke depan.

Keunggulan: karangan berhenti segera; pengunjung tidak melihat angka palsu lagi.
Risiko: bila ternyata ada skema aktif yang belum lengkap, cetak APL.02/IA.01 tertahan sampai diisi.

### Opsi B — Lengkapi dulu, pasang kemudian

```
1. Pasang HANYA halaman Migrasi SKKNI (bagian dari Core) — atau jalankan audit di lokal
2. Lengkapi semua skema yang punya kegiatan aktif
3. Baru pasang Core baru + Certification baru
```

Cocok bila: ada kegiatan berjalan dan dokumen harus tetap bisa dicetak minggu ini.

Risiko: karangan masih beredar selama masa pengisian. Karena itu, jangan menunda lebih dari yang
perlu — badge "1 Elemen" yang salah tetap klaim yang keliru.

### Yang tidak disarankan

- **Memasang Certification dulu tanpa Core.** Certification baru akan menilai kelengkapan dari data
  yang dikirim Core lama — yang masih berisi elemen karangan — sehingga gate tidak menahan apa pun.
  Urutan selalu: **Core dulu, lalu Certification.**
- **Menunda tanpa tanggal.** Selama Core lama aktif, setiap skema baru yang dibuat tanpa data SKKNI
  akan menambah baris karangan baru.

---

## 4. Langkah teknis pemasangan

### Sebelum

1. Backup database (panel hosting / phpMyAdmin) dan simpan ZIP plugin versi lama.
2. Catat versi saat ini (`LSP Core Management` 1.1.0-RC1, `LSP Certification Management` 1.0.1).
3. Buka `/wp-admin/edit.php?post_type=skema&page=lsp-skkni-migration` **setelah** Core baru terpasang,
   atau jalankan audit di lokal lebih dulu.

### Pemasangan

1. Plugins → Add New → Upload → **Replace current with uploaded** untuk Core.
2. Smoke test: beranda, satu halaman skema, satu halaman TUK, dashboard admin.
3. Buka halaman Migrasi SKKNI → pastikan daftar skema terbaca.
4. Plugins → Add New → Upload → Replace untuk Certification.
5. Cetak satu dokumen **selain** APL.02/IA.01 (mis. FR.APL.01) → harus normal.
6. Cetak FR.APL.02 untuk skema yang **sudah lengkap** → harus normal.
7. Cetak FR.APL.02 untuk skema yang **belum lengkap** → harus muncul pesan penolakan yang rapi.

### Sesudah

- Isi skema prioritas lewat import, satu per satu, memakai mode uji coba dulu.
- Setiap skema yang selesai **langsung** bisa dicetak lagi — tidak perlu deploy ulang, tidak perlu
  aktivasi ulang plugin.

---

## 5. Rollback

Bila ada masalah tak terduga:

| Gejala | Tindakan |
|---|---|
| Halaman publik error setelah update Core | Rename folder `lsp-core-management` → `lsp-core-management-off`, lalu pasang kembali ZIP lama |
| Cetak APL.02 tertahan padahal data sudah diisi | Cek halaman Migrasi SKKNI: status harus "Lengkap"; periksa apakah ada unit tanpa KUK |
| Ingin kembali ke perilaku lama sementara | Pasang ZIP Core lama. **Catatan:** `skkni_data` yang sudah diimpor tetap aman, tidak hilang |

Rollback Core tidak merusak data: perubahan hanya di kode pembacaan, bukan struktur tabel.
Data `skkni_data` yang diimpor tetap tersimpan.

---

## 6. Estimasi pengisian data

Satu skema tipikal (4–9 unit, 15–40 elemen, 50–120 KUK) dari dokumen SKKNI:

- menyalin & memformat ke teks import: **10–30 menit**;
- verifikasi lewat mode uji coba: **2 menit**;
- simpan + cetak satu dokumen uji: **3 menit**.

Bila SKKNI sumber sudah berbentuk tabel (seperti `SKKNI Units - Social Media Marketing`), proses
penyalinan bisa lebih cepat karena struktur elemen/KUK sudah berurutan.

---

## 7. Rekomendasi akhir

**Pasang sekarang (Opsi A), lengkapi bertahap**, dengan satu syarat: buka halaman Migrasi SKKNI
segera setelah Core terpasang, dan dahulukan skema yang punya kegiatan aktif. Alasannya:

- dampak ke pengunjung website nyaris nol (hanya badge angka);
- dokumen resmi berhenti memuat data karangan — ini yang paling penting untuk integritas LSP;
- pencetakan terbuka kembali per skema secara otomatis, tanpa deploy ulang;
- menunda berarti terus mencetak Elemen/KUK yang tidak berasal dari SKKNI, yang berisiko saat
  dokumen dipertanyakan di audit BNSP.
