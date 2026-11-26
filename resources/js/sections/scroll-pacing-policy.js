import { valuesPaintTravel, compactFlipTravel } from './scroll-pacing-poses.js';
import { valuesCompactedProgress } from './values-card-keyframes.js';
import { createPolicyCache } from './scroll-pacing-policy-cache.js';
import { trackPacingTransitions } from './scroll-pacing-transitions.js';

function measureGeometry(nodes, galleryStart, galleryEnd, shrink) {
    const { about, stories, program, cards, headings, values, track, viewport, valuesCards } = nodes;
    const ranges = [];
    const y = scrollY;
    const height = innerHeight;
    const width = innerWidth;
    const top = node => node.getBoundingClientRect().top + y;
    const add = (name, start, end, seconds, segments = 8, travel, enabled) => {
        if (end > start) ranges.push({ name, start, end, speed: seconds ? (end - start) / seconds : Infinity, segments, travel, enabled });
    };
    const aboutStories = new Map();
    if (about) {
        const duration = parseFloat(getComputedStyle(about).transitionDuration) || .7;
        for (const story of stories) {
            const start = top(story);
            const id = story.dataset.aboutStory;
            aboutStories.set(id, story);
            add('about-change', start - height * .55, start + story.offsetHeight - height * .2,
                duration, 8, undefined, moving => moving || id !== about.dataset.activeStory);
        }
    }
    for (const { name, node, lines } of headings) {
        if (!node) continue;
        const start = top(node);
        const seconds = name === 'program' ? .76 : .9;
        add(`${name}-heading`, start - height * .88, start + node.offsetHeight,
            seconds * (lines > 1 ? 2 : 1), 8, undefined, () => node.dataset.headingState === 'idle');
    }
    for (const card of cards) {
        const start = top(card);
        const size = card.offsetHeight;
        add('program-card', start - height + width * .12 + size * .08, start + size, 1.08, 8, undefined,
            () => program.hasAttribute('data-reveal-enabled') && card.dataset.revealed !== 'true');
    }
    if (track && viewport && values.hasAttribute('data-line-ready') && !values.hasAttribute('data-cards-flow')) {
        if (getComputedStyle(viewport).position === 'sticky') {
            const start = top(track) - (parseFloat(getComputedStyle(values).scrollMarginBlockStart) || 0);
            const span = Math.max(1, track.offsetHeight - viewport.offsetHeight);
            add('values-entrance', start, start + valuesCompactedProgress(.052) * span, 0, 4);
            add('values-flip', start + valuesCompactedProgress(.052) * span, start + valuesCompactedProgress(.308) * span, 0, 8,
                (position, direction) => valuesPaintTravel((position - start) / span, Number(values.dataset.cardsProgress), span, direction));
        } else {
            for (const { node, flip } of valuesCards) {
                const size = node.offsetHeight;
                const tablet = width >= 768;
                const start = top(node) - height + size * (tablet ? .16 : .5);
                const span = size * (tablet ? .62 : .5);
                add('values-compact-flip', start, start + span, 0, 8, (position, direction) => {
                    const angle = Math.abs(parseFloat(flip.style.transform.match(/rotateY\(([-\d.]+)/)?.[1]));
                    return compactFlipTravel((position - start) / span, angle, span, direction);
                });
            }
        }
    }
    if (galleryEnd > galleryStart) {
        const span = galleryEnd - galleryStart;
        const phase = (name, a, b, segments) => add(name, galleryStart + a * span, galleryStart + b * span, 0, segments);
        phase('gallery-opening', 0, shrink, 6);
        phase('gallery-assembly', shrink, .52, 8);
        phase('gallery-rise', .52, .76, 6);
        phase('gallery-exit', .76, .9, 8);
        const reveal = shrink + (.52 - shrink) * .6;
        phase('gallery-words', reveal, reveal + .116, 5);
    }
    return { about, aboutStories, ranges };
}

export function createPacingPolicy(root, signal, headingAnimations = () => {}) {
    const cache = createPolicyCache(root, signal, measureGeometry);
    const transitions = trackPacingTransitions(root, signal);
    const ranges = [];
    const timedRanges = [];
    let position;
    let timedCount = 0;
    let snapshot;
    function timed(target, remainingMs) {
        if (!remainingMs) return;
        const box = target.getBoundingClientRect();
        if (box.bottom <= 0 || box.top >= innerHeight) return;
        const start = Math.min(position, box.top + position - innerHeight);
        const end = box.bottom + position;
        const range = timedRanges[timedCount] ??= { name: 'timed-animation', segments: 8 };
        timedCount++;
        range.start = start;
        range.end = end;
        range.speed = (end - start) * 1000 / remainingMs;
        ranges.push(range);
    }
    function cssTimed(target, remainingMs) {
        if (target === snapshot.about) target = snapshot.aboutStories.get(target.dataset.activeStory) ?? target;
        timed(target, remainingMs);
    }
    return {
        invalidate: cache.invalidate,
        ranges(gallery, scrollPosition) {
            snapshot = cache.read(gallery);
            position = scrollPosition;
            ranges.length = 0;
            timedCount = 0;
            const { ranges: measured } = snapshot;
            const moving = transitions.aboutMoving();
            for (const range of measured) {
                if (!range.enabled || range.enabled(moving)) ranges.push(range);
            }
            transitions.visitAbout(cssTimed);
            headingAnimations(timed);
            transitions.visitProgram(timed);
            return ranges;
        },
    };
}
