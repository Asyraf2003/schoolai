import { PlaneGeometry, Vector3 } from 'three';

// A cylindrical surface: every column occupies a different depth, including
// the interior. UVs remain the original source rectangle (no image stretching).
export function bentPoint(x, y, width) {
    const radius = width / 1.15;
    return new Vector3(Math.sin(x / radius) * radius, y, (Math.cos(x / radius) - 1) * radius);
}
export function bentPlane(width, height) {
    const geometry = new PlaneGeometry(width, height, 48, 12);
    const positions = geometry.attributes.position;
    for (let i = 0; i < positions.count; i++) {
        const p = bentPoint(positions.getX(i), positions.getY(i), width);
        positions.setXYZ(i, p.x, p.y, p.z);
    }
    geometry.computeVertexNormals();
    return geometry;
}
export function projectPanel(panel, camera, width, height) {
    const { mesh, w, h, link } = panel;
    const project = (x, y) => {
        const p = mesh.localToWorld(bentPoint(x, y, w)).project(camera);
        return [(p.x + 1) * width / 2, (1 - p.y) * height / 2];
    };
    const outline = [];
    for (let i = 0; i <= 24; i++) outline.push(project((i / 24 - .5) * w, h / 2));
    for (let i = 24; i >= 0; i--) outline.push(project((i / 24 - .5) * w, -h / 2));
    link.style.clipPath = `polygon(${outline.map(p => `${p[0]}px ${p[1]}px`).join(',')})`;
    const left = project(-w / 2 + .12, -h / 2 + .27);
    const right = project(-w / 2 + .45, -h / 2 + .27);
    const angle = Math.atan2(right[1] - left[1], right[0] - left[0]);
    const caption = link.querySelector('span');
    caption.style.transform = `translate(${left[0]}px,${left[1]}px) rotate(${angle}rad) translateY(-100%)`;
    caption.style.setProperty('--caption-width', `${Math.max(90, Math.min(width * .7, w * height / 7))}px`);
}
