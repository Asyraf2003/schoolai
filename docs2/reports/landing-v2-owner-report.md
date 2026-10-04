# Laporan owner — Shell + Menu + Hero

Tanggal audit: 2026-10-04. Status target: BLOCKED_BY_MISSING_EVIDENCE.
Ini laporan source dan database lokal, bukan klaim tampilan browser sudah sama.

## APA YANG DITEMUKAN

1. Pada awal audit folder baru kosong, tetapi alamat `/` masih memanggil homepage lama.
   Ini sudah diperbaiki dengan Blade landing baru yang hanya memuat Menu + Hero.
2. Menu mempunyai logo, Home, Education, Gallery, Articles, Contact, Login,
   pilihan bahasa, dan kontrol suara Hero. Desktop punya panel gambar + tautan;
   sampai lebar 1180px menu memakai tombol hamburger dan panel layar penuh.
3. Hero memakai video CF, poster cadangan, teks, dan tautan. Bila ada artikel
   pilihan, Hero menjadi carousel; bila tidak, video pembuka berulang sendiri.
4. Isi final Hero diubah lagi oleh database dan status PPDB. Database lokal saat
   audit menunjukkan PPDB terbuka dan tidak ada artikel yang dipasang di Hero.
   Jadi contoh slide dari file bahasa bukan isi final yang boleh langsung disalin.
5. Menu masih menunjuk bagian yang sengaja belum dibangun: visi-misi, nilai,
   program, galeri homepage, artikel homepage, dan kontak.

## APA FUNGSINYA

Menu mengantar pengunjung ke tujuan dan mengatur panelnya sendiri. Hero menyampaikan
pesan utama sekolah/kampanye pendaftaran dengan media sekolah yang sama.
Suara awalnya mati; pengunjung memilih menyalakannya.
Video berhenti saat halaman tidak terlihat, di luar layar, atau pengguna meminta
pengurangan gerakan. Poster menjaga tampilan saat video tidak dapat diputar.
Header berubah setelah scroll awal. Di desktop, Header juga dapat menghilang saat
scroll turun melewati Hero, lalu muncul saat naik/fokus; ini berbeda dari pergantian warna awal.

## APA YANG DIPERTAHANKAN

- Identitas warna, font Inter, logo, menu bergambar, Hero media penuh dan susunan copy.
- Teks dari sumber bahasa/DB existing, termasuk penggantian copy saat PPDB terbuka.
- URL video, poster, logo, gambar submenu, dan tautan sistem yang masih valid.
- Buka/tutup menu, Escape, fokus, kontrol suara, media fallback, dan carousel bila data memerlukannya.
- Batas menu existing 1180/1181px sebagai referensi terukur untuk V2.

## APA YANG DIBUANG / DITULIS ULANG

Yang ditulis ulang adalah implementasinya; source arsip tidak dihapus.
CSS Hero lama memuat banyak lapisan cascade, kemudian diubah lagi oleh style
inline Blade. V2 membutuhkan satu pemilik style per komponen dan perubahan responsif
yang jelas. JS Hero lama bahkan memulai submenu; pola ini tidak dibawa.
Loader/readiness global, selector lama, fallback yang menyuntik style panjang,
dan pengendalian seluruh homepage tidak menjadi dependency V2.

## APA YANG PINDAH OWNER

- Buka/tutup submenu, menu layar kecil, fokus, warna dan visibilitas Header → Menu/Header.
- Slide, video, poster, suara, dan penghentian playback → Hero.
- Tombol suara di Menu hanya meminta perubahan kepada Hero, lalu menampilkan hasilnya.
- Font/reset/focus/direction dasar → foundation; bukan file Hero yang menata Menu.
- Pemilihan content DB/PPDB → binding data landing yang terpisah dari behavior browser.

## RISIKO YANG DITEMUKAN

- Mengambil lang saja akan kehilangan content DB/kampanye PPDB.
- Mempertahankan tautan section sebagai aktif membuat klik menuju target yang belum ada.
- Pilihan bahasa lama mengaktifkan ID/AR; fase sekarang harus tetap EN.
- Halaman tujuan non-landing masih memiliki route, tetapi view-nya juga berada di arsip.
  Route terdaftar tidak sama dengan halaman tujuan telah berfungsi; tidak diperbaiki diam-diam.
- Pemeriksaan struktur awal gagal karena file JS Hero kosong belum diimpor; sekarang sudah PASS.
- Chromium, Firefox, WebKit, dan Edge sudah diuji pada scope yang dicatat di proof.
  Kesamaan visual penuh dengan legacy dan Safari asli belum dibuktikan.

## RENCANA IMPLEMENTASI

1. Foundation dan Blade shell EN; route tipis tanpa memanggil semua bagian homepage lama.
2. Menu modular dengan URL section tetap aktif sesuai keputusan owner.
3. Hero statis memakai content final dari sumber existing.
4. Uji lebar kecil, sedang, besar dan 1180/1181px; lanjut media dan motion bertahap.
5. Uji keyboard, pengurangan gerakan, kegagalan media/JS, serta browser yang tersedia.
6. PR tetap draft sampai bukti cukup. Jangan merge atau menyebut Hero CLOSED saat masih gagal.

## OWNER_CONFIRMED

Tautan section tetap tampil aktif dengan URL lama, meskipun target belum ada.
Section tujuan tetap tidak dibangun pada fase ini.

## HASIL IMPLEMENTASI SAAT INI

Fondasi, Blade shell, Menu/Header, Hero, kontrol media, dan motion terbatas tersedia.
Teks/DB/kampanye PPDB dan URL CF tetap memakai sumber existing.
Menu bekerja tanpa JS melalui panel native; Hero menampilkan poster dan copy.
Dengan JS, state Menu dan Hero terpisah; tombol suara terhubung melalui contract.
Video benar-benar terputar di Chromium, Firefox, WebKit, dan Edge; kontrol suara/pause lulus uji.
Pengujian 200% ukuran teks, media gagal, dan perpindahan lebar saat menu terbuka lulus.

Video canonical berukuran 94.907.995 byte menurut header HTTP CF. URL tidak diubah.
Poster menjadi baseline; video baru dimuat saat playback diizinkan, tanpa loader global.
Ini belum merupakan sertifikasi Lighthouse atau performa jaringan lambat.

Target belum CLOSED. Rincian gate, kekurangan bukti, dan langkah berikutnya berada
pada [catatan proof](../proof/landing-v2-status.md).
