<?php
/* ADMIN_DESKTOP_DUMMY_LANG_FINAL */

return [
    'meta' => [
        'title' => 'Admin Al Mustaqbal School',
    ],
    'brand' => [
        'name' => 'Al Mustaqbal',
        'panel' => 'Admin Panel',
        'mark' => 'AM',
    ],
    'desktop_only' => [
        'title' => 'Dashboard admin hanya untuk PC.',
        'description' => 'Area ini sengaja dibuat untuk layar desktop agar pengelolaan konten lebih rapi dan tidak dipaksa muat di layar kecil.',
    ],
    'nav' => [
        'label' => 'Menu admin',
        'dashboard' => 'Dashboard',
        'ppdb' => 'PPDB',
        'artikel' => 'Artikel',
        'galery' => 'Galery',
        'view_site' => 'Lihat Website',
        'logout' => 'Logout',
    ],
    'shell' => [
        'eyebrow' => 'Admin Area',
        'status' => 'Dummy Mode',
        'notice' => 'Panel ini masih dummy. Struktur disiapkan dulu, fitur asli menyusul kalau manusia tidak berubah pikiran tiap lima menit.',
    ],

    /* ADMIN_GALLERY_DUMMY_LANG_FINAL */
    'gallery' => [
        'title' => 'Admin Galery',
        'eyebrow' => 'Modul Pertama',
        'heading' => 'Kelola Galery Publik',
        'description' => 'Halaman ini masih dummy, tetapi sudah memetakan struktur galeri dari data homepage menuju rancangan database nanti.',
        'limit_badge' => 'Maksimal :max item aktif',
        'minimum_badge' => 'Minimal :min item tetap valid',
        'video_badge' => 'Video maksimal :minutes menit',
        'source_title' => 'Sumber dummy dari homepage',
        'source_description' => 'Data di bawah membaca item galeri dari lang/home.php, dibatasi 6 item terbaru untuk simulasi admin.',
        'empty_title' => 'Belum ada item galeri',
        'empty_description' => 'Minimal nanti cukup 1 item galeri untuk membuat section publik tetap bisa tampil.',
        'type' => 'Tipe',
        'category' => 'Kategori',
        'duration' => 'Durasi',
        'date' => 'Tanggal',
        'caption' => 'Caption',
        'video_rule' => 'Aturan video',
        'video_rule_text' => 'Untuk video/reel, simpan durasi dalam detik dan tolak jika lebih dari 180 detik.',
        'db_title' => 'Peta Database Nanti',
        'db_description' => 'Belum dibuat migration. Ini hanya kontrak struktur agar nanti DB tidak ngawur seperti lemari tanpa rak.',
        'field' => 'Field',
        'data_type' => 'Tipe Data',
        'note' => 'Catatan',
        'slot_title' => 'Slot Tampilan Publik',
        'slot_description' => 'Homepage dan halaman galeri cukup mengambil item aktif berdasarkan sort_order, maksimal 6 untuk homepage.',
    ],
    /* /ADMIN_GALLERY_DUMMY_LANG_FINAL */

    'pages' => [
        'dashboard' => [
            'title' => 'Dashboard Admin',
            'heading' => 'Dashboard',
            'description' => 'Ringkasan awal untuk pengelolaan website sekolah.',
            'empty_title' => 'Dashboard belum tersedia',
            'empty_description' => 'Nanti area ini bisa diisi ringkasan PPDB, jumlah artikel, aktivitas galeri, dan status konten publik.',
            'cards' => [
                ['label' => 'Status Website', 'value' => 'Public dummy aktif'],
                ['label' => 'Modul Aktif', 'value' => 'Navbar, footer, halaman publik'],
                ['label' => 'Aksi Berikutnya', 'value' => 'Siapkan data admin asli'],
            ],
        ],
        'ppdb' => [
            'title' => 'Admin PPDB',
            'heading' => 'PPDB',
            'description' => 'Ruang pengelolaan informasi PPDB.',
            'empty_title' => 'Modul PPDB belum tersedia',
            'empty_description' => 'Nanti bisa berisi data pendaftar, timeline, dokumen, FAQ, dan tombol publikasi.',
            'cards' => [
                ['label' => 'Data Pendaftar', 'value' => 'Belum aktif'],
                ['label' => 'Timeline', 'value' => 'Dummy'],
                ['label' => 'Dokumen', 'value' => 'Belum tersedia'],
            ],
        ],
        'artikel' => [
            'title' => 'Admin Artikel',
            'heading' => 'Artikel',
            'description' => 'Ruang pengelolaan artikel sekolah.',
            'empty_title' => 'Modul artikel belum tersedia',
            'empty_description' => 'Nanti bisa berisi daftar artikel, kategori, draft, jadwal publikasi, dan editor konten.',
            'cards' => [
                ['label' => 'Total Artikel', 'value' => 'Dummy'],
                ['label' => 'Draft', 'value' => 'Belum aktif'],
                ['label' => 'Kategori', 'value' => 'Belum tersedia'],
            ],
        ],
        'galeri' => [
            'title' => 'Admin Galery',
            'heading' => 'Galery',
            'description' => 'Ruang pengelolaan foto dan video kegiatan.',
            'empty_title' => 'Modul galery belum tersedia',
            'empty_description' => 'Nanti bisa berisi upload foto, video, kategori kegiatan, dan pengaturan tampil di homepage.',
            'cards' => [
                ['label' => 'Foto', 'value' => 'Belum aktif'],
                ['label' => 'Video/Reel', 'value' => 'Belum aktif'],
                ['label' => 'Kategori', 'value' => 'Dummy'],
            ],
        ],
    ],
];
