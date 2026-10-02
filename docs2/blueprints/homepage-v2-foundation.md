# Homepage V2 foundation blueprint

Status: **FOUNDATION ACTIVE — Hero scaffold started**  
Tanggal: 2026-10-03

## OWNER_RAW

> "kita buat resources 2, bahasa nya tinggal ambil langnya yg sama, resources nya pelan\" kita rapikan total, buil awal yaa html mentahnya, kemudian tau dlu maksimalkan dlu hero sectionnya, setelah itu lanjut ke transisinya, intinya css dan js nya bener\" rapi gituu"

> "hero section dlu baru about section"

> "resources jelas mana namanya css untuk font, untuk ar id en, untuk rtl ltr, untuk penyesuaian tablet pc hp, untuk penyesuaian jenis style dll, sejak awal terstruktur rapi gituu"

> "jangan tambah\" asumsi anda, fokus ke masalah kita, klo ada asumsi tulsi asumsi"

> "saya belum liat resources 2 nya, buat dlu untuk html css js mentahnya cukup sekedar ada dlu aja"

> "resources saat ini dicatatat reosurces old"

## AI_TRANSLATION

1. Homepage V2 dibangun sebagai frontend baru yang bersih di `/resources2`.
2. Existing `/resources` tetap menjadi legacy source sampai tahap switch/retirement dibahas terpisah.
3. Existing language source tetap digunakan; V2 tidak membuat sistem translation baru.
4. Urutan pembangunan: Hero → seam setelah Hero → About → seam berikutnya → section berikutnya.
5. Setiap section dimulai dari HTML mentah, kemudian CSS dan baru JS bila diperlukan.
6. Resource ownership dipisah jelas sejak awal.

## OWNER_CONFIRMED

- `/resources2` adalah build area baru.
- `/resources` dicatat sebagai OLD/LEGACY source.
- Skeleton awal cukup HTML/CSS/JS mentah.
- Hero adalah target pertama.
- Existing language source tetap dipakai nanti.
- Dokumentasi harus membedakan arahan owner, terjemahan AI, dan asumsi AI.

## AI_ASSUMPTIONS

1. `#hero` dipakai sebagai placeholder link tunggal pada raw scaffold agar tidak broken. Ini bukan URL/routing final.
2. Nilai breakpoint final belum ditetapkan.
3. Belum diputuskan apakah raw `index.html` nanti tetap menjadi preview surface atau diganti Blade ketika integration dimulai.
4. Belum ada keputusan tentang Vite entry, Laravel route preview, feature flag, atau waktu switch production.
5. Belum ada izin menghapus legacy.

## SCOPE

Foundation + physical `resources2` scaffold + Hero-only raw entry.

## OUT_OF_SCOPE

- final Hero design;
- final Hero content/media;
- Hero motion implementation;
- route/Vite integration;
- About implementation;
- production switch;
- legacy retirement;
- global loader/readiness.

## NEXT VALID STEP

Audit target visual/behavior Hero yang harus dipertahankan atau dibangun, lalu implement Hero di `resources2` tanpa masuk ke About atau seam sebelum Hero dinyatakan CLOSED.
