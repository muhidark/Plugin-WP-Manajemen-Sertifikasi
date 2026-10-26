# STG-02 — Manual Print Acceptance (checklist)

**Status:** **LULUS** — dinyatakan oleh pengguna pada 27 Oktober 2026
**Data uji:** kegiatan #76, peserta #88, asesor #55 (dari STG-01)

> Catatan kejujuran: penilaian dilakukan manual oleh pengguna di browser. Lembar checklist
> per-dokumen tidak diisi baris demi baris; yang dicatat di sini adalah pernyataan hasil
> ("hasil cetak sudah OK"), bukan rekonstruksi nilai per butir.

Berkas siap cetak:

- `~/Documents/STG-02 Checklist Manual Print Acceptance.docx`
- `~/Documents/STG-02 Checklist Manual Print Acceptance.pdf` (3 halaman A4 landscape)

## Isi checklist

**A. Matriks per dokumen** — 19 dokumen (FR.APL.01/02, FR.MAPA.01/02, FR.AK.01–07,
FR.IA.01–05C) dengan kolom: A4, margin, page break, tabel, checkbox, teks panjang,
unit/elemen/KUK, TTD, identitas, catatan.

**B. Fokus khusus** — 12 aspek, termasuk pagination FR.AK.05, FR.IA.01 multi-halaman,
checkbox tidak boleh ☑ untuk elemen tanpa jawaban, dan tidak boleh ada nama/MET contoh.

**C. Kesimpulan** — jumlah sesuai/tidak sesuai, keputusan (LULUS / LULUS DENGAN CATATAN /
TIDAK LULUS), kolom tanda tangan penguji.

## Cara menjalankan

1. Login admin di `http://localhost/wordpress/wp-admin/`.
2. Buka **LSP Certification → Cetak Dokumen**, pilih peserta uji (#88).
3. Untuk tiap dokumen: tekan Cetak → periksa pratinjau/cetak → isi checklist.
4. MAPA.02 dicetak dari **LSP Certification → Jadwal** (per kegiatan), bukan per peserta.
5. Setelah selesai: putuskan LULUS/TIDAK, lalu data uji (#76/#88) boleh dihapus.

## Hasil

Pengguna memeriksa hasil cetak dan menyatakan **OK** pada 27 Oktober 2026. Tidak ada temuan cetak
yang dilaporkan.

Temuan putaran sebelumnya — FR.APL.02 tercetak dengan seluruh kolom K/BK kosong — sudah diperbaiki
pada **PH-11** dan diverifikasi ulang sebelum pemeriksaan ini, sehingga yang diperiksa pengguna
adalah dokumen dengan kolom terisi.

## Tindak lanjut

1. Hapus data uji (#76/#88/#55) sebelum kegiatan nyata dimulai.
2. Lanjut **REL-01** — freeze kode, paket produksi final, checksum.
3. Lanjut **PROD-01** — deployment ke `lspkomunikasi.id` setelah backup.

## Hasil yang diharapkan bila lulus

Tidak ada teks terpotong, tidak ada halaman kosong, margin sesuai (kiri 3 cm, kanan 5,08 cm),
checkbox rapi, Elemen/KUK lengkap dan sesuai master, identitas asesi/asesor benar.
