var DESKTOP_QUERY = '(min-width: 961px)';
var REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';
var REVEAL_ORDER = [2, 1, 3, 7, 8, 5, 6, 0, 4, 9, 10, 11];

function clamp(value, minimum, maximum) {
        return Math.min(Math.max(value, minimum), maximum);
    }

function smootherstep(edgeStart, edgeEnd, value) {
        if (edgeStart === edgeEnd) return value < edgeStart ? 0 : 1;

        var progress = clamp((value - edgeStart) / (edgeEnd - edgeStart), 0, 1);
        return progress * progress * progress * (progress * (progress * 6 - 15) + 10);
    }

export function installPolishedTimeline(root, nodes) {
        var track = root.querySelector('[data-testimonial-track]');
        if (!track || !nodes.length) return;

        var desktopMedia = window.matchMedia(DESKTOP_QUERY);
        var reducedMotionMedia = window.matchMedia(REDUCED_MOTION_QUERY);
        var frame = null;

        root.classList.add('is-testimonial-polished');

        function setStoryHeight() {
            var viewportHeight = Math.max(
                window.innerHeight || 0,
                document.documentElement.clientHeight || 0,
                640
            );
            var screens = Math.max(6.2, 4.7 + nodes.length * 0.23);

            root.style.setProperty(
                '--testimonial-polished-height',
                Math.round(viewportHeight * screens) + 'px'
            );
        }

        function readProgress() {
            var viewportHeight = Math.max(
                window.innerHeight || 0,
                document.documentElement.clientHeight || 0,
                1
            );
            var rect = track.getBoundingClientRect();
            var range = Math.max(track.offsetHeight - viewportHeight, 1);
            return clamp(-rect.top / range, 0, 1);
        }

        function sequenceForIndex(index) {
            var orderIndex = REVEAL_ORDER.indexOf(index);
            return orderIndex >= 0 ? orderIndex : index;
        }

        function render() {
            frame = null;

            if (!desktopMedia.matches || reducedMotionMedia.matches) {
                nodes.forEach(function (node) {
                    node.style.setProperty('--polish-opacity', '1');
                    node.style.setProperty('--polish-x', '0px');
                    node.style.setProperty('--polish-y', '0px');
                    node.style.setProperty('--polish-scale', '1');
                    node.style.setProperty('--polish-rotate', '0deg');
                    node.style.pointerEvents = 'auto';
                    node.removeAttribute('aria-hidden');
                });
                return;
            }

            var progress = readProgress();
            var viewportWidth = Math.max(window.innerWidth || 0, 1);

            nodes.forEach(function (node, index) {
                var sequence = sequenceForIndex(index);
                var start = 0.34 + sequence * 0.043;
                var end = start + 0.095;
                var presence = smootherstep(start, end, progress);
                var rect = node.getBoundingClientRect();
                var nodeCenter = rect.left + rect.width / 2;
                var side = nodeCenter < viewportWidth / 2 ? 1 : -1;
                var enterX = Math.abs(nodeCenter - viewportWidth / 2) < viewportWidth * 0.12
                    ? 0
                    : side * 46;
                var x = enterX * (1 - presence);
                var y = 48 * (1 - presence);
                var scale = 0.72 + 0.28 * presence;
                var rotate = (index % 2 === 0 ? -2.4 : 2.1) * (1 - presence);

                node.style.setProperty('--polish-opacity', presence.toFixed(4));
                node.style.setProperty('--polish-x', x.toFixed(2) + 'px');
                node.style.setProperty('--polish-y', y.toFixed(2) + 'px');
                node.style.setProperty('--polish-scale', scale.toFixed(4));
                node.style.setProperty('--polish-rotate', rotate.toFixed(2) + 'deg');
                node.style.pointerEvents = presence > 0.2 ? 'auto' : 'none';

                if (presence > 0.2) {
                    node.removeAttribute('aria-hidden');
                } else {
                    node.setAttribute('aria-hidden', 'true');
                }
            });
        }

        function queueRender() {
            if (frame !== null) return;
            frame = window.requestAnimationFrame(render);
        }

        function onResize() {
            setStoryHeight();
            queueRender();
        }

        setStoryHeight();
        render();
        window.addEventListener('scroll', queueRender, { passive: true });
        window.addEventListener('resize', onResize, { passive: true });
    }
