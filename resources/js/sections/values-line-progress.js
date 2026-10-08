// Match each screen's arc length without a speed jump at screen boundaries.
export function createLineProgress(stops) {
    const spans = stops.slice(1).map((stop, index) => stop - stops[index]);
    const tangents = stops.map((_, index) => {
        if (index === 0) return spans[0];
        if (index === spans.length) return spans.at(-1);
        return 2 * spans[index - 1] * spans[index] / (spans[index - 1] + spans[index]);
    });
    return progress => {
        const scaled = Math.min(1, Math.max(0, progress)) * spans.length;
        const index = Math.min(spans.length - 1, Math.floor(scaled));
        const t = scaled - index;
        const t2 = t * t;
        const t3 = t2 * t;
        const length = (2 * t3 - 3 * t2 + 1) * stops[index]
            + (t3 - 2 * t2 + t) * tangents[index]
            + (-2 * t3 + 3 * t2) * stops[index + 1]
            + (t3 - t2) * tangents[index + 1];
        return length / stops.at(-1);
    };
}
