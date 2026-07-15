# SEC-09 sampai SEC-14 — implementation checkpoint

Tanggal: 2026-07-15

## FACT

- `npm audit --package-lock-only --json` dijalankan terhadap `package-lock.json` di `main`: 0 critical, 0 high, 0 moderate, 0 low.
- SEC-10 menambah validasi raster aktual, batas dimensi/pixel, penolakan ekstensi/MIME palsu, payload aktif, data trailing/polyglot, dan EXIF JPEG. Penyimpanan file baru di-rollback bila database gagal; file lama baru dihapus setelah database berhasil.
- SEC-11 tidak menemukan Blade raw echo pada permukaan publik yang diaudit. Sink `innerHTML` statis di lightbox dihapus dan diganti DOM API; regression test mengunci escaping payload database.
- SEC-12 mengubah language switch yang menulis session/cookie dari GET menjadi POST + CSRF. Logout tetap POST dan Socialite tetap memakai state.
- SEC-13 mengaktifkan fallback secure/encrypted session untuk production, mendokumentasikan HttpOnly/SameSite, menguji invalidasi logout, dan membuat trusted proxy bersifat opt-in.
- SEC-14 memusatkan validasi URL publik dan menolak kredensial URL, skema non-HTTP(S), IPv4 alternatif, IPv4/IPv6 private atau reserved, punycode, serta hasil DNS private. Aplikasi tidak melakukan fetch URL pengguna di server.
- Semua file PHP yang berubah telah lolos parser sintaks statis. JavaScript yang berubah telah lolos `node --check`.

## GAP yang harus dibuktikan runner

- Container implementasi tidak menyediakan binary PHP/Composer, sehingga hasil `composer audit` dan Laravel test suite tidak boleh diklaim PASS dari container ini.
- Workflow `.github/workflows/security-audit.yml` menjalankan Composer audit, npm audit, frontend build, dan Laravel tests pada setiap push/PR. Status workflow adalah bukti final SEC-09 dan regression suite.

## Verifikasi lokal

```bash
composer audit --locked --no-interaction
npm audit --audit-level=high
npm run build
php artisan test
```

Expected: Composer dan npm tidak memiliki advisory high/critical, build selesai, dan seluruh test PASS.
