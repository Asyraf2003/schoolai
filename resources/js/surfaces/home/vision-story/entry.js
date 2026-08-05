export function createTypographyEntry(root, wide, timelineFor) {
    let armed = false;
    let lastTop = Number.POSITIVE_INFINITY;

    function sync(direction = 'initial', initial = false) {
        if (!wide.matches) return;

        const timeline = timelineFor();
        if (!timeline) return;
        const top = root.getBoundingClientRect().top;

        if (initial) {
            if (top > window.innerHeight) {
                timeline.resetTypography();
                armed = true;
            } else {
                timeline.showTypography();
                armed = false;
            }
            lastTop = top;
            return;
        }

        if (direction === 'up') {
            timeline.showTypography();
            armed = false;
            lastTop = top;
            return;
        }

        if (top > window.innerHeight) {
            if (!armed) timeline.resetTypography();
            armed = true;
        } else if (
            armed
            && direction === 'down'
            && lastTop > window.innerHeight
        ) {
            timeline.playTypography();
            armed = false;
        }

        lastTop = top;
    }

    return { sync };
}
