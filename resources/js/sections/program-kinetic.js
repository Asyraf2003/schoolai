import { programTypeIn, programTypeOut, programTypeLicense } from './program-type.js';

export function createProgramKinetic(gsap, root, rtl) {
    const dialog = root.querySelector('[data-program-dialog]');
    const type = root.querySelector('[data-program-type]');
    const lines = [...root.querySelectorAll('[data-program-type-line]')];
    const cards = [...root.querySelectorAll('[data-program-card]')];
    const header = root.querySelector('[data-program-header]');
    let timeline;
    let parts;
    const select = detail => ({
        copy: [...detail.querySelectorAll('[data-program-back], .program__detail-title, .program__description')],
        wrap: detail.querySelector('[data-program-image-wrap]'),
        image: detail.querySelector('[data-program-image]'),
    });
    const reset = () => {
        timeline?.kill();
        timeline = null;
        const targets = [type, ...lines, ...cards, header];
        if (parts) targets.push(...parts.copy, parts.wrap, parts.image);
        gsap.set(targets, { clearProps: 'transform,opacity,pointerEvents' });
    };
    return {
        referenceLicense: programTypeLicense,
        open(detail, revealed, complete) {
            reset();
            parts = select(detail);
            const typeIn = programTypeIn(gsap, type, lines, rtl);
            const entrance = typeIn.totalDuration() * .75 + .3;
            timeline = gsap.timeline({ onComplete: () => { timeline = null; complete(); } });
            timeline.to(cards, { duration: .8, ease: 'power2.inOut', opacity: 0, y: index => index % 2 ? '25%' : '-25%' }, 0)
                .to(header, { duration: .8, ease: 'power3', opacity: 0 }, 0)
                .add(typeIn.play(), .3)
                .call(() => { dialog.dataset.phase = 'detail'; revealed(); }, [], entrance)
                .set(parts.copy, { opacity: 0, y: '50%' }, entrance)
                .set(parts.wrap, { y: '100%' }, entrance)
                .set(parts.image, { y: '-100%' }, entrance)
                .to(parts.copy, { duration: 1, ease: 'expo', opacity: 1, y: '0%', stagger: .08 }, entrance)
                .to([parts.wrap, parts.image], { duration: 1, ease: 'expo', y: '0%' }, entrance);
        },
        close(detail, complete) {
            timeline?.kill();
            parts = select(detail);
            const typeOut = programTypeOut(gsap, type, lines);
            const returnAt = typeOut.totalDuration() * .7 + .5;
            timeline = gsap.timeline({ onComplete: () => { reset(); complete(); } });
            timeline.to(parts.copy, { duration: 1, ease: 'power4.in', opacity: 0, y: '50%', stagger: -.08 }, 0)
                .to(parts.wrap, { duration: 1, ease: 'power4.in', y: '100%' }, 0)
                .to(parts.image, { duration: 1, ease: 'power4.in', y: '-100%' }, 0)
                .add(typeOut.play(), .5)
                .call(() => { dialog.dataset.phase = 'opening'; }, [], returnAt)
                .to(header, { duration: .8, ease: 'power3', opacity: 1 }, returnAt)
                .to(cards, { duration: 1, ease: 'power3.inOut', opacity: 1, y: '0%' }, returnAt);
        },
        reset,
        suspend() { timeline?.pause(); },
        resume() { timeline?.resume(); },
    };
}
