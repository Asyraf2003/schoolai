<?php

return [
    'meta' => [
        'title' => 'Al Mustaqbal School',
        'description' => 'Website resmi Al Mustaqbal School untuk informasi pendidikan, PPDB, program, kegiatan, dan kontak sekolah.',
    ],

    'hero' => [
        'eyebrow' => 'Al Mustaqbal School • TK & SD',
        'title_before' => 'Sekolah Islam yang menumbuhkan',
        'title_highlight' => 'adab, ilmu, dan hafalan',
        'title_after' => 'sejak dini',
        'subtitle' => 'Lingkungan belajar yang hangat untuk anak bertumbuh dengan Al-Qur’an, akhlak, bahasa, kreativitas, dan pendampingan guru yang dekat dengan keluarga.',
        'background_image' => 'media/home/hero-school.png',
        'visual_image' => 'media/home/hero-school.png',
        'visual_image_alt' => 'Gedung Al Mustaqbal School',
        'logo_image' => 'media/home/mustaqbalfont.png',
        'logo_image_alt' => 'Al Mustaqbal School',
        'primary_cta' => [
            'label' => 'Daftar PPDB',
            'href' => '#ppdb',
        ],
        'secondary_cta' => [
            'label' => 'Lihat Program',
            'href' => '#program',
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
            ],
            [
                'count' => 24,
                'suffix' => '+',
                'label' => 'Penghargaan Bergengsi',
            ],
            [
                'count' => 1200,
                'suffix' => '+',
                'label' => 'Jam',
            ],
            [
                'count' => 18,
                'suffix' => '+',
                'label' => 'Program',
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
        'title' => 'Daftar Sekarang di Al Mustaqbal School',
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
            'eyebrow' => 'Visi',
            'title' => 'Arah Pendidikan Al-Mustaqbal',
            'text_parts' => [
                ['text' => 'Program Pendidikan Al-Mustaqbal bertujuan untuk membina '],
                ['text' => 'generasi Muslim', 'mark' => 'blue'],
                ['text' => ' yang memiliki karakter '],
                ['text' => 'menginspirasi, inovatif, dan berintegritas', 'mark' => 'orange'],
                ['text' => ', mampu menjalankan peran mereka sebagai '],
                ['text' => 'Khalifatullah', 'mark' => 'green'],
                ['text' => ' melalui pengembangan '],
                ['text' => 'pendidikan holistik.', 'mark' => 'purple'],
            ],
        ],
        'missions_intro' => [
            'eyebrow' => 'Misi',
            'title' => 'Empat langkah membentuk generasi Islam yang utuh',
        ],
        'missions' => [
            [
                'title' => 'Fondasi Al-Qur’an & Sunnah',
                'accent' => '#22c55e',
                'text_parts' => [
                    ['text' => 'Membentuk generasi Islam berdasarkan '],
                    ['text' => 'Al-Qur’an dan Sunnah Rasulullah SAW', 'mark' => 'green'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'Wawasan Global & Bahasa',
                'accent' => '#0ea5e9',
                'text_parts' => [
                    ['text' => 'Mengembangkan semangat inspirasi dengan '],
                    ['text' => 'wawasan global', 'mark' => 'blue'],
                    ['text' => ' dan kemampuan berkomunikasi secara sosial, melalui penguasaan '],
                    ['text' => 'lebih dari satu bahasa', 'mark' => 'orange'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'Pembelajar Kritis & Inovatif',
                'accent' => '#f97316',
                'text_parts' => [
                    ['text' => 'Menciptakan generasi '],
                    ['text' => 'pembelajar sepanjang hayat', 'mark' => 'purple'],
                    ['text' => ' yang berpikir '],
                    ['text' => 'kritis dan inovatif', 'mark' => 'orange'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'Peduli Lingkungan & Berintegritas',
                'accent' => '#14b8a6',
                'text_parts' => [
                    ['text' => 'Membentuk karakter '],
                    ['text' => 'peduli lingkungan', 'mark' => 'green'],
                    ['text' => ' dengan '],
                    ['text' => 'integritas', 'mark' => 'blue'],
                    ['text' => ', baik secara lokal maupun global.'],
                ],
            ],
        ],
    ],

    'nilai_sekolah' => [
        'eyebrow' => 'Nilai Sekolah',
        'title' => 'Nilai yang Menjadi Arah Tumbuh Anak',
        'subtitle' => 'Empat nilai utama yang membentuk budaya belajar Al-Mustaqbal: dekat dengan Al-Qur’an, berani berpikir, menyatu dalam karakter, dan menginspirasi lingkungan.',
        'items' => [
            [
                'code' => 'Q',
                'title' => 'Qur\'ani',
                'accent' => '#22c55e',
                'summary' => 'Berakar pada Al-Qur’an dan Sunnah.',
                'text_parts' => [
                    ['text' => 'Membentuk karakter siswa berdasarkan '],
                    ['text' => 'Al-Qur’an dan Sunnah Rasulullah SAW', 'mark' => 'green'],
                    ['text' => ' dalam kebiasaan belajar dan kehidupan sehari-hari.'],
                ],
            ],
            [
                'code' => 'I',
                'title' => 'Inovatif',
                'accent' => '#f97316',
                'summary' => 'Berani berpikir dan mencoba solusi baru.',
                'text_parts' => [
                    ['text' => 'Mendorong siswa berpikir '],
                    ['text' => 'kritis, kreatif, dan terbuka', 'mark' => 'orange'],
                    ['text' => ' terhadap pembelajaran baru.'],
                ],
            ],
            [
                'code' => 'G',
                'title' => 'Integratif',
                'accent' => '#0ea5e9',
                'summary' => 'Ilmu, adab, bahasa, dan sosial berjalan bersama.',
                'text_parts' => [
                    ['text' => 'Menghubungkan '],
                    ['text' => 'ilmu, karakter, bahasa, dan kepedulian sosial', 'mark' => 'blue'],
                    ['text' => ' dalam pengalaman belajar yang utuh.'],
                ],
            ],
            [
                'code' => 'N',
                'title' => 'Inspiratif',
                'accent' => '#a855f7',
                'summary' => 'Tumbuh menjadi pribadi yang memberi manfaat.',
                'text_parts' => [
                    ['text' => 'Menumbuhkan semangat menjadi pribadi yang '],
                    ['text' => 'bermanfaat dan menginspirasi', 'mark' => 'purple'],
                    ['text' => ' lingkungan sekitar.'],
                ],
            ],
        ],
    ],

    'program_unggulan' => [
        'eyebrow' => 'Program Unggulan',
        'title' => 'Jalur Belajar dari Usia Dini sampai Mandiri',
        'subtitle' => 'Program Al-Mustaqbal dirancang untuk menguatkan adab, akademik, Al-Qur’an, bahasa, literasi, dan kemandirian anak secara bertahap.',
        'items' => [
            [
                'code' => 'KB',
                'label' => 'Usia Dini',
                'title' => 'Kelompok Bermain',
                'accent' => '#a855f7',
                'summary' => 'Bermain terarah untuk membangun rasa aman, sosial, dan mandiri.',
                'text_parts' => [
                    ['text' => 'Kegiatan bermain yang hangat untuk menumbuhkan '],
                    ['text' => 'kemandirian, sosial, dan rasa ingin tahu', 'mark' => 'purple'],
                    ['text' => ' sejak awal.'],
                ],
            ],
            [
                'code' => 'TK',
                'label' => 'Fondasi Anak',
                'title' => 'Taman Kanak-Kanak',
                'accent' => '#f97316',
                'summary' => 'Pembelajaran aktif sesuai tahap perkembangan anak.',
                'text_parts' => [
                    ['text' => 'Pembelajaran usia dini yang '],
                    ['text' => 'hangat, aktif, dan bertahap', 'mark' => 'orange'],
                    ['text' => ' agar anak siap masuk jenjang dasar.'],
                ],
            ],
            [
                'code' => 'SD',
                'label' => 'Akademik Qur’ani',
                'title' => 'SDIT',
                'accent' => '#0ea5e9',
                'summary' => 'Sekolah dasar Islam terpadu dengan penguatan ilmu dan karakter.',
                'text_parts' => [
                    ['text' => 'Program sekolah dasar Islam terpadu dengan penguatan '],
                    ['text' => 'akademik, adab, dan karakter Islami', 'mark' => 'blue'],
                    ['text' => '.'],
                ],
            ],
            [
                'code' => 'TQ',
                'label' => 'Hafalan & Adab',
                'title' => 'Tahfidz Al-Qur’an',
                'accent' => '#22c55e',
                'summary' => 'Hafalan Al-Qur’an dibangun bersama pembiasaan adab.',
                'text_parts' => [
                    ['text' => 'Program hafalan Al-Qur’an dengan pembiasaan '],
                    ['text' => 'adab, murajaah, dan kedekatan dengan Al-Qur’an', 'mark' => 'green'],
                    ['text' => '.'],
                ],
            ],
            [
                'code' => 'MB',
                'label' => 'Arab & Inggris',
                'title' => 'Mitra Bahasa',
                'accent' => '#14b8a6',
                'summary' => 'Bahasa menjadi alat percaya diri dan komunikasi global.',
                'text_parts' => [
                    ['text' => 'Pembelajaran bahasa Arab dan Inggris untuk membangun '],
                    ['text' => 'kemampuan komunikasi global', 'mark' => 'green'],
                    ['text' => ' secara bertahap.'],
                ],
            ],
            [
                'code' => 'LT',
                'label' => 'Budaya Baca',
                'title' => 'Literasi & Perpustakaan',
                'accent' => '#eab308',
                'summary' => 'Ruang literasi untuk menumbuhkan minat baca dan belajar mandiri.',
                'text_parts' => [
                    ['text' => 'Ruang literasi yang mendorong '],
                    ['text' => 'minat baca dan kebiasaan belajar mandiri', 'mark' => 'yellow'],
                    ['text' => '.'],
                ],
            ],
        ],
        'empty' => 'Program Unggulan Belum Tersedia',
    ],

    'ekstrakurikuler' => [
        'eyebrow' => 'Eksplorasi Bakat',
        'title' => 'Ekstrakurikuler yang Membuat Anak Berani Mencoba',
        'subtitle' => 'Kegiatan pilihan di luar kelas untuk membantu siswa mengenali minat, melatih percaya diri, bergerak aktif, dan belajar bekerja sama.',
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
            [
                'label' => 'Bahasa',
                'value' => 'bahasa',
            ],
            [
                'label' => 'Karakter',
                'value' => 'karakter',
            ],
        ],
        'items' => [
            [
                'code' => 'ART',
                'icon' => '🖍️',
                'title' => 'Menggambar Kreatif',
                'description' => 'Melatih imajinasi, warna, bentuk, dan keberanian anak mengekspresikan ide.',
                'level' => 'TK & SD',
                'category' => 'seni',
                'category_label' => 'Seni',
                'tag_class' => 'tag--seni',
                'accent' => '#ec4899',
                'variant' => 'wide',
            ],
            [
                'code' => 'MSC',
                'icon' => '🎵',
                'title' => 'Musik & Irama',
                'description' => 'Anak belajar ritme, percaya diri tampil, dan menikmati proses kreatif bersama.',
                'level' => 'TK & SD',
                'category' => 'seni',
                'category_label' => 'Seni',
                'tag_class' => 'tag--seni',
                'accent' => '#a855f7',
                'variant' => 'normal',
            ],
            [
                'code' => 'FUT',
                'icon' => '⚽',
                'title' => 'Futsal Mini',
                'description' => 'Melatih motorik, sportivitas, strategi sederhana, dan kerja sama tim.',
                'level' => 'SD',
                'category' => 'olahraga',
                'category_label' => 'Olahraga',
                'tag_class' => 'tag--olahraga',
                'accent' => '#22c55e',
                'variant' => 'normal',
            ],
            [
                'code' => 'ARC',
                'icon' => '🏹',
                'title' => 'Panahan Anak',
                'description' => 'Membangun fokus, ketenangan, disiplin, dan kontrol diri secara bertahap.',
                'level' => 'SD',
                'category' => 'olahraga',
                'category_label' => 'Olahraga',
                'tag_class' => 'tag--olahraga',
                'accent' => '#14b8a6',
                'variant' => 'tall',
            ],
            [
                'code' => 'SCI',
                'icon' => '🔬',
                'title' => 'Sains Eksperimen',
                'description' => 'Eksperimen sederhana untuk membiasakan anak bertanya, mencoba, dan menyimpulkan.',
                'level' => 'SD',
                'category' => 'sains',
                'category_label' => 'Sains',
                'tag_class' => 'tag--sains',
                'accent' => '#0ea5e9',
                'variant' => 'wide',
            ],
            [
                'code' => 'ROB',
                'icon' => '🤖',
                'title' => 'Robotik Dasar',
                'description' => 'Pengenalan logika, urutan instruksi, problem solving, dan teknologi secara menyenangkan.',
                'level' => 'SD',
                'category' => 'sains',
                'category_label' => 'Sains',
                'tag_class' => 'tag--sains',
                'accent' => '#6366f1',
                'variant' => 'normal',
            ],
            [
                'code' => 'ENG',
                'icon' => '🌍',
                'title' => 'English Fun Club',
                'description' => 'Belajar kosakata, percakapan ringan, permainan bahasa, dan keberanian berbicara.',
                'level' => 'TK & SD',
                'category' => 'bahasa',
                'category_label' => 'Bahasa',
                'tag_class' => 'tag--bahasa',
                'accent' => '#f97316',
                'variant' => 'normal',
            ],
            [
                'code' => 'ARB',
                'icon' => '📚',
                'title' => 'Arabic Fun Club',
                'description' => 'Pengenalan bahasa Arab sehari-hari melalui lagu, permainan, dan hafalan ringan.',
                'level' => 'TK & SD',
                'category' => 'bahasa',
                'category_label' => 'Bahasa',
                'tag_class' => 'tag--bahasa',
                'accent' => '#eab308',
                'variant' => 'normal',
            ],
            [
                'code' => 'SCOUT',
                'icon' => '🏕️',
                'title' => 'Pramuka Siaga',
                'description' => 'Kegiatan karakter untuk melatih kemandirian, tanggung jawab, kepemimpinan, dan kepedulian.',
                'level' => 'SD',
                'category' => 'karakter',
                'category_label' => 'Karakter',
                'tag_class' => 'tag--karakter',
                'accent' => '#92400e',
                'variant' => 'wide',
            ],
            [
                'code' => 'QUR',
                'icon' => '🕌',
                'title' => 'Tahsin & Adab',
                'description' => 'Pembiasaan membaca Al-Qur’an dengan baik, adab harian, dan kedisiplinan ibadah.',
                'level' => 'TK & SD',
                'category' => 'karakter',
                'category_label' => 'Karakter',
                'tag_class' => 'tag--karakter',
                'accent' => '#16a34a',
                'variant' => 'normal',
            ],
        ],
        'empty' => 'Belum ada kegiatan di kategori ini.',
    ],

    'galeri' => [
        'eyebrow' => 'Galeri',
        'title' => 'Galeri Pilihan Kami',
        'subtitle' => 'Dokumentasi kegiatan dan suasana belajar siswa di Al Mustaqbal School.',
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
