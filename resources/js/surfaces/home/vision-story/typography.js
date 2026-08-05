const UNIT_CLASS = 'vision-paper__reveal-unit';
const WORD_CLASS = 'vision-paper__reveal-word';
const READY_ATTRIBUTE = 'data-vision-typography-ready';
const clamp = (value) => Math.max(0, Math.min(1, value));

function graphemes(value) {
    if (typeof Intl?.Segmenter === 'function') {
        const segmenter = new Intl.Segmenter(undefined, {
            granularity: 'grapheme',
        });
        return Array.from(segmenter.segment(value), ({ segment }) => segment);
    }
    return Array.from(value);
}

function textNodes(element) {
    const walker = document.createTreeWalker(element, NodeFilter.SHOW_TEXT);
    const nodes = [];
    let current = walker.nextNode();
    while (current) {
        if (current.nodeValue?.trim()) nodes.push(current);
        current = walker.nextNode();
    }
    return nodes;
}

function createUnit(text, word = false) {
    const unit = document.createElement('span');
    unit.className = word ? `${WORD_CLASS} ${UNIT_CLASS}` : UNIT_CLASS;
    unit.setAttribute('aria-hidden', 'true');
    unit.textContent = text;
    return unit;
}

function splitTarget(target, mode, preserveWords) {
    if (target.hasAttribute(READY_ATTRIBUTE)) {
        return Array.from(target.querySelectorAll(`.${UNIT_CLASS}`));
    }

    const label = (target.textContent || '').replace(/\s+/gu, ' ').trim();
    if (label) target.setAttribute('aria-label', label);

    textNodes(target).forEach((node) => {
        const fragment = document.createDocumentFragment();
        (node.nodeValue || '').split(/(\s+)/u).forEach((segment) => {
            if (!segment) return;
            if (/^\s+$/u.test(segment)) {
                fragment.append(document.createTextNode(segment));
                return;
            }
            if (mode === 'vision' || preserveWords) {
                fragment.append(createUnit(segment, true));
                return;
            }
            const word = document.createElement('span');
            word.className = WORD_CLASS;
            word.setAttribute('aria-hidden', 'true');
            graphemes(segment).forEach((character) => {
                word.append(createUnit(character));
            });
            fragment.append(word);
        });
        node.replaceWith(fragment);
    });

    target.setAttribute(READY_ATTRIBUTE, 'true');
    return Array.from(target.querySelectorAll(`.${UNIT_CLASS}`));
}

function staggerDelay(index, total, span) {
    return total <= 1 ? 0 : (index / (total - 1)) * span;
}

function createSequence(units, keyframesFor, optionsFor) {
    const animations = units.map((unit, index) => {
        const options = optionsFor(index, units.length);
        const animation = unit.animate(keyframesFor(index), {
            ...options,
            fill: 'both',
        });
        const endTime = options.delay + options.duration;
        animation.pause();
        animation.currentTime = endTime;
        return { animation, endTime };
    });
    const totalTime = Math.max(1, ...animations.map(({ endTime }) => endTime));

    function setProgress(progress) {
        const time = clamp(progress) * totalTime;
        animations.forEach(({ animation, endTime }) => {
            animation.pause();
            animation.currentTime = Math.min(time, endTime);
        });
    }

    return {
        setProgress,
        reset: () => setProgress(0),
        finish: () => setProgress(1),
        play() {
            animations.forEach(({ animation }) => {
                animation.pause();
                animation.currentTime = 0;
                animation.playbackRate = 1;
                animation.play();
            });
        },
        destroy() {
            animations.forEach(({ animation }) => animation.cancel());
        },
    };
}

function createVisionSequence(units, rtl) {
    const x = [-18, 12, -9, 16, -13, 8];
    const y = [-5, 6, 3, -4, 7, -2];
    const z = [120, 90, 150, 105, 135, 80];
    const rotate = [-16, 12, -9, 15, -12, 8];
    const direction = rtl ? -1 : 1;

    return createSequence(
        units,
        (index) => {
            const slot = index % x.length;
            const start = `perspective(900px) translate3d(${x[slot] * direction}px, ${y[slot]}px, ${z[slot]}px) rotateX(${rotate[slot]}deg)`;
            return [
                { opacity: 0, transform: start },
                {
                    opacity: 1,
                    transform: 'perspective(900px) translate3d(0, 0, 0) rotateX(0deg)',
                },
            ];
        },
        (index, total) => ({
            duration: 320,
            delay: staggerDelay(index, total, 150),
            easing: 'cubic-bezier(.16,1,.3,1)',
        }),
    );
}

function createMissionSequence(units) {
    return createSequence(
        units,
        () => [
            { transform: 'scaleY(0.001)' },
            { transform: 'scaleY(1)' },
        ],
        (index, total) => ({
            duration: 220,
            delay: staggerDelay(index, total, 180),
            easing: 'cubic-bezier(.55,.055,.675,.19)',
        }),
    );
}

function unitsFor(root, name, preserveWords) {
    return Array.from(
        root.querySelectorAll(`[data-vision-typography="${name}"]`),
    ).flatMap((target) => splitTarget(target, name, preserveWords));
}

export function prepareTypography(root) {
    const rtl = document.documentElement.dir === 'rtl';
    unitsFor(root, 'vision', true);
    unitsFor(root, 'mission', rtl);
}

export function createTypographyEntrance(root) {
    const rtl = document.documentElement.dir === 'rtl';
    const vision = createVisionSequence(unitsFor(root, 'vision', true), rtl);
    const mission = createMissionSequence(unitsFor(root, 'mission', rtl));

    return {
        setVisionProgress: (progress) => vision.setProgress(progress),
        setMissionProgress: (progress) => mission.setProgress(progress),
        resetVision: () => vision.reset(),
        playVision: () => vision.play(),
        finishVision: () => vision.finish(),
        resetMission: () => mission.reset(),
        finishMission: () => mission.finish(),
        reset() {
            vision.reset();
            mission.reset();
        },
        play() {
            vision.play();
            mission.play();
        },
        finish() {
            vision.finish();
            mission.finish();
        },
        destroy() {
            vision.destroy();
            mission.destroy();
        },
    };
}
