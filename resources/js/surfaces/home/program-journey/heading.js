export function mountProgramHeading(root) {
  const heading = root.querySelector('[data-program-heading]');
  if (!heading) return () => {};

  const reveal = () => root.classList.add('is-program-heading-revealed');
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (reduced || !('IntersectionObserver' in window)) {
    reveal();
    return () => root.classList.remove('is-program-heading-revealed');
  }

  const observer = new IntersectionObserver((entries) => {
    if (!entries.some((entry) => entry.isIntersecting)) return;
    requestAnimationFrame(reveal);
    observer.disconnect();
  }, {
    root: null,
    rootMargin: '0px 0px -12% 0px',
    threshold: 0.16,
  });

  observer.observe(heading);

  return () => {
    observer.disconnect();
    root.classList.remove('is-program-heading-revealed');
  };
}
