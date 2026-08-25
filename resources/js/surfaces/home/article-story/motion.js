const HORIZONTAL_END = 0.78;

const clamp = (value, min = 0, max = 1) => (
    Math.min(max, Math.max(min, value))
);

function smoothProgress(value) {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
}

function paintMainMotion(mainItems, trackX) {
    const viewportWidth = Math.max(1, window.innerWidth);
    const viewportHeight = Math.max(1, window.innerHeight);
    const viewportCenter = viewportWidth * 0.5;
    const mediaWidth = viewportHeight + viewportWidth * 0.10;
    const copyTopMargin = viewportHeight / 24;

    mainItems.forEach((item, index) => {
        const itemLeft = item.offsetLeft + trackX;
        const mediaCenter = itemLeft + mediaWidth * 0.5;
        const mediaRight = itemLeft + mediaWidth;
        const relative = clamp(
            (mediaCenter - viewportCenter) / viewportWidth,
            -1,
            1,
        );
        const mediaX = clamp(relative * 8, -8, 8);
        const copy = item.querySelector('[data-article-main-copy]');

        item.style.setProperty(
            '--article-main-media-x',
            `${mediaX.toFixed(3)}%`,
        );

        if (!copy) return;

        const copyProgress = smoothProgress(clamp(
            ((viewportWidth * 0.95) - mediaRight) / (viewportWidth * 0.40),
        ));
        const centeredTop = viewportHeight * 0.5 - copy.offsetHeight * 0.5;
        const targetTop = index % 2 === 0
            ? copyTopMargin
            : viewportHeight - copyTopMargin - copy.offsetHeight;
        const copyY = (targetTop - centeredTop) * copyProgress;

        item.style.setProperty(
            '--article-main-copy-y',
            `${copyY.toFixed(2)}px`,
        );
    });
}

export function paintArticleMotion({
    cta, mainItems, progress, rollDistance, root, trackDistance,
}) {
    const horizontalProgress = smoothProgress(clamp(progress / HORIZONTAL_END));
    const rollProgress = smoothProgress(clamp(
        (progress - HORIZONTAL_END) / (1 - HORIZONTAL_END),
    ));
    const trackX = -trackDistance * horizontalProgress;

    root.style.setProperty('--article-track-x', `${trackX.toFixed(2)}px`);
    paintMainMotion(mainItems, trackX);
    root.style.setProperty(
        '--article-roll-y',
        `${(-rollDistance * rollProgress).toFixed(2)}px`,
    );

    const ctaReady = horizontalProgress > 0.995 && rollProgress > 0.12;
    root.classList.toggle('is-article-cta-ready', ctaReady);
    if (cta) cta.tabIndex = ctaReady ? 0 : -1;
}
