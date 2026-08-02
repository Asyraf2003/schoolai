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
        'navigation' => [
            'login' => 'LOGIN',
            'toggle' => 'Open login options',
            'guru' => 'Teacher',
            'guru_description' => 'Sign in with a registered Google account.',
            'murid' => 'Student',
            'murid_description' => 'Sign in with a student ID and password.',
        ],
        'login' => [
            'title' => 'Login',
            'heading' => 'Login',
            'description' => 'Sign in with your Google account to continue.',
            'google_button' => 'Continue with Google',
            'back_home' => 'Back to homepage',
        ],

        'guru_login' => [
            'title' => 'Teacher Login',
            'heading' => 'Teacher Login',
            'description' => 'Use the Google account registered by the school.',
            'google_button' => 'Continue with Google',
            'loading' => 'Connecting…',
        ],

        'student_login' => [
            'title' => 'Student Login',
            'heading' => 'Student Login',
            'description' => 'Use the student ID and password provided by the school.',
            'student_id' => 'Student ID',
            'password' => 'Password',
            'submit' => 'Login',
            'loading' => 'Checking…',
            'locked_countdown' => 'Try again in :seconds seconds',
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
            'access_unavailable' => 'This account is not registered or does not have access.',
            'student_credentials' => 'The student ID or password is incorrect.',
            'student_locked' => 'Too many attempts. Try again in one minute.',
            'login_failed' => 'Login failed. Please try again.',
            'network' => 'The service cannot be reached. Please try again.',
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
