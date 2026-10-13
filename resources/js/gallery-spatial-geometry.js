import { PlaneGeometry, Vector3 } from 'three';

// One unbounded world-space track. Its depth and tangent depend only on global
// distance, never on an individual panel's width, center, or media identity.
export function trackPoint(distance, y, curve) {
    const z = -curve.depth * (Math.hypot(1, distance / curve.radius) - 1);
    return new Vector3(distance, y + .05, z);
}
export const mediaIndex = (slot, count) => ((slot % count) + count) % count;
export const nearestOccurrence = (index, progress, count) => index + Math.round((progress - index) / count) * count;
export function trackPlane() { return new PlaneGeometry(1, 1, 48, 12); }
export function deformTrack(geometry, center, width, height, curve) {
    const { position, uv } = geometry.attributes;
    for (let i = 0; i < position.count; i++) {
        const p = trackPoint(center + (uv.getX(i) - .5) * width, (uv.getY(i) - .5) * height, curve);
        position.setXYZ(i, p.x, p.y, p.z);
    }
    position.needsUpdate = true;
}
export function projectTrack(distance, y, curve, camera, width, height) {
    const p = trackPoint(distance, y, curve).project(camera);
    return [(p.x + 1) * width / 2, (1 - p.y) * height / 2];
}
export function projectPanel(panel, curve, camera, width, height) {
    const { center, w, h, link } = panel;
    const project = (x, y) => projectTrack(center + x, y, curve, camera, width, height);
    const outline = [];
    for (let i = 0; i <= 48; i++) outline.push(project((i / 48 - .5) * w, h / 2));
    for (let i = 48; i >= 0; i--) outline.push(project((i / 48 - .5) * w, -h / 2));
    link.style.clipPath = `polygon(${outline.map(p => `${p[0]}px ${p[1]}px`).join(',')})`;
    const left = project(-w / 2 + .12, -h / 2 + .27);
    const right = project(-w / 2 + .45, -h / 2 + .27);
    const angle = Math.atan2(right[1] - left[1], right[0] - left[0]);
    const caption = link.querySelector('span');
    caption.style.transform = `translate(${left[0]}px,${left[1]}px) rotate(${angle}rad) translateY(-100%)`;
    caption.style.setProperty('--caption-width', `${Math.max(90, Math.min(width * .7, w * height / 7))}px`);
}
