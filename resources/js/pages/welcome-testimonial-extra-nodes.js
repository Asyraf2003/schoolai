(function () {
    'use strict';

    var ROOT_SELECTOR = '[data-testimonial-network-story]';
    var FEED_URL = '/testimoni/media';
    var MAX_NODES = 12;

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

        var play = document.createElement('span');
        play.setAttribute('aria-hidden', 'true');
        play.textContent = item.type === 'video' ? '▶' : '';
        play.style.position = 'absolute';
        play.style.inset = '0';
        play.style.display = 'grid';
        play.style.placeItems = 'center';
        play.style.fontSize = '1.6rem';
        play.style.color = '#fff';
        play.style.textShadow = '0 3px 18px #000';
        wrapper.appendChild(play);

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

        function destroy() {
            document.body.style.overflow = '';
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
        button.style.setProperty('--node-opacity', '0');
        button.style.setProperty('--node-y', '34px');
        button.style.setProperty('--node-scale', '0.82');
        button.appendChild(createNodeMedia(item));
        button.addEventListener('click', function () {
            openModal(item);
        });
        return button;
    }

    function enhanceExtraNodes(root, items) {
        var nodesContainer = root.querySelector('.testimonial-network-story__nodes');
        var track = root.querySelector('[data-testimonial-track]');
        if (!nodesContainer || !track) return;

        var databaseItems = items.filter(function (item) {
            return item && item.id !== null && item.id !== undefined && item.media_url;
        }).slice(0, MAX_NODES);

        var existingCount = nodesContainer.querySelectorAll('[data-testimonial-node]').length;
        if (existingCount >= databaseItems.length) return;

        var extraNodes = [];

        databaseItems.slice(existingCount).forEach(function (item, offset) {
            var index = existingCount + offset;
            var node = createNode(item, index);
            nodesContainer.appendChild(node);
            extraNodes.push({ node: node, index: index });
        });

        if (!extraNodes.length) return;

        function readProgress() {
            var viewportHeight = Math.max(window.innerHeight || 0, document.documentElement.clientHeight || 0, 1);
            var rect = track.getBoundingClientRect();
            var range = Math.max(track.offsetHeight - viewportHeight, 1);
            return clamp(-rect.top / range, 0, 1);
        }

        function render() {
            if (root.getAttribute('data-mode') !== 'desktop') {
                extraNodes.forEach(function (entry) {
                    entry.node.style.setProperty('--node-opacity', '1');
                    entry.node.style.setProperty('--node-y', '0px');
                    entry.node.style.setProperty('--node-scale', '1');
                });
                return;
            }

            var progress = readProgress();

            extraNodes.forEach(function (entry) {
                var localIndex = entry.index - 8;
                var start = 0.70 + localIndex * 0.055;
                var presence = smootherstep(start, start + 0.085, progress);
                var y = 34 * (1 - presence);
                var scale = 0.82 + 0.18 * presence;

                entry.node.style.setProperty('--node-opacity', presence.toFixed(4));
                entry.node.style.setProperty('--node-y', y.toFixed(2) + 'px');
                entry.node.style.setProperty('--node-scale', scale.toFixed(4));
            });
        }

        render();
        window.addEventListener('scroll', render, { passive: true });
        window.addEventListener('resize', render, { passive: true });
    }

    async function initialize() {
        var results = await Promise.all([waitForRoot(), loadItems()]);
        enhanceExtraNodes(results[0], results[1]);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
})();
