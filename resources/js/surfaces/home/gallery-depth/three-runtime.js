const THREE_MODULE_URL = 'https://cdn.jsdelivr.net/npm/three@0.183.0/build/three.module.min.js';

let runtimePromise = null;

export function loadThreeRuntime() {
    if (!runtimePromise) {
        runtimePromise = import(/* @vite-ignore */ THREE_MODULE_URL);
    }

    return runtimePromise;
}

export { THREE_MODULE_URL };
