const VALUES_BLUE = [32, 56, 255];
const GALLERY_GREEN = [111, 155, 114];

function clamp(value) {
    return Math.max(0, Math.min(1, value));
}

function smoothstep(value) {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
}

export function bridgeColor(progress) {
    const amount = smoothstep(progress);
    const channels = VALUES_BLUE.map((channel, index) => Math.round(
        channel + ((GALLERY_GREEN[index] - channel) * amount),
    ));

    return `rgb(${channels.join(' ')})`;
}

export function kineticOpacity(cardExitProgress) {
    return 1 - smoothstep(cardExitProgress / 0.16);
}

export function worldOpacity(cardExitProgress) {
    return 1 - smoothstep(cardExitProgress / 0.06);
}

