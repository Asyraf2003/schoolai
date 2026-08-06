import {
  createCopyMotion,
  createMomentumSettler,
  createRailMotion,
  moveWithFlip,
} from './motion.js';

const clamp = (value) => Math.max(0, Math.min(1, value));

export function mountProgramJourney(root) {
  if (!root) return null;
  var frames = Array.prototype.slice.call(root.querySelectorAll('[data-program-frame]'));
  var railItems = Array.prototype.slice.call(root.querySelectorAll('[data-program-rail-item]'));
  var hud = root.querySelector('[data-program-hud]');
  var rail = root.querySelector('.program-journey__rail');
  var railWindow = root.querySelector('[data-program-rail-window]');
  var railTrack = root.querySelector('[data-program-rail]');
  var exit = root.querySelector('[data-program-exit]');
  var exitLines = Array.prototype.slice.call(root.querySelectorAll('[data-program-exit-lines] span'));
  var label = root.querySelector('[data-program-active-label]');
  var count = root.querySelector('[data-program-active-count]');
  var titleSlot = root.querySelector('[data-program-title-slot]');
  var descriptionSlot = root.querySelector('[data-program-description-slot]');
  var link = root.querySelector('[data-program-active-link]');
  var linkLabel = root.querySelector('[data-program-active-link-label]');
  var origin = document.querySelector('[data-program-origin]');
  var titleHome = origin && origin.querySelector('[data-program-title-home]');
  var descriptionHome = origin && origin.querySelector('[data-program-description-home]');
  var title = origin && origin.querySelector('[data-program-origin-title]');
  var description = origin && origin.querySelector('[data-program-origin-description]');
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var introTitle = title ? title.textContent.trim() : '';
  var introDescription = description ? description.textContent.trim() : '';
  var defaultLabel = label ? label.textContent.trim() : '';
  var defaultLinkLabel = linkLabel ? linkLabel.textContent.trim() : '';
  var defaultLinkHref = link ? link.getAttribute('href') : '';
  var activeIndex = -1;
  var mode = 'intro';
  var renderFrame = 0;
  var handedOff = false;
  var destroyed = false;
  var exitProgress = 0;
  var copyMotion = createCopyMotion(reducedMotion);

  if (!frames.length || !hud || !exit || !title || !description) return null;
  root.classList.add('is-enhanced');
  var railMotion = createRailMotion(railTrack, railWindow, railItems, reducedMotion);

  function text(node, value) {
    if (node) node.textContent = value || '';
  }

  function handoffIn() {
    if (handedOff) return;
    handedOff = true;
    if (titleHome) titleHome.style.minHeight = title.offsetHeight + 'px';
    if (descriptionHome) descriptionHome.style.minHeight = description.offsetHeight + 'px';
    root.classList.add('has-handoff');
    moveWithFlip(title, titleSlot, reducedMotion);
    moveWithFlip(description, descriptionSlot, reducedMotion);
  }

  function handoffOut() {
    if (!handedOff) return;
    handedOff = false;
    copyMotion.cancel();
    text(title, introTitle);
    text(description, introDescription);
    moveWithFlip(title, titleHome, reducedMotion);
    moveWithFlip(description, descriptionHome, reducedMotion);
    root.classList.remove('has-handoff');
    if (titleHome) titleHome.style.removeProperty('min-height');
    if (descriptionHome) descriptionHome.style.removeProperty('min-height');
    mode = 'intro';
    activeIndex = -1;
  }

  function setIntro() {
    if (mode === 'intro') return;
    mode = 'intro';
    copyMotion.swap([label, count, title, description, link], function () {
      text(label, defaultLabel);
      text(count, '');
      text(title, introTitle);
      text(description, introDescription);
      text(linkLabel, defaultLinkLabel);
      if (link) link.href = defaultLinkHref;
    });
  }

  function setActive(index) {
    if (index === activeIndex && mode === 'program') return;
    var program = frames[index] ? frames[index].dataset : null;
    if (!program) return;
    activeIndex = index;
    mode = 'program';
    frames.forEach(function (frame, i) { frame.classList.toggle('is-active', i === index); });
    railItems.forEach(function (item, i) {
      item.setAttribute('aria-current', i === index ? 'true' : 'false');
    });
    root.style.setProperty('--program-accent', program.programAccent || '#0ea5e9');
    copyMotion.swap([label, count, title, description, link], function () {
      text(label, program.programLabel);
      text(count, String(index + 1).padStart(2, '0') + ' / ' + String(frames.length).padStart(2, '0'));
      text(title, program.programTitle);
      text(description, program.programDescription);
      if (link) link.href = program.programLink || defaultLinkHref;
      text(linkLabel, defaultLinkLabel);
    });
    railMotion.move(index);
  }

  function nearestFrameIndex() {
    var focus = window.innerHeight * .5;
    return frames.reduce(function (best, frame, index) {
      var rect = frame.getBoundingClientRect();
      var distance = Math.abs(rect.top + rect.height / 2 - focus);
      return distance < best.distance ? { index: index, distance: distance } : best;
    }, { index: 0, distance: Infinity }).index;
  }

  function anchors() {
    return frames.map(function (frame) {
      return window.scrollY + frame.getBoundingClientRect().top;
    });
  }

  function render() {
    renderFrame = 0;
    if (destroyed) return;
    var viewport = window.innerHeight;
    var rootRect = root.getBoundingClientRect();
    if (rootRect.top <= viewport * .82 && rootRect.bottom > 0) handoffIn();
    if (rootRect.top > viewport * .9) handoffOut();

    var exitRect = exit.getBoundingClientRect();
    exitProgress = clamp((viewport - exitRect.top) / viewport);
    hud.style.opacity = String(handedOff ? 1 - clamp(exitProgress * 1.12) : 0);
    hud.style.filter = 'blur(' + (exitProgress * 18).toFixed(2) + 'px)';
    exitLines.forEach(function (line, index) {
      var progress = clamp((exitProgress - index * .035) / .72);
      line.style.transform = 'scaleX(' + progress.toFixed(3) + ')';
    });
    if (rail) rail.style.opacity = String(clamp((viewport * .58 - rootRect.top) / (viewport * .36)) * (1 - exitProgress));

    if (!handedOff || rootRect.top > viewport * .12) setIntro();
    else setActive(nearestFrameIndex());
  }

  function scheduleRender() {
    if (!renderFrame) renderFrame = requestAnimationFrame(render);
  }

  var settler = createMomentumSettler({
    reducedMotion: reducedMotion,
    onFrame: scheduleRender,
    canSettle: function () {
      var rect = root.getBoundingClientRect();
      return handedOff && exitProgress < .08 && rect.top < 0 && rect.bottom > window.innerHeight;
    },
    getAnchors: anchors,
  });

  function onScroll() {
    scheduleRender();
    if (!settler.isRunning()) settler.observe();
  }

  railItems.forEach(function (item, index) {
    item.addEventListener('click', function () { settler.goTo(anchors()[index]); });
  });

  function destroy() {
    if (destroyed) return;
    destroyed = true;
    copyMotion.cancel();
    settler.destroy();
    railMotion.cancel();
    if (renderFrame) cancelAnimationFrame(renderFrame);
    handoffOut();
    root.classList.remove('is-enhanced');
    window.removeEventListener('scroll', onScroll);
    window.removeEventListener('resize', scheduleRender);
    window.removeEventListener('pagehide', destroy);
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', scheduleRender, { passive: true });
  window.addEventListener('pagehide', destroy, { once: true });
  render();
  return { destroy: destroy };
}
