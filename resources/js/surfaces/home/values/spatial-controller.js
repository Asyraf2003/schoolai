export function createValuesSpatialBridge(root, host, requestRender) {
    let active = false;
    let destroyed = false;
    let enabled = false;
    let generation = 0;
    let loading = null;
    let scene = null;
    let suspended = false;

    function setState(state) {
        root.dataset.valuesSpatialState = state;
        if (host) host.dataset.valuesSpatialState = state;
    }

    function disposeScene() {
        generation += 1;
        scene?.destroy();
        scene = null;
        loading = null;
        setState('static');
    }

    function load() {
        if (!host || loading || scene || !enabled || !active
            || suspended || destroyed) return;

        const requestedGeneration = generation;
        setState('loading');
        loading = import('./spatial-scene.js')
            .then(({ createValuesSpatialScene }) => {
                if (destroyed || !enabled || requestedGeneration !== generation) return;
                scene = createValuesSpatialScene(host);
                loading = null;
                setState('ready');
                requestRender();
            })
            .catch(() => {
                loading = null;
                setState('failed');
            });
    }

    function sync() {
        if (!enabled || !active || suspended || destroyed) {
            scene?.suspend();
            if (scene) setState('suspended');
            return;
        }
        scene?.resume();
        if (scene) setState('ready');
        else load();
    }

    setState('static');

    return {
        setActive(value) { active = value; sync(); },
        setEnabled(value) {
            enabled = value;
            if (!enabled) disposeScene();
            else sync();
        },
        suspend() { suspended = true; sync(); },
        resume() { suspended = false; sync(); },
        update(frame) {
            if (enabled && active && !suspended) scene?.update(frame);
        },
        destroy() { destroyed = true; disposeScene(); },
    };
}
