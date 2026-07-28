import { ROOT_SELECTOR, onDocumentReady } from './welcome-about-stats/core.js';
import { createMediaController } from './welcome-about-stats/media.js';
import { createReelMotion } from './welcome-about-stats/motion.js';

var destroyReels = null;

function initializeReels() {
    if (typeof destroyReels === 'function') destroyReels();

    var cleanups = Array.from(document.querySelectorAll(ROOT_SELECTOR)).map(
        function initializeReel(root) {
            var media = createMediaController(root);
            var motion = createReelMotion(root, {
                onModeChange: media.setEnabled,
                onProximityChange: media.setNear
            });

            return function destroyReel() {
                motion.destroy();
                media.destroy();
            };
        }
    );

    destroyReels = function destroyAllReels() {
        cleanups.forEach(function cleanupReel(cleanup) {
            cleanup();
        });
        cleanups = [];
        destroyReels = null;
    };
}

onDocumentReady(initializeReels);

window.addEventListener('pagehide', function cleanupAboutReels() {
    if (typeof destroyReels === 'function') destroyReels();
});

window.addEventListener('pageshow', function restoreAboutReels(event) {
    if (event.persisted) initializeReels();
});
