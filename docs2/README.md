# docs2 — Homepage V2 source of truth

Status: **ACTIVE — documentation foundation only**  
Scope saat ini: **homepage V2**. Belum ada implementasi produk dari dokumen ini.

`docs2/` dibuat sebagai sumber arahan baru yang bersih agar pekerjaan homepage V2 tidak tercampur dengan histori, asumsi, atau keputusan legacy yang sudah menumpuk.

## Urutan otoritas

Jika ada konflik, gunakan urutan berikut:

1. **OWNER_RAW** — arahan mentah owner. Ini sumber tertinggi.
2. **OWNER_CONFIRMED** — keputusan yang sudah dikonfirmasi owner setelah diskusi.
3. **AI_TRANSLATION** — terjemahan AI terhadap goal owner. Ini interpretasi, bukan kata-kata owner.
4. **AI_ASSUMPTION** — hal yang belum diberikan owner tetapi sementara dibutuhkan untuk berpikir. Asumsi tidak boleh diam-diam berubah menjadi requirement.
5. **LEGACY_REFERENCE** — source/docs lama hanya sebagai bahan referensi atau kontrak perilaku bila map aktif memang memerlukannya.

## Format wajib setiap blueprint/map

Setiap dokumen kerja V2 wajib mempunyai bagian yang jelas:

- `OWNER_RAW`
- `AI_TRANSLATION`
- `AI_ASSUMPTIONS`
- `OWNER_CONFIRMED` bila ada
- `SCOPE`
- `OUT_OF_SCOPE`
- `STATUS`
- `NEXT VALID STEP`

AI dilarang mencampur OWNER_RAW dengan AI_TRANSLATION dalam satu paragraf seolah-olah keduanya berasal dari owner.

Jika AI membuat keputusan teknis yang tidak pernah diminta owner, keputusan itu harus berada di `AI_ASSUMPTIONS` atau ditandai sebagai proposal sampai dibuktikan/dikonfirmasi.

## Cara membaca docs2

Untuk sesi baru, baca hanya urutan ini terlebih dahulu:

1. `docs2/README.md`
2. blueprint aktif di `docs2/blueprints/`
3. aturan struktur di `docs2/architecture/`
4. protokol kerja di `docs2/protocols/`
5. handoff aktif bila nanti sudah dibuat

Jangan membaca seluruh `docs/` legacy sebagai prasyarat default. Legacy hanya dibuka jika blueprint aktif secara eksplisit menyebut file/source tertentu sebagai `LEGACY_REFERENCE`.

## Dokumen awal

- `blueprints/homepage-v2-foundation.md` — goal dasar rebuild homepage V2 dan pemisahan arahan owner vs interpretasi AI.
- `architecture/frontend-v2-structure.md` — struktur logical resource V2 agar font, locale, direction, responsive, style, section, dan JS tidak tercampur.
- `protocols/working-contract.md` — cara AI bekerja section demi section, Git checkpoint, dan handoff.

## Prinsip utama

Homepage V2 dibangun sebagai surface frontend baru yang bersih. Data, bahasa, dan media existing dapat digunakan sebagai source yang sama, tetapi source frontend legacy tidak otomatis dipindahkan.

Yang dipindahkan dari legacy adalah **behavior/data/reference yang memang dibutuhkan**, bukan seluruh sejarah implementasinya.

Dokumen ini tidak memberi izin untuk menghapus legacy. Retirement legacy hanya boleh dilakukan nanti setelah V2 selesai, consumer audit jelas, dan owner menyetujui tahap tersebut.
