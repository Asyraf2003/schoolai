# Laporan owner — nav lebih rapat / satu hover / motion lambat

Tinggi nav desktop sekarang maksimal 10vh. Logo, menu, Sound dan Language berada
di tengah frame itu. Ukuran logo tetap seperti sebelumnya ketika muat; pada
viewport sangat pendek logo dibatasi agar tetap berada di dalam nav. Spacing
samping, media dan gap logo→media tetap sama. Yang dipangkas adalah ruang vertikal
atas; bukan mengecilkan semua spacing lagi.

H1, deskripsi dan CTA berbagi satu hover tone. Pada PPDB ketiganya memakai satu
anchor/tujuan yang sama. Hover salah satunya membuat semua sedikit lebih gelap;
underline dan garis CTA dihapus. Eyebrow berada di luar tiga bagian tersebut.
Tujuan lain dari sumber existing tetap dipertahankan.

Tablet portrait bermedia sekarang juga mempunyai frame empat slot: tinggi frame
sama dengan media, setiap item hanya mendapat 1/4, sisa slot kosong. Lebar media
portrait tetap sama; typography mengikuti kapasitas slot. HP tetap tanpa media
dan barisnya mengikuti isi seperti sebelumnya.

Expand/collapse berubah dari320ms menjadi1440ms, 4.5× lebih lama, dengan easing
awal/akhir lembut. Compact memakai tinggi nyata, desktop memakai reveal vertikal
panel sehingga tinggi nav tetap stabil. Dropdown lama selesai collapse sebelum
baru expand. Klik ulang meneruskan gerakan dari posisi yang sedang terlihat.
Header menjaga theme selama panel closing, kemudian kembali setelah closed;
reduced motion, resize, hidden menu dan disposal menyelesaikan state dengan aman.

99 kasus ukuran/locale Chromium, Firefox dan WebKit lolos. Sembilan kasus center,
sembilan hover Hero dan sembilan motion normal lolos. Edge smoke juga lolos.
Regresi video asli/Language, cursor dan Sound tetap lolos. Build, struktur255,
Pint, 10 tes Node dan tujuh tes V2 PHP/46 assertions lolos. Suite PHP penuh masih
mempunyai71 kegagalan dan75 error legacy. Native Safari dan sertifikasi manual/
PageSpeed/CWV belum diverifikasi.

Owner perlu melihat rasa motion1.44detik, besar font slot tablet, dan tinggi nav.
[Menu](../proof/nav-frame-menu.png), [Language](../proof/nav-frame-language.png),
[bukti](../proof/header-nav-frame.json). PR64 tetap draft, tidak merge.
Hero NOT CLOSED sampai review UI berikutnya.
