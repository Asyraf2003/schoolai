# Prompt untuk Claude — Lanjutan SchoolAI

Lanjutkan pekerjaan SchoolAI dari kondisi lokal berikut. Bagian **Tambahan
dan koreksi saya** di bawah menentukan scope pekerjaan. Kalau bertentangan
dengan konteks lama, ikuti koreksi saya yang terbaru.

## Kondisi terakhir dari Codex

- Checkout kerja ada di `/home/asyraf/projects/mustaqbal/school`. Ini folder
  yang dipakai `make on` untuk menjalankan `http://127.0.0.1:8001/`.
- Saat handoff, HEAD lokal `8ca97044` dan `origin/main` `dafca25d`
  (PR #112). Perubahan Galeri dan Testimoni terbaru masih lokal, termasuk
  file baru yang belum dilacak Git. Cek status, diff, dan SHA sebelum kerja.
- Galeri sekarang memakai tampilan biru → hitam dan gerak asli. Sepuluh aset
  lama tampil masing-masing tiga kali. Media utama mengecil dengan titik
  tengah tetap. Lebar tiga kolom **3:4:3**, dengan jumlah 9/12/9.
- Testimoni terbaru ditemukan sebagai perubahan lokal di
  `/tmp/schoolai-testimonials-work` dan sudah ada di checkout utama. Desktop
  punya dua track yang bergerak berlawanan, HP punya enam kartu bergantian,
  dan WebKit sudah mendapat perbaikan tap. Urutan homepage saat ini Galeri
  → Cerita → Testimoni.
- Build, struktur, 47 tes Node, 278 tes PHP, dan tes terfokus Chromium/WebKit
  lulus pada preview lokal. Uji Galeri di browser mencakup lebar 390, 768,
  dan 1440 px. Safari pada perangkat Apple belum diukur.
- Worktree `school-gallery-choreography` menyimpan rancangan putih yang
  sudah digantikan. Pakai hasil live dari checkout `school` sebagai acuan.

Baca `AGENTS.md`, `CLAUDE.md`, aturan canonical, dan
`docs/architecture/handoffs/2026-11-26-gallery-testimonials-handoff.md`.
Fokus pada tambahan saya di bawah. Jaga perubahan lokal yang sudah ada.
Pakai scroll engine, Values, cursor, dan footer yang sekarang. Jalankan
tes di SQLite in-memory, pertahankan database lokal, dan cek hasil dari
`8001` setelah rebuild. Publikasi mengikuti izin saya untuk scope ini.
Laporkan perubahan, bukti, dan bagian yang masih belum terverifikasi.

## Tambahan dan koreksi saya

<!-- Isi atau hapus kolom yang tidak diperlukan sebelum dikirim. -->

**Yang mau saya ubah**

[Tulis perubahan atau koreksi di sini.]

**Hasil yang saya inginkan**

[Tulis tampilan atau perilaku yang diharapkan.]

**Referensi**

[Tempel route, screenshot, video, kode, atau contoh bila ada.]

**Yang perlu dipertahankan**

[Tulis bagian yang ingin tetap seperti sekarang.]

**Cara cek hasilnya**

[Tulis kondisi, ukuran layar, browser, atau interaksi yang penting.]
