# Frontend V2 resource structure

Status: **SCAFFOLD IMPLEMENTED — Hero only**

## OWNER_RAW

> "resources jelas mana namanya css untuk font, untuk ar id en, untuk rtl ltr, untuk penyesuaian tablet pc hp, untuk penyesuaian jenis style dll, sejak awal terstruktur rapi gituu"

> "intinya css dan js nya bener\" rapi gituu"

> "saya belum liat resources 2 nya, buat dlu untuk html css js mentahnya cukup sekedar ada dlu aja"

## OWNER_CONFIRMED

- Physical build area baru: `/resources2`.
- `/resources` dicatat sebagai OLD/LEGACY source dan belum dihapus/di-rename.
- Untuk sekarang cukup sediakan HTML/CSS/JS mentah.
- Fokus implementasi pertama hanya Hero.

## AI_TRANSLATION

- setiap concern punya file/folder yang mudah ditebak;
- font, locale, direction, responsive, style, section, dan JS dipisah sejak awal;
- section punya owner CSS/JS sendiri;
- JS tidak boleh punya banyak owner untuk behavior yang sama;
- shared abstraction baru diisi ketika reuse nyata muncul.

## AI_ASSUMPTIONS

1. Raw scaffold menggunakan `#hero` sebagai satu placeholder destination agar tidak menghasilkan broken link. Ini bukan routing final.
2. Breakpoint mobile/tablet/desktop belum diputuskan; file tersedia sebagai ownership bucket, bukan angka breakpoint final.
3. Locale file boleh tetap kosong/comment-only jika tidak ada adjustment visual locale-specific.
4. Laravel route, Vite production entry, dan switch homepage belum diputuskan.

## PHYSICAL STRUCTURE

```text
resources2/
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
```

## OWNERSHIP RULES

- `foundation/fonts.css`: font declaration/fallback only.
- `direction/`: difference caused by LTR/RTL only.
- `locale/`: visual difference truly specific to ID/EN/AR only; content stays in existing lang source.
- `responsive/`: device/layout adjustment only; breakpoint final remains pending.
- `style/`: shared primitives only after reuse is proven.
- `sections/hero.css`: Hero-only presentation.
- `js/sections/hero.js`: Hero-only behavior.

## BUILD ORDER

1. raw semantic HTML;
2. static CSS layout;
3. direction/locale/responsive adjustment actually required;
4. visual style;
5. motion;
6. JS only for behavior that needs JS;
7. media lifecycle if Hero requires it;
8. browser/performance proof;
9. only after Hero CLOSED, build Hero → About transition.

## LEGACY BOUNDARY

Legacy `/resources` may be read only as `BEHAVIOR_REFERENCE`, `VISUAL_REFERENCE`, `DATA_SOURCE`, or `MEDIA_SOURCE` when the active map needs it. It is not an automatic CSS/JS dependency for V2.

## STATUS

Physical `resources2` scaffold exists. No production route/build integration yet. Hero is the only active section.
