export class DepthGalleryEndCta {
    constructor(root, scroll) {
        this.root = root;
        this.scroll = scroll;
        this.link = root.querySelector('[data-depth-gallery-end-link]');
        this.articleSeed = root.querySelector(
            '[data-depth-gallery-article-seed]',
        );
        this.progress = 0;
    }

    init() {
        this.reset();
    }

    update() {
        if (!this.link) return;

        this.progress = smoothProgress(this.scroll.endProgress);
        const focused = this.link.contains(document.activeElement);
        const visibleProgress = focused
            ? Math.max(this.progress, 0.72)
            : this.progress;
        const translateY = (1 - visibleProgress) * 26;
        const scale = 0.78 + visibleProgress * 0.22;
        const interactive = this.progress >= 0.72 || focused;
        const seedProgress = smoothProgress(
            Math.max(0, (this.progress - 0.42) / 0.58),
        );

        this.root.style.setProperty(
            '--depth-end-progress',
            this.progress.toFixed(4),
        );
        this.root.style.setProperty(
            '--depth-label-opacity',
            (1 - this.progress).toFixed(4),
        );
        this.root.style.setProperty(
            '--depth-article-seed-opacity',
            seedProgress.toFixed(4),
        );
        this.root.style.setProperty(
            '--depth-article-seed-scale',
            (0.72 + seedProgress * 0.28).toFixed(4),
        );
        this.root.classList.toggle('is-depth-end-ready', interactive);
        this.link.style.opacity = visibleProgress.toFixed(4);
        this.link.style.transform = [
            `translate3d(0, ${translateY.toFixed(2)}px, 0)`,
            `scale(${scale.toFixed(4)})`,
        ].join(' ');
        this.link.tabIndex = interactive ? 0 : -1;
        this.link.setAttribute(
            'aria-hidden',
            interactive ? 'false' : 'true',
        );
    }

    isVisible() {
        return this.progress >= 0.58;
    }

    reset() {
        this.progress = 0;
        this.root.style.removeProperty('--depth-end-progress');
        this.root.style.removeProperty('--depth-label-opacity');
        this.root.style.removeProperty('--depth-article-seed-opacity');
        this.root.style.removeProperty('--depth-article-seed-scale');
        this.root.classList.remove('is-depth-end-ready');

        if (!this.link) return;
        this.link.style.removeProperty('opacity');
        this.link.style.removeProperty('transform');
        this.link.removeAttribute('aria-hidden');
        this.link.removeAttribute('tabindex');
    }

    dispose() {
        this.reset();
    }
}

function smoothProgress(value) {
    const progress = Math.min(1, Math.max(0, value));
    return progress * progress * (3 - 2 * progress);
}
