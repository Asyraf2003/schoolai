import { createCopyMotion, createRailMotion, createSoftSettler } from './motion.js';

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
  var title = root.querySelector('[data-program-active-title]');
  var summary = root.querySelector('[data-program-active-summary]');
  var description = root.querySelector('[data-program-active-description]');
  var copyMotionNode = root.querySelector('[data-program-copy-motion]');
  var link = root.querySelector('[data-program-active-link]');
  var linkLabel = root.querySelector('[data-program-active-link-label]');
  var introTitleNode = document.getElementById('vision-program-title');
  var introDescriptionNode = introTitleNode && introTitleNode.parentElement
    ? introTitleNode.parentElement.querySelector('p') : null;
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var defaultLabel = label ? label.textContent.trim() : '';
  var defaultLinkLabel = linkLabel ? linkLabel.textContent.trim() : '';
  var defaultLinkHref = link ? link.getAttribute('href') : '';
  var total = frames.length;
  var activeIndex = -1;
  var mode = '';
  var renderFrame = 0;
  var destroyed = false;
  var introProgress = 0;
  var exitProgress = 0;
  var copyMotion = createCopyMotion(reducedMotion);

  if (!frames.length || !hud || !exit) return null;
  root.classList.add('is-enhanced');

  function text(node, value) {
    if (node) node.textContent = value || '';
  }

  function programAt(index) {
    var frame = frames[index];
    return frame ? frame.dataset : null;
  }

  var railMotion = createRailMotion(railTrack, railWindow, railItems, reducedMotion);

  function setActive(index) {
    if (index === activeIndex && mode === 'program') return;
    var program = programAt(index);
    if (!program) return;
    activeIndex = index;
    mode = 'program';

    frames.forEach(function (frame, frameIndex) {
      frame.classList.toggle('is-active', frameIndex === index);
    });
    railItems.forEach(function (item, itemIndex) {
      item.setAttribute('aria-current', itemIndex === index ? 'true' : 'false');
    });

    root.style.setProperty('--program-accent', program.programAccent || '#0ea5e9');
    copyMotion.swap([label, count, title, copyMotionNode, link], function () {
      text(label, program.programLabel);
      text(count, String(index + 1).padStart(2, '0') + ' / ' + String(total).padStart(2, '0'));
      text(title, program.programTitle);
      text(summary, program.programSummary);
      text(description, program.programDescription);
      if (link) {
        link.href = program.programLink || defaultLinkHref;
        link.setAttribute('aria-label', defaultLinkLabel + ': ' + program.programTitle);
      }
      text(linkLabel, defaultLinkLabel);
    });
    railMotion.move(index);
  }

  function setIntro() {
    if (mode === 'intro') return;
    mode = 'intro';
    copyMotion.swap([label, count, title, copyMotionNode, link], function () {
      text(label, defaultLabel);
      text(count, '');
      text(title, introTitleNode ? introTitleNode.textContent.trim() : '');
      text(summary, introDescriptionNode ? introDescriptionNode.textContent.trim() : '');
      text(description, '');
      text(linkLabel, defaultLinkLabel);
      if (link) link.setAttribute('aria-label', defaultLinkLabel);
    });
  }

  function nearestFrameIndex() {
    var focus = window.innerHeight * 0.5;
    var nearest = 0;
    var distance = Infinity;
    frames.forEach(function (frame, index) {
      var rect = frame.getBoundingClientRect();
      var nextDistance = Math.abs(rect.top + rect.height / 2 - focus);
      if (nextDistance < distance) {
        distance = nextDistance;
        nearest = index;
      }
    });
    return nearest;
  }

  function render() {
    renderFrame = 0;
    if (destroyed) return;
    var rootRect = root.getBoundingClientRect();
    var viewport = window.innerHeight;
    introProgress = clamp(-rootRect.top / (viewport * 0.62));
    var compact = window.innerWidth <= 1180;
    root.style.setProperty('--intro-y', ((1 - introProgress) * viewport * (compact ? .16 : .28)).toFixed(2) + 'px');
    root.style.setProperty('--intro-scale', (1 + (1 - introProgress) * (compact ? .18 : .48)).toFixed(3));

    var exitRect = exit.getBoundingClientRect();
    exitProgress = clamp((viewport - exitRect.top) / viewport);
    hud.style.opacity = String(1 - clamp(exitProgress * 1.12));
    hud.style.filter = 'blur(' + (exitProgress * 18).toFixed(2) + 'px)';
    root.style.setProperty('--exit-blur', (exitProgress * 16).toFixed(2) + 'px');
    root.style.setProperty('--exit-scale', (1 + exitProgress * .055).toFixed(3));
    exitLines.forEach(function (line, index) {
      var progress = clamp((exitProgress - index * .035) / .72);
      line.style.transform = 'scaleX(' + progress.toFixed(3) + ')';
    });
    if (rail) rail.style.opacity = String(clamp((introProgress - .18) / .42) * (1 - exitProgress));

    if (introProgress < .28) setIntro();
    else setActive(nearestFrameIndex());
  }

  function scheduleRender() {
    if (!renderFrame) renderFrame = requestAnimationFrame(render);
  }

  function nearestSnapTarget() {
    var nearest = frames[0];
    var distance = Infinity;
    frames.forEach(function (frame) {
      var rect = frame.getBoundingClientRect();
      if (Math.abs(rect.top) < distance) {
        distance = Math.abs(rect.top);
        nearest = frame;
      }
    });
    return window.scrollY + nearest.getBoundingClientRect().top;
  }

  var settler = createSoftSettler({
    reducedMotion: reducedMotion,
    onFrame: scheduleRender,
    canSettle: function () {
      var rect = root.getBoundingClientRect();
      return introProgress >= .28 && exitProgress < .12 && rect.top < 0 && rect.bottom > window.innerHeight;
    },
    getTarget: nearestSnapTarget,
  });

  function onScroll() {
    scheduleRender();
    if (!settler.isRunning()) settler.schedule();
  }

  function onResize() {
    scheduleRender();
    railMotion.move(activeIndex < 0 ? 0 : activeIndex);
  }

  railItems.forEach(function (item, index) {
    item.addEventListener('click', function () {
      var target = window.scrollY + frames[index].getBoundingClientRect().top;
      settler.goTo(target);
    });
  });

  function destroy() {
    if (destroyed) return;
    destroyed = true;
    root.classList.remove('is-enhanced');
    copyMotion.cancel();
    settler.destroy();
    if (renderFrame) cancelAnimationFrame(renderFrame);
    railMotion.cancel();
    window.removeEventListener('scroll', onScroll);
    window.removeEventListener('resize', onResize);
    window.removeEventListener('pagehide', destroy);
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onResize, { passive: true });
  window.addEventListener('pagehide', destroy, { once: true });
  render();
  railMotion.move(0);
  return { destroy: destroy };
}