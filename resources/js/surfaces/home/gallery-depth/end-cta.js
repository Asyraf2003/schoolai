export class DepthGalleryEndCta {
    constructor(root, scroll) {
        this.root = root;
        this.scroll = scroll;
        this.journey = root.querySelector('[data-depth-gallery-journey]');
        this.viewport = root.querySelector('[data-depth-gallery-viewport]');
        this.link = root.querySelector('[data-depth-gallery-end-link]');
        this.progress = 0;
        this.transitionProgress = 0;
    }

    init() {
        this.reset();
    }

    readTransitionProgress() {
        if (!this.journey || !this.viewport) return 0;

        const transitionDistance = this.scroll.getTransitionDistance();
        if (transitionDistance <= 0) return 0;

        const fullTravel = Math.max(
            1,
            this.journey.offsetHeight - this.viewport.clientHeight,
        );
        const galleryTravel = Math.max(1, fullTravel - transitionDistance);
        const rawScroll = Math.max(
            0,
            -this.journey.getBoundingClientRect().top,
        );

        return clamp((rawScroll - galleryTravel) / transitionDistance);
    }

    update() {
        if (!this.link) return;

        this.progress = smoothProgress(this.scroll.endProgress);
        this.transitionProgress = this.readTransitionProgress();

        const focused = this.link.contains(document.activeElement);
        const visibleProgress = focused
            ? Math.max(this.progress, 0.78)
            : this.progress;
        const interactive = (
            (this.progress >= 0.7 && this.transitionProgress < 0.12)
            || focused
        );
        const transitionScale = 1 - this.transitionProgress;

        this.root.style.setProperty(
            '--depth-end-progress',
            visibleProgress.toFixed(4),
        );
        this.root.style.setProperty(
            '--depth-label-opacity',
            (1 - this.progress).toFixed(4),
        );
        this.root.style.setProperty(
            '--depth-transition-progress',
            this.transitionProgress.toFixed(4),
        );
        this.root.style.setProperty(
            '--depth-transition-scale',
            transitionScale.toFixed(4),
        );
        this.root.classList.toggle('is-depth-end-ready', interactive);
        this.root.classList.toggle(
            'is-depth-transitioning',
            this.transitionProgress > 0.001,
        );

        this.link.tabIndex = interactive ? 0 : -1;
        this.link.setAttribute(
            'aria-hidden',
            interactive ? 'false' : 'true',
        );
    }

    isVisible() {
        return this.progress >= 0.5 && this.transitionProgress < 0.995;
    }

    reset() {
        this.progress = 0;
        this.transitionProgress = 0;
        this.root.style.removeProperty('--depth-end-progress');
        this.root.style.removeProperty('--depth-label-opacity');
        this.root.style.removeProperty('--depth-transition-progress');
        this.root.style.removeProperty('--depth-transition-scale');
        this.root.classList.remove(
            'is-depth-end-ready',
            'is-depth-transitioning',
        );

        if (!this.link) return;
        this.link.removeAttribute('aria-hidden');
        this.link.removeAttribute('tabindex');
    }

    dispose() {
        this.reset();
    }
}

function clamp(value) {
    return Math.min(1, Math.max(0, value));
}

function smoothProgress(value) {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
}
