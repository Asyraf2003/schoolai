import { layoutGalleryGrid, waitForGalleryMedia } from './gallery-grid-media.js';

export function initGalleryGridDemo() {
  var root = document.querySelector('[data-gallery-grid-demo]');
  if (!root) return;

  var links = Array.prototype.slice.call(root.querySelectorAll('[data-gallery-category-target]'));
  var panels = Array.prototype.slice.call(root.querySelectorAll('[data-gallery-category-panel]'));
  var activeTitle = root.querySelector('[data-gallery-active-title]');
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var observer = null;
  var activeGrid = null;
  var resizeTimer = null;

  function stopObserver() {
    if (!observer) return;
    observer.disconnect();
    observer = null;
  }

  function prepareGrid(grid) {
    if (!grid) return;

    stopObserver();
    activeGrid = grid;

    var items = Array.prototype.slice.call(grid.children).filter(function (item) {
      return item.tagName === 'LI';
    });

    items.forEach(function (item) {
      item.classList.remove('shown', 'animate');
      item.style.animationDuration = '';
      item.style.opacity = '0';
    });

    var paint = function () {
      layoutGalleryGrid(grid);

      if (reducedMotion || !('IntersectionObserver' in window)) {
        items.forEach(function (item) {
          item.style.opacity = '';
          item.classList.add('shown');
        });
        return;
      }

      observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;

          var item = entry.target;
          var duration = Math.random() * .3 + .4;
          var viewportCenter = window.scrollY + window.innerHeight / 2;

          grid.style.perspectiveOrigin = '50% ' + viewportCenter + 'px';
          item.style.animationDuration = duration.toFixed(3) + 's';
          item.classList.add('animate');
          observer.unobserve(item);

          item.addEventListener('animationend', function () {
            item.classList.remove('animate');
            item.classList.add('shown');
            item.style.opacity = '';
          }, { once: true });
        });
      }, {
        threshold: .2,
      });

      items.forEach(function (item) {
        observer.observe(item);
      });
    };

    requestAnimationFrame(paint);
    waitForGalleryMedia(grid, function () {
      requestAnimationFrame(function () {
        layoutGalleryGrid(grid);
      });
    });
  }

  function activate(anchor, updateUrl) {
    var nextPanel = panels.find(function (panel) {
      return panel.id === anchor;
    }) || panels[0];

    if (!nextPanel) return;

    panels.forEach(function (panel) {
      var active = panel === nextPanel;
      panel.hidden = !active;
      panel.classList.toggle('is-active', active);
    });

    links.forEach(function (link) {
      link.classList.toggle(
        'current-demo',
        link.getAttribute('data-gallery-category-target') === nextPanel.id
      );
    });

    if (activeTitle) {
      activeTitle.textContent = nextPanel.getAttribute('data-gallery-title') || 'Index';
    }

    if (updateUrl && window.history && window.history.replaceState) {
      window.history.replaceState(null, '', '#' + nextPanel.id);
    }

    prepareGrid(nextPanel.querySelector('[data-gallery-grid]'));
  }

  links.forEach(function (link) {
    link.addEventListener('click', function (event) {
      event.preventDefault();
      var anchor = link.getAttribute('data-gallery-category-target');
      activate(anchor, true);

      window.scrollTo({
        top: Math.max(0, root.getBoundingClientRect().top + window.scrollY),
        behavior: 'auto',
      });
    });
  });

  window.addEventListener('resize', function () {
    window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(function () {
      layoutGalleryGrid(activeGrid);
    }, 120);
  });

  var requested = window.location.hash ? window.location.hash.slice(1) : '';
  activate(requested, false);
}

