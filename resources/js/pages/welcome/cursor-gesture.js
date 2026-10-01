export const SHAKE = Object.freeze({
    minVectorPx: 4,
    minSpeedPxPerMs: 0.5,
    reversalCosine: -0.55,
    reversalWindowMs: 650,
    reversalsPerBurst: 3,
    burstRefractoryMs: 650,
    burstWindowMs: 5000,
    burstsForAnger: 3,
    dizzyMs: 3000,
    annoyedMs: 3000,
    angryMs: 3000,
    cooldownMs: 3500,
});

export function createCursorGesture(readState, onDizzy, onSequence) {
    let lastX = null;
    let lastY = null;
    let lastMoveAt = 0;
    let previousVector = null;
    let reversalTimes = [];
    let burstTimes = [];
    let lastBurstAt = -Infinity;

    const registerShakeBurst = (now) => {
        const { emotionSequenceActive, cooldownUntil } = readState();
        if (
            emotionSequenceActive ||
            now < cooldownUntil ||
            now - lastBurstAt < SHAKE.burstRefractoryMs
        ) {
            return;
        }

        lastBurstAt = now;
        burstTimes = burstTimes.filter((time) => now - time <= SHAKE.burstWindowMs);
        burstTimes.push(now);

        if (burstTimes.length >= SHAKE.burstsForAnger) {
            onSequence();
            return;
        }

        onDizzy();
    };

    const detectShake = (event, now) => {
        const { baseState, emotionSequenceActive, cooldownUntil } = readState();
        if (
            baseState === 'disabled' ||
            emotionSequenceActive ||
            now < cooldownUntil
        ) {
            return;
        }

        if (lastX === null || lastY === null) {
            lastX = event.clientX;
            lastY = event.clientY;
            lastMoveAt = now;
            return;
        }

        const dx = event.clientX - lastX;
        const dy = event.clientY - lastY;
        const dt = Math.max(now - lastMoveAt, 1);
        const distance = Math.hypot(dx, dy);

        lastX = event.clientX;
        lastY = event.clientY;
        lastMoveAt = now;

        if (distance < SHAKE.minVectorPx) {
            return;
        }

        const speed = distance / dt;
        const currentVector = { dx, dy, distance };

        if (previousVector && speed >= SHAKE.minSpeedPxPerMs) {
            const dot = dx * previousVector.dx + dy * previousVector.dy;
            const cosine = dot / (distance * previousVector.distance);

            if (cosine <= SHAKE.reversalCosine) {
                reversalTimes = reversalTimes.filter(
                    (time) => now - time <= SHAKE.reversalWindowMs,
                );
                reversalTimes.push(now);

                if (reversalTimes.length >= SHAKE.reversalsPerBurst) {
                    reversalTimes = [];
                    registerShakeBurst(now);
                }
            }
        }

        previousVector = currentVector;
    };

    return { detectShake, reset() { reversalTimes = []; burstTimes = []; } };
}
