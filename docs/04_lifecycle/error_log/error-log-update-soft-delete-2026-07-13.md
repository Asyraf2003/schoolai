# ERROR LOG UPDATE — SOFT DELETE CONTENT ADMIN

**Tanggal:** 13 Juli 2026  
**Project:** Al Mustaqbal School / `schoolai`  
**Branch lokal terakhir:** `agent/remaining-content-soft-delete`  
**Status integrasi:** Implementasi dan validasi lokal selesai, PR bertingkat masih draft dan belum digabung ke `main`.

---

## 1. Kondisi Awal dari Audit Sebelumnya

Audit awal menemukan enam kelompok konten admin masih memakai alur hapus biasa melalui pemanggilan model `delete()`, sementara beberapa controller juga langsung menghapus file fisik dari storage.

Kelompok yang terdampak:

1. Artikel
2. Galeri Utama
3. Bagian Halaman Galeri
4. Media pada Bagian Galeri
5. PPDB Showcase
6. Statistik Homepage

Risiko yang ditemukan:

- data tidak memiliki mekanisme arsip dan pemulihan;
- file media dapat hilang saat record dihapus;
- record lama tidak dapat dipulihkan;
- tidak ada mekanisme aman ketika staf membuat data pengganti yang identik;
- relasi parent–child pada Bagian Galeri dan Media perlu aturan khusus;
- batas aktif pada Galeri dan Statistik harus tetap dihitung hanya dari record aktif;
- seed Statistik berpotensi membuat data default baru ketika semua record aktif terarsip.

`PpdbSetting`, akun pengguna, tabel cache, queue, session, dan password reset sengaja tidak dimasukkan ke batch soft delete ini.

---

## 2. Keputusan Desain

Semua enam kelompok konten sekarang memakai pola berikut:

- record dihapus secara logis dengan `deleted_at`;
- tidak ada route atau tombol hapus permanen;
- record arsip tetap terlihat di admin dalam kondisi redup/grayscale;
- record arsip bersifat read-only;
- halaman publik hanya membaca record aktif melalui global scope `SoftDeletes`;
- aksi **Pulihkan** mengaktifkan kembali record arsip;
- aksi **Pulihkan & Gantikan** hanya tersedia dari record arsip;
- target pengganti diverifikasi ulang di server;
- pertukaran status dilakukan dalam transaksi database;
- file media tidak dihapus saat record dipindahkan ke arsip;
- file lama hanya boleh dihapus saat tidak lagi direferensikan oleh record aktif maupun arsip.

---

## 3. Artikel

### Branch dan PR

- Branch: `agent/article-soft-delete`
- Draft PR: `#3`
- Head SHA: `a7004e05e07fab69336a0376073b4d8c15eb2d72`

### Implementasi

- menambahkan `SoftDeletes` pada model `Article`;
- menambahkan kolom `deleted_at` pada tabel `articles`;
- thumbnail tetap disimpan ketika artikel diarsipkan;
- artikel arsip tidak tampil di homepage dan halaman artikel publik;
- detail dan edit artikel arsip tidak dapat dibuka lewat URL langsung;
- restore biasa tersedia;
- restore-ganti memakai identitas `link_id` yang dinormalisasi;
- normalisasi URL mengabaikan perbedaan scheme/host case, port default, trailing slash, fragment, dan urutan query parameter;
- target pengganti yang tidak identik ditolak;
- endpoint restore menolak record yang masih aktif.

### Hasil

Vertical slice Artikel selesai dan menjadi pola dasar untuk kelompok konten berikutnya.

---

## 4. Galeri Utama

### Branch dan PR

- Branch: `agent/gallery-main-soft-delete`
- Draft PR: `#4`
- Base: `agent/article-soft-delete`
- Head SHA: `2124ad9794db230ff7c1b57601166d2fe1e7c732`

### Migration

- `2026_07_13_100000_add_soft_deletes_to_gallery_items_table`

### Implementasi

- menambahkan `SoftDeletes` pada `GalleryItem`;
- file foto tetap disimpan ketika item diarsipkan;
- arsip tampil redup di admin;
- detail, edit, toggle, pindah urutan, dan penghapusan ulang diblokir untuk arsip;
- restore biasa tersedia bila slot aktif belum penuh;
- restore-ganti hanya menerima item dengan `type + media_url` ter-normalisasi yang identik;
- batas maksimal enam item hanya menghitung record aktif;
- minimal satu item terbit tetap dilindungi;
- normalisasi urutan hanya memproses record aktif;
- file bersama tidak dihapus bila masih direferensikan record arsip.

### Migration lokal

```text
2026_07_13_100000_add_soft_deletes_to_gallery_items_table ... DONE
```

---

## 5. Bagian Galeri dan Media Bagian

### Branch dan PR

- Branch: `agent/gallery-sections-soft-delete`
- Draft PR: `#5`
- Base: `agent/gallery-main-soft-delete`
- Head SHA: `c85d60d160bf0210e90a345e8990aab10b236a2b`

### Migration

- `2026_07_13_110000_add_soft_deletes_to_gallery_page_sections_table`
- `2026_07_13_110100_add_soft_deletes_to_gallery_page_media_items_table`

### Aturan Parent–Child

- mengarsipkan Bagian Galeri tidak mengubah atau menghapus child media;
- child tetap tersimpan, tetapi tidak terlihat publik selama parent terarsip;
- pemulihan parent langsung mengembalikan child aktif seperti semula;
- akses detail, edit, toggle, dan delete media diblokir ketika parent terarsip;
- `cascadeOnDelete` lama tetap ada hanya untuk kemungkinan penghapusan fisik di luar UI;
- tidak ada route `forceDelete` pada admin.

### Restore dan Identitas

- section identik dicocokkan berdasarkan judul Indonesia yang dinormalisasi;
- media identik dicocokkan berdasarkan section yang sama serta `type + media_url`;
- pengganti lintas section ditolak;
- swap dilakukan secara atomik;
- file media bersama tetap disimpan bila masih direferensikan record aktif atau arsip.

### Test fokus

```text
PASS Tests\Feature\Admin\GallerySectionMediaSoftDeleteTest
11 passed
74 assertions
```

### Migration lokal

```text
2026_07_13_110000_add_soft_deletes_to_gallery_page_sections_table ... DONE
2026_07_13_110100_add_soft_deletes_to_gallery_page_media_items_table ... DONE
```

---

## 6. PPDB Showcase dan Statistik Homepage

### Branch dan PR

- Branch: `agent/remaining-content-soft-delete`
- Draft PR: `#6`
- Base: `agent/gallery-sections-soft-delete`
- Head SHA terakhir: `b5d4e1c4f101d09400ae420ed008d010e2850ee3`

### Migration

- `2026_07_13_120000_add_soft_deletes_to_ppdb_showcase_items_table`
- `2026_07_13_120100_add_soft_deletes_to_site_statistics_table`

### PPDB Showcase

- CRUD showcase aktif dipisahkan ke controller khusus;
- file foto tidak dihapus saat item diarsipkan;
- file lama tidak dihapus bila masih digunakan record arsip;
- arsip tampil redup dan read-only;
- restore biasa menempatkan item pada posisi aktif berikutnya dalam audience yang sama;
- restore-ganti hanya menerima item dengan `audience + title_id` ter-normalisasi yang identik;
- target lintas audience atau tidak berkaitan ditolak;
- endpoint restore menolak record aktif;
- halaman PPDB publik hanya menampilkan item aktif.

### Statistik Homepage

- maksimal empat statistik hanya menghitung record aktif;
- statistik terakhir tidak boleh diarsipkan;
- urutan hanya dinormalisasi untuk record aktif;
- arsip tampil redup dan tidak dapat diedit;
- restore biasa hanya tersedia bila slot aktif belum penuh;
- ketika empat slot aktif terisi, restore harus memakai pengganti yang valid;
- restore-ganti memakai label Indonesia dan English yang dinormalisasi;
- seed default hanya berjalan bila tabel benar-benar kosong, termasuk tidak memiliki record arsip;
- halaman publik hanya menampilkan statistik aktif.

---

## 7. Error Test yang Ditemukan dan Diperbaiki

### Error

Test awal menghasilkan:

```text
FAILED Tests\Feature\Admin\RemainingContentSoftDeleteTest
BadMethodCallException
Call to undefined method App\Models\SiteStatistic::toBe()
```

Lokasi:

```text
tests/Feature/Admin/RemainingContentSoftDeleteTest.php:282
```

### Root Cause

Kesalahan berada pada chaining assertion Pest:

```php
expect($archived->fresh())
    ->not->toBeNull()
    ->value->toBe('100')
    ->sort_order->toBe(1);
```

Setelah assertion properti `value`, chain tidak lagi bekerja pada object expectation yang sesuai dan akhirnya mencoba memanggil `toBe()` melalui model `SiteStatistic`.

### Perbaikan

Assertion dipisahkan:

```php
$restored = $archived->fresh();

expect($restored)->not->toBeNull();
expect($restored->value)->toBe('100');
expect($restored->sort_order)->toBe(1);
```

Commit perbaikan:

```text
b5d4e1c
```

### Hasil ulang

```text
PASS Tests\Feature\Admin\RemainingContentSoftDeleteTest
PASS Tests\Feature\Admin\SiteStatisticCrudTest

17 passed
97 assertions
```

Error tersebut hanya berasal dari kode test. Implementasi soft delete dan transaksi tidak menjadi penyebab kegagalan.

---

## 8. Validasi Build dan Migration

### Frontend

```bash
npm run build
```

Hasil:

- Vite build selesai tanpa error;
- manifest dan asset frontend baru berhasil dibuat.

### Preview migration

```bash
php artisan migrate --pretend
```

SQL yang tervalidasi:

```text
alter table `ppdb_showcase_items` add `deleted_at` timestamp null
alter table `site_statistics` add `deleted_at` timestamp null
```

Tidak ada SQL destruktif.

### Migration nyata

```text
2026_07_13_120000_add_soft_deletes_to_ppdb_showcase_items_table ... DONE
2026_07_13_120100_add_soft_deletes_to_site_statistics_table ... DONE
```

---

## 9. Full Regression Test

Perintah:

```bash
php artisan test
```

Hasil akhir:

```text
Tests: 59 passed
Assertions: 459
Duration: 8.21s
Failures: 0
```

Seluruh test Artikel, Galeri, Bagian Galeri, Media Bagian, PPDB Showcase, Statistik, autentikasi, locale, dan fitur lama tetap lulus.

---

## 10. Status Akhir Sesi

### Selesai

- Artikel soft delete: selesai
- Galeri Utama soft delete: selesai
- Bagian Galeri soft delete: selesai
- Media Bagian soft delete: selesai
- PPDB Showcase soft delete: selesai
- Statistik Homepage soft delete: selesai
- restore biasa: selesai
- restore-ganti aman: selesai
- retensi file media: selesai
- proteksi parent–child: selesai
- proteksi batas aktif: selesai
- build frontend: lulus
- seluruh migration lokal: lulus
- test fokus: lulus
- full regression suite: lulus
- pemeriksaan lokal terakhir dikonfirmasi berfungsi oleh pengguna

### Belum dilakukan

- PR `#3`, `#4`, `#5`, dan `#6` belum digabung;
- perubahan belum masuk ke branch `main`;
- migration production belum dijalankan;
- build/deploy package production belum dibuat dari hasil gabungan terbaru.

### Urutan Integrasi yang Aman

Karena PR disusun bertingkat, urutan merge harus:

1. PR `#3` Artikel
2. PR `#4` Galeri Utama
3. PR `#5` Bagian Galeri + Media
4. PR `#6` PPDB Showcase + Statistik

Setelah setiap base digabung, base PR berikutnya perlu diverifikasi atau dipindahkan ke `main` sebelum merge.

---

## 11. Kesimpulan

Akar masalah audit awal, yaitu penghapusan konten tanpa arsip dan tanpa pemulihan aman, telah diselesaikan untuk seluruh enam kelompok konten admin.

Status saat ini:

```text
IMPLEMENTASI BRANCH: SELESAI
BUILD LOKAL: PASS
MIGRATION LOKAL: PASS
FULL TEST SUITE: 59 PASS / 459 ASSERTIONS
PR STACK: DRAFT, BELUM MERGE
PRODUCTION: BELUM DIUPDATE
```
