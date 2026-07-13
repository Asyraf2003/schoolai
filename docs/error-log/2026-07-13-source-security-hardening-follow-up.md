# Error Log: Source Security Hardening Follow-up

Tanggal pencatatan: 13 Juli 2026  
Status: Belum selesai  
Prioritas: Tinggi  
Scope: Source code aplikasi Laravel SchoolAI  
Tidak termasuk: VPC, firewall, reverse proxy, TLS termination, konfigurasi OS, dan keamanan provider

## Tujuan sesi berikutnya

Menyelesaikan hardening keamanan source code sebelum deployment production.

Jangan mengklaim skor keamanan 92-93% hanya berdasarkan test yang sudah hijau. Nilai source-level saat ini diperkirakan sekitar 80-84% untuk keseluruhan aplikasi, sementara modul autentikasi dan otorisasi sudah mendekati 89-92%.

Target setelah pekerjaan ini bukan angka kosmetik, tetapi seluruh acceptance criteria di bawah terbukti melalui kode dan test.

---

## Status yang sudah selesai

### Autentikasi

- Login password sudah dihapus.
- Login hanya melalui Google OAuth.
- Route `POST /login` sudah tidak tersedia dan menghasilkan HTTP 405.
- Form email/password, remember-me, dan modul JavaScript login lama sudah dihapus.
- Session diregenerasi setelah login.
- Logout membatalkan session dan meregenerasi CSRF token.

### Role dan akses admin

- Login Google pertama pada bootstrap database menjadi admin.
- Login Google berikutnya menjadi user biasa.
- User biasa diarahkan ke `/akun`.
- User biasa tidak dapat mengakses halaman maupun action admin.
- Admin tidak diarahkan ke halaman user biasa.
- Semua route admin yang sudah ada memakai middleware `auth` dan `admin`.

### Klaim admin

- Klaim admin pertama menggunakan tabel `auth_bootstrap_states`.
- Klaim dijalankan dalam database transaction.
- Row klaim dikunci menggunakan `lockForUpdate()`.
- Admin aktif saat ini sudah memiliki `google_id`.
- Tidak ada admin aktif dengan `google_id = NULL`.

### Unsafe OAuth account linking

Temuan sebelumnya:

Aplikasi sempat mencari user berdasarkan `google_id`, lalu fallback mencari berdasarkan email. Jika ada akun admin lama dengan email sama tetapi `google_id` kosong, identitas Google baru dapat tertaut otomatis ke row admin tersebut.

Perbaikan yang sudah diterapkan di worktree:

- User login utama hanya dicari berdasarkan `google_id`.
- Email hanya dipakai untuk mendeteksi konflik kepemilikan.
- Jika email sudah dimiliki row lain tetapi identitas Google tidak cocok, login ditolak.
- Identitas Google baru tidak boleh tertaut otomatis ke akun lama hanya berdasarkan kesamaan email.
- Konflik menghasilkan error `google_identity_conflict`.
- Login tetap diizinkan apabila `google_id` memang sudah tertaut ke user yang sama.

File yang berubah untuk perbaikan ini:

- `app/Http/Controllers/Auth/GoogleAuthController.php`
- `lang/id/app.php`
- `lang/en/app.php`
- `tests/Feature/Auth/GoogleRoleAccessTest.php`

Hasil test terakhir:

- Auth test: 8 passed
- Full test suite: 19 passed
- Assertions: 209
- `git diff --check`: bersih
- Admin dengan `google_id = NULL`: 0

Catatan:

Error `rg: regex parse error` pada audit terakhir berasal dari quoting command audit, bukan dari source code dan bukan kegagalan aplikasi.

Perubahan unsafe account linking kemungkinan masih berada di worktree. Periksa `git status` sebelum memulai dan jangan menghapus perubahan tersebut.

---

## Masalah keamanan yang masih harus diselesaikan

### SEC-01: Batasi bootstrap admin ke identitas Google tertentu

Prioritas: Kritis

Masalah:

Pada database baru atau setelah state klaim admin ter-reset, akun Google pertama yang login masih dapat menjadi admin.

Risiko:

- Salah akun login lebih dahulu.
- Database hasil restore memiliki state bootstrap kosong.
- Operator menjalankan reset akun tanpa sengaja.
- Pengunjung luar menjadi admin pada deployment baru.

Rencana:

- Tambahkan konfigurasi identitas bootstrap admin melalui `.env`.
- Gunakan Google subject ID, bukan email, sebagai identitas utama.
- Contoh nama variabel:

```env
GOOGLE_BOOTSTRAP_ADMIN_ID=
Pindahkan pembacaan variabel ke file config.
Jangan memanggil env() langsung dari controller.
Ketika klaim admin masih kosong:
hanya Google ID yang cocok dengan konfigurasi boleh menjadi admin;
akun lain tetap menjadi user biasa;
akun lain tidak boleh menutup klaim admin.
Setelah klaim selesai, konfigurasi bootstrap tidak boleh mengubah admin lain secara otomatis.
Jangan menaruh Google ID asli di repository atau dokumentasi.

Test minimum:

Google ID yang cocok menjadi admin pertama.
Google ID lain tetap user saat klaim masih terbuka.
Login user biasa tidak menutup klaim admin.
Admin hanya dapat diklaim satu kali.
Nilai config kosong harus fail-closed atau menghasilkan error konfigurasi yang jelas.
SEC-02: Verifikasi email OAuth harus fail-closed

Prioritas: Tinggi

Masalah saat ini:

Kode menggunakan fallback verifikasi email yang pada kondisi atribut tidak tersedia dapat dianggap terverifikasi.

Pola yang harus dihindari:

$emailVerified = $rawUser['email_verified']
    ?? $rawUser['verified_email']
    ?? true;

Rencana:

Gunakan default false:

$emailVerified = $rawUser['email_verified']
    ?? $rawUser['verified_email']
    ?? false;

Login harus ditolak jika atribut verifikasi:

tidak tersedia;
false;
'false';
0;
'0';
bernilai tidak dikenal.

Test minimum:

email_verified = true diterima.
verified_email = true diterima.
Nilai false dalam bentuk boolean, string, dan integer ditolak.
Atribut verifikasi tidak tersedia harus ditolak.
User tidak dibuat ketika verifikasi gagal.
SEC-03: Pindahkan konfigurasi Google dari controller

Prioritas: Tinggi

Masalah:

GoogleAuthController masih mengonfigurasi OAuth dengan memanggil env() langsung ketika request berjalan.

Risiko:

Laravel production biasanya memakai php artisan config:cache. Setelah config di-cache, akses env() di luar file config tidak boleh diandalkan.

Rencana:

Pastikan config/services.php memiliki:

'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],

Kemudian:

hapus method configureGoogleOAuth();
hapus semua pemanggilan method tersebut;
controller cukup memakai Socialite::driver('google');
jangan menulis nilai credential ke log atau test output.

Test dan audit minimum:

Login Google mock tetap lulus.
php artisan config:cache berhasil.
php artisan config:clear berhasil.
Tidak ada env('GOOGLE_...') di controller atau service runtime.
Credential hanya dibaca melalui file config.
SEC-04: Tambahkan rate limit OAuth

Prioritas: Tinggi

Masalah:

Endpoint redirect dan callback Google belum memiliki limiter khusus.

Risiko:

spam callback;
pembuatan banyak user valid;
pembengkakan database;
request abuse.

Rencana:

Buat limiter bernama, jangan sekadar menempel angka acak tanpa dokumentasi.
Terapkan pada:
/auth/google/redirect
/auth/google/callback
Pertimbangkan key berdasarkan IP dan session.
Jangan memblokir login normal secara agresif.

Test minimum:

request dalam batas diterima;
request melewati batas menghasilkan HTTP 429;
limiter tidak membocorkan credential atau data OAuth.
SEC-05: Perkuat test proteksi route admin

Prioritas: Tinggi

Masalah:

Test sekarang terutama mengumpulkan route berdasarkan nama admin.*.

Risiko:

Developer dapat membuat URI /admin/... tanpa nama admin.*, sehingga route tersebut tidak ikut diperiksa.

Rencana:

Test harus memeriksa seluruh route yang:

URI-nya admin;
URI-nya diawali admin/;
memakai method POST, PUT, PATCH, atau DELETE di area admin.

Seluruh route tersebut wajib mengandung middleware:

web;
auth;
admin;
admin.locale jika memang standar project.

Test juga harus memastikan:

guest diarahkan ke login;
user biasa diarahkan ke /akun;
request mutasi user biasa tidak mengubah database;
route admin baru tanpa middleware menyebabkan test gagal.
SEC-06: Tambahkan security headers berbasis middleware

Prioritas: Tinggi

Scope ini masih source-level.

Header minimum yang perlu dievaluasi dan diterapkan:

X-Content-Type-Options: nosniff
Referrer-Policy
Permissions-Policy
Content-Security-Policy
perlindungan framing melalui CSP frame-ancestors
Strict-Transport-Security hanya ketika production benar-benar HTTPS

Catatan CSP:

Aplikasi memakai embed dari beberapa provider. frame-src perlu mengizinkan hanya domain yang benar-benar digunakan, misalnya:

YouTube
TikTok
Instagram
Facebook
Vimeo

Jangan menggunakan wildcard luas tanpa alasan.

Test minimum:

public page menerima security headers;
admin page menerima security headers;
CSP tidak mematahkan asset Vite;
CSP tidak mematahkan embed yang memang didukung;
domain iframe asing ditolak oleh kebijakan.
SEC-07: Emergency admin revocation

Prioritas: Tinggi

Masalah:

Jika akun Google admin diambil alih, belum ada kontrol source-level untuk menonaktifkan admin dengan cepat selain mengubah database langsung.

Rencana yang perlu dipilih dan didokumentasikan:

kolom is_active;
atau disabled_at;
atau mekanisme revocation setara.

Middleware autentikasi harus menolak akun nonaktif.

Pertimbangkan pula:

penghapusan session aktif;
rotasi session;
pencatatan waktu login terakhir;
pencatatan perubahan role;
jangan membuat UI perubahan role sebelum model otorisasinya jelas.

Test minimum:

admin aktif dapat masuk;
admin nonaktif ditolak;
user nonaktif ditolak;
session lama akun yang dinonaktifkan tidak tetap memiliki akses admin.
SEC-08: Audit log untuk aktivitas admin penting

Prioritas: Menengah-Tinggi

Aktivitas minimum yang layak dicatat:

login admin berhasil;
login admin ditolak;
konflik identitas Google;
logout admin;
pembuatan, perubahan, dan penghapusan artikel;
perubahan galeri;
perubahan PPDB;
perubahan statistik;
perubahan status akun apabila fitur revocation dibuat.

Jangan mencatat:

OAuth authorization code;
access token;
refresh token;
client secret;
cookie session;
password;
isi .env.
SEC-09: Dependency security audit

Prioritas: Tinggi

Jalankan:

composer audit
npm audit

Kemudian:

catat dependency rentan;
bedakan production dependency dan development dependency;
jangan melakukan upgrade mayor membabi buta;
jalankan test dan build setelah setiap perubahan dependency;
dokumentasikan risiko yang sengaja diterima.

Acceptance criteria:

tidak ada vulnerability critical atau high yang tidak ditangani;
build frontend berhasil;
full test suite berhasil;
lock files konsisten.
SEC-10: Audit upload dan penyimpanan file

Prioritas: Menengah-Tinggi

Yang sudah ada:

validasi file;
validasi image;
daftar MIME/ekstensi;
batas ukuran;
penyimpanan menggunakan nama yang dibuat Laravel;
pemeriksaan path traversal saat penghapusan.

Yang masih perlu diperiksa:

batas dimensi gambar;
file gambar rusak atau polyglot;
SVG harus tetap tidak diizinkan kecuali ada sanitizer;
metadata EXIF sensitif;
file lama saat update gagal;
konsistensi rollback file dan database;
content type saat file disajikan;
public storage hanya berisi file yang memang boleh publik.

Test minimum:

upload file non-gambar ditolak;
SVG ditolak;
file dengan ekstensi palsu ditolak;
ukuran berlebih ditolak;
gambar rusak ditolak;
path traversal tidak dapat menghapus file di luar disk;
kegagalan database tidak meninggalkan file yatim bila memungkinkan.
SEC-11: Audit XSS dan output Blade

Prioritas: Tinggi

Periksa seluruh Blade untuk:

{!! ... !!};
innerHTML;
URL dari database;
isi artikel/deskripsi;
attribute URL;
iframe src;
JSON yang ditanam ke HTML;
data attribute.

Pastikan:

teks menggunakan escaping Blade standar {{ ... }};
URL hanya berasal dari normalizer atau validator yang sesuai;
iframe tidak menerima URL arbitrer;
tidak ada HTML mentah dari input admin tanpa sanitizer;
penggunaan target="_blank" memiliki rel="noopener noreferrer" jika relevan.

Tambahkan test stored XSS dengan payload seperti:

<script>alert(1)</script>
"><img src=x onerror=alert(1)>
javascript:alert(1)

Payload harus tampil sebagai teks aman atau ditolak, bukan dieksekusi.

SEC-12: Audit CSRF dan method mutation

Prioritas: Menengah-Tinggi

Periksa:

seluruh form mutasi memiliki @csrf;
update memakai method spoofing yang benar;
delete memakai method spoofing yang benar;
tidak ada mutasi data melalui GET;
callback OAuth tetap menggunakan state bawaan Socialite;
tidak ada penggunaan stateless() tanpa alasan.

Test minimum:

request mutasi tanpa CSRF ditolak pada environment web normal;
GET tidak mengubah database;
logout tetap POST;
action admin tidak dapat dijalankan melalui URL GET.
SEC-13: Session dan cookie source configuration

Prioritas: Tinggi

Periksa konfigurasi:

secure cookie untuk production HTTPS;
HTTP only;
SameSite;
session lifetime;
session driver;
session invalidation;
trusted proxy behavior;
APP_URL dan redirect OAuth.

Nilai production jangan di-hardcode di source. Dokumentasikan variabel .env yang diperlukan tanpa menyimpan nilainya.

SEC-14: Pemeriksaan URL publik dan SSRF-related logic

Prioritas: Menengah

Aplikasi menyimpan URL artikel, PPDB, dan embed.

Yang sudah ada:

sebagian URL localhost dan private IPv4 ditolak;
embed video dibatasi ke provider yang didukung.

Yang masih perlu diperiksa:

IPv6 private/link-local;
IPv4 dalam bentuk alternatif;
hostname yang resolve ke IP privat;
credential dalam URL;
redirect chain jika aplikasi kelak melakukan server-side fetch;
domain spoofing;
punycode/homograph;
skema selain HTTP/HTTPS.

Catatan:

Jika aplikasi hanya menyimpan URL dan browser pengguna yang membukanya, risikonya berbeda dari SSRF. Namun validasi tetap harus konsisten.

Urutan eksekusi yang disarankan

Kerjakan berurutan:

Pastikan patch unsafe email account linking tersimpan dan test tetap hijau.
SEC-01 bootstrap admin allowlist.
SEC-02 verified email fail-closed.
SEC-03 pindahkan config OAuth ke config/services.php.
SEC-04 rate limiting OAuth.
SEC-05 perluas route security tests.
SEC-06 security headers dan CSP.
SEC-07 emergency account revocation.
SEC-08 audit log admin.
SEC-09 dependency audit.
SEC-10 sampai SEC-14 audit aplikasi menyeluruh.

Jangan mencampur seluruh perubahan dalam satu patch besar. Setiap bagian harus memiliki test dan diff yang dapat diperiksa.

Guardrails sesi berikutnya
Jangan menjalankan migrate:fresh.
Jangan menghapus akun admin atau user saat ini.
Jangan mereset auth_bootstrap_states.
Jangan mengubah role akun secara manual tanpa kebutuhan terbukti.
Jangan menyimpan Google ID, email admin, token, atau secret di repository.
Jangan mengaktifkan login password kembali.
Jangan memakai Socialite::stateless() tanpa alasan dan test khusus.
Jangan commit sebelum targeted test, full test, build, dan git diff --check lulus.
Gunakan command audit non-fatal yang tetap melanjutkan dan melaporkan kegagalan.
Pertahankan Blade, vanilla CSS, dan vanilla JavaScript.
Jangan menambah framework UI atau CDN eksternal.
Acceptance criteria akhir

Pekerjaan dinyatakan selesai hanya jika:

unsafe email linking tetap tertutup;
bootstrap admin dibatasi ke identitas yang dikonfigurasi;
missing email verification ditolak;
tidak ada env() runtime di controller;
OAuth memiliki limiter;
seluruh URI admin diuji berdasarkan URI dan middleware;
security headers diterapkan dan diuji;
akun dapat dinonaktifkan;
aktivitas admin penting tercatat tanpa secret;
composer audit tidak memiliki critical/high yang belum ditangani;
npm audit tidak memiliki critical/high yang belum ditangani;
audit upload, XSS, CSRF, session, dan URL selesai;
npm run build berhasil;
targeted test berhasil;
full test suite berhasil;
git diff --check bersih;
browser smoke test admin dan user biasa berhasil;
dokumentasi deployment diperbarui bila ada variabel .env baru.
Command awal sesi berikutnya

Mulai dengan audit tanpa mengubah file:

{
  printf '%s\n' '--- CURRENT COMMIT ---'
  git log -3 --oneline --decorate

  printf '%s\n' '--- WORKTREE ---'
  GIT_PAGER=cat git status --short

  printf '%s\n' '--- SECURITY PATCH DIFF ---'
  git diff -- \
    app/Http/Controllers/Auth/GoogleAuthController.php \
    lang/id/app.php \
    lang/en/app.php \
    tests/Feature/Auth/GoogleRoleAccessTest.php \
    || true

  printf '%s\n' '--- AUTH TEST ---'
  php artisan test \
    tests/Feature/Auth/GoogleRoleAccessTest.php \
    || true

  printf '%s\n' '--- FULL TEST ---'
  php artisan test || true

  printf '%s\n' '--- BUILD ---'
  npm run build || true

  printf '%s\n' '--- RUNTIME ENV USAGE ---'
  rg -n "env\\(" app routes resources || true

  printf '%s\n' '--- ROUTE SECURITY ---'
  php artisan route:list -v \
    | rg -C 3 'admin|auth/google|login|logout' \
    || true

  printf '%s\n' '--- SECURITY-SENSITIVE SOURCE ---'
  rg -n \
    "stateless|innerHTML|{!!|Storage::|hasFile|iframe|target=\"_blank\"|Auth::login|lockForUpdate|google_id|email_verified" \
    app \
    routes \
    resources \
    tests \
    || true

  printf '%s\n' '--- DEPENDENCY AUDIT ---'
  composer audit || true
  npm audit || true

  printf '%s\n' '--- DIFF CHECK ---'
  git diff --check || true
} 2>&1

Setelah membaca output tersebut, kerjakan SEC-01 sampai SEC-03 lebih dahulu.
