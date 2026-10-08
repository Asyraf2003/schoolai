// Program detail and Values drawing share the existing exact GSAP authority.
const base = 'https://cdn.jsdelivr.net/npm/gsap@3.7.1/dist/';
const pending = new Map();

function request(name) {
    if (window[name]) return Promise.resolve(window[name]);
    if (pending.has(name)) return pending.get(name);
    const promise = new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = `${base}${name === 'gsap' ? 'gsap' : 'ScrollTrigger'}.min.js`;
        script.async = true;
        if (name === 'gsap') script.dataset.programGsap = 'true';
        else script.dataset.valuesScrollTrigger = 'true';
        let settled = false;
        const finish = error => {
            if (settled) return;
            settled = true;
            clearTimeout(deadline);
            script.onload = script.onerror = null;
            if (error) { script.remove(); pending.delete(name); reject(error); }
            else resolve(window[name]);
        };
        // Loading deadline only; it does not drive scroll motion.
        const deadline = setTimeout(() => finish(new Error(`${name} unavailable`)), 5000);
        script.onload = () => finish(window[name] ? null : new Error(`${name} unavailable`));
        script.onerror = () => finish(new Error(`${name} request failed`));
        document.head.append(script);
    });
    pending.set(name, promise);
    return promise;
}

export function loadGsapLibrary(name, signal) {
    if (signal.aborted) return Promise.reject(new Error('Animation preparation aborted'));
    return new Promise((resolve, reject) => {
        const abort = () => reject(new Error('Animation preparation aborted'));
        signal.addEventListener('abort', abort, { once: true });
        request(name).then(value => {
            signal.removeEventListener('abort', abort);
            if (!signal.aborted) resolve(value);
        }, error => { signal.removeEventListener('abort', abort); reject(error); });
    });
}
