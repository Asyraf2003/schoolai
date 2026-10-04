# MAP-V2-02 — Koreksi Menu + Hero

STATUS: IMPLEMENTING. Blueprint: OWNER_ACCEPTED melalui instruksi owner 2026-10-05.

## OWNER_RAW
Lihat [teks mentah lengkap](../owner/menu-hero-correction-raw.md). Arahan 2026-10-05: media “2 : 3 saja”,
“tetep ambil 1/4 selalu”, “3 bahasa hidup”, dan “Jangan merge ke main”.
Ini supersedes EN-only dan batas navigasi lama bila kapasitas tidak cocok.

## OWNER_CONFIRMED
Empat slot tetap per kolom; media dan nav satu frame; logo saja; tanpa pause;
putih polos saat open; aksen kuning; bahasa EN/ID/AR aktif; RTL foundation saja.
URL section yang belum ada tetap aktif. PR #64 tidak di-merge.

## AI_TRANSLATION / BLUEPRINT
Header memiliki geometri, warna, modal, motion dan panel. Language memakai
native details/form POST route existing, masuk dismissal Header. Hero tetap
memiliki playback. Media mempertahankan poster dan copy semantic saat gagal.
Geometri desktop: inset 7.5vw, media 20vw, tinggi 30vw, empat track nav.
Batas tinggi viewport membatasi frame; pada viewport pendek konten dapat scroll.
Compact tetap satu DOM; tablet landscape full menu mulai 1024px bila landscape,
desktop 1181px ke atas. Mobile <768 tanpa wave; hover hanya fine pointer.

## AI_ASSUMPTIONS
1024px landscape adalah breakpoint kandidat, harus diuji kapasitas tiga bahasa.
Posisi language popup mengikuti kontrol, tuning final tidak diminta.

## OWNER_CONFIRMED — keputusan HP
Jawaban awal "HP tanpa media visual; tetap tampilkan penjelasan teks."
digantikan koreksi OWNER_RAW terbaru: "no, hp  hanya menu saja, media g ada".
Berlaku dropdown <768px, bukan video Hero. HP hanya menu + deskripsi menu;
figure, gambar dan caption media disembunyikan. Desktop/tablet shared frame.
Tidak ada keputusan pending.

## SCOPE / OUT_OF_SCOPE
Shell, Header, Hero, Language, Sound, tests dan docs2. Tidak ada section lain,
admin/auth, CI legacy, dependency baru atau full RTL tuning.

## CURRENT BEHAVIOR / EXPECTED / ROOT CAUSE / OWNER / PATCH LOCATION
| Current | Expected | Root cause | Owner / location |
|---|---|---|---|
| Media hampir separuh | 20% viewport, 2:3 | Dua kolom 1fr | Header / header.css |
| Link auto rows | Empat slot tetap | Grid auto tanpa frame | Header / header.css |
| Logo + tulisan | Logo saja | Span brand tambahan | Header / header.blade.php |
| Pause control | Auto slideshow | Kontrol tambahan | Hero / hero.js, hero.blade.php |
| Tanpa garis menu | Kuning seluruh state | Hanya border link panel | Header / header.css |
| Scroll cepat dark | Batas Hero lewat | Threshold 24/48 | Header / header-state.js |
| Cream/gradient | Open putih polos | Token fill lama | Header / header.css |
| Hover wave saja | Opening tablet, no mobile | Capability hover tunggal | Header / header-motion.js |
| Language mati | POST locale existing | Span, force EN controller | Language / Blade, HomeController |
| Media rusak kosong | Poster lalu copy | Tanpa image readiness | Media / media-fallback.js |
| Close langsung | Exit ke bawah | Dismiss langsung | Header / header.js |
| Sound statis | Flowing motion | Path statis | Sound / header.css |
| Title panjang | Sekitar dua baris | Hanya clamp viewport | Hero / hero-title.js |

## PROOF
Pending patch verification; historical proof tidak membuktikan patch ini.

## NEXT VALID STEP
Implement atomic Header geometry + language, lalu Hero/media dan browser proof.
