# Frontend V2 resource structure

Status: **HISTORICAL STRUCTURE — source switch checkpoint**

Struktur runtime terbaru dan ownership berada pada [technical map](landing-v2-technical-map.md).
Pernyataan Hero-first/route-pending di bawah adalah histori sebelum arahan Shell + Menu + Hero.

## OWNER_RAW

> "resources jelas mana namanya css untuk font, untuk ar id en, untuk rtl ltr, untuk penyesuaian tablet pc hp, untuk penyesuaian jenis style dll, sejak awal terstruktur rapi gituu"

> "intinya css dan js nya bener\" rapi gituu"

> "saya belum liat resources 2 nya, buat dlu untuk html css js mentahnya cukup sekedar ada dlu aja"

> "sekarang bagaimana inii? saya harus lakuin apa? kita pindah dlu rename keduanya 1 nya resources 1 nya resources old, kemudian buat html kosongan, baru pasang 1 1 fungsinya dan ui nya atau 1 1 sectionnya"

## OWNER_CONFIRMED

- `/resources` adalah area frontend baru Homepage V2.
- `/resources_old` adalah source frontend lama/legacy.
- `resources/index.html` dimulai sebagai shell HTML kosong.
- Pembangunan dilakukan satu fungsi/UI atau satu section pada satu waktu.
- Fokus implementasi pertama tetap Hero.

## AI_TRANSLATION

- setiap concern punya file/folder yang mudah ditebak;
- font, locale, direction, responsive, style, section, dan JS dipisah sejak awal;
- HTML tidak langsung diisi seluruh homepage;
- Hero dibangun dan ditutup dulu sebelum seam/section berikutnya;
- legacy hanya referensi, bukan dependency otomatis.

## AI_ASSUMPTIONS

1. Nilai breakpoint mobile/tablet/desktop belum diputuskan.
2. Locale file boleh tetap comment-only jika tidak ada adjustment visual locale-specific.
3. `resources/index.html` saat ini hanya shell lokal/build foundation, bukan keputusan final tentang route Laravel production.
4. Source lama di `resources_old/` dipertahankan utuh sebagai referensi dan tidak dihapus pada tahap ini.

## PHYSICAL STRUCTURE

```text
resources/
├── README.md
├── index.html
├── css/
│   ├── index.css
│   ├── foundation/{fonts,tokens,base}.css
│   ├── direction/{ltr,rtl}.css
│   ├── locale/{id,en,ar}.css
│   ├── responsive/{mobile,tablet,desktop}.css
│   ├── style/{surfaces,media,motion}.css
│   └── sections/hero.css
└── js/
    ├── index.js
    └── sections/hero.js

resources_old/
└── legacy frontend source utuh
```

## OWNERSHIP RULES

- `foundation/fonts.css`: font declaration/fallback only.
- `direction/`: difference caused by LTR/RTL only.
- `locale/`: visual difference truly specific to ID/EN/AR only; content tetap dari language source existing.
- `responsive/`: device/layout adjustment only; breakpoint final masih pending.
- `style/`: shared primitives only after reuse is proven.
- `sections/hero.css`: Hero-only presentation.
- `js/sections/hero.js`: Hero-only behavior.

## BUILD ORDER

1. raw semantic HTML;
2. static CSS layout;
3. direction/locale/responsive adjustment yang benar-benar dibutuhkan;
4. visual style;
5. motion;
6. JS hanya jika behavior membutuhkan JS;
7. media lifecycle jika Hero membutuhkan media;
8. browser/performance proof;
9. Hero CLOSED;
10. baru Hero → About seam;
11. baru About.

## LEGACY BOUNDARY

`resources_old/` hanya boleh dibaca sebagai `BEHAVIOR_REFERENCE`, `VISUAL_REFERENCE`, `DATA_SOURCE`, atau `MEDIA_SOURCE` ketika map aktif memang memerlukannya.

## STATUS

Physical source switch selesai. `resources/index.html` masih shell kosong. Hero belum diimplementasikan.
