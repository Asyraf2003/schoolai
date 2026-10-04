// Optional browser animation adapter. Semantic labels remain the source of truth.
export function mountHeaderMotion(root) {
    const preference = window.matchMedia('(prefers-reduced-motion: reduce)');
    const capability = window.matchMedia('(min-width: 768px) and (hover: hover)');
    const labels = [...root.querySelectorAll('[data-menu-label]')];
    const animations = new Set();
    const restores = [];
    if (!Element.prototype.animate || !Element.prototype.getAnimations) return () => {};
    labels.forEach(label => {
        const text = label.textContent;
        const visual = document.createElement('span');
        visual.className = 'menu-roll';
        visual.setAttribute('aria-hidden', 'true');
        const semantic = document.createElement('span');
        semantic.className = 'sr-only';
        semantic.textContent = text;
        const segments = document.documentElement.dir === 'rtl' ? [text] : Array.from(text);
        segments.forEach(segment => {
            const character = document.createElement('span');
            character.textContent = segment === ' ' ? '\u00a0' : segment;
            visual.append(character);
        });
        label.replaceChildren(semantic, visual);
        restores.push(() => { label.textContent = text; });
    });
    function play(event) {
        if (preference.matches || !capability.matches) return;
        const control = event.target.closest('a, summary');
        if (!control || (event.relatedTarget instanceof Node && control.contains(event.relatedTarget))) return;
        control.querySelectorAll('.menu-roll > span').forEach((character, index) => {
            character.getAnimations().forEach(animation => animation.cancel());
            const animation = character.animate([
                { transform: 'translateY(0) rotateX(0)', opacity: 1 },
                { transform: 'translateY(-65%) rotateX(90deg)', opacity: 0, offset: .49 },
                { transform: 'translateY(65%) rotateX(-90deg)', opacity: 0, offset: .5 },
                { transform: 'translateY(0) rotateX(0)', opacity: 1 },
            ], { duration: 520, delay: index * 22, easing: 'cubic-bezier(.22,1,.36,1)' });
            animations.add(animation);
            animation.finished.catch(() => {}).finally(() => animations.delete(animation));
        });
    }
    function cancel() { animations.forEach(animation => animation.cancel()); animations.clear(); }
    root.addEventListener('pointerover', play);
    root.addEventListener('focusin', play);
    preference.addEventListener?.('change', cancel);
    return () => {
        cancel(); restores.forEach(restore => restore());
        root.removeEventListener('pointerover', play);
        root.removeEventListener('focusin', play);
        preference.removeEventListener?.('change', cancel);
    };
}
