// Pure Header policy. Browser observations arrive as values, never DOM nodes.
export function createHeaderState(render) {
    let state = { desktop: false, mobileOpen: false, panel: null, scrolled: false, concealed: false };
    let lastY = 0;
    let distance = 0;
    let direction = 0;
    function send(event) {
        if (event.type === 'viewport') {
            state = { ...state, desktop: event.desktop, mobileOpen: false, panel: null, concealed: false };
        } else if (event.type === 'mobile') {
            state = { ...state, mobileOpen: !state.desktop && !state.mobileOpen, panel: null, concealed: false };
        } else if (event.type === 'panel') {
            state = { ...state, panel: event.open ? event.id : (state.panel === event.id ? null : state.panel), concealed: false };
        } else if (event.type === 'dismiss') {
            state = { ...state, mobileOpen: false, panel: null, concealed: false };
        } else if (event.type === 'focus') {
            state = { ...state, concealed: false };
            distance = 0;
        } else if (event.type === 'scroll') {
            const delta = event.y - lastY;
            const nextDirection = Math.sign(delta);
            if (nextDirection !== direction) distance = 0;
            direction = nextDirection;
            distance += Math.abs(delta);
            lastY = event.y;
            state = { ...state, scrolled: event.y > (state.scrolled ? 24 : 48) };
            if (!state.desktop || !event.outsideHero || state.panel !== null || state.mobileOpen || event.focused) {
                state.concealed = false;
                distance = 0;
            } else if (distance >= 12) {
                state.concealed = direction > 0;
                distance = 0;
            }
        }
        render({ ...state });
    }
    return { send, snapshot: () => ({ ...state }) };
}
