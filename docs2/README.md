# docs2 — Homepage V2 source of truth

Status: **ACTIVE**  
Scope saat ini: **physical source switch + blank Homepage V2 foundation**.

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

`resources/index.html` sengaja hanya berisi shell HTML kosong. Belum ada Hero final, About, seam, atau runtime logic.

Target implementasi pertama hanya **Hero**.

## Prinsip utama

Homepage V2 dibangun dari nol secara bertahap. Data, bahasa, dan media existing boleh dipakai sebagai source saat dibutuhkan, tetapi implementasi frontend legacy tidak otomatis dipindahkan.

Yang dipindahkan dari legacy adalah behavior/data/reference yang memang dibutuhkan, bukan seluruh sejarah implementasinya.
