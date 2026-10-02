# Homepage V2 foundation blueprint

Status: **DRAFT FOUNDATION — docs only**  
Tanggal: 2026-10-03

## OWNER_RAW

> "kita buat resources 2, bahasa nya tinggal ambil langnya yg sama, resources nya pelan\" kita rapikan total, buil awal yaa html mentahnya, kemudian tau dlu maksimalkan dlu hero sectionnya, setelah itu lanjut ke transisinya, intinya css dan js nya bener\" rapi gituu"

> "hero section dlu baru about section"

> "resources jelas mana namanya css untuk font, untuk ar id en, untuk rtl ltr, untuk penyesuaian tablet pc hp, untuk penyesuaian jenis style dll, sejak awal terstruktur rapi gituu"

> "jangan tambah\" asumsi anda, fokus ke masalah kita, klo ada asumsi tulsi asumsi"

> "misal docs blueprint goal\n> saya : xxx\n> anda : goal xxx xxx xxx"

> "jadi walaupun anda improvisasi saya tau mana letak kesalahan anda di docs itu tu"

## AI_TRANSLATION

Interpretasi AI terhadap arahan di atas:

1. Homepage V2 dibangun sebagai frontend baru yang bersih, bukan melanjutkan penumpukan CSS/JS legacy.
2. Source bahasa existing tetap menjadi sumber content locale; V2 tidak membuat sistem bahasa baru.
3. Data/media existing tetap dapat dipakai bila memang source yang sama dibutuhkan.
4. Pembangunan dilakukan berurutan dan kecil:
   - Hero;
   - transition/seam setelah Hero;
   - About/Vision/Mission;
   - transition ke section berikutnya;
   - lanjut section demi section sampai bawah.
5. Setiap section dimulai dari struktur HTML/Blade yang benar dan sederhana terlebih dahulu.
6. Setelah struktur matang, baru CSS layout/responsive/style dimaksimalkan.
7. JavaScript ditambahkan hanya untuk behavior yang memang membutuhkan JavaScript.
8. Struktur resource harus dari awal memperlihatkan ownership dengan jelas: font, locale, RTL/LTR, responsive HP/tablet/PC, style/motion, section, dan JS tidak dicampur dalam file generik besar.
9. Legacy dipakai sebagai sumber behavior/data/visual reference jika diperlukan, bukan sebagai source yang otomatis dicopy ke V2.
10. Setiap improvisasi AI harus tetap terlihat sebagai interpretasi atau asumsi, sehingga owner bisa menemukan sumber salah keputusan dengan cepat.

## OWNER_CONFIRMED

Owner sudah menyetujui arah umum berikut dalam percakapan:

- membangun V2 secara bersih dan bertahap;
- Hero dikerjakan terlebih dahulu;
- setelah Hero, kerjakan transition sebelum masuk ke About;
- resource V2 harus terstruktur jelas sejak awal;
- source bahasa existing tetap dipakai;
- dokumentasi harus membedakan arahan mentah owner dengan terjemahan/asumsi AI.

## AI_ASSUMPTIONS

Asumsi berikut **BELUM merupakan keputusan owner**:

1. `resources 2` belum menetapkan nama/path folder fisik final. Dokumen architecture hanya akan memberikan struktur logical/proposal sampai map implementasi pertama menetapkan path final.
2. Nilai breakpoint HP/tablet/PC belum diberikan owner dalam arahan ini. Tidak boleh menciptakan angka breakpoint sebagai requirement V2 tanpa audit source/goal pada map terkait.
3. Belum terbukti bahwa setiap locale membutuhkan CSS khusus. Folder/bucket locale boleh disiapkan secara konsep, tetapi file locale-specific hanya boleh berisi perbedaan yang benar-benar diperlukan.
4. Belum ada keputusan owner tentang route preview V2, mekanisme feature flag, atau waktu switch dari homepage legacy ke V2.
5. Belum ada keputusan owner untuk menghapus source legacy.

## SCOPE

Blueprint foundation ini hanya menetapkan:

- cara membedakan arahan owner vs terjemahan AI;
- arah rebuild homepage V2;
- urutan section/seam;
- prinsip pemisahan resource;
- penggunaan source language/data/media existing tanpa membawa otomatis implementasi frontend legacy.

## OUT_OF_SCOPE

Belum dibahas/diizinkan oleh blueprint ini:

- implementasi Hero V2;
- pemilihan framework/library baru;
- perubahan database/content model;
- perubahan source translation;
- redesign visual;
- penghapusan CSS/JS legacy;
- switch route production;
- loader 0–100%;
- readiness graph final homepage;
- angka performance target final.

Semua hal di atas harus punya map/blueprint sendiri jika nanti dikerjakan.

## URUTAN MAP YANG DITERJEMAHKAN AI

Ini adalah **AI_TRANSLATION**, bukan daftar mentah dari owner:

1. `MAP-V2-00` — foundation/ownership/entry structure.
2. `MAP-V2-01` — Hero.
3. `MAP-V2-02` — Hero → About seam.
4. `MAP-V2-03` — About / Vision / Mission.
5. `MAP-V2-04` — About → section berikutnya seam.
6. Lanjut section → seam → section secara berurutan sampai footer.
7. Readiness/loading global baru ditentukan setelah dependency section V2 nyata sudah diketahui.
8. Legacy retirement hanya dibahas setelah V2 siap dan consumer audit tersedia.

## DEFINITION OF CLEAN V2 FOUNDATION

Foundation dianggap benar bila sesi AI baru bisa mengetahui, tanpa membaca seluruh legacy docs:

- apa yang benar-benar diminta owner;
- apa yang hanya terjemahan AI;
- apa yang masih asumsi;
- target aktif apa;
- source mana yang boleh dibaca;
- bagian mana yang belum boleh disentuh.

## NEXT VALID STEP

Dokumentasi foundation selesai lebih dahulu. Implementasi belum dimulai dari blueprint ini.

Saat owner memulai eksekusi V2, buat map aktif pertama dengan format docs2 dan mulai dari Hero, bukan dari refactor seluruh homepage.
