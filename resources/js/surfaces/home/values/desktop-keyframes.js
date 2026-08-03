import { clamp, mix, smooth } from './motion.js';

const SAMPLE_TIMES = [0, 0.1, 0.22, 0.39, 0.57, 0.77, 1];
const X_FACTORS = [0.93, 0.93, 0.955, 0.975, 0.988, 0.996, 1];
const Y_SAMPLES = [
    [4.191, 7.161, 3.547, -3.328],
    [4.191, 7.161, 3.547, -3.328],
    [-7.55, -5.996, 1.071, 7.153],
    [5.806, 8.509, 3.39, -4.847],
    [-1.307, 6.976, 8.845, 2.582],
    [5.045, 9.772, 5.515, -3.812],
    [8.36, 0.036, -8.321, -9.027],
];
const RZ_SAMPLES = [
    [-12.639, -4.213, 4.213, 12.639],
    [-12.639, -4.213, 4.213, 12.639],
    [-7.612, -2.537, 2.537, 7.612],
    [-0.746, -0.249, 0.249, 0.746],
    [-0.072, -0.024, 0.024, 0.072],
    [0, 0, 0, 0],
    [0, 0, 0, 0],
];
const RY_SAMPLES = [
    [180, 180, 180, 180],
    [123.539, 150.52, 167.831, 179.079],
    [65.918, 107.185, 136.954, 156.925],
    [14.942, 45.001, 80.369, 114.728],
    [-10.699, 6.053, 30.105, 59.303],
    [-17.398, -16.491, -7.624, 8.021],
    [0, 0, 0, 0],
];

function segmentAt(amount) {
    const value = clamp(amount);

    for (let index = 1; index < SAMPLE_TIMES.length; index += 1) {
        if (value <= SAMPLE_TIMES[index]) {
            const start = SAMPLE_TIMES[index - 1];
            const end = SAMPLE_TIMES[index];
            return {
                index: index - 1,
                amount: smooth((value - start) / Math.max(0.0001, end - start)),
            };
        }
    }

    return { index: SAMPLE_TIMES.length - 2, amount: 1 };
}

function sampleScalar(values, amount) {
    const segment = segmentAt(amount);
    return mix(
        values[segment.index],
        values[segment.index + 1],
        segment.amount,
    );
}

function sampleCard(values, cardIndex, amount) {
    return sampleScalar(
        values.map((sample) => sample[cardIndex]),
        amount,
    );
}

function rowSlot(geometry) {
    const sideSpace = Math.max(32, geometry.viewportWidth * 0.045);
    const available = (
        geometry.viewportWidth - sideSpace * 2 - geometry.cardWidth
    ) / 3;

    return Math.min(
        geometry.cardWidth * 1.03,
        Math.max(geometry.cardWidth * 0.72, available),
    );
}

export function measuredDesktopPose(index, amount, geometry, baseY) {
    const yScale = clamp(geometry.cardWidth / 400, 0.78, 1.12);
    const slot = rowSlot(geometry);

    return {
        x: (index - 1.5) * slot * sampleScalar(X_FACTORS, amount),
        y: baseY + sampleCard(Y_SAMPLES, index, amount) * yScale,
        z: -index * 4,
        rx: 0,
        ry: sampleCard(RY_SAMPLES, index, amount),
        rz: sampleCard(RZ_SAMPLES, index, amount),
        scale: 1,
        opacity: 1,
    };
}
