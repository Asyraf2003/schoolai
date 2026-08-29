<?php

return [
    'meta' => [
        'title' => 'Al Mustaqbal School',
        'description' => 'The official Al Mustaqbal School website for information about education, admissions, programs, activities, and school contact details.',
    ],

    'accessibility' => [
        'skip_to_content' => 'Skip to main content',
    ],

    'hero' => [
        'section_label' => 'Al Mustaqbal School Highlights',
        'carousel_roledescription' => 'carousel',
        'slide_roledescription' => 'slide',
        'slide_label' => 'Slide :current of :total',
        'dots_label' => 'Choose a highlight',
        'previous_label' => 'Show previous slide',
        'next_label' => 'Show next slide',
        'pause_label' => 'Pause slideshow',
        'play_label' => 'Play slideshow',
        'autoplay_interval' => 7200,
        'fallback_image' => config('media.static.hero_school'),
        'fallback_image_alt' => 'Al Mustaqbal School environment',
        'fallback_title' => 'Nurturing a Muslim generation ready to fulfill their role as Khalifatullah',
        'fallback_description' => 'A holistic education rooted in the Qur’an, innovation, inspiration, and integrity.',
        'slides' => [
            [
                'type' => 'image',
                'media' => config('media.static.hero_school'),
                'poster' => null,
                'media_alt' => 'Al Mustaqbal School building and surroundings',
                'eyebrow' => 'Al Mustaqbal School',
                'title' => 'Nurturing a Muslim Generation Ready to Become Khalifatullah',
                'description' => 'A holistic education that brings together the Qur’an, character, knowledge, languages, creativity, and the courage to benefit others.',
                'cta' => [
                    'label' => 'Discover Our Educational Direction',
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
                'media_alt' => 'Children learning together in a classroom',
                'eyebrow' => 'Meaningful Learning',
                'title' => 'Rooted in the Qur’an. Moving Forward with Innovation.',
                'description' => 'Every learning experience is designed to encourage children to ask questions, create, collaborate, and grow with Islamic values as their guiding compass.',
                'cta' => [
                    'label' => 'Explore Our Programs',
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
                'media_alt' => 'A child focused on studying and writing',
                'eyebrow' => 'Qur’anic · Inspirational · Innovative · Integrity',
                'title' => 'Every Great Journey Begins in the Right Environment',
                'description' => 'School and family grow together as one team to prepare children for the world without losing their faith and sense of identity.',
                'cta' => [
                    'label' => 'Begin the Admission Journey',
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
        'aria_label' => 'Main menu',
        'logo' => [
            'href' => '#beranda',
            'icon' => '🌈',
            'image' => 'media/home/logo-nav.webp',
            'image_alt' => 'Al Mustaqbal School logo',
            'line_1' => 'Al Mustaqbal',
            'line_2' => 'School',
        ],
        'items' => [
            [
                'label' => 'Home',
                'href' => '#beranda',
            ],
            [
                'label' => 'Education',
                'href' => '#program',
                'mega' => [
                    'toggle_label' => 'Open Education menu',
                    'eyebrow' => 'Holistic Education',
                    'title' => 'A path of growth for faith, knowledge, and character.',
                    'description' => 'Discover the educational direction of Al Mustaqbal and the learning experiences built together with families.',
                    'links' => [
                        [
                            'label' => 'Vision & Mission',
                            'description' => 'The broader educational direction of Al Mustaqbal.',
                            'href' => '#visi-misi',
                        ],
                        [
                            'label' => 'QIII Values',
                            'description' => 'Qur’anic, inspirational, innovative, and rooted in integrity.',
                            'href' => '#nilai',
                        ],
                        [
                            'label' => 'Featured Programs',
                            'description' => 'Core learning experiences for every child.',
                            'href' => '#program',
                        ],
                        [
                            'label' => 'Admission Information',
                            'description' => 'The first steps toward joining Al Mustaqbal.',
                            'href' => '/ppdb',
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Gallery',
                'href' => '#galeri',
            ],
            [
                'label' => 'Articles',
                'href' => '#artikel',
            ],
            [
                'label' => 'Contact',
                'href' => '#kontak',
            ],
            [
                'label' => 'Language',
                'href' => '#',
                'type' => 'language',
                'badge' => 'ID',
                'options' => [
                    [
                        'locale' => 'id',
                        'label' => 'Indonesian',
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
            'label' => 'Apply Now',
            'href' => '/ppdb',
        ],
        'mobile_open_label' => 'Open menu',
    ],

    'about_stats_story' => [
        'board_title' => 'Al Mustaqbal School',
        'headline_line_one' => 'Bold Ideas,',
        'headline_line_two' => 'Brought to Life',
        'description' => 'Al Mustaqbal supports every child through a holistic education that unites the Qur’an, character, knowledge, languages, creativity, and courage—nurturing a Muslim generation that inspires, innovates, acts with integrity, and is ready to fulfil its role as Khalifatullah.',
        'cta' => 'Our Approach',
        'media_label' => 'Explore Our Journey',
        'media_alt' => 'The learning journey at Al Mustaqbal School',
    ],

    'stats' => [
        'items' => [
            [
                'count' => 320,
                'suffix' => '+',
                'label' => 'Students',
            ],
            [
                'count' => 24,
                'suffix' => '+',
                'label' => 'Prestigious Awards',
            ],
            [
                'count' => 1200,
                'suffix' => '+',
                'label' => 'Hours',
            ],
            [
                'count' => 18,
                'suffix' => '+',
                'label' => 'Programs',
            ],
        ],
    ],

    'quick_info' => [
        'items' => [
            [
                'icon' => '📚',
                'background' => 'var(--color-yellow-soft)',
                'title' => 'Academic Information',
                'description' => 'School announcements, schedules, and grades are available in one place.',
            ],
            [
                'icon' => '🎨',
                'background' => 'var(--color-mint-soft)',
                'title' => 'Extracurricular Activities',
                'description' => 'Explore student activities designed to develop interests and talents.',
            ],
            [
                'icon' => '📝',
                'background' => 'var(--color-blue-soft)',
                'title' => 'Online Admissions',
                'description' => 'New student registration can be completed more easily online.',
            ],
            [
                'icon' => '🎒',
                'background' => 'var(--color-pink-soft)',
                'title' => 'Education Levels',
                'description' => 'Programs are available for Islamic elementary school, kindergarten, tahfidz, and playgroup.',
            ],
        ],
    ],

    'ppdb' => [
        'title' => 'Apply Now to Al Mustaqbal School',
        'description' => 'New student registration is now easier. Academic information, activities, and the admission process are available in one place.',
        'cta' => [
            'label' => 'Apply Now',
            'href' => '/ppdb',
        ],
        'steps' => [
            [
                'number' => '1',
                'title' => 'Complete the Form',
                'description' => 'Submit prospective student and parent information online.',
            ],
            [
                'number' => '2',
                'title' => 'Data Verification',
                'description' => 'The administrative team reviews the completeness of the submitted information and registration documents.',
            ],
            [
                'number' => '3',
                'title' => 'Registration Confirmation',
                'description' => 'Parents receive further information from the school.',
            ],
            [
                'number' => '4',
                'title' => 'Announcement',
                'description' => 'Registration results are communicated through the school’s official contact channels.',
            ],
        ],
    ],

    'visi_misi' => [
        'section_title' => 'Educational Direction, Vision, and Mission',

        'section_subtitle' => 'This section summarizes the broader educational direction of Al-Mustaqbal: faith, knowledge, character, languages, independence, and the holistic development of every child.',

        'vision' => [
            'title' => 'Al-Mustaqbal Educational Direction',
            'text_parts' => [
                ['text' => 'The Al-Mustaqbal Education Program aims to nurture a '],
                ['text' => 'Muslim generation', 'mark' => 'blue'],
                ['text' => ' with qualities that are '],
                ['text' => 'inspiring, innovative, and rooted in integrity', 'mark' => 'orange'],
                ['text' => ', capable of fulfilling their role as '],
                ['text' => 'Khalifatullah', 'mark' => 'green'],
                ['text' => ' through the development of '],
                ['text' => 'holistic education.', 'mark' => 'purple'],
            ],
        ],

        'missions_intro' => [
            'title' => 'Four pathways to nurturing a well-rounded Muslim generation',
        ],

        'missions' => [
            [
                'title' => 'Foundation of the Qur’an & Sunnah',
                'accent' => '#22c55e',
                'text_parts' => [
                    ['text' => 'Nurturing a Muslim generation based on the '],
                    ['text' => 'Qur’an and the Sunnah of the Prophet Muhammad ﷺ', 'mark' => 'green'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'Global Perspective & Languages',
                'accent' => '#0ea5e9',
                'text_parts' => [
                    ['text' => 'Developing an inspiring spirit through a '],
                    ['text' => 'global perspective', 'mark' => 'blue'],
                    ['text' => ' and strong social communication skills through mastery of '],
                    ['text' => 'more than one language', 'mark' => 'orange'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'Critical & Innovative Learners',
                'accent' => '#f97316',
                'text_parts' => [
                    ['text' => 'Developing a generation of '],
                    ['text' => 'lifelong learners', 'mark' => 'purple'],
                    ['text' => ' who think '],
                    ['text' => 'critically and innovatively', 'mark' => 'orange'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'Environmentally Conscious & Rooted in Integrity',
                'accent' => '#14b8a6',
                'text_parts' => [
                    ['text' => 'Building character that is '],
                    ['text' => 'environmentally conscious', 'mark' => 'green'],
                    ['text' => ' and grounded in '],
                    ['text' => 'integrity', 'mark' => 'blue'],
                    ['text' => ', both locally and globally.'],
                ],
            ],
        ],
    ],

    'nilai_sekolah' => [
        'heading' => 'VALUES STUDENTS',
        'heading_lines' => ['VALUES', 'STUDENTS'],
        'subtitle' => 'Four core values shape the learning culture at Al-Mustaqbal: closeness to the Qur’an, courage to think, unity of character, and the ability to inspire others.',
        'aria_label' => 'Al-Mustaqbal school values',
        'items' => [
            [
                'code' => 'Q',
                'title' => 'Qur\'anic',
                'accent' => '#22c55e',
                'summary' => 'Rooted in the Qur’an and Sunnah.',
                'text_parts' => [
                    ['text' => 'Building student character based on the '],
                    ['text' => 'Qur’an and the Sunnah of the Messenger of Allah, peace and blessings be upon him', 'mark' => 'green'],
                    ['text' => ' through everyday learning habits and daily life.'],
                ],
            ],
            [
                'code' => 'I',
                'title' => 'Innovative',
                'accent' => '#f97316',
                'summary' => 'Courageous in thinking and exploring new solutions.',
                'text_parts' => [
                    ['text' => 'Encouraging students to think '],
                    ['text' => 'critically, creatively, and openly', 'mark' => 'orange'],
                    ['text' => ' when approaching new learning experiences.'],
                ],
            ],
            [
                'code' => 'G',
                'title' => 'Integrative',
                'accent' => '#0ea5e9',
                'summary' => 'Knowledge, character, languages, and social awareness grow together.',
                'text_parts' => [
                    ['text' => 'Connecting '],
                    ['text' => 'knowledge, character, languages, and social awareness', 'mark' => 'blue'],
                    ['text' => ' through a holistic learning experience.'],
                ],
            ],
            [
                'code' => 'N',
                'title' => 'Inspirational',
                'accent' => '#a855f7',
                'summary' => 'Growing into a person who brings benefit to others.',
                'text_parts' => [
                    ['text' => 'Nurturing the aspiration to become someone who is '],
                    ['text' => 'beneficial and inspiring', 'mark' => 'purple'],
                    ['text' => ' to the surrounding community.'],
                ],
            ],
        ],
    ],

    'program_unggulan' => [
        'section_title' => 'Al-Mustaqbal Education Programs',

        'section_subtitle' => 'Educational levels and learning programs are designed progressively, from early childhood until children are ready to become academically, socially, and Qur’anically independent.',

        'title' => 'A Learning Journey from Early Childhood to Independence',
        'subtitle' => 'Al-Mustaqbal programs are designed to progressively strengthen character, academics, the Qur’an, languages, literacy, and children’s independence.',
        'chips_aria_label' => 'Program summary',
        'flow_aria_label' => 'Featured program list',

        'chips' => [
            '6 Programs',
            'Kindergarten & Elementary',
            'Qur’anic',
        ],

        'items' => [
            [
                'code' => 'KB',
                'label' => 'Early Childhood',
                'title' => 'Playgroup',
                'accent' => '#a855f7',
                'summary' => 'Guided play that builds a sense of security, social skills, and independence.',
                'text_parts' => [
                    ['text' => 'Warm and purposeful play activities designed to nurture '],
                    ['text' => 'independence, social skills, and curiosity', 'mark' => 'purple'],
                    ['text' => ' from an early age.'],
                ],
            ],
            [
                'code' => 'TK',
                'label' => 'Childhood Foundation',
                'title' => 'Kindergarten',
                'accent' => '#f97316',
                'summary' => 'Active learning tailored to each stage of child development.',
                'text_parts' => [
                    ['text' => 'Early childhood learning that is '],
                    ['text' => 'warm, active, and progressive', 'mark' => 'orange'],
                    ['text' => ' to prepare children for elementary education.'],
                ],
            ],
            [
                'code' => 'SD',
                'label' => 'Qur’anic Academics',
                'title' => 'Islamic Elementary School',
                'accent' => '#0ea5e9',
                'summary' => 'An integrated Islamic elementary school that strengthens knowledge and character.',
                'text_parts' => [
                    ['text' => 'An integrated Islamic elementary education program focused on strengthening '],
                    ['text' => 'academics, character, and Islamic values', 'mark' => 'blue'],
                    ['text' => '.'],
                ],
            ],
            [
                'code' => 'TQ',
                'label' => 'Memorization & Character',
                'title' => 'Qur’an Memorization',
                'accent' => '#22c55e',
                'summary' => 'Qur’an memorization developed alongside consistent character-building habits.',
                'text_parts' => [
                    ['text' => 'A Qur’an memorization program supported by consistent practice in '],
                    ['text' => 'character, murajaah, and closeness to the Qur’an', 'mark' => 'green'],
                    ['text' => '.'],
                ],
            ],
            [
                'code' => 'MB',
                'label' => 'Arabic & English',
                'title' => 'Language Partner',
                'accent' => '#14b8a6',
                'summary' => 'Languages become tools for confidence and global communication.',
                'text_parts' => [
                    ['text' => 'Arabic and English learning designed to progressively develop '],
                    ['text' => 'global communication skills', 'mark' => 'green'],
                    ['text' => '.'],
                ],
            ],
            [
                'code' => 'LT',
                'label' => 'Reading Culture',
                'title' => 'Literacy & Library',
                'accent' => '#eab308',
                'summary' => 'A literacy space that nurtures reading interest and independent learning.',
                'text_parts' => [
                    ['text' => 'A literacy environment that encourages '],
                    ['text' => 'a love of reading and independent learning habits', 'mark' => 'yellow'],
                    ['text' => '.'],
                ],
            ],
        ],

        'empty' => 'Featured Programs Are Not Available Yet',
    ],

    'galeri' => [
        'section_title' => 'School Activity Gallery',

        'section_subtitle' => 'Highlights of learning, worship, creative work, and student togetherness documented at school.',

        'title' => 'Latest Moments from Al Mustaqbal School',
        'subtitle' => 'Documentation of student learning, worship, creativity, and togetherness. The gallery currently uses manually managed data and Instagram links rather than an API.',
        'aria_label' => 'Latest gallery moments',
        'visual_aria_label' => 'Gallery media preview',
        'open_media_prefix' => 'Open media',
        'fallback_item_label' => 'gallery',
        'lightbox_label' => 'Homepage gallery media',
        'close_label' => 'Close',
        'video_title' => 'Gallery video',
        'default_type_label' => 'Photo',

        'cta' => [
            'label' => 'View All Gallery',
            'href' => '/galeri',
        ],

        'filters' => [
            [
                'label' => 'All',
                'value' => 'semua',
            ],
            [
                'label' => 'Photo',
                'value' => 'photo',
            ],
            [
                'label' => 'Video/Reel',
                'value' => 'video',
            ],
        ],

        'items' => [
            [
                'title' => 'Islamic Elementary Students Market Day',
                'type' => 'reel',
                'instagram_url' => 'https://www.instagram.com/',
                'thumbnail' => '',
                'published_at' => '2026-07-01',
                'date' => '1 July 2026',
                'caption' => 'Students build confidence, practice basic arithmetic, and learn to interact through Market Day activities.',
                'category' => 'Student Activities',
                'variant' => 'feature',
                'accent' => '#f97316',
                'fallback_icon' => '🛍️',
                'item_class' => 'galeri-item--tall',
                'g1' => 'var(--color-orange)',
                'g2' => 'var(--color-yellow)',
                'emoji' => '🛍️🎥',
            ],
            [
                'title' => 'Morning Tahfidz with Teachers',
                'type' => 'video',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-06-27',
                'date' => '27 June 2026',
                'caption' => 'A morning atmosphere of murajaah and tahfidz that nurtures a close relationship with the Qur’an.',
                'category' => 'Tahfidz',
                'variant' => 'tall',
                'accent' => '#22c55e',
                'fallback_icon' => '🕌',
                'item_class' => 'galeri-item--tall',
                'g1' => 'var(--color-mint)',
                'g2' => 'var(--color-blue)',
                'emoji' => '🕌🎬',
            ],
            [
                'title' => 'Children’s Science Experiment',
                'type' => 'photo',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-06-22',
                'date' => '22 June 2026',
                'caption' => 'Children learn to observe, experiment, and draw conclusions through simple science activities.',
                'category' => 'Science',
                'variant' => 'wide',
                'accent' => '#0ea5e9',
                'fallback_icon' => '🔬',
                'item_class' => '',
                'g1' => 'var(--color-blue)',
                'g2' => 'var(--color-purple)',
                'emoji' => '🔬📸',
            ],
            [
                'title' => 'Bright and Colorful Student Artwork',
                'type' => 'photo',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-06-18',
                'date' => '18 June 2026',
                'caption' => 'Student artwork that develops imagination, color awareness, and self-expression.',
                'category' => 'Art',
                'variant' => 'normal',
                'accent' => '#ec4899',
                'fallback_icon' => '🎨',
                'item_class' => '',
                'g1' => 'var(--color-pink)',
                'g2' => 'var(--color-purple)',
                'emoji' => '🎨✨',
            ],
            [
                'title' => 'English Fun Club',
                'type' => 'reel',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-06-12',
                'date' => '12 June 2026',
                'caption' => 'Light and engaging English activities through games, vocabulary practice, and simple conversations.',
                'category' => 'Languages',
                'variant' => 'wide',
                'accent' => '#14b8a6',
                'fallback_icon' => '🌍',
                'item_class' => '',
                'g1' => 'var(--color-mint)',
                'g2' => 'var(--color-blue)',
                'emoji' => '🌍🎥',
            ],
            [
                'title' => 'Outdoor Activities and Teamwork',
                'type' => 'photo',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-06-05',
                'date' => '5 June 2026',
                'caption' => 'Outdoor activities designed to strengthen courage, independence, and teamwork.',
                'category' => 'Outdoor',
                'variant' => 'tall',
                'accent' => '#eab308',
                'fallback_icon' => '🏕️',
                'item_class' => 'galeri-item--tall',
                'g1' => 'var(--color-yellow)',
                'g2' => 'var(--color-orange)',
                'emoji' => '🏕️📸',
            ],
            [
                'title' => 'Student Creativity Performance',
                'type' => 'video',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-05-29',
                'date' => '29 May 2026',
                'caption' => 'Students take the stage through art, language, and activities that build confidence in performing.',
                'category' => 'School Events',
                'variant' => 'feature',
                'accent' => '#a855f7',
                'fallback_icon' => '🎭',
                'item_class' => '',
                'g1' => 'var(--color-purple)',
                'g2' => 'var(--color-pink)',
                'emoji' => '🎭🎬',
            ],
            [
                'title' => 'Literacy and Library',
                'type' => 'photo',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-05-20',
                'date' => '20 May 2026',
                'caption' => 'Students discover books, stories, and reading habits in a comfortable environment.',
                'category' => 'Literacy',
                'variant' => 'normal',
                'accent' => '#f59e0b',
                'fallback_icon' => '📚',
                'item_class' => '',
                'g1' => 'var(--color-yellow)',
                'g2' => 'var(--color-mint)',
                'emoji' => '📚📸',
            ],
        ],
    ],

    'artikel' => [
        'title' => 'School Stories That Keep Parents Connected',
        'subtitle' => 'Short updates, parenting insights, and stories from children’s activities presented in an accessible format so parents can quickly understand the key information.',
        'read_more' => 'Read Article',
        'empty' => 'No recent articles yet.',
        'rail_aria_label' => 'More school stories',

        'cta' => [
            'label' => 'View All Stories',
            'href' => '/artikel',
        ],

        'items' => [
            [
                'issue' => '01',
                'date' => '3 June 2026',
                'category' => 'Academics',
                'reading_time' => '3 min read',
                'emoji' => '🧭',
                'gradient_from' => 'var(--color-yellow-soft)',
                'gradient_to' => 'var(--color-orange-soft)',
                'title' => 'A Child’s Learning Rhythm: Calm, Purposeful, and Unhurried',
                'description' => 'How the school structures daily activities so children have time to learn, play, pray, ask questions, and confidently try new things.',
                'highlight' => 'Focus: daily learning routines',
                'href' => '/artikel',
            ],
            [
                'issue' => '02',
                'date' => '18 May 2026',
                'category' => 'Character',
                'reading_time' => '4 min read',
                'emoji' => '🌱',
                'gradient_from' => 'var(--color-mint-soft)',
                'gradient_to' => 'var(--color-blue-soft)',
                'title' => 'Small Acts of Adab Practiced Every Day',
                'description' => 'From greeting others and waiting in line to putting things away and having the courage to apologize. Small habits can create a remarkably lasting impact.',
                'highlight' => 'Focus: character-building habits',
                'href' => '/artikel',
            ],
            [
                'issue' => '03',
                'date' => '2 May 2026',
                'category' => 'Programs',
                'reading_time' => '3 min read',
                'emoji' => '🏫',
                'gradient_from' => 'var(--color-pink-soft)',
                'gradient_to' => 'var(--color-purple-soft)',
                'title' => 'Understanding the Kindergarten, Islamic Elementary, Tahfidz, and Literacy Programs',
                'description' => 'A concise overview that helps parents understand the focus of each school program without having to work through pages of dense documentation.',
                'highlight' => 'Focus: school programs',
                'href' => '/artikel',
            ],
            [
                'issue' => '04',
                'date' => '20 April 2026',
                'category' => 'Activities',
                'reading_time' => '2 min read',
                'emoji' => '🎨',
                'gradient_from' => 'var(--color-blue-soft)',
                'gradient_to' => 'var(--color-yellow-soft)',
                'title' => 'Learning Through Creativity, Stories, and the Courage to Perform',
                'description' => 'Creative activities help children develop language, self-expression, teamwork, and confidence in a safe environment.',
                'highlight' => 'Focus: children’s creativity',
                'href' => '/galeri',
            ],
        ],
    ],

    'footer' => [
        'brand' => [
            'href' => '#beranda',
            'image' => '/media/home/logo-footer.webp',
            'image_alt' => 'Al Mustaqbal logo',
            'name' => 'Al Mustaqbal',
            'description' => 'A child-friendly Islamic school that supports students’ growth, character, and curiosity from an early age.',
        ],

        'channels_title' => 'Social Media:',

        'channels' => [
            [
                'label' => 'Location',
                'note' => 'Google Maps',
                'icon' => 'maps',
                'asset' => '/media/home/maps.png',
                'asset_alt' => 'Google Maps location icon',
                'href' => 'https://www.google.com/maps/search/?api=1&query=Jl.%20Mayjend%20Panjaitan%20No.19%2C%20Penanggungan%2C%20Klojen%2C%20Malang',
            ],
            [
                'label' => 'WhatsApp',
                'note' => 'Chat with Admin',
                'icon' => 'whatsapp',
                'asset' => '/media/home/wa.svg',
                'asset_alt' => 'WhatsApp icon',
                'href' => 'https://wa.me/6288991128060',
            ],
            [
                'label' => 'Instagram',
                'note' => 'School Activities',
                'icon' => 'instagram',
                'asset' => '/media/home/instagram.svg',
                'asset_alt' => 'Instagram icon',
                'href' => 'https://www.instagram.com/sdit.almustaqbal',
            ],
            [
                'label' => 'Facebook',
                'note' => 'School Page',
                'icon' => 'facebook',
                'asset' => '/media/home/facebook.png',
                'asset_alt' => 'Facebook icon',
                'href' => 'https://www.facebook.com/almustaqbal.School19/',
            ],
            [
                'label' => 'Email',
                'note' => 'Send a Message',
                'icon' => 'email',
                'asset' => '/media/home/gmail.png',
                'asset_alt' => 'Gmail icon',
                'href' => 'mailto:almustaqbal010@gmail.com',
            ],
        ],

        'links_title' => 'Our Pages',

        'links' => [
            [
                'label' => 'Home',
                'href' => '#beranda',
            ],
            [
                'label' => 'About',
                'href' => '#visi-misi',
            ],
            [
                'label' => 'Articles',
                'href' => '/artikel',
            ],
            [
                'label' => 'Gallery',
                'href' => '/galeri',
            ],
            [
                'label' => 'Admissions',
                'href' => '/ppdb',
            ],
        ],

        'gallery_links_title' => 'Our Gallery',

        'gallery_links' => [
            [
                'label' => 'Islamic Elementary School',
                'href' => '/galeri',
            ],
            [
                'label' => 'Languages',
                'href' => '/galeri',
            ],
            [
                'label' => 'Kindergarten',
                'href' => '/galeri',
            ],
            [
                'label' => 'Tahfidz',
                'href' => '/galeri',
            ],
            [
                'label' => 'Library',
                'href' => '/galeri',
            ],
        ],

        'partners_title' => 'Our Partners',

        'partners' => [
            [
                'label' => 'Activity Documentation',
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

        'copyright' => 'Al Mustaqbal. All Rights Reserved.',
    ],
];
