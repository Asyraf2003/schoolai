# SchoolAI: Deployment ke cPanel Shared Hosting

Panduan ini digunakan untuk deployment SchoolAI ketika hosting hanya menyediakan cPanel dan File Manager tanpa terminal.

## Layout hosting Al Mustaqbal saat ini

Home directory cPanel:

```text
/home/almusta2
```

Struktur migrasi pertama:

```text
/home/almusta2/app1         # SchoolAI lama; dipertahankan sementara sebagai rollback
/home/almusta2/schoolai     # SchoolAI baru dari paket deployment
/home/almusta2/public_html  # document root domain
```

Jangan rename atau hapus `app1` sebelum versi baru selesai melewati smoke test. Karena data pada fase awal masih dummy dan boleh dibuang, pembuatan ZIP backup tambahan bersifat opsional selama `app1` tetap tidak diubah. Ketika data production sudah nyata, backup file, media, dan database kembali menjadi wajib.

## Hasil akhir

Perintah lokal:

```bash
make deploy
```

menghasilkan dua file di `deploy-package/`:

```text
schoolai-cpanel-001.zip
schoolai-cpanel-001-setup.txt
```

ZIP berisi tepat dua folder:

```text
schoolai/
public_html/
```

- `schoolai/` adalah root aplikasi Laravel dan harus berada di luar document root.
- `public_html/` berisi front controller, `.htaccess`, aset Vite, dan runner deployment sekali pakai.
- File `*-setup.txt` tetap disimpan di komputer lokal karena berisi URL dengan token sekali pakai. Jangan unggah file ini.

## Persyaratan lokal

- Git working tree bersih.
- PHP 8.3 atau lebih baru.
- Composer.
- Node.js dan npm.
- `rsync`, `zip`, dan `unzip`.
- Koneksi internet saat `npm ci`, pengambilan font, dan `composer install`.

Builder berhenti jika working tree kotor, build Vite gagal, Composer gagal, manifest hilang, atau ZIP membawa file terlarang.

## Persyaratan hosting

- PHP 8.3 atau lebih baru.
- Ekstensi PHP yang dibutuhkan Laravel dan MySQL, khususnya `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, dan `tokenizer`.
- Fungsi PHP `symlink()` tidak dinonaktifkan.
- `storage/` dan `bootstrap/cache/` dapat ditulis oleh PHP.
- Database MySQL dan seluruh tabel aplikasi sudah disiapkan melalui phpMyAdmin atau mekanisme lain.

## Membuat paket

Pastikan berada pada commit yang akan dideploy dan working tree bersih:

```bash
git status --short --branch
make deploy
```

Builder akan:

1. menjalankan `npm ci` dan build Vite;
2. membersihkan cache environment lokal;
3. membuat root Laravel dan document root terpisah;
4. memasang dependency Composer production tanpa paket development;
5. membuat token setup acak;
6. membuat ZIP;
7. memverifikasi struktur, vendor, manifest, cache, media, SQLite, symlink, dan file environment.
8. menormalkan permission direktori menjadi `0755` dan file menjadi `0644` agar aset statis dapat dibaca web server.

## File yang tidak pernah masuk ZIP

- `.env` dan variasi environment yang berisi credential;
- database SQLite lokal;
- upload lokal dari `storage/app/public` dan `storage/app/private`;
- `public/storage` dari komputer lokal;
- config, route, event, dan view cache yang bergantung pada environment lokal;
- `.git`, `node_modules`, test, serta source frontend yang tidak dibutuhkan hosting.

Template `schoolai/.env.production.example` boleh digunakan sebagai acuan, tetapi tidak berisi `APP_KEY`, credential database, atau credential Google OAuth.

## Deployment pertama

1. Buat backup file hosting dan database yang sudah ada.
2. Upload ZIP melalui cPanel File Manager.
3. Ekstrak ZIP di folder sementara agar isinya dapat diperiksa.
4. Pindahkan folder `schoolai/` ke home directory akun cPanel sehingga hasilnya seperti:

   ```text
   /home/USERNAME/schoolai/
   ```

5. Pindahkan seluruh isi folder hasil ekstraksi `public_html/` ke document root sebenarnya:

   ```text
   /home/USERNAME/public_html/
   ```

6. Buat atau salin environment production ke:

   ```text
   /home/USERNAME/schoolai/.env
   ```

7. Pastikan minimal nilai berikut sudah benar sebelum setup dijalankan:

   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://almustaqbal.sch.id
   APP_KEY=base64:...

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=...
   DB_USERNAME=...
   DB_PASSWORD=...
   ```

8. Biarkan variabel Google OAuth kosong jika login Google tidak digunakan:

   ```dotenv
   GOOGLE_CLIENT_ID=
   GOOGLE_CLIENT_SECRET=
   GOOGLE_REDIRECT_URI=
   ```

9. Siapkan atau import database MySQL sebelum menjalankan runner setup.
10. Buka file `schoolai-cpanel-001-setup.txt` di komputer lokal lalu kunjungi URL bertoken yang tertulis di dalamnya.

### Migrasi khusus dari `app1` ke `schoolai`

Untuk layout `/home/almusta2` yang sekarang:

1. Biarkan `/home/almusta2/app1` tetap utuh.
2. Ekstrak paket di folder sementara.
3. Pindahkan folder hasil ekstraksi `schoolai` ke `/home/almusta2/schoolai`.
4. Salin `.env` lama dari `app1/.env` ke `schoolai/.env`, lalu kosongkan konfigurasi Google OAuth jika tidak digunakan.
5. Jika ada media yang perlu dipertahankan, salin isi `app1/storage/app/public` ke `schoolai/storage/app/public`.
6. Salin isi folder hasil ekstraksi `public_html` ke `/home/almusta2/public_html`.
7. Pastikan database siap, lalu jalankan URL token dari file setup lokal.
8. Jalankan seluruh smoke test sebelum mempertimbangkan penghapusan `app1`.

Jika versi baru gagal sebelum `app1` dihapus, rollback cukup dengan mengembalikan file `public_html/index.php` lama beserta aset publik lama agar kembali menunjuk ke `app1`.

## Runner setup sekali pakai

`public_html/deploy_once.php` melakukan pemeriksaan berikut:

- versi PHP;
- keberadaan `.env`, vendor, bootstrap, public-path marker, dan Vite manifest;
- permission direktori yang harus writable;
- `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_KEY` tersedia;
- koneksi database berhasil;
- symlink media mengarah ke target yang benar.

Symlink yang dibuat:

```text
/home/USERNAME/public_html/storage
    -> /home/USERNAME/schoolai/storage/app/public
```

`storage/app/private` tidak pernah dihubungkan ke document root.

Setelah preflight lulus, runner menjalankan:

```text
config:clear
route:clear
view:clear
config:cache
route:cache
view:cache
```

Runner kemudian menulis marker keberhasilan dan mencoba menghapus `deploy_once.php` secara otomatis. Jika penghapusan otomatis gagal, hapus file tersebut melalui File Manager.

Jangan membagikan URL token dan jangan menjalankannya sebelum `.env` serta database siap.

## Verifikasi setelah deployment

Periksa secara berurutan:

1. `https://almustaqbal.sch.id/up` memberikan respons sukses.
2. Homepage dapat dibuka tanpa error 500.
3. Pergantian bahasa ID/EN bekerja.
4. Halaman `/ppdb`, `/artikel`, `/galeri`, dan `/login` dapat dibuka.
5. Login admin dan logout bekerja.
6. Upload satu media uji melalui admin.
7. Media uji dapat dibuka melalui URL `/storage/...`.
8. Edit, toggle, urutan, dan hapus media tetap bekerja.
9. `public_html/deploy_once.php` sudah tidak ada.

## Deployment pembaruan

- Jangan menghapus `.env` production.
- Jangan menghapus `schoolai/storage/app/public`; folder tersebut menyimpan upload production.
- Jangan mengganti `APP_KEY` pada deployment biasa.
- Backup database dan media sebelum mengganti kode.
- Hapus atau ganti `public_html/build` dengan build baru agar aset lama tidak menumpuk.
- Salin kode root Laravel baru dengan tetap mempertahankan `.env` dan media production.
- Salin isi `public_html/` baru.
- Jalankan URL token baru dari file setup lokal.
- Runner dapat digunakan kembali karena menerima symlink lama jika targetnya masih benar.

Alur ringkas update berikutnya:

```text
git pull
git status --short --branch
make deploy
upload ZIP baru
pertahankan schoolai/.env dan schoolai/storage/app/public
ganti kode aplikasi dan isi public_html
jalankan token setup baru
jalankan smoke test
```

File setup lokal selalu mengikuti nomor ZIP yang baru dan tidak boleh diunggah ke hosting.

## Rollback

Sebelum deployment pembaruan, simpan:

- folder aplikasi Laravel lama;
- `.env` production;
- `storage/app/public`;
- isi `public_html` lama;
- export database MySQL.

Jika deployment baru gagal, kembalikan kode aplikasi, `public_html`, dan database dari backup yang berasal dari waktu yang sama. Jangan mencampur database baru dengan kode lama tanpa memastikan migration-nya kompatibel.

## Troubleshooting singkat

### `symlink() is disabled`

Hosting memblokir pembuatan symbolic link. Jangan menghubungkan `storage/app/private`. Hubungi provider atau ubah strategi public storage sebelum melanjutkan.

### `Database connection failed`

Periksa `DB_HOST`, nama database, username, password, hak akses user MySQL, dan pastikan database sudah tersedia.

### `.public-path does not resolve`

Pastikan kedua folder berada tepat di:

```text
/home/USERNAME/schoolai
/home/USERNAME/public_html
```

### Build font gagal dengan `fetch failed`

Build font membutuhkan internet. Ulangi `npm run build`; setelah berhasil, jalankan kembali `make deploy`.

### HTML tampil tetapi CSS/JS mengembalikan 404

Periksa permission di `public_html/build`. Direktori harus `0755` dan file CSS/JS harus `0644`. Builder dan verifier terbaru menormalkan serta memeriksa permission tersebut; buat ulang paket dan ganti folder `public_html/build` jika paket lama menghasilkan mode `0700/0600`.

### Runner menampilkan `APP_KEY is empty`

Salin `APP_KEY` production lama atau buat key production sebelum menjalankan runner. Jangan mengganti key pada aplikasi production yang sudah memiliki data terenkripsi.
