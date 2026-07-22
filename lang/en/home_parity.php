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
                'media' => 'media/home/hero-school.png',
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
                    'label' => 'Discover School Life',
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
