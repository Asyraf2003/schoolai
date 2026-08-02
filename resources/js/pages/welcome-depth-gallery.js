const root = document.querySelector('[data-depth-gallery]');

if (root) {
    let loaded = false;
    const load = () => {
        if (loaded) return;
        loaded = true;
        import('../surfaces/home/gallery-depth/controller.js')
            .then(({ mountHomepageDepthGallery }) => mountHomepageDepthGallery())
            .catch(() => root.classList.add('is-depth-fallback'));
    };

    if (!('IntersectionObserver' in window)) {
        load();
    } else {
        const observer = new IntersectionObserver((entries) => {
            if (!entries.some((entry) => entry.isIntersecting)) return;
            observer.disconnect();
            load();
        }, { rootMargin: '100% 0px 100% 0px', threshold: 0.01 });
        observer.observe(root);
    }
}
