<?php

return [
    'meta' => [
        'title' => 'مدرسة المستقبل',
        'description' => 'الموقع الرسمي لمدرسة المستقبل، ويضم معلومات عن التعليم والتسجيل والبرامج والأنشطة ووسائل التواصل مع المدرسة.',
    ],

    'accessibility' => [
        'skip_to_content' => 'الانتقال مباشرة إلى المحتوى الرئيسي',
    ],

    'hero' => [
        'section_label' => 'أبرز ما في مدرسة المستقبل',
        'carousel_roledescription' => 'عارض شرائح',
        'slide_roledescription' => 'شريحة',
        'slide_label' => 'الشريحة :current من :total',
        'dots_label' => 'اختر إحدى الشرائح',
        'previous_label' => 'عرض الشريحة السابقة',
        'next_label' => 'عرض الشريحة التالية',
        'pause_label' => 'إيقاف العرض التلقائي مؤقتًا',
        'play_label' => 'تشغيل العرض التلقائي',
        'autoplay_interval' => 7200,
        'fallback_image' => 'media/home/hero-school.png',
        'fallback_image_alt' => 'بيئة مدرسة المستقبل',
        'fallback_title' => 'تنشئة جيل مسلم مستعد للقيام بدوره خليفةً لله في الأرض',
        'fallback_description' => 'تعليم شمولي قرآني يقوم على الابتكار والإلهام والنزاهة.',
        'slides' => [
            [
                'type' => 'image',
                'media' => 'media/home/hero-school.png',
                'poster' => null,
                'media_alt' => 'مبنى ومرافق مدرسة المستقبل',
                'eyebrow' => 'مدرسة المستقبل',
                'title' => 'نُنشّئ جيلاً مسلمًا مستعدًا للقيام بدوره خليفةً لله في الأرض',
                'description' => 'تعليم شمولي يجمع بين القرآن والأدب والعلم واللغات والإبداع والشجاعة ليكون الطفل نافعًا لنفسه ولمجتمعه.',
                'cta' => [
                    'label' => 'تعرّف على توجهنا التعليمي',
                    'href' => '#visi-misi',
                    'action' => 'anchor',
                ],
                'focal_position' => 'center center',
                'overlay_strength' => 0.46,
                'source' => [
                    'name' => 'مدرسة المستقبل',
                    'url' => null,
                ],
            ],
            [
                'type' => 'video',
                'media' => null,
                'poster' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=2400&q=82',
                'media_alt' => 'أطفال يتعلمون معًا داخل الفصل',
                'eyebrow' => 'تعلّم هادف',
                'title' => 'راسخون في القرآن، ومنطلقون بالابتكار.',
                'description' => 'نصمم كل تجربة تعليمية لتشجع الطفل على التساؤل والإبداع والتعاون والنمو، مسترشدًا بالقيم الإسلامية في كل خطوة.',
                'cta' => [
                    'label' => 'استكشف برامجنا',
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
                'media_alt' => 'طفل يتعلم ويكتب بتركيز',
                'eyebrow' => 'قرآنية · ملهمة · مبتكرة · نزاهة',
                'title' => 'كل رحلة عظيمة تبدأ من البيئة المناسبة',
                'description' => 'تنمو المدرسة والأسرة معًا كفريق واحد لإعداد الطفل لمواجهة العالم دون أن يفقد إيمانه وهويته.',
                'cta' => [
                    'label' => 'ابدأ رحلة التسجيل',
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
        'aria_label' => 'القائمة الرئيسية',
        'logo' => [
            'href' => '#beranda',
            'icon' => '🌈',
            'image' => 'media/home/logo-nav.webp',
            'image_alt' => 'شعار مدرسة المستقبل',
            'line_1' => 'مدرسة',
            'line_2' => 'المستقبل',
        ],
        'items' => [
            [
                'label' => 'الرئيسية',
                'href' => '#beranda',
            ],
            [
                'label' => 'التعليم',
                'href' => '#program',
                'mega' => [
                    'toggle_label' => 'فتح قائمة التعليم',
                    'eyebrow' => 'تعليم شمولي',
                    'title' => 'مسار للنمو في الإيمان والعلم والشخصية.',
                    'description' => 'تعرّف على توجه مدرسة المستقبل التعليمي وتجارب التعلم التي نبنيها بالشراكة مع الأسرة.',
                    'links' => [
                        [
                            'label' => 'الرؤية والرسالة',
                            'description' => 'التوجه العام للتعليم في مدرسة المستقبل.',
                            'href' => '#visi-misi',
                        ],
                        [
                            'label' => 'قيم QIII',
                            'description' => 'قرآنية، ملهمة، مبتكرة، وقائمة على النزاهة.',
                            'href' => '#nilai',
                        ],
                        [
                            'label' => 'البرامج المميزة',
                            'description' => 'تجارب التعلم الأساسية لكل طفل.',
                            'href' => '#program',
                        ],
                        [
                            'label' => 'معلومات التسجيل والقبول',
                            'description' => 'الخطوات الأولى للانضمام إلى مدرسة المستقبل.',
                            'href' => '/ppdb',
                        ],
                    ],
                ],
            ],
            [
                'label' => 'المعرض',
                'href' => '#galeri',
            ],
            [
                'label' => 'المقالات',
                'href' => '#artikel',
            ],
            [
                'label' => 'تواصل معنا',
                'href' => '#kontak',
            ],
            [
                'label' => 'اللغة',
                'href' => '#',
                'type' => 'language',
                'badge' => 'ID',
                'options' => [
                    [
                        'locale' => 'id',
                        'label' => 'الإندونيسية',
                        'short' => 'ID',
                    ],
                    [
                        'locale' => 'en',
                        'label' => 'الإنجليزية',
                        'short' => 'EN',
                    ],
                ],
            ],
        ],
        'cta' => [
            'label' => 'سجّل الآن',
            'href' => '/ppdb',
        ],
        'mobile_open_label' => 'فتح القائمة',
    ],

    'about_stats_story' => [
        'section_label' => 'عن مدرسة المستقبل',
        'board_title' => 'مدرسة المستقبل',
        'headline_line_one' => 'أفكار جريئة،',
        'headline_line_two' => 'نحوّلها إلى واقع',
        'description' => 'ترافق مدرسة المستقبل نمو الطفل من خلال تعليم شمولي يجمع بين القرآن والأدب والعلم واللغات والإبداع والشجاعة؛ لتنشئة جيل مسلم ملهم ومبتكر وقائم على النزاهة، مستعد للقيام بدوره خليفةً لله في الأرض.',
        'cta' => 'نهجنا التعليمي',
        'media_label' => 'اكتشف رحلتنا',
        'media_alt' => 'رحلة التعلّم في مدرسة المستقبل',
    ],

    'stats' => [
        'items' => [
            [
                'count' => 320,
                'suffix' => '+',
                'label' => 'طالبًا',
            ],
            [
                'count' => 24,
                'suffix' => '+',
                'label' => 'جائزة مرموقة',
            ],
            [
                'count' => 1200,
                'suffix' => '+',
                'label' => 'ساعة',
            ],
            [
                'count' => 18,
                'suffix' => '+',
                'label' => 'برنامجًا',
            ],
        ],
    ],

    'quick_info' => [
        'items' => [
            [
                'icon' => '📚',
                'background' => 'var(--color-yellow-soft)',
                'title' => 'المعلومات الأكاديمية',
                'description' => 'تتوفر إعلانات المدرسة والجداول والدرجات في مكان واحد.',
            ],
            [
                'icon' => '🎨',
                'background' => 'var(--color-mint-soft)',
                'title' => 'الأنشطة اللامنهجية',
                'description' => 'استكشف أنشطة الطلاب التي تساعدهم على تنمية اهتماماتهم ومواهبهم.',
            ],
            [
                'icon' => '📝',
                'background' => 'var(--color-blue-soft)',
                'title' => 'التسجيل الإلكتروني',
                'description' => 'يمكن إتمام تسجيل الطلاب الجدد بسهولة أكبر عبر الإنترنت.',
            ],
            [
                'icon' => '🎒',
                'background' => 'var(--color-pink-soft)',
                'title' => 'المراحل التعليمية',
                'description' => 'تتوفر برامج المدرسة الابتدائية الإسلامية والروضة وتحفيظ القرآن ومجموعة اللعب.',
            ],
        ],
    ],

    'ppdb' => [
        'title' => 'سجّل الآن في مدرسة المستقبل',
        'description' => 'أصبح تسجيل الطلاب الجدد أكثر سهولة، وتتوفر المعلومات الأكاديمية والأنشطة ومراحل التسجيل في مكان واحد.',
        'cta' => [
            'label' => 'سجّل الآن',
            'href' => '/ppdb',
        ],
        'steps' => [
            [
                'number' => '1',
                'title' => 'تعبئة الاستمارة',
                'description' => 'أكمل بيانات الطالب وولي الأمر عبر الإنترنت.',
            ],
            [
                'number' => '2',
                'title' => 'التحقق من البيانات',
                'description' => 'يراجع فريق الإدارة اكتمال البيانات ومستندات التسجيل.',
            ],
            [
                'number' => '3',
                'title' => 'تأكيد التسجيل',
                'description' => 'يتلقى أولياء الأمور المعلومات اللاحقة من إدارة المدرسة.',
            ],
            [
                'number' => '4',
                'title' => 'إعلان النتيجة',
                'description' => 'يتم إبلاغ نتائج التسجيل عبر وسائل التواصل الرسمية للمدرسة.',
            ],
        ],
    ],

    'visi_misi' => [
        'section_title' => 'التوجه التعليمي ورؤية المدرسة ورسالتها',

        'section_subtitle' => 'يلخص هذا القسم التوجه العام للتعليم في مدرسة المستقبل: الإيمان والعلم والأدب واللغات والاستقلالية وبناء شخصية الطفل بصورة متكاملة.',

        'vision' => [
            'title' => 'التوجه التعليمي لمدرسة المستقبل',
            'text_parts' => [
                ['text' => 'يهدف البرنامج التعليمي في مدرسة المستقبل إلى تنشئة '],
                ['text' => 'جيل مسلم', 'mark' => 'blue'],
                ['text' => ' يتمتع بشخصية '],
                ['text' => 'ملهمة ومبتكرة وقائمة على النزاهة', 'mark' => 'orange'],
                ['text' => '، وقادر على القيام بدوره بوصفه '],
                ['text' => 'خليفةً لله في الأرض', 'mark' => 'green'],
                ['text' => ' من خلال تطوير '],
                ['text' => 'تعليم شمولي متكامل.', 'mark' => 'purple'],
            ],
        ],

        'missions_intro' => [
            'title' => 'أربعة مسارات لبناء جيل مسلم متكامل',
        ],

        'missions' => [
            [
                'title' => 'أساس القرآن والسنة',
                'accent' => '#22c55e',
                'text_parts' => [
                    ['text' => 'تنشئة جيل مسلم يستند إلى '],
                    ['text' => 'القرآن الكريم وسنة رسول الله ﷺ', 'mark' => 'green'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'رؤية عالمية وإتقان اللغات',
                'accent' => '#0ea5e9',
                'text_parts' => [
                    ['text' => 'تنمية روح الإلهام من خلال '],
                    ['text' => 'رؤية عالمية', 'mark' => 'blue'],
                    ['text' => ' والقدرة على التواصل الاجتماعي من خلال إتقان '],
                    ['text' => 'أكثر من لغة', 'mark' => 'orange'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'متعلمين ناقدين ومبتكرين',
                'accent' => '#f97316',
                'text_parts' => [
                    ['text' => 'إعداد جيل من '],
                    ['text' => 'المتعلمين مدى الحياة', 'mark' => 'purple'],
                    ['text' => ' القادرين على التفكير '],
                    ['text' => 'النقدي والابتكاري', 'mark' => 'orange'],
                    ['text' => '.'],
                ],
            ],
            [
                'title' => 'وعي بيئي ونزاهة',
                'accent' => '#14b8a6',
                'text_parts' => [
                    ['text' => 'بناء شخصية تتصف بـ '],
                    ['text' => 'الوعي والمسؤولية تجاه البيئة', 'mark' => 'green'],
                    ['text' => ' وتتمسك بـ '],
                    ['text' => 'النزاهة', 'mark' => 'blue'],
                    ['text' => ' على المستويين المحلي والعالمي.'],
                ],
            ],
        ],
    ],

    'nilai_sekolah' => [
        'title' => 'قيم ترسم مسار نمو الطفل',
        'subtitle' => 'أربع قيم أساسية تشكل ثقافة التعلم في مدرسة المستقبل: الارتباط بالقرآن، والشجاعة في التفكير، والتكامل في بناء الشخصية، والقدرة على إلهام الآخرين.',
        'aria_label' => 'قيم مدرسة المستقبل',
        'items' => [
            [
                'code' => 'Q',
                'title' => 'قرآنية',
                'accent' => '#22c55e',
                'summary' => 'راسخة في القرآن والسنة.',
                'text_parts' => [
                    ['text' => 'بناء شخصية الطلاب على أساس '],
                    ['text' => 'القرآن الكريم وسنة رسول الله ﷺ', 'mark' => 'green'],
                    ['text' => ' في عادات التعلم والحياة اليومية.'],
                ],
            ],
            [
                'code' => 'I',
                'title' => 'ابتكارية',
                'accent' => '#f97316',
                'summary' => 'الشجاعة في التفكير وتجربة حلول جديدة.',
                'text_parts' => [
                    ['text' => 'تشجيع الطلاب على التفكير بصورة '],
                    ['text' => 'نقدية وإبداعية ومنفتحة', 'mark' => 'orange'],
                    ['text' => ' تجاه تجارب التعلم الجديدة.'],
                ],
            ],
            [
                'code' => 'G',
                'title' => 'تكاملية',
                'accent' => '#0ea5e9',
                'summary' => 'العلم والأدب واللغات والوعي الاجتماعي تسير معًا.',
                'text_parts' => [
                    ['text' => 'الربط بين '],
                    ['text' => 'العلم والشخصية واللغات والوعي الاجتماعي', 'mark' => 'blue'],
                    ['text' => ' ضمن تجربة تعليمية متكاملة.'],
                ],
            ],
            [
                'code' => 'N',
                'title' => 'ملهمة',
                'accent' => '#a855f7',
                'summary' => 'النمو ليصبح الطفل شخصًا نافعًا لغيره.',
                'text_parts' => [
                    ['text' => 'تنمية الرغبة في أن يصبح الطالب شخصًا '],
                    ['text' => 'نافعًا وملهمًا', 'mark' => 'purple'],
                    ['text' => ' لمن حوله.'],
                ],
            ],
        ],
    ],

    'program_unggulan' => [
        'section_title' => 'البرامج التعليمية في مدرسة المستقبل',

        'section_subtitle' => 'صُممت المراحل والبرامج التعليمية بصورة متدرجة، من الطفولة المبكرة حتى يصبح الطفل قادرًا على الاستقلال أكاديميًا واجتماعيًا وقرآنيًا.',

        'title' => 'رحلة تعلم تبدأ من الطفولة المبكرة نحو الاستقلال',
        'subtitle' => 'صُممت برامج مدرسة المستقبل لتعزيز الأدب والتحصيل الأكاديمي والارتباط بالقرآن واللغات والثقافة القرائية واستقلالية الطفل بصورة متدرجة.',
        'chips_aria_label' => 'ملخص البرامج',
        'flow_aria_label' => 'قائمة البرامج المميزة',

        'chips' => [
            '6 برامج',
            'الروضة والابتدائية',
            'قرآنية',
        ],

        'items' => [
            [
                'code' => 'KB',
                'label' => 'الطفولة المبكرة',
                'title' => 'مجموعة اللعب',
                'accent' => '#a855f7',
                'summary' => 'لعب موجّه يساعد الطفل على بناء الشعور بالأمان والمهارات الاجتماعية والاستقلالية.',
                'text_parts' => [
                    ['text' => 'أنشطة لعب دافئة وهادفة لتنمية '],
                    ['text' => 'الاستقلالية والمهارات الاجتماعية والفضول', 'mark' => 'purple'],
                    ['text' => ' منذ السنوات الأولى.'],
                ],
            ],
            [
                'code' => 'TK',
                'label' => 'مرحلة التأسيس',
                'title' => 'رياض الأطفال',
                'accent' => '#f97316',
                'summary' => 'تعلم نشط يتناسب مع مراحل نمو الطفل.',
                'text_parts' => [
                    ['text' => 'تعليم في مرحلة الطفولة المبكرة يتسم بأنه '],
                    ['text' => 'دافئ ونشط ومتدرج', 'mark' => 'orange'],
                    ['text' => ' ليكون الطفل مستعدًا للمرحلة الابتدائية.'],
                ],
            ],
            [
                'code' => 'SD',
                'label' => 'تعليم أكاديمي قرآني',
                'title' => 'المدرسة الابتدائية الإسلامية المتكاملة',
                'accent' => '#0ea5e9',
                'summary' => 'تعليم ابتدائي إسلامي متكامل يعزز العلم والشخصية.',
                'text_parts' => [
                    ['text' => 'برنامج للتعليم الابتدائي الإسلامي المتكامل يركز على تعزيز '],
                    ['text' => 'التحصيل الأكاديمي والأدب والشخصية الإسلامية', 'mark' => 'blue'],
                    ['text' => '.'],
                ],
            ],
            [
                'code' => 'TQ',
                'label' => 'الحفظ والأدب',
                'title' => 'تحفيظ القرآن الكريم',
                'accent' => '#22c55e',
                'summary' => 'تحفيظ القرآن بالتوازي مع ترسيخ الأدب والعادات الحسنة.',
                'text_parts' => [
                    ['text' => 'برنامج لتحفيظ القرآن الكريم مع ترسيخ '],
                    ['text' => 'الأدب والمراجعة المستمرة والقرب من القرآن', 'mark' => 'green'],
                    ['text' => '.'],
                ],
            ],
            [
                'code' => 'MB',
                'label' => 'العربية والإنجليزية',
                'title' => 'شريك اللغة',
                'accent' => '#14b8a6',
                'summary' => 'تتحول اللغة إلى أداة لبناء الثقة والتواصل العالمي.',
                'text_parts' => [
                    ['text' => 'تعليم اللغتين العربية والإنجليزية لبناء '],
                    ['text' => 'مهارات التواصل العالمي', 'mark' => 'green'],
                    ['text' => ' بصورة متدرجة.'],
                ],
            ],
            [
                'code' => 'LT',
                'label' => 'ثقافة القراءة',
                'title' => 'الثقافة القرائية والمكتبة',
                'accent' => '#eab308',
                'summary' => 'مساحة معرفية تنمي حب القراءة والتعلم المستقل.',
                'text_parts' => [
                    ['text' => 'بيئة معرفية تشجع على '],
                    ['text' => 'حب القراءة وعادات التعلم المستقل', 'mark' => 'yellow'],
                    ['text' => '.'],
                ],
            ],
        ],

        'empty' => 'لا توجد برامج مميزة متاحة حتى الآن',
    ],

    'galeri' => [
        'section_title' => 'معرض أنشطة المدرسة',

        'section_subtitle' => 'لمحات موثقة من أنشطة التعلم والعبادة والإبداع وروح التعاون بين الطلاب داخل المدرسة.',

        'title' => 'أحدث لحظات مدرسة المستقبل',
        'subtitle' => 'توثيق لأنشطة التعلم والعبادة والإبداع والتعاون بين الطلاب. يستخدم المعرض حاليًا بيانات تُدار يدويًا وروابط إنستغرام، ولم يتم ربطه بواجهة API بعد.',
        'aria_label' => 'قائمة أحدث لحظات المعرض',
        'visual_aria_label' => 'معاينة وسائط المعرض',
        'open_media_prefix' => 'فتح الوسائط',
        'fallback_item_label' => 'المعرض',
        'lightbox_label' => 'وسائط معرض الصفحة الرئيسية',
        'close_label' => 'إغلاق',
        'video_title' => 'فيديو من المعرض',
        'default_type_label' => 'صورة',

        'cta' => [
            'label' => 'عرض جميع محتويات المعرض',
            'href' => '/galeri',
        ],

        'filters' => [
            [
                'label' => 'الكل',
                'value' => 'semua',
            ],
            [
                'label' => 'الصور',
                'value' => 'photo',
            ],
            [
                'label' => 'فيديو / ريل',
                'value' => 'video',
            ],
        ],

        'items' => [
            [
                'title' => 'يوم السوق لطلاب المرحلة الابتدائية الإسلامية',
                'type' => 'reel',
                'instagram_url' => 'https://www.instagram.com/',
                'thumbnail' => '',
                'published_at' => '2026-07-01',
                'date' => '1 يوليو 2026',
                'caption' => 'يتعلم الطلاب الثقة بالنفس والحساب البسيط والتفاعل مع الآخرين من خلال أنشطة يوم السوق.',
                'category' => 'أنشطة الطلاب',
                'variant' => 'feature',
                'accent' => '#f97316',
                'fallback_icon' => '🛍️',
                'item_class' => 'galeri-item--tall',
                'g1' => 'var(--color-orange)',
                'g2' => 'var(--color-yellow)',
                'emoji' => '🛍️🎥',
            ],
            [
                'title' => 'تحفيظ القرآن صباحًا مع المعلمين',
                'type' => 'video',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-06-27',
                'date' => '27 يونيو 2026',
                'caption' => 'أجواء من المراجعة وتحفيظ القرآن صباحًا لترسيخ القرب من كتاب الله.',
                'category' => 'تحفيظ القرآن',
                'variant' => 'tall',
                'accent' => '#22c55e',
                'fallback_icon' => '🕌',
                'item_class' => 'galeri-item--tall',
                'g1' => 'var(--color-mint)',
                'g2' => 'var(--color-blue)',
                'emoji' => '🕌🎬',
            ],
            [
                'title' => 'تجربة علمية للأطفال',
                'type' => 'photo',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-06-22',
                'date' => '22 يونيو 2026',
                'caption' => 'يتعلم الأطفال الملاحظة والتجربة والاستنتاج من خلال تجارب علمية بسيطة.',
                'category' => 'العلوم',
                'variant' => 'wide',
                'accent' => '#0ea5e9',
                'fallback_icon' => '🔬',
                'item_class' => '',
                'g1' => 'var(--color-blue)',
                'g2' => 'var(--color-purple)',
                'emoji' => '🔬📸',
            ],
            [
                'title' => 'أعمال فنية زاهية بالألوان',
                'type' => 'photo',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-06-18',
                'date' => '18 يونيو 2026',
                'caption' => 'توثيق لأعمال الطلاب الفنية التي تنمي الخيال والإحساس بالألوان والقدرة على التعبير عن الذات.',
                'category' => 'الفنون',
                'variant' => 'normal',
                'accent' => '#ec4899',
                'fallback_icon' => '🎨',
                'item_class' => '',
                'g1' => 'var(--color-pink)',
                'g2' => 'var(--color-purple)',
                'emoji' => '🎨✨',
            ],
            [
                'title' => 'نادي الإنجليزية الممتع',
                'type' => 'reel',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-06-12',
                'date' => '12 يونيو 2026',
                'caption' => 'أنشطة خفيفة وممتعة باللغة الإنجليزية من خلال الألعاب والمفردات والمحادثات البسيطة.',
                'category' => 'اللغات',
                'variant' => 'wide',
                'accent' => '#14b8a6',
                'fallback_icon' => '🌍',
                'item_class' => '',
                'g1' => 'var(--color-mint)',
                'g2' => 'var(--color-blue)',
                'emoji' => '🌍🎥',
            ],
            [
                'title' => 'الأنشطة الخارجية والعمل الجماعي',
                'type' => 'photo',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-06-05',
                'date' => '5 يونيو 2026',
                'caption' => 'أنشطة خارج الفصل لتنمية الشجاعة والاستقلالية والعمل الجماعي لدى الطلاب.',
                'category' => 'أنشطة خارجية',
                'variant' => 'tall',
                'accent' => '#eab308',
                'fallback_icon' => '🏕️',
                'item_class' => 'galeri-item--tall',
                'g1' => 'var(--color-yellow)',
                'g2' => 'var(--color-orange)',
                'emoji' => '🏕️📸',
            ],
            [
                'title' => 'عرض إبداعات الطلاب',
                'type' => 'video',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-05-29',
                'date' => '29 مايو 2026',
                'caption' => 'لحظات يعتلي فيها الطلاب المسرح من خلال أنشطة الفن واللغة التي تنمي الجرأة والثقة بالنفس.',
                'category' => 'فعاليات المدرسة',
                'variant' => 'feature',
                'accent' => '#a855f7',
                'fallback_icon' => '🎭',
                'item_class' => '',
                'g1' => 'var(--color-purple)',
                'g2' => 'var(--color-pink)',
                'emoji' => '🎭🎬',
            ],
            [
                'title' => 'الثقافة القرائية والمكتبة',
                'type' => 'photo',
                'instagram_url' => '',
                'thumbnail' => '',
                'published_at' => '2026-05-20',
                'date' => '20 مايو 2026',
                'caption' => 'يتعرف الطلاب على الكتب والقصص وعادات القراءة في أجواء مريحة ومحفزة.',
                'category' => 'الثقافة القرائية',
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
        'title' => 'قصص مدرسية تقرّب أولياء الأمور من حياة أبنائهم',
        'subtitle' => 'أخبار موجزة، ورؤى تربوية، وقصص من أنشطة الأطفال بصياغة سهلة تساعد أولياء الأمور على فهم أهم المعلومات بسرعة.',
        'read_more' => 'قراءة المقال',
        'empty' => 'لا توجد مقالات حديثة حتى الآن.',
        'rail_aria_label' => 'قصص مدرسية أخرى',

        'cta' => [
            'label' => 'عرض جميع القصص',
            'href' => '/artikel',
        ],

        'items' => [
            [
                'issue' => '01',
                'date' => '3 يونيو 2026',
                'category' => 'أكاديمي',
                'reading_time' => '3 دقائق قراءة',
                'emoji' => '🧭',
                'gradient_from' => 'var(--color-yellow-soft)',
                'gradient_to' => 'var(--color-orange-soft)',
                'title' => 'إيقاع تعلم الطفل: هادئ، منظم، ومن دون استعجال',
                'description' => 'كيف تنظم المدرسة الأنشطة اليومية ليجد الطفل وقتًا للتعلم واللعب والدعاء وطرح الأسئلة وتجربة أشياء جديدة بثقة.',
                'highlight' => 'التركيز: الروتين اليومي للتعلم',
                'href' => '/artikel',
            ],
            [
                'issue' => '02',
                'date' => '18 مايو 2026',
                'category' => 'الشخصية',
                'reading_time' => '4 دقائق قراءة',
                'emoji' => '🌱',
                'gradient_from' => 'var(--color-mint-soft)',
                'gradient_to' => 'var(--color-blue-soft)',
                'title' => 'آداب صغيرة نغرسها كل يوم',
                'description' => 'من إلقاء السلام والانتظار في الصف إلى ترتيب الأغراض والقدرة على الاعتذار. عادات صغيرة تصنع أثرًا كبيرًا ودائمًا.',
                'highlight' => 'التركيز: بناء العادات والشخصية',
                'href' => '/artikel',
            ],
            [
                'issue' => '03',
                'date' => '2 مايو 2026',
                'category' => 'البرامج',
                'reading_time' => '3 دقائق قراءة',
                'emoji' => '🏫',
                'gradient_from' => 'var(--color-pink-soft)',
                'gradient_to' => 'var(--color-purple-soft)',
                'title' => 'التعرف على برامج الروضة والابتدائية الإسلامية وتحفيظ القرآن والثقافة القرائية',
                'description' => 'ملخص واضح يساعد أولياء الأمور على فهم محور كل برنامج تعليمي دون الحاجة إلى قراءة وثائق طويلة ومعقدة.',
                'highlight' => 'التركيز: برامج المدرسة',
                'href' => '/artikel',
            ],
            [
                'issue' => '04',
                'date' => '20 أبريل 2026',
                'category' => 'الأنشطة',
                'reading_time' => 'دقيقتان للقراءة',
                'emoji' => '🎨',
                'gradient_from' => 'var(--color-blue-soft)',
                'gradient_to' => 'var(--color-yellow-soft)',
                'title' => 'التعلم من خلال الإبداع والقصص والشجاعة في الظهور',
                'description' => 'تساعد الأنشطة الإبداعية الأطفال على تنمية اللغة والتعبير عن الذات والعمل الجماعي والثقة بالنفس في بيئة آمنة.',
                'highlight' => 'التركيز: إبداع الأطفال',
                'href' => '/galeri',
            ],
        ],
    ],

    'footer' => [
        'brand' => [
            'href' => '#beranda',
            'image' => '/media/home/logo-footer.webp',
            'image_alt' => 'شعار المستقبل',
            'name' => 'المستقبل',
            'description' => 'مدرسة إسلامية صديقة للطفل ترافق نمو الطلاب وتغرس فيهم الأدب وحب الاستكشاف منذ مرحلة الطفولة المبكرة.',
        ],

        'channels_title' => 'وسائل التواصل الاجتماعي:',

        'channels' => [
            [
                'label' => 'الموقع',
                'note' => 'خرائط Google',
                'icon' => 'maps',
                'asset' => '/media/home/maps.png',
                'asset_alt' => 'أيقونة الموقع في خرائط Google',
                'href' => 'https://www.google.com/maps/search/?api=1&query=Jl.%20Mayjend%20Panjaitan%20No.19%2C%20Penanggungan%2C%20Klojen%2C%20Malang',
            ],
            [
                'label' => 'واتساب',
                'note' => 'محادثة الإدارة',
                'icon' => 'whatsapp',
                'asset' => '/media/home/wa.svg',
                'asset_alt' => 'أيقونة واتساب',
                'href' => 'https://wa.me/6288991128060',
            ],
            [
                'label' => 'إنستغرام',
                'note' => 'أنشطة المدرسة',
                'icon' => 'instagram',
                'asset' => '/media/home/instagram.svg',
                'asset_alt' => 'أيقونة إنستغرام',
                'href' => 'https://www.instagram.com/sdit.almustaqbal',
            ],
            [
                'label' => 'فيسبوك',
                'note' => 'صفحة المدرسة',
                'icon' => 'facebook',
                'asset' => '/media/home/facebook.png',
                'asset_alt' => 'أيقونة فيسبوك',
                'href' => 'https://www.facebook.com/almustaqbal.School19/',
            ],
            [
                'label' => 'البريد الإلكتروني',
                'note' => 'إرسال رسالة',
                'icon' => 'email',
                'asset' => '/media/home/gmail.png',
                'asset_alt' => 'أيقونة Gmail',
                'href' => 'mailto:almustaqbal010@gmail.com',
            ],
        ],

        'links_title' => 'صفحاتنا',

        'links' => [
            [
                'label' => 'الرئيسية',
                'href' => '#beranda',
            ],
            [
                'label' => 'عن المدرسة',
                'href' => '#visi-misi',
            ],
            [
                'label' => 'المقالات',
                'href' => '/artikel',
            ],
            [
                'label' => 'المعرض',
                'href' => '/galeri',
            ],
            [
                'label' => 'التسجيل والقبول',
                'href' => '/ppdb',
            ],
        ],

        'gallery_links_title' => 'معرضنا',

        'gallery_links' => [
            [
                'label' => 'المدرسة الابتدائية الإسلامية',
                'href' => '/galeri',
            ],
            [
                'label' => 'اللغات',
                'href' => '/galeri',
            ],
            [
                'label' => 'رياض الأطفال',
                'href' => '/galeri',
            ],
            [
                'label' => 'تحفيظ القرآن',
                'href' => '/galeri',
            ],
            [
                'label' => 'المكتبة',
                'href' => '/galeri',
            ],
        ],

        'partners_title' => 'شركاؤنا',

        'partners' => [
            [
                'label' => 'توثيق الأنشطة',
                'image' => '/media/home/6.png',
                'href' => 'https://www.instagram.com/reel/CzFSt1oyYGm',
            ],
            [
                'label' => 'مدرسة المستقبل الابتدائية الإسلامية المتكاملة',
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

        'copyright' => 'المستقبل. جميع الحقوق محفوظة.',
    ],
];
