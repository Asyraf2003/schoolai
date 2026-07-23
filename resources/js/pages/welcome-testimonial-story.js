import '../../css/pages/welcome-testimonial-story.css';

(function () {
    'use strict';

    var ROOT_SELECTOR = '[data-testimonial-network-story]';
    var DESKTOP_QUERY = '(min-width: 961px)';
    var REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';
    var FEED_URL = '/testimoni/media';

    var FALLBACK_PRIMARY = {
        type: 'video',
        source: 'upload',
        media_url: '/media/hero/shanghai-mega-city.mp4',
        thumbnail_url: '/images/hero-video-poster.svg'
    };

    var FALLBACK_NODES = [
        '/media/home/9.png',
        '/media/home/10.png',
        '/media/home/11.png',
        '/media/home/12.png',
        '/media/home/9.png',
        '/media/home/10.png',
        '/media/home/11.png',
        '/media/home/12.png'
    ].map(function (url) {
        return {
            type: 'photo',
            source: 'upload',
            media_url: url,
            thumbnail_url: url
        };
    });

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

    function escapeAttribute(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function normalizeItem(item) {
        if (!item || typeof item !== 'object') return null;

        var type = item.type === 'video' ? 'video' : 'photo';
        var source = item.source === 'embed' ? 'embed' : 'upload';
        var mediaUrl = typeof item.media_url === 'string' ? item.media_url.trim() : '';
        var thumbnailUrl = typeof item.thumbnail_url === 'string' ? item.thumbnail_url.trim() : '';

        if (!mediaUrl) return null;
        if (type === 'photo') source = 'upload';

        return {
            id: item.id || null,
            type: type,
            source: source,
            media_url: mediaUrl,
            thumbnail_url: thumbnailUrl
        };
    }

    async function loadMediaFeed() {
        try {
            var response = await fetch(FEED_URL, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' }
            });

            if (!response.ok) return [];

            var payload = await response.json();
            var items = Array.isArray(payload.items) ? payload.items : [];

            return items.map(normalizeItem).filter(Boolean).slice(0, 9);
        } catch (error) {
            return [];
        }
    }

    function buildStoryData(items) {
        if (!items.length) {
            return {
                primary: FALLBACK_PRIMARY,
                nodes: FALLBACK_NODES
            };
        }

        var primaryIndex = items.findIndex(function (item) {
            return item.type === 'video';
        });
        var primary = primaryIndex >= 0 ? items[primaryIndex] : FALLBACK_PRIMARY;
        var nodes = items.filter(function (item, index) {
            return index !== primaryIndex;
        }).slice(0, 8);

        return {
            primary: primary,
            nodes: nodes
        };
    }

    function autoplayEmbedUrl(url) {
        try {
            var parsed = new URL(url, window.location.href);
            var host = parsed.hostname.toLowerCase().replace(/^www\./, '');

            if (host === 'youtube.com' || host === 'youtube-nocookie.com') {
                parsed.searchParams.set('autoplay', '1');
                parsed.searchParams.set('mute', '1');
                parsed.searchParams.set('playsinline', '1');
                parsed.searchParams.set('rel', '0');
            } else if (host === 'player.vimeo.com') {
                parsed.searchParams.set('autoplay', '1');
                parsed.searchParams.set('muted', '1');
                parsed.searchParams.set('loop', '1');
                parsed.searchParams.set('background', '1');
            }

            return parsed.toString();
        } catch (error) {
            return url;
        }
    }

    function mainMediaMarkup(item) {
        var url = escapeAttribute(item.media_url);

        if (item.type === 'photo') {
            return '<img src="' + url + '" alt="" decoding="async" />';
        }

        if (item.source === 'embed') {
            return [
                '<iframe',
                ' src="' + escapeAttribute(autoplayEmbedUrl(item.media_url)) + '"',
                ' title="Testimonial video"',
                ' loading="eager"',
                ' allow="autoplay; encrypted-media; fullscreen; picture-in-picture; web-share"',
                ' allowfullscreen',
                ' referrerpolicy="strict-origin-when-cross-origin"',
                ' style="width:100%;height:100%;display:block;border:0;pointer-events:none"',
                '></iframe>'
            ].join('');
        }

        return [
            '<video muted loop autoplay playsinline webkit-playsinline preload="metadata" data-testimonial-preview-video>',
            '<source src="' + url + '" type="video/mp4" />',
            '</video>'
        ].join('');
    }

    function nodeMediaMarkup(item) {
        var thumbnail = item.thumbnail_url || '';
        var url = escapeAttribute(item.media_url);

        if (thumbnail) {
            return '<img src="' + escapeAttribute(thumbnail) + '" alt="" loading="lazy" decoding="async" />';
        }

        if (item.type === 'video' && item.source === 'upload') {
            return [
                '<video muted playsinline webkit-playsinline preload="metadata"',
                ' src="' + url + '"',
                ' style="width:100%;height:100%;display:block;object-fit:cover"',
                '></video>',
                '<span aria-hidden="true" style="position:absolute;inset:0;display:grid;place-items:center;font-size:1.6rem;color:#fff;text-shadow:0 3px 18px #000">▶</span>'
            ].join('');
        }

        return '<span aria-hidden="true" style="position:absolute;inset:0;display:grid;place-items:center;font-size:2rem;color:#fff;background:linear-gradient(135deg,#26251f,#11110f)">▶</span>';
    }

    function createStoryMarkup(copy, storyData) {
        var primary = storyData.primary;
        var nodeMarkup = storyData.nodes.map(function (item, index) {
            var label = item.type === 'video' ? copy.openVideo : copy.openMedia;

            return [
                '<button',
                ' type="button"',
                ' class="testimonial-network-story__node testimonial-network-story__node--' + (index + 1) + '"',
                ' data-testimonial-node',
                ' data-testimonial-media-type="' + escapeAttribute(item.type) + '"',
                ' data-testimonial-media-source="' + escapeAttribute(item.source) + '"',
                ' data-testimonial-media-url="' + escapeAttribute(item.media_url) + '"',
                ' aria-label="' + escapeAttribute(label + ' ' + (index + 1)) + '"',
                '>',
                '<span class="testimonial-network-story__node-media">',
                nodeMediaMarkup(item),
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
            '        data-testimonial-media-type="' + escapeAttribute(primary.type) + '"',
            '        data-testimonial-media-source="' + escapeAttribute(primary.source) + '"',
            '        data-testimonial-media-url="' + escapeAttribute(primary.media_url) + '"',
            '        role="button"',
            '        tabindex="0"',
            '        aria-label="' + escapeAttribute(primary.type === 'video' ? copy.openVideo : copy.openMedia) + '"',
            '      >',
            mainMediaMarkup(primary),
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

    function insertStory(storyData) {
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
        section.innerHTML = createStoryMarkup(copy, storyData);

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
                '<button type="button" class="testimonial-media-modal__backdrop" data-testimonial-modal-close aria-label="' + escapeAttribute(copy.close) + '"></button>',
                '<article class="testimonial-media-modal__panel">',
                '  <button type="button" class="testimonial-media-modal__close" data-testimonial-modal-close aria-label="' + escapeAttribute(copy.close) + '">' + copy.close + '</button>',
                '  <div class="testimonial-media-modal__media" data-testimonial-modal-media></div>',
                '</article>'
            ].join('');

            document.body.appendChild(modal);
            state.modal = modal;
            state.modalMedia = modal.querySelector('[data-testimonial-modal-media]');

            Array.prototype.slice.call(modal.querySelectorAll('[data-testimonial-modal-close]')).forEach(function (button) {
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
            var type = trigger.getAttribute('data-testimonial-media-type') || 'photo';
            var source = trigger.getAttribute('data-testimonial-media-source') || 'upload';
            var url = trigger.getAttribute('data-testimonial-media-url') || '';

            if (!url) return;

            ensureModal();
            clearModalMedia();
            state.lastFocused = document.activeElement;
            state.previousBodyOverflow = document.body.style.overflow;

            if (type === 'video' && source === 'embed') {
                var iframe = document.createElement('iframe');
                iframe.src = url;
                iframe.title = copyForLocale().openVideo;
                iframe.allow = 'autoplay; encrypted-media; fullscreen; picture-in-picture; web-share';
                iframe.allowFullscreen = true;
                iframe.referrerPolicy = 'strict-origin-when-cross-origin';
                iframe.style.width = '100%';
                iframe.style.height = 'min(76svh, 760px)';
                iframe.style.border = '0';
                state.modalMedia.appendChild(iframe);
            } else if (type === 'video') {
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
