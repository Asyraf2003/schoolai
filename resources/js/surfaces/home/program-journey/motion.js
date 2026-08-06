const fluidEase = 'cubic-bezier(.16,1,.3,1)';
const lerp = (from, to, amount) => from + (to - from) * amount;
const clamp = (value, min, max) => Math.max(min, Math.min(max, value));

export function createCopyMotion(reducedMotion) {
  let token = 0;
  let animations = [];

  function cancel() {
    animations.forEach((animation) => animation.cancel());
    animations = [];
  }

  function swap(nodes, update) {
    token += 1;
    const activeToken = token;
    const targets = nodes.filter(Boolean);
    cancel();

    if (reducedMotion || !Element.prototype.animate) {
      update();
      return;
    }

    Promise.all(targets.map((node) => {
      const animation = node.animate([
        { opacity: 1, filter: 'blur(0)', transform: 'translateY(0)' },
        { opacity: 0, filter: 'blur(10px)', transform: 'translateY(-18px)' },
      ], { duration: 220, easing: 'ease-in', fill: 'forwards' });
      animations.push(animation);
      return animation.finished.catch(() => {});
    })).then(() => {
      if (activeToken !== token) return;
      cancel();
      update();
      targets.forEach((node) => {
        const animation = node.animate([
          { opacity: 0, filter: 'blur(10px)', transform: 'translateY(42px)' },
          { opacity: 1, filter: 'blur(0)', transform: 'translateY(0)' },
        ], { duration: 760, easing: fluidEase });
        animations.push(animation);
      });
    });
  }

  return { swap, cancel };
}

export function moveWithFlip(node, target, reducedMotion) {
  if (!node || !target || node.parentElement === target) return;
  const before = node.getBoundingClientRect();
  target.appendChild(node);
  const after = node.getBoundingClientRect();
  if (reducedMotion || !node.animate || !before.width || !after.width) return;
  node.animate([
    {
      transform: `translate3d(${before.left - after.left}px,${before.top - after.top}px,0) scale(${before.width / after.width})`,
      filter: 'blur(0)',
    },
    { transform: 'translate3d(0,0,0) scale(1)', filter: 'blur(0)' },
  ], { duration: 920, easing: fluidEase });
}

export function createVisualScrollEngine(options) {
  let frame = 0;
  let snapTimer = 0;
  let current = 0;
  let previous = 0;
  let target = 0;
  let velocity = 0;
  let inputVelocity = 0;
  let lastInput = 0;
  let lastTime = performance.now();
  let snapping = false;
  let snapTarget = null;

  function wake() {
    if (!frame) frame = requestAnimationFrame(tick);
  }

  function tick() {
    frame = 0;
    target = options.readTarget();
    current = options.reducedMotion ? target : lerp(current, target, .08);
    velocity = lerp(velocity, current - previous, .12);
    previous = current;
    options.onUpdate({ target, current, velocity });

    if (snapping && snapTarget !== null
      && Math.abs(target - snapTarget) < 1
      && Math.abs(current - target) < .35) {
      snapping = false;
      snapTarget = null;
    }

    if (Math.abs(target - current) > .08 || Math.abs(velocity) > .015 || snapping) wake();
  }

  function chooseSnap() {
    snapTimer = 0;
    if (snapping || options.reducedMotion || !options.canSnap()) return;
    const anchors = options.getAnchors();
    if (!anchors.length) return;
    const limit = window.innerHeight;
    const projected = target + clamp(inputVelocity * 280, -limit, limit);
    const next = anchors.reduce((best, anchor) => (
      Math.abs(anchor - projected) < Math.abs(best - projected) ? anchor : best
    ), anchors[0]);
    if (Math.abs(next - target) < 2) return;
    snapping = true;
    snapTarget = next;
    options.snapTo(next);
    wake();
  }

  function observe() {
    const now = performance.now();
    const next = options.readTarget();
    if (!snapping) {
      const elapsed = clamp(now - lastTime, 8, 80);
      inputVelocity = inputVelocity * .68 + ((next - lastInput) / elapsed) * .32;
      clearTimeout(snapTimer);
      snapTimer = window.setTimeout(chooseSnap, 72);
    }
    target = next;
    lastInput = next;
    lastTime = now;
    wake();
  }

  function stopSnap() {
    clearTimeout(snapTimer);
    snapTimer = 0;
    if (snapping) options.stopSnap?.();
    snapping = false;
    snapTarget = null;
    inputVelocity = 0;
    target = options.readTarget();
    wake();
  }

  function sync(value) {
    clearTimeout(snapTimer);
    current = value;
    previous = value;
    target = value;
    lastInput = value;
    velocity = 0;
    inputVelocity = 0;
    options.onUpdate({ target, current, velocity });
  }

  function snapNow(value) {
    stopSnap();
    snapping = true;
    snapTarget = value;
    options.snapTo(value);
    wake();
  }

  const interrupts = ['wheel', 'touchstart', 'pointerdown', 'keydown'];
  interrupts.forEach((name) => window.addEventListener(name, stopSnap, { passive: true }));

  function destroy() {
    clearTimeout(snapTimer);
    if (frame) cancelAnimationFrame(frame);
    interrupts.forEach((name) => window.removeEventListener(name, stopSnap));
  }

  return { observe, sync, snapNow, destroy };
}

export function createRailMotion(track, windowNode, items, reducedMotion) {
  let animation = null;
  let offset = 0;

  function move(index) {
    if (!track || !windowNode || !items[index]) return;
    const item = items[index];
    const next = (windowNode.clientHeight / 2) - (item.offsetTop + item.offsetHeight / 2);
    animation?.cancel();
    if (!reducedMotion && track.animate) {
      animation = track.animate([
        { transform: `translate3d(0,${offset}px,0)` },
        { transform: `translate3d(0,${next}px,0)` },
      ], { duration: 720, easing: fluidEase });
    }
    offset = next;
    track.style.transform = `translate3d(0,${next}px,0)`;
  }

  return { move, cancel: () => animation?.cancel() };
}
