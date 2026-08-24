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

   FILESYSTEM_DISK=local
   MEDIA_DISK=s3
   MEDIA_PUBLIC_URL=https://media.almustaqbal.sch.id
   MEDIA_CACHE_CONTROL="public, max-age=31536000, immutable"

   AWS_ACCESS_KEY_ID=...
   AWS_SECRET_ACCESS_KEY=...
   AWS_DEFAULT_REGION=auto
   AWS_BUCKET=almustaqbal
   AWS_URL=https://media.almustaqbal.sch.id
   AWS_ENDPOINT=https://ACCOUNT_ID.r2.cloudflarestorage.com
   AWS_USE_PATH_STYLE_ENDPOINT=true
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

### Migrasi media legacy ke R2

Konfigurasikan CORS bucket dari workstation tepercaya memakai
`deploy/cloudflare/r2-cors.json`, lalu purge cache hostname media setelah policy
berubah. Contoh Wrangler:

```bash
npx wrangler r2 bucket cors set almustaqbal --file deploy/cloudflare/r2-cors.json
npx wrangler r2 bucket cors list almustaqbal
```

Di server aplikasi, audit lalu migrasikan satu owner per eksekusi:

```bash
php artisan media:migrate-r2 hero --dry-run
php artisan media:migrate-r2 hero
php artisan media:migrate-r2 gallery-homepage
php artisan media:migrate-r2 gallery-page
php artisan media:migrate-r2 ppdb
php artisan media:migrate-r2 testimonials
php artisan media:migrate-r2 articles
```

Command bersifat idempotent: hanya URL `/storage/...` dan `media/...` yang
memiliki binary lokal yang dipindahkan. External/provider URL dan URL R2 yang
sudah canonical dilewati. Pertahankan `storage/app/public` sampai seluruh owner,
public rendering, soft-delete, dan restore selesai diverifikasi.

Pengecualian H6 yang eksplisit adalah embed provider sosial/video serta URL
Unsplash yang sudah ada pada seed, demo/fallback, dan pencarian provider Article
Canvas. URL tersebut bukan object R2 milik SchoolAI dan tidak boleh ikut siklus
delete. Memindahkannya memerlukan keputusan lisensi/atribusi dan source binary
terpisah. Aset brand, chrome, serta semantic/static fallback yang dikemas di
repository juga tetap release-bundled karena bukan media konten milik CRUD.
Upload konten first-party baru tidak termasuk pengecualian dan wajib masuk R2.

## Verifikasi setelah deployment

Periksa secara berurutan:

1. `https://almustaqbal.sch.id/up` memberikan respons sukses.
2. Homepage dapat dibuka tanpa error 500.
3. Pergantian bahasa ID/EN bekerja.
4. Halaman `/ppdb`, `/artikel`, `/galeri`, dan `/login` dapat dibuka.
5. Login admin dan logout bekerja.
6. Upload satu media uji melalui admin.
7. Media uji memakai URL `https://media.almustaqbal.sch.id/...` dan tidak
   mengekspos endpoint S3.
8. Respons media memiliki `Content-Type`, cache policy immutable, dukungan
   range untuk video, dan CORS exact-origin untuk `https://almustaqbal.sch.id`.
9. Edit/replace menghapus object R2 lama yang owned; soft-delete dan restore
   mempertahankan object yang sama.
10. `public_html/deploy_once.php` sudah tidak ada.

## Deployment pembaruan tanpa kehilangan data file

Bagian ini hanya menangani pergantian file aplikasi dan upload production. Perubahan struktur atau isi database ditangani terpisah melalui phpMyAdmin, SQL query, export, dan import yang sesuai dengan release tersebut.

Jangan mengekstrak ZIP baru langsung ke `/home/almusta2` dan jangan menimpa folder aktif satu per satu. Overwrite dapat meninggalkan file kode lama yang sudah dihapus dari repository sehingga hosting berisi campuran dua release.

### Data yang harus dipertahankan

Wajib dibawa ke release baru:

```text
schoolai/.env
schoolai/storage/app/public/
schoolai/storage/app/private/
```

Ketentuan penting:

- pertahankan `APP_KEY` production yang sudah digunakan;
- jangan mengganti credential production hanya karena membuat ZIP baru;
- `storage/app/public` berisi upload yang dapat diakses melalui `/storage/...`;
- `storage/app/private` tidak boleh dipindahkan ke `public_html` atau dibuatkan symlink publik;
- `storage/logs` boleh disimpan bersama backup release lama untuk diagnosis, tetapi tidak wajib disalin ke release baru;
- `storage/framework`, `bootstrap/cache/*.php`, dan cache lama tidak perlu dipertahankan karena runner akan membuat ulang cache production.

### File milik hosting di `public_html`

Sebelum menukar `public_html`, periksa apakah ada file atau direktori yang bukan bagian paket SchoolAI, misalnya:

```text
.well-known/
cgi-bin/
file verifikasi domain
file konfigurasi atau validasi milik provider
```

Jika ada dan masih diperlukan, salin item tersebut ke `public_html` staging sebelum swap. Jangan menganggap seluruh isi document root selalu milik aplikasi.

### Layout staging dan backup

Gunakan satu nama release yang mudah dikenali, misalnya nomor paket atau timestamp:

```text
/home/almusta2/deploy-stage-004/
├── schoolai/
└── public_html/

/home/almusta2/schoolai
/home/almusta2/public_html
```

Setelah swap, hasil sementara dapat berbentuk:

```text
/home/almusta2/schoolai
/home/almusta2/public_html
/home/almusta2/schoolai-backup-004
/home/almusta2/public_html-backup-004
```

Gunakan nama yang konsisten. Jangan memakai nama backup yang sama untuk dua deployment berbeda.

### Urutan deployment pembaruan

#### A. Siapkan paket di komputer lokal

1. Pastikan branch dan commit yang akan dideploy sudah benar.
2. Pastikan working tree bersih.
3. Buat paket baru:

   ```bash
   git status --short --branch
   make deploy
   ```

4. Simpan pasangan file ZIP dan setup dengan nomor yang sama:

   ```text
   schoolai-cpanel-004.zip
   schoolai-cpanel-004-setup.txt
   ```

5. Jangan unggah file `*-setup.txt`.

#### B. Buat staging melalui cPanel File Manager

1. Upload ZIP baru ke lokasi sementara, bukan ke dalam `schoolai` atau `public_html` aktif.
2. Buat folder staging, misalnya:

   ```text
   /home/almusta2/deploy-stage-004
   ```

3. Ekstrak ZIP di dalam folder staging tersebut.
4. Pastikan hasil ekstrak tepat:

   ```text
   /home/almusta2/deploy-stage-004/schoolai
   /home/almusta2/deploy-stage-004/public_html
   ```

5. Jangan menjalankan `deploy_once.php` ketika folder masih berada di staging.

#### C. Salin data persisten ke release staging

Salin dari aplikasi aktif:

```text
/home/almusta2/schoolai/.env
    -> /home/almusta2/deploy-stage-004/schoolai/.env

/home/almusta2/schoolai/storage/app/public/*
    -> /home/almusta2/deploy-stage-004/schoolai/storage/app/public/

/home/almusta2/schoolai/storage/app/private/*
    -> /home/almusta2/deploy-stage-004/schoolai/storage/app/private/
```

Periksa bahwa:

- nama file tetap tepat `.env`, bukan `.env.txt`;
- file tersembunyi ditampilkan di File Manager;
- isi folder upload benar-benar tersalin, termasuk subfolder;
- `.env` staging masih memakai `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` production, `APP_KEY` lama, dan credential database production;
- tidak ada `public_html/storage` hasil salinan manual di staging.

Jika `public_html` aktif mempunyai file milik hosting yang masih dibutuhkan, salin file tersebut ke:

```text
/home/almusta2/deploy-stage-004/public_html
```

#### D. Tangani database secara terpisah

Selesaikan prosedur database yang sesuai sebelum menjalankan runner release baru. Prosedur database tidak didokumentasikan sebagai bagian swap file ini karena dapat berbeda pada setiap perubahan schema.

Minimal pastikan:

- database target dan user MySQL masih sesuai dengan `.env`;
- backup SQL yang sesuai sudah tersedia jika release mengubah schema atau data;
- kode lama dan database baru tidak dicampur ketika kompatibilitasnya belum dibuktikan.

#### E. Swap folder aktif

Lakukan rename atau move melalui cPanel File Manager dengan urutan berikut:

1. Rename aplikasi aktif:

   ```text
   /home/almusta2/schoolai
       -> /home/almusta2/schoolai-backup-004
   ```

2. Rename document root aktif:

   ```text
   /home/almusta2/public_html
       -> /home/almusta2/public_html-backup-004
   ```

3. Pindahkan aplikasi staging menjadi aktif:

   ```text
   /home/almusta2/deploy-stage-004/schoolai
       -> /home/almusta2/schoolai
   ```

4. Pindahkan document root staging menjadi aktif:

   ```text
   /home/almusta2/deploy-stage-004/public_html
       -> /home/almusta2/public_html
   ```

5. Jangan menghapus folder backup pada tahap ini.

Downtime swap seharusnya hanya terjadi di antara rename folder aktif dan pemindahan folder staging. Siapkan seluruh staging dan data persisten sebelum memulai swap agar jeda tersebut sesingkat mungkin.

#### F. Jalankan runner release baru

1. Buka file setup bernomor sama di komputer lokal.
2. Kunjungi URL token yang tercantum di dalamnya.
3. Pastikan hasilnya diawali:

   ```text
   DEPLOYMENT COMPLETE
   ```

4. Runner akan:
   - memeriksa struktur dan `.env`;
   - memastikan direktori writable;
   - memeriksa koneksi database;
   - membuat atau memvalidasi symlink `public_html/storage`;
   - membersihkan dan membuat cache production;
   - mencoba menghapus `deploy_once.php`.
5. Jika penghapusan otomatis gagal, hapus `public_html/deploy_once.php` secara manual.

Runner dapat digunakan pada update karena `public_html` baru belum mempunyai symlink storage. Runner akan membuat symlink baru menuju `schoolai/storage/app/public` yang sudah berisi data production.

#### G. Smoke test update

Jalankan verifikasi pada bagian **Verifikasi setelah deployment**, lalu khusus update pastikan:

- media lama masih dapat dibuka melalui `/storage/...`;
- upload media baru berhasil;
- media lama dapat diedit atau dihapus dari admin;
- homepage memuat CSS dan JavaScript dari `public_html/build` baru;
- `deploy_once.php` sudah tidak tersedia;
- tidak ada error 500 pada halaman publik dan admin.

Jangan hapus backup sebelum seluruh pemeriksaan tersebut lulus.

### Alur ringkas update berikutnya

```text
git pull
git status --short --branch
make deploy
upload ZIP baru
extract ke deploy-stage-NNN
salin .env + storage/app/public + storage/app/private
salin file hosting non-aplikasi jika memang ada
tangani database secara terpisah
rename schoolai dan public_html aktif menjadi backup
pindahkan schoolai dan public_html staging menjadi aktif
jalankan token setup bernomor sama
jalankan smoke test
hapus staging kosong setelah terbukti aman
simpan backup sampai release dinyatakan stabil
```

File setup lokal selalu mengikuti nomor ZIP yang baru dan tidak boleh diunggah ke hosting.

## Rollback deployment pembaruan

Rollback file hanya aman dilakukan langsung bila database masih kompatibel dengan kode lama. Jika release mengubah schema atau data, pulihkan juga backup SQL yang berasal dari waktu yang sama melalui prosedur database terpisah.

Jika runner atau smoke test gagal:

1. Jangan menghapus folder release baru; rename dahulu agar dapat diperiksa:

   ```text
   /home/almusta2/schoolai
       -> /home/almusta2/schoolai-failed-004

   /home/almusta2/public_html
       -> /home/almusta2/public_html-failed-004
   ```

2. Kembalikan backup:

   ```text
   /home/almusta2/schoolai-backup-004
       -> /home/almusta2/schoolai

   /home/almusta2/public_html-backup-004
       -> /home/almusta2/public_html
   ```

3. Periksa kembali `/up`, homepage, login, dan media lama.
4. Jika database turut berubah, pulihkan SQL backup yang cocok dengan release lama sebelum menyatakan rollback selesai.

Simpan untuk setiap deployment penting:

- folder aplikasi Laravel lama;
- `.env` production di dalam backup aplikasi;
- `storage/app/public` dan `storage/app/private` di dalam backup aplikasi;
- isi `public_html` lama;
- export database MySQL yang sesuai dengan waktu release;
- nomor ZIP, commit Git, dan waktu deployment.

Jangan menggabungkan kode dari satu release dengan `vendor`, build Vite, cache, atau database dari release lain tanpa bukti kompatibilitas.

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
