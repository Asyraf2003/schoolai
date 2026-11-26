export function trackPacingTransitions(root, signal) {
    const about = new Map();
    const program = new Map();
    const pending = new Map();

    root.addEventListener('transitionrun', event => {
        const target = event.target;
        if (target.closest('[data-about]')) pending.set(target, about);
        else if (target.closest('[data-program-cards]')) pending.set(target, program);
    }, { signal });

    // Only CSS owners that emitted a transition event are inspected. Retain
    // the resulting clocks until they finish or are replaced on that element.
    function collect() {
        for (const [target, active] of pending) {
            const clocks = (target.getAnimations?.() ?? []).flatMap(animation => {
                const end = animation.effect.getComputedTiming().endTime;
                return Number.isFinite(end) ? [{ animation, end }] : [];
            });
            if (clocks.length) active.set(target, clocks);
        }
        pending.clear();
    }
    function visit(active, callback) {
        for (const [target, clocks] of active) {
            let running = false;
            for (const { animation, end } of clocks) {
                if (animation.playState !== 'running') continue;
                running = true;
                const left = Math.max(0, end - Number(animation.currentTime ?? 0));
                if (left) callback(target, left);
            }
            if (!running) active.delete(target);
        }
    }
    return {
        aboutMoving() {
            collect();
            for (const clocks of about.values()) {
                if (clocks.some(({ animation }) => animation.playState === 'running')) return true;
            }
            return false;
        },
        visitAbout(callback) { visit(about, callback); },
        visitProgram(callback) { visit(program, callback); },
    };
}
