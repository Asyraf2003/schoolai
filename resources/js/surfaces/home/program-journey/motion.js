const GSAP_SRC = 'https://cdn.jsdelivr.net/npm/gsap@3.7.1/dist/gsap.min.js';
let gsapRequest = null;

export function loadGsap() {
  if (window.gsap) return Promise.resolve(window.gsap);
  if (gsapRequest) return gsapRequest;

  gsapRequest = new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = GSAP_SRC;
    script.async = true;
    script.dataset.programGsap = 'true';
    script.onload = () => window.gsap ? resolve(window.gsap) : reject(new Error('GSAP unavailable'));
    script.onerror = () => reject(new Error('Failed to load GSAP'));
    document.head.appendChild(script);
  });

  return gsapRequest;
}

export class TypeTransition {
  constructor(gsap, element, lines, rtl = false) {
    this.gsap = gsap;
    this.element = element;
    this.lines = lines;
    this.rtl = rtl;
  }

  in() {
    const xLead = this.rtl ? '-20%' : '20%';
    const xExit = this.rtl ? '200%' : '-200%';
    const rotation = this.rtl ? 90 : -90;

    return this.gsap.timeline({ paused: true })
      .to(this.element, { duration: 1.4, ease: 'power2.inOut', scale: 2.7, rotate: rotation })
      .to(this.lines, {
        keyframes: [
          { x: xLead, duration: 1, ease: 'power1.inOut' },
          { x: xExit, duration: 1.5, ease: 'power1.in' },
        ],
        stagger: 0.04,
      }, 0)
      .to(this.lines, {
        keyframes: [
          { opacity: 1, duration: 1, ease: 'power1.in' },
          { opacity: 0, duration: 1.5, ease: 'power1.in' },
        ],
      }, 0);
  }

  out() {
    return this.gsap.timeline({ paused: true })
      .to(this.element, { duration: 1.4, ease: 'power2.inOut', scale: 1, rotate: 0 }, 1.2)
      .to(this.lines, { duration: 2.3, ease: 'back', x: '0%', stagger: -0.04 }, 0)
      .to(this.lines, {
        keyframes: [
          { opacity: 1, duration: 1, ease: 'power1.in' },
          { opacity: 0.05, duration: 1.5, ease: 'power1.in' },
        ],
      }, 0)
      .set(this.lines, { clearProps: 'opacity,transform' })
      .set(this.element, { clearProps: 'transform' });
  }
}

export function mountItemHover(gsap, cards) {
  const cleanups = [];
  const defaults = { duration: 1, ease: 'expo' };

  cards.forEach((card) => {
    const parts = [
      card.querySelector('.program-kinetic__image-wrap img'),
      card.querySelector('.program-kinetic__name'),
      card.querySelector('.program-kinetic__summary'),
    ].filter(Boolean);
    const enter = () => gsap.timeline({ defaults }).to(parts, { y: (position) => position * 8 - 4 });
    const leave = () => gsap.timeline({ defaults }).to(parts, { y: 0 });
    card.addEventListener('mouseenter', enter);
    card.addEventListener('mouseleave', leave);
    cleanups.push(() => {
      card.removeEventListener('mouseenter', enter);
      card.removeEventListener('mouseleave', leave);
    });
  });

  return () => cleanups.forEach((cleanup) => cleanup());
}
