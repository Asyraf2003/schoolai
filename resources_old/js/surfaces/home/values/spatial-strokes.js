import { CatmullRomCurve3, Vector3 } from 'three';
import { Line2 } from 'three/addons/lines/Line2.js';
import { LineGeometry } from 'three/addons/lines/LineGeometry.js';
import { LineMaterial } from 'three/addons/lines/LineMaterial.js';

const SAMPLE_COUNT = 220;

/*
 * Desktop camera at z=9 with a 46deg FOV sees roughly y=-3.8..3.8 and,
 * at 16:10-ish desktop aspect ratios, x=-6.1..6.1. Keep the authored
 * strokes inside that corridor so the line itself, not an off-screen control
 * polygon, is what the user actually sees.
 */
const PATHS = [
    {
        // C: kanan atas -> sapu kiri -> turun -> kembali ke kanan bawah.
        points: [
            [6.55, 3.55, .08], [5.55, 3.32, .08], [3.25, 2.72, .08],
            [.15, 2.38, .08], [-3.35, 2.08, .08], [-4.75, .72, .08],
            [-4.82, -1.08, .08], [-3.52, -2.42, .08], [-.55, -2.86, .08],
            [3.18, -2.52, .08], [6.45, -3.42, .08],
        ],
        width: .075,
        reveal: [0, .68],
    },
    {
        // S: kiri atas -> silang tengah -> kanan bawah, mulai sebelum C selesai.
        points: [
            [-6.55, 3.48, .16], [-4.72, 2.84, .16], [-1.55, 2.38, .16],
            [2.55, 2.08, .16], [4.38, 1.02, .16], [3.62, .14, .16],
            [1.22, -.38, .16], [-2.18, -.62, .16], [-4.12, -1.42, .16],
            [-2.92, -2.22, .16], [.18, -2.64, .16], [3.45, -2.72, .16],
            [6.45, -3.42, .16],
        ],
        width: .062,
        reveal: [.18, .82],
    },
    {
        // U/O: menimpa awal C dari kanan, membuat U + loop, keluar kiri bawah.
        points: [
            [6.55, 3.55, .34], [5.55, 3.32, .34], [3.25, 2.72, .34],
            [4.15, 1.55, .34], [4.28, -.72, .34], [3.02, -2.22, .34],
            [.62, -2.78, .34], [-2.12, -2.34, .34], [-3.48, -.96, .34],
            [-3.22, .72, .34], [-1.72, 1.42, .34], [.28, 1.26, .34],
            [1.62, .46, .34], [1.72, -.48, .34], [.92, -1.18, .34],
            [-.18, -1.42, .34], [-1.28, -.96, .34], [-1.68, .02, .34],
            [-2.18, -1.72, .34], [-3.62, -2.68, .34], [-6.38, -3.42, .34],
        ],
        width: .068,
        reveal: [.50, 1],
    },
];

export const clamp = (value) => Math.min(1, Math.max(0, value));
export const rangeProgress = (value, start, end) => (
    clamp((value - start) / Math.max(.0001, end - start))
);

function writePoint(buffer, offset, point) {
    buffer[offset] = point.x;
    buffer[offset + 1] = point.y;
    buffer[offset + 2] = point.z;
}

function writeHead(buffer, offset, from, to, amount) {
    buffer[offset] = from.x + (to.x - from.x) * amount;
    buffer[offset + 1] = from.y + (to.y - from.y) * amount;
    buffer[offset + 2] = from.z + (to.z - from.z) * amount;
}

function createStroke(definition) {
    const controls = definition.points.map((point) => new Vector3(...point));
    const curve = new CatmullRomCurve3(controls, false, 'centripetal');
    const master = curve.getPoints(SAMPLE_COUNT);
    const buffer = new Float32Array((master.length + 1) * 3);
    const geometry = new LineGeometry();
    const first = master[0];
    geometry.setPositions([
        first.x, first.y, first.z,
        first.x, first.y, first.z,
    ]);
    const material = new LineMaterial({
        alphaToCoverage: true,
        color: 0xffffff,
        depthTest: true,
        depthWrite: false,
        linewidth: definition.width,
        opacity: 0,
        transparent: true,
        worldUnits: true,
    });
    const line = new Line2(geometry, material);
    line.frustumCulled = false;

    return { buffer, definition, geometry, line, master, material };
}

export function createValuesStrokes() {
    return PATHS.map(createStroke);
}

export function revealStroke(stroke, progress) {
    const { buffer, geometry, master, material } = stroke;
    const scaled = clamp(progress) * (master.length - 1);
    const fullIndex = Math.floor(scaled);
    const fraction = scaled - fullIndex;
    let pointCount = 0;

    for (let index = 0; index <= fullIndex; index += 1) {
        writePoint(buffer, pointCount * 3, master[index]);
        pointCount += 1;
    }

    if (fullIndex < master.length - 1) {
        writeHead(
            buffer, pointCount * 3, master[fullIndex],
            master[fullIndex + 1], fraction,
        );
        pointCount += 1;
    }

    if (pointCount === 1) {
        writePoint(buffer, 3, master[0]);
        pointCount = 2;
    }

    geometry.setPositions(buffer.subarray(0, pointCount * 3));
    material.opacity = clamp(progress * 12);
}
