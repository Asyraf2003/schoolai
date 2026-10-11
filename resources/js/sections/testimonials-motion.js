const clamp = value => Math.max(0, Math.min(1, value));
const smooth = value => { const p = clamp(value); return p * p * (3 - 2 * p); };

export const testimonialRevealDistance = (blurDuration = .20) => .22 + blurDuration;
export const testimonialDepartureStart = (blurDuration = .20) => 2 * testimonialRevealDistance(blurDuration);

export function testimonialIntroFrame(progress, blurDuration = .20) {
    const p = clamp(progress);
    const start = testimonialDepartureStart(blurDuration);
    const exit = clamp((p - start) / (1 - start));
    // Read the white sentence on Gallery black before any light transition.
    return { tone: Math.round(8 + 247 * exit * exit), y: -Math.max(0, p - start), rows: smooth((p - start) / Math.min(.12, 1 - start)) };
}

export function testimonialWordFrame(progress, index, count, blurDuration = .20) {
    const order = index / Math.max(1, count - 1);
    // Match Gallery's linear word opacity, 16px blur and .016/.22 stagger.
    const opacity = clamp((progress - order * .016) / .10);
    const sharp = clamp((progress - order * .22) / blurDuration);
    const start = testimonialDepartureStart(blurDuration);
    const exit = clamp((progress - start) / (1 - start));
    return { opacity: opacity * (1 - exit), blur: 16 * (1 - sharp) * (1 - exit) + 14 * exit };
}

export function testimonialTrackOffset(progress, travel, index, rtl) {
    const distance = (rtl ? 1 : -1) * travel;
    return distance * (index % 2 ? 1 - clamp(progress) : clamp(progress));
}
