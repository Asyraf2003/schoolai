# Laporan koreksi Menu + Hero

## APA YANG SALAH
Media dropdown terlalu lebar, daftar belum mempunyai empat jatah tetap,
logo berisi tulisan tambahan, garis kuning tidak tampak, bahasa belum bisa dipilih.
Warna menu berubah terlalu awal saat scroll dan menu langsung hilang saat ditutup.

## KENAPA TERJADI
Ukuran media mengikuti hampir separuh dropdown. Tinggi daftar mengikuti jumlah
isinya. Bahasa hanya ditampilkan sebagai teks, sementara halaman selalu dipaksa EN.
Warna Header memakai angka scroll kecil dan belum membaca posisi akhir Hero.

## APA YANG DIUBAH
- Desktop/tablet: media 2:3 mengambil sekitar 20% lebar layar. Daftar memakai
  empat slot dalam frame media; item kelima mulai kolom berikutnya.
- HP: sesuai koreksi terakhir owner, hanya menu dan deskripsi menu. Tidak ada
  gambar atau caption media dropdown. Media utama Hero tetap tersedia.
- Logo saja, garis kuning kembali, ukuran Sound/menu/Language seimbang.
- Menu di atas Hero tetap putih saat hover. Dropdown dan burger putih polos,
  teks hitam. Scroll memakai batas Hero, bukan angka scroll acak.
- Tablet landscape yang cukup lebar memakai full-menu; portrait memakai burger.
  Wave tersedia mulai tablet; hover hanya pointer yang mendukungnya; HP tanpa wave.
- Menu burger masuk dari bawah dan keluar ke bawah. Sound memakai garis saat
  mati dan kurva bergerak saat hidup, kecuali reduced motion.
- Pilihan tiga bahasa memakai bendera dan route bahasa existing. Halaman pertama
  tetap EN jika belum ada pilihan; preferensi bahasa yang tersimpan dihormati.
- Tombol Pause slideshow dihapus. Video/slideshow tetap otomatis mengikuti
  izin browser, reduced motion, visibility dan fokus konten.
- Media gagal/lambat menggunakan poster; jika gambar gagal, teks bisnis tetap
  tersedia. Judul panjang menyesuaikan ruang menuju dua baris tanpa dipotong.

## HASIL SEKARANG
Build dan tes V2 terarah lulus. Browser proof terbaru dicatat pada
[status patch](../proof/menu-hero-correction-status.md), bukan memakai hasil lama.
PR #64 tetap draft dan tidak di-merge.

## APA YANG BELUM SEMPURNA
- Safari asli belum diuji; WebKit automation adalah bukti terpisah.
- Fondasi RTL siap, tetapi tampilan Arab dan animasi RTL final belum dituning.
- Judul ekstrem tetap boleh lebih dari dua baris agar tidak terpotong atau terlalu kecil.
- Tes legacy penuh masih gagal karena mengharapkan source lama. Tidak diperluas
  menjadi refactor CI atau rebuild area di luar landing.
- Tidak ada klaim Lighthouse, kecepatan jaringan pengguna, atau visual CLOSED.

## APA YANG MASIH HARUS DICEK OWNER DI UI
Proporsi foto desktop/tablet, empat slot dan ruang kosong, ukuran judul,
karakter wave/Sound, serta rasa transisi menu. HP kini hanya menampilkan daftar.
Sesudah koreksi ini, Hero masih membutuhkan persetujuan visual owner.
