import { ready } from './core/dom.js';
import { loadPage } from './core/page-loader.js';

// Entry JS Vite. Ambil nama halaman dari body[data-page], lalu load module halaman yang relevan saja.
ready(() => {
    loadPage(document.body?.dataset?.page);
});
