# docs2 — Homepage V2 source of truth

Status: **ACTIVE**  
Scope saat ini: **MAP-V2-16: Hero V2 hanya opening video**.

Map aktif: [MAP-V2-16](blueprints/hero-video-only-ui.md).
Map sebelumnya: [MAP-V2-15](blueprints/values-old-fidelity-heading.md).
Bukti terbaru: [Values OLD / heading](proof/values-old-fidelity-heading-status.md).
Latar yang dilindungi: [Latar selang-seling](proof/values-background-73-status.md).
Laporan kartu: [audit source dan matriks berikutnya](reports/values-card-audit.md).
Bukti garis sebelumnya: [Garis Program → Values](proof/program-values-line-status.md),
[endpoint dinding #71](proof/values-line-wall-71.json).
Bukti garis sebelumnya: [data #69](proof/program-values-line-69.json).
Bukti sebelumnya: [Values intro](proof/values-intro-status.md), [data terukur](proof/values-intro-67.json).
Program sebelumnya: [Motion #65](proof/menu-heading-status.md), [data](proof/program-motion-65.json).
Bukti putaran sebelumnya: [Nav dan latar AR](proof/program-third-status.md).
Bukti sebelumnya: [Judul Program dan navigasi](proof/nav-program-status.md).
Koreksi Program sebelumnya: [Program visual correction](proof/program-feedback-status.md).
Migrasi awal: [Program proof](proof/program-status.md).
Sound yang tetap diterima: [MAP-V2-06](blueprints/header-sound-feedback.md).
About terakhir: [MAP-V2-05](blueprints/homepage-v2-about-type.md).
Komposisi/Header yang tetap diterima: [MAP-V2-04](blueprints/homepage-v2-about-polish.md).
Arsitektur About yang diterima: [MAP-V2-03](blueprints/homepage-v2-about.md).
Header/Hero terdahulu: [MAP-V2-02](blueprints/menu-hero-correction.md);
ownership dan bukti historisnya tetap berlaku untuk owner yang tidak berubah.
Blueprint foundation lama adalah histori; urutan terbaru pada map aktif berlaku.

`docs2/` adalah sumber arahan baru yang bersih agar pekerjaan Homepage V2 tidak tercampur dengan histori dan keputusan legacy.

## Urutan otoritas

1. **OWNER_RAW** — arahan mentah owner.
2. **OWNER_CONFIRMED** — keputusan yang sudah dikonfirmasi owner.
3. **AI_TRANSLATION** — terjemahan AI terhadap goal owner.
4. **AI_ASSUMPTION** — hal yang belum diberikan owner. Tidak boleh diam-diam menjadi requirement.
5. **LEGACY_REFERENCE** — source/docs lama hanya dibuka bila map aktif memang memerlukannya.

## Physical source boundary — OWNER_CONFIRMED

- `/resources` = **NEW / ACTIVE Homepage V2 build area**.
- `/resources_old` = **OLD / LEGACY frontend source**.
- `/resources2` sudah tidak dipakai setelah source switch ini.

Legacy disimpan utuh untuk referensi. V2 tidak boleh otomatis mengimpor CSS/JS dari `resources_old/`.

## Format wajib setiap blueprint/map

- `OWNER_RAW`
- `AI_TRANSLATION`
- `AI_ASSUMPTIONS`
- `OWNER_CONFIRMED` bila ada
- `SCOPE`
- `OUT_OF_SCOPE`
- `STATUS`
- `NEXT VALID STEP`

AI dilarang mencampur OWNER_RAW dengan AI_TRANSLATION seolah-olah keduanya berasal dari owner.

## Cara membaca docs2

Untuk sesi baru, baca hanya:

1. `docs2/README.md`
2. blueprint aktif di `docs2/blueprints/`
3. aturan struktur di `docs2/architecture/`
4. protokol kerja di `docs2/protocols/`
5. handoff aktif bila ada

Jangan membaca seluruh `docs/` legacy sebagai prasyarat default.

## Active implementation source

Delivery aktif: `resources/views/landing/index.blade.php` melalui route home.
`resources/index.html` hanya artefak shell awal; bukan frontend production paralel.

Urutan aktif: **foundation → shell → Menu → Hero → About → Program → proof**.
Values memiliki judul, transisi latar dari Program, lima komposisi garis scroll,
dan kartu OLD yang diadaptasi melalui MAP-V2-15. Section berikutnya belum diimplementasikan.

## Prinsip utama

Homepage V2 dibangun dari nol secara bertahap. Data, bahasa, dan media existing boleh dipakai sebagai source saat dibutuhkan, tetapi implementasi frontend legacy tidak otomatis dipindahkan.

Yang dipindahkan dari legacy adalah behavior/data/reference yang memang dibutuhkan, bukan seluruh sejarah implementasinya.
