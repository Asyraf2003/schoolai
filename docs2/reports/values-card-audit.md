# Audit kartu Values — 8 Oktober 2026

FACT dari main2663b32e. Audit source, bukan sertifikasi runtime kartu V2.
Owner meminta laporan sebelum pemetaan HP/tablet/PC, ID/EN/AR dan browser.

## FACT — status aktif

`resources/views/landing/values.blade.php` hanya merender heading dan SVG.
`LandingValuesPresenter` hanya memasok heading/heading_lines; belum memasok items.
Runway garis aktif500svh. Kartu belum diimplementasikan di V2.

Source lama: `resources_old/views/home/sections/school-values.blade.php`,
`resources_old/css/surfaces/home/values/`,
`resources_old/js/surfaces/home/values/`.
Datanya ada di `lang/{id,en,ar}/home.php` → `nilai_sekolah.items`.
`BuildsHomePage` memasok schoolValues; `HomeValuesComposer` menambah nomor01–04.

## FACT — isi empat kartu

| ID | EN | AR | Aksen |
| --- | --- | --- | --- |
| Qur'ani | Qur'anic | قرآنية | Hijau #22c55e |
| Inovatif | Innovative | ابتكارية | Oranye #f97316 |
| Integratif | Integrative | تكاملية | Biru #0ea5e9 |
| Inspiratif | Inspirational | ملهمة | Ungu #a855f7 |

Front: nomor, summary, judul h3, body dengan penekanan strong dari text_parts.
Back: biru, pola geometri berulang, bingkai, merek AL MUSTAQBAL; aria-hidden.
Kartu berupa article, bukan tombol/modal. Flip digerakkan scroll, bukan klik.
Source kartu ini tidak memasang foto, video, ilustrasi kartun, atau model3D.
Satu media dekoratif dipakai di belakang: config media.static.ornaments.geometry_32
→ site/ornaments/gallery-ornament-32-v3.webp. Ketersediaan live belum diuji.

Body terpanjang adalah kartu Qur'ani: ID148, EN169, AR149 karakter Unicode
(mb_strlen, termasuk spasi/harakat). Summary EN Integrative68karakter paling panjang.
Jumlah karakter bukan ukuran lebar teks; wrap/leading harus diukur pada font nyata.

## FACT — layout dan motion source lama

| Lebar | Kolom | Perilaku lama |
| --- | --- | --- |
| 360–639 | 1 | Alur halaman; flip180°→0° saat kartu masuk |
| 640–767 | 1 | Alur halaman; flip180°→0° |
| 768–1023 | 2 | Grid2×2; flip mengikuti keterlihatan kartu |
| 1024–1279 | 2 | Grid2×2; belum memakai deck desktop |
| 1280–1535 | 4 | Stage sticky: tumpuk→melebar/fan→flip→tegak→keluar ke atas |
| ≥1536 | 4 | Stage sticky yang sama, skala ruang lebih besar |

Artinya mode desktop lama dimulai1280px; layar1024px masih mode grid2×2.
Rasio kartu lama .717 (lebar/tinggi), front padding/type berbasis lebar kartu/cqi.
Card face overflow:hidden. Body yang terlalu panjang berpotensi terpotong;
tinggi/rasio baru harus dibuktikan dengan tiga bahasa dan zoom, bukan disalin tetap.
Grid lama direction:ltr; front/back mengikuti RTL, sehingga urutan fisik tidak
otomatis dibalik. Chronology RTL baru belum diputuskan.

Motion responsive: flip halus tanpa overshoot, tilt dibatasi≤20° dan gap.
Motion desktop: fan[-13,-4.5,4.5,13]°, flip stagger dengan overshoot−18°,
lalu keluar ke atas. Controller memakai smoothing desktop, progress langsung
pada mode kecil; ada CSS float3s berulang pada setiap kartu.
Blueprint historis pernah meminta tiga bounce saja; source sekarang memakai
float berulang. Ini konflik histori/source, bukan izin mengaktifkan salah satunya.

Timeline lama670svh mencakup100svh tail untuk handoff Gallery. Gallery V2 belum
aktif; nilai670svh dan transisi lama tidak boleh dibawa otomatis ke runway500svh.
Controller lama mensyaratkan tepat4kartu dan node stage/perspective/grid/spatial,
serta hook Program/Values dunia lama. DOM V2 berbeda; controller tidak siap diimpor.
Spatial Three.js ada di source, tetapi VALUES_SPATIAL_ENABLED=false; flip kartu
sendiri adalah CSS3D pada DOM, bukan WebGL. Tidak ada kebutuhan engine baru yang terbukti.

## GAP sebelum pemetaan baru

1. Posisi/durasi tiap kartu relatif terhadap lima komposisi garis putih belum dipetakan.
2. Deck/fan/flip/float lama belum dipilih sebagai storyboard V2.
3. Ukuran front dengan semua teks, landscape dan zoom200% belum diukur di V2.
4. Urutan fisik kartu untuk AR serta arah flip/entry belum diputuskan.
5. Runtime kartu V2 pada browser/perangkat belum ada karena kartu belum diimplementasikan.

Tidak ada data tambahan yang dibutuhkan untuk laporan source ini. Keputusan
storyboard diperlukan sebelum implementasi kartu; preview lama bukan bukti UI baru.

## DECISION — usulan untuk map berikutnya, belum implementasi

Gunakan satu semantic DOM dan data items melalui presenter V2; ID/EN memakai
Inter/LTR, AR Cairo/RTL dengan leading dan wrap yang diukur. Perlakukan pola back
sebagai dekorasi. Konten front tetap terbaca tanpa JS, reduced motion atau gagal media.
Map kandidat1kolom/2×2/4kolom pada enam tier di atas untuk dibandingkan di preview.
Utamakan keterbacaan pada viewport pendek; jangan memaksakan card tinggi terpotong
demi stage sticky. Gunakan satu pemilik geometry/progress kartu dan garis;
hindari menambah runway670svh atau scroll controller lama secara otomatis.

## COMMAND PLAN / PROOF untuk tahap pemetaan nanti

Satu langkah berikutnya: susun storyboard posisi empat kartu terhadap lima layar
garis yang sudah diterima, lalu minta review owner atas map konkret tersebut.

Matriks verifikasi yang perlu dicatat pada map itu:

| Sumbu | Kasus yang akan dibuktikan |
| --- | --- |
| Lebar | 360/640/768/1024/1280/1536; tambahan390 dan1440, tepat batas±1px |
| Tinggi/input | Portrait/landscape pendek, touch dan mouse/keyboard |
| Bahasa | ID/EN/AR; switching locale; font siap; semua front tidak terpotong |
| Chromium | Chrome desktop/Android dan Edge desktop: clipping3D, resize, scroll |
| Gecko | Firefox: backface/perspective, flip, teks dan overflow |
| WebKit | Safari macOS/iOS: sticky, dynamic viewport, backface dan restore |
| State | Down/up, scroll cepat, resize di tengah flip, hidden/BFCache, disposal |
| Fallback | NoJS, reduced motion, feature CSS/IO tidak tersedia, asset gagal |
| Akses | Urutan heading/reading, dekorasi tersembunyi, kontras, zoom200% |
| Performa | Ukur frame/render cost dan pause offscreen pada profil yang disebut |

Playwright WebKit bukan bukti Safari perangkat asli; Chrome/Edge berbagi engine
tetapi hasil product/device tidak boleh diklaim tanpa dijalankan.
Matriks ini adalah rencana proof, belum PASS kartu V2.
