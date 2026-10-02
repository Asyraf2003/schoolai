import '../../../css/pages/welcome-testimonial-wall.css';
let preparation = null;

const REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';

function clamp(value, minimum, maximum) {
  return Math.min(Math.max(value, minimum), maximum);
}

function mountTestimonialWall(root) {
  const rows = root.querySelector('[data-testimonial-rows]');
  const tracks = Array.from(root.querySelectorAll('[data-testimonial-track]'));

  if (!rows || tracks.length === 0) return;

  const reducedMotion = window.matchMedia(REDUCED_MOTION_QUERY);
  const lifecycle = new AbortController();
  let suspended = false;
  let nearby = !('IntersectionObserver' in window);
  let active = false;
  let frame = null;
  let rangeStart = 0;
  let rangeEnd = 1;
  let metrics = [];

  const cancelFrame = () => {
    if (frame === null) return;
    window.cancelAnimationFrame(frame);
    frame = null;
  };

  const measure = () => {
    const rowsRect = rows.getBoundingClientRect();
    const viewportHeight = Math.max(window.innerHeight, 1);
    const documentTop = rowsRect.top + window.scrollY;

    rangeStart = documentTop - viewportHeight;
    rangeEnd = documentTop + rowsRect.height;

    metrics = tracks.map((track) => {
      const viewport = track.closest('[data-testimonial-viewport]');
      const overflow = viewport ? Math.max(track.scrollWidth - viewport.clientWidth, 0) : 0;
      const travel = overflow * 0.9;

      return { track, travel };
    });
  };

  const render = () => {
    frame = null;

    if (!active) return;
    paint();
  };

  const paint = () => {
    if (reducedMotion.matches) return;
    const progress = clamp(
      (window.scrollY - rangeStart) / Math.max(rangeEnd - rangeStart, 1),
      0,
      1,
    );
    const isRtl = document.documentElement.dir === 'rtl';

    metrics.forEach(({ track, travel }, index) => {
      const towardInlineStart = index % 2 === 0;
      const signedTravel = isRtl ? travel : -travel;
      const startX = towardInlineStart ? 0 : signedTravel;
      const endX = towardInlineStart ? signedTravel : 0;
      const x = startX + (endX - startX) * progress;

      track.style.setProperty('--testimonial-track-x', `${x.toFixed(2)}px`);
    });
  };

  const scheduleRender = () => {
    if (!active || frame !== null) return;
    frame = window.requestAnimationFrame(render);
  };

  const onScroll = () => scheduleRender();

  const syncActivity = () => {
    const shouldAnimate = nearby && !suspended && !reducedMotion.matches && !document.hidden;

    if (shouldAnimate === active) {
      if (active) scheduleRender();
      return;
    }

    active = shouldAnimate;
    root.classList.toggle('is-testimonial-active', active);

    if (active) {
      window.addEventListener('scroll', onScroll, { passive: true });
      scheduleRender();
    } else {
      window.removeEventListener('scroll', onScroll);
      cancelFrame();
    }
  };

  const applyMotionPreference = () => {
    root.classList.toggle('is-testimonial-enhanced', !reducedMotion.matches);

    if (reducedMotion.matches) {
      tracks.forEach((track) => track.style.removeProperty('--testimonial-track-x'));
    }

    measure();
    paint();
    syncActivity();
  };

  const intersectionObserver = 'IntersectionObserver' in window ? new IntersectionObserver((entries) => {
    nearby = entries.some((entry) => entry.isIntersecting);
    syncActivity();
  }, { rootMargin: '45% 0px' }) : null;

  const resizeObserver = 'ResizeObserver' in window ? new ResizeObserver(() => {
    measure();
    scheduleRender();
  }) : null;

  const onVisibilityChange = () => syncActivity();
  const onPageShow = () => {
    suspended = false;
    measure();
    paint();
    syncActivity();
  };
  const onPageHide = event => {
    suspended = true;
    syncActivity();
    if (event.persisted) return;
    lifecycle.abort();
    intersectionObserver?.disconnect();
    resizeObserver?.disconnect();
  };

  applyMotionPreference();
  intersectionObserver?.observe(rows);
  resizeObserver?.observe(rows);
  document.addEventListener('visibilitychange', onVisibilityChange, { signal: lifecycle.signal });
  window.addEventListener('pageshow', onPageShow, { signal: lifecycle.signal });
  window.addEventListener('pagehide', onPageHide, { signal: lifecycle.signal });
  window.addEventListener('resize', onPageShow, { signal: lifecycle.signal, passive: true });
  reducedMotion.addEventListener('change', applyMotionPreference, { signal: lifecycle.signal });
  root.dataset.testimonialReady = 'prepared';
  document.addEventListener('schoolai:homepage-geometry', () => { measure(); paint(); scheduleRender(); }, { signal: lifecycle.signal });
}

export function prepareHomepageTestimonials() {
  if (preparation) return preparation;
  const root = document.querySelector('[data-testimonial-wall]');
  if (root) mountTestimonialWall(root);
  return preparation = Promise.resolve({ state: root?.dataset.testimonialReady === 'prepared' ? 'prepared' : 'failed' });
}
