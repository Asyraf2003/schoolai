let runtimePromise = null;

export function loadThreeRuntime() {
    if (!runtimePromise) {
        runtimePromise = import('./three-package.js').catch((error) => {
            runtimePromise = null;
            throw error;
        });
    }

    return runtimePromise;
}
