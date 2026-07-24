import { ensureAllNodes, loadItems, waitForRoot } from './welcome-testimonial-extra-nodes/nodes.js';
import { installPolishedTimeline } from './welcome-testimonial-extra-nodes/timeline.js';

(function () {
    'use strict';

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
