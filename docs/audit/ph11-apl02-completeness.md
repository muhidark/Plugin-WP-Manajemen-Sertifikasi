# PH-11 — Kelengkapan FR.APL.02 (temuan uji manual)

**Tanggal:** 27 Oktober 2026
**Asal temuan:** pemeriksaan manual pengguna pada dokumen cetak STG-02
**Status:** SELESAI — 30 pemeriksaan lulus

---

## 1. Temuan

> "Seharusnya di bagian APL 02 (asesmen mandiri) peserta menyatakan K semua, atau kalau ada
> beberapa yang BK maka wajib dilakukan konfirmasi ke asesi. Karena ini virtual testing mungkin
> tidak dilakukan checklist sehingga kosong. Saya selaku admin juga kurang mengecek, langsung
> klik diterima/dilanjutkan."

Dokumen FR.APL.02 tercetak dengan **seluruh kolom K dan BK kosong**, lalu pendaftaran tetap bisa
diterima admin.

---

## 2. Akar masalah

| # | Sebab | Akibat |
|---|---|---|
| 1 | Tidak ada validasi kelengkapan saat APL.02 dikirim | Jawaban sebagian (bahkan nol) tersimpan tanpa peringatan |
| 2 | Tidak ada gerbang saat admin menerima pendaftaran | Berkas kosong lolos ke tahap asesmen |
| 3 | Gerbang cetak PH-02 hanya memeriksa **master SKKNI**, bukan **jawaban asesi** | APL.02 kosong tetap bisa dicetak dan ditandatangani |
| 4 | Halaman verifikasi tidak menampilkan status APL.02 | Admin tidak punya alasan untuk curiga |

Ketiga hal itu saling menutupi: masing-masing mengira pihak lain sudah memeriksa.

---

## 3. Mitigasi — empat lapis

| Lapis | Di mana | Perilaku |
|---|---|---|
| **1. Saat pengisian** | `public/class-form-handler.php` | Kiriman APL.02 yang tidak lengkap **ditolak (HTTP 422)**, dengan jumlah elemen kosong. Validasi dilakukan **sebelum** pendaftaran disimpan, sehingga tidak ada pendaftaran yatim |
| **2. Saat penerimaan** | `admin/class-pendaftaran.php` | "Diterima" atau "Lengkap" **ditolak (HTTP 409)** bila APL.02 kosong atau belum lengkap, dengan daftar elemen yang belum dijawab |
| **3. Saat cetak** | `includes/class-document-printer.php` | Cetak **FR.APL.02, FR.APL.01+02, dan paket lengkap** ditahan (HTTP 409) selama masih ada elemen kosong |
| **4. Di layar admin** | `admin/views/pendaftaran-detail.php` | Kotak status "FR.APL.02 — terisi X dari Y (K: n, BK: m)" berwarna hijau/kuning/merah, plus pesan galat server yang ditampilkan apa adanya |

### Aturan jawaban BK

Bila ada elemen dijawab **BK**, penerimaan mewajibkan centang **"Sudah dikonfirmasi ke asesi"**.
Tanpa centang → ditolak (HTTP 409) dengan pesan menyebut jumlah elemen BK. Saat dicentang,
waktu konfirmasi disimpan di kolom `konfirmasi_bk` — jadi ada jejak bahwa konfirmasi benar terjadi.

### Kebijakan yang dipilih

- **Pendaftaran tanpa APL.02 tetap diterima.** Asesi boleh mendaftar dulu, mengisi asesmen mandiri
  menyusul. Yang dilarang adalah **melanjutkan** pendaftaran tanpa jawaban.
- **APL.02 sebagian tidak pernah disimpan.** Setengah formulir lebih berbahaya daripada kosong,
  karena terlihat seperti sudah diisi.
- **APL.01 tidak terpengaruh** — tetap bisa dicetak kapan saja.

---

## 4. Perubahan berkas

| Berkas | Isi |
|---|---|
| `includes/class-integration.php` | `get_apl02_kelengkapan( $pendaftaran_id )` — hitung total/terjawab/K/BK/kosong terhadap master |
| `public/class-form-handler.php` | `elemen_belum_dijawab()`; validasi sebelum insert; respons memuat `apl02_status` |
| `admin/class-pendaftaran.php` | Gerbang penerimaan + simpan `konfirmasi_bk` |
| `includes/class-document-printer.php` | `enforce_apl02_completeness()` + pemanggilan pada cetak per peserta |
| `admin/views/pendaftaran-detail.php` | Kotak status APL.02, centang konfirmasi BK, penanganan galat 409 |
| `includes/class-activator.php` | Kolom `konfirmasi_bk` (tabel baru & `upgrade_columns()`) |
| `lsp-certification-management.php` | `LSP_CERT_DB_VERSION` → **1.1.0** (migrasi menambah kolom) |
| `tests/ph11-apl02-completeness.php` | 30 assertion |
| `tests/stg01-browser-acceptance.php` | Mendaftar dengan APL.02 lengkap agar alur tetap teruji |
| `tests/fixtures/isi-apl02-peserta-uji.php` | Pengisi APL.02 untuk peserta uji STG-01 |

---

## 5. Hasil gate

| Kelompok | Uji | Hasil |
|---|---|---|
| A | Pendaftaran tanpa APL.02 → tersimpan, ditandai `kosong`, 0/21 terjawab | PASS |
| B | Admin menerima APL.02 kosong → **409**, status tetap `pending` | PASS |
| C | Cetak APL.02 & paket lengkap → **409** "FR.APL.02 Belum Lengkap"; APL.01 tetap 200 | PASS |
| D | Kiriman 3 dari 21 elemen → **422**, tidak ada pendaftaran yatim | PASS |
| E | Kiriman 21/21 K → tersimpan 21 baris, dinyatakan lengkap | PASS |
| F | Penerimaan saat lengkap tanpa BK → 200, status `disetujui` | PASS |
| G | 2 elemen BK → ditolak tanpa konfirmasi; diterima setelah konfirmasi, waktu tercatat | PASS |
| H | Cetak APL.02 setelah lengkap → 200, 60,0 KB, tanpa pesan penolakan | PASS |

Regresi: seluruh suite PH-01…PH-11 + STG-01 dijalankan ulang (lihat laporan ringkas).

---

## 6. Catatan operasional

- **Migrasi otomatis**: kolom `konfirmasi_bk` ditambahkan saat request pertama setelah update
  (`LSP_CERT_DB_VERSION` 1.0.0 → 1.1.0). Tidak perlu aktivasi ulang.
- **Jejak konfirmasi BK** hanya mencatat waktu. Bila kelak perlu bukti tertulis, tambahkan kolom
  catatan konfirmasi pada fase berikutnya.
- Peserta uji #88 sudah diisi 21 jawaban K agar STG-02 bisa mencetak dokumen lengkap.

---

## 7. Langkah berikutnya

Ulangi **STG-02** pada data uji: dokumen APL.02 kini tercetak dengan kolom K terisi dan tidak
mungkin lagi kosong.
