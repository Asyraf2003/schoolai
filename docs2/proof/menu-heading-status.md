# MAP-V2-10 — Hasil tiga koreksi visual, 2026-10-07

Branch lokal `feat/home-v2-about`, HEAD `08039bbad2f59ba37cac996bc15730fbee7d52e9`.
Main baru diambil: `a44d484f4cd8bc532514f0152e4423a2fcdb9781`.
Tidak ada push/merge/commit atau perubahan dependency.

## 1. Penyebab perbedaan menu old dan V2

Old mengaktifkan `has-open-menu`, lalu memakai satu `nav-mega-surface` untuk
Header dan panel. V2 sebelumnya mengecualikan panel desktop dari kondisi bidang
terang: `scrolled || (!desktop && navigationOpen)`. Ini mengikuti keputusan lama
yang sekarang diganti oleh arahan terbaru owner.

Akibatnya, saat membuka submenu di Hero, panel putih tetapi Header tetap
transparan/gradien dan teks putih. Dalam baseline1440px, panel sudah mulai tepat
di y72px, sama dengan Header bottom. Jadi sumber masalah bukan celah1px pada
posisi, melainkan bidang yang berbeda. Strip6px pada sambungan hanya4320 dari
8640pixel putih; separuh sisanya berasal dari Header/Hero.

Sekarang panel yang sedang terbuka/menutup ikut mengaktifkan bidang terang.
Header dan panel mewarisi `--header-surface` yang sama. Semua8640pixel pada strip
sambungan putih, tanpa Hero bleed. Root Header/controller/animasi panel tetap.
Tidak ada bidang baru, layer yang dinaikkan, atau perubahan geometri.

Audit closed native-details juga dilakukan: anak absolute dapat tetap memiliki
layout rect walau details tertutup. Control browser yang menyembunyikan anak
tersebut tidak memperlihatkan perbedaan visual pada capture isolasi. Tidak ada
patch hide tambahan dibuat berdasarkan rect saja.

Ukuran V2 tetap: Header72px pada1440×900; logo38px pada1440 dan48px pada1920,
sesuai clamp yang sudah ada. Media/radius/grid panel/compact navigation tetap.
Old78px/18px radius/dimensi/dua kolom tidak disalin.

## 2. File menu yang berubah

- [header-state.js](../../resources/js/sections/header-state.js): kondisi bidang
  terang kini `scrolled || navigationOpen`, termasuk rendered closing panel.
- [header.css](../../resources/css/sections/header.css): satu token bidang untuk
  Header dan panel; posisi, ukuran, radius, dan animasi tidak diubah.

Blade, Header controller, accordion, media lifecycle dan locale tidak diubah.

## 3. Jarak judul sebelum/sesudah

Ukuran font/weight/family tetap. Pengukuran menggunakan baseline DOM dan batas
huruf dari font Canvas yang sama; bukan menganggap CSS grid gap sebagai seluruh
ruang kosong. Batas huruf juga diperiksa berada di dalam mask.

| Profil Chromium | EN sebelum→sesudah | ID sebelum→sesudah |
| --- | --- | --- |
|390px|25,00→8,31px|26,00→9,31px|
|768px|48,14→16,20px|49,14→17,20px|
|1440px|73,88→24,64px|74,88→25,64px|
|1920px|98,84→33,16px|101,84→36,16px|

Desktop1440 hasil≈33,4% EN dan34,2% ID dari jarak sebelumnya. Font tetap172,8px.
CSS leading.9→.815, gap.12em→.04em, padding mask dua baris.08em→.02em.
AR satu baris tetap leading1.35 dan padding.08em, Cairo/RTL/tanpa gerak horizontal.
Firefox membulatkan font sedikit berbeda:1440 gap25,60 EN/26,60 ID, tetap dalam
rasio yang diterima. Tidak ada penyesuaian engine atau hardcoded posisi perangkat.

## 4. Durasi sebelum/sesudah

5,8detik berasal dari SECTION HEADING, bukan detail Codrops:

- Vertikal:900ms→760ms.
- Horizontal baris bawah:ID5800ms, EN1450ms setelah jeda900ms→760ms tanpa jeda.
- Kedua transform kini berjalan dalam satu entrance760ms dengan
  `cubic-bezier(.22,1,.36,1)`. Tidak ada gerak idle beberapa detik atau loop.
- Jarak horizontal ID4×/EN2×/AR0 tetap; multiplier durasi ID dihapus.
- Reduced motion/no JS langsung terbaca. Reverse/reentry tetap lewat IO/CSS.
- Card formation900ms/450ms dan seluruh detail/GSAP700ms/open/close tetap.

## 5. Bukti

Linux lokal, Ryzen AI7 445, RAM host15GiB, Node24.20.0.
Chromium153.0.8010.52, Edge154.0.4258.62, Firefox155, WebKit26.6.
Server lokal127.0.0.1:8000, normal/reduced/no JS, ID/EN/AR.

- Empat browser:84capture state menu,48ukuran/bahasa heading PASS.
  Total1225frame opening/closing memeriksa fill/color, sambungan dan transform.
- Menu:Hero closed, hover, open, close; scrolled, scrolled-open; rapid reopen;
  Header tetap visible saat panel open dan wheel scroll. Pita6px join full-white
  pada36open captures; tidak ada independent transform bidang/panel.
- Existing Header lifecycle:216tier/locale cells, keyboard/reversal/resize,
  sampled tone dan18no-JS PASS. Both1180/1181 dan portrait/landscape dipertahankan.
- Existing menu polish:36kasus tone/alignment/720ms panel/media reuse PASS.
- Existing heading:36normal,180reduced,18no-JS,3reentry PASS.
- Total21focused browser tests PASS; tambahan baseline/menu-only2 PASS.
- Focused JS12tes PASS; PHP V2 fokus27tes/369assertion PASS.
- `git diff --check`, `npm run check:structure`277sumber/max200baris,
  `npm run build`, Pint dirty agent:PASS.
- Full PHP332tes:186PASS,71failure/75error,1644assertion. Nama kegagalan/error
  sama dengan baseline sebelumnya; gate repository tetap FAIL.

Screenshot: [menu sebelum](menu-heading-before-en-hero-open.png),
[menu sesudah](menu-heading-after-en-hero-open.png),
[close di Hero](menu-heading-after-en-hero-close.png),
[scrolled+open](menu-heading-after-en-scrolled-open.png),
[rapid reopen](menu-heading-after-en-rapid-open.png),
[sebelum reveal](menu-heading-after-id-hidden.png),
[saat reveal](menu-heading-after-id-entering.png),
[judul final](menu-heading-heading-id.png).

Data: `menu-heading-before-chromium.json`, `menu-heading-after-{engine}.json`,
`menu-heading-heading-*.json`, `menu-heading-nav-*.json`,
`menu-heading-navigation-*.json`, [full PHP](menu-heading-full-php.json),
[sumber dilindungi](menu-heading-protected-source.json).

Sumber produk lain yang berubah hanya `program-field.css`, `program-reveal.css`,
`locale/id.css`. Test yang diperbarui: HeaderSurfaceRuntime, NavigationLifecycle,
NavigationPolish, ProgramHeadingSequence. Test/helper baru: MenuHeadingVisualBrowser
dan MenuHeadingVisualSupport.772hash awal,763identik,9authorized changes,
2test/helper baru;386file legacy identik. Protected About/Hero/cards/detail/media/
content/anchors/cursor/controller tetap. Tidak ada Values/scroll line.

## 6. Gap yang nyata

PASS untuk tiga kontrak koreksi lokal yang diuji; full suite repository tetap
FAIL karena kegagalan lama di atas. Native Safari/perangkat fisik belum diuji;
WebKit lab bukan sertifikasi Safari fisik. Tidak ada klaim whole-site DOD/100/CWV.
Capture heading dalam rangkaian resize cepat memiliki patch putih pada latar
dekoratif yang juga ada di baseline; capture viewport tetap/isolasi normal bersih.
Penyebab paint ini belum dibuktikan dan tidak ditutup dengan patch selector.
Geometri/timing/glyph heading dan state/sambungan menu tetap lolos bukti di atas.
Tidak ada step implementasi aktif. Next channel owner/local terminal: review
tiga koreksi ini; audit paint latar setelah resize dapat menjadi scope terpisah.

Pemeriksaan ulang atas arahan yang dikirim kembali:4browser/84menu states/
48heading cells PASS; JS12/PHP27/build/structure/Pint/diff PASS. Seluruh774
sumber tetap identik selama pemeriksaan ulang; tidak ada pengecilan kedua atau
perubahan kode tambahan. Data: `menu-heading-confirmation.json`.
