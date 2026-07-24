import '../../css/pages/welcome-testimonial-story.css';
import { DESKTOP_QUERY, REDUCED_MOTION_QUERY } from './welcome-testimonial-story/core.js';
import { buildStoryData, loadMediaFeed } from './welcome-testimonial-story/feed.js';
import { insertStory } from './welcome-testimonial-story/markup.js';
import { createModalActions } from './welcome-testimonial-story/modal.js';
import { installStoryMode } from './welcome-testimonial-story/mode.js';
import { createScrollActions } from './welcome-testimonial-story/scroll.js';

(function () {
    'use strict';

    function initializeStory(root) {
        if (!root || root.getAttribute('data-testimonial-initialized') === 'true') {
            return;
        }

        var track = root.querySelector('[data-testimonial-track]');
        var previewVideo = root.querySelector('[data-testimonial-preview-video]');
        var nodes = Array.prototype.slice.call(root.querySelectorAll('[data-testimonial-node]'));
        var mediaTriggers = Array.prototype.slice.call(root.querySelectorAll('[data-testimonial-media-url]'));
        var desktopMedia = window.matchMedia(DESKTOP_QUERY);
        var reducedMotionMedia = window.matchMedia(REDUCED_MOTION_QUERY);
        var state = {
            enhanced: false,
            resizeTimer: null,
            progressFrame: null,
            lastFrameTime: 0,
            targetProgress: 0,
            renderedProgress: 0,
            modal: null,
            modalMedia: null,
            lastFocused: null,
            previousBodyOverflow: ''
        };

        if (!track) return;

        root.setAttribute('data-testimonial-initialized', 'true');

        var context = {
            root: root,
            track: track,
            previewVideo: previewVideo,
            nodes: nodes,
            mediaTriggers: mediaTriggers,
            desktopMedia: desktopMedia,
            reducedMotionMedia: reducedMotionMedia,
            state: state
        };
        var modal = createModalActions(state);
        var closeModal = modal.closeModal;
        var openModal = modal.openModal;

        mediaTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                openModal(trigger);
            });

            if (trigger.tagName !== 'BUTTON') {
                trigger.addEventListener('keydown', function (event) {
                    if (event.key !== 'Enter' && event.key !== ' ') return;
                    event.preventDefault();
                    openModal(trigger);
                });
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closeModal();
        });

        if (previewVideo) {
            if (typeof IntersectionObserver === 'function') {
                var videoObserver = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            var playResult = previewVideo.play();
                            if (playResult && typeof playResult.catch === 'function') {
                                playResult.catch(function () {});
                            }
                        } else {
                            previewVideo.pause();
                        }
                    });
                }, { threshold: 0.08 });

                videoObserver.observe(root);
            } else {
                var playResult = previewVideo.play();
                if (playResult && typeof playResult.catch === 'function') {
                    playResult.catch(function () {});
                }
            }
        }

        installStoryMode(context, createScrollActions(context));
    }

    async function initialize() {
        var items = await loadMediaFeed();
        var root = insertStory(buildStoryData(items));
        initializeStory(root);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
})();
