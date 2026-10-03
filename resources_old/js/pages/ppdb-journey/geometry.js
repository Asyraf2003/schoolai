const svgNamespace = 'http://www.w3.org/2000/svg';

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

export const clamp = (value, min = 0, max = 1) => Math.min(Math.max(value, min), max);
export const segment = (value, start, end) => clamp((value - start) / Math.max(end - start, 0.0001));
export const ease = (value) => value * value * (3 - (2 * value));
export const createSvgElement = (name) => document.createElementNS(svgNamespace, name);

export const addRainbowGradient = (svg, id, x2, y2) => {
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

export const roundedFramePath = (width, height, radius) => {
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

export const relativeBox = (element, ancestor) => {
    let x = 0;
    let y = 0;
    let current = element;

    while (current && current !== ancestor) {
        x += current.offsetLeft;
        y += current.offsetTop;
        current = current.offsetParent;
    }

    if (current === ancestor) {
        return {
            x,
            y,
            width: Math.max(element.offsetWidth, 1),
            height: Math.max(element.offsetHeight, 1),
        };
    }

    const rect = element.getBoundingClientRect();
    const ancestorRect = ancestor.getBoundingClientRect();
    return {
        x: rect.left - ancestorRect.left,
        y: rect.top - ancestorRect.top,
        width: Math.max(rect.width, 1),
        height: Math.max(rect.height, 1),
    };
};


export const quarterArcPath = (startX, startY, endX, endY) => {
    const horizontal = endX - startX;
    const vertical = Math.max(endY - startY, 1);
    const direction = horizontal >= 0 ? 1 : -1;
    const radius = clamp(Math.min(Math.abs(horizontal) * 0.72, vertical * 0.48), 96, 230);
    const drop = clamp(vertical * 0.34, 90, 210);
    const approach = Math.min(radius, Math.max(Math.abs(horizontal) * 0.42, 82));
    return `M ${startX} ${startY} C ${startX} ${startY + drop}, ${endX - (direction * approach)} ${endY}, ${endX} ${endY}`;
};

export const hangingConnectorPath = ({ start, end, visual, sceneHeight }) => {
    const visualCenterX = visual.x + (visual.width / 2);
    const visualCenterY = visual.y + (visual.height * 0.56);
    const lowerEdge = Math.max(start.y, end.y, visual.y + visual.height);
    const sagY = clamp(lowerEdge + (sceneHeight * 0.16), lowerEdge + 74, sceneHeight - 8);
    const firstDrop = clamp((visualCenterY - start.y) * 0.7, 72, 180);
    const approachDirection = visualCenterX >= end.x ? 1 : -1;
    const approachX = end.x + (approachDirection * clamp(Math.abs(visualCenterX - end.x) * 0.42, 82, 210));

    return [
        `M ${start.x} ${start.y}`,
        `C ${start.x} ${start.y + firstDrop}, ${visualCenterX} ${visualCenterY - 44}, ${visualCenterX} ${visualCenterY}`,
        `C ${visualCenterX} ${sagY}, ${approachX} ${sagY}, ${end.x} ${end.y}`,
    ].join(' ');
};

export const prepareStroke = (path) => {
    const length = Math.max(path.getTotalLength(), 1);
    path.style.strokeDasharray = String(length);
    path.style.strokeDashoffset = String(length);
    path.style.opacity = '0';
    return length;
};
