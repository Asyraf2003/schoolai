const wait = (ms) => new Promise((resolve) => window.setTimeout(resolve, ms));

async function animateTo(element, keyframes, options, reduced) {
  if (!element) return;
  const finalFrame = keyframes[keyframes.length - 1];
  if (reduced || !element.animate) {
    Object.assign(element.style, finalFrame);
    return;
  }
  const animation = element.animate(keyframes, { fill: 'forwards', ...options });
  try {
    await animation.finished;
  } catch {
    return;
  }
  Object.assign(element.style, finalFrame);
  animation.cancel();
}

export async function cardsOut(cards, reduced) {
  await Promise.all(cards.map((card, index) => animateTo(card, [
    { opacity: '1', transform: 'translate3d(0,0,0)' },
    { opacity: '0', transform: `translate3d(0,${index % 2 ? '25%' : '-25%'},0)` },
  ], { duration: 760, easing: 'cubic-bezier(.65,0,.35,1)' }, reduced)));
}

export async function cardsIn(cards, reduced) {
  await Promise.all(cards.map((card) => animateTo(card, [
    { opacity: '0', transform: card.style.transform || 'translate3d(0,0,0)' },
    { opacity: '1', transform: 'translate3d(0,0,0)' },
  ], { duration: 900, easing: 'cubic-bezier(.22,1,.36,1)' }, reduced)));
}

export async function typeIn(type, lines, reduced, rtl) {
  if (reduced) return;
  const direction = rtl ? -1 : 1;
  const root = animateTo(type, [
    { transform: 'translate(-50%,-50%) scale(1) rotate(0deg)' },
    { transform: `translate(-50%,-50%) scale(2.7) rotate(${direction * -90}deg)` },
  ], { duration: 1400, easing: 'cubic-bezier(.65,0,.35,1)' }, false);
  const lineAnimations = lines.map((line, index) => animateTo(line, [
    { opacity: '.07', transform: 'translate3d(0,0,0)' },
    { opacity: '1', transform: `translate3d(${direction * 20}%,0,0)`, offset: .4 },
    { opacity: '0', transform: `translate3d(${direction * -200}%,0,0)` },
  ], { duration: 2300, delay: index * 38, easing: 'cubic-bezier(.55,.08,.68,.53)' }, false));
  await Promise.all([root, ...lineAnimations]);
}

export async function typeOut(type, lines, reduced, rtl) {
  if (reduced) return;
  const direction = rtl ? -1 : 1;
  const lineAnimations = [...lines].reverse().map((line, index) => animateTo(line, [
    { opacity: '0', transform: `translate3d(${direction * -200}%,0,0)` },
    { opacity: '1', transform: `translate3d(${direction * -20}%,0,0)`, offset: .45 },
    { opacity: '.07', transform: 'translate3d(0,0,0)' },
  ], { duration: 2100, delay: index * 32, easing: 'cubic-bezier(.22,1,.36,1)' }, false));
  const root = animateTo(type, [
    { transform: `translate(-50%,-50%) scale(2.7) rotate(${direction * -90}deg)` },
    { transform: 'translate(-50%,-50%) scale(1) rotate(0deg)' },
  ], { duration: 1400, delay: 800, easing: 'cubic-bezier(.65,0,.35,1)' }, false);
  await Promise.all([root, ...lineAnimations]);
}

export async function detailIn(parts, reduced) {
  if (!parts) return;
  if (!reduced) await wait(80);
  const copy = parts.copy.map((element, index) => animateTo(element, [
    { opacity: '0', transform: 'translate3d(0,50%,0)' },
    { opacity: '1', transform: 'translate3d(0,0,0)' },
  ], { duration: 820, delay: index * 45, easing: 'cubic-bezier(.16,1,.3,1)' }, reduced));
  const imageWrap = animateTo(parts.imageWrap, [
    { transform: 'translate3d(0,100%,0)' }, { transform: 'translate3d(0,0,0)' },
  ], { duration: 900, easing: 'cubic-bezier(.16,1,.3,1)' }, reduced);
  const image = animateTo(parts.image, [
    { transform: 'translate3d(0,-100%,0) scale(1.04)' }, { transform: 'translate3d(0,0,0) scale(1.04)' },
  ], { duration: 900, easing: 'cubic-bezier(.16,1,.3,1)' }, reduced);
  await Promise.all([...copy, imageWrap, image]);
}

export async function detailOut(parts, reduced) {
  if (!parts) return;
  const copy = parts.copy.map((element, index) => animateTo(element, [
    { opacity: '1', transform: 'translate3d(0,0,0)' },
    { opacity: '0', transform: 'translate3d(0,45%,0)' },
  ], { duration: 520, delay: (parts.copy.length - index - 1) * 24, easing: 'cubic-bezier(.7,0,.84,0)' }, reduced));
  const imageWrap = animateTo(parts.imageWrap, [
    { transform: 'translate3d(0,0,0)' }, { transform: 'translate3d(0,100%,0)' },
  ], { duration: 620, easing: 'cubic-bezier(.7,0,.84,0)' }, reduced);
  const image = animateTo(parts.image, [
    { transform: 'translate3d(0,0,0) scale(1.04)' }, { transform: 'translate3d(0,-100%,0) scale(1.04)' },
  ], { duration: 620, easing: 'cubic-bezier(.7,0,.84,0)' }, reduced);
  await Promise.all([...copy, imageWrap, image]);
}

export { wait };
