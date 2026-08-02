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
        'navigation' => [
            'login' => 'LOGIN',
            'toggle' => 'Buka pilihan login',
            'guru' => 'Guru',
            'guru_description' => 'Masuk dengan akun Google yang terdaftar.',
            'murid' => 'Murid',
            'murid_description' => 'Masuk dengan ID siswa dan password.',
        ],
        'login' => [
            'title' => 'Login',
            'heading' => 'Login',
            'description' => 'Masuk menggunakan akun Google untuk melanjutkan.',
            'google_button' => 'Masuk dengan Google',
            'back_home' => 'Kembali ke beranda',
        ],

        'guru_login' => [
            'title' => 'Login Guru',
            'heading' => 'Login Guru',
            'description' => 'Gunakan akun Google yang telah didaftarkan oleh sekolah.',
            'google_button' => 'Masuk dengan Google',
            'loading' => 'Menghubungkan…',
        ],

        'student_login' => [
            'title' => 'Login Murid',
            'heading' => 'Login Murid',
            'description' => 'Gunakan ID siswa dan password yang diberikan sekolah.',
            'student_id' => 'ID siswa',
            'password' => 'Password',
            'submit' => 'Masuk',
            'loading' => 'Memeriksa…',
            'locked_countdown' => 'Coba lagi dalam :seconds detik',
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
            'account_disabled' => 'Akun ini telah dinonaktifkan. Hubungi administrator.',
            'access_unavailable' => 'Akun belum terdaftar atau belum memiliki akses.',
            'student_credentials' => 'ID siswa atau password tidak sesuai.',
            'student_locked' => 'Terlalu banyak percobaan. Coba lagi setelah satu menit.',
            'login_failed' => 'Login gagal. Silakan coba lagi.',
            'network' => 'Layanan tidak dapat dihubungi. Coba lagi.',
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
