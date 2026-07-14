# Security Hardening Checkpoint — SEC-07 dan SEC-08

Tanggal implementasi: 14 Juli 2026  
Branch target: `main`  
Status: IMPLEMENTASI MASUK, MENUNGGU VALIDASI RUNTIME LOKAL

## SEC-07

Implementasi emergency account revocation:

- kolom `disabled_at`;
- kolom `last_login_at`;
- middleware `active.account` pada seluruh route authenticated dan admin;
- session lama akun nonaktif di-logout dan di-invalidasi saat request berikutnya;
- login Google akun nonaktif ditolak;
- login Google berhasil memperbarui waktu login terakhir;
- tidak ada UI perubahan role baru.

## SEC-08

Implementasi audit log append-only:

- login admin berhasil;
- login admin ditolak karena akun nonaktif;
- kegagalan provider Google;
- konflik identitas Google;
- logout admin;
- perubahan status akun dan role;
- create, update, delete, dan restore konten admin.

Model konten yang diaudit:

- Article;
- GalleryItem;
- GalleryPageSection;
- GalleryPageMediaItem;
- PpdbSetting;
- PpdbShowcaseItem;
- SiteStatistic.

Audit log tidak menerima raw request dan tidak menyimpan:

- authorization code;
- access token;
- refresh token;
- client secret;
- cookie;
- session payload;
- password;
- isi `.env`;
- email atau Google ID pada metadata event autentikasi.

## MIGRASI BARU

- `2026_07_14_130000_add_account_security_to_users_table.php`
- `2026_07_14_140000_create_security_audit_logs_table.php`

## PROOF SEBELUM SEC-09

```bash
{
  php artisan config:clear || true
  php artisan migrate:status || true
  php artisan test     tests/Feature/Auth/GoogleRoleAccessTest.php     tests/Feature/SecurityHeadersTest.php     tests/Feature/Auth/AccountRevocationTest.php     tests/Feature/SecurityAuditLogTest.php     || true
  php artisan test || true
  npm run build || true
  git diff --check || true
} 2>&1
```

Jangan lanjut SEC-09 sebelum hasil proof diperiksa.
