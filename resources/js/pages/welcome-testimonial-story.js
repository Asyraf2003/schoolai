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
                eyebrow: 'Suara Keluarga Al Mustaqbal',
                title: 'Apa Kata Mereka Tentang Al Mustaqbal?',
                note: 'Sebuah ruang untuk melihat pengalaman, cerita, dan momen yang tumbuh bersama perjalanan sekolah.',
                networkEyebrow: 'Cerita yang Terhubung',
                networkTitle: 'Satu sekolah, banyak sudut pandang.',
                networkNote: 'Scroll perlahan untuk membuka setiap potongan cerita. Klik media untuk melihatnya lebih dekat.',
                scroll: 'Scroll untuk menjelajah',
                openMedia: 'Buka media testimoni',
                openVideo: 'Buka video testimoni',
                close: 'Tutup'
            },
            en: {
                eyebrow: 'Voices of Al Mustaqbal',
                title: 'What Do People Say About Al Mustaqbal?',
                note: 'A space for experiences, stories, and moments that grow alongside the school journey.',
                networkEyebrow: 'Connected Stories',
                networkTitle: 'One school, many perspectives.',
                networkNote: 'Scroll slowly to reveal each piece of the story. Open any media for a closer look.',
                scroll: 'Scroll to explore',
                openMedia: 'Open testimonial media',
                openVideo: 'Open testimonial video',
                close: 'Close'
            },
            ar: {
                eyebrow: 'أصوات من مجتمع المستقبل',
                title: 'ماذا يقول الناس عن مدرسة المستقبل؟',
                note: 'مساحة لعرض التجارب والقصص واللحظات التي تنمو مع رحلة المدرسة.',
                networkEyebrow: 'قصص مترابطة',
                networkTitle: 'مدرسة واحدة، ووجهات نظر متعددة.',
                networkNote: 'مرر بهدوء لتظهر كل لقطة من القصة، واضغط على أي وسائط لمشاهدتها عن قرب.',
                scroll: 'مرر للاستكشاف',
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
                ' data-testimonial-order="' + index + '"',
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
            '      <header class="testimonial-network-story__intro-copy">',
            '        <p class="testimonial-network-story__eyebrow">' + copy.eyebrow + '</p>',
            '        <h2 class="testimonial-network-story__intro-title">' + copy.title + '</h2>',
            '        <p class="testimonial-network-story__intro-note">' + copy.note + '</p>',
            '      </header>',
            '',
            '      <div class="testimonial-network-story__network-copy">',
            '        <p class="testimonial-network-story__eyebrow">' + copy.networkEyebrow + '</p>',
            '        <h3 class="testimonial-network-story__network-title">' + copy.networkTitle + '</h3>',
            '        <p class="testimonial-network-story__network-note">' + copy.networkNote + '</p>',
            '      </div>',
            '',
            '      <svg class="testimonial-network-story__connections" viewBox="0 0 1600 900" preserveAspectRatio="none" aria-hidden="true">',
            '        <path class="testimonial-network-story__connection" data-testimonial-line data-testimonial-order="0" d="M1120 365 C930 315 790 205 610 165" />',
            '        <path class="testimonial-network-story__connection" data-testimonial-line data-testimonial-order="1" d="M1060 405 C870 355 720 260 490 120" />',
            '        <path class="testimonial-network-story__connection" data-testimonial-line data-testimonial-order="2" d="M1035 455 C820 445 570 430 300 430" />',
            '        <path class="testimonial-network-story__connection" data-testimonial-line data-testimonial-order="3" d="M1055 490 C860 510 710 500 560 420" />',
            '        <path class="testimonial-network-story__connection" data-testimonial-line data-testimonial-order="4" d="M1085 545 C890 625 690 705 430 735" />',
            '        <path class="testimonial-network-story__connection" data-testimonial-line data-testimonial-order="5" d="M1160 330 C1110 220 1035 145 890 105" />',
            '        <path class="testimonial-network-story__connection" data-testimonial-line data-testimonial-order="6" d="M1135 570 C1040 690 925 755 800 770" />',
            '        <path class="testimonial-network-story__connection" data-testimonial-line data-testimonial-order="7" d="M1290 565 C1390 640 1470 720 1535 765" />',
            '      </svg>',
            '',
            '      <div class="testimonial-network-story__nodes">' + nodeMarkup + '</div>',
            '      <p class="testimonial-network-story__scroll-cue" aria-hidden="true">' + copy.scroll + '</p>',
            '    </div>',
            '  </div>',
            '</div>'
        ].join('\n');
    }

    function insertStory() {
        if (document.querySelector(ROOT_SELECTOR)) return document.querySelector(ROOT_SELECTOR);

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
        var lines = Array.prototype.slice.call(root.querySelectorAll('[data-testimonial-line]'));
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

        lines.forEach(function (line) {
            var length = 1;

            try {
                length = Math.max(line.getTotalLength(), 1);
            } catch (error) {
                length = 1;
            }

            line.setAttribute('data-testimonial-line-length', String(length));
            line.style.strokeDasharray = String(length);
            line.style.strokeDashoffset = String(length);
        });

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
                }, { threshold: 0.18 });

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
                Math.round(viewportHeight * 7.2) + 'px'
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
            var intro = smootherstep(0, 0.12, progress);
            var shrink = smootherstep(0.13, 0.34, progress);
            var shift = smootherstep(0.25, 0.47, progress);
            var dark = smootherstep(0.27, 0.52, progress);
            var introExit = smootherstep(0.19, 0.36, progress);
            var networkPresence = smootherstep(0.33, 0.5, progress);
            var mediaScale = lerp(1.035, 0.455, shrink);
            var mediaX = lerp(0, viewportWidth * 0.235, shift);
            var mediaY = lerp(viewportHeight * 0.02, 0, shrink);
            var mediaRadius = lerp(7, 30, shrink);

            setNumberProperty(root, '--testimonial-dark', dark);
            setPixelProperty(root, '--testimonial-media-x', mediaX);
            setPixelProperty(root, '--testimonial-media-y', mediaY);
            setNumberProperty(root, '--testimonial-media-scale', mediaScale);
            setPixelProperty(root, '--testimonial-media-radius', mediaRadius);
            setNumberProperty(
                root,
                '--testimonial-intro-opacity',
                intro * (1 - introExit)
            );
            setNumberProperty(
                root,
                '--testimonial-network-opacity',
                networkPresence
            );

            lines.forEach(function (line, index) {
                var start = 0.39 + index * 0.058;
                var end = start + 0.07;
                var lineProgress = smootherstep(start, end, progress);
                var length = Number(
                    line.getAttribute('data-testimonial-line-length') || 1
                );

                line.style.strokeDashoffset = String(length * (1 - lineProgress));
            });

            nodes.forEach(function (node, index) {
                var lineStart = 0.39 + index * 0.058;
                var nodeStart = lineStart + 0.045;
                var nodeEnd = nodeStart + 0.075;
                var presence = smootherstep(nodeStart, nodeEnd, progress);
                var y = lerp(34, 0, presence);
                var scale = lerp(0.82, 1, presence);

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

            setNumberProperty(root, '--testimonial-dark', 1);
            setNumberProperty(root, '--testimonial-intro-opacity', 1);
            setNumberProperty(root, '--testimonial-network-opacity', 1);

            lines.forEach(function (line) {
                line.style.strokeDashoffset = '0';
            });

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
