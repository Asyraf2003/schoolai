/* =========================================================
   SEKOLAH CERIA NUSANTARA — SCRIPT.JS
   Daftar isi:
   1. Tahun berjalan di footer
   2. Mobile hamburger menu
   3. Smooth scroll menu + nav aktif saat scroll
   4. Navbar berubah saat discroll
   5. Filter ekstrakurikuler
   6. Galeri halaman khusus
   7. Tombol scroll-to-top
   8. Animasi reveal saat elemen masuk viewport
   9. Tilt halus pada ilustrasi hero (opsional)
   ========================================================= */

import './welcome/navigation.js';
import './welcome/public-content.js';
import './welcome/gallery-wall.js';
import './welcome/lazy-media.js';
import './welcome/about-video-modal.js';
import { scheduleHomepagePreparation } from './welcome/preparation.js';

scheduleHomepagePreparation();
