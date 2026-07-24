import { clamp, ease, segment } from './geometry.js';

export const updateTargets = (state) => {
    if (!state || state.panel.hidden) return;

    const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

    state.frames.forEach((frame, index) => {
        const card = frame.node.closest('.ppdb-liftoff-card');
        if (!card) return;

        const cardRect = card.getBoundingClientRect();
        const nodeRect = frame.node.getBoundingClientRect();
        const stickyTop = clamp(viewportHeight * 0.18, 116, 188);
        const availableTravel = Math.max(card.offsetHeight - nodeRect.height - stickyTop - 72, viewportHeight * 0.55);
        const traveled = stickyTop - cardRect.top;
        const progress = clamp(traveled / availableTravel);

        frame.targetFrame = ease(segment(progress, 0.03, 0.25));
        frame.targetConnector = index < state.connectors.length
            ? ease(segment(progress, 0.61, 0.96))
            : 0;

        const frameComplete = progress >= 0.25;
        const holding = progress >= 0.25 && progress < 0.61;
        frame.node.classList.toggle('is-story-lit', frameComplete);
        frame.node.classList.toggle('is-story-holding', holding);
        frame.path.classList.toggle('is-story-lit', frameComplete);
        frame.path.classList.toggle('is-story-holding', holding);
    });
};

export const drawState = (state) => {
    if (!state || state.panel.hidden) return false;
    let unsettled = false;

    state.frames.forEach((frame, index) => {
        const frameDelta = frame.targetFrame - frame.currentFrame;
        const connectorDelta = frame.targetConnector - frame.currentConnector;
        frame.currentFrame += frameDelta * 0.12;
        frame.currentConnector += connectorDelta * 0.085;

        if (Math.abs(frameDelta) > 0.001 || Math.abs(connectorDelta) > 0.001) unsettled = true;

        const frameProgress = clamp(frame.currentFrame);
        frame.path.style.strokeDashoffset = String(frame.length * (1 - frameProgress));
        frame.path.style.opacity = frameProgress > 0.002 ? '1' : '0';

        if (index < state.connectors.length) {
            const connector = state.connectors[index];
            const connectorProgress = clamp(frame.currentConnector);
            connector.path.style.strokeDashoffset = String(connector.length * (1 - connectorProgress));
            connector.path.style.opacity = connectorProgress > 0.002 ? '1' : '0';
        }
    });

    return unsettled;
};
