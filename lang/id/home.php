<?php

return [
    'meta' => [
        'title' => 'Islamic School',
        'description' => 'Selamat Datang di Website Sekolah',
    ],

    'hero' => [
        'eyebrow' => 'Islamic School • TK & SD',
        'title_before' => 'Sekolah Islam yang menumbuhkan',
        'title_highlight' => 'adab, ilmu, dan hafalan',
        'title_after' => 'sejak dini',
        'subtitle' => 'Lingkungan belajar yang hangat untuk anak bertumbuh dengan Al-Qur’an, akhlak, bahasa, kreativitas, dan pendampingan guru yang dekat dengan keluarga.',
        'background_image' => 'media/home/hero-school.png',
        'visual_image' => 'media/home/hero-school.png',
        'visual_image_alt' => 'Gedung Islamic School Al Mustaqbal',
        'logo_image' => 'media/home/mustaqbalfont.png',
        'logo_image_alt' => 'Al Mustaqbal Islamic School',
        'primary_cta' => [
            'label' => 'Daftar PPDB',
            'href' => '#ppdb',
        ],
        'secondary_cta' => [
            'label' => 'Lihat Program',
            'href' => '#program',
        ],
        'badges' => [
            [
                'icon' => '📖',
                'label' => 'Tahfidz Al-Qur’an',
                'image' => 'media/home/hero-school.png',
                'alt' => 'Kegiatan tahfidz Al-Qur’an',
            ],
            [
                'icon' => '🧑‍🏫',
                'label' => 'Guru Pendamping',
                'image' => 'media/home/hero-school.png',
                'alt' => 'Guru mendampingi siswa belajar di kelas',
            ],
            [
                'icon' => '🏫',
                'label' => 'Lingkungan Nyaman',
                'image' => 'media/home/hero-school.png',
                'alt' => 'Lingkungan belajar Islamic School',
            ],
        ],
    ],

    'navbar' => [
        'aria_label' => 'Menu utama',
        'logo' => [
            'href' => '#beranda',
            'icon' => '🌈',
            'image' => 'media/home/logo.png',
            'image_alt' => 'Logo Al Mustaqbal School',
            'line_1' => 'Al Mustaqbal',
            'line_2' => 'School',
        ],
        'items' => [
            [
                'label' => 'Beranda',
                'href' => '#beranda',
            ],
            [
                'label' => 'Pendidikan',
                'href' => '#program',
            ],
            [
                'label' => 'Galeri',
                'href' => '#galeri',
            ],
            [
                'label' => 'Artikel',
                'href' => '#artikel',
            ],
            [
                'label' => 'PPDB',
                'href' => '#ppdb',
            ],
            [
                'label' => 'Kontak',
                'href' => '#kontak',
            ],
        ],
        'cta' => [
            'label' => 'Daftar Sekarang',
            'href' => '#ppdb',
        ],
        'mobile_open_label' => 'Buka menu',
    ],

    'stats' => [
        'items' => [
            [
                'count' => 320,
                'suffix' => '+',
                'label' => 'Siswa',
                'description' => 'Yang telah lulus',
            ],
            [
                'count' => 24,
                'suffix' => '+',
                'label' => 'Penghargaan Bergengsi',
                'description' => 'Kami telah memenangkan berbagai penghargaan bergengsi dalam bidang pendidikan.',
            ],
            [
                'count' => 1200,
                'suffix' => '+',
                'label' => 'Jam',
                'description' => 'Kegiatan belajar mengajar.',
            ],
            [
                'count' => 18,
                'suffix' => '+',
                'label' => 'Program',
                'description' => 'Ekstrakurikuler unggulan.',
            ],
        ],
    ],

    'quick_info' => [
        'items' => [
            [
                'icon' => '📚',
                'background' => 'var(--color-yellow-soft)',
                'title' => 'Informasi Akademik',
                'description' => 'Pengumuman, jadwal, dan nilai sekolah tersedia dalam satu tempat.',
            ],
            [
                'icon' => '🎨',
                'background' => 'var(--color-mint-soft)',
                'title' => 'Ekstrakurikuler',
                'description' => 'Jelajahi kegiatan siswa untuk mengembangkan minat dan bakat.',
            ],
            [
                'icon' => '📝',
                'background' => 'var(--color-blue-soft)',
                'title' => 'PPDB Online',
                'description' => 'Pendaftaran peserta didik baru dapat dilakukan dengan lebih mudah.',
            ],
            [
                'icon' => '🎒',
                'background' => 'var(--color-pink-soft)',
                'title' => 'Jenjang Pendidikan',
                'description' => 'Tersedia program SDIT, taman kanak-kanak, tahfidz, dan kelompok bermain.',
            ],
        ],
    ],

    'ppdb' => [
        'eyebrow' => 'Penerimaan Peserta Didik Baru',
        'title' => 'Daftar Sekarang di Islamic School',
        'description' => 'Pendaftaran peserta didik baru dapat dilakukan dengan lebih mudah. Informasi akademik, kegiatan, dan proses PPDB tersedia dalam satu tempat.',
        'cta' => [
            'label' => 'Daftar Sekarang',
            'href' => '#ppdb',
        ],
        'steps' => [
            [
                'number' => '1',
                'title' => 'Isi Formulir',
                'description' => 'Lengkapi data calon siswa dan orang tua secara online.',
            ],
            [
                'number' => '2',
                'title' => 'Verifikasi Data',
                'description' => 'Tim admin memeriksa kelengkapan data dan berkas pendaftaran.',
            ],
            [
                'number' => '3',
                'title' => 'Konfirmasi Pendaftaran',
                'description' => 'Orang tua mendapatkan informasi lanjutan dari pihak sekolah.',
            ],
            [
                'number' => '4',
                'title' => 'Pengumuman',
                'description' => 'Hasil pendaftaran diinformasikan melalui kontak resmi sekolah.',
            ],
        ],
    ],

    'visi_misi' => [
        'vision' => [
            'eyebrow' => '🧭 Visi',
            'text' => 'Program Pendidikan Islamic School bertujuan untuk membina generasi Muslim yang memiliki karakter menginspirasi, inovatif, dan berintegritas, mampu menjalankan peran mereka sebagai Khalifatullah melalui pengembangan pendidikan holistik.',
        ],
        'missions' => [
            [
                'icon' => '🌱',
                'text' => 'Membentuk generasi Islam berdasarkan Al-Qur’an dan Sunnah Rasulullah SAW.',
            ],
            [
                'icon' => '🌍',
                'text' => 'Mengembangkan semangat inspirasi dengan wawasan global dan kemampuan berkomunikasi secara sosial melalui penguasaan lebih dari satu bahasa.',
            ],
            [
                'icon' => '💡',
                'text' => 'Menciptakan generasi pembelajar sepanjang hayat yang berpikir kritis dan inovatif.',
            ],
            [
                'icon' => '🤝',
                'text' => 'Membentuk karakter peduli lingkungan dengan integritas, baik secara lokal maupun global.',
            ],
        ],
    ],

    'nilai_sekolah' => [
        'eyebrow' => 'Nilai',
        'title' => 'Nilai-Nilai Kami',
        'items' => [
            [
                'card_class' => 'nilai-card--yellow',
                'icon' => '📖',
                'title' => 'Qur\'ani',
                'description' => 'Membentuk karakter siswa berdasarkan Al-Qur’an dan Sunnah Rasulullah SAW.',
            ],
            [
                'card_class' => 'nilai-card--mint',
                'icon' => '💡',
                'title' => 'Inovasi',
                'description' => 'Mendorong siswa berpikir kritis, kreatif, dan terbuka terhadap pembelajaran baru.',
            ],
            [
                'card_class' => 'nilai-card--pink',
                'icon' => '🤝',
                'title' => 'Integrasi',
                'description' => 'Menghubungkan ilmu, karakter, bahasa, dan kepedulian sosial dalam kehidupan sehari-hari.',
            ],
            [
                'card_class' => 'nilai-card--purple',
                'icon' => '✨',
                'title' => 'Inspirasi',
                'description' => 'Menumbuhkan semangat menjadi pribadi yang bermanfaat dan menginspirasi lingkungan sekitar.',
            ],
        ],
    ],

    'program_unggulan' => [
        'eyebrow' => 'Program Unggulan',
        'title' => 'Program Unggulan Kami',
        'subtitle' => 'Pilihan program pendidikan Islamic School untuk mendukung pembelajaran, karakter, bahasa, dan literasi siswa.',
        'items' => [
            [
                'card_class' => 'program-card--rot-1',
                'icon' => '🏫',
                'background' => 'var(--color-pink-soft)',
                'title' => 'SDIT',
                'description' => 'Program sekolah dasar Islam terpadu dengan penguatan akademik dan karakter Islami.',
            ],
            [
                'card_class' => 'program-card--rot-2',
                'icon' => '🌍',
                'background' => 'var(--color-mint-soft)',
                'title' => 'Mitra Bahasa',
                'description' => 'Pembelajaran bahasa Inggris dan bahasa Arab untuk membangun kemampuan komunikasi global.',
            ],
            [
                'card_class' => 'program-card--rot-1',
                'icon' => '🎒',
                'background' => 'var(--color-blue-soft)',
                'title' => 'Taman Kanak-Kanak',
                'description' => 'Pembelajaran usia dini yang hangat, aktif, dan sesuai tahap perkembangan anak.',
            ],
            [
                'card_class' => 'program-card--rot-2',
                'icon' => '🕌',
                'background' => 'var(--color-yellow-soft)',
                'title' => 'Tahfidz',
                'description' => 'Program hafalan Al-Qur’an dengan pembiasaan adab dan karakter Islami.',
            ],
            [
                'card_class' => 'program-card--rot-1',
                'icon' => '🧸',
                'background' => 'var(--color-purple-soft)',
                'title' => 'Kelompok Bermain',
                'description' => 'Kegiatan bermain terarah untuk membangun kemandirian, sosial, dan rasa ingin tahu.',
            ],
            [
                'card_class' => 'program-card--rot-2',
                'icon' => '📚',
                'background' => 'var(--color-orange-soft)',
                'title' => 'Perpustakaan',
                'description' => 'Ruang literasi untuk menumbuhkan minat baca dan kebiasaan belajar mandiri.',
            ],
        ],
        'empty' => 'Program Unggulan Belum Tersedia',
    ],

    'ekstrakurikuler' => [
        'eyebrow' => 'Kembangkan Bakat',
        'title' => 'Ekstrakurikuler',
        'subtitle' => 'Ekstrakurikuler unggulan di luar jam kelas untuk membantu siswa mengenali minat dan bakatnya.',
        'filters' => [
            [
                'label' => 'Semua',
                'value' => 'semua',
            ],
            [
                'label' => 'Seni',
                'value' => 'seni',
            ],
            [
                'label' => 'Olahraga',
                'value' => 'olahraga',
            ],
            [
                'label' => 'Sains',
                'value' => 'sains',
            ],
        ],
        'items' => [
            [
                'icon' => '🖍️',
                'title' => 'Menggambar',
                'category' => 'seni',
                'category_label' => 'Seni',
                'tag_class' => 'tag--seni',
            ],
            [
                'icon' => '🎵',
                'title' => 'Musik',
                'category' => 'seni',
                'category_label' => 'Seni',
                'tag_class' => 'tag--seni',
            ],
            [
                'icon' => '⚽',
                'title' => 'Futsal Mini',
                'category' => 'olahraga',
                'category_label' => 'Olahraga',
                'tag_class' => 'tag--olahraga',
            ],
            [
                'icon' => '🏕️',
                'title' => 'Pramuka Siaga',
                'category' => 'olahraga',
                'category_label' => 'Olahraga',
                'tag_class' => 'tag--olahraga',
            ],
            [
                'icon' => '🔬',
                'title' => 'Sains Dasar',
                'category' => 'sains',
                'category_label' => 'Sains',
                'tag_class' => 'tag--sains',
            ],
            [
                'icon' => '🌍',
                'title' => 'Bahasa Inggris',
                'category' => 'sains',
                'category_label' => 'Sains',
                'tag_class' => 'tag--sains',
            ],
        ],
        'empty' => 'Belum ada kegiatan di kategori ini.',
    ],

    'galeri' => [
        'eyebrow' => 'Galeri',
        'title' => 'Galeri Pilihan Kami',
        'subtitle' => 'Dokumentasi kegiatan dan suasana belajar siswa di Islamic School.',
        'items' => [
            [
                'item_class' => 'galeri-item--tall',
                'g1' => 'var(--color-yellow)',
                'g2' => 'var(--color-orange)',
                'caption' => 'Kegiatan belajar siswa Islamic School di kelas.',
                'emoji' => '📚🧒',
                'title' => 'Kegiatan Belajar',
            ],
            [
                'item_class' => '',
                'g1' => 'var(--color-mint)',
                'g2' => 'var(--color-blue)',
                'caption' => 'Aktivitas bahasa untuk membangun kemampuan komunikasi siswa.',
                'emoji' => '🌍💬',
                'title' => 'Program Bahasa',
            ],
            [
                'item_class' => '',
                'g1' => 'var(--color-blue)',
                'g2' => 'var(--color-purple)',
                'caption' => 'Kegiatan tahfidz dan pembiasaan karakter Islami.',
                'emoji' => '🕌📖',
                'title' => 'Tahfidz dan Karakter',
            ],
            [
                'item_class' => 'galeri-item--tall',
                'g1' => 'var(--color-pink)',
                'g2' => 'var(--color-purple)',
                'caption' => 'Kreativitas siswa dalam kegiatan seni dan proyek sekolah.',
                'emoji' => '🎨✨',
                'title' => 'Kreativitas Siswa',
            ],
            [
                'item_class' => '',
                'g1' => 'var(--color-orange)',
                'g2' => 'var(--color-yellow)',
                'caption' => 'Kegiatan luar kelas yang melatih kerja sama dan kemandirian.',
                'emoji' => '🏕️🤝',
                'title' => 'Kegiatan Luar Kelas',
            ],
            [
                'item_class' => '',
                'g1' => 'var(--color-purple)',
                'g2' => 'var(--color-pink)',
                'caption' => 'Momen kebersamaan siswa dalam acara sekolah.',
                'emoji' => '🎭🎶',
                'title' => 'Acara Sekolah',
            ],
        ],
    ],

    'artikel' => [
        'eyebrow' => 'Artikel Terbaru',
        'title' => 'Semua Artikel',
        'read_more' => 'Baca Artikel',
        'items' => [
            [
                'date' => '3 Juni 2026',
                'emoji' => '📰',
                'gradient_from' => 'var(--color-yellow-soft)',
                'gradient_to' => 'var(--color-orange-soft)',
                'title' => 'Informasi Akademik Islamic School',
                'description' => 'Kumpulan informasi akademik, kegiatan sekolah, dan agenda penting untuk orang tua dan siswa.',
                'href' => '#artikel',
            ],
            [
                'date' => '18 Mei 2026',
                'emoji' => '📚',
                'gradient_from' => 'var(--color-mint-soft)',
                'gradient_to' => 'var(--color-blue-soft)',
                'title' => 'Membangun Generasi Berkarakter',
                'description' => 'Catatan tentang pendidikan karakter, pembiasaan baik, dan lingkungan belajar yang mendukung perkembangan siswa.',
                'href' => '#artikel',
            ],
            [
                'date' => '2 Mei 2026',
                'emoji' => '🏫',
                'gradient_from' => 'var(--color-pink-soft)',
                'gradient_to' => 'var(--color-purple-soft)',
                'title' => 'Mengenal Program Pendidikan Islamic School',
                'description' => 'Ringkasan program SDIT, taman kanak-kanak, tahfidz, bahasa, kelompok bermain, dan perpustakaan.',
                'href' => '#artikel',
            ],
        ],
    ],

    'pengumuman' => [
        'eyebrow' => 'Pengumuman',
        'title' => 'Pengumuman Sekolah',
        'read_more' => 'Baca Selengkapnya',
        'items' => [
            [
                'note_class' => 'sticky-note--yellow',
                'pin' => '📌',
                'date' => '10 Juli 2026',
                'title' => 'Jadwal Open House',
                'description' => 'Kunjungi kelas, kenali guru, dan lihat langsung suasana belajar Islamic School.',
                'href' => '#pengumuman',
            ],
            [
                'note_class' => 'sticky-note--pink',
                'pin' => '📌',
                'date' => '1 Juni – 31 Juli 2026',
                'title' => 'Pendaftaran PPDB Gelombang 1',
                'description' => 'Pendaftaran peserta didik baru telah dibuka. Kuota tiap program terbatas.',
                'href' => '#ppdb',
            ],
            [
                'note_class' => 'sticky-note--mint',
                'pin' => '📌',
                'date' => '17 Agustus 2026',
                'title' => 'Libur Nasional',
                'description' => 'Sekolah libur dalam rangka Hari Kemerdekaan Republik Indonesia.',
                'href' => '#pengumuman',
            ],
            [
                'note_class' => 'sticky-note--purple',
                'pin' => '📌',
                'date' => '20 Desember 2026',
                'title' => 'Kegiatan Pentas Seni',
                'description' => 'Penampilan bakat seni, bahasa, dan kreativitas siswa pada akhir semester.',
                'href' => '#pengumuman',
            ],
        ],
        'empty' => 'Belum ada pengumuman.',
    ],

    'fasilitas' => [
        'eyebrow' => 'Fasilitas Sekolah',
        'title' => 'Fasilitas Pendukung Islamic School',
        'items' => [
            [
                'icon' => '🏫',
                'label' => 'Ruang Kelas Nyaman',
            ],
            [
                'icon' => '📚',
                'label' => 'Perpustakaan',
            ],
            [
                'icon' => '🛝',
                'label' => 'Area Bermain Aman',
            ],
            [
                'icon' => '🩺',
                'label' => 'UKS',
            ],
            [
                'icon' => '📷',
                'label' => 'CCTV Area Sekolah',
            ],
            [
                'icon' => '🍎',
                'label' => 'Kantin Sehat',
            ],
        ],
    ],

    'kontak' => [
        'eyebrow' => 'Kontak',
        'title' => 'Hubungi Kami',
        'cta' => [
            'label' => 'Kirim Pesan',
            'href' => 'mailto:example@gmail.com',
        ],
        'items' => [
            [
                'icon' => '📍',
                'label' => 'Alamat',
                'value' => 'Jl. Raya Cileungsi No.KM 2, RT.002/RW.008, Cileungsi, Kec. Cileungsi, Kabupaten Bogor, Jawa Barat 16820',
            ],
            [
                'icon' => '💬',
                'label' => 'WhatsApp',
                'value' => '+62 812-3456-7890',
            ],
            [
                'icon' => '✉️',
                'label' => 'Email',
                'value' => 'example@gmail.com',
            ],
            [
                'icon' => '🕘',
                'label' => 'Jam Operasional',
                'value' => 'Senin – Jumat, 07.00 – 15.00 WIB',
            ],
        ],
        'map' => [
            'aria_label' => 'Peta lokasi Islamic School',
            'pin_label' => 'Islamic School',
            'todo' => 'TODO: ganti placeholder peta dengan lokasi resmi jika data map final sudah tersedia.',
        ],
    ],

    'footer' => [
        'brand' => [
            'href' => '#beranda',
            'icon' => '🌈',
            'line_1' => 'Islamic',
            'line_2' => 'School',
            'description' => 'Mencetak generasi berkarakter, berprestasi, dan berwawasan global melalui pendidikan holistik.',
        ],
        'socials' => [
            [
                'label' => 'Instagram',
                'icon' => '📸',
                'href' => '#',
            ],
            [
                'label' => 'Facebook',
                'icon' => '📘',
                'href' => '#',
            ],
            [
                'label' => 'YouTube',
                'icon' => '📺',
                'href' => '#',
            ],
            [
                'label' => 'TikTok',
                'icon' => '🎵',
                'href' => '#',
            ],
        ],
        'links_title' => 'Tautan',
        'links' => [
            [
                'label' => 'Beranda',
                'href' => '#beranda',
            ],
            [
                'label' => 'Pendidikan',
                'href' => '#program',
            ],
            [
                'label' => 'Galeri',
                'href' => '#galeri',
            ],
            [
                'label' => 'Artikel',
                'href' => '#artikel',
            ],
            [
                'label' => 'PPDB',
                'href' => '#ppdb',
            ],
            [
                'label' => 'Kontak',
                'href' => '#kontak',
            ],
        ],
        'contact_title' => 'Kontak',
        'contact' => [
            [
                'icon' => '📍',
                'text' => 'Jl. Raya Cileungsi No.KM 2, RT.002/RW.008, Cileungsi, Kec. Cileungsi, Kabupaten Bogor, Jawa Barat 16820',
            ],
            [
                'icon' => '💬',
                'text' => '+62 812-3456-7890',
            ],
            [
                'icon' => '✉️',
                'text' => 'example@gmail.com',
            ],
        ],
        'copyright' => 'Islamic School. Semua hak dilindungi.',
    ],
];
