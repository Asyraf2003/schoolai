export function mountProgramHeading(root) {
  const heading = root.querySelector('[data-program-heading]');
  if (!heading) return () => {};

  const setRevealed = (revealed) => {
    root.classList.toggle('is-program-heading-revealed', revealed);
  };
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (reduced || !('IntersectionObserver' in window)) {
    setRevealed(true);
    return () => setRevealed(false);
  }

  const observer = new IntersectionObserver((entries) => {
    const entry = entries.find((candidate) => candidate.target === heading);
    if (!entry) return;
    const revealed = entry.isIntersecting && entry.intersectionRatio >= 0.16;
    requestAnimationFrame(() => setRevealed(revealed));
  }, {
    root: null,
    rootMargin: '0px 0px -12% 0px',
    threshold: [0, 0.16],
  });

  observer.observe(heading);

  return () => {
    observer.disconnect();
    setRevealed(false);
  };
}
