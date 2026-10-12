import { valuesPaintTravel, compactFlipTravel } from './scroll-pacing-poses.js';
import { valuesCompactedProgress } from './values-card-keyframes.js';
import { testimonialRevealDistance, testimonialDepartureStart } from './testimonials-motion.js';

export function measurePacingRanges(root) {
    const ranges = [];
    const top = node => node.getBoundingClientRect().top + scrollY;
    const add = (name, start, end, seconds, segments = 8, travel) => {
        if (end > start) ranges.push({ name, start, end, speed: seconds ? (end - start) / seconds : Infinity, segments, travel });
    };
    const about = root.querySelector('[data-about]');
    if (about) {
        const transitions = about.getAnimations?.({ subtree: true }) ?? [];
        const moving = transitions.some(animation => animation.playState === 'running');
        const duration = parseFloat(getComputedStyle(about).transitionDuration) || .7;
        about.querySelectorAll('[data-about-story]').forEach(story => {
            if (story.dataset.aboutStory === about.dataset.activeStory && !moving) return;
            add('about-change', top(story) - innerHeight * .55,
                top(story) + story.offsetHeight - innerHeight * .2, duration);
        });
    }
    for (const [name, seconds] of [['program', .76], ['values', .9]]) {
        const heading = root.querySelector(`[data-${name}-heading]`);
        if (heading?.dataset.headingState !== 'idle') continue;
        const lines = heading.querySelectorAll(`[data-${name}-heading-line]`).length;
        add(`${name}-heading`, top(heading) - innerHeight * .88,
            top(heading) + heading.offsetHeight, seconds * (lines > 1 ? 2 : 1));
    }
    const program = root.querySelector('[data-program][data-reveal-enabled]');
    program?.querySelectorAll('[data-program-card]:not([data-revealed="true"])').forEach(card => {
        add('program-card', top(card) - innerHeight + innerWidth * .12 + card.offsetHeight * .08,
            top(card) + card.offsetHeight, 1.08);
    });
    const values = root.querySelector('[data-values]');
    if (values?.hasAttribute('data-line-ready') && !values.hasAttribute('data-cards-flow')) {
        const track = values.querySelector('[data-values-cards-track]');
        const viewport = values.querySelector('[data-values-cards-viewport]');
        if (getComputedStyle(viewport).position === 'sticky') {
            const start = top(track) - (parseFloat(getComputedStyle(values).scrollMarginBlockStart) || 0);
            const span = Math.max(1, track.offsetHeight - viewport.offsetHeight);
            add('values-entrance', start, start + valuesCompactedProgress(.052) * span, 0, 4);
            add('values-flip', start + valuesCompactedProgress(.052) * span,
                start + valuesCompactedProgress(.308) * span, 0, 8,
                (position, direction) => {
                    const native = (position - start) / span;
                    const painted = Number(values.dataset.cardsProgress);
                    // Spend one angular budget from the last painted pose.
                    return valuesPaintTravel(native, painted, span, direction);
                });
        } else {
            values.querySelectorAll('[data-values-card]').forEach(card => {
                const height = card.offsetHeight;
                const tablet = innerWidth >= 768;
                const start = top(card) - innerHeight + height * (tablet ? .16 : .5);
                const span = height * (tablet ? .62 : .5);
                const flip = card.querySelector('[data-values-card-inner]');
                const angle = Math.abs(parseFloat(flip.style.transform.match(/rotateY\(([-\d.]+)/)?.[1]));
                add('values-compact-flip', start, start + span, 0, 8,
                    (position, direction) => compactFlipTravel((position - start) / span, angle, span, direction));
            });
        }
    }
    const gallery = window.ScrollTrigger?.getById('gallery-v2');
    if (gallery?.enabled) {
        const span = gallery.end - gallery.start;
        const shrink = +root.querySelector('[data-gallery]').dataset.galleryShrinkStart;
        const phase = (name, a, b, segments) => add(name, gallery.start + a * span, gallery.start + b * span, 0, segments);
        phase('gallery-opening', 0, shrink, 6);
        phase('gallery-assembly', shrink, .52, 8);
        phase('gallery-rise', .52, .76, 6);
        phase('gallery-exit', .76, .9, 8);
        const reveal = shrink + (.52 - shrink) * .6;
        // Opacity needs intermediate paints; the stagger is one shared range,
        // not a separate brake applied again for every word.
        phase('gallery-words', reveal, reveal + .116, 5);
    }
    // Running CSS/WAAPI animations retain their own clock. Use only the
    // remaining visible travel/time; completed headings impose no restriction.
    for (const selector of ['[data-about]', '[data-program-heading]', '[data-values-heading]', '[data-program-cards]']) {
        const node = root.querySelector(selector);
        if (!node?.getAnimations) continue;
        const animations = node.getAnimations({ subtree: true });
        for (const animation of animations) {
            if (animation.playState !== 'running') continue;
            const timing = animation.effect.getComputedTiming();
            if (!Number.isFinite(timing.endTime)) continue;
            let seconds = Math.max(0, timing.endTime - Number(animation.currentTime ?? 0)) / 1000;
            if (!seconds) continue;
            if (node.dataset.headingState === 'revealing' && node.querySelectorAll('[data-program-heading-line], [data-values-heading-line]').length > 1) {
                seconds += Number(timing.duration) / 1000;
            }
            let target = animation.effect.target;
            if (target === node && selector === '[data-about]') {
                target = node.querySelector(`[data-about-story="${node.dataset.activeStory}"]`) ?? node;
            }
            const box = target.getBoundingClientRect();
            if (box.bottom <= 0 || box.top >= innerHeight) continue;
            add('timed-animation', Math.min(scrollY, top(target) - innerHeight), top(target) + box.height, seconds);
        }
    }
    const intro = root.querySelector('[data-testimonials-enabled] [data-testimonials-intro]');
    if (intro) {
        const start = top(intro), span = Math.max(1, intro.offsetHeight - innerHeight);
        const shrink = Number(root.querySelector('[data-gallery]')?.dataset.galleryShrinkStart);
        const duration = shrink > 0 ? .4 * (.52 - shrink) : .20;
        const sharpEnd = testimonialRevealDistance(duration);
        for (const [name, a, b, segments] of [['words', 0, .116, 5], ['sharp', .116, sharpEnd, 6], ['depart', testimonialDepartureStart(duration), 1, 8]]) {
            add(`testimonial-${name}`, start + a * span, start + b * span, 0, segments);
        }
    }
    return ranges;
}
