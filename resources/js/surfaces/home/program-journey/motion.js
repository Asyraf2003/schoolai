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

    if (reducedMotion || !Element.prototype.animate) {
      update();
      return;
    }

    var outgoing = nodes.filter(Boolean).map(function (node) {
      var animation = node.animate([
        { opacity: 1, filter: 'blur(0)', transform: 'translateY(0)' },
        { opacity: 0, filter: 'blur(9px)', transform: 'translateY(-18px)' },
      ], { duration: 190, easing: 'ease-in', fill: 'forwards' });
      animations.push(animation);
      return animation.finished.catch(function () {});
    });

    Promise.all(outgoing).then(function () {
      if (activeToken !== token) return;
      cancel();
      update();
      nodes.filter(Boolean).forEach(function (node) {
        var animation = node.animate([
          { opacity: 0, filter: 'blur(9px)', transform: 'translateY(42px)' },
          { opacity: 1, filter: 'blur(0)', transform: 'translateY(0)' },
        ], { duration: 720, easing: fluidEase });
        animations.push(animation);
      });
    });
  }

  return { swap: swap, cancel: cancel };
}

export function createSoftSettler(options) {
  var reducedMotion = options.reducedMotion;
  var timer = 0;
  var frame = 0;
  var running = false;

  function cancel() {
    window.clearTimeout(timer);
    if (frame) cancelAnimationFrame(frame);
    timer = 0;
    frame = 0;
    running = false;
  }

  function goTo(target) {
    cancel();
    if (!Number.isFinite(target)) return;
    if (reducedMotion) {
      window.scrollTo(0, target);
      return;
    }

    var start = window.scrollY;
    var distance = target - start;
    if (Math.abs(distance) < 2) return;
    var duration = Math.min(680, Math.max(320, Math.abs(distance) * 0.62));
    var startedAt = performance.now();
    running = true;

    function tick(now) {
      var progress = Math.min(1, (now - startedAt) / duration);
      var eased = 1 - Math.pow(1 - progress, 4);
      window.scrollTo(0, start + distance * eased);
      options.onFrame();

      if (progress < 1 && running) {
        frame = requestAnimationFrame(tick);
        return;
      }
      frame = 0;
      running = false;
    }

    frame = requestAnimationFrame(tick);
  }

  function schedule() {
    if (running || reducedMotion) return;
    window.clearTimeout(timer);
    timer = window.setTimeout(function () {
      timer = 0;
      if (!options.canSettle()) return;
      var target = options.getTarget();
      if (target !== null) goTo(target);
    }, 150);
  }

  var interruptEvents = ['wheel', 'touchstart', 'pointerdown', 'keydown'];
  interruptEvents.forEach(function (eventName) {
    window.addEventListener(eventName, cancel, { passive: true });
  });

  function destroy() {
    cancel();
    interruptEvents.forEach(function (eventName) {
      window.removeEventListener(eventName, cancel);
    });
  }

  return {
    schedule: schedule,
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
