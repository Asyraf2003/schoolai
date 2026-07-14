# Security Hardening Checkpoint — SEC-03 sampai SEC-06

Tanggal implementasi: 14 Juli 2026  
Branch target: `main`  
Status: IMPLEMENTASI MASUK, MENUNGGU VALIDASI RUNTIME LOKAL

## FACT

SEC-01 dan SEC-02 sudah selesai lebih dahulu.

Perubahan sesi ini:

- SEC-03 memindahkan seluruh credential Google OAuth ke `config/services.php` dan menghapus pembacaan `env()` runtime dari controller.
- SEC-04 menambahkan limiter bernama `google-oauth` pada redirect dan callback Google, dengan batas 20 request/menit per IP dan 10 request/menit per session.
- SEC-05 mengubah audit route admin dari pencarian nama `admin.*` menjadi audit seluruh URI `admin` dan `admin/*`, termasuk seluruh route mutasi.
- SEC-06 menambahkan security headers, CSP berbasis nonce, allowlist iframe provider, dan HSTS hanya untuk request HTTPS production.

Commit sebelum SEC-06:

- `60e175e` — hapus konfigurasi OAuth runtime dari controller.
- `904c1ed` — pusatkan konfigurasi OAuth di `config/services.php`.
- `2c76e92` — daftarkan limiter OAuth bernama.
- `596e33e` — pasang limiter pada dua endpoint OAuth.
- `eb82506` — test rate limit OAuth.
- `4825186` — audit proteksi admin berdasarkan URI dan method mutasi.

## KEPUTUSAN CSP

Script inline memakai nonce per response. Inline event handler dilarang melalui `script-src-attr 'none'`.

Style element memakai nonce. Style attribute tetap diizinkan karena homepage dan admin memakai CSS custom properties serta style dinamis pada Blade.

`frame-src` hanya mengizinkan:

- same-origin;
- YouTube;
- YouTube No-Cookie;
- TikTok;
- Instagram;
- Facebook;
- Vimeo.

Tidak ada wildcard iframe.

## GAP VALIDASI

Environment eksekusi GitHub sesi ini tidak menyediakan PHP, Composer, Node checkout, atau `gh`. Karena itu implementasi tidak boleh dinyatakan selesai sebelum proof lokal berikut lulus.

## PROOF WAJIB

Jalankan setelah pull:

```bash
{
  php artisan config:clear || true
  php artisan test tests/Feature/Auth/GoogleRoleAccessTest.php tests/Feature/SecurityHeadersTest.php || true
  php artisan config:cache || true
  php artisan config:clear || true
  php artisan test || true
  npm run build || true
  git diff --check || true
} 2>&1
```

Jangan lanjut SEC-07 sebelum output proof diperiksa dan regresi diperbaiki.
