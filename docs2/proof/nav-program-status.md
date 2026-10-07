# Hasil koreksi judul Program dan navigasi

Perbaikan lokal pada `feat/home-v2-about`, HEAD `08039bba`.
Main diperiksa ulang: `a44d484f`. Tidak ada commit, push, merge, atau deployment.
Map: [MAP-V2-08](../blueprints/nav-program-polish.md).
Status bagian yang diubah: PASS. Gate seluruh repo: FAIL pada kegagalan lama.
Bukti perangkat fisik: BLOCKED_BY_MISSING_EVIDENCE.

## Yang berubah

Judul Program tetap Center Split. Baris pertama datang dari bawah, baris kedua
dari atas; setelah reveal900ms, baris bawah bergerak sedikit ke tengah selama
1450ms. Gerak selesai sendiri walaupun scroll berhenti. Keluar membatalkan gerak;
masuk ulang dapat memulai lagi. Posisi akhir memakai skala viewport dan batas
`clamp`, dengan ruang yang dicadangkan agar teks tetap muat. Contoh hasil:
390px bergeser9.75px;1440px46.8px. AR berlawanan arah dan kata sumber tetap utuh.
Reduced motion/no JavaScript langsung memakai posisi akhir. Konflik aturan
transisi reduced motion yang terlihat saat resize sudah diperbaiki di owner CSS.

Nav desktop mengikuti warna bidang Header. Saat transparan di Hero, label utama
tetap putih termasuk ketika submenu dibuka. Saat bar putih akibat scroll/reverse,
label utama gelap. Isi panel submenu yang putih tetap gelap. Perubahan warna teks
dan bidang terjadi bersama; animasi sembunyi/tampil Header tetap dipertahankan.
Mode compact tetap memakai overlay putih yang sudah diterima.

Chevron disejajarkan dengan kotak teks sebenarnya, memperhitungkan ruang
underline label yang sudah ada. Selisih pusat kotak terukur kurang dari1px.
ID/EN memakai Inter; Header AR kini memakai Cairo melalui adapter kecil.
Tiga label yang sebelumnya literal di presenter dipindah persis ke `shared.php`.
Nama Program, pemasaran, data DB, urutan kartu dan URL media tidak diubah.

Submenu membuka/menutup720ms dari1440ms: dua kali lebih cepat. Pergantian antar
submenu tetap menutup yang lama lalu membuka pilihan baru; klik cepat dan resize
menyelesaikan state dengan benar. Foto yang sama disiapkan sebelum klik pada
lebar yang menampilkan media, dengan prioritas rendah dan decode sekali. Node,
src dan jumlah permintaan tetap saat ditutup/dibuka ulang. Caption tidak tampil
lebih dahulu saat foto masih dipersiapkan. Jika foto gagal, caption lokal muncul;
tautan tetap dapat digunakan. Pada koneksi lambat foto masih dapat menunggu.
Pemuatan baru HP di bawah768px tidak mempersiapkan gambar yang tersembunyi.

## Identitas dan batas Program yang dipertahankan

Tetap8 Program lokal, dua kolom compact/four-column staircase ketika ruang cukup,
media existing, eyebrow/judul/summary, radius ringan acuan Codrops, hover dengan
pertumbuhan kecil tanpa underline, teks latar dan reveal yang dapat berbalik.
Detail kinetik tetap lazy/on-demand; GSAP hanya untuk interaksi detail. Back
di atas judul, Escape, focus/inert dan fallback sebelumnya tetap dipertahankan.
Uji sentuh baru juga membuka detail dan mengembalikan fokus melalui Back.

About→Program tetap seam ringan yang sudah ada: geometry33 memudar dan bidang
Mission menyelesaikan warna menuju Program dalam alur dokumen. Source About,
Hero, cursor, Sound, Language, media dan semua arsip tidak berubah pada koreksi ini.
Birunya Program→Values ditunda sesuai penjelasan owner. Values dan garis belum
dibangun. Anchor `data-program-story-start`, `data-program-story-end`,
`data-program-values-seam`, `data-values-entry-anchor` tetap tersedia dan stabil.
Tidak ada scroll owner bersama Values, pemindahan DOM ke track About, scroll
snap/wheel hijack, forced scrollTo, atau runtime import `resources_old`.

## Uji dan bukti

Ringkasan mesin/command: [nav-program-gates.json](nav-program-gates.json).
Diff, struktur277 source dengan batas200 baris, build Vite dan Pint PASS.
PHP fokus27 tes/369 assertion PASS; kontrak label Gallery1 tes/11 assertion PASS.
Node unit16 PASS. Browser20 tes PASS, tanpa pageerror pada kasus yang diperiksa.

Chromium153, Edge154, Firefox155, WebKit26.6 Linux:36 kasus heading normal,
180 kasus reduced motion,216 kasus batas Header dan36 kasus tone/media.
Tambahan18 no-JS heading,3 reentry,6 profil HP sentuh dengan EN/ID/AR, detail,
orientasi/reduced, media lambat/gagal dan6 overview tablet/desktop. Batas global
360/640/768/1024/1280/1536 dan kedua sisi breakpoint diuji. Navigasi existing
tetap compact sampai1180 pada portrait; pengecualian landscape sejak1024 yang
sudah diterima tetap ada. Tidak dibuat cabang implementasi per perangkat/browser.

Profil HP:393×873,DPR2.75,touch/mobile; Chromium dengan UA Android/Redmi12
perkiraan dan WebKit dengan UA iPhone. Ini emulasi browser, bukan perangkat fisik.
Tes menguji sentuhan asli pada browser dan font16px tanpa pembesaran teks tak
terduga. Cache browser pernah menghasilkan event permintaan untuk gambar yang
sudah dimuat saat landscape. Bukti payload HP dingin memakai context baru untuk
setiap bahasa; reuse saat buka/tutup dibuktikan terpisah pada node yang sama.

Suite PHP penuh:332 tes,186 PASS,71 FAIL,75 error,1644 assertion.
[Nama kegagalan](nav-program-full-php.json) sama dengan baseline sebelumnya;
tidak ada kegagalan/error baru. Satu kontrak lama yang mewajibkan literal label
di PHP disesuaikan ke sumber bahasa; label persis ID/EN/AR, query/random3/route
tetap diperiksa dan hasil V2 juga diperiksa. Tes Program legacy tidak dilonggarkan.
[Hash perlindungan source](nav-program-protected-source.json):13 perubahan lama
diizinkan dari762 source yang sudah ada;749 source tidak berubah.

## Screenshot

- [Heading EN setelah masuk](nav-program-heading-en.png)
- [Heading AR/RTL](nav-program-heading-ar.png)
- [Program desktop lengkap](nav-program-field-en-1440.png)
- [Program tablet](nav-program-field-en-768.png)
- [Program AR desktop](nav-program-field-ar-1440.png)
- [Nav Hero dan submenu EN](nav-program-hero-open-en.png)
- [Nav Hero dan submenu AR](nav-program-hero-open-ar.png)
- [HP Chromium EN](nav-program-chromium-phone-en.png)
- [HP Chromium ID](nav-program-chromium-phone-id.png)
- [HP WebKit AR](nav-program-webkit-phone-ar.png)
- [Program HP Chromium](nav-program-chromium-program-en.png)
- [Detail HP AR](nav-program-chromium-detail-ar.png)

Overview seluruh kartu memakai reduced motion agar semua kartu dapat dinilai
sekaligus; heading/reentry/detail normal dibuktikan terpisah. Snapshot desktop
bukan bukti setiap frame layar atau pixel parity terhadap legacy.

## File pada koreksi ini

Perubahan source yang sudah ada:
`resources/css/sections/program-field.css`, `program-reveal.css`, `header.css`;
`resources/css/direction/rtl.css`; `resources/css/locale/ar.css`;
`resources/js/sections/header.js`, `header-accordion.js`;
`resources/views/landing/header.blade.php`;
`app/View/Presenters/Concerns/PreparesNavbarMediaMenus.php`;
`lang/id/shared.php`, `lang/en/shared.php`, `lang/ar/shared.php`;
`tests/Feature/HomeGalleryPreviewContractTest.php`.

Source baru:
`resources/js/sections/header-media.js`;
`tests/Feature/LandingNavigationV2Test.php`;
`tests/Unit/ProgramHeadingSequenceBrowser.test.mjs`;
`tests/Unit/NavigationPolishBrowser.test.mjs`;
`tests/Unit/NavigationMobileBrowser.test.mjs`;
`tests/Unit/NavigationLifecycleBrowser.test.mjs`;
`tests/Unit/ProgramReviewBrowser.test.mjs`.

Dokumen: map di atas, `docs/architecture/UI_UX_CURRENT_STATE.md`,
`docs2/README.md`, `docs2/protocols/working-contract.md`, catatan penyelesaian GAP
pada `docs2/proof/program-feedback-status.md`, laporan ini, dan artefak
`docs2/proof/nav-program-*` / `nav-production-*`.
Daftar ini dibandingkan snapshot sebelum koreksi, bukan seluruh dirty tree;
pekerjaan About/Header/Program yang sudah ada tetap dipertahankan.

## Gap yang nyata

Redmi12 dan Safari iPhone fisik belum tersedia untuk diuji. Chromium/WebKit mobile
tidak dapat membuktikan seluruh perbedaan Android/iOS, cache produksi atau versi
browser lama. [Audit produksi read-only](nav-production-audit.json) memakai
halaman legacy yang masih aktif; CSS berhasil200 dan overflow tidak direproduksi
pada profil yang diuji. Penyebab kegagalan HP owner belum dapat dipastikan dari
data itu. Produksi dipakai untuk antisipasi V2 dan tidak diubah.
Screen reader/zoom fisik/BFCache nyata dan PSI/field CWV belum disertifikasi.
Suite repo tetap FAIL pada daftar kegagalan lama. Tidak ada klaim skor100.
Tidak ada langkah kode berikutnya dalam scope ini; hasil lokal siap ditinjau.
