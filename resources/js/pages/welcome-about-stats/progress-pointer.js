import { clamp, setPixelProperty } from './core.js';

export function createProgressPointerActions(context, layout, renderScene) {
    var root = context.root;
    var sticky = context.sticky;
    var state = context.state;
    var readScrollProgress = layout.readScrollProgress;

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
            var smoothing = 1 - Math.pow(0.96, deltaFrames);
            var difference = state.targetProgress - state.renderedProgress;

            state.lastFrameTime = timestamp;
            state.renderedProgress += difference * smoothing;

            if (Math.abs(difference) < 0.00006) {
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

        function renderPointer() {
            state.pointerFrame = null;
            if (!state.enhanced) return;

            var viewportWidth = Math.max(window.innerWidth || 1, 1);
            var viewportHeight = Math.max(window.innerHeight || 1, 1);
            var normalizedX = clamp(
                (state.pointerX / viewportWidth - 0.5) * 2,
                -1,
                1
            );
            var normalizedY = clamp(
                (state.pointerY / viewportHeight - 0.5) * 2,
                -1,
                1
            );

            setPixelProperty(root, '--ambient-x', normalizedX * 9);
            setPixelProperty(root, '--ambient-y', normalizedY * 7);
            setPixelProperty(root, '--ambient-x-reverse', normalizedX * -7);
            setPixelProperty(root, '--ambient-y-reverse', normalizedY * -5);
        }

        function requestPointerRender(event) {
            if (!state.enhanced || event.pointerType === 'touch') return;

            state.pointerX = event.clientX;
            state.pointerY = event.clientY;

            if (state.pointerFrame !== null) return;
            state.pointerFrame = window.requestAnimationFrame(renderPointer);
        }

        function resetPointer() {
            if (state.pointerFrame !== null) {
                window.cancelAnimationFrame(state.pointerFrame);
                state.pointerFrame = null;
            }

            root.style.setProperty('--ambient-x', '0px');
            root.style.setProperty('--ambient-y', '0px');
            root.style.setProperty('--ambient-x-reverse', '0px');
            root.style.setProperty('--ambient-y-reverse', '0px');
        }

        function listenForPointer() {
            if (state.pointerListening) return;

            sticky.addEventListener('pointermove', requestPointerRender, {
                passive: true
            });
            sticky.addEventListener('pointerleave', resetPointer, {
                passive: true
            });
            state.pointerListening = true;
        }

        function stopListeningForPointer() {
            if (!state.pointerListening) return;

            sticky.removeEventListener('pointermove', requestPointerRender);
            sticky.removeEventListener('pointerleave', resetPointer);
            state.pointerListening = false;
            resetPointer();
        }

    return { stopProgressLoop, syncScrollTarget, listenForPointer, stopListeningForPointer };
}
