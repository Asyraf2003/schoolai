import {
    addRainbowGradient,
    clamp,
    createSvgElement,
    prepareStroke,
    quarterArcPath,
    roundedFramePath,
} from './geometry.js';

const createFrame = (node, index, panelKey) => {
    const svg = createSvgElement('svg');
    const path = createSvgElement('path');
    const gradientId = `ppdbJourneyFrame-${panelKey}-${index}`;

    svg.classList.add('ppdb-journey-v3__frame');
    svg.setAttribute('aria-hidden', 'true');
    svg.setAttribute('focusable', 'false');
    path.classList.add('ppdb-journey-v3__frame-path');
    path.setAttribute('stroke', `url(#${gradientId})`);
    svg.appendChild(path);
    node.prepend(svg);

    return {
        node,
        svg,
        path,
        gradientId,
        length: 1,
        currentFrame: 0,
        targetFrame: 0,
        currentConnector: 0,
        targetConnector: 0,
    };
};

export const createPanelState = (panel, panelStates) => {
    const panelKey = panel.dataset.ppdbLiftoffPanel || 'panel';
    const stack = panel.querySelector('.ppdb-liftoff__stack');
    const nodes = Array.from(panel.querySelectorAll('[data-ppdb-story-node]'));
    if (!stack || nodes.length === 0) return null;

    const connectorLayer = createSvgElement('svg');
    connectorLayer.classList.add('ppdb-journey-v3__connectors');
    connectorLayer.setAttribute('aria-hidden', 'true');
    connectorLayer.setAttribute('focusable', 'false');
    stack.prepend(connectorLayer);

    const frames = nodes.map((node, index) => createFrame(node, index, panelKey));
    const connectors = frames.slice(0, -1).map((frame, index) => {
        const path = createSvgElement('path');
        path.classList.add('ppdb-journey-v3__connector-path');
        connectorLayer.appendChild(path);
        return { path, index, length: 1 };
    });

    const state = { panel, stack, frames, connectors, connectorLayer, connectorGradientId: `ppdbJourneyConnector-${panelKey}` };
    panelStates.set(panel, state);
    return state;
};

export const updateConnectorGeometry = (state) => {
    if (!state || state.panel.hidden) return;

    const stackRect = state.stack.getBoundingClientRect();

    state.connectors.forEach((connector, index) => {
        const current = state.frames[index];
        const next = state.frames[index + 1];
        const rect = current.node.getBoundingClientRect();
        const nextRect = next.node.getBoundingClientRect();
        const startX = (rect.left - stackRect.left) + (rect.width / 2);
        const startY = (rect.bottom - stackRect.top) - 5;
        const currentCenterX = rect.left + (rect.width / 2);
        const nextCenterX = nextRect.left + (nextRect.width / 2);
        const enterFromLeft = currentCenterX < nextCenterX;
        const endX = (nextRect.left - stackRect.left) + (enterFromLeft ? 5 : nextRect.width - 5);
        const endY = (nextRect.top - stackRect.top) + (nextRect.height * 0.52);

        connector.path.setAttribute('d', quarterArcPath(startX, startY, endX, endY));
        connector.length = Math.max(connector.path.getTotalLength(), 1);
        connector.path.style.strokeDasharray = String(connector.length);
    });
};

export const rebuildGeometry = (state) => {
    if (!state || state.panel.hidden) return;

    const width = Math.max(state.stack.clientWidth, 1);
    const height = Math.max(state.stack.scrollHeight, state.stack.clientHeight, 1);
    state.connectorLayer.setAttribute('viewBox', `0 0 ${width} ${height}`);
    state.connectorLayer.replaceChildren();
    addRainbowGradient(state.connectorLayer, state.connectorGradientId, width, height);

    state.frames.forEach((frame) => {
        const rect = frame.node.getBoundingClientRect();
        const frameWidth = Math.max(rect.width, 1);
        const frameHeight = Math.max(rect.height, 1);
        const radius = clamp(Math.min(frameWidth, frameHeight) * 0.13, 24, 36);

        frame.svg.setAttribute('viewBox', `0 0 ${frameWidth} ${frameHeight}`);
        frame.svg.replaceChildren();
        addRainbowGradient(frame.svg, frame.gradientId, frameWidth, frameHeight);
        frame.path.setAttribute('d', roundedFramePath(frameWidth, frameHeight, radius));
        frame.path.setAttribute('stroke', `url(#${frame.gradientId})`);
        frame.svg.appendChild(frame.path);
        frame.length = prepareStroke(frame.path);
    });

    state.connectors.forEach((connector) => {
        connector.path.setAttribute('stroke', `url(#${state.connectorGradientId})`);
        state.connectorLayer.appendChild(connector.path);
        connector.path.style.opacity = '0';
    });

    updateConnectorGeometry(state);
};
