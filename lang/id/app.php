<?php

return [
    'brand' => 'SchoolAI',

    'home' => [
        'title' => 'SchoolAI',
        'kicker' => 'SchoolAI',
        'heading' => 'Selamat datang',
        'note' => 'Halaman utama sudah terhubung dengan Vite CSS dan JavaScript.',
    ],

    'auth' => [
        'login' => [
            'title' => 'Login',
            'heading' => 'Login',
            'description' => 'Masuk menggunakan akun Google untuk melanjutkan.',
            'google_button' => 'Masuk dengan Google',
        ],

        'account' => [
            'title' => 'Konten Belum Tersedia',
            'status' => 'Pengguna biasa',
            'heading' => 'Konten belum tersedia di sini',
            'description' => 'Akun Anda berhasil masuk sebagai pengguna biasa. Area admin tidak tersedia untuk akun ini.',
            'logout' => 'Keluar',
        ],

        'errors' => [
            'google_failed' => 'Login Google gagal. Coba lagi.',
            'google_missing_email' => 'Akun Google tidak memiliki email yang bisa digunakan.',
            'google_unverified_email' => 'Email Google belum terverifikasi.',
            'google_identity_conflict' => 'Identitas Google tidak cocok dengan akun yang tersimpan. Hubungi administrator.',
        ],

        'success' => [
            'logged_in' => 'Berhasil masuk.',
            'logged_out' => 'Berhasil keluar.',
        ],
    ],

    'dashboard' => [
        'title' => 'Dashboard',
        'heading' => 'Selamat datang',
        'logout' => 'Logout',
    ],
];
