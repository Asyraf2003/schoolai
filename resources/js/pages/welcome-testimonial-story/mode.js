import { addMediaListener, removeMediaListener, setNumberProperty, setPixelProperty } from './core.js';

export function installStoryMode(context, scroll) {
    var root = context.root;
    var nodes = context.nodes;
    var desktopMedia = context.desktopMedia;
    var reducedMotionMedia = context.reducedMotionMedia;
    var state = context.state;
    var onDesktopResize = scroll.onDesktopResize;
    var onDesktopScroll = scroll.onDesktopScroll;
    var setStoryHeight = scroll.setStoryHeight;
    var stopProgressLoop = scroll.stopProgressLoop;
    var syncScrollTarget = scroll.syncScrollTarget;

        function teardownMode() {
            window.removeEventListener('scroll', onDesktopScroll);
            window.removeEventListener('resize', onDesktopResize);
            window.clearTimeout(state.resizeTimer);
            stopProgressLoop();
            state.enhanced = false;
            root.style.removeProperty('--testimonial-story-height');
        }

        function setupMode() {
            teardownMode();

            if (desktopMedia.matches && !reducedMotionMedia.matches) {
                state.enhanced = true;
                root.setAttribute('data-mode', 'desktop');
                setStoryHeight();

                window.addEventListener('scroll', onDesktopScroll, { passive: true });
                window.addEventListener('resize', onDesktopResize, { passive: true });

                syncScrollTarget(true);
                return;
            }

            root.setAttribute(
                'data-mode',
                reducedMotionMedia.matches ? 'static' : 'mobile'
            );

            nodes.forEach(function (node) {
                setNumberProperty(node, '--node-opacity', 1);
                setPixelProperty(node, '--node-y', 0);
                setNumberProperty(node, '--node-scale', 1);
            });
        }

        setupMode();
        addMediaListener(desktopMedia, setupMode);
        addMediaListener(reducedMotionMedia, setupMode);

        window.addEventListener('pagehide', function cleanup() {
            teardownMode();
            removeMediaListener(desktopMedia, setupMode);
            removeMediaListener(reducedMotionMedia, setupMode);
        }, { once: true });
}
