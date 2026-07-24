import {
    addRainbowGradient,
    clamp,
    createSvgElement,
    hangingConnectorPath,
    prepareStroke,
    relativeBox,
    roundedFramePath,
} from './geometry.js';

const createFrame = (card, index, panelKey) => {
    const svg = createSvgElement('svg');
    const path = createSvgElement('path');
    const gradientId = `ppdbJourneyFrame-${panelKey}-${index}`;
    svg.classList.add('ppdb-journey-native-layer', 'ppdb-journey-v4__frame');
    svg.setAttribute('aria-hidden', 'true');
    svg.setAttribute('focusable', 'false');
    path.classList.add('ppdb-journey-v4__frame-path');
    path.setAttribute('stroke', `url(#${gradientId})`);
    svg.appendChild(path);
    card.node.prepend(svg);
    return {
        ...card,
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

export const createPanelState = (panel, panelStates = null) => {
    const stack = panel.querySelector('.ppdb-liftoff__stack');
    const panelKey = panel.dataset.ppdbLiftoffPanel || 'panel';
    if (!stack) return null;
    panel.querySelectorAll('.ppdb-journey-native-layer').forEach((layer) => layer.remove());
    const cards = Array.from(panel.querySelectorAll('[data-ppdb-journey-card], .ppdb-liftoff-card')).map((element, index) => ({
        element,
        index,
        visual: element.querySelector('[data-ppdb-journey-visual], .ppdb-liftoff-card__visual'),
        node: element.querySelector('[data-ppdb-story-node]'),
    })).filter((card) => card.visual && card.node);
    if (cards.length === 0) return null;

    const connectorLayer = createSvgElement('svg');
    connectorLayer.classList.add('ppdb-journey-native-layer', 'ppdb-journey-v4__connectors');
    connectorLayer.setAttribute('aria-hidden', 'true');
    connectorLayer.setAttribute('focusable', 'false');
    stack.prepend(connectorLayer);
    const frames = cards.map((card, index) => createFrame(card, index, panelKey));
    const connectors = frames.slice(0, -1).map((frame, index) => {
        const path = createSvgElement('path');
        path.classList.add('ppdb-journey-v4__connector-path');
        connectorLayer.appendChild(path);
        return { path, index, length: 1 };
    });
    const state = {
        panel,
        stack,
        frames,
        connectors,
        connectorLayer,
        connectorGradientId: `ppdbJourneyConnector-${panelKey}`,
        targetProgress: 0,
        renderedProgress: 0,
        lastRenderTime: 0,
        activeIndex: -1,
        geometryDirty: true,
        timeline: null,
    };
    panelStates?.set(panel, state);
    return state;
};

export const updateConnectorGeometry = (state) => {
    if (!state || state.panel.hidden) return;
    const height = Math.max(state.stack.clientHeight, 1);
    state.connectors.forEach((connector, index) => {
        const current = state.frames[index];
        const next = state.frames[index + 1];
        const currentBox = relativeBox(current.node, state.stack);
        const nextBox = relativeBox(next.node, state.stack);
        const visualBox = relativeBox(next.visual, state.stack);
        const visualIsLeft = (visualBox.x + visualBox.width / 2) < (nextBox.x + nextBox.width / 2);
        const start = {
            x: currentBox.x + (currentBox.width / 2),
            y: currentBox.y + currentBox.height - 5,
        };
        const end = {
            x: nextBox.x + (visualIsLeft ? 5 : nextBox.width - 5),
            y: nextBox.y + (nextBox.height * 0.52),
        };
        connector.path.setAttribute('d', hangingConnectorPath({
            start,
            end,
            visual: visualBox,
            sceneHeight: height,
        }));
        connector.length = Math.max(connector.path.getTotalLength(), 1);
        connector.path.style.strokeDasharray = String(connector.length);
    });
};

export const rebuildGeometry = (state) => {
    if (!state || state.panel.hidden) return;
    const width = Math.max(state.stack.clientWidth, 1);
    const height = Math.max(state.stack.clientHeight, 1);
    state.connectorLayer.setAttribute('viewBox', `0 0 ${width} ${height}`);
    state.connectorLayer.setAttribute('preserveAspectRatio', 'none');
    state.connectorLayer.replaceChildren();
    addRainbowGradient(state.connectorLayer, state.connectorGradientId, width, height);
    state.frames.forEach((frame) => {
        const frameWidth = Math.max(frame.node.offsetWidth, 1);
        const frameHeight = Math.max(frame.node.offsetHeight, 1);
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
    state.geometryDirty = false;
};

export const resetPanelState = (state) => {
    if (!state) return;
    state.targetProgress = 0;
    state.renderedProgress = 0;
    state.lastRenderTime = 0;
    state.activeIndex = -1;
    state.geometryDirty = true;
};

export const destroyPanelState = (state) => {
    if (!state) return;
    state.connectorLayer.remove();
    state.frames.forEach((frame) => frame.svg.remove());
    state.frames.forEach(({ element, node }) => {
        element.removeAttribute('style');
        element.inert = false;
        node.classList.remove('is-story-lit', 'is-story-holding');
    });
};
