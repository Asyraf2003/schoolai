export const clamp = (value) => Math.max(0, Math.min(1, value));

export function createVisionGeometry(root) {
    let start = 0;
    let distance = 1;

    function measure() {
        const rect = root.getBoundingClientRect();
        start = window.scrollY + rect.top;
        distance = Math.max(1, root.offsetHeight - window.innerHeight);
    }

    function readProgress() {
        return clamp((window.scrollY - start) / distance);
    }

    return { measure, readProgress };
}
