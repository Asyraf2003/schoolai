import { bindTestimonialsDrag } from './testimonials-drag.js';

export function mountTestimonialsLoop(rows, direction, signal) {
    let running = false;
    let frame = 0;
    let time;
    const rails = rows.map((row, index) => {
        const track = row.firstElementChild;
        const cards = [...track.children];
        const state = { row, track, cards, phase: index / 2, step: 1, leading: -1, drift: index % 2 === 0 ? -1 : 1 };

        function shift(amount) {
            state.phase += amount;
            paintRail(state);
        }

        bindTestimonialsDrag(row, {
            move: pixels => shift(-pixels * direction / state.step),
            release() {},
        }, signal);
        row.tabIndex = 0;
        row.addEventListener('keydown', event => {
            if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
            event.preventDefault();
            shift((event.key === 'ArrowRight' ? 1 : -1) * direction);
        }, { signal });
        row.addEventListener('focusin', event => {
            const card = event.target.closest('[data-testimonial-card]');
            if (!card) return;
            const bounds = row.getBoundingClientRect();
            const box = card.getBoundingClientRect();
            if (box.left >= bounds.left && box.right <= bounds.right) return;
            state.phase = cards.indexOf(card);
            paintRail(state);
        }, { signal });

        return state;
    });

    function paintRail(rail) {
        const count = rail.cards.length;
        const phase = ((rail.phase % count) + count) % count;
        const leading = Math.floor(phase);
        if (rail.leading !== leading) {
            rail.cards.forEach((card, index) => { card.style.order = (index - leading + count) % count; });
            rail.leading = leading;
        }
        rail.track.style.transform = `translate3d(${(leading - phase) * rail.step * direction}px, 0, 0)`;
    }

    function resize() {
        rails.forEach(rail => {
            const gap = parseFloat(getComputedStyle(rail.track).columnGap) || 0;
            rail.step = rail.cards[0].getBoundingClientRect().width + gap;
            paintRail(rail);
        });
    }

    function tick(now) {
        frame = 0;
        if (!running) return;
        const seconds = time === undefined ? 0 : Math.min((now - time) / 1000, .05);
        time = now;
        rails.forEach(rail => {
            if (rail.row.matches(':hover, :focus-within, [data-testimonial-dragging]')) return;
            rail.phase += rail.drift * 23 * seconds / rail.step;
            paintRail(rail);
        });
        frame = requestAnimationFrame(tick);
    }

    function stop() {
        running = false;
        cancelAnimationFrame(frame);
        frame = 0;
        time = undefined;
    }

    resize();
    return {
        resize,
        start() { if (!running) { running = true; frame = requestAnimationFrame(tick); } },
        stop,
        destroy() {
            stop();
            rails.forEach(({ row, track, cards }) => {
                row.removeAttribute('tabindex');
                row.removeAttribute('data-testimonial-dragging');
                track.style.removeProperty('transform');
                cards.forEach(card => card.style.removeProperty('order'));
            });
        },
    };
}
