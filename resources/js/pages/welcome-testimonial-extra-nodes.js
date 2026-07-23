(function () {
    'use strict';

    var ROOT_SELECTOR = '[data-testimonial-network-story]';
    var FEED_URL = '/testimoni/media';
    var MAX_NODES = 12;
    var DESKTOP_QUERY = '(min-width: 961px)';
    var REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';

    // Reveal from the video outward, rather than marching around the DOM order.
    var REVEAL_ORDER = [2, 1, 3, 7, 8, 5, 6, 0, 4, 9, 10, 11];

    function clamp(value, minimum, maximum) {
        return Math.min(Math.max(value, minimum), maximum);
    }

    function smootherstep(edgeStart, edgeEnd, value) {
        if (edgeStart === edgeEnd) return value < edgeStart ? 0 : 1;

        var progress = clamp((value - edgeStart) / (edgeEnd - edgeStart), 0, 1);
        return progress * progress * progress * (progress * (progress * 6 - 15) + 10);
    }

    function waitForRoot() {
        var existing = document.querySelector(ROOT_SELECTOR);
        if (existing) return Promise.resolve(existing);

        return new Promise(function (resolve) {
            var observer = new MutationObserver(function () {
                var root = document.querySelector(ROOT_SELECTOR);
                if (!root) return;
                observer.disconnect();
                resolve(root);
            });

            observer.observe(document.documentElement, {
                childList: true,
                subtree: true
            });
        });
    }

    async function loadItems() {
        try {
            var response = await fetch(FEED_URL, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' }
            });

            if (!response.ok) return [];

            var payload = await response.json();
            return Array.isArray(payload.items) ? payload.items : [];
        } catch (error) {
            return [];
        }
    }

    function createNodeMedia(item) {
        var wrapper = document.createElement('span');
        wrapper.className = 'testimonial-network-story__node-media';

        if (item.thumbnail_url) {
            var image = document.createElement('img');
            image.src = item.thumbnail_url;
            image.alt = '';
            image.loading = 'lazy';
            image.decoding = 'async';
            wrapper.appendChild(image);
            return wrapper;
        }

        if (item.type === 'video' && item.source === 'upload') {
            var video = document.createElement('video');
            video.src = item.media_url;
            video.muted = true;
            video.playsInline = true;
            video.preload = 'metadata';
            video.style.width = '100%';
            video.style.height = '100%';
            video.style.display = 'block';
            video.style.objectFit = 'cover';
            wrapper.appendChild(video);
        }

        if (item.type === 'video') {
            var play = document.createElement('span');
            play.setAttribute('aria-hidden', 'true');
            play.textContent = '▶';
            play.style.position = 'absolute';
            play.style.inset = '0';
            play.style.display = 'grid';
            play.style.placeItems = 'center';
            play.style.fontSize = '1.6rem';
            play.style.color = '#fff';
            play.style.textShadow = '0 3px 18px #000';
            wrapper.appendChild(play);
        }

        return wrapper;
    }

    function localizedCloseLabel() {
        var language = (document.documentElement.lang || '').toLowerCase();
        if (language.indexOf('ar') === 0) return 'إغلاق';
        if (language.indexOf('en') === 0) return 'Close';
        return 'Tutup';
    }

    function openModal(item) {
        var closeLabel = localizedCloseLabel();
        var modal = document.createElement('div');
        modal.className = 'testimonial-media-modal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');

        var backdrop = document.createElement('button');
        backdrop.type = 'button';
        backdrop.className = 'testimonial-media-modal__backdrop';
        backdrop.setAttribute('aria-label', closeLabel);

        var panel = document.createElement('article');
        panel.className = 'testimonial-media-modal__panel';

        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'testimonial-media-modal__close';
        close.textContent = closeLabel;

        var media = document.createElement('div');
        media.className = 'testimonial-media-modal__media';

        if (item.type === 'video' && item.source === 'embed') {
            var iframe = document.createElement('iframe');
            iframe.src = item.media_url;
            iframe.allow = 'autoplay; encrypted-media; fullscreen; picture-in-picture; web-share';
            iframe.allowFullscreen = true;
            iframe.referrerPolicy = 'strict-origin-when-cross-origin';
            iframe.style.width = '100%';
            iframe.style.height = 'min(76svh, 760px)';
            iframe.style.border = '0';
            media.appendChild(iframe);
        } else if (item.type === 'video') {
            var video = document.createElement('video');
            video.src = item.media_url;
            video.controls = true;
            video.autoplay = true;
            video.playsInline = true;
            media.appendChild(video);
        } else {
            var image = document.createElement('img');
            image.src = item.media_url;
            image.alt = '';
            image.decoding = 'async';
            media.appendChild(image);
        }

        var previousOverflow = document.body.style.overflow;

        function destroy() {
            document.body.style.overflow = previousOverflow;
            modal.remove();
        }

        backdrop.addEventListener('click', destroy);
        close.addEventListener('click', destroy);
        modal.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') destroy();
        });

        panel.appendChild(close);
        panel.appendChild(media);
        modal.appendChild(backdrop);
        modal.appendChild(panel);
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        close.focus();
    }

    function createNode(item, index) {
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'testimonial-network-story__node testimonial-network-story__node--' + (index + 1);
        button.setAttribute('data-testimonial-node', '');
        button.setAttribute('aria-label', 'Media testimoni ' + (index + 1));
        button.appendChild(createNodeMedia(item));
        button.addEventListener('click', function () {
            openModal(item);
        });
        return button;
    }

    function databaseItemsOnly(items) {
        return items.filter(function (item) {
            return item && item.id !== null && item.id !== undefined && item.media_url;
        }).slice(0, MAX_NODES);
    }

    function ensureAllNodes(root, items) {
        var nodesContainer = root.querySelector('.testimonial-network-story__nodes');
        if (!nodesContainer) return [];

        var databaseItems = databaseItemsOnly(items);
        var existingCount = nodesContainer.querySelectorAll('[data-testimonial-node]').length;

        databaseItems.slice(existingCount).forEach(function (item, offset) {
            nodesContainer.appendChild(createNode(item, existingCount + offset));
        });

        return Array.prototype.slice.call(
            nodesContainer.querySelectorAll('[data-testimonial-node]')
        ).slice(0, MAX_NODES);
    }

    function installPolishedTimeline(root, nodes) {
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

    async function initialize() {
        var results = await Promise.all([waitForRoot(), loadItems()]);
        var root = results[0];
        var nodes = ensureAllNodes(root, results[1]);
        installPolishedTimeline(root, nodes);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
})();
