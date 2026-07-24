import { clamp, lerp, setNumberProperty, setPixelProperty, smootherstep } from './core.js';

export function createScrollActions(context) {
    var root = context.root;
    var track = context.track;
    var nodes = context.nodes;
    var state = context.state;

        function setStoryHeight() {
            var viewportHeight = Math.max(
                window.innerHeight || 0,
                document.documentElement.clientHeight || 0,
                640
            );
            var screens = Math.max(4.8, 4.2 + nodes.length * 0.34);

            root.style.setProperty(
                '--testimonial-story-height',
                Math.round(viewportHeight * screens) + 'px'
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
            var shrink = smootherstep(0.08, 0.34, progress);
            var titleLift = smootherstep(0.1, 0.39, progress);
            var targetWidth = Math.min(
                viewportWidth * 0.42,
                viewportHeight * 0.82,
                720
            );
            var targetHeight = targetWidth * 9 / 16;
            var mediaWidth = lerp(viewportWidth, targetWidth, shrink);
            var mediaHeight = lerp(viewportHeight, targetHeight, shrink);
            var mediaRadius = lerp(0, 28, shrink);
            var titleTop = lerp(
                viewportHeight * 0.6,
                Math.max(150, viewportHeight * 0.2),
                titleLift
            );
            var titleScale = lerp(1, 0.5, titleLift);

            setPixelProperty(root, '--testimonial-media-width', mediaWidth);
            setPixelProperty(root, '--testimonial-media-height', mediaHeight);
            setPixelProperty(root, '--testimonial-media-radius', mediaRadius);
            setPixelProperty(root, '--testimonial-title-top', titleTop);
            setNumberProperty(root, '--testimonial-title-scale', titleScale);

            nodes.forEach(function (node, index) {
                var nodeStart = 0.35 + index * 0.066;
                var nodeEnd = nodeStart + 0.09;
                var presence = smootherstep(nodeStart, nodeEnd, progress);
                var y = lerp(36, 0, presence);
                var scale = lerp(0.8, 1, presence);

                setNumberProperty(node, '--node-opacity', presence);
                setPixelProperty(node, '--node-y', y);
                setNumberProperty(node, '--node-scale', scale);
            });
        }

        function stopProgressLoop() {
            if (state.progressFrame !== null) {
                window.cancelAnimationFrame(state.progressFrame);
                state.progressFrame = null;
            }

            state.lastFrameTime = 0;
        }

        function progressLoop(timestamp) {
            if (!state.enhanced) {
                stopProgressLoop();
                return;
            }

            var deltaFrames = state.lastFrameTime
                ? clamp((timestamp - state.lastFrameTime) / 16.667, 0.5, 4)
                : 1;
            var smoothing = 1 - Math.pow(0.935, deltaFrames);
            var difference = state.targetProgress - state.renderedProgress;

            state.lastFrameTime = timestamp;
            state.renderedProgress += difference * smoothing;

            if (Math.abs(difference) < 0.00008) {
                state.renderedProgress = state.targetProgress;
            }

            renderScene(state.renderedProgress);

            if (state.renderedProgress !== state.targetProgress) {
                state.progressFrame = window.requestAnimationFrame(progressLoop);
                return;
            }

            state.progressFrame = null;
            state.lastFrameTime = 0;
        }

        function startProgressLoop() {
            if (!state.enhanced || state.progressFrame !== null) return;
            state.progressFrame = window.requestAnimationFrame(progressLoop);
        }

        function syncScrollTarget(immediate) {
            if (!state.enhanced) return;

            state.targetProgress = readScrollProgress();

            if (immediate) {
                stopProgressLoop();
                state.renderedProgress = state.targetProgress;
                renderScene(state.renderedProgress);
                return;
            }

            startProgressLoop();
        }

        function onDesktopScroll() {
            syncScrollTarget(false);
        }

        function onDesktopResize() {
            window.clearTimeout(state.resizeTimer);
            state.resizeTimer = window.setTimeout(function () {
                setStoryHeight();
                syncScrollTarget(true);
            }, 120);
        }

    return { onDesktopResize, onDesktopScroll, setStoryHeight, stopProgressLoop, syncScrollTarget };
}
