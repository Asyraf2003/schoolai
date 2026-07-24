import { clamp, easeOutCubic, lerp, setNumberProperty, setPixelProperty, smootherstep } from './core.js';

export function createSceneRenderer(context, layout) {
    var root = context.root;
    var statElements = context.statElements;
    var state = context.state;
    var resolveAboutSide = layout.resolveAboutSide;
    var sideForStat = layout.sideForStat;
    var updateActiveIndex = layout.updateActiveIndex;

        function renderScene(progress) {
            if (!state.enhanced) return;

            var viewportWidth = Math.max(
                window.innerWidth || 0,
                document.documentElement.clientWidth || 0,
                1
            );
            var viewportHeight = Math.max(
                window.innerHeight || 0,
                document.documentElement.clientHeight || 0,
                1
            );
            var timelinePosition = progress * context.timeline.total;
            var intro = smootherstep(0, context.timeline.introEnd, timelinePosition);
            var firstShift = smootherstep(
                context.timeline.introEnd,
                context.timeline.firstShiftEnd,
                timelinePosition
            );
            var outro = smootherstep(
                context.timeline.outroStart,
                context.timeline.total,
                timelinePosition
            );
            var aboutSide = resolveAboutSide(timelinePosition);
            var aboutX = aboutSide * viewportWidth * 0.225;
            var aboutY = lerp(
                viewportHeight * 0.26,
                0,
                easeOutCubic(intro)
            );
            var baseAboutScale = lerp(0.96, 1, intro) - firstShift * 0.12;
            var aboutOpacity = intro * (1 - outro);
            var storyStatProgress = clamp(
                (timelinePosition - context.timeline.firstStatStart) /
                    Math.max(context.timeline.lastExit - context.timeline.firstStatStart, 0.001),
                0,
                1
            );
            var activeIndex = -1;
            var activeSettle = 0;

            setPixelProperty(root, '--about-x', aboutX);
            setPixelProperty(root, '--about-y', aboutY);
            setNumberProperty(root, '--about-opacity', aboutOpacity);
            setNumberProperty(root, '--story-progress', storyStatProgress);
            setNumberProperty(
                root,
                '--progress-opacity',
                smootherstep(
                    context.timeline.firstShiftEnd - 0.12,
                    context.timeline.firstStatStart,
                    timelinePosition
                ) * (1 - outro)
            );

            statElements.forEach(function (statElement, index) {
                var phase = context.timeline.stats[index];
                var enter = phase
                    ? smootherstep(
                        phase.enterStart,
                        phase.enterEnd,
                        timelinePosition
                    )
                    : 0;
                var exit = phase
                    ? smootherstep(
                        phase.holdEnd,
                        phase.exitEnd,
                        timelinePosition
                    )
                    : 1;
                var presence = enter * (1 - exit) * (1 - outro);
                var x = sideForStat(index) * viewportWidth * 0.18;
                var y =
                    (1 - enter) * viewportHeight * 0.32 -
                    exit * viewportHeight * 0.28;
                var scale =
                    lerp(0.94, 1, easeOutCubic(enter)) - exit * 0.045;
                var blur =
                    lerp(18, 0, easeOutCubic(enter)) + exit * 9;
                var settle = 0;

                if (phase) {
                    var settleIn = smootherstep(
                        phase.enterEnd,
                        phase.settleEnd,
                        timelinePosition
                    );
                    var settleOut = smootherstep(
                        phase.holdEnd - 0.22,
                        phase.holdEnd,
                        timelinePosition
                    );
                    var holdProgress = clamp(
                        (timelinePosition - phase.enterEnd) /
                            Math.max(phase.holdEnd - phase.enterEnd, 0.001),
                        0,
                        1
                    );
                    var breathe = 0.55 + 0.45 * Math.sin(holdProgress * Math.PI);

                    settle = settleIn * (1 - settleOut) * breathe;
                }

                if (
                    phase &&
                    timelinePosition >= phase.enterStart &&
                    timelinePosition < phase.exitEnd
                ) {
                    activeIndex = index;
                    activeSettle = Math.max(activeSettle, settle);
                }

                setNumberProperty(statElement, '--stat-opacity', presence);
                setPixelProperty(statElement, '--stat-x', x);
                setPixelProperty(statElement, '--stat-y', y);
                setNumberProperty(statElement, '--stat-scale', scale);
                setPixelProperty(statElement, '--stat-blur', blur);
                setNumberProperty(statElement, '--stat-settle', settle);
            });

            setNumberProperty(
                root,
                '--about-scale',
                Math.max(baseAboutScale + activeSettle * 0.008, 0.84)
            );
            updateActiveIndex(activeIndex);
        }

    return renderScene;
}
