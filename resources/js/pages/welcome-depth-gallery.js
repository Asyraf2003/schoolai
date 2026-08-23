let preparationPromise = null;

function mountWhenRelevant(root, mount) {
    let mounted = false;
    const run = () => {
        if (mounted) return;
        mounted = true;
        mount();
    };

    if (!('IntersectionObserver' in window)) {
        run();
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        if (!entries.some((entry) => entry.isIntersecting)) return;
        observer.disconnect();
        run();
    }, { rootMargin: '100% 0px 100% 0px', threshold: 0.01 });
    observer.observe(root);
}

export function prepareHomepageDepthGallery() {
    const root = document.querySelector('[data-depth-gallery]');
    if (!root) return Promise.resolve(null);
    if (preparationPromise) return preparationPromise;

    preparationPromise = Promise.all([
        import('../surfaces/home/gallery-depth/controller.js'),
        import('../surfaces/home/gallery-depth/three-runtime.js'),
    ])
        .then(async ([{ mountHomepageDepthGallery }, { loadThreeRuntime }]) => {
            mountWhenRelevant(root, mountHomepageDepthGallery);
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return null;
            }
            return loadThreeRuntime();
        })
        .catch((error) => {
            preparationPromise = null;
            root.classList.add('is-depth-fallback');
            console.warn('Depth gallery preparation failed.', error);
            return null;
        });

    return preparationPromise;
}
