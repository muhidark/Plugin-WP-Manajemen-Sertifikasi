# PH-03 — Identity & Document Metadata Hardening

**Plugin:** LSP Certification Management 1.0.1
**Tanggal:** 26 Oktober 2026
**Lingkungan uji:** WordPress 7.1.3, PHP 8.5.4
**Status:** SELESAI — menunggu review sebelum PH-04

---

## 1. Ringkasan

Seluruh identitas contoh (nama penyusun, nama validator, Nomor MET) sudah dihapus dari kode.
Tidak ada lagi jalur yang mengisi kolom tanda tangan dengan nama orang yang tidak dipilih admin.
Bila data master kosong, dokumen tetap tercetak dengan kolom identitas **kosong**, ditambah
peringatan khusus admin yang tidak ikut tercetak.

---

## 2. Temuan

### PH03-01 — Identitas contoh tersebar di 9 template + printer

44 kemunculan literal ditemukan:

| Berkas | Isi |
|---|---|
| `form-ia01.php`, `form-ia02.php`, `form-ia03.php`, `form-ia04a.php`, `form-ia04b.php`, `form-ia05a.php`, `form-ia05b.php` | 4 baris tiap berkas: `'Masriah'`, `'MET.000.032423 2019'`, `'Eko Prasyanto'`, `'MET.000.014249 2019'` |
| `form-mapa01.php`, `form-mapa02.php` | fallback `??` pada sel nama & Nomor MET penyusun/validator |
| `includes/class-document-printer.php` | 2 blok (render pendaftaran & render kegiatan), 4 fallback per blok |

Dampak: setiap dokumen yang datanya belum diisi mencetak nama dan Nomor MET orang yang tidak
berhubungan dengan kegiatan tersebut — pada dokumen resmi bertanda tangan.

Tingkat: **HIGH**

### PH03-02 — Tidak ada penanda saat identitas kosong

Setelah fallback dihapus, kolom akan kosong tanpa penjelasan. Operator bisa mengira dokumen rusak.

Tingkat: **MEDIUM**

---

## 3. Perubahan

### Template & printer

Semua literal identitas contoh diganti string kosong (`''`). Sumber sah tetap dipakai berurutan:
data pendaftaran → data kegiatan → asesor yang ditugaskan. Tidak ada sumber keempat.

### `includes/class-document-printer.php`

Fungsi baru `daftar_identitas_kosong( $penyusun, $validator )` mengembalikan label field yang belum
terisi (Nama/Nomor MET Penyusun & Validator). Dipanggil di kedua jalur render.

### `templates/docs/document-layout.php`

Bila ada field kosong **dan** pengguna adalah admin, tampil banner peringatan di atas area kertas
dengan kelas `lsp-no-print` — sehingga terlihat di layar tetapi tidak tercetak:

> **Peringatan admin:** identitas tim asesor belum lengkap — Nama Validator, Nomor MET Validator.
> Bagian tersebut tercetak **kosong** (tidak memakai nama contoh) dan dapat diisi manual setelah dicetak.

### `tests/ph03-identity.php` (baru)

13 assertion: pemindaian kode, helper, render 22 jenis dokumen, skenario metadata kosong, dan
verifikasi pemulihan data.

---

## 4. Hasil Gate

| Gate roadmap | Cara uji | Hasil |
|---|---|---|
| Tidak ada nama contoh muncul otomatis | pindai 10 berkas + render 22 dokumen | **PASS** — 0 kemunculan `Masriah`/`Eko Prasyanto`/`MET.000.032423`/`MET.000.014249` |
| Tidak ada Nomor MET dummy | idem | **PASS** |
| Template tetap berfungsi jika metadata kosong | kosongkan identitas kegiatan & pendaftaran sementara, render FR.MAPA.02 | **PASS** — dokumen tetap > 1000 byte, kolom kosong, banner muncul |
| Banner tidak ikut tercetak | cek kelas `lsp-no-print` + aturan `@media print` | **PASS** |
| Data dipulihkan setelah uji | bandingkan 6 kolom identitas kegiatan + 4 kolom pendaftaran | **PASS** — identik |
| Cetak nyata via HTTP | `?page=lsp-pendaftaran&action=print&id=1&doc=ia01` | **PASS** — 132.167 byte, nama asli tampil (penyusun 3×, validator 1×), 0 identitas contoh |
| Regresi | beranda & admin Certification | **PASS** — HTTP 200 |

---

## 5. Catatan

- Nama asli pada data uji (`masriah hebat`, `Muhadi Tri Wusana`) berasal dari data yang Anda isi
  sendiri di kegiatan — bukan dari kode. Tidak ada yang diubah.
- Istilah "kosong" berarti kolom tanda tangan tanpa teks. Cetak-tanda tangan manual tetap berjalan:
  nama dapat ditulis tangan atau dilengkapi lebih dulu di data kegiatan.

---

## 6. Tambahan PH-03b — Persyaratan Dasar & Administratif

**Konteks:** persyaratan administratif memang sama untuk semua skema, sehingga daftar generik di kode
bukan sekadar "data karangan" — tetapi tempatnya salah (hardcoded, tidak bisa diubah admin). Persyaratan
dasar sebaliknya bersifat per skema (ijazah, pengalaman, sertifikat pelatihan) dan tidak boleh disamakan.

### Perubahan

| Berkas | Isi |
|---|---|
| Core `lsp-core-management.php` | Pengaturan baru **Persyaratan Administratif (sama untuk semua skema)** di LSP Core → Pengaturan LSP, satu baris per persyaratan; helper `get_persyaratan_admin_global()` jadi satu-satunya penentu nilai bawaan |
| Cert `class-integration.php` | `get_persyaratan_admin()`: meta skema → pengaturan global Core → kosong. `get_persyaratan_dasar()`: meta skema saja. Daftar bawaan dihapus dari plugin |
| `tests/ph03b-persyaratan.php` | 12 assertion |

### Hasil uji

| Uji | Hasil |
|---|---|
| Skema dengan meta sendiri | **PASS** — dasar & administratif dari meta, teks generik 0 |
| Skema tanpa meta | **PASS** — dasar kosong (tidak dikarang), administratif 2 baris dari pengaturan global |
| Pengaturan global diubah | **PASS** — 3 baris baru langsung dipakai skema tanpa meta |
| Meta skema menang atas global | **PASS** |
| Pengaturan global dipulihkan | **PASS** |
| Halaman Pengaturan LSP (HTTP) | **PASS** — HTTP 200, field baru tampil dengan nilai bawaan KTP + pas foto |
| FR.APL.01 (HTTP) | **PASS** — 31.983 byte, memakai persyaratan skema; 0 teks generik lama |

**Catatan perilaku:** skema yang belum mengisi `persyaratan_skema` sekarang mencetak bagian
"Persyaratan Dasar" **kosong** — bukan daftar karangan. Isi lewat Master Skema agar FR.APL.01 lengkap.

---

## 7. Temuan di luar scope (untuk fase berikutnya)

| # | Temuan | Fase |
|---|---|---|
| 1 | `render_kegiatan_print_page()` memakai `$pendaftaran->paket_muk_id` sebelum `$pendaftaran` diisi (`null`) | PH-09 |
| 2 | `page_dashboard()` tidak terdaftar di menu (PH01-05) | UI/PH-10 |

---

## 8. Langkah berikutnya

**PH-04 — Role & Capability Hardening.** Menyatukan model hak akses: `current_user_can('asesor')`
dipakai sebagai capability padahal `asesor` adalah role di Core. Gate: admin penuh, asesor sah bisa
bekerja, asesor lain ditolak, guest ditolak, menu sesuai capability.
