// Existing Program animation authority, requested only on detail intent.
const source = 'https://cdn.jsdelivr.net/npm/gsap@3.7.1/dist/gsap.min.js';

export function loadProgramGsap(signal) {
    if (window.gsap) return Promise.resolve(window.gsap);
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = source;
        script.async = true;
        script.dataset.programGsap = 'true';
        let settled = false;
        const finish = error => {
            if (settled) return;
            settled = true;
            clearTimeout(deadline);
            signal.removeEventListener('abort', abort);
            script.onload = script.onerror = null;
            if (error) { script.remove(); reject(error); }
            else resolve(window.gsap);
        };
        const abort = () => finish(new Error('Program animation preparation aborted'));
        // A network deadline bounds loading only; it never drives motion/layout.
        const deadline = setTimeout(() => finish(new Error('Program GSAP unavailable')), 5000);
        script.onload = () => finish(window.gsap ? null : new Error('Program GSAP unavailable'));
        script.onerror = () => finish(new Error('Program GSAP request failed'));
        signal.addEventListener('abort', abort, { once: true });
        if (signal.aborted) abort();
        else document.head.append(script);
    });
}
