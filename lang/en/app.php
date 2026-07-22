<?php

return [
    'brand' => 'SchoolAI',

    'home' => [
        'title' => 'SchoolAI',
        'kicker' => 'SchoolAI',
        'heading' => 'Welcome',
        'note' => 'The home page is already connected to Vite CSS and JavaScript.',
    ],

    'auth' => [
        'login' => [
            'title' => 'Login',
            'heading' => 'Login',
            'description' => 'Sign in with your Google account to continue.',
            'google_button' => 'Continue with Google',
        ],

        'account' => [
            'title' => 'Content Not Available Yet',
            'status' => 'Standard user',
            'heading' => 'Content is not available here yet',
            'description' => 'You have successfully signed in as a standard user. The admin area is not available for this account.',
            'logout' => 'Logout',
        ],

        'errors' => [
            'google_failed' => 'Google login failed. Please try again.',
            'google_missing_email' => 'This Google account does not have a usable email address.',
            'google_unverified_email' => 'The Google email address has not been verified.',
            'google_identity_conflict' => 'The Google identity does not match the stored account. Please contact the administrator.',
            'account_disabled' => 'This account has been disabled. Please contact the administrator.',
        ],

        'success' => [
            'logged_in' => 'Successfully signed in.',
            'logged_out' => 'Successfully signed out.',
        ],
    ],

    'dashboard' => [
        'title' => 'Dashboard',
        'heading' => 'Welcome',
        'logout' => 'Logout',
    ],
];
