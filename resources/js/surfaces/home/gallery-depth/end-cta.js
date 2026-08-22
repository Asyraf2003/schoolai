export class DepthGalleryEndCta {
    constructor(root, scroll) {
        this.root = root;
        this.scroll = scroll;
        this.link = root.querySelector('[data-depth-gallery-end-link]');
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
            ? Math.max(this.progress, 0.78)
            : this.progress;
        const interactive = this.progress >= 0.7 || focused;

        this.root.style.setProperty(
            '--depth-end-progress',
            visibleProgress.toFixed(4),
        );
        this.root.style.setProperty(
            '--depth-label-opacity',
            (1 - this.progress).toFixed(4),
        );
        this.root.classList.toggle('is-depth-end-ready', interactive);
        this.link.tabIndex = interactive ? 0 : -1;
        this.link.setAttribute(
            'aria-hidden',
            interactive ? 'false' : 'true',
        );
    }

    isVisible() {
        return this.progress >= 0.5;
    }

    reset() {
        this.progress = 0;
        this.root.style.removeProperty('--depth-end-progress');
        this.root.style.removeProperty('--depth-label-opacity');
        this.root.classList.remove('is-depth-end-ready');

        if (!this.link) return;
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
