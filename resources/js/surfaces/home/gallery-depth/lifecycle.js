export const GalleryLifecycleState = Object.freeze({
    Semantic: 'SEMANTIC',
    StaticReady: 'STATIC_READY',
    Fetching: 'FETCHING',
    Prepared: 'PREPARED',
    EnhancementReady: 'ENHANCEMENT_READY',
    Active: 'ACTIVE',
    Suspended: 'SUSPENDED',
    Disposed: 'DISPOSED',
});

const transitions = new Map([
    [GalleryLifecycleState.Semantic, new Set([
        GalleryLifecycleState.StaticReady,
        GalleryLifecycleState.Disposed,
    ])],
    [GalleryLifecycleState.StaticReady, new Set([
        GalleryLifecycleState.Fetching,
        GalleryLifecycleState.Disposed,
    ])],
    [GalleryLifecycleState.Fetching, new Set([
        GalleryLifecycleState.StaticReady,
        GalleryLifecycleState.Prepared,
        GalleryLifecycleState.Disposed,
    ])],
    [GalleryLifecycleState.Prepared, new Set([
        GalleryLifecycleState.StaticReady,
        GalleryLifecycleState.EnhancementReady,
        GalleryLifecycleState.Suspended,
        GalleryLifecycleState.Disposed,
    ])],
    [GalleryLifecycleState.EnhancementReady, new Set([
        GalleryLifecycleState.StaticReady,
        GalleryLifecycleState.Active,
        GalleryLifecycleState.Suspended,
        GalleryLifecycleState.Disposed,
    ])],
    [GalleryLifecycleState.Active, new Set([
        GalleryLifecycleState.StaticReady,
        GalleryLifecycleState.Suspended,
        GalleryLifecycleState.Disposed,
    ])],
    [GalleryLifecycleState.Suspended, new Set([
        GalleryLifecycleState.StaticReady,
        GalleryLifecycleState.Active,
        GalleryLifecycleState.Disposed,
    ])],
]);

const readyStates = new Set([
    GalleryLifecycleState.EnhancementReady,
    GalleryLifecycleState.Active,
    GalleryLifecycleState.Suspended,
]);

const visibleEnhancementStates = new Set([
    GalleryLifecycleState.Active,
    GalleryLifecycleState.Suspended,
]);

export function createGalleryLifecycle(root, canvas, fallback) {
    let state = GalleryLifecycleState.Semantic;

    function syncDom() {
        const ready = readyStates.has(state);
        const visible = visibleEnhancementStates.has(state);
        const staticOnly = !ready;

        root.dataset.depthLifecycle = state;
        root.classList.toggle('is-depth-fallback', staticOnly);
        root.classList.toggle('is-depth-ready', ready);
        root.classList.toggle('is-depth-active', visible);

        fallback?.removeAttribute('hidden');
        fallback?.removeAttribute('aria-hidden');
        if (fallback) fallback.inert = false;

        canvas?.setAttribute('aria-hidden', 'true');
        canvas?.toggleAttribute(
            'aria-busy',
            state === GalleryLifecycleState.Fetching
                || state === GalleryLifecycleState.Prepared,
        );

        if (staticOnly || state === GalleryLifecycleState.Disposed) {
            root.classList.remove(
                'is-depth-end-ready',
                'is-depth-transitioning',
                'is-depth-leaving',
            );
        }
    }

    function transition(nextState) {
        if (state === nextState) {
            syncDom();
            return true;
        }
        if (!transitions.get(state)?.has(nextState)) return false;

        state = nextState;
        syncDom();
        return true;
    }

    return {
        get state() {
            return state;
        },
        is(nextState) {
            return state === nextState;
        },
        transition,
        staticReady() {
            return transition(GalleryLifecycleState.StaticReady);
        },
        dispose() {
            return transition(GalleryLifecycleState.Disposed);
        },
    };
}
