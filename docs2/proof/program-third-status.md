# Koreksi nav, judul Program, dan latar AR — 2026-10-07

Perbaikan lokal selesai pada `feat/home-v2-about`, HEAD `08039bbad2f59ba37cac996bc15730fbee7d52e9`.
Main yang diambil saat audit: `a44d484f4cd8bc532514f0152e4423a2fcdb9781`.
Tidak ada commit, push, merge, perubahan dependensi, atau pekerjaan Values/garis.

## Hasil yang terlihat

- Nav mempunyai lapisan dasar tersendiri di bawah isi menu. Satu status
  `data-surface` menentukan latar dan warna teks bersama: putih→gelap,
  Hero transparan→putih. Membuka submenu desktop di Hero tetap mempertahankan
  teks putih; panel submenu sendiri tetap putih dengan teks gelap.
- Pola lapisan mengacu pada old yang sudah diperiksa. Batas scroll48/24 dan
  aturan tampil/sembunyi tetap. Tidak ditambahkan jeda warna atau controller lain.
  Bug PC tertentu belum direproduksi sebelum perubahan; bukti menunjukkan
  kontrak lapisan/warna setelah perbaikan, bukan kepastian sebab bug perangkat.
- Baris bawah judul ID bergeser4× dan durasinya4×:5,8detik. EN jarak2×,
  durasi1,45detik. AR tidak bergeser horizontal. Reveal vertikal900ms tetap;
  geser dimulai setelah reveal, memakai CSS, tanpa mengatur scroll.
- Jarak tetap berasal dari ukuran layar dan token, bukan koordinat kartu.
  Reduced motion/no JS langsung menyediakan posisi akhir yang terbaca.
- Latar AR kini menggunakan jarak baris dekoratif.75, sesuai old. Aturan1.35
  sempat ikut mengatur latar dan menyebabkan baris terlalu jarang. Judul/detail/
  isi kartu tidak dirapatkan. Cairo, RTL, copy lang/DB, dan media dipertahankan.
- Pada390/768/1440px, baris AR yang tampak berubah4/4/5→8/8/8, sama jumlah
  tampaknya dengan EN. Tetap20 baris DOM yang sama.

## Gerak dan biaya latar

Old dan V2 memakai gerak kinetik terbatas ketika detail dibuka/ditutup.
Tidak ditemukan gerak latar otomatis yang berulang saat diam. Latar V2 mengikuti
batas section lewat sticky/CSS; tidak ada RAF latar, canvas, atau GSAP saat scroll.
Detail tetap menyiapkan modul/GSAP saat ada niat membuka; fallback tetap tersedia.

Pengukuran Chromium153 lokal memakai ukuran390/768/1440, font/media siap,
reduced motion, tanpa throttle. Setiap profil membandingkan latar tampak/hidden,
masing-masing3ulang×8langkah wheel. Sebelum36percobaan, sesudah36percobaan.

| AR | Baris sebelum→sesudah | Heap JS sebelum→sesudah | Waktu kerja median sesudah / terburuk |
| --- | --- | --- | --- |
|390px|4→8|2,33→2,33MB|24,71 /26,74ms|
|768px|4→8|2,34→2,34MB|24,64 /25,77ms|
|1440px|5→8|2,35→2,34MB|24,44 /26,55ms|

Waktu di atas adalah total kerja untuk8langkah wheel, bukan biaya per frame.
Tidak terdeteksi long task>50ms dalam percobaan tersebut. Tidak ada font, media,
baris DOM, atau modul animasi tambahan. Angka heap adalah memori JavaScript
halaman, bukan total RAM browser/GPU/Android/iOS. Selisih waktu control beragam;
data ini tidak membuktikan skor perangkat atau menghapus semua biaya paint.
[Data mentah/median](program-third-performance-summary.json).

## Identitas Program dan batas pekerjaan

Delapan kartu tetap mengikuti urutan semantik dan media/content lama. Dua kolom
kompak di HP, stagger dua kolom640–767, staircase empat kolom mulai768. Margin,
jarak langkah, radius kecil, dan ukuran teks mengikuti CSS responsif. Hover
ringan tanpa underline tetap. Perbandingan dengan screenshot legacy menunjukkan
karakter staircase/media yang sama; tidak ada klaim pixel parity.

Seam Mission tetap ordinary flow18svh: geometry33 memudar pada akhir12svh About,
mint Mission menuju putih Program. Proof seam baru lolos; sumber About tidak
diubah. Detail tetap judul dominan, Back di atasnya, deskripsi, lalu media; judul
menyesuaikan panjang lokal. Satu type DOM masuk dialog lalu kembali ke section;
Back/Escape/fokus/inert dan scroll biasa setelah tutup sudah diuji.

Mesin sticky terintegrasi lama, handoff11langkah, shared frame sampler, coupling
Program/Values, scroll snap/wheel hijack/scrollTo choreography tidak dibawa.
Anchor `data-program-story-start`, `data-program-story-end`,
`data-program-values-seam`, `data-values-entry-anchor` tetap stabil dan mengikuti
layout. Tidak ada Values, perubahan putih→biru Values, atau garis/SVG renderer.

## Bukti dan gate

Linux lokal, AMD Ryzen AI7 445, RAM host15GiB, Node24.20.0;
Chromium153.0.8010.52, Edge154.0.4258.62, Firefox155.0, WebKit26.6.
Playwright yang sudah tersedia digunakan di luar dependency repository.
WebKit sempat berhenti karena plugin audio host hilang; runtime media Ubuntu
dipulihkan hanya di `/tmp`, lalu normal/reduced/mobile proof berhasil.

- `git diff --check`:PASS.
- `npm run check:structure`:PASS,277 sumber aktif, maksimum200baris.
- `npm run build`:PASS. CSS32,81kB, JS awal37,44kB; chunk detail tetap terpisah.
- `vendor/bin/pint --dirty --format agent`:PASS, tidak mengubah PHP putaran ini.
- PHP V2 fokus:27tes/369assertion PASS. PHP CLI memakai SQLite/GD sementara.
- JS murni:18tes PASS, termasuk dua kontrak pemilik bidang nav baru.
- Heading:36normal,180reduced ukuran/bahasa/engine,18no-JS,3reentry PASS.
- Nav:216kasus ukuran/bahasa/engine,36tone/media,18no-JS PASS.
 58sampel state Chromium memeriksa warna aktual label/glyph/chevron vs lapisan
 dasar. Rapid/reverse/keyboard, media dipakai ulang, media lambat/gagal PASS.
- Touch mobile:6profil bahasa Chromium/WebKit, DPR2.75 dan rotasi PASS.
 Ini emulasi Android/iPhone, bukan perangkat Redmi/Safari fisik.
- Detail AR:12normal dan4reduced/resize PASS; style terbersihkan, fokus kembali,
 anchor tetap, native scroll setelah tutup bekerja pada empat engine.
- Program200% pembesaran teks/height480:36kasus PASS untuk area Program.
 Gap About ID390 yang sudah tercatat tetap dilindungi, tidak diperbaiki di sini.
- Header1920:dropdown, language modal, Escape, highlight, Sound on/off PASS.
- Full `php artisan test --compact --no-ansi`:FAIL,332tes;186PASS,
 71failure/75error/1644assertion. Nama failure/error identik baseline putaran
 sebelumnya, tanpa tambahan. [Perbandingan](program-third-full-php.json).

## Screenshot yang diperiksa

1. [Mission→Program](program-third-mission-entry.png).
2. [Judul ID posisi akhir](program-third-heading-id.png), [EN](program-third-heading-en.png).
3. [Seluruh kartu desktop](program-third-field-id-1440.png).
4. [Detail AR desktop](program-third-detail-ar-1440.png).
5. [Tablet AR](program-third-field-ar-768.png).
6. [HP EN](program-third-field-en-390.png), [HP AR](program-third-field-ar-390.png).
7. [Desktop AR](program-third-field-ar-1440.png).
8. [Nav putih/dark labels](program-third-light-open-en.png), [Hero/white labels](program-third-hero-open-en.png).

## File yang berubah pada putaran ini

Sumber produk8file:

- `resources/css/locale/id.css`
- `resources/css/locale/en.css`
- `resources/css/locale/ar.css`
- `resources/css/sections/program-field.css`
- `resources/css/sections/program-reveal.css`
- `resources/css/sections/header.css`
- `resources/js/sections/header-state.js`
- `resources/js/sections/header.js`

Test lama yang diperbarui7file:

- `tests/Unit/ProgramHeadingSequenceBrowser.test.mjs`
- `tests/Unit/NavigationPolishBrowser.test.mjs`
- `tests/Unit/NavigationLifecycleBrowser.test.mjs`
- `tests/Unit/NavigationMobileBrowser.test.mjs`
- `tests/Unit/ProgramReviewBrowser.test.mjs`
- `tests/Unit/ProgramKineticBrowser.test.mjs`
- `tests/Unit/ProgramAccessBrowser.test.mjs`

Test baru3file:

- `tests/Unit/HeaderSurfaceRuntime.test.mjs`
- `tests/Unit/ProgramTypeBudgetBrowser.test.mjs`
- `tests/Unit/ProgramTypeLifecycleBrowser.test.mjs`

Dokumen: `docs2/blueprints/nav-type-final-polish.md`, `docs2/README.md`,
`docs2/protocols/working-contract.md`, `docs/architecture/UI_UX_CURRENT_STATE.md`,
laporan ini dan artefak `docs2/proof/program-third-*` di
[daftar bukti lengkap](program-third-artifacts.json).

Hash769 sumber awal:754tetap,15perubahan di atas,3test baru;386file legacy
tetap identik. About/Hero/cursor/Sound/Language/media/lang/presenter/DB protected.
[Rincian sumber](program-third-protected-source.json). Daftar ini hanya putaran
terakhir; [migrasi awal](program-status.md) dan [koreksi sebelumnya](nav-program-status.md)
memuat perubahan putaran terdahulu.

## Status dan gap yang nyata

PASS untuk kontrak koreksi lokal yang diuji. FAIL untuk full suite repository.
BLOCKED_BY_MISSING_EVIDENCE untuk RAM perangkat fisik, native Redmi12/Safari,
screen reader/zoom browser nyata, Lighthouse100 dan p75 field CWV.
Tidak ada step implementasi aktif tersisa pada scope ini. Next channel owner/
local terminal: review hasil lokal; Values memerlukan task/blueprint tersendiri.
