# Program V2 — laporan lokal 2026-10-07

Laporan migrasi awal. Koreksi visual terbaru dan penggantian once-only reveal:
[program-feedback-status.md](program-feedback-status.md).

Program sudah masuk setelah About pada branch `feat/home-v2-about`.
HEAD lokal `08039bbad2f59ba37cac996bc15730fbee7d52e9`.
Main yang diperiksa `a44d484f4cd8bc532514f0152e4423a2fcdb9781`.
Tidak ada commit, push, merge, perubahan dependency, atau implementasi Values/garis.
Blueprint: [MAP-V2-07](../blueprints/homepage-v2-program.md).

## Yang dipertahankan

Delapan pilihan dan urutan sumber: TQ, KH, LC, SJ, TS, IT, FD, SC.
Copy ID/EN/AR, media, titik potong foto, eyebrow, judul, ringkasan, dan deskripsi
tetap dari sumber lama. Tidak ada perubahan copy marketing.
Center Split tetap masuk dari bawah/atas dengan tepi awal yang sama.
Sumber judul Arab memang satu kata/satu baris; tidak dibelah menjadi copy baru.
Kartu tetap media-led dengan dua baris tangga desktop dan klik menuju detail.

Detail mempertahankan huruf latar besar berputar/bergerak, kartu keluar,
copy dan foto masuk, lalu gerak kembali saat ditutup.
Rujukan gerak: [Codrops KineticTypePageTransition](https://github.com/codrops/KineticTypePageTransition).
Notice MIT ikut tersimpan pada adapter lazy dan hasil build, tanpa mengubah UI.
Desktop memakai awal sekitar 20svh, region minimal 80svh, kolom copy 30%,
media fleksibel, judul dominan yang berlapis dengan media, dan Back di atas judul.
Ukuran judul mengikuti panjang teks, bukan nama Program atau bahasa tertentu.
Sudut kartu diperkecil agar selaras dengan About V2.

## Sambungan dan posisi

`program-seam.css` memudarkan geometry_33 pada 12svh terakhir About melalui mask.
Bidang mint Mission beralih ke putih melalui zona Program 18svh dalam alur biasa.
Tidak ada pengendali scroll tambahan. Fallback mask tersedia secara berbasis fitur.
File About, media, modal, observer, dan tipografinya tidak diubah.

Posisi akhir dimiliki CSS grid, gutter V2, ukuran isi, rasio, dan `clamp()`.
XS: dua kolom kompak. SM: dua kolom dengan stagger kecil.
MD: empat kolom tangga. LG/XL/2XL: empat kolom, dua baris tangga yang membesar
secara proporsional. Detail satu kolom di bawah LG dan dapat discroll pada tinggi
pendek/teks besar. RTL mengikuti arah grid; tidak ada koordinat kartu di JS.
Rasio media dan batas kontrol adalah aturan komposisi, bukan posisi per perangkat.

IO mengamati kotak layout tetap; CSS menggerakkan isi kartu. Urutan semantik
dibuka bertahap, dengan jeda kecil per kelompok. Kartu yang sudah terlihat tetap
terlihat ketika scroll balik. Reduced motion/missing IO langsung menampilkan isi.
Native scroll anchoring dinonaktifkan hanya pada Program setelah bukti bahwa
transform detail menyebabkan scroll bergeser 25px; tidak memakai `scrollTo`.

## Modul dan detail

Presenter membentuk data; Blade memiliki satu DOM; CSS memiliki ukuran/posisi.
Policy state murni terpisah dari adapter dialog dan adapter gerak.
Composition root memasang port lazy animation. Ini batas modular yang digunakan.
GSAP 3.7.1 dari otoritas CDN legacy dimuat saat intent, khusus detail.
Tidak dimuat untuk seam, heading, atau formation; tidak menambah npm package.
Kegagalan library/import menjadi detail statis yang dapat dipakai.
Hanya media detail pilihan yang diaktifkan; tidak ada preload detail tambahan.

Native dialog melindungi interaksi latar, menahan fokus, mendukung Escape/Back,
dan mengembalikan fokus ke kartu tanpa memindahkan scroll dokumen.
Escape bisa membatalkan persiapan/gerak masuk. Token membuang hasil async lama.
Pagehide/hidden/dispose membatalkan detail dan melepas scroll lock.
Perubahan reduced motion saat detail terbuka langsung menghasilkan keadaan statis.
Tanpa JS/native dialog, delapan disclosure HTML tetap membuka deskripsi.

## Mesin lama yang dilepas

Tidak memakai sticky owner Vision/Mission→Program, pemindahan DOM ke track lama,
11 langkah stripe, shared frame sampler, smoothed scroll geometry, scroll snapping,
wheel hijack, forced scroll, atau penggabungan scroll Program/Values.
Tidak ada impor runtime `resources_old`, canvas/WebGL, renderer garis, SVG path,
Values controller, atau geometri Values yang menentukan Program.

Empat marker tetap mengikuti alur layout:
`data-program-story-start`, `data-program-story-end`, `data-program-values-seam`,
`data-values-entry-anchor`. Pengukuran sebelum/sesudah detail memastikan marker
tidak bergeser. Garis masa depan mengikuti marker, bukan memiliki layout kartu.

## Bukti

Hasil akhir command/browser dirangkum di `program-gates.json`.
PHP fokus Program/About/Landing: 23 tes, 285 assertion PASS.
PHP seluruh repo: 328 tes, 182 PASS, 71 FAIL, 75 error.
Baseline sebelum Program: 319 tes, 173 PASS, 71 FAIL, 75 error.
Nama failure/error tetap sama; tidak ada kegagalan baru.
[Perbandingan PHP](program-full-php.json), [baseline](program-baseline-php.json).
Tes Program legacy tetap dijalankan dan tidak dilemahkan: 1 FAIL/10 error karena
path frontend lama dan kontrak yang sudah ditinggalkan. [Bukti](program-legacy-tests.json).

Chromium153, Edge154, Firefox155, WebKit26.6 diuji pada Linux.
19 lebar × 3 bahasa × 4 mesin = 228 kasus layout, termasuk semua batas tier.
8 detail × 5 lebar × 3 bahasa × 4 mesin = 480 detail reduced motion.
36 pembukaan/penutupan kinetik normal, Back/Escape/fokus/inert/scroll/RTL.
Empat uji Center Split/formation, lima fallback, dan lima akses/touch.
36 kasus teks 200%/tinggi480 diuji khusus Program.
WebKit adalah mesin uji; bukan bukti Safari pada perangkat Apple.
Library/media failure, async terlambat, perubahan preferensi, resize saat detail,
locale reload saat terbuka, dan synthetic persisted page lifecycle juga diuji.
Synthetic lifecycle tidak menggantikan sertifikasi BFCache perangkat nyata.
Foto gagal dimuat tetap memiliki ruang, teks, Back/Escape, dan fokus yang pulih.
[Bukti foto gagal](program-media-failure.json),
[build notice dan open/close terakhir](program-build-attribution.json).

Browser dependencies tersedia sementara di `/tmp`/cache tooling, bukan repo.
Ekstensi PHP SQLite/GD diaktifkan lewat konfigurasi CLI sementara untuk tes.
WebKit memerlukan library Linux/media yang dilengkapi di `/tmp`; tidak mengubah
source atau pengaturan aplikasi agar lolos.
[Hash perlindungan](program-protected-source.json): 471 dari 476 source awal
identik; lima yang berubah adalah entry V2 dan adapter Program yang diizinkan.
Semua `resources_old` dan `lang` dalam baseline tetap identik.

Enam sampel lab lokal: tiga cold context ponsel dan tiga desktop.
[Bukti pengukuran](program-performance.json). Tidak ada GSAP saat scroll biasa.
Profil headless/local tanpa throttle; tidak membuktikan skor Lighthouse/PSI/CWV.
Median/worst waktu tugas selama window scroll: ponsel108.7/118.1ms,
desktop104.4/130.0ms; tidak ada tugas tunggal ≥50ms pada window Program itu.
CLS cold seluruh halaman sekitar .55 pada ponsel berasal dari `hero__content`/
`hero__cta`, juga terlihat ketika Program dilepas sementara dalam tes browser.
[Bukti sumber CLS](program-cls-source.json). Ini bukan baseline build historis,
dan tidak menjadi izin untuk mengubah Hero atau mengklaim CLS halaman sudah baik.

## Screenshot dan pembandingan

- [Mission→Program](program-mission-entry.png)
- [Center Split](program-heading.png)
- [Seluruh tangga desktop](program-desktop-field.png)
- [Huruf kinetik bergerak](program-kinetic-open.png)
- [Detail terbuka](program-detail-open.png)
- [Tablet768](program-en-768.png)
- [Ponsel390](program-en-390.png)
- [Arab desktop1440](program-ar-1440.png)
- [Referensi tangga lama](program-legacy-desktop.png)
- [Referensi heading lama](program-legacy-heading.png)

Screenshot section mengambil kotak Program; overlay header/cursor dapat muncul
sesuai viewport. Layout runtime tidak diubah. Seam/detail memakai tampilan asli.
Referensi lama dirender terpisah dari Blade/CSS arsip tanpa controller scroll,
untuk membandingkan identitas, bukan membuktikan runtime lama atau pixel parity.
Urutan media, tangga, Center Split, dan tumpang tindih judul/media dipertahankan;
margin, radius, dan keterbacaan mengikuti V2. Foto tidak diolah ulang.

## File tepat yang disentuh untuk tugas ini

Source baru:

- `app/View/Presenters/LandingProgramPresenter.php`
- `resources/views/landing/program.blade.php`
- `resources/css/sections/program.css`
- `resources/css/sections/program-field.css`
- `resources/css/sections/program-seam.css`
- `resources/css/sections/program-reveal.css`
- `resources/css/sections/program-detail.css`
- `resources/js/sections/program.js`
- `resources/js/sections/program-state.js`
- `resources/js/sections/program-reveal.js`
- `resources/js/sections/program-dialog.js`
- `resources/js/sections/program-gsap.js`
- `resources/js/sections/program-kinetic.js`
- `resources/js/sections/program-type.js`

Integrasi yang diubah: `app/Http/Controllers/HomeController.php`,
`resources/views/landing/index.blade.php`, `resources/css/index.css`,
`resources/js/index.js`, `resources/css/locale/ar.css`,
`resources/css/direction/rtl.css`, `tests/Feature/LandingV2Test.php`.
Perubahan About/Header yang sudah kotor sebelum tugas ini tetap dipertahankan.

Tes baru: `tests/Feature/LandingProgramV2Test.php`,
`tests/Unit/ProgramBrowserSupport.mjs`, `ProgramStateRuntime.test.mjs`,
`ProgramResponsiveBrowser.test.mjs`, `ProgramKineticBrowser.test.mjs`,
`ProgramRevealBrowser.test.mjs`, `ProgramFallbackBrowser.test.mjs`,
`ProgramAccessBrowser.test.mjs` (semua nama pendek terakhir di `tests/Unit/`).

Dokumen: blueprint Program ini, laporan ini, `docs2/README.md`,
`docs2/protocols/working-contract.md`, `docs/architecture/UI_UX_CURRENT_STATE.md`.
Artefak bukti baru semuanya memakai prefix `docs2/proof/program-`;
daftar lengkap terdapat pada `program-gates.json`.

## Gap asli dan status

PASS untuk perilaku Program pada profil yang disebutkan.
FAIL untuk suite PHP seluruh repo karena hutang test/path legacy yang sudah ada.
BLOCKED_BY_MISSING_EVIDENCE untuk sertifikasi rilis penuh: Safari/perangkat fisik,
screen reader nyata, zoom browser nyata, BFCache nyata, dan skor PSI/field CWV.
Pada 200% teks, label About Indonesia di390px melebar sampai396px; hasil sama
tanpa Program. Bukti `program-access-{engine}.json`. Pemilik About tidak diubah.
Data Arab satu baris dan jumlah delapan kartu adalah fakta sumber, bukan bug copy.

NEXT VALID STEP: owner meninjau hasil Program lokal dan screenshot.
Tidak ada izin/scope untuk memperbaiki About, menerapkan Values/garis, atau publikasi.
