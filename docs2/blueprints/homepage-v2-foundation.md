# Homepage V2 foundation blueprint

Status: **FOUNDATION ACTIVE — source switch complete, Hero not started**  
Tanggal: 2026-10-04

## OWNER_RAW

> "kita buat resources 2, bahasa nya tinggal ambil langnya yg sama, resources nya pelan\" kita rapikan total, buil awal yaa html mentahnya, kemudian tau dlu maksimalkan dlu hero sectionnya, setelah itu lanjut ke transisinya, intinya css dan js nya bener\" rapi gituu"

> "hero section dlu baru about section"

> "resources jelas mana namanya css untuk font, untuk ar id en, untuk rtl ltr, untuk penyesuaian tablet pc hp, untuk penyesuaian jenis style dll, sejak awal terstruktur rapi gituu"

> "jangan tambah\" asumsi anda, fokus ke masalah kita, klo ada asumsi tulsi asumsi"

> "sekarang bagaimana inii? saya harus lakuin apa? kita pindah dlu rename keduanya 1 nya resources 1 nya resources old, kemudian buat html kosongan, baru pasang 1 1 fungsinya dan ui nya atau 1 1 sectionnya"

## AI_TRANSLATION

1. Homepage V2 sekarang memakai `/resources` sebagai source aktif baru.
2. Frontend lama dipindahkan utuh ke `/resources_old` sebagai legacy reference.
3. `resources/index.html` dikosongkan menjadi shell dasar sebelum Hero dibangun.
4. Existing language source tetap digunakan; V2 tidak membuat sistem translation baru.
5. Urutan pembangunan: Hero → seam setelah Hero → About → seam berikutnya → section berikutnya.
6. Setiap section dimulai dari HTML mentah, kemudian CSS dan baru JS bila diperlukan.
7. Resource ownership dipisah jelas sejak awal.

## OWNER_CONFIRMED

- `/resources` = NEW Homepage V2 source.
- `/resources_old` = OLD/LEGACY source.
- HTML awal dibuat kosong.
- Implementasi dipasang satu fungsi/UI atau satu section pada satu waktu.
- Hero adalah target pertama.
- Existing language source tetap dipakai nanti.
- Dokumentasi harus membedakan arahan owner, terjemahan AI, dan asumsi AI.

## AI_ASSUMPTIONS

1. Nilai breakpoint final belum ditetapkan.
2. Belum diputuskan bentuk final route Laravel untuk V2.
3. Legacy di `resources_old/` dipertahankan untuk referensi dan tidak dihapus pada tahap ini.
4. Vite entry aktif sementara diarahkan hanya ke CSS/JS V2 agar source baru dapat dibangun tanpa mengandalkan legacy.

## SCOPE

- physical rename/source switch;
- blank HTML shell;
- clean CSS/JS ownership skeleton;
- V2 build entry;
- Hero tetap target pertama tetapi belum diimplementasikan.

## OUT_OF_SCOPE

- final Hero design;
- final Hero content/media;
- Hero motion implementation;
- About implementation;
- production route switch;
- legacy deletion;
- global loader/readiness.

## NEXT VALID STEP

Buka MAP-V2-01 khusus Hero. Audit Hero lama hanya sebagai visual/behavior/data/media reference, lalu bangun Hero baru di `resources/` dari HTML mentah sampai CLOSED sebelum menyentuh seam atau About.
