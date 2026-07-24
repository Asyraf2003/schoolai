import { clamp, lerp, smootherstep } from './core.js';

export function createLayoutActions(context) {
    var root = context.root;
    var track = context.track;
    var progressDots = context.progressDots;
    var state = context.state;

        function sideForAbout(index) {
            if (index < 0) return 0;
            return (index % 2 === 0 ? 1 : -1) * state.direction;
        }

        function sideForStat(index) {
            return -sideForAbout(index);
        }

        function updateActiveIndex(index) {
            if (state.activeIndex === index) return;

            state.activeIndex = index;
            root.setAttribute(
                'data-active-stat',
                index >= 0 ? String(index) : ''
            );

            progressDots.forEach(function (dot, dotIndex) {
                dot.classList.toggle('is-active', dotIndex === index);
            });
        }

        function setStoryHeight() {
            var viewportHeight = Math.max(
                window.innerHeight || 0,
                document.documentElement.clientHeight || 0,
                640
            );
            var screenCount = Math.max(
                7.2,
                1 + context.timeline.total * 0.31
            );

            root.style.setProperty(
                '--story-scroll-height',
                Math.round(viewportHeight * screenCount) + 'px'
            );
        }

        function readScrollProgress() {
            var viewportHeight = Math.max(
                window.innerHeight || 0,
                document.documentElement.clientHeight || 0,
                1
            );
            var rect = track.getBoundingClientRect();
            var scrollRange = Math.max(track.offsetHeight - viewportHeight, 1);

            return clamp(-rect.top / scrollRange, 0, 1);
        }

        function resolveAboutSide(timelinePosition) {
            var resolvedSide = 0;

            for (var index = 0; index < context.timeline.shifts.length; index += 1) {
                var shift = context.timeline.shifts[index];
                var fromSide = sideForAbout(shift.fromIndex);
                var toSide = sideForAbout(shift.toIndex);

                if (timelinePosition < shift.start) {
                    break;
                }

                if (timelinePosition <= shift.end) {
                    var shiftProgress = smootherstep(
                        shift.start,
                        shift.end,
                        timelinePosition
                    );

                    return lerp(fromSide, toSide, shiftProgress);
                }

                resolvedSide = toSide;
            }

            return resolvedSide;
        }

    return { sideForStat, updateActiveIndex, setStoryHeight, readScrollProgress, resolveAboutSide };
}
