import { flipAngle, flipLocal, responsiveFlipAngle, valuesCompactedProgress, valuesSourceProgress } from './values-card-keyframes.js';

let lower = 0, upper = 1;
for (let index = 0; index < 24; index++) {
    const middle = (lower + upper) / 2;
    if (flipAngle(middle) > 90) lower = middle; else upper = middle;
}
const mixedFaces = valuesCompactedProgress(.052 + (lower + upper) / 2 * .22 + .018);

// Read the original pose curves. Bound visible angular change rather than
// making the gentle start/end of the flip as slow as its steep middle.
export function valuesFlipTravel(progress, span, direction) {
    let maximum = (valuesCompactedProgress(.22) - valuesCompactedProgress(0)) * span / 4;
    const checkpoint = (mixedFaces - progress) * span * direction;
    if (checkpoint > .5) maximum = Math.min(maximum, checkpoint);
    const angles = Array.from({ length: 4 }, (_, index) => flipAngle(flipLocal(index, valuesSourceProgress(progress))));
    const change = distance => {
        let largest = 0;
        for (const fraction of [.25, .5, .75, 1]) {
            const source = valuesSourceProgress(progress + direction * distance * fraction / span);
            angles.forEach((angle, index) => { largest = Math.max(largest, Math.abs(flipAngle(flipLocal(index, source)) - angle)); });
        }
        return largest;
    };
    if (change(maximum) <= 45) return maximum;
    let safe = 0;
    for (let index = 0; index < 12; index++) {
        const middle = (safe + maximum) / 2;
        if (change(middle) > 45) maximum = middle; else safe = middle;
    }
    return safe;
}


export function valuesPaintTravel(native, painted, span, direction) {
    const anchor = Number.isFinite(painted) ? painted : native;
    return Math.max(0, valuesFlipTravel(anchor, span, direction) - (native - anchor) * span * direction);
}

export function compactFlipTravel(native, paintedAngle, span, direction) {
    const angle = Number.isFinite(paintedAngle) ? paintedAngle : responsiveFlipAngle(native);
    const target = Math.max(0, Math.min(180, angle - direction * 45));
    let low = 0, high = 1;
    for (let index = 0; index < 18; index++) {
        const middle = (low + high) / 2;
        if (responsiveFlipAngle(middle) > target) low = middle; else high = middle;
    }
    const endpoint = target === 0 ? 1 : target === 180 ? 0 : (low + high) / 2;
    const travel = Math.max(0, (endpoint - native) * span * direction);
    return travel > 0 && (target === 0 || target === 180) ? Math.max(1, travel) : travel;
}
