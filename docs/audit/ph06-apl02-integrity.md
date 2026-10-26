# PH-06 — APL.02 Integrity Hardening

**Plugin:** LSP Certification Management 1.0.1
**Tanggal:** 26 Oktober 2026
**Lingkungan uji:** WordPress 7.1.3, PHP 8.5.4 — pengiriman HTTP nyata ke `admin-ajax.php`
**Status:** SELESAI — menunggu review sebelum PH-07

---

## 1. Ringkasan

Jawaban APL.02 sekarang dibangun ulang dari Master SKKNI. Browser hanya menentukan **pilihan K/BK**,
**kode unit**, dan **nomor elemen**; judul unit dan nama elemen selalu diambil dari master. Manipulasi
hidden field tidak lagi bisa mengubah isi dokumen resmi.

Ditemukan juga cacat tampilan yang berkaitan: template APL.02 menganggap setiap elemen yang belum
dijawab sebagai **Kompeten (K)**. Sudah diperbaiki menjadi kosong (☐).

---

## 2. Temuan

| # | Temuan | Tingkat |
|---|---|---|
| PH06-01 | `unit_kompetensi_kode`, `unit_kompetensi_judul`, `elemen_nama` disimpan apa adanya dari `$_POST` — manipulasi hidden field langsung masuk database dan dokumen cetak | **CRITICAL** |
| PH06-02 | `jawaban` tidak di-whitelist; nilai bebas (`KOMPETEN`, string kosong, apa pun) disimpan | **HIGH** |
| PH06-03 | Template APL.02 memakai fallback `?? 'K'` — elemen tanpa jawaban tercetak sebagai Kompeten | **HIGH** |
| PH06-04 | Entri dengan kode unit / nomor elemen yang tidak ada di master tetap disimpan sebagai baris yatim | **MEDIUM** |

---

## 3. Perubahan

### `public/class-form-handler.php`

Fungsi baru **`susun_jawaban_apl02( $skema_id, $input )`**:

1. Membangun peta master dari `LSP_Cert_Integration::get_unit_kompetensi()`:
   `kode unit → judul` dan `nomor elemen → nama elemen`.
2. Untuk setiap entri dari browser:
   - kode unit tidak dikenal → **diabaikan**;
   - nomor elemen tidak ada di unit tersebut → **diabaikan**;
   - `jawaban` bukan `K`/`BK` → **diabaikan** (huruf kecil dinormalkan ke huruf besar);
   - entri sah disimpan dengan **judul & nama elemen dari master**.
3. Duplikat `kode + nomor elemen` di-dedupe (entri terakhir menang).

Respons sukses kini mengembalikan `apl02_tersimpan` dan `apl02_diabaikan` agar operator bisa melihat
apakah ada data yang ditolak.

### `templates/docs/form-apl02.php`

`$ans = $jawaban_map[...] ?? 'K'` → `?? ''`. Elemen tanpa jawaban tercetak **kosong (☐☐)**, bukan
diasumsikan Kompeten. Ini menghilangkan klaim yang tidak pernah disampaikan asesi.

### `tests/ph06-apl02-integrity.php` (baru)

15 assertion: pengiriman manipulatif lewat HTTP, isi database, dan hasil cetak.

---

## 4. Hasil Gate

Skenario: kirim 6 entri APL.02 — 2 sah (satu dengan judul & nama elemen **dipalsukan**, satu dengan
jawaban huruf kecil), 4 cacat (kode unit asing, nomor elemen 99, jawaban `KOMPETEN`, jawaban kosong).

| Gate roadmap | Uji | Hasil |
|---|---|---|
| Browser hanya mengirim K/BK | jawaban `KOMPETEN` dan `''` | **PASS** — keduanya diabaikan |
| Server membangun ulang judul unit | hidden `judul` diisi "JUDUL PALSU DARI BROWSER" | **PASS** — tersimpan judul master "Menggunakan Perangkat Lunak Pengolah Kata Tingkat Dasar" |
| Server membangun ulang nama elemen | hidden `elemen_nama` diisi "ELEMEN PALSU DARI BROWSER" | **PASS** — tersimpan "Membuat dokumen" |
| Manipulasi kode unit | kode `ZZZ.000XXX.999.9` | **PASS** — diabaikan, tidak ada baris yatim |
| Manipulasi nomor elemen | nomor 99 pada unit sah | **PASS** — diabaikan |
| Normalisasi jawaban | `bk` huruf kecil | **PASS** — tersimpan `BK` |
| Hitungan respons | `apl02_tersimpan` / `apl02_diabaikan` | **PASS** — 2 tersimpan, 4 diabaikan |
| Isi database bersih dari teks palsu | pindai seluruh baris | **PASS** — 0 kemunculan "PALSU" |
| Dokumen cetak memakai data master | render FR.APL.02 | **PASS** — teks elemen asli muncul, teks palsu 0 |
| Tidak ada jawaban yang dikarang | jumlah ☑ dibanding baris tersimpan | **PASS** — 2 ☑ untuk 2 jawaban (sebelumnya semua elemen dapat ☑) |
| Regresi pendaftaran | PH-05 (26 assertion) | **PASS** |
| Regresi capability | PH-04 (25 assertion) | **PASS** |
| Cetak APL.02 data lama | pendaftaran #1 via HTTP | **PASS** — 200, 60.947 byte |
| Data uji dibersihkan | hitung ulang baris | **PASS** |

---

## 5. Batasan

- **Kelengkapan jawaban tidak dipaksa.** Pengiriman sebagian (mis. hanya 2 dari 21 elemen) tetap
  diterima; elemen yang tidak dijawab tercetak kosong. Bila nanti perlu "semua elemen wajib dijawab",
  itu keputusan aturan bisnis, bukan integritas.
- **Penomoran elemen bergantung pada master.** Bila Master SKKNI diubah (elemen dipecah/dinomor ulang)
  setelah pendaftaran tersimpan, baris jawaban lama menunjuk nomor lama. Dokumen akan menampilkan
  jawaban pada elemen berbeda. Belum ditangani di fase ini — perlu keputusan pada PH-08/PH-09
  (mis. menyimpan versi master atau memeriksa ulang saat cetak).
- Rate limit dan validasi kegiatan dari PH-05 tetap berlaku untuk jalur ini.

---

## 6. Langkah berikutnya

**PH-07 — Registration Concurrency & Uniqueness.** Menutup celah over-quota dan pendaftaran ganda
akibat request paralel (pemeriksaan `count` lalu `INSERT` belum atomik).
