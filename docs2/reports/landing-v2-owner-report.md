# Laporan owner — koreksi Header V2

Dropdown sebelumnya memakai jarak logo dan media yang berbeda, serta desktop
masih menyisakan empat slot submenu. Sekarang satu unit spacing mengatur jarak
logo/media ke pinggir, logo ke media, dan media ke bawah panel. Media tetap
landscape 3:2; submenu mengikuti isi sebenarnya.

Menu utama menampilkan Language (Bahasa/label Arab mengikuti bahasa aktif).
Klik membuka tiga bendera di tengah layar dengan blur dan warna backdrop dari
resources_old. Ukuran bendera berubah mengikuti lebar layar, bahasa aktif memakai
ring/border kuning lama. Tidak ada panel/card, heading atau tombol X.
Escape atau ketuk area blur menutup overlay; klik bendera mengganti bahasa.
Jika browser tidak mendukung blur, backdrop transparan gelap dan pilihan bahasa
masih berfungsi. Label pilihan tetap tersedia bagi screen reader.

Cursor memilih karakter cwo/cwe sekali ketika halaman dimulai. Karakter itu
berganti hanya antara default dan interactive saat menunjuk link/tombol/menu.
Pada touch-only cursor tambahan tidak muncul. Ketika aset gagal, pointer native
masih tersedia. Header dan Language tidak mengatur internal cursor.

99 kombinasi ukuran/bahasa Chromium, WebKit dan Firefox lolos untuk spacing dan
Language. Edge juga lolos smoke desktop/mobile viewport. Identitas cursor stabil
pada 10 siklus hover, termasuk di dalam overlay. Sound lolos regresi lifecycle.
Build, struktur, Pint, delapan tes Node dan tujuh tes V2 PHP lolos.
Suite PHP penuh masih mempunyai 71 kegagalan dan 75 error pada referensi legacy.
Safari perangkat asli, PageSpeed/CWV dan audit aksesibilitas manual penuh belum
terverifikasi. Rincian ada di [proof](../proof/landing-v2-status.md).

Yang perlu dilihat owner: keseimbangan media/submenu, rhythm di tablet landscape,
besar bendera dan rasa blur pada HP, serta posisi gambar cursor terhadap pointer.
Preview: [menu](../proof/rhythm-menu.png), [bahasa](../proof/rhythm-language.png).

PR #64 tetap draft. Main tidak diubah. Hero tetap NOT CLOSED sampai review UI owner.
