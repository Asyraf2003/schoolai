# resources2 — Homepage V2 build area

Status: **SCAFFOLD ONLY**

## Ownership label

- `/resources` = **OLD / LEGACY frontend source**. Tetap hidup untuk homepage lama dan tidak di-rename pada tahap ini karena masih dipakai aplikasi.
- `/resources2` = **NEW / Homepage V2 build area**.

Tidak ada izin dari scaffold ini untuk menghapus, memindahkan, atau mengimpor otomatis CSS/JS dari `/resources`.

## Active scope

Target pertama hanya **Hero**.

Urutan kerja setelah foundation ini:

1. HTML Hero mentah.
2. CSS Hero statis.
3. font / direction / locale / responsive concern yang benar-benar dibutuhkan Hero.
4. style/motion Hero.
5. JS Hero hanya bila behavior membutuhkan JS.
6. setelah Hero CLOSED, baru transition Hero → About.

## Folder map

```text
resources2/
├── index.html
├── css/
│   ├── index.css
│   ├── foundation/
│   │   ├── fonts.css
│   │   ├── tokens.css
│   │   └── base.css
│   ├── direction/
│   │   ├── ltr.css
│   │   └── rtl.css
│   ├── locale/
│   │   ├── id.css
│   │   ├── en.css
│   │   └── ar.css
│   ├── responsive/
│   │   ├── mobile.css
│   │   ├── tablet.css
│   │   └── desktop.css
│   ├── style/
│   │   ├── surfaces.css
│   │   ├── media.css
│   │   └── motion.css
│   └── sections/
│       └── hero.css
└── js/
    ├── index.js
    └── sections/
        └── hero.js
```

## Temporary link assumption

**AI_ASSUMPTION:** raw scaffold memakai `#hero` sebagai satu placeholder destination agar link tidak broken. Ini bukan keputusan routing V2 dan harus diganti ketika route/link contract dibahas.

## Build integration

Belum terhubung ke Laravel route, Blade production, atau Vite production entry. Itu disengaja. Scaffold ini hanya menyiapkan area kerja bersih tanpa memengaruhi homepage lama.
