import '../../../css/pages/welcome-testimonial-wall.css';

const REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';

function clamp(value, minimum, maximum) {
  return Math.min(Math.max(value, minimum), maximum);
}

function mountTestimonialWall(root) {
  const rows = root.querySelector('[data-testimonial-rows]');
  const tracks = Array.from(root.querySelectorAll('[data-testimonial-track]'));

  if (!rows || tracks.length === 0) return;

  const reducedMotion = window.matchMedia(REDUCED_MOTION_QUERY);
  let nearby = false;
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
    const shouldAnimate = nearby && !reducedMotion.matches && !document.hidden;

    if (shouldAnimate === active) {
      if (active) scheduleRender();
      return;
    }

    active = shouldAnimate;
    root.classList.toggle('is-testimonial-active', active);

    if (active) {
      measure();
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
    syncActivity();
  };

  const intersectionObserver = new IntersectionObserver((entries) => {
    nearby = entries.some((entry) => entry.isIntersecting);
    syncActivity();
  }, { rootMargin: '45% 0px' });

  const resizeObserver = new ResizeObserver(() => {
    measure();
    scheduleRender();
  });

  const onVisibilityChange = () => syncActivity();
  const onPageShow = () => {
    measure();
    syncActivity();
  };

  applyMotionPreference();
  intersectionObserver.observe(rows);
  resizeObserver.observe(rows);
  document.addEventListener('visibilitychange', onVisibilityChange);
  window.addEventListener('pageshow', onPageShow);
  reducedMotion.addEventListener('change', applyMotionPreference);
}

function initializeTestimonialWall() {
  const root = document.querySelector('[data-testimonial-wall]');
  if (root) mountTestimonialWall(root);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeTestimonialWall, { once: true });
} else {
  initializeTestimonialWall();
}
