const fluidEase = 'cubic-bezier(.16,1,.3,1)';

export function createCopyMotion(reducedMotion) {
  var token = 0;
  var animations = [];

  function cancel() {
    animations.forEach(function (animation) { animation.cancel(); });
    animations = [];
  }

  function swap(nodes, update) {
    token += 1;
    var activeToken = token;
    cancel();
    var targets = nodes.filter(Boolean);

    if (reducedMotion || !Element.prototype.animate) {
      update();
      return;
    }

    var outgoing = targets.map(function (node) {
      var animation = node.animate([
        { opacity: 1, filter: 'blur(0)', transform: 'translateY(0)' },
        { opacity: 0, filter: 'blur(10px)', transform: 'translateY(-18px)' },
      ], { duration: 220, easing: 'ease-in', fill: 'forwards' });
      animations.push(animation);
      return animation.finished.catch(function () {});
    });

    Promise.all(outgoing).then(function () {
      if (activeToken !== token) return;
      cancel();
      update();
      targets.forEach(function (node) {
        var animation = node.animate([
          { opacity: 0, filter: 'blur(10px)', transform: 'translateY(42px)' },
          { opacity: 1, filter: 'blur(0)', transform: 'translateY(0)' },
        ], { duration: 760, easing: fluidEase });
        animations.push(animation);
      });
    });
  }

  return { swap: swap, cancel: cancel };
}

export function moveWithFlip(node, target, reducedMotion) {
  if (!node || !target || node.parentElement === target) return;
  var before = node.getBoundingClientRect();
  target.appendChild(node);
  var after = node.getBoundingClientRect();
  if (reducedMotion || !node.animate || !before.width || !after.width) return;
  var scaleX = before.width / after.width;
  var scaleY = before.height / after.height;
  node.animate([
    {
      transform: 'translate3d(' + (before.left - after.left) + 'px,'
        + (before.top - after.top) + 'px,0) scale(' + scaleX + ',' + scaleY + ')',
      filter: 'blur(0)',
    },
    { transform: 'translate3d(0,0,0) scale(1)', filter: 'blur(0)' },
  ], { duration: 920, easing: fluidEase });
}

export function createMomentumSettler(options) {
  var reducedMotion = options.reducedMotion;
  var timer = 0;
  var frame = 0;
  var running = false;
  var lastY = window.scrollY;
  var lastTime = performance.now();
  var velocity = 0;

  function cancel() {
    clearTimeout(timer);
    if (frame) cancelAnimationFrame(frame);
    timer = 0;
    frame = 0;
    running = false;
  }

  function observe() {
    if (running || reducedMotion) return;
    var now = performance.now();
    var y = window.scrollY;
    var dt = Math.max(8, Math.min(80, now - lastTime));
    var sample = (y - lastY) / dt;
    velocity = velocity * .62 + sample * .38;
    lastY = y;
    lastTime = now;
    clearTimeout(timer);
    timer = setTimeout(start, 88);
  }

  function run(target, initialVelocity) {
    if (!Number.isFinite(target)) return;
    var position = window.scrollY;
    var speed = initialVelocity * 16.67;
    running = true;

    function tick() {
      var error = target - position;
      speed = (speed + error * .072) * .82;
      position += speed;
      window.scrollTo({ top: position, behavior: 'auto' });
      options.onFrame();
      if (Math.abs(error) > .6 || Math.abs(speed) > .08) {
        frame = requestAnimationFrame(tick);
        return;
      }
      window.scrollTo({ top: target, behavior: 'auto' });
      lastY = target;
      lastTime = performance.now();
      velocity = 0;
      frame = 0;
      running = false;
      options.onFrame();
    }

    frame = requestAnimationFrame(tick);
  }

  function start() {
    timer = 0;
    if (!options.canSettle()) return;
    var anchors = options.getAnchors();
    if (!anchors.length) return;
    var position = window.scrollY;
    var projected = position + Math.max(
      -window.innerHeight,
      Math.min(window.innerHeight, velocity * 340)
    );
    var target = anchors.reduce(function (best, value) {
      return Math.abs(value - projected) < Math.abs(best - projected) ? value : best;
    }, anchors[0]);
    run(target, velocity);
  }

  function goTo(target) {
    cancel();
    velocity = 0;
    lastY = window.scrollY;
    run(target, 0);
  }

  var interrupts = ['wheel', 'touchstart', 'pointerdown', 'keydown'];
  interrupts.forEach(function (name) {
    window.addEventListener(name, cancel, { passive: true });
  });

  function destroy() {
    cancel();
    interrupts.forEach(function (name) { window.removeEventListener(name, cancel); });
  }

  return {
    observe: observe,
    goTo: goTo,
    cancel: cancel,
    destroy: destroy,
    isRunning: function () { return running; },
  };
}

export function createRailMotion(track, windowNode, items, reducedMotion) {
  var animation = null;
  var offset = 0;

  function move(index) {
    if (!track || !windowNode || !items[index]) return;
    var item = items[index];
    var next = (windowNode.clientHeight / 2) - (item.offsetTop + item.offsetHeight / 2);
    if (animation) animation.cancel();
    if (!reducedMotion && track.animate) {
      animation = track.animate([
        { transform: 'translate3d(0,' + offset + 'px,0)' },
        { transform: 'translate3d(0,' + next + 'px,0)' },
      ], { duration: 720, easing: fluidEase });
    }
    offset = next;
    track.style.transform = 'translate3d(0,' + next + 'px,0)';
  }

  return { move: move, cancel: function () { if (animation) animation.cancel(); } };
}
