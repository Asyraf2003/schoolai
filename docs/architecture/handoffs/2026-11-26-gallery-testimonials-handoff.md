# Handoff SchoolAI untuk Claude

## Identitas

| Informasi | Nilai |
| --- | --- |
| Tanggal | 26 November 2026, WIB |
| Repo dan checkout kerja | `Asyraf2003/schoolai` di `/home/asyraf/projects/mustaqbal/school` |
| SHA `origin/main` setelah fetch | `dafca25dd91fbc915c655c99467a7a5af289f37f` |
| HEAD lokal | `8ca97044ae0f15e0cdc4f468659ffcc63511938f` pada `main`, 7 commit di belakang |
| Status publikasi | PR #112 memuat optimasi scroll dan cursor. Koreksi Galeri dan Testimoni berikutnya tersimpan sebagai perubahan lokal, termasuk file baru yang belum dilacak Git. |
| Kanal penerima | Claude di terminal lokal |
| Status konteks | Preview lokal `PASS`. Scope perubahan berikutnya diisi pemilik di prompt. |

## Kondisi yang dilanjutkan

`make on` menjalankan `mustaqbal-school-web.service` dari checkout `school`.
Saat handoff, [homepage lokal](http://127.0.0.1:8001/) merespons dengan 30 item
Galeri dan dua track Testimoni. Urutannya **Galeri → Cerita → Testimoni**.
Jangkar yang relevan adalah [Galeri](http://127.0.0.1:8001/#galeri),
[Cerita](http://127.0.0.1:8001/#artikel), dan [Testimoni](http://127.0.0.1:8001/#testimoni).

- **Galeri.** Sepuluh aset lama tampil tiga kali dalam tiga urutan tetap. Aset
  pembuka tetap pertama. Media utama mulai mengecil setelah titik tengahnya
  mencapai tengah viewport dan bertahan di sana sampai formasi. Lebar kolom
  **3:4:3** setelah gutter, dengan jumlah **9/12/9**. Pada viewport sangat
  lebar dan pendek, lebar formasi dibatasi agar media utama tetap mengecil.
  Gaya asli kembali, termasuk transisi biru ke hitam, reveal teks, dan fade.
  Poster lazy. Maksimum dua sumber video terpasang. Reduced motion dan no-JS
  menggunakan grid statis.
- **Testimoni.** Versi terakhir yang dicari pemilik berasal dari perubahan
  lokal berbasis `c2921f36` di `/tmp/schoolai-testimonials-work`. Versi itu
  sudah dibawa ke checkout `school`. Judul editorial berbunyi “Testimoni”
  untuk ID. Desktop punya dua track yang bergerak berlawanan dan dapat
  ditelusuri dengan drag atau keyboard. HP menampilkan enam kartu yang
  bergantian. Glow, avatar, copy lokal, dan perilaku tap dipertahankan.
  Perbaikan kompatibilitas WebKit menangani `click` berlabel mouse setelah
  `pointerdown` touch. Isi kutipan masih berupa sampel.
- **Riwayat keputusan.** Rancangan Gallery putih dan sambungan langsung ke
  Testimoni sudah digantikan koreksi pemilik. Worktree
  `/home/asyraf/projects/mustaqbal/school-gallery-choreography` menyimpan
  percobaan itu. Baseline aktif adalah checkout `school` dan catatan
  “Gallery repeats and recovered Testimonials preview”.
  [Ledger kondisi saat ini](../UI_UX_CURRENT_STATE.md),
  [blueprint historis](../blueprints/2026-11-26-gallery-testimonials-choreography.md).

## Peta file dan scope

| Area | Owner source |
| --- | --- |
| Urutan homepage | `resources/views/landing/index.blade.php` |
| Galeri | `resources/views/landing/gallery.blade.php`, `resources/css/sections/gallery.css`, `resources/js/sections/gallery*.js` |
| Testimoni | `resources/views/landing/testimonials.blade.php`, `resources/css/sections/testimonials*.css`, `resources/js/sections/testimonials*.js`, `lang/{id,en,ar}/testimonials.php` |
| Tipografi bersama | `resources/css/sections/stories.css` berbagi aturan heading dengan Testimoni |
| Tes terfokus | `tests/Feature/LandingGalleryV2Test.php`, `tests/Feature/LandingTestimonialsV2Test.php`, `tests/Unit/Gallery*.mjs`, `tests/Unit/Testimonials*.mjs` |

Baca `AGENTS.md`, `CLAUDE.md`, dan rantai canonical di
[docs/architecture/README.md](../README.md). File yang boleh diedit
mengikuti tambahan pemilik di prompt. Peta ini menunjukkan tempat mulai
membaca. Lindungi perubahan lokal yang sudah ada, termasuk `Makefile`,
`scripts/local-db.sh`, admin, dan workflow. Pertahankan cursor satu gambar,
native `scrollY`, cache pacing, Values, footer, dan isi Cerita di luar scope
yang diberikan. Gunakan database tes SQLite in-memory. Pertahankan `.env` dan
database lokal yang dipakai `make on`. Commit, push, merge, dan deploy
mengikuti izin publikasi untuk scope baru.

## Bukti dan batas verifikasi

| Gate | Hasil pada preview terkoreksi |
| --- | --- |
| `git diff --check`, `npm run build`, `npm run check:structure` | Lulus. Vite 66 modul dan struktur 284 file. |
| `node --test tests/Unit/*.test.mjs` | 47 lulus. Tes browser dijalankan terpisah. |
| `php artisan test --compact` dengan SQLite in-memory | 278 lulus, 2.277 assertions. Satu tes opt-in penulisan R2 dilewati. |
| Galeri di Chromium 158 dan WebKit 26.6 | Lulus pada 390, 768, dan 1440 px. Cek posisi tengah saat shrink, rasio kolom, warna/fase asli, exit dan reverse. Unit geometry meliputi batas tier, RTL, dan landscape pendek. |
| Testimoni di kedua engine | Layout ID/EN/AR, hover, fokus, drag, tap, enam slot HP, reduced/no-JS, dan sambungan Cerita hingga footer lulus. Uji ulang WebKit lulus setelah tes mengganti pembacaan avatar yang sedang berganti sumber. |
| Lifecycle Galeri | Chromium lulus dengan MP4 asli, fallback poster, dan maksimum dua sumber video. |

Bukti di mesin lokal tersimpan di `/tmp/schoolai-preview-correction-proof.V9PI2a`.
Lihat `gallery-{chromium,webkit}.log`, `testimonials-{chromium,webkit}.log`,
`testimonials-scroll-checked-{chromium,webkit}.log`, `node.log`, dan
`php.log`. File `testimonials-chromium-phone-six.jpg` memperlihatkan versi
HP yang sudah dipasang. Metrik pada entri rancangan putih di ledger berlaku untuk rancangan historis tersebut.

- **GAP-SAFARI.** Bukti WebKit headless Linux tersedia. Safari pada perangkat
  Apple belum diukur.
- **GAP-RELEASE.** Lighthouse produksi dan CWV lapangan belum diukur pada
  patch lokal ini.

## Lanjutan

**Claude di terminal lokal** merekonsiliasi `git status`, diff, dan SHA
dengan checkout yang melayani `8001`, lalu mengambil satu scope aktif dari
bagian “Tambahan dan koreksi saya” pada prompt. Setelah mengerjakan scope
tersebut, build ulang di checkout `school` dan cek hasil di URL yang sama.
[Prompt untuk Claude](2026-11-26-gallery-testimonials-claude-prompt.md).
