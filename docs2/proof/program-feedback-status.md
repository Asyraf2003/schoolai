# Koreksi visual Program — 2026-10-07

Scope hanya bagian yang ditegur owner: teks latar, warna, sudut foto,
hover, posisi heading, serta muncul/hilang. Branch `feat/home-v2-about`;
HEAD08039bba, main yang baru diperiksa a44d484f. Tidak ada push/merge.

## Temuan dan perbaikan

Versi pertama terlalu menyederhanakan tampilan. Tes fungsi sebelumnya tidak
membuktikan seluruh detail visual yang sekarang ditegur owner.

| Bagian | Penyebab yang diperiksa | Perubahan |
| --- | --- | --- |
| Teks latar | Seluruh type berada dalam dialog tertutup | Satu type viewport mempunyai rumah pada bidang Program; dipindahkan ke dialog saat detail, lalu kembali |
| Warna detail | Opacity bidang biru langsung0/1 tanpa transition | Putih↔biru muda memudar700ms saat buka/tutup; reduced motion langsung |
| Sudut foto | Kartu3.2px/detail6.4px pada desktop, terlalu kecil | Radius fluid kartu8–12px; detail12–17px di dua sudut atas, mengikuti acuan |
| Hover | CSS memberi underline, tidak ada pertumbuhan media/copy | Underline dihapus; foto scale1.015 dan copy1.025, transisi350ms, kembali saat pointer keluar |
| Heading | Area heading sama lebar dengan field kartu | Area heading90% mengikuti source lama; sedikit lebih masuk, tepi awal baris sama, RTL logis |
| Muncul/hilang | IO dihentikan setelah pertama terlihat | IO bisa menyembunyikan ulang kartu di bawah batas entry saat scroll balik, lalu mereveal lagi |

Acuan utama: [Codrops base CSS](https://github.com/codrops/KineticTypePageTransition/blob/main/src/css/base.css)
memakai radius12px pada kartu dan17px/17px/0/0 pada detail.
Source lokal `resources_old/css/surfaces/home/section-display-heading.css`
memakai heading90vw di desktop. Arsip hanya dibaca; tidak diimpor ke runtime.
Hover mengikuti permintaan baru owner, menggunakan CSS dan skala, bukan GSAP.
Outline keyboard tetap ada. Posisi akhir kartu tetap milik grid.

Teks latar memakai kata lokal existing dan satu DOM, bukan clone dua scene.
Layer melekat pada viewport selama berada di Program, dibatasi Program,
dan memudar pada18svh entry/8svh akhir. Tidak meluas ke About/Values.
Reduced motion tetap mempunyai latar statis dan semua isi kartu terlihat.
GSAP tetap hanya detail, lazy saat intent; kartu tidak memiliki RAF/scroll-frame.

## Kasus scroll cepat yang dibuktikan

IO bisa melewatkan perubahan status ketika kartu melompat dari atas layar ke
bawah layar dan kedua posisi sama-sama tidak beririsan. Terjadi pada pengujian
balik menuju Hero; beberapa status kartu tertinggal meskipun scroll sudah0.
Pemeriksaan satu kali pada `scrollend` menyelaraskan status terlihat, juga ketika
resize/detail selesai. Delapan pembacaan kotak, tanpa scroll listener kontinu,
loop per-frame, atau menulis posisi akhir. Keempat mesin mendukung `scrollend`.
Fokus pada kartu tetap memunculkan isinya walaupun status reveal tersembunyi.

## Bukti dan batasnya

Ringkasan command dan artefak: [program-feedback-gates.json](program-feedback-gates.json).
Diff/struktur/build/Pint PASS. PHP fokus23/291 PASS; Node16 PASS.
Repo PHP328/182 PASS/71 FAIL/75 error,1565 assertion; nama gagal tetap sama.
Uji baru memperlihatkan opacity warna di antara0 dan1 pada buka dan tutup.
Uji reveal membuktikan reverse→hilang→masuk ulang; tidak lagi menguji once-only
yang telah diganti oleh permintaan owner. Assertion lama tidak sekadar dilonggarkan.

Empat mesin: Chromium153, Edge154, Firefox155, WebKit26.6 pada Linux.
228 kasus layout dan480 detail reduced motion;36 kasus teks besar/tinggi pendek;
36 detail normal,36 pemeriksaan visual; reveal semua locale/mesin; lima fallback.
Back/Escape, fokus/inert, scroll, cancel, preferensi, resize dan lifecycle diuji.
WebKit bukan sertifikasi Safari perangkat Apple. Screen reader/zoom/BFCache nyata
dan PSI/field CWV tetap belum dibuktikan. Kegagalan repo legacy tetap terbuka.

Enam sampel scroll lokal dengan layer latar baru tidak menunjukkan long task
pada jendela yang diukur. Profil/durasi tercatat di
[program-feedback-performance.json](program-feedback-performance.json).
Profilnya berbeda dari baseline sebelumnya; tidak dipakai untuk klaim peningkatan
performance atau skor100. About/Hero/header/cursor/locale/media/arsip terlindungi;
[hash sebelum/sesudah](program-feedback-protected-source.json).

## Screenshot

- [Sebelum koreksi](program-feedback-before.png)
- [Heading dan teks latar EN](program-feedback-heading-en.png)
- [Heading dan teks latar AR](program-feedback-heading-ar.png)
- [Field desktop normal](program-feedback-field.png)
- [Seluruh kartu desktop, reduced motion](program-feedback-desktop-complete.png)
- [Ponsel](program-feedback-phone.png)
- [Arab](program-feedback-ar.png)
- [Hover](program-feedback-hover.png)
- [Detail dan radius atas](program-feedback-detail.png)

Screenshot memakai halaman/runtime asli. Foto/icon/header bisa tampil menurut
keadaan media dan pointer. Screenshot seluruh bidang tidak membuktikan frame
viewport latar sticky di seluruh scroll; screenshot viewport dan pemeriksaan
kotak layer pada tiga lebar/bahasa membuktikan perilaku tersebut.

## File koreksi ini

- `resources/views/landing/program.blade.php`
- `resources/css/sections/program-field.css`
- `resources/css/sections/program-detail.css`
- `resources/css/sections/program-reveal.css`
- `resources/css/direction/rtl.css`
- `resources/js/sections/program-dialog.js`
- `resources/js/sections/program-reveal.js`
- `tests/Feature/LandingProgramV2Test.php`
- `tests/Unit/ProgramRevealBrowser.test.mjs`
- `tests/Unit/ProgramKineticBrowser.test.mjs`
- baru: `tests/Unit/ProgramVisualBrowser.test.mjs`
- dokumen map/current-state/entrypoint/laporan dan artefak `program-feedback-*`

## Gap keputusan owner — diselesaikan pada MAP-V2-08

Owner kemudian menjelaskan: baris bawah bergerak masuk setelah reveal, secara
mandiri; biru saat menuju Values ditunda bersama Values. Keduanya sudah menjadi
keputusan eksplisit, bukan GAP lagi. Lihat [hasil terbaru](nav-program-status.md).
Catatan di bawah merekam pertanyaan pada tahap sebelumnya.

Pertanyaan teks sudah dikirim: warna yang dimaksud pada Mission→Program,
buka/tutup detail, atau bidang Program ketika scroll? Juga apakah heading yang
dimaksud posisi akhir lebih masuk atau gerak horizontal saat reveal?
Saat ini koreksi menggunakan warna detail existing dan inset source heading,
tanpa mengasumsikan compositor Program→Values atau choreography baru.
PASS untuk perilaku koreksi yang sudah diuji; BLOCKED_BY_MISSING_EVIDENCE untuk
makna tambahan warna/gerak yang belum dijelaskan; suite repo tetap FAIL.
NEXT channel owner/local terminal: jelaskan tahap warna/gerak bila berbeda dari
hasil yang sekarang terlihat. Values/garis tetap tidak dikerjakan.
