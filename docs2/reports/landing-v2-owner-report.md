# Laporan owner — koreksi frame, state dan motion

Spacing desktop sekarang memakai 2/3 dari rhythm sebelumnya. Jarak logo dan
media ke pinggir, logo ke media, dan media ke bawah panel tetap satu sistem.
Media tetap 3:2. Frame submenu tepat setinggi media; setiap item mendapat satu
slot 1/4. Jika hanya satu sampai tiga item, slot sisanya kosong.

Menu utama bold, awalnya putih. Theme dark hanya mengikuti dropdown navigasi
putih atau state scroll legacy. Maksimal satu yellow line: hover sementara,
menu terbuka, lalu kembali ke halaman aktif. Submenu tidak punya line kuning;
hover/focus/klik memberi warna merah dan roll tetap tersedia pada desktop.

Language memiliki state sendiri. Membukanya hanya menampilkan blur dan tiga
bendera; dropdown yang sudah terbuka dan theme Header tetap dipertahankan.
Escape/backdrop close dan locale POST tetap berfungsi; tidak ada X.
Cursor dua state dengan identitas acak sekali dan Sound sebelumnya dipertahankan.

Pada tablet/HP, dropdown lama collapse dahulu, kemudian yang baru expand,
320ms berbasis tinggi aktual. Klik cepat membalik transisi dari tinggi yang sedang
terlihat; resize/reduced motion menyelesaikan state dengan aman. Radius media
rectangle mengikuti tinggi/15, sementara bendera mempertahankan bentuk lama.

Video tidak lagi pause karena fokus/klik Hero atau Language. Media adapter hanya
menerapkan perubahan policy nyata dan tidak menulis ulang muted/loop jika sama.
Hero menggunakan IntersectionObserver threshold0: sedikit bagian masih terlihat
berarti video terus berjalan; saat sepenuhnya keluar viewport video pause,
kemudian resume saat kembali. Source tidak di-unload. Document hidden/pagehide
dan preferensi reduced motion tetap dihormati. Fokus hanya menahan pergantian
slide otomatis, agar tautan yang sedang digunakan tidak berpindah.

Bukti: 99 kombinasi geometry/state/locale pada Chromium, Firefox dan WebKit,
enam motion accordion normal, dan video asli klik/overlay/menu/offscreen/resume
pada tiga engine lolos. Edge smoke juga lolos. Cursor dan Sound lolos regresi.
Build, struktur255, Pint, 10 tes Node dan tujuh tes V2 PHP/46 assertions lolos.
Suite PHP penuh masih gagal pada 71 kasus dan 75 error legacy; tidak dibersihkan.
Native Safari, audit manual penuh dan PageSpeed/CWV belum disertifikasi.

Owner perlu meninjau ukuran font di slot 1/4, keseimbangan desktop yang lebih
rapat dan kecepatan accordion. [Menu](../proof/slots-menu.png),
[Language](../proof/slots-language.png), [bukti teknis](../proof/header-slots-lifecycle.json).
PR #64 tetap draft, tidak merge. Hero NOT CLOSED sampai review UI berikutnya.
