import '../css/pages/welcome-testimonial-layout.css';
import { ready } from './core/dom.js';
import { loadPage } from './core/page-loader.js';
import { installTestimonialAdminNav } from './admin/testimonial-nav.js';

// Entry JS Vite. Ambil nama halaman dari body[data-page], lalu load module halaman yang relevan saja.
ready(() => {
    installTestimonialAdminNav();
    loadPage(document.body?.dataset?.page);

    if (document.getElementById('galeri') && document.getElementById('artikel')) {
        import('./pages/welcome-testimonial-extra-nodes.js');
    }
});
