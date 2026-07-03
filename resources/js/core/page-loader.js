// Loader halaman: module hanya di-import ketika body[data-page] cocok. Tidak semua DOM logic dipaksa hidup global.
const pageModules = {
    welcome: () => import('../pages/welcome.js'),
    authLogin: () => import('../pages/auth-login.js'),
    dashboard: () => import('../pages/dashboard.js'),
};

export function loadPage(pageName) {
    if (! pageName || ! pageModules[pageName]) {
        return;
    }

    pageModules[pageName]()
        .then((module) => module.mount?.())
        .catch((error) => {
            console.error(`[SchoolAI] Gagal memuat module halaman: ${pageName}`, error);
        });
}
