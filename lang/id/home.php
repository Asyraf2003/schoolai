<?php

return [
    'meta' => [
        'title' => 'Al Mustaqbal School',
        'description' => 'Website resmi Al Mustaqbal School untuk informasi pendidikan, PPDB, program, kegiatan, dan kontak sekolah.',
    ],

    'accessibility' => [
        'skip_to_content' => 'Langsung ke konten utama',
    ],

    'hero' => [
        'section_label' => 'Sorotan Al Mustaqbal School',
        'carousel_roledescription' => 'karusel',
        'slide_roledescription' => 'slide',
        'slide_label' => 'Slide :current dari :total',
        'dots_label' => 'Pilih sorotan',
        'previous_label' => 'Tampilkan slide sebelumnya',
        'next_label' => 'Tampilkan slide berikutnya',
        'pause_label' => 'Jeda slideshow',
        'play_label' => 'Putar slideshow',
        'autoplay_interval' => 7200,
        'fallback_image' => 'media/home/hero-school.png',
        'fallback_image_alt' => 'Lingkungan Al Mustaqbal School',
        'fallback_title' => 'Menumbuhkan generasi Muslim untuk menjalankan peran sebagai Khalifatullah',
        'fallback_description' => 'Pendidikan holistik yang Qurani, inovatif, inspiratif, dan berintegritas.',
        'slides' => [
            [
                'type' => 'image',
                'media' => 'media/home/hero-school.png',
                'poster' => null,
                'media_alt' => 'Gedung dan lingkungan Al Mustaqbal School',
                'eyebrow' => 'Al Mustaqbal School',
                'title' => 'Menumbuhkan Generasi Muslim yang Siap Menjadi Khalifatullah',
                'description' => 'Pendidikan holistik yang menautkan Al-Qur’an, adab, ilmu, bahasa, kreativitas, dan keberanian untuk memberi manfaat.',
                'cta' => [
                    'label' => 'Kenali Arah Pendidikan Kami',
                    'href' => '#visi-misi',
                    'action' => 'anchor',
                ],
                'focal_position' => 'center center',
                'overlay_strength' => 0.46,
                'source' => [
                    'name' => 'Al Mustaqbal School',
                    'url' => null,
                ],
            ],
            [
                'type' => 'video',
                'media' => null,
                'poster' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=2400&q=82',
                'media_alt' => 'Anak-anak belajar bersama di ruang kelas',
                'eyebrow' => 'Belajar yang Bermakna',
                'title' => 'Berakar pada Al-Qur’an. Bergerak dengan Inovasi.',
                'description' => 'Setiap pengalaman belajar dirancang agar anak bertanya, berkarya, bekerja sama, dan tumbuh dengan kompas nilai Islam.',
                'cta' => [
                    'label' => 'Jelajahi Program',
                    'href' => '#program',
                    'action' => 'anchor',
                ],
                'focal_position' => 'center 46%',
                'overlay_strength' => 0.50,
                'source' => [
                    'name' => 'Unsplash',
                    'url' => 'https://unsplash.com/',
                ],
            ],
            [
                'type' => 'image',
                'media' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=2400&q=82',
                'poster' => null,
                'media_alt' => 'Seorang anak tekun belajar dan menulis',
                'eyebrow' => 'Qurani · Inspiratif · Inovatif · Integritas',
                'title' => 'Satu Perjalanan Besar Dimulai dari Lingkungan yang Tepat',
                'description' => 'Sekolah dan keluarga bertumbuh sebagai satu tim untuk menyiapkan anak menghadapi dunia tanpa kehilangan iman dan jati dirinya.',
                'cta' => [
                    'label' => 'Mulai Perjalanan PPDB',
                    'href' => '/ppdb',
                    'action' => 'admission',
                ],
                'focal_position' => 'center 42%',
                'overlay_strength' => 0.54,
                'source' => [
                    'name' => 'Unsplash',
                    'url' => 'https://unsplash.com/',
                ],
            ],
        ],
    ],

    'navbar' => [
        'aria_label' => 'Menu utama',
        'logo' => [
            'href' => '#beranda',
            'icon' => '🌈',
            'image' => 'media/home/logo-nav.webp',
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
                'mega' => [
                    'toggle_label' => 'Buka menu Pendidikan',
                    'eyebrow' => 'Pendidikan Holistik',
                    'title' => 'Jalur tumbuh untuk iman, ilmu, dan karakter.',
                    'description' => 'Kenali arah pendidikan Al Mustaqbal serta pengalaman belajar yang dibangun bersama keluarga.',
                    'links' => [
                        [
                            'label' => 'Visi & Misi',
                            'description' => 'Arah besar pendidikan Al Mustaqbal.',
                            'href' => '#visi-misi',
                        ],
                        [
                            'label' => 'Nilai QIII',
                            'description' => 'Qurani, inspiratif, inovatif, dan berintegritas.',
                            'href' => '#nilai',
                        ],
                        [
                            'label' => 'Program Unggulan',
                            'description' => 'Pengalaman belajar utama untuk setiap anak.',
                            'href' => '#program',
                        ],
                        [
                            'label' => 'Informasi PPDB',
                            'description' => 'Tahapan awal bergabung bersama Al Mustaqbal.',
                            'href' => '/ppdb',
                        ],
                    ],
                ],
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
                'label' => 'Kontak',
                'href' => '#kontak',
            ],
            [
                'label' => 'Bahasa',
                'href' => '#',
                'type' => 'language',
                'badge' => 'ID',
                'options' => [
                    [
                        'locale' => 'id',
                        'label' => 'Indonesia',
                        'short' => 'ID',
                    ],
                    [
                        'locale' => 'en',
                        'label' => 'English',
                        'short' => 'EN',
                    ],
                ],
            ], 
        ], 
        'cta' => [
            'label' => 'Daftar Sekarang',
            'href' => '/ppdb',
        ],
        'mobile_open_label' => 'Buka menu',
    ],

    'about_stats_story' => [
        'board_title' => 'Al Mustaqbal School',
        'headline_line_one' => 'Gagasan Berani,',
        'headline_line_two' => 'Dihidupkan Bersama',
        'description' => 'Al Mustaqbal mendampingi anak bertumbuh melalui pendidikan holistik yang menyatukan Al-Qur’an, adab, ilmu, bahasa, kreativitas, dan keberanian—agar lahir generasi Muslim yang inspiratif, inovatif, berintegritas, serta siap menjalankan peran sebagai Khalifatullah.',
        'cta' => 'Arah Pendidikan Kami',
        'media_label' => 'Lihat Perjalanan Kami',
        'media_alt' => 'Perjalanan belajar di Al Mustaqbal School',
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
        'title' => 'Daftar Sekarang di Al Mustaqbal School',
        'description' => 'Pendaftaran peserta didik baru dapat dilakukan dengan lebih mudah. Informasi akademik, kegiatan, dan proses PPDB tersedia dalam satu tempat.',
        'cta' => [
            'label' => 'Daftar Sekarang',
            'href' => '/ppdb',
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

        'section_title' => 'Arah Pendidikan, Visi, dan Misi Sekolah',

        'section_subtitle' => 'Bagian ini merangkum arah besar pendidikan Al-Mustaqbal: iman, ilmu, adab, bahasa, kemandirian, dan karakter anak secara utuh.',

        'vision' => [
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
            ]
        ],
        'missions_intro' => [
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
                    ['text' => 'kemampuan berbahasa dan wawasan global', 'mark' => 'blue'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'Inovasi & Kemandirian',
                'accent' => '#f97316',
                'text_parts' => [
                    ['text' => 'Menumbuhkan kemampuan berpikir kritis, kreatif, dan mandiri melalui '],
                    ['text' => 'pengalaman belajar yang inovatif', 'mark' => 'orange'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'Integritas & Kepemimpinan',
                'accent' => '#8b5cf6',
                'text_parts' => [
                    ['text' => 'Membentuk karakter yang '],
                    ['text' => 'jujur, bertanggung jawab, berintegritas, dan siap memimpin', 'mark' => 'purple'],
                    ['text' => '.'],
                ],
            ],
        ],
    ],

    'values' => [
        'eyebrow' => 'Nilai Utama',
        'title' => 'QIII sebagai fondasi karakter.',
        'description' => 'Empat nilai utama yang mengarahkan cara anak belajar, bertumbuh, dan berinteraksi.',
        'items' => [
            [
                'key' => 'qurani',
                'label' => 'Qurani',
                'letter' => 'Q',
                'description' => 'Al-Qur’an menjadi pijakan untuk membentuk iman, adab, cara berpikir, dan tindakan.',
            ],
            [
                'key' => 'inspiratif',
                'label' => 'Inspiratif',
                'letter' => 'I',
                'description' => 'Setiap anak didorong untuk memberi teladan dan menghadirkan manfaat bagi lingkungan.',
            ],
            [
                'key' => 'inovatif',
                'label' => 'Inovatif',
                'letter' => 'I',
                'description' => 'Belajar dirancang untuk mengembangkan kreativitas, rasa ingin tahu, dan keberanian mencoba.',
            ],
            [
                'key' => 'integritas',
                'label' => 'Integritas',
                'letter' => 'I',
                'description' => 'Kejujuran, tanggung jawab, dan konsistensi dibiasakan dalam setiap proses.',
            ],
        ],
    ],

    'programs' => [
        'eyebrow' => 'Program Pendidikan',
        'title' => 'Ruang tumbuh yang utuh untuk setiap anak.',
        'description' => 'Program dirancang untuk menyatukan fondasi keislaman, kemampuan akademik, kreativitas, kemandirian, dan keterampilan sosial.',
        'spotlight' => [
            'eyebrow' => 'Program Unggulan',
            'title' => 'Belajar lewat pengalaman nyata.',
            'description' => 'Anak belajar melalui proyek, eksplorasi, percakapan, praktik, dan refleksi yang dekat dengan kehidupan sehari-hari.',
            'cta' => 'Lihat program',
            'href' => '#galeri',
        ],
        'items' => [
            [
                'number' => '01',
                'title' => 'Tahfidz & Qurani',
                'description' => 'Pembiasaan Al-Qur’an, hafalan, adab, dan nilai Islam hadir dalam ritme belajar sehari-hari.',
                'href' => '#galeri',
            ],
            [
                'number' => '02',
                'title' => 'Bahasa & Literasi',
                'description' => 'Bahasa Indonesia, Inggris, dan Arab dikembangkan lewat membaca, berbicara, cerita, dan aktivitas bermakna.',
                'href' => '#galeri',
            ],
            [
                'number' => '03',
                'title' => 'Sains & Eksplorasi',
                'description' => 'Rasa ingin tahu tumbuh melalui pengamatan, eksperimen, proyek, dan pertanyaan yang dekat dengan dunia anak.',
                'href' => '#galeri',
            ],
            [
                'number' => '04',
                'title' => 'Karakter & Kemandirian',
                'description' => 'Anak dibiasakan bertanggung jawab, bekerja sama, berkomunikasi, dan menyelesaikan masalah secara bertahap.',
                'href' => '#galeri',
            ],
        ],
    ],

    'gallery' => [
        'eyebrow' => 'Galeri',
        'title' => 'Momen belajar yang hidup.',
        'description' => 'Dokumentasi kegiatan siswa dari kelas, proyek, ibadah, seni, olahraga, dan pengalaman belajar lainnya.',
        'cta' => 'Lihat semua galeri',
        'href' => '/galeri',
        'fallback_alt' => 'Kegiatan siswa Al Mustaqbal School',
    ],

    'articles' => [
        'eyebrow' => 'Artikel',
        'title' => 'Cerita, gagasan, dan kabar dari sekolah.',
        'description' => 'Baca pembaruan terbaru tentang pendidikan, kegiatan siswa, program, dan kehidupan sekolah.',
        'cta' => 'Lihat semua artikel',
        'href' => '/artikel',
        'read_more' => 'Baca selengkapnya',
        'empty' => 'Belum ada artikel yang dipublikasikan.',
    ],

    'testimonials' => [
        'eyebrow' => 'Cerita dari Keluarga',
        'title' => 'Tumbuh bersama sekolah.',
        'description' => 'Pengalaman orang tua dan keluarga yang menjadi bagian dari perjalanan pendidikan Al Mustaqbal.',
        'items' => [
            [
                'quote' => 'Anak kami semakin percaya diri, lebih mandiri, dan senang bercerita tentang apa yang ia pelajari di sekolah.',
                'name' => 'Orang Tua Siswa',
                'role' => 'Keluarga Al Mustaqbal',
            ],
            [
                'quote' => 'Kami melihat sekolah tidak hanya mengejar akademik, tetapi juga memperhatikan adab, karakter, dan kebiasaan baik anak.',
                'name' => 'Wali Murid',
                'role' => 'Keluarga Al Mustaqbal',
            ],
        ],
    ],

    'cta_final' => [
        'eyebrow' => 'Mulai Perjalanan',
        'title' => 'Temukan lingkungan belajar yang tepat untuk anak.',
        'description' => 'Kenali program, suasana sekolah, dan proses penerimaan siswa baru Al Mustaqbal School.',
        'primary' => [
            'label' => 'Informasi PPDB',
            'href' => '/ppdb',
        ],
        'secondary' => [
            'label' => 'Hubungi Sekolah',
            'href' => '#kontak',
        ],
    ],

'footer' => [
        'brand' => [
            'href' => '#beranda',
            'image' => '/media/home/logo-footer.webp',
            'image_alt' => 'Logo Al Mustaqbal',
            'name' => 'Al Mustaqbal',
            'description' => 'Sekolah Islam ramah anak yang mendampingi tumbuh kembang, adab, dan rasa ingin tahu siswa sejak usia dini.',
        ],
        'channels_title' => 'Media Sosial :',
        'channels' => [
            [
                'label' => 'Lokasi',
                'icon' => 'maps',
                'asset' => '/media/home/maps.png',
                'asset_alt' => 'Ikon lokasi Google Maps',
                'href' => 'https://www.google.com/maps/search/?api=1&query=Jl.%20Mayjend%20Panjaitan%20No.19%2C%20Penanggungan%2C%20Klojen%2C%20Malang',
            ],
            [
                'label' => 'WhatsApp',
                'icon' => 'whatsapp',
                'asset' => '/media/home/wa.svg',
                'asset_alt' => 'Ikon WhatsApp',
                'href' => 'https://wa.me/6288991128060',
            ],
            [
                'label' => 'Instagram',
                'icon' => 'instagram',
                'asset' => '/media/home/instagram.svg',
                'asset_alt' => 'Ikon Instagram',
                'href' => 'https://www.instagram.com/sdit.almustaqbal',
            ],
            [
                'label' => 'Facebook',
                'icon' => 'facebook',
                'asset' => '/media/home/facebook.png',
                'asset_alt' => 'Ikon Facebook',
                'href' => 'https://www.facebook.com/almustaqbal.School19/',
            ],
            [
                'label' => 'Email',
                'icon' => 'email',
                'asset' => '/media/home/gmail.png',
                'asset_alt' => 'Ikon Gmail',
                'href' => 'mailto:almustaqbal010@gmail.com',
            ],
        ],
'links_title' => 'Halaman Kami',
        'links' => [
            [
                'label' => 'Beranda',
                'href' => '#beranda',
            ],
            [
                'label' => 'Tentang',
                'href' => '#visi-misi',
            ],
            [
                'label' => 'Artikel',
                'href' => '/artikel',
            ],
            [
                'label' => 'Galeri',
                'href' => '/galeri',
            ],
            [
                'label' => 'PPDB',
                'href' => '/ppdb',
            ],
        ],
        'gallery_links_title' => 'Galeri Kami',
        'gallery_links' => [
            [
                'label' => 'SDIT',
                'href' => '/galeri',
            ],
            [
                'label' => 'Bahasa',
                'href' => '/galeri',
            ],
            [
                'label' => 'Taman Kanak-Kanak',
                'href' => '/galeri',
            ],
            [
                'label' => 'Tahfidz',
                'href' => '/galeri',
            ],
            [
                'label' => 'Perpustakaan',
                'href' => '/galeri',
            ],
        ],
        'partners_title' => 'Mitra Kami',
        'partners' => [
            [
                'label' => 'Dokumentasi Kegiatan',
                'image' => '/media/home/6.png',
                'href' => 'https://www.instagram.com/reel/CzFSt1oyYGm',
            ],
            [
                'label' => 'SDIT Al Mustaqbal',
                'image' => '/media/home/2.png',
                'href' => 'https://www.instagram.com/sdit.almustaqbal',
            ],
            [
                'label' => 'DT Peduli',
                'image' => '/media/home/3.png',
                'href' => 'https://dtpeduli.org/',
            ],
            [
                'label' => 'F1 Club Taekwondo Malang',
                'image' => '/media/home/4.png',
                'href' => 'https://www.instagram.com/f1clubtaekwondo_malang',
            ],
            [
                'label' => 'Digido',
                'image' => '/media/home/5.png',
                'href' => 'https://digido.co.id/',
            ],
        ],
        'copyright' => 'Al Mustaqbal. Semua Hak Dilindungi.',
    ],

];