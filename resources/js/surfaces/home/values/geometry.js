function numberFrom(value, fallback = 0) {
    const parsed = Number.parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
}

function unionRect(rects) {
    const left = Math.min(...rects.map((rect) => rect.left));
    const right = Math.max(...rects.map((rect) => rect.right));
    const top = Math.min(...rects.map((rect) => rect.top));
    const bottom = Math.max(...rects.map((rect) => rect.bottom));

    return {
        left,
        top,
        width: right - left,
        height: bottom - top,
    };
}

function requiredNode(root, selector) {
    const node = root.querySelector(selector);
    if (!node) throw new Error(`Values story is missing ${selector}`);
    return node;
}

export function collectValuesNodes(root) {
    return {
        timeline: requiredNode(root, '[data-values-timeline]'),
        stage: requiredNode(root, '[data-values-stage]'),
        perspective: requiredNode(root, '[data-values-perspective]'),
        grid: requiredNode(root, '[data-values-cards]'),
        trailPath: root.querySelector('[data-values-trail-path]'),
        trailHead: root.querySelector('[data-values-trail-head]'),
    };
}

export function measureValuesGeometry(root, cards, nodes) {
    const rootRect = root.getBoundingClientRect();
    const stageRect = nodes.perspective.getBoundingClientRect();
    const cardRects = cards.map((card) => card.getBoundingClientRect());
    const cardsRect = unionRect(cardRects);
    const rootStyles = window.getComputedStyle(root);
    const stageStyles = window.getComputedStyle(nodes.stage);
    const stageCenterX = stageRect.left + stageRect.width / 2;
    const stageCenterY = stageRect.top + stageRect.height / 2;
    const cardsCenterX = cardsRect.left + cardsRect.width / 2;
    const cardsCenterY = cardsRect.top + cardsRect.height / 2;
    const scrollY = window.scrollY || 0;

    return {
        mode: Number.parseInt(
            rootStyles.getPropertyValue('--values-layout-mode'),
            10,
        ) || 1,
        directionSign: rootStyles.direction === 'rtl' ? -1 : 1,
        viewportHeight: window.innerHeight || 1,
        viewportWidth: window.innerWidth || 1,
        rootHeight: Math.max(1, root.offsetHeight),
        timelineHeight: Math.max(1, nodes.timeline.offsetHeight),
        stickyTop: numberFrom(stageStyles.top),
        stageWidth: Math.max(1, stageRect.width),
        stageHeight: Math.max(1, stageRect.height),
        stageCenterX,
        stageCenterY,
        cardWidth: Math.max(1, cardRects[0].width),
        cardHeight: Math.max(1, cardRects[0].height),
        slots: cardRects.map((rect) => ({
            centerX: rect.left + rect.width / 2,
            centerY: rect.top + rect.height / 2,
            rootOffsetY: rect.top - rootRect.top,
        })),
        centerDeltaX: cardsCenterX - stageCenterX,
        centerDeltaY: cardsCenterY - stageCenterY,
        rootDocumentTop: rootRect.top + scrollY,
        trailLength: nodes.trailPath?.getTotalLength?.() || 0,
    };
}

export function exposeCenterMeasurement(root, geometry) {
    root.dataset.valuesCenterDeltaX = geometry.centerDeltaX.toFixed(2);
    root.dataset.valuesCenterDeltaY = geometry.centerDeltaY.toFixed(2);
}

export function clearCenterMeasurement(root) {
    delete root.dataset.valuesCenterDeltaX;
    delete root.dataset.valuesCenterDeltaY;
}
