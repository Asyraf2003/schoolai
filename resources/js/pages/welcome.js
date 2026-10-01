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

function scheduleTestimonialWall() {
    var root = document.querySelector('[data-testimonial-wall]');
    if (!root) return;

    var started = false;
    var observer = null;

    function start() {
        if (started) return;
        started = true;
        if (observer) observer.disconnect();

        import('./welcome/testimonial-wall.js').catch(function (error) {
            console.warn('Homepage testimonial preparation failed.', error);
        });
    }

    if (!('IntersectionObserver' in window)) {
        window.setTimeout(start, 0);
        return;
    }

    observer = new IntersectionObserver(function (entries) {
        if (entries.some(function (entry) { return entry.isIntersecting; })) {
            start();
        }
    }, { rootMargin: '150% 0px' });

    observer.observe(root);
}

function scheduleCursor() {
    if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
    const start = () => import('./welcome/cursor.js')
        .then(({ initHomepageCursor }) => initHomepageCursor())
        .catch(error => console.warn('Cursor enhancement unavailable.', error));
    const idle = () => {
        if ('requestIdleCallback' in window) window.requestIdleCallback(start, { timeout: 2000 });
        else window.setTimeout(start, 0);
    };
    window.requestAnimationFrame(() => window.requestAnimationFrame(idle));
}

scheduleCursor();
scheduleHomepagePreparation();
scheduleTestimonialWall();
