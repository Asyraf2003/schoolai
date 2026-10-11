const clamp = value => Math.max(0, Math.min(1, value));
const smooth = value => { const p = clamp(value); return p * p * (3 - 2 * p); };

export function testimonialIntroFrame(progress) {
    const p = clamp(progress);
    // Establish a >=7:1 black-text field before introducing any black ink.
    const tone = Math.round(8 + 247 * smooth(p / .7));
    const reveal = tone >= 150 ? smooth((p - .39) / .22) : 0;
    return { tone, opacity: reveal, y: 32 * (1 - reveal) };
}

export function testimonialTrackOffset(progress, travel, index, rtl) {
    const distance = (rtl ? 1 : -1) * travel;
    return distance * (index % 2 ? 1 - clamp(progress) : clamp(progress));
}
