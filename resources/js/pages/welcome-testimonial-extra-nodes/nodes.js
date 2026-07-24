import { createNodeMedia, openModal } from './media-modal.js';

var ROOT_SELECTOR = '[data-testimonial-network-story]';
var FEED_URL = '/testimoni/media';
var MAX_NODES = 12;

export function waitForRoot() {
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

export async function loadItems() {
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

export function ensureAllNodes(root, items) {
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
