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
                    'label' => 'استكشف مرافق المدرسة',
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
            2 => [
                'label' => 'المرافق',
            ],
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
    'visi_misi' => [
        'missions_intro' => [
            'title' => 'خمسة ضمانات لجودة خريجي مدرسة المستقبل',
        ],
        'missions' => [
            [
                'title' => 'جيل إسلامي على القرآن والسنة',
                'accent' => '#22c55e',
                'text_parts' => [
                    ['text' => 'تنشئة جيل إسلامي يستند إلى '],
                    ['text' => 'القرآن الكريم وسنة رسول الله صلى الله عليه وسلم', 'mark' => 'green'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'التميز الأكاديمي والثقافة التقنية',
                'accent' => '#0ea5e9',
                'text_parts' => [
                    ['text' => 'بناء '],
                    ['text' => 'التميز الأكاديمي', 'mark' => 'blue'],
                    ['text' => ' وتنمية '],
                    ['text' => 'الثقافة والمهارات الأساسية في تقنية المعلومات', 'mark' => 'orange'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'التواصل الفعّال بثلاث لغات',
                'accent' => '#f97316',
                'text_parts' => [
                    ['text' => 'تنمية القدرة على التواصل الفعّال باللغات '],
                    ['text' => 'العربية والإنجليزية والإندونيسية', 'mark' => 'orange'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'الشغف بالقراءة والكتابة',
                'accent' => '#a855f7',
                'text_parts' => [
                    ['text' => 'غرس '],
                    ['text' => 'حب القراءة والكتابة', 'mark' => 'purple'],
                    ['text' => ' وتحويلهما إلى عادة مستمرة في التعلم.'],
                ],
            ],
            [
                'title' => 'العناية بالبيئة',
                'accent' => '#14b8a6',
                'text_parts' => [
                    ['text' => 'تنمية '],
                    ['text' => 'الوعي البيئي', 'mark' => 'green'],
                    ['text' => ' والعادات المسؤولة في المحافظة على البيئة المحيطة.'],
                ],
            ],
        ],
    ],
    'nilai_sekolah' => [
        'subtitle' => 'تمثل Q-III أربع ركائز للشخصية في مدرسة المستقبل: القرآن، والإلهام، والابتكار، والنزاهة.',
        'aria_label' => 'قيم Q-III في مدرسة المستقبل',
        'items' => [
            [
                'code' => 'Q',
                'title' => 'قرآني (Quranic)',
                'accent' => '#22c55e',
                'summary' => 'راسخ في القرآن وسنة رسول الله صلى الله عليه وسلم.',
                'text_parts' => [
                    ['text' => 'الاستناد إلى '],
                    ['text' => 'القرآن والسنة', 'mark' => 'green'],
                    ['text' => ' أساسًا للتعلم والسلوك والنمو.'],
                ],
            ],
            [
                'code' => 'I',
                'title' => 'الإلهام (Inspiration)',
                'accent' => '#a855f7',
                'summary' => 'تنمية روح الإلهام والنفع للمحيط.',
                'text_parts' => [
                    ['text' => 'تنمية الرغبة في '],
                    ['text' => 'الإلهام وتقديم النفع', 'mark' => 'purple'],
                    ['text' => ' للمجتمع والبيئة المحيطة.'],
                ],
            ],
            [
                'code' => 'I',
                'title' => 'الابتكار (Innovation)',
                'accent' => '#f97316',
                'summary' => 'تفكير نقدي وإبداعي مع الشجاعة لتجربة الجديد.',
                'text_parts' => [
                    ['text' => 'تشجيع التفكير '],
                    ['text' => 'النقدي والإبداعي والشجاعة في تجربة أفكار جديدة', 'mark' => 'orange'],
                    ['text' => ' أثناء التعلم.'],
                ],
            ],
            [
                'code' => 'I',
                'title' => 'النزاهة (Integrity)',
                'accent' => '#0ea5e9',
                'summary' => 'صدق وأمانة وأخلاق كريمة.',
                'text_parts' => [
                    ['text' => 'بناء شخصية تتصف بـ '],
                    ['text' => 'الصدق والأمانة ومكارم الأخلاق', 'mark' => 'blue'],
                    ['text' => ' في القول والعمل.'],
                ],
            ],
        ],
    ],
    'galeri' => [
        'source' => 'facilities',
        'section_title' => 'مرافقنا',
        'section_subtitle' => 'مساحات وخدمات مساندة صُممت لدعم التعلم والعبادة والصحة والنشاط البدني والشراكة مع الأسرة.',
        'title' => 'مرافق مدرسة المستقبل',
        'subtitle' => 'تدعم مرافقنا التعلم الأكاديمي والتقنية والعبادة والصحة والحركة ومشاركة الوالدين.',
        'aria_label' => 'قائمة مرافق مدرسة المستقبل',
        'cta' => [
            'label' => '',
            'href' => '',
        ],
        'items' => [
            [
                'title' => 'مرافق الوسائط المتعددة ومختبر تقنية المعلومات',
                'caption' => 'أدوات ومساحات تقنية تدعم الثقافة الرقمية والتعلم المعتمد على تقنية المعلومات.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'المصلى',
                'caption' => 'مساحة للعبادة تدعم الصلاة جماعة والعادات الإسلامية اليومية للطلاب.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'قاعة متعددة الأغراض',
                'caption' => 'قاعة للأنشطة المدرسية والعروض والاجتماعات والفعاليات المجتمعية.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'المسبح',
                'caption' => 'مرفق مائي يدعم تدريب السباحة واللياقة والثقة ومهارات السلامة في الماء.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'المكتبة والمختبر',
                'caption' => 'مساحات للقراءة والتجربة والملاحظة وبناء عادات التعلم المستقل.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'الإرشاد النفسي',
                'caption' => 'دعم نفسي يساعد نمو الطفل ويعزز التواصل بين المدرسة والأسرة.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'صف الوالدين',
                'caption' => 'مساحة تعلم للوالدين لتوحيد أساليب دعم الطفل في المدرسة والمنزل.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'رعاية صحة الأسنان',
                'caption' => 'توعية ودعم لصحة الأسنان ضمن العادات اليومية الصحية.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1800&q=82',
            ],
            [
                'title' => 'خدمة الدوام المدرسي الكامل',
                'caption' => 'رعاية منظمة وآمنة للطفل خلال يوم مدرسي أطول وأكثر تكاملًا.',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1800&q=82',
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
