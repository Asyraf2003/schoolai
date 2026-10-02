# Frontend V2 resource structure

Status: **PROPOSAL / AI_TRANSLATION — belum implementasi**

## OWNER_RAW

> "resources jelas mana namanya css untuk font, untuk ar id en, untuk rtl ltr, untuk penyesuaian tablet pc hp, untuk penyesuaian jenis style dll, sejak awal terstruktur rapi gituu"

> "intinya css dan js nya bener\" rapi gituu"

## AI_TRANSLATION

Goal yang diterjemahkan AI:

- setiap file/folder punya tanggung jawab yang mudah ditebak dari namanya;
- font tidak bercampur dengan section animation;
- locale tidak bercampur dengan direction;
- RTL/LTR tidak bercampur dengan responsive;
- penyesuaian HP/tablet/PC terlihat jelas;
- style dasar, motion, media, dan section tidak berubah menjadi satu file besar;
- JS tidak memiliki banyak owner untuk behavior yang sama;
- AI pada sesi baru dapat membuka file yang relevan saja, bukan seluruh frontend.

## AI_ASSUMPTIONS

1. Path root fisik V2 belum diputuskan owner. Karena itu struktur di bawah memakai placeholder `<HOME_V2_ROOT>`.
2. Nama `mobile/tablet/desktop` mengikuti bahasa owner untuk membedakan target device; angka breakpoint final belum ditetapkan.
3. File locale-specific tidak wajib berisi CSS. File hanya dibuat jika ada perbedaan presentasi yang benar-benar spesifik locale.
4. Shared abstraction tidak dibuat hanya supaya struktur terlihat canggih. Ia harus punya reuse nyata.
5. Nama folder/file di bawah adalah proposal AI dan dapat dikoreksi owner saat MAP-V2-00.

## PROPOSED LOGICAL STRUCTURE

```text
<HOME_V2_ROOT>/
├── views/
│   ├── index.blade.php
│   └── sections/
│       ├── hero.blade.php
│       ├── about.blade.php
│       ├── program.blade.php
│       └── ...
│
├── css/
│   ├── foundation/
│   │   ├── fonts.css
│   │   ├── tokens.css
│   │   └── base.css
│   │
│   ├── direction/
│   │   ├── ltr.css
│   │   └── rtl.css
│   │
│   ├── locale/
│   │   ├── id.css
│   │   ├── en.css
│   │   └── ar.css
│   │
│   ├── responsive/
│   │   ├── mobile.css
│   │   ├── tablet.css
│   │   └── desktop.css
│   │
│   ├── style/
│   │   ├── surfaces.css
│   │   ├── media.css
│   │   └── motion.css
│   │
│   ├── sections/
│   │   ├── hero.css
│   │   ├── about.css
│   │   ├── program.css
│   │   └── ...
│   │
│   └── index.css
│
└── js/
    ├── index.js
    ├── sections/
    │   ├── hero.js
    │   ├── about.js
    │   ├── program.js
    │   └── ...
    ├── media/
    │   └── ... only when a proven media owner is needed
    └── shared/
        └── ... only after reuse is proven
```

Struktur di atas **bukan izin membuat semua file kosong dari awal**. Ia adalah peta ownership. Buat file hanya ketika tanggung jawab tersebut benar-benar ada.

## OWNERSHIP RULES

### Font

`foundation/fonts.css` hanya untuk deklarasi/ownership font dan fallback font yang digunakan V2.

Tidak boleh berisi layout section, animation, media state, atau breakpoint section.

### Locale ID / EN / AR

Text/content tetap berasal dari language source existing.

`locale/id.css`, `locale/en.css`, `locale/ar.css` hanya digunakan bila ada adjustment visual yang benar-benar spesifik locale dan tidak dapat dijelaskan oleh direction atau responsive.

Jangan copy text translation ke CSS/JS.

### RTL / LTR

`direction/rtl.css` dan `direction/ltr.css` hanya menangani behavior presentation yang memang berbeda karena direction.

Arabic tidak otomatis berarti semua adjustment masuk `ar.css`; jika penyebabnya direction, ownership-nya di RTL.

### Responsive

`responsive/mobile.css`, `tablet.css`, `desktop.css` adalah ownership penyesuaian device/layout.

Breakpoint angka final harus ditetapkan berdasarkan map aktif/audit, bukan diasumsikan dari nama file.

### Style

`style/` hanya untuk style concern yang digunakan lintas section dan benar-benar shared.

- `surfaces.css`: surface/global visual primitives yang memang reusable.
- `media.css`: presentation primitive media yang benar-benar shared.
- `motion.css`: motion primitive/shared rule yang benar-benar reusable.

Jika style hanya milik Hero, tetap di `sections/hero.css`, jangan dipindah ke shared hanya agar terlihat abstrak.

### Section

Setiap section punya owner jelas di `views/sections`, `css/sections`, dan bila perlu `js/sections`.

Section tidak boleh bergantung diam-diam pada selector legacy.

### JavaScript

Prinsip awal:

- satu behavior punya satu owner;
- jangan ada beberapa module yang sama-sama mengatur `src/load/play/pause` video yang sama;
- jangan buat global scroll controller jika section belum membuktikan kebutuhan shared;
- jangan membuat `utils.js` atau `helpers.js` sebagai tempat buangan;
- shared module baru dibuat setelah reuse/ownership terbukti.

## BUILD ORDER PER SECTION

Ini **AI_TRANSLATION** dari arahan owner "HTML mentah dulu":

1. Blade/HTML semantic dan data binding.
2. CSS static layout.
3. responsive + direction + locale adjustment yang benar-benar diperlukan.
4. visual style.
5. motion.
6. JavaScript hanya jika behavior tidak cukup diselesaikan oleh HTML/CSS.
7. media lifecycle jika section memang punya media.
8. browser proof dan performance proof.

Tahap berikutnya tidak boleh dipakai untuk menutupi fondasi tahap sebelumnya yang belum benar.

## LEGACY BOUNDARY

Legacy boleh dibaca untuk:

- `BEHAVIOR_REFERENCE`
- `VISUAL_REFERENCE`
- `DATA_SOURCE`
- `MEDIA_SOURCE`

Legacy tidak otomatis menjadi:

- CSS dependency V2;
- JS dependency V2;
- architecture template V2;
- alasan membawa fallback/state lama yang tidak dibutuhkan.

Jika satu bagian legacy benar-benar harus dipakai kembali, map aktif harus menulis alasan dan consumer-nya secara eksplisit.

## STATUS

Belum ada physical folder V2 yang dibuat oleh dokumen ini.

Path final dan import order final menjadi keputusan MAP-V2-00/implementasi pertama, dengan tetap menjaga kategori ownership di atas.
