<?php

return [
    'hero' => [
        'title_highlight' => 'character, knowledge, and memorization',
        'slides' => [
            0 => [
                'type' => 'video',
                'media' => 'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4',
                'poster' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=2400&q=82',
                'media_alt' => 'Background video of a learning environment',
                'eyebrow' => 'Meaningful Learning',
                'title' => 'Rooted in the Qur’an. Moving Forward with Innovation.',
                'description' => 'Every learning experience is designed to encourage children to ask questions, create, collaborate, and grow with Islamic values as their guiding compass.',
                'cta' => [
                    'label' => 'Explore Our Programs',
                    'href' => '#program',
                    'action' => 'anchor',
                ],
                'focal_position' => 'center center',
                'overlay_strength' => 0.34,
                'source' => [
                    'name' => 'MDN CC0 sample video',
                    'url' => 'https://developer.mozilla.org/en-US/docs/Web/HTML/Element/video',
                ],
            ],
            1 => [
                'type' => 'image',
                'media' => config('media.static.hero_school'),
                'poster' => null,
                'media_alt' => 'Al Mustaqbal School building and surroundings',
                'eyebrow' => 'Al Mustaqbal School',
                'title' => 'Nurturing a Muslim Generation Ready to Fulfill Their Role as Khalifatullah',
                'description' => 'A holistic education that brings together the Qur’an, character, knowledge, languages, creativity, and the courage to benefit others.',
                'cta' => [
                    'label' => 'Explore Our Educational Vision',
                    'href' => '#visi-misi',
                    'action' => 'anchor',
                ],
                'focal_position' => 'center center',
                'overlay_strength' => 0.34,
                'source' => [
                    'name' => 'Al Mustaqbal School',
                    'url' => null,
                ],
            ],
            3 => [
                'type' => 'image',
                'media' => 'https://resources.finalsite.net/images/f_auto,q_auto,t_image_size_6/v1742884220/jiseduorg/vmaca0dvv3mnapr6y0b4/HSFModuleatNight.jpg',
                'poster' => null,
                'media_alt' => 'School architecture at night',
                'eyebrow' => 'Spaces That Bring Learning to Life',
                'title' => 'An Environment That Invites Children to Grow and Discover Their Potential',
                'description' => 'A meaningful learning environment is more than just a place. It is part of an experience that nurtures curiosity, courage, and a sense of belonging.',
                'cta' => [
                    'label' => 'Explore Our Facilities',
                    'href' => '#galeri',
                    'action' => 'anchor',
                ],
                'focal_position' => 'center center',
                'overlay_strength' => 0.34,
                'source' => [
                    'name' => 'Temporary Finalsite reference asset',
                    'url' => 'https://resources.finalsite.net/images/f_auto,q_auto,t_image_size_6/v1742884220/jiseduorg/vmaca0dvv3mnapr6y0b4/HSFModuleatNight.jpg',
                ],
            ],
        ],
    ],
    'navbar' => [
        'logo' => [
            'line_1' => 'Al Mustaqbal',
            'line_2' => 'School',
        ],
        'items' => [
            2 => [
                'label' => 'Facilities',
            ],
            5 => [
                'options' => [
                    ['locale' => 'id', 'label' => 'Indonesian', 'short' => 'ID'],
                    ['locale' => 'en', 'label' => 'English', 'short' => 'EN'],
                    ['locale' => 'ar', 'label' => 'Arabic', 'short' => 'AR'],
                ],
            ],
        ],
    ],
    'stats' => [
        'items' => [
            1 => ['label' => 'Prestigious Awards'],
        ],
    ],
    'quick_info' => [
        'items' => [
            0 => [
                'description' => 'School announcements, schedules, and grades are available in one place.',
            ],
            1 => [
                'title' => 'Extracurricular Activities',
            ],
        ],
    ],
    'visi_misi' => [
        'missions_intro' => [
            'title' => 'Five Al-Mustaqbal Graduate Quality Assurances',
        ],
        'missions' => [
            [
                'title' => 'Islamic Generation Rooted in the Qur’an & Sunnah',
                'accent' => '#22c55e',
                'text_parts' => [
                    ['text' => 'Nurturing an Islamic generation based on the '],
                    ['text' => 'Qur’an and the Sunnah of the Messenger of Allah', 'mark' => 'green'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'Academic Excellence & IT Literate',
                'accent' => '#0ea5e9',
                'text_parts' => [
                    ['text' => 'Building '],
                    ['text' => 'academic excellence', 'mark' => 'blue'],
                    ['text' => ' together with practical '],
                    ['text' => 'information-technology literacy', 'mark' => 'orange'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'Active Communication in Three Languages',
                'accent' => '#f97316',
                'text_parts' => [
                    ['text' => 'Developing active communication skills in '],
                    ['text' => 'Arabic, English, and Indonesian', 'mark' => 'orange'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'Passion for Reading & Writing',
                'accent' => '#a855f7',
                'text_parts' => [
                    ['text' => 'Cultivating a lasting '],
                    ['text' => 'passion for reading and writing', 'mark' => 'purple'],
                    ['text' => ' as part of everyday learning.'],
                ],
            ],
            [
                'title' => 'Environmentalist',
                'accent' => '#14b8a6',
                'text_parts' => [
                    ['text' => 'Developing '],
                    ['text' => 'environmental awareness', 'mark' => 'green'],
                    ['text' => ' and responsible habits in caring for the surrounding environment.'],
                ],
            ],
        ],
    ],
    'nilai_sekolah' => [
        'subtitle' => 'Q-III represents four character foundations at Al-Mustaqbal: Quranic, Inspiration, Innovation, and Integrity.',
        'aria_label' => 'Al-Mustaqbal Q-III values',
        'items' => [
            [
                'code' => 'Q',
                'title' => 'Quranic',
                'accent' => '#22c55e',
                'summary' => 'Rooted in the Qur’an and the Sunnah of the Messenger of Allah.',
                'text_parts' => [
                    ['text' => 'Rooted in the '],
                    ['text' => 'Qur’an and Sunnah', 'mark' => 'green'],
                    ['text' => ' as the foundation for learning, character, and growth.'],
                ],
            ],
            [
                'code' => 'I',
                'title' => 'Inspiration',
                'accent' => '#a855f7',
                'summary' => 'Growing the spirit to inspire and benefit the surrounding community.',
                'text_parts' => [
                    ['text' => 'Growing the spirit to '],
                    ['text' => 'inspire and bring benefit', 'mark' => 'purple'],
                    ['text' => ' to the people and environment around us.'],
                ],
            ],
            [
                'code' => 'I',
                'title' => 'Innovation',
                'accent' => '#f97316',
                'summary' => 'Thinking critically, creatively, and courageously trying new ideas.',
                'text_parts' => [
                    ['text' => 'Encouraging students to think '],
                    ['text' => 'critically, creatively, and courageously try new ideas', 'mark' => 'orange'],
                    ['text' => ' throughout the learning process.'],
                ],
            ],
            [
                'code' => 'I',
                'title' => 'Integrity',
                'accent' => '#0ea5e9',
                'summary' => 'Honest, trustworthy, and grounded in noble character.',
                'text_parts' => [
                    ['text' => 'Building a character that is '],
                    ['text' => 'honest, trustworthy, and morally grounded', 'mark' => 'blue'],
                    ['text' => ' in words and actions.'],
                ],
            ],
        ],
    ],
    'galeri' => [
        'source' => 'facilities',
        'section_title' => 'OUR FACILITIES',
        'section_subtitle' => 'Spaces and support services designed to strengthen learning, worship, wellbeing, physical activity, and partnership with families.',
        'title' => 'Al Mustaqbal School Facilities',
        'subtitle' => 'Our facilities support academics, technology, worship, health, movement, and meaningful parent involvement.',
        'aria_label' => 'Al Mustaqbal School facilities list',
        'cta' => [
            'label' => '',
            'href' => '',
        ],
        'items' => [
            [
                'title' => 'Multimedia Facilities & IT Lab',
                'caption' => 'Multimedia tools and technology spaces supporting digital literacy and IT-enabled learning.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'Mushallah',
                'caption' => 'A dedicated worship space supporting congregational prayer and students’ Islamic routines.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'Multifunction Hall',
                'caption' => 'A shared venue for school activities, presentations, meetings, and community events.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'Swimming Pool',
                'caption' => 'An aquatic facility supporting swimming practice, fitness, confidence, and water skills.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'Library & Laboratory',
                'caption' => 'Spaces for reading, experimenting, observing, and building independent learning habits.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'Psychological Counseling',
                'caption' => 'Psychological support that helps children’s development and strengthens school-family communication.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'Parents Class',
                'caption' => 'A learning space for parents to align support for children at school and at home.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'Dental Health Care',
                'caption' => 'Education and support for dental health as part of healthy daily habits.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'Full Day School Service',
                'caption' => 'Structured and supervised support for children across a longer, safe, and purposeful school day.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1800&q=82',
            ],
        ],
    ],
    'footer' => [
        'partners' => [
            0 => ['label' => 'Activity Documentation'],
            1 => ['label' => 'SDIT Al Mustaqbal'],
            2 => ['label' => 'DT Peduli'],
            3 => ['label' => 'F1 Club Taekwondo Malang'],
            4 => ['label' => 'Digido'],
        ],
    ],
];
