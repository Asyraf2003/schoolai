<?php
/* ADMIN_DESKTOP_DUMMY_LANG_FINAL */

return [
    'meta' => [
        'title' => 'Al Mustaqbal School Admin',
    ],
    'brand' => [
        'name' => 'Al Mustaqbal',
        'panel' => 'Admin Panel',
        'mark' => 'AM',
    ],
    'desktop_only' => [
        'title' => 'The admin dashboard is desktop-only.',
        'description' => 'This area is intentionally designed for desktop screens so content management stays clean and is not forced into a tiny layout.',
    ],
    'nav' => [
        'label' => 'Admin menu',
        'dashboard' => 'Dashboard',
        'ppdb' => 'Admission',
        'artikel' => 'Articles',
        'galery' => 'Gallery',
        'view_site' => 'View Website',
        'logout' => 'Logout',
    ],
    'shell' => [
        'eyebrow' => 'Admin Area',
        'status' => 'Dummy Mode',
        'notice' => 'This panel is still dummy. The structure comes first, real features can follow when humans stop changing scope every five minutes.',
    ],

    /* ADMIN_GALLERY_DUMMY_LANG_FINAL */
    'gallery' => [
        'title' => 'Gallery Admin',
        'eyebrow' => 'First Module',
        'heading' => 'Manage Public Gallery',
        'description' => 'This page is still dummy, but it already maps homepage gallery data toward a future database structure.',
        'limit_badge' => 'Maximum :max active items',
        'minimum_badge' => 'Minimum :min item is valid',
        'video_badge' => 'Video maximum :minutes minutes',
        'source_title' => 'Dummy source from homepage',
        'source_description' => 'The list below reads gallery items from lang/home.php, limited to 6 latest items for admin simulation.',
        'empty_title' => 'No gallery item yet',
        'empty_description' => 'Later, even 1 gallery item is enough to keep the public section displayable.',
        'type' => 'Type',
        'category' => 'Category',
        'duration' => 'Duration',
        'date' => 'Date',
        'caption' => 'Caption',
        'video_rule' => 'Video rule',
        'video_rule_text' => 'For video/reel, store duration in seconds and reject anything longer than 180 seconds.',
        'db_title' => 'Future Database Map',
        'db_description' => 'No migration is created yet. This is only a structure contract so the future DB does not become a drawer with no shelves.',
        'field' => 'Field',
        'data_type' => 'Data Type',
        'note' => 'Note',
        'slot_title' => 'Public Display Slot',
        'slot_description' => 'Homepage and gallery page can read active items ordered by sort_order, maximum 6 for homepage.',
    ],
    /* /ADMIN_GALLERY_DUMMY_LANG_FINAL */

    'pages' => [
        'dashboard' => [
            'title' => 'Admin Dashboard',
            'heading' => 'Dashboard',
            'description' => 'Initial overview for managing the school website.',
            'empty_title' => 'Dashboard is not available yet',
            'empty_description' => 'This area can later show admission summaries, article counts, gallery activity, and public content status.',
            'cards' => [
                ['label' => 'Website Status', 'value' => 'Public dummy active'],
                ['label' => 'Active Modules', 'value' => 'Navbar, footer, public pages'],
                ['label' => 'Next Action', 'value' => 'Prepare real admin data'],
            ],
        ],
        'ppdb' => [
            'title' => 'Admission Admin',
            'heading' => 'Admission',
            'description' => 'Management space for admission information.',
            'empty_title' => 'Admission module is not available yet',
            'empty_description' => 'This can later contain applicant data, timeline, documents, FAQ, and publishing controls.',
            'cards' => [
                ['label' => 'Applicants', 'value' => 'Inactive'],
                ['label' => 'Timeline', 'value' => 'Dummy'],
                ['label' => 'Documents', 'value' => 'Unavailable'],
            ],
        ],
        'artikel' => [
            'title' => 'Article Admin',
            'heading' => 'Articles',
            'description' => 'Management space for school articles.',
            'empty_title' => 'Article module is not available yet',
            'empty_description' => 'This can later contain article lists, categories, drafts, publish schedule, and content editor.',
            'cards' => [
                ['label' => 'Total Articles', 'value' => 'Dummy'],
                ['label' => 'Drafts', 'value' => 'Inactive'],
                ['label' => 'Categories', 'value' => 'Unavailable'],
            ],
        ],
        'galeri' => [
            'title' => 'Gallery Admin',
            'heading' => 'Gallery',
            'description' => 'Management space for activity photos and videos.',
            'empty_title' => 'Gallery module is not available yet',
            'empty_description' => 'This can later contain photo uploads, videos, activity categories, and homepage display settings.',
            'cards' => [
                ['label' => 'Photos', 'value' => 'Inactive'],
                ['label' => 'Video/Reel', 'value' => 'Inactive'],
                ['label' => 'Categories', 'value' => 'Dummy'],
            ],
        ],
    ],
];
