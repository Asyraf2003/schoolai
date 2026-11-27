import { bindTestimonialsDrag } from './testimonials-drag.js';

const interval = 2000;
const stagger = 220;

export function mountTestimonialsRotation(board, rows, direction, signal) {
    // Pool cards from any 3rd+ row evenly into the 2 vertical phone columns; restore on destroy.
    const moved = [];
    rows.slice(2).forEach(extra => {
        const track = extra.firstElementChild;
        [...track.children].forEach((card, i) => {
            moved.push({ card, track });
            rows[i % 2].firstElementChild.appendChild(card);
        });
        extra.hidden = true;
    });
    const columns = rows.slice(0, 2).map(row => [...row.firstElementChild.children]);
    const cards = columns.flat();
    const slots = Array.from({ length: 6 }, (_, index) => {
        const column = index % 2;
        const row = Math.floor(index / 2);
        return { column, row, card: columns[column][row], sign: (column ? 1 : -1) * direction };
    });
    const cursors = [2, 2];
    const animations = new Map();
    const timers = new Set();
    let running = false;
    let entered = false;
    let userPaused = false;

    function place(slot, card) {
        card.hidden = false;
        card.inert = false;
        card.style.setProperty('--testimonials-slot', slot.row + 1);
        slot.card = card;
    }

    function resize() {
        const hidden = cards.filter(card => card.hidden);
        board.setAttribute('data-testimonials-measuring', '');
        hidden.forEach(card => { card.hidden = false; });
        const height = Math.max(...cards.map(card => card.offsetHeight));
        hidden.forEach(card => { card.hidden = true; });
        board.removeAttribute('data-testimonials-measuring');
        board.style.setProperty('--testimonials-phone-card-height', `${height}px`);
    }

    function queue(callback, delay) {
        const timer = setTimeout(() => { timers.delete(timer); callback(); }, delay);
        timers.add(timer);
    }

    function finish(card) {
        const record = animations.get(card);
        if (!record) return;
        animations.delete(card);
        record.animation.cancel();
        record.after?.();
    }

    function animate(card, frames, options, after) {
        finish(card);
        const record = { animation: card.animate(frames, options), after };
        animations.set(card, record);
        record.animation.finished.then(() => {
            if (animations.get(card) === record) finish(card);
        }).catch(() => {});
    }

    function enter(slot, delay = 0) {
        slot.card.removeAttribute('data-testimonial-pending');
        animate(slot.card, [
            { transform: `translateX(${slot.sign * 85}%)`, opacity: 0 },
            { transform: `translateX(${slot.sign * 28}%)`, opacity: .94, offset: .52 },
            { transform: 'translateX(0)', opacity: 1 },
        ], { duration: 460, delay, fill: 'backwards', easing: 'cubic-bezier(.2,.78,.26,1)' });
    }

    function replace(slot, step) {
        const list = columns[slot.column];
        const isShowing = card => slots.some(active => active.card === card) || animations.has(card);
        for (let attempts = 0; attempts < list.length; attempts++) {
            const cursor = (cursors[slot.column] + step + list.length) % list.length;
            cursors[slot.column] = cursor;
            const incoming = list[cursor];
            if (isShowing(incoming)) continue;

            const old = slot.card;
            finish(old);
            old.inert = true;
            animate(old, [
                { transform: 'translateY(0) scale(1)', opacity: 1 },
                { transform: 'translateY(5px) scale(.95)', opacity: .22, offset: .5 },
                { transform: 'translateY(8px) scale(.92)', opacity: 0 },
            ], { duration: 470, easing: 'ease-in-out' }, () => {
                old.hidden = true;
                old.inert = false;
                old.style.removeProperty('--testimonials-slot');
            });
            place(slot, incoming);
            enter(slot, 70);
            break;
        }
    }

    function clear() {
        timers.forEach(clearTimeout);
        timers.clear();
        animations.forEach((record, card) => finish(card));
        if (entered) slots.forEach(({ card }) => card.removeAttribute('data-testimonial-pending'));
    }

    function arm(delay = interval) {
        if (running && !userPaused && !board.matches(':focus-within')) {
            queue(() => {
                wave(1);
                arm();
            }, delay);
        }
    }

    function wave(step) {
        slots.forEach((slot, index) => queue(() => replace(slot, step), index * stagger));
    }

    function start() {
        if (running) return;
        running = true;
        if (!entered) {
            entered = true;
            slots.forEach((slot, index) => queue(() => enter(slot), index * 130));
        }
        arm();
    }

    function stop() {
        running = false;
        clear();
    }

    function manual(step) {
        clear();
        wave(step);
        arm(5000);
    }

    bindTestimonialsDrag(board, {
        press() { clear(); arm(4500); },
        move(_, travel) {
            const offset = Math.max(-28, Math.min(28, travel * .24));
            board.style.setProperty('--testimonials-swipe', `${offset}px`);
        },
        release(travel) {
            board.style.removeProperty('--testimonials-swipe');
            if (Math.abs(travel) >= 28) manual(-Math.sign(travel) * direction);
        },
    }, signal);

    board.tabIndex = 0;
    board.addEventListener('focusin', clear, { signal });
    board.addEventListener('focusout', event => {
        if (!board.contains(event.relatedTarget)) arm(5000);
    }, { signal });
    board.addEventListener('keydown', event => {
        if (event.key === 'Escape') { userPaused = true; clear(); }
        if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
        event.preventDefault();
        manual((event.key === 'ArrowRight' ? 1 : -1) * direction);
    }, { signal });

    cards.forEach(card => { card.hidden = true; });
    slots.forEach(slot => {
        place(slot, slot.card);
        slot.card.setAttribute('data-testimonial-pending', '');
    });
    resize();

    return {
        start, stop, resize,
        destroy() {
            stop();
            board.removeAttribute('tabindex');
            board.removeAttribute('data-testimonial-dragging');
            board.style.removeProperty('--testimonials-swipe');
            board.style.removeProperty('--testimonials-phone-card-height');
            cards.forEach(card => {
                card.hidden = false;
                card.inert = false;
                card.removeAttribute('data-testimonial-pending');
                card.style.removeProperty('--testimonials-slot');
            });
            moved.forEach(({ card, track }) => track.appendChild(card));
            rows.slice(2).forEach(extra => { extra.hidden = false; });
        },
    };
}
