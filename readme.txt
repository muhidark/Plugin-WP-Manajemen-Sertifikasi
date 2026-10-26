=== LSP Certification Management ===
Contributors: muhadi
Tags: lsp, sertifikasi, kompetensi, bns
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later

Plugin untuk manajemen proses sertifikasi kompetensi LSP Komunikasi Indonesia.

== Description ==

Plugin ini mengelola proses sertifikasi kompetensi dari pendaftaran hingga pencetakan dokumen:

* Manajemen kegiatan sertifikasi (jadwal, TUK, kuota)
* Form pendaftaran online untuk asesi
* Pengisian APL 01 (data pribadi & pekerjaan)
* Pengisian APL 02 (asesmen mandiri)
* Verifikasi kelengkapan oleh admin
* Penugasan asesor
* Pencetakan dokumen (tahap selanjutnya)

**Catatan:** Plugin ini memerlukan LSP Core Management untuk aktif.

== Installation ==

1. Upload folder `lsp-certification-management` ke `/wp-content/plugins/`
2. Aktifkan plugin melalui menu 'Plugins' di WordPress
3. Pastikan LSP Core Management sudah aktif
4. Database tables akan dibuat otomatis saat aktivasi

== Usage ==

**Admin:**
1. Buat kegiatan sertifikasi di menu Sertifikasi > Kegiatan
2. Set status "Dibuka" agar bisa dilihat asesi
3. Monitor pendaftaran di menu Sertifikasi > Pendaftaran
4. Verifikasi kelengkapan dokumen
5. Setujui dan pilih asesor

**Frontend:**
1. Buat halaman baru
2. Tambahkan shortcode `[lsp_sertifikasi_daftar]` untuk menampilkan daftar kegiatan
3. Atau gunakan shortcode `[lsp_sertifikasi_form kegiatan_id="X"]` untuk form langsung

== Changelog ==

= 1.1.0 =
* Keamanan: dependency Core dicek lewat kelas, bukan hanya file plugin; aktivasi ditolak bila Core tidak ada
* Keamanan: Elemen/KUK karangan dihapus — pencetakan FR.APL.02/FR.IA.01 ditahan bila master SKKNI belum lengkap
* Keamanan: identitas contoh (nama & Nomor MET) dihapus dari seluruh dokumen; field kosong tercetak kosong
* Keamanan: model capability baru (manage_activities, manage_instruments, view_docs, print_docs)
* Keamanan: pendaftaran divalidasi server-side (status kegiatan, tanggal, skema, format identitas)
* Keamanan: jawaban APL.02 dibangun ulang dari Master SKKNI (hanya K/BK dari browser)
* Keamanan: pendaftaran atomik — mutex per kegiatan + indeks unik NIK/email
* Keamanan: isolasi dokumen fail-closed; asesor hanya peserta/kegiatan yang ditugaskan
* Baru: versioning skema database (LSP_CERT_DB_VERSION) dengan migrasi idempoten
* Baru: persyaratan administratif global dari Pengaturan Core (sama untuk semua skema)

= 1.0.1 =
* Keamanan: pembatas 3 pengiriman pendaftaran per menit per alamat IP
* Perbaikan: tolak pendaftaran ganda (NIK atau email sama pada kegiatan yang sama)

= 1.0.0 =
* Initial release
* Manajemen kegiatan sertifikasi
* Form pendaftaran asesi
* APL 01 & APL 02
* Verifikasi admin
* Penugasan asesor
