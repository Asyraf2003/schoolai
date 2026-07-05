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
