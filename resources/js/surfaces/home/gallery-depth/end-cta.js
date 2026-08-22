export class DepthGalleryEndCta {
    constructor(root, scroll) {
        this.root = root;
        this.scroll = scroll;
        this.link = root.querySelector('[data-depth-gallery-end-link]');
        this.progress = 0;
        this.transitionProgress = 0;
    }

    init() {
        this.reset();
    }

    readTransitionProgress() {
        return this.scroll.getTransitionProgress();
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
        const swingProgress = readSwingProgress(this.transitionProgress);
        const riseProgress = clamp(
            (this.transitionProgress - 0.30) / 0.70,
        );
        const transitionScale = 1 - riseProgress;
        const mediaOneRotation = -3 - 17 * swingProgress;
        const mediaTwoRotation = 4 + 16 * swingProgress;

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
        this.root.style.setProperty(
            '--depth-end-media-1-rotation',
            `${mediaOneRotation.toFixed(3)}deg`,
        );
        this.root.style.setProperty(
            '--depth-end-media-2-rotation',
            `${mediaTwoRotation.toFixed(3)}deg`,
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
        this.root.style.removeProperty('--depth-end-media-1-rotation');
        this.root.style.removeProperty('--depth-end-media-2-rotation');
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

function readSwingProgress(progress) {
    if (progress <= 0.15) {
        return smoothProgress(progress / 0.15);
    }
    if (progress <= 0.30) {
        return 1 - smoothProgress((progress - 0.15) / 0.15);
    }
    return 0;
}

function clamp(value) {
    return Math.min(1, Math.max(0, value));
}

function smoothProgress(value) {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
}
