export function bindTestimonialsDrag(element, onDrag, signal) {
    let pointer;
    let suppressClick = false;

    element.addEventListener('pointerdown', event => {
        if (!event.isPrimary || event.button !== 0) return;
        suppressClick = false;
        pointer = { id: event.pointerId, x: event.clientX, y: event.clientY, movement: 0, dragging: false };
        onDrag.press?.();
    }, { signal });

    element.addEventListener('pointermove', event => {
        if (pointer?.id !== event.pointerId) return;
        const travel = event.clientX - pointer.x;
        const vertical = Math.abs(event.clientY - pointer.y);
        if (!pointer.dragging) {
            if (vertical > 9 && vertical > Math.abs(travel)) { pointer = null; return; }
            if (Math.abs(travel) < 9 || Math.abs(travel) <= vertical) return;
            element.setPointerCapture(event.pointerId);
            element.setAttribute('data-testimonial-dragging', '');
            pointer.dragging = true;
            suppressClick = true;
        }
        onDrag.move(travel - pointer.movement, travel);
        pointer.movement = travel;
    }, { signal });

    function finish(event) {
        if (pointer?.id !== event.pointerId) return;
        if (pointer.dragging) {
            element.removeAttribute('data-testimonial-dragging');
            onDrag.release(event.type === 'pointercancel' ? 0 : pointer.movement);
        }
        pointer = null;
    }

    element.addEventListener('pointerup', finish, { signal });
    element.addEventListener('pointercancel', finish, { signal });
    element.addEventListener('lostpointercapture', finish, { signal });
    element.addEventListener('dragstart', event => event.preventDefault(), { signal });
    element.addEventListener('click', event => {
        if (!suppressClick || !event.detail) return;
        suppressClick = false;
        event.preventDefault();
        event.stopPropagation();
    }, { capture: true, signal });
}
