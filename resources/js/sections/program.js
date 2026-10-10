import { observeProgram } from './program-reveal.js';
import { mountProgramHeading } from './program-heading.js';
import { mountProgramDialog } from './program-dialog.js';

export function mountProgram(root, { loadKinetic } = {}) {
    if (!root) return { suspend() {}, resume() {}, dispose() {} };
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    let prepared;
    let disposed = false;
    const prepare = () => {
        if (disposed) return Promise.resolve(null);
        if (!prepared) {
            const loader = loadKinetic ?? (async () => {
                const [{ loadProgramGsap }, { createProgramKinetic }] = await Promise.all([
                    import('./program-gsap.js'), import('./program-kinetic.js'),
                ]);
                const gsap = await loadProgramGsap(signal);
                return createProgramKinetic(gsap, root, document.documentElement.dir === 'rtl');
            });
            prepared = Promise.resolve().then(loader).catch(() => {
                root.dataset.programAnimation = 'fallback';
                return null;
            }).then(adapter => {
                if (disposed) { adapter?.reset(); return null; }
                root.dataset.programAnimation = adapter ? 'kinetic' : 'fallback';
                return adapter;
            });
        }
        return prepared;
    };
    const stopReveal = observeProgram(root, signal);
    const heading = mountProgramHeading(root);
    const dialog = mountProgramDialog(root, prepare, signal);
    document.addEventListener('visibilitychange', () => { if (document.hidden) dialog.suspend(); }, { signal });
    return {
        suspend() { dialog.suspend(); heading.suspend(); },
        resume() { dialog.resume(); heading.resume(); },
        dispose() { disposed = true; dialog.dispose(); heading.dispose(); stopReveal(); lifecycle.abort(); },
    };
}
