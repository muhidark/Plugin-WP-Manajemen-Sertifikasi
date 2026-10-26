# PH-05 — Public Registration Hardening

**Plugin:** LSP Certification Management 1.0.1
**Tanggal:** 26 Oktober 2026
**Lingkungan uji:** WordPress 7.1.3, PHP 8.5.4, uji HTTP nyata ke `admin-ajax.php`
**Status:** SELESAI — menunggu review sebelum PH-06

---

## 1. Ringkasan

Pendaftaran publik sudah punya nonce, rate limit, cek kuota, dan cek duplikasi NIK/email. Yang belum
ada: **aturan kegiatan** (status, tanggal, keabsahan skema) dan **validasi format identitas**. Keduanya
kini ditegakkan di server, sehingga request langsung tidak lagi bisa melewati aturan yang hanya tampil
di formulir.

Bukti: 26 assertion lulus, semuanya lewat HTTP nyata ke `admin-ajax.php` — bukan unit test fungsi.

---

## 2. Temuan

| # | Temuan | Tingkat |
|---|---|---|
| PH05-01 | Status kegiatan tidak diperiksa di server. Formulir hanya menampilkan kegiatan `dibuka`, tetapi POST langsung ke kegiatan `ditutup`/`draft`/`selesai` tetap diterima | **HIGH** |
| PH05-02 | Tanggal pelaksanaan tidak diperiksa. Kegiatan yang sudah lewat tetap bisa didaftari | **HIGH** |
| PH05-03 | Skema tidak divalidasi. Kegiatan dengan `skema_id` yatim tetap menerima pendaftaran, lalu dokumen asesmen gagal disusun | **MEDIUM** |
| PH05-04 | Format identitas tidak divalidasi: NIK bebas, tanggal lahir bebas string, jenis kelamin bebas, kode pos bebas, tujuan asesmen bebas | **MEDIUM** |

Yang **sudah** benar sebelum PH-05 (dipertahankan): nonce `lsp_cert_public_nonce`, rate limit 3 kiriman
per menit per IP, cek kuota, dan cek duplikasi NIK/email per kegiatan.

---

## 3. Perubahan

### `public/class-form-handler.php`

1. **`validasi_kegiatan( $kegiatan )`** — menolak bila:
   - status bukan `dibuka`;
   - `tanggal_pelaksanaan` kosong atau sudah lewat (dibanding `current_time( 'Y-m-d' )`);
   - `skema_id` tidak menunjuk Master Skema yang sah.
   Respons: HTTP **409** dengan pesan spesifik.

2. **`validasi_identitas( $post )`** — menolak bila:
   - NIK bukan 16 angka;
   - nomor telepon di luar 8–15 angka;
   - kode pos bukan 5 angka;
   - tanggal lahir bukan format `YYYY-MM-DD`, di masa depan, atau usia < 15 tahun;
   - jenis kelamin bukan `Laki-laki`/`Perempuan`;
   - tujuan asesmen di luar daftar formulir (`sertifikasi`, `sertifikasi_ulang`, `pkt`, `rpl`, `lainnya`).

Urutan pemeriksaan: nonce → rate limit → field wajib → format email → kegiatan ada → **aturan kegiatan**
→ **format identitas** → kuota → duplikasi → insert.

### `tests/ph05-public-registration.php` (baru)

26 assertion lewat HTTP: fixture kegiatan (terbuka, ditutup, kedaluwarsa, skema rusak, penuh),
pengiriman nyata ke `admin-ajax.php`, lalu pembersihan seluruh data uji.

---

## 4. Hasil Gate

| Gate roadmap | Uji | Hasil |
|---|---|---|
| Kegiatan ditemukan | `kegiatan_id` palsu | **PASS** — "Kegiatan tidak ditemukan" |
| Status = dibuka | kegiatan `ditutup` | **PASS** — HTTP 409, pesan menyebut ditutup |
| Tanggal valid | kegiatan 3 hari lalu | **PASS** — HTTP 409, "sudah lewat" |
| Kuota tersedia | kegiatan kuota 1 yang sudah terisi | **PASS** — "penuh" |
| Skema valid | kegiatan dengan `skema_id` 99999999 | **PASS** — HTTP 409, pesan menyebut skema |
| NIK/email tidak duplikat | kirim ulang NIK sama, lalu email sama | **PASS** — HTTP 409 untuk keduanya |
| Request sah (nonce) | nonce palsu / tanpa nonce | **PASS** — HTTP 403 `-1` |
| Rate limit aktif | 4 kiriman berturut-turut | **PASS** — 3 diproses, ke-4 HTTP 429 |
| Format identitas | NIK 5 angka, tanggal `05-05-1995`, tanggal masa depan, jenis kelamin `Lainnya`, kode pos `40`, tujuan `sembarang` | **PASS** — semuanya ditolak dengan pesan spesifik |
| Pendaftaran sah tetap berhasil | kegiatan terbuka, data valid | **PASS** — HTTP 200, `pendaftaran_id` dikembalikan |
| Data uji dibersihkan | hitung ulang baris | **PASS** — 0 sisa kegiatan & pendaftaran uji |
| Regresi tampilan publik | beranda & shortcode daftar | **PASS** — HTTP 200, shortcode 965 byte tanpa error |

Catatan: halaman publik (`class-public.php:31,73`) memang sudah menyaring `status === 'dibuka'`. Yang
ditambahkan PH-05 adalah penegakan yang sama di sisi server, karena tampilan bukan pengaman.

---

## 5. Batasan

- **Kuota masih bisa terlewati oleh request paralel.** Pemeriksaan `count >= kuota` lalu `INSERT`
  bukan operasi atomik. Perbaikannya di **PH-07** (unique constraint / transaksi / atomic insert),
  bukan di sini.
- Rate limit berbasis `REMOTE_ADDR`. Di balik reverse proxy, semua pengunjung bisa terlihat satu IP.
  Bila produksi memakai proxy, perlu penyesuaian header — belum diubah karena belum ada buktinya.
- Data APL.02 masih dipercaya dari browser; itu **PH-06**.

---

## 6. Langkah berikutnya

**PH-06 — APL.02 Integrity Hardening.** Browser hanya boleh mengirim pilihan `K`/`BK`; struktur unit,
elemen, dan KUK harus dibangun ulang di server dari Master SKKNI.
