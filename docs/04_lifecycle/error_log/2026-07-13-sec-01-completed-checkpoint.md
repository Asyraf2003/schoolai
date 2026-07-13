# Security Hardening Checkpoint — SEC-01 Selesai

Tanggal update: 13 Juli 2026
Project: Al Mustaqbal School / `schoolai`
Status: SEC-01 selesai dan siap digabung
PR: #7 — Restrict first admin bootstrap to configured Google identity
Branch: `agent/sec-01-google-bootstrap-allowlist`
Base: `main`

## Instruksi sesi berikutnya

Jangan mengaudit ulang SEC-01 dari awal kecuali PR #7 berubah, mengalami konflik, atau test regresi baru gagal.

Mulai pekerjaan berikutnya dari **SEC-02: verifikasi email OAuth harus fail-closed**.

Sebelum memulai SEC-02, cukup verifikasi singkat:

```bash
git fetch origin --prune
git status --short --branch
gh pr view 7 --repo Asyraf2003/schoolai --json number,state,isDraft,mergeable,baseRefName,headRefName,url
```

## FACT — Kondisi sebelum perbaikan

Pada database baru atau state bootstrap yang belum diklaim, akun Google pertama dapat menjadi admin tanpa pembatasan identitas.

Logika lama hanya memeriksa:

- klaim admin masih terbuka;
- belum ada akun admin.

Tidak ada pemeriksaan bahwa Google subject ID login cocok dengan identitas operator yang ditentukan.

## DECISION

Klaim admin pertama dibatasi menggunakan Google subject ID melalui variabel konfigurasi:

```env
GOOGLE_BOOTSTRAP_ADMIN_ID=
```

Google subject ID dipilih sebagai identitas utama, bukan email.

Nilai asli tidak disimpan dalam repository atau dokumentasi.

Konfigurasi kosong harus fail-closed: tidak ada akun yang otomatis menjadi admin.

## IMPLEMENTASI SELESAI

File perubahan SEC-01:

- `.env.example`
- `app/Http/Controllers/Auth/GoogleAuthController.php`
- `config/services.php`
- `deploy/cpanel/.env.production.example`
- `tests/Feature/Auth/GoogleRoleAccessTest.php`

Perilaku akhir:

- Google ID yang cocok dengan konfigurasi dapat mengklaim admin pertama;
- Google ID lain tetap menjadi user biasa;
- login user biasa tidak menutup klaim admin;
- konfigurasi kosong tidak menghasilkan admin;
- klaim admin hanya dapat terjadi satu kali;
- perubahan konfigurasi setelah klaim tidak memindahkan role admin;
- controller membaca melalui `config()`, bukan `env()` langsung;
- Google ID asli tidak berada di repository.

## TEST DAN VALIDASI

Targeted auth test:

```text
9 passed
166 assertions
```

Full regression suite pada branch SEC-01:

```text
20 passed
227 assertions
```

Validasi lain:

- `php artisan config:cache`: berhasil;
- `php artisan config:clear`: berhasil;
- `npm run build`: berhasil;
- `git diff --check`: bersih;
- worktree sebelum push: bersih;
- remote branch terverifikasi;
- PR #7 berstatus open, ready for review, dan mergeable;
- PR #7 hanya memuat lima file implementasi/test SEC-01 sebelum checkpoint dokumentasi ini.

## CATATAN CONFIG CACHE

Jangan menjalankan PHPUnit ketika config lokal masih di-cache.

`php artisan config:cache` mengunci environment lokal sehingga `APP_ENV=testing` dari `phpunit.xml` tidak diterapkan seperti yang diharapkan. Gejalanya adalah request POST test menghasilkan HTTP 419 karena CSRF aktif.

Urutan validasi yang benar:

```bash
php artisan config:cache
php artisan config:clear
php artisan test
```

Kegagalan HTTP 419 yang pernah terlihat bukan bug SEC-01.

## DEPLOYMENT WAJIB

Sebelum klaim admin pertama pada production, isi nilai berikut di `.env` production:

```env
GOOGLE_BOOTSTRAP_ADMIN_ID=<Google subject ID admin yang sah>
```

Kemudian jalankan refresh konfigurasi production sesuai prosedur deployment.

Jangan:

- menyimpan Google subject ID asli ke Git;
- menggunakan email sebagai pengganti subject ID;
- mereset `auth_bootstrap_states` tanpa prosedur khusus;
- menjalankan `migrate:fresh`;
- mengubah role admin secara manual tanpa bukti kebutuhan.

## STATUS AKHIR

```text
SEC-01 IMPLEMENTATION : SELESAI
TARGETED TEST         : PASS
FULL TEST             : PASS
FRONTEND BUILD        : PASS
REMOTE BRANCH         : TERSEDIA
PR #7                 : READY FOR REVIEW, BELUM MERGE
PRODUCTION ENV        : BELUM DIISI
NEXT SECURITY ITEM    : SEC-02
```

## NEXT STEP

Kerjakan **SEC-02 saja** pada patch terpisah:

- ubah verifikasi email OAuth menjadi fail-closed;
- atribut verifikasi yang hilang harus ditolak;
- hanya nilai boolean yang benar-benar valid yang diterima;
- user tidak boleh dibuat bila verifikasi gagal;
- tambahkan targeted tests sebelum mengubah controller.
