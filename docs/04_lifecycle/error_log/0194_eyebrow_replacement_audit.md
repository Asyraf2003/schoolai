# Eyebrow Replacement Audit

Generated: 2026-07-06T10:51:09

## Tujuan

Melacak ulang semua `eyebrow` lama dan menentukan bentuk pengganti tanpa mengembalikan badge kecil visual.

## Prinsip pengganti

- `eyebrow` yang berfungsi sebagai nama section -> naikkan jadi `h1` / `h2` / `h3`.
- `eyebrow` yang cuma dekoratif -> hapus permanen.
- `eyebrow` yang dipakai aksesibilitas -> ganti dengan heading/title yang relevan.
- Jangan pakai class/key bernama `eyebrow` lagi.

## Temuan mentah dari history

### `resources/views/welcome.blade.php`

- `b80ee2dd` L144: `<span class="visi-card__kicker">{{ $visiMisi['vision']['eyebrow'] }}</span>`
- `b80ee2dd` L163: `<span class="misi-panel__kicker">{{ $visiMisi['missions_intro']['eyebrow'] }}</span>`
- `b80ee2dd` L251: `<span class="program-spotlight__kicker">{{ $featuredPrograms['eyebrow'] }}</span>`
- `17258fe5` L110: `<p class="eyebrow eyebrow--pink">`
- `17258fe5` L111: `{{ $hero['eyebrow'] }}`
- `3062194d` L490: `<p class="eyebrow eyebrow--yellow">{{ $articlesSection['eyebrow'] }}</p>`
- `3062194d` L588: `<p class="eyebrow eyebrow--purple">{{ $facilitiesSection['eyebrow'] }}</p>`
- `0577e212` L658: `<p class="eyebrow eyebrow--blue">{{ $announcementsSection['eyebrow'] }}</p>`
- `4aae4c06` L642: `<p class="eyebrow eyebrow--purple">{{ $contactSection['eyebrow'] }}</p>`
- `4dd57df8` L620: `<p class="eyebrow eyebrow--mint">{{ $facilitiesSection['eyebrow'] }}</p>`
- `75ccc565` L432: `<p class="eyebrow eyebrow--white">{{ $gallerySection['eyebrow'] }}</p>`

### `resources/views/pages/ppdb.blade.php`

- `b80ee2dd` L11: `<span class="public-eyebrow">{{ $page['hero']['eyebrow'] }}</span>`
- `b80ee2dd` L22: `<div class="public-stat-row" aria-label="{{ $page['hero']['eyebrow'] }}">`
- `b80ee2dd` L32: `<div class="ppdb-hero-card reveal" aria-label="{{ $page['hero']['eyebrow'] }}">`
- `b80ee2dd` L59: `<p class="ppdb-journey-kicker">{{ $page['psb_showcase']['eyebrow'] }}</p>`
- `b80ee2dd` L119: `<span class="public-eyebrow">{{ $page['steps_intro']['eyebrow'] }}</span>`
- `b80ee2dd` L139: `<span class="public-eyebrow">{{ $page['program_intro']['eyebrow'] }}</span>`
- `b80ee2dd` L160: `<span class="public-eyebrow">{{ $page['documents_intro']['eyebrow'] }}</span>`
- `b80ee2dd` L173: `<span class="public-eyebrow">{{ $page['timeline_intro']['eyebrow'] }}</span>`
- `b80ee2dd` L193: `<span class="public-eyebrow">{{ $page['faq_intro']['eyebrow'] }}</span>`

### `resources/views/pages/artikel.blade.php`

- `b80ee2dd` L12: `<span class="public-eyebrow">{{ $page['hero']['eyebrow'] }}</span>`
- `b80ee2dd` L16: `<div class="article-toolbar" aria-label="{{ $page['hero']['eyebrow'] }}">`

### `resources/views/pages/galeri.blade.php`

- `b80ee2dd` L18: `<span class="public-eyebrow">{{ $page['hero']['eyebrow'] ?? '' }}</span>`
- `b80ee2dd` L40: `<span class="public-eyebrow">{{ $page['wall']['eyebrow'] ?? '' }}</span>`
- `b80ee2dd` L54: `<span class="public-eyebrow">{{ $page['wall']['section_eyebrow'] ?? 'Bagian Galeri' }}</span>`
- `86343aec` L40: `<span class="public-eyebrow">{{ $page['toolbar']['eyebrow'] ?? '' }}</span>`
- `a6fcfdd0` L11: `<span class="public-eyebrow">{{ $page['hero']['eyebrow'] }}</span>`

### `resources/views/layouts/admin.blade.php`

- `b80ee2dd` L220: `.admin-topbar__eyebrow {`

### `resources/css/pages/welcome.css`

- `b80ee2dd` L132: `.eyebrow {`
- `b80ee2dd` L143: `.eyebrow--pink { background: var(--color-pink-soft); color: #C74672; }`
- `b80ee2dd` L144: `.eyebrow--blue { background: var(--color-blue-soft); color: #1A7594; }`
- `b80ee2dd` L145: `.eyebrow--mint { background: var(--color-mint-soft); color: #1F8F63; }`
- `b80ee2dd` L146: `.eyebrow--yellow { background: var(--color-yellow-soft); color: #9A6B0A; }`
- `b80ee2dd` L147: `.eyebrow--orange { background: var(--color-orange-soft); color: #C25E1E; }`
- `b80ee2dd` L148: `.eyebrow--purple { background: var(--color-purple-soft); color: #6B4FA0; }`
- `b80ee2dd` L149: `.eyebrow--white { background: rgba(255,255,255,0.2); color: var(--color-white); }`
- `b80ee2dd` L2987: `.hero .eyebrow {`
- `b80ee2dd` L5807: `.galeri-section .eyebrow--white {`
- `b80ee2dd` L8542: `.public-eyebrow {`

### `resources/css/style.css`

- `b80ee2dd` L117: `.eyebrow {`
- `b80ee2dd` L128: `.eyebrow--pink { background: var(--color-pink-soft); color: #C74672; }`
- `b80ee2dd` L129: `.eyebrow--blue { background: var(--color-blue-soft); color: #1A7594; }`
- `b80ee2dd` L130: `.eyebrow--mint { background: var(--color-mint-soft); color: #1F8F63; }`
- `b80ee2dd` L131: `.eyebrow--yellow { background: var(--color-yellow-soft); color: #9A6B0A; }`
- `b80ee2dd` L132: `.eyebrow--orange { background: var(--color-orange-soft); color: #C25E1E; }`
- `b80ee2dd` L133: `.eyebrow--purple { background: var(--color-purple-soft); color: #6B4FA0; }`
- `b80ee2dd` L134: `.eyebrow--white { background: rgba(255,255,255,0.2); color: var(--color-white); }`

### `lang/id/home.php`

- `b80ee2dd` L141: `'eyebrow' => 'Penerimaan Peserta Didik Baru',`
- `b80ee2dd` L174: `'eyebrow' => 'Visi',`
- `b80ee2dd` L188: `'eyebrow' => 'Misi',`
- `b80ee2dd` L289: `'eyebrow' => 'Program Unggulan',`
- `17258fe5` L10: `'eyebrow' => 'Al Mustaqbal School • TK & SD',`
- `3062194d` L518: `'eyebrow' => 'Artikel Terbaru',`
- `3062194d` L558: `'eyebrow' => 'Fasilitas Sekolah',`
- `68055690` L712: `'eyebrow' => 'Pengumuman',`
- `4dd57df8` L779: `'eyebrow' => 'Kontak',`
- `75ccc565` L511: `'eyebrow' => 'Galeri Sekolah',`
- `afaee8a1` L511: `'eyebrow' => 'Galeri',`

### `lang/en/home.php`

- `b80ee2dd` L93: `'eyebrow' => 'New Student Admission',`
- `b80ee2dd` L110: `'eyebrow' => 'Vision',`
- `b80ee2dd` L124: `'eyebrow' => 'Mission',`
- `b80ee2dd` L225: `'eyebrow' => 'Featured Programs',`

### `lang/id/pages.php`

- `b80ee2dd` L46: `'eyebrow' => 'PPDB Tahun Ajaran 2026/2027',`
- `b80ee2dd` L66: `'eyebrow' => 'Program Khusus PPDB',`
- `b80ee2dd` L98: `'eyebrow' => 'Alur Pendaftaran',`
- `b80ee2dd` L109: `'eyebrow' => 'Jenjang Tersedia',`
- `b80ee2dd` L119: `'eyebrow' => 'Syarat Dokumen',`
- `b80ee2dd` L131: `'eyebrow' => 'Timeline Dummy',`
- `b80ee2dd` L141: `'eyebrow' => 'FAQ PPDB',`
- `b80ee2dd` L161: `'eyebrow' => 'Majalah Sekolah',`
- `b80ee2dd` L251: `'eyebrow' => 'Galeri Sekolah',`
- `b80ee2dd` L261: `'eyebrow' => 'Album Pilihan',`
- `86343aec` L264: `'eyebrow' => 'Pilih Bagian',`
- `a6fcfdd0` L251: `'eyebrow' => 'Galeri Kegiatan',`

### `lang/en/pages.php`

- `b80ee2dd` L46: `'eyebrow' => 'Admission 2026/2027',`
- `b80ee2dd` L66: `'eyebrow' => 'Admission Special Program',`
- `b80ee2dd` L98: `'eyebrow' => 'Admission Flow',`
- `b80ee2dd` L109: `'eyebrow' => 'Available Levels',`
- `b80ee2dd` L119: `'eyebrow' => 'Required Documents',`
- `b80ee2dd` L131: `'eyebrow' => 'Dummy Timeline',`
- `b80ee2dd` L141: `'eyebrow' => 'Admission FAQ',`
- `b80ee2dd` L161: `'eyebrow' => 'School Journal',`
- `b80ee2dd` L251: `'eyebrow' => 'School Gallery',`
- `b80ee2dd` L261: `'eyebrow' => 'Selected Album',`
- `86343aec` L264: `'eyebrow' => 'Choose Section',`
- `a6fcfdd0` L251: `'eyebrow' => 'Activity Gallery',`

### `lang/id/admin.php`

- `b80ee2dd` L27: `'eyebrow' => 'Admin Area',`
- `e88835db` L35: `'eyebrow' => 'Galeri',`
- `0dc3d858` L35: `'eyebrow' => 'Galery',`
- `82db70d4` L35: `'eyebrow' => 'CRUD Galery',`
- `5bdc519d` L35: `'eyebrow' => 'Modul Pertama',`

### `lang/en/admin.php`

- `b80ee2dd` L27: `'eyebrow' => 'Admin Area',`
- `e88835db` L35: `'eyebrow' => 'Gallery',`
- `82db70d4` L35: `'eyebrow' => 'Gallery CRUD',`
- `5bdc519d` L35: `'eyebrow' => 'First Module',`

## Rekomendasi mapping

| Area | Eyebrow lama | Bentuk baru | Catatan |
|---|---|---|---|
| Homepage Visi/Misi | Visi / Misi | `visi_misi.section_title` + `section_subtitle` | Jangan tampilkan badge kecil. Pakai header section normal. |
| Homepage Program/Pendidikan | Program Unggulan | `program_unggulan.section_title` + `section_subtitle` | Judul section utama, bukan chip. |
| Homepage Galeri | Galeri / Momen | `galeri.section_title` + `section_subtitle` | Header putih di atas gallery story. |
| PPDB Hero | PPDB Tahun Ajaran | `hero.heading` dan subtitle | Sudah cukup via H1. |
| PPDB Alur/Program/Dokumen/Timeline/FAQ | Label kecil section | `*_intro.heading` | Sudah lebih waras sebagai H2. |
| Artikel | Majalah Sekolah | `hero.heading` | Jangan badge. |
| Galeri public | Galeri Sekolah / Album Pilihan | `hero.heading` dan `wall.title` | Sudah cocok sebagai H1/H2. |
| Admin | Admin Area | tidak perlu visual | Layout admin sudah cukup jelas. |

## Aksi berikutnya

1. Tambahkan `section_title` dan `section_subtitle` untuk homepage Visi/Misi, Program, dan Galeri di `lang/id/home.php` dan `lang/en/home.php`.
2. Tambahkan CSS untuk header section baru, bukan `.eyebrow`.
3. Pastikan `rg 'eyebrow|public-eyebrow|admin-topbar__eyebrow' resources lang` tetap kosong.
