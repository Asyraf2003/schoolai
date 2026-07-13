<?php

return [
    'brand' => 'SchoolAI',

    'home' => [
        'title' => 'SchoolAI',
        'kicker' => 'SchoolAI',
        'heading' => 'Welcome',
        'note' => 'The homepage is connected to Vite CSS and JavaScript.',
    ],

    'auth' => [
        'login' => [
            'title' => 'Login',
            'heading' => 'Login',
            'google_button' => 'Sign in with Google',
            'separator' => 'or sign in with email and password',
            'email_label' => 'Email',
            'password_label' => 'Password',
            'remember_label' => 'Remember me',
            'submit' => 'Sign in',
        ],

        'account' => [
            'title' => 'Content Not Available',
            'status' => 'Regular user',
            'heading' => 'Content is not available here yet',
            'description' => 'Your account signed in successfully as a regular user. The admin area is not available to this account.',
            'logout' => 'Sign out',
        ],

        'errors' => [
            'invalid_credentials' => 'The email or password is incorrect.',
            'google_failed' => 'Google login failed. Please try again.',
            'google_missing_email' => 'The Google account does not have a usable email address.',
            'google_unverified_email' => 'The Google email address is not verified yet.',
        ],

        'success' => [
            'logged_in' => 'Signed in successfully.',
            'logged_out' => 'Signed out successfully.',
        ],
    ],

    'dashboard' => [
        'title' => 'Dashboard',
        'heading' => 'Welcome',
        'logout' => 'Logout',
    ],
];
