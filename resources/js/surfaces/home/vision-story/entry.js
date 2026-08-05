export function createTypographyEntry(root, wide, timelineFor) {
    const visionTitle = root.querySelector(
        '[data-vision-copy="vision"] .vision-paper__kicker',
    );
    let wideArmed = false;
    let visionArmed = false;
    let lastRootTop = Number.POSITIVE_INFINITY;
    let lastVisionTop = Number.POSITIVE_INFINITY;

    function syncWide(timeline, rootTop, direction, initial) {
        if (initial) {
            if (rootTop > window.innerHeight) {
                timeline.resetTypography();
                wideArmed = true;
            } else {
                timeline.showTypography();
                wideArmed = false;
            }
            return;
        }

        if (direction === 'up') {
            timeline.showTypography();
            wideArmed = false;
            return;
        }

        if (rootTop > window.innerHeight) {
            if (!wideArmed) timeline.resetTypography();
            wideArmed = true;
            return;
        }

        if (
            wideArmed
            && direction === 'down'
            && lastRootTop > window.innerHeight
            && rootTop <= window.innerHeight
        ) {
            timeline.playTypography();
            wideArmed = false;
        }
    }

    function syncCompact(timeline, direction, initial) {
        if (!visionTitle) return;
        const top = visionTitle.getBoundingClientRect().top;

        if (initial) {
            if (top > window.innerHeight) {
                timeline.resetVisionTypography();
                visionArmed = true;
            } else {
                timeline.showVisionTypography();
                visionArmed = false;
            }
            lastVisionTop = top;
            return;
        }

        if (direction === 'up') {
            timeline.showVisionTypography();
            visionArmed = false;
            lastVisionTop = top;
            return;
        }

        if (top > window.innerHeight) {
            if (!visionArmed && direction === 'down') {
                timeline.resetVisionTypography();
                visionArmed = true;
            }
        } else if (
            visionArmed
            && direction === 'down'
            && lastVisionTop > window.innerHeight
        ) {
            timeline.playVisionTypography();
            visionArmed = false;
        }

        lastVisionTop = top;
    }

    function sync(direction = 'initial', initial = false) {
        const timeline = timelineFor();
        if (!timeline) return;

        const rootTop = root.getBoundingClientRect().top;

        if (wide.matches) {
            syncWide(timeline, rootTop, direction, initial);
        } else {
            syncCompact(timeline, direction, initial);
        }

        lastRootTop = rootTop;
    }

    return { sync };
}
