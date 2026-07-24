const desktopJourney = window.matchMedia('(min-width: 901px) and (prefers-reduced-motion: no-preference)');

if (desktopJourney.matches) {
    const originalRoot = document.querySelector('[data-ppdb-liftoff]');

    if (originalRoot) {
        const root = originalRoot.cloneNode(true);
        originalRoot.replaceWith(root);
        root.classList.add('ppdb-journey-v3');

        root.querySelectorAll('[data-ppdb-storyline]').forEach((storyline) => {
            storyline.replaceChildren();
            storyline.setAttribute('hidden', '');
        });

        root.querySelectorAll('[data-ppdb-story-node]').forEach((node) => {
            node.classList.remove('is-story-lit', 'is-story-holding');
        });

        const tabs = Array.from(root.querySelectorAll('[data-ppdb-liftoff-tab]'));
        const panels = Array.from(root.querySelectorAll('[data-ppdb-liftoff-panel]'));
        const svgNamespace = 'http://www.w3.org/2000/svg';
        const panelStates = new WeakMap();
        let frameRequest = 0;
        let needsGeometry = true;
        let needsTargets = true;

        const clamp = (value, min = 0, max = 1) => Math.min(Math.max(value, min), max);
        const segment = (value, start, end) => clamp((value - start) / Math.max(end - start, 0.0001));
        const ease = (value) => value * value * (3 - (2 * value));
        const createSvgElement = (name) => document.createElementNS(svgNamespace, name);

        const rainbowStops = [
            ['0%', '#ff4d8d'],
            ['16%', '#ff8a3d'],
            ['32%', '#ffd84d'],
            ['48%', '#57d68d'],
            ['64%', '#4cc9f0'],
            ['80%', '#6c63ff'],
            ['92%', '#c77dff'],
            ['100%', '#ff4d8d'],
        ];

        const addRainbowGradient = (svg, id, x2, y2) => {
            const defs = createSvgElement('defs');
            const gradient = createSvgElement('linearGradient');
            gradient.setAttribute('id', id);
            gradient.setAttribute('gradientUnits', 'userSpaceOnUse');
            gradient.setAttribute('x1', '0');
            gradient.setAttribute('y1', '0');
            gradient.setAttribute('x2', String(x2));
            gradient.setAttribute('y2', String(y2));

            rainbowStops.forEach(([offset, color]) => {
                const stop = createSvgElement('stop');
                stop.setAttribute('offset', offset);
                stop.setAttribute('stop-color', color);
                gradient.appendChild(stop);
            });

            defs.appendChild(gradient);
            svg.appendChild(defs);
        };

        const roundedFramePath = (width, height, radius) => {
            const inset = 5;
            const left = inset;
            const top = inset;
            const right = Math.max(width - inset, left + 1);
            const bottom = Math.max(height - inset, top + 1);
            const centerX = width / 2;
            const safeRadius = Math.min(radius, (right - left) / 2, (bottom - top) / 2);

            return [
                `M ${centerX} ${top}`,
                `H ${right - safeRadius}`,
                `Q ${right} ${top} ${right} ${top + safeRadius}`,
                `V ${bottom - safeRadius}`,
                `Q ${right} ${bottom} ${right - safeRadius} ${bottom}`,
                `H ${left + safeRadius}`,
                `Q ${left} ${bottom} ${left} ${bottom - safeRadius}`,
                `V ${top + safeRadius}`,
                `Q ${left} ${top} ${left + safeRadius} ${top}`,
                `H ${centerX}`,
            ].join(' ');
        };

        const quarterArcPath = (startX, startY, endX, endY) => {
            const horizontal = endX - startX;
            const vertical = Math.max(endY - startY, 1);
            const direction = horizontal >= 0 ? 1 : -1;
            const radius = clamp(Math.min(Math.abs(horizontal) * 0.72, vertical * 0.48), 96, 230);
            const drop = clamp(vertical * 0.34, 90, 210);
            const approach = Math.min(radius, Math.max(Math.abs(horizontal) * 0.42, 82));

            return [
                `M ${startX} ${startY}`,
                `C ${startX} ${startY + drop}, ${endX - (direction * approach)} ${endY}, ${endX} ${endY}`,
            ].join(' ');
        };

        const prepareStroke = (path) => {
            const length = Math.max(path.getTotalLength(), 1);
            path.style.strokeDasharray = String(length);
            path.style.strokeDashoffset = String(length);
            path.style.opacity = '0';
            return length;
        };

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

        const createPanelState = (panel) => {
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

        const updateConnectorGeometry = (state) => {
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

        const rebuildGeometry = (state) => {
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

        const updateTargets = (state) => {
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

        const drawState = (state) => {
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

        const activePanel = () => root.querySelector(`[data-ppdb-liftoff-panel="${root.dataset.activeAudience}"]`);

        const render = () => {
            frameRequest = 0;
            const panel = activePanel();
            const state = panel ? panelStates.get(panel) : null;
            if (!state) return;

            if (needsGeometry) {
                rebuildGeometry(state);
                needsGeometry = false;
                needsTargets = true;
            }

            if (needsTargets) {
                updateTargets(state);
                needsTargets = false;
            }

            updateConnectorGeometry(state);
            const unsettled = drawState(state);
            if (unsettled) frameRequest = window.requestAnimationFrame(render);
        };

        const schedule = ({ geometry = false } = {}) => {
            needsGeometry = needsGeometry || geometry;
            needsTargets = true;
            if (!frameRequest) frameRequest = window.requestAnimationFrame(render);
        };

        const activate = (audience) => {
            const target = root.querySelector(`[data-ppdb-liftoff-panel="${audience}"]`);
            if (!target) return;

            root.dataset.activeAudience = audience;
            tabs.forEach((tab) => {
                const selected = tab.dataset.ppdbLiftoffTab === audience;
                tab.classList.toggle('is-active', selected);
                tab.setAttribute('aria-selected', selected ? 'true' : 'false');
            });
            panels.forEach((panel) => {
                panel.hidden = panel !== target;
            });

            window.requestAnimationFrame(() => schedule({ geometry: true }));
        };

        panels.forEach((panel) => createPanelState(panel));
        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                if (!tab.disabled) activate(tab.dataset.ppdbLiftoffTab);
            });
        });

        if ('IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) entry.target.classList.add('is-visible');
                });
            }, { rootMargin: '20% 0px 20% 0px', threshold: 0.01 });
            root.querySelectorAll('.reveal').forEach((element) => revealObserver.observe(element));
        } else {
            root.querySelectorAll('.reveal').forEach((element) => element.classList.add('is-visible'));
        }

        window.addEventListener('scroll', () => schedule(), { passive: true });
        window.addEventListener('resize', () => schedule({ geometry: true }));

        if ('ResizeObserver' in window) {
            const resizeObserver = new ResizeObserver(() => schedule({ geometry: true }));
            panels.forEach((panel) => resizeObserver.observe(panel));
        }

        schedule({ geometry: true });
    }
}
