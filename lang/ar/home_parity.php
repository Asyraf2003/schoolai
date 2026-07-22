<?php

return [
    'hero' => [
        'title_highlight' => 'الأدب والعلم والحفظ',
        'slides' => [
            0 => [
                'type' => 'video',
                'media' => 'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4',
                'poster' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=2400&q=82',
                'media_alt' => 'فيديو خلفي لأجواء التعلم',
                'eyebrow' => 'تعلّم هادف',
                'title' => 'راسخون في القرآن، ومنطلقون بالابتكار.',
                'description' => 'نصمم كل تجربة تعليمية لتشجع الطفل على التساؤل والإبداع والتعاون والنمو، مسترشدًا بالقيم الإسلامية في كل خطوة.',
                'cta' => [
                    'label' => 'استكشف برامجنا',
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
                'media_alt' => 'مبنى ومرافق مدرسة المستقبل',
                'eyebrow' => 'مدرسة المستقبل',
                'title' => 'نُنشّئ جيلاً مسلمًا مستعدًا للقيام بدوره خليفةً لله في الأرض',
                'description' => 'تعليم شمولي يجمع بين القرآن والأدب والعلم واللغات والإبداع والشجاعة، ليكون الطفل نافعًا لنفسه ولمجتمعه.',
                'cta' => [
                    'label' => 'تعرّف على رؤيتنا التعليمية',
                    'href' => '#visi-misi',
                    'action' => 'anchor',
                ],
                'focal_position' => 'center center',
                'overlay_strength' => 0.34,
                'source' => [
                    'name' => 'مدرسة المستقبل',
                    'url' => null,
                ],
            ],

            3 => [
                'type' => 'image',
                'media' => 'https://resources.finalsite.net/images/f_auto,q_auto,t_image_size_6/v1742884220/jiseduorg/vmaca0dvv3mnapr6y0b4/HSFModuleatNight.jpg',
                'poster' => null,
                'media_alt' => 'العمارة المدرسية ليلاً',
                'eyebrow' => 'مساحات تُحيي تجربة التعلم',
                'title' => 'بيئة تحتضن نمو الطفل وتدعوه إلى اكتشاف إمكاناته',
                'description' => 'بيئة التعلم الجيدة ليست مجرد مكان، بل هي جزء من تجربة تنمّي الفضول والشجاعة وروح الانتماء والتعاون.',
                'cta' => [
                    'label' => 'اكتشف الحياة المدرسية',
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
            'line_1' => 'مدرسة',
            'line_2' => 'المستقبل',
        ],
        'items' => [
            5 => [
                'options' => [
                    ['locale' => 'id', 'label' => 'الإندونيسية', 'short' => 'ID'],
                    ['locale' => 'en', 'label' => 'الإنجليزية', 'short' => 'EN'],
                    ['locale' => 'ar', 'label' => 'العربية', 'short' => 'AR'],
                ],
            ],
        ],
    ],

    'stats' => [
        'items' => [
            1 => ['label' => 'جوائز مرموقة'],
        ],
    ],

    'quick_info' => [
        'items' => [
            0 => [
                'description' => 'تتوفر إعلانات المدرسة والجداول والدرجات في مكان واحد.',
            ],
            1 => [
                'title' => 'الأنشطة اللامنهجية',
            ],
        ],
    ],

    'footer' => [
        'partners' => [
            0 => ['label' => 'توثيق الأنشطة'],
            1 => ['label' => 'مدرسة المستقبل الابتدائية الإسلامية المتكاملة'],
            2 => ['label' => 'DT Peduli'],
            3 => ['label' => 'F1 Club Taekwondo Malang'],
            4 => ['label' => 'Digido'],
        ],
    ],
];
