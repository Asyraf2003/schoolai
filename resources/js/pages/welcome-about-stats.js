import {
    DESKTOP_QUERY,
    REDUCED_MOTION_QUERY,
    ROOT_SELECTOR,
    STAT_SELECTOR,
    TRACK_SELECTOR,
    ensureStatDescription,
    isRtlDocument,
} from './welcome-about-stats/core.js';
import { createLayoutActions } from './welcome-about-stats/layout.js';
import { installStoryMode } from './welcome-about-stats/mode.js';
import { createProgressPointerActions } from './welcome-about-stats/progress-pointer.js';
import { createSceneRenderer } from './welcome-about-stats/renderer.js';
import { buildTimeline } from './welcome-about-stats/timeline.js';

(function () {
    'use strict';

    function initializeStory(root) {
        if (root.getAttribute('data-about-stats-initialized') === 'true') {
            return;
        }

        var track = root.querySelector(TRACK_SELECTOR);
        var sticky = root.querySelector('.about-stats-story__sticky') || root;
        var statElements = Array.prototype.slice.call(
            root.querySelectorAll(STAT_SELECTOR)
        );
        var progressDots = Array.prototype.slice.call(
            root.querySelectorAll('[data-about-stats-progress-dot]')
        );
        var desktopMedia = window.matchMedia(DESKTOP_QUERY);
        var reducedMotionMedia = window.matchMedia(REDUCED_MOTION_QUERY);
        var context = {
            root: root,
            track: track,
            sticky: sticky,
            statElements: statElements,
            progressDots: progressDots,
            desktopMedia: desktopMedia,
            reducedMotionMedia: reducedMotionMedia,
            timeline: buildTimeline(statElements.length),
            state: {
                enhanced: false,
                resizeTimer: null,
                progressFrame: null,
                lastFrameTime: 0,
                targetProgress: 0,
                renderedProgress: 0,
                pointerFrame: null,
                pointerListening: false,
                pointerX: 0,
                pointerY: 0,
                activeIndex: -1,
                direction: isRtlDocument() ? -1 : 1,
                mobileObserver: null
            }
        };

        if (!track) return;

        root.setAttribute('data-about-stats-initialized', 'true');
        statElements.forEach(ensureStatDescription);

        var layout = createLayoutActions(context);
        var renderScene = createSceneRenderer(context, layout);
        var progress = createProgressPointerActions(context, layout, renderScene);
        installStoryMode(context, layout, progress);
    }

    function initialize() {
        document.querySelectorAll(ROOT_SELECTOR).forEach(initializeStory);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, {
            once: true
        });
    } else {
        initialize();
    }
})();
