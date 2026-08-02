<?php

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
        'title' => 'The admin dashboard is available on desktop only.',
        'description' => 'This area is intentionally designed for desktop screens so content management remains clear and organized instead of being forced into a small display.',
    ],

    'nav' => [
        'label' => 'Admin menu',
        'dashboard' => 'Dashboard',
        'accounts' => 'Akun',
        'ppdb' => 'Admissions',
        'artikel' => 'Articles',
        'galery' => 'Gallery',
        'stats' => 'Statistics',
        'view_site' => 'View Website',
        'logout' => 'Logout',
    ],

    'shell' => [
        'status' => 'Dummy Mode',
        'notice' => 'This panel is still a dummy version. The structure is being prepared first, and the actual features will follow if humans can avoid changing their minds every five minutes.',
    ],

    'gallery' => [
        'title' => 'Gallery Admin',
        'heading' => 'Gallery',
        'description' => 'Manage the 6 main homepage items and the gallery page section.',
        'create_title' => 'Add Item',
        'edit_title' => 'Edit Item',
        'detail_title' => 'Item Details',
        'create_button' => 'Add',
        'back_button' => 'Back',
        'save_button' => 'Save',
        'update_button' => 'Update',
        'delete_button' => 'Delete',
        'edit_button' => 'Edit',
        'toggle_on' => 'Enable',
        'toggle_off' => 'Disable',
        'move_up' => 'Move Up',
        'move_down' => 'Move Down',
        'empty_title' => 'No items yet.',
        'empty_description' => 'Add at least 1 item.',
        'limit_badge' => ':count/:max items',
        'type' => 'Type',
        'category' => 'Category',
        'date' => 'Date',
        'caption' => 'Caption',
        'status' => 'Status',
        'published' => 'Active',
        'draft' => 'Inactive',
        'media' => 'Media',
        'sort_order' => 'Position',

        'form' => [
            'title_id' => 'Indonesian Title',
            'title_en' => 'English Title',
            'title_ar' => 'Arabic Title',
            'type' => 'Type',
            'category_id' => 'Indonesian Category',
            'category_en' => 'English Category',
            'category_ar' => 'Arabic Category',
            'caption_id' => 'Indonesian Caption',
            'caption_en' => 'English Caption',
            'caption_ar' => 'Arabic Caption',
            'photo_file' => 'Upload Photo',
            'video_url' => 'Video URL',
            'published_at' => 'Date',
            'is_published' => 'Active',
            'current_media' => 'Current Media',
            'photo_hint' => 'JPG, PNG, or WebP. Maximum 10MB.',
            'video_hint' => 'Paste a YouTube, TikTok, Instagram, or Vimeo URL.',
            'review_title' => 'Media Preview',
            'review_empty' => 'Choose a photo or paste a video URL to preview it.',
        ],
    ],

    'pages' => [
        'dashboard' => [
            'title' => 'Admin Dashboard',
            'heading' => 'Dashboard',
            'description' => 'An initial overview for managing the school website.',
            'empty_title' => 'Dashboard not available yet',
            'empty_description' => 'This area can later contain admission summaries, article counts, gallery activity, and public content status.',
            'cards' => [
                ['label' => 'Website Status', 'value' => 'Public dummy active'],
                ['label' => 'Active Modules', 'value' => 'Navbar, footer, public pages'],
                ['label' => 'Next Action', 'value' => 'Prepare real admin data'],
            ],
        ],

        'ppdb' => [
            'title' => 'Admissions Admin',
            'heading' => 'Admissions',
            'description' => 'A management area for admission information.',
            'empty_title' => 'Admissions module not available yet',
            'empty_description' => 'This area can later contain applicant data, timelines, documents, FAQs, and publishing controls.',
            'cards' => [
                ['label' => 'Applicant Data', 'value' => 'Not active yet'],
                ['label' => 'Timeline', 'value' => 'Dummy'],
                ['label' => 'Documents', 'value' => 'Not available yet'],
            ],
        ],

        'artikel' => [
            'title' => 'Articles Admin',
            'heading' => 'Articles',
            'description' => 'A management area for school articles.',
            'empty_title' => 'Article module not available yet',
            'empty_description' => 'This area can later contain article lists, categories, drafts, publishing schedules, and a content editor.',
            'cards' => [
                ['label' => 'Total Articles', 'value' => 'Dummy'],
                ['label' => 'Drafts', 'value' => 'Not active yet'],
                ['label' => 'Categories', 'value' => 'Not available yet'],
            ],
        ],

        'galeri' => [
            'title' => 'Gallery Admin',
            'heading' => 'Gallery',
            'description' => 'A management area for activity photos and videos.',
            'empty_title' => 'Gallery module not available yet',
            'empty_description' => 'This area can later contain photo uploads, videos, activity categories, and homepage display settings.',
            'cards' => [
                ['label' => 'Photos', 'value' => 'Not active yet'],
                ['label' => 'Video/Reel', 'value' => 'Not active yet'],
                ['label' => 'Categories', 'value' => 'Dummy'],
            ],
        ],
    ],
];
