import '../../css/pages/welcome-testimonial-story.css';

(function () {
    'use strict';

    var ROOT_SELECTOR = '[data-testimonial-network-story]';
    var DESKTOP_QUERY = '(min-width: 961px)';
    var REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';

    function clamp(value, minimum, maximum) {
        return Math.min(Math.max(value, minimum), maximum);
    }

    function lerp(from, to, progress) {
        return from + (to - from) * progress;
    }

    function smootherstep(edgeStart, edgeEnd, value) {
        if (edgeStart === edgeEnd) return value < edgeStart ? 0 : 1;

        var progress = clamp(
            (value - edgeStart) / (edgeEnd - edgeStart),
            0,
            1
        );

        return progress * progress * progress * (
            progress * (progress * 6 - 15) + 10
        );
    }

    function setNumberProperty(element, property, value, precision) {
        if (!element) return;

        element.style.setProperty(
            property,
            Number(value).toFixed(typeof precision === 'number' ? precision : 4)
        );
    }

    function setPixelProperty(element, property, value) {
        if (!element) return;
        element.style.setProperty(property, Number(value).toFixed(2) + 'px');
    }

    function localeKey() {
        var language = (document.documentElement.lang || '').toLowerCase();

        if (language.indexOf('ar') === 0) return 'ar';
        if (language.indexOf('en') === 0) return 'en';
        return 'id';
    }

    function copyForLocale() {
        var copy = {
            id: {
                title: 'Apa Kata Mereka Tentang Al Mustaqbal?',
                openMedia: 'Buka media testimoni',
                openVideo: 'Buka video testimoni',
                close: 'Tutup'
            },
            en: {
                title: 'What Do People Say About Al Mustaqbal?',
                openMedia: 'Open testimonial media',
                openVideo: 'Open testimonial video',
                close: 'Close'
            },
            ar: {
                title: 'ماذا يقول الناس عن مدرسة المستقبل؟',
                openMedia: 'افتح وسائط الشهادة',
                openVideo: 'افتح فيديو الشهادة',
                close: 'إغلاق'
            }
        };

        return copy[localeKey()] || copy.id;
    }

    function createStoryMarkup(copy) {
        var media = [
            { image: '/media/home/9.png', type: 'image', url: '/media/home/9.png' },
            { image: '/media/home/10.png', type: 'image', url: '/media/home/10.png' },
            { image: '/media/home/11.png', type: 'image', url: '/media/home/11.png' },
            { image: '/media/home/12.png', type: 'image', url: '/media/home/12.png' },
            { image: '/media/home/9.png', type: 'video', url: '/media/hero/shanghai-mega-city.mp4' },
            { image: '/media/home/10.png', type: 'image', url: '/media/home/10.png' },
            { image: '/media/home/11.png', type: 'image', url: '/media/home/11.png' },
            { image: '/media/home/12.png', type: 'image', url: '/media/home/12.png' }
        ];

        var nodeMarkup = media.map(function (item, index) {
            var label = item.type === 'video' ? copy.openVideo : copy.openMedia;

            return [
                '<button',
                ' type="button"',
                ' class="testimonial-network-story__node testimonial-network-story__node--' + (index + 1) + '"',
                ' data-testimonial-node',
                ' data-testimonial-media-type="' + item.type + '"',
                ' data-testimonial-media-url="' + item.url + '"',
                ' aria-label="' + label + ' ' + (index + 1) + '"',
                '>',
                '<span class="testimonial-network-story__node-media">',
                '<img src="' + item.image + '" alt="" loading="lazy" decoding="async" />',
                '</span>',
                '</button>'
            ].join('');
        }).join('');

        return [
            '<div class="testimonial-network-story__track" data-testimonial-track>',
            '  <div class="testimonial-network-story__sticky">',
            '    <div class="testimonial-network-story__stage">',
            '      <figure',
            '        class="testimonial-network-story__main-media"',
            '        data-testimonial-main-media',
            '        data-testimonial-media-type="video"',
            '        data-testimonial-media-url="/media/hero/shanghai-mega-city.mp4"',
            '        role="button"',
            '        tabindex="0"',
            '        aria-label="' + copy.openVideo + '"',
            '      >',
            '        <video muted loop playsinline webkit-playsinline preload="metadata" poster="/images/hero-video-poster.svg" data-testimonial-preview-video>',
            '          <source src="/media/hero/shanghai-mega-city.mp4" type="video/mp4" />',
            '        </video>',
            '      </figure>',
            '',
            '      <header class="testimonial-network-story__title-wrap">',
            '        <h2 class="testimonial-network-story__title">' + copy.title + '</h2>',
            '      </header>',
            '',
            '      <div class="testimonial-network-story__nodes">' + nodeMarkup + '</div>',
            '    </div>',
            '  </div>',
            '</div>'
        ].join('\n');
    }

    function insertStory() {
        var existing = document.querySelector(ROOT_SELECTOR);
        if (existing) return existing;

        var articleSection = document.getElementById('artikel');
        var gallerySection = document.getElementById('galeri');

        if (!articleSection || !gallerySection || !articleSection.parentNode) {
            return null;
        }

        var copy = copyForLocale();
        var section = document.createElement('section');
        section.className = 'testimonial-network-story';
        section.id = 'testimoni';
        section.setAttribute('data-testimonial-network-story', '');
        section.setAttribute('aria-label', copy.title);
        section.innerHTML = createStoryMarkup(copy);

        articleSection.parentNode.insertBefore(section, articleSection);
        return section;
    }

    function addMediaListener(mediaQuery, listener) {
        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', listener);
            return;
        }

        if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(listener);
        }
    }

    function removeMediaListener(mediaQuery, listener) {
        if (typeof mediaQuery.removeEventListener === 'function') {
            mediaQuery.removeEventListener('change', listener);
            return;
        }

        if (typeof mediaQuery.removeListener === 'function') {
            mediaQuery.removeListener(listener);
        }
    }

    function initializeStory(root) {
        if (!root || root.getAttribute('data-testimonial-initialized') === 'true') {
            return;
        }

        var track = root.querySelector('[data-testimonial-track]');
        var previewVideo = root.querySelector('[data-testimonial-preview-video]');
        var nodes = Array.prototype.slice.call(
            root.querySelectorAll('[data-testimonial-node]')
        );
        var mediaTriggers = Array.prototype.slice.call(
            root.querySelectorAll('[data-testimonial-media-url]')
        );
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

        function ensureModal() {
            if (state.modal) return;

            var copy = copyForLocale();
            var modal = document.createElement('div');
            modal.className = 'testimonial-media-modal';
            modal.setAttribute('role', 'dialog');
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('aria-label', copy.title);
            modal.hidden = true;
            modal.innerHTML = [
                '<button type="button" class="testimonial-media-modal__backdrop" data-testimonial-modal-close aria-label="' + copy.close + '"></button>',
                '<article class="testimonial-media-modal__panel">',
                '  <button type="button" class="testimonial-media-modal__close" data-testimonial-modal-close aria-label="' + copy.close + '">' + copy.close + '</button>',
                '  <div class="testimonial-media-modal__media" data-testimonial-modal-media></div>',
                '</article>'
            ].join('');

            document.body.appendChild(modal);
            state.modal = modal;
            state.modalMedia = modal.querySelector('[data-testimonial-modal-media]');

            Array.prototype.slice.call(
                modal.querySelectorAll('[data-testimonial-modal-close]')
            ).forEach(function (button) {
                button.addEventListener('click', closeModal);
            });
        }

        function clearModalMedia() {
            if (!state.modalMedia) return;
            state.modalMedia.replaceChildren();
        }

        function closeModal() {
            if (!state.modal || state.modal.hidden) return;

            state.modal.hidden = true;
            clearModalMedia();
            document.body.style.overflow = state.previousBodyOverflow;

            if (state.lastFocused && typeof state.lastFocused.focus === 'function') {
                state.lastFocused.focus();
            }
        }

        function openModal(trigger) {
            var type = trigger.getAttribute('data-testimonial-media-type') || 'image';
            var url = trigger.getAttribute('data-testimonial-media-url') || '';

            if (!url) return;

            ensureModal();
            clearModalMedia();
            state.lastFocused = document.activeElement;
            state.previousBodyOverflow = document.body.style.overflow;

            if (type === 'video') {
                var video = document.createElement('video');
                video.src = url;
                video.controls = true;
                video.autoplay = true;
                video.playsInline = true;
                video.setAttribute('playsinline', '');
                video.setAttribute('webkit-playsinline', '');
                state.modalMedia.appendChild(video);
            } else {
                var image = document.createElement('img');
                image.src = url;
                image.alt = trigger.getAttribute('aria-label') || '';
                image.decoding = 'async';
                state.modalMedia.appendChild(image);
            }

            state.modal.hidden = false;
            document.body.style.overflow = 'hidden';

            var closeButton = state.modal.querySelector('.testimonial-media-modal__close');
            if (closeButton) closeButton.focus();
        }

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

        function setStoryHeight() {
            var viewportHeight = Math.max(
                window.innerHeight || 0,
                document.documentElement.clientHeight || 0,
                640
            );

            root.style.setProperty(
                '--testimonial-story-height',
                Math.round(viewportHeight * 6.8) + 'px'
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

                window.addEventListener('scroll', onDesktopScroll, {
                    passive: true
                });
                window.addEventListener('resize', onDesktopResize, {
                    passive: true
                });

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

    function initialize() {
        var root = insertStory();
        initializeStory(root);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
})();