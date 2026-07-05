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
