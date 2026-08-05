const UNIT_CLASS = 'vision-paper__reveal-unit';
const READY_ATTRIBUTE = 'data-vision-typography-ready';
const REVEAL_DURATION = 420;
const REVEAL_SPAN = 720;

const clamp = (value) => Math.max(0, Math.min(1, value));

function normalizeLabel(value) {
    return value.replace(/\s+/gu, ' ').trim();
}

function segmentText(value, preserveArabicWords) {
    if (preserveArabicWords) return value.split(/(\s+)/u);

    if (typeof Intl?.Segmenter === 'function') {
        const segmenter = new Intl.Segmenter(undefined, { granularity: 'grapheme' });
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

function splitTarget(target, preserveArabicWords) {
    if (target.hasAttribute(READY_ATTRIBUTE)) {
        return Array.from(target.querySelectorAll(`.${UNIT_CLASS}`));
    }

    const label = normalizeLabel(target.textContent || '');
    if (label) target.setAttribute('aria-label', label);

    textNodes(target).forEach((node) => {
        const fragment = document.createDocumentFragment();
        const segments = segmentText(node.nodeValue || '', preserveArabicWords);

        segments.forEach((segment) => {
            if (/^\s+$/u.test(segment)) {
                fragment.append(document.createTextNode(segment));
                return;
            }

            const unit = document.createElement('span');
            unit.className = UNIT_CLASS;
            unit.setAttribute('aria-hidden', 'true');
            unit.textContent = segment;
            fragment.append(unit);
        });

        node.replaceWith(fragment);
    });

    target.setAttribute(READY_ATTRIBUTE, 'true');
    return Array.from(target.querySelectorAll(`.${UNIT_CLASS}`));
}

function createReveal(units) {
    if (!units.length) return { setProgress() {}, destroy() {} };

    const stagger = REVEAL_SPAN / Math.max(1, units.length - 1);
    const totalDuration = REVEAL_DURATION + (stagger * (units.length - 1));
    const animations = units.map((unit, index) => {
        const animation = unit.animate([
            { transform: 'scaleY(0)' },
            { transform: 'scaleY(1)' },
        ], {
            duration: REVEAL_DURATION,
            delay: index * stagger,
            easing: 'cubic-bezier(.55,.055,.675,.19)',
            fill: 'both',
        });

        animation.pause();
        animation.currentTime = 0;
        return animation;
    });

    return {
        setProgress(progress) {
            const time = clamp(progress) * totalDuration;
            animations.forEach((animation) => {
                animation.currentTime = time;
            });
        },
        destroy() {
            animations.forEach((animation) => animation.cancel());
        },
    };
}

function createGroup(root, name, preserveArabicWords) {
    const selector = `[data-vision-typography="${name}"]`;
    const units = Array.from(root.querySelectorAll(selector))
        .flatMap((target) => splitTarget(target, preserveArabicWords));

    return createReveal(units);
}

export function createTypographyReveal(root) {
    const preserveArabicWords = document.documentElement.dir === 'rtl';
    const vision = createGroup(root, 'vision', preserveArabicWords);
    const mission = createGroup(root, 'mission', preserveArabicWords);

    return {
        setVisionProgress(progress) {
            vision.setProgress(progress);
        },
        setMissionProgress(progress) {
            mission.setProgress(progress);
        },
        destroy() {
            vision.destroy();
            mission.destroy();
        },
    };
}
