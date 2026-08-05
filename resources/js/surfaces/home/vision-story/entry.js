const clamp = (value) => Math.max(0, Math.min(1, value));

function titleState(title) {
    if (!title) return { below: false, progress: 1 };
    const rect = title.getBoundingClientRect();
    const startTop = window.innerHeight;
    const endTop = (window.innerHeight - rect.height) / 2;
    return {
        below: rect.top > startTop,
        progress: clamp(
            (startTop - rect.top) / Math.max(1, startTop - endTop),
        ),
    };
}

export function createTypographyEntry(root, wide, timelineFor) {
    const visionTitle = root.querySelector(
        '[data-vision-copy="vision"] .vision-paper__kicker',
    );
    const missionTitle = root.querySelector(
        '[data-vision-copy="mission"] .vision-paper__kicker',
    );
    let wideArmed = false;
    let visionArmed = false;
    let missionArmed = false;
    let lastScrollY = window.scrollY;
    let lastTop = Number.POSITIVE_INFINITY;

    function showFinal(timeline) {
        timeline.showTypography();
        wideArmed = false;
        visionArmed = false;
        missionArmed = false;
    }

    function syncWide(timeline, top, movingDown, initial) {
        if (initial) {
            if (top > window.innerHeight) {
                timeline.resetTypography();
                wideArmed = true;
            } else {
                showFinal(timeline);
            }
            return;
        }
        if (top > window.innerHeight) {
            if (!wideArmed) timeline.resetTypography();
            wideArmed = true;
            return;
        }
        if (
            wideArmed
            && movingDown
            && lastTop > window.innerHeight
            && top <= window.innerHeight
        ) {
            timeline.playTypography();
            wideArmed = false;
        }
    }

    function syncTarget(state, armed, setProgress, movingDown, initial) {
        if (initial) {
            setProgress(state.below ? 0 : 1);
            return state.below;
        }
        if (state.below && !armed) {
            setProgress(0);
            return true;
        }
        if (!movingDown || !armed) return armed;
        setProgress(state.progress);
        return state.progress < 1;
    }

    function syncCompact(timeline, movingDown, initial) {
        visionArmed = syncTarget(
            titleState(visionTitle),
            visionArmed,
            (progress) => timeline.setVisionTypographyProgress(progress),
            movingDown,
            initial,
        );
        missionArmed = syncTarget(
            titleState(missionTitle),
            missionArmed,
            (progress) => timeline.setMissionTypographyProgress(progress),
            movingDown,
            initial,
        );
    }

    function sync(initial = false) {
        const timeline = timelineFor();
        if (!timeline) return;
        const top = root.getBoundingClientRect().top;
        const scrollY = window.scrollY;
        const movingDown = scrollY > lastScrollY + 0.5;
        const movingUp = scrollY < lastScrollY - 0.5;

        if (movingUp) {
            showFinal(timeline);
        } else if (wide.matches) {
            syncWide(timeline, top, movingDown, initial);
        } else {
            syncCompact(timeline, movingDown, initial);
        }
        lastScrollY = scrollY;
        lastTop = top;
    }

    return { sync };
}
