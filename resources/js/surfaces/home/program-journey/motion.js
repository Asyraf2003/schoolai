const fluidEase = 'cubic-bezier(.16,1,.3,1)';
const lerp = (from, to, amount) => from + (to - from) * amount;

function framesFor(nodes, stable) {
  return nodes.filter(Boolean).map((node) => ({ node, stable }));
}

export function createCopyMotion(reducedMotion) {
  let token = 0;
  let animations = [];

  function cancel() {
    animations.forEach((animation) => animation.cancel());
    animations = [];
  }

  function keyframes(stable, entering) {
    if (stable) {
      return entering
        ? [{ opacity: 0, filter: 'blur(6px)' }, { opacity: 1, filter: 'blur(0)' }]
        : [{ opacity: 1, filter: 'blur(0)' }, { opacity: 0, filter: 'blur(6px)' }];
    }
    return entering
      ? [
        { opacity: 0, filter: 'blur(10px)', transform: 'translateY(38px)' },
        { opacity: 1, filter: 'blur(0)', transform: 'translateY(0)' },
      ]
      : [
        { opacity: 1, filter: 'blur(0)', transform: 'translateY(0)' },
        { opacity: 0, filter: 'blur(10px)', transform: 'translateY(-16px)' },
      ];
  }

  function swap({ moving = [], stable = [], update }) {
    token += 1;
    const activeToken = token;
    const targets = [...framesFor(moving, false), ...framesFor(stable, true)];
    cancel();

    if (reducedMotion || !Element.prototype.animate) {
      update();
      return;
    }

    Promise.all(targets.map(({ node, stable: isStable }) => {
      const animation = node.animate(keyframes(isStable, false), {
        duration: 190,
        easing: 'ease-in',
        fill: 'forwards',
      });
      animations.push(animation);
      return animation.finished.catch(() => {});
    })).then(() => {
      if (activeToken !== token) return;
      cancel();
      update();
      targets.forEach(({ node, stable: isStable }) => {
        const animation = node.animate(keyframes(isStable, true), {
          duration: isStable ? 430 : 700,
          easing: fluidEase,
        });
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
  let current = 0;
  let previous = 0;
  let target = 0;
  let velocity = 0;

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
    if (Math.abs(target - current) > .08 || Math.abs(velocity) > .015) wake();
  }

  function observe() {
    target = options.readTarget();
    wake();
  }

  function sync(value) {
    current = value;
    previous = value;
    target = value;
    velocity = 0;
    options.onUpdate({ target, current, velocity });
  }

  function destroy() {
    if (frame) cancelAnimationFrame(frame);
  }

  return { observe, sync, destroy };
}
