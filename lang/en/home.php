<?php

$id = require __DIR__ . '/../id/home.php';

return array_replace_recursive($id, [
    'meta' => [
        'title' => 'Al Mustaqbal School',
        'description' => 'Official Al Mustaqbal School website for admission, programs, school activities, and contact information.',
    ],

    'hero' => [
        'title_before' => 'An Islamic school that nurtures',
        'title_highlight' => 'character, knowledge, and memorization',
        'title_after' => 'from an early age',
        'subtitle' => 'A warm learning environment where children grow with the Qur’an, good manners, language, creativity, and teachers who work closely with families.',
        'visual_image_alt' => 'Al Mustaqbal School building',
        'logo_image_alt' => 'Al Mustaqbal School',
        'primary_cta' => [
            'label' => 'Apply for Admission',
            'href' => '/ppdb',
        ],
        'secondary_cta' => [
            'label' => 'View Programs',
            'href' => '#program',
        ],
    ],

    'navbar' => [
        'aria_label' => 'Main menu',
        'items' => [
            [
                'label' => 'Home',
                'href' => '#beranda',
            ],
            [
                'label' => 'Education',
                'href' => '#program',
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
                'badge' => 'EN',
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
            'label' => 'Apply Now',
            'href' => '/ppdb',
        ],
        'mobile_open_label' => 'Open menu',
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
                'label' => 'Achievements',
            ],
            [
                'count' => 1200,
                'suffix' => '+',
                'label' => 'Learning Hours',
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
                'description' => 'Announcements, schedules, and school information in one place.',
            ],
            [
                'icon' => '🎨',
                'background' => 'var(--color-mint-soft)',
                'title' => 'Student Activities',
                'description' => 'Explore activities that help students grow their interests and talents.',
            ],
            [
                'icon' => '📝',
                'background' => 'var(--color-blue-soft)',
                'title' => 'Online Admission',
                'description' => 'New student admission can be completed more easily.',
            ],
            [
                'icon' => '🎒',
                'background' => 'var(--color-pink-soft)',
                'title' => 'Education Levels',
                'description' => 'Programs are available for SDIT, kindergarten, tahfidz, and playgroup.',
            ],
        ],
    ],

    'ppdb' => [
        'eyebrow' => 'New Student Admission',
        'title' => 'Apply Now at Al Mustaqbal School',
        'description' => 'New student admission is easier to access. Academic information, activities, and registration steps are available in one place.',
        'cta' => [
            'label' => 'Apply Now',
            'href' => '/ppdb',
        ],
        'steps' => [
            [
                'number' => '1',
                'title' => 'Fill the Form',
                'description' => 'Complete the prospective student and parent data online.',
            ],
            [
                'number' => '2',
                'title' => 'Data Verification',
                'description' => 'The admin team reviews the registration data and documents.',
            ],
            [
                'number' => '3',
                'title' => 'Registration Confirmation',
                'description' => 'Parents receive further information from the school.',
            ],
            [
                'number' => '4',
                'title' => 'Announcement',
                'description' => 'Admission results are shared through the school’s official contact.',
            ],
        ],
    ],

    'visi_misi' => [
        'vision' => [
            'eyebrow' => 'Vision',
            'title' => 'The Educational Direction of Al Mustaqbal',
            'text_parts' => [
                ['text' => 'Al Mustaqbal’s education program aims to nurture '],
                ['text' => 'Muslim generations', 'mark' => 'blue'],
                ['text' => ' with '],
                ['text' => 'inspiring, innovative, and integrity-driven character', 'mark' => 'orange'],
                ['text' => ', capable of carrying out their role as '],
                ['text' => 'Khalifatullah', 'mark' => 'green'],
                ['text' => ' through '],
                ['text' => 'holistic education.', 'mark' => 'purple'],
            ],
        ],
        'missions_intro' => [
            'eyebrow' => 'Mission',
            'title' => 'Four steps to shape a complete Islamic generation',
        ],
    ],

    'nilai_sekolah' => [
        'title' => 'Values That Guide Children’s Growth',
        'subtitle' => 'Four core values shape the learning culture of Al Mustaqbal: close to the Qur’an, brave in thinking, strong in character, and inspiring to the community.',
    ],

    'program_unggulan' => [
        'title' => 'Featured Programs',
        'subtitle' => 'Learning programs designed to nurture faith, character, language, literacy, creativity, and confidence.',
    ],

    'galeri' => [
        'title' => 'School Gallery',
        'subtitle' => 'Snapshots of learning, creativity, worship, play, and daily student activities at Al Mustaqbal School.',
        'cta' => [
            'label' => 'View Full Gallery',
            'href' => '/galeri',
        ],
    ],

    'artikel' => [
        'title' => 'School Stories for Parents',
        'subtitle' => 'Selected stories about learning, character, activities, and school programs for families.',
        'read_more' => 'Read story',
        'cta' => [
            'label' => 'View All Stories',
            'href' => '/artikel',
        ],
        'empty' => 'No stories are available yet.',
    ],

    'footer' => [
        'brand' => [
            'description' => 'A child-friendly Islamic school that supports children’s growth, manners, and curiosity from an early age.',
        ],
        'channels_title' => 'Social Media :',
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
                'note' => 'Chat Admin',
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
                'note' => 'Send Message',
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
                'label' => 'Admission',
                'href' => '/ppdb',
            ],
        ],
        'gallery_links_title' => 'Our Gallery',
        'gallery_links' => [
            [
                'label' => 'SDIT',
                'href' => '/galeri',
            ],
            [
                'label' => 'Language',
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
        'copyright' => 'Al Mustaqbal. All Rights Reserved.',
    ],
]);
