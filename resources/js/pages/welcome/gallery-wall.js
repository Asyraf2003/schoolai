function galleryColumnCount() {
  if (window.matchMedia('(max-width: 400px)').matches) return 1;
  if (window.matchMedia('(max-width: 900px)').matches) return 2;
  return 3;
}

function layoutGalleryGrid(grid) {
  if (!grid || grid.closest('[hidden]')) return;

  var items = Array.prototype.slice.call(grid.children).filter(function (item) {
    return item.tagName === 'LI';
  });
  var columns = galleryColumnCount();
  var width = grid.clientWidth;

  if (!items.length || width <= 0) return;

  var columnWidth = width / columns;
  var heights = Array(columns).fill(0);

  grid.classList.add('is-masonry');

  items.forEach(function (item) {
    item.style.width = (100 / columns) + '%';
  });

  items.forEach(function (item) {
    var column = 0;

    for (var index = 1; index < heights.length; index += 1) {
      if (heights[index] < heights[column]) column = index;
    }

    item.style.left = (column * columnWidth) + 'px';
    item.style.top = heights[column] + 'px';
    heights[column] += item.offsetHeight;
  });

  grid.style.height = Math.max.apply(Math, heights) + 'px';
}

function waitForGalleryMedia(grid, callback) {
  var pending = [];

  grid.querySelectorAll('img').forEach(function (image) {
    if (image.complete) return;

    pending.push(new Promise(function (resolve) {
      image.addEventListener('load', resolve, { once: true });
      image.addEventListener('error', resolve, { once: true });
    }));
  });

  grid.querySelectorAll('video').forEach(function (video) {
    if (video.readyState >= 1) return;

    pending.push(new Promise(function (resolve) {
      video.addEventListener('loadedmetadata', resolve, { once: true });
      video.addEventListener('error', resolve, { once: true });
    }));
  });

  Promise.allSettled(pending).then(callback);
}

function initGalleryGridDemo() {
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
    });

    var paint = function () {
      layoutGalleryGrid(grid);

      if (reducedMotion || !('IntersectionObserver' in window)) {
        items.forEach(function (item) {
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

function initGalleryModal() {
  var modal = document.querySelector('[data-gallery-modal]');
  if (!modal) return;

  var mediaBox = modal.querySelector('[data-gallery-modal-media]');
  var titleBox = modal.querySelector('[data-gallery-modal-title]');
  var closeButtons = Array.prototype.slice.call(modal.querySelectorAll('[data-gallery-modal-close]'));
  var openers = Array.prototype.slice.call(document.querySelectorAll('[data-gallery-modal-open]'));
  var lastFocused = null;

  if (!mediaBox || !titleBox || !openers.length) return;

  function clearMedia() {
    mediaBox.replaceChildren();
  }

  function closeModal() {
    if (modal.hidden) return;

    modal.hidden = true;
    document.body.classList.remove('gallery-modal-open');
    clearMedia();

    if (lastFocused && typeof lastFocused.focus === 'function') {
      lastFocused.focus({ preventScroll: true });
    }
  }

  function openModal(opener) {
    var title = opener.getAttribute('data-gallery-title') || '';
    var mediaUrl = opener.getAttribute('data-gallery-media-url') || '';
    var thumbnailUrl = opener.getAttribute('data-gallery-thumbnail-url') || '';
    var isVideo = opener.getAttribute('data-gallery-is-video') === '1';
    var isDirectVideo = opener.getAttribute('data-gallery-is-direct-video') === '1';

    lastFocused = document.activeElement;
    titleBox.textContent = title;
    clearMedia();

    if (isVideo && isDirectVideo && mediaUrl) {
      var video = document.createElement('video');
      video.src = mediaUrl;
      video.controls = true;
      video.autoplay = true;
      video.playsInline = true;
      video.preload = 'metadata';
      mediaBox.appendChild(video);
    } else if (isVideo && mediaUrl) {
      var iframe = document.createElement('iframe');
      iframe.src = mediaUrl;
      iframe.title = title || 'Gallery video';
      iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
      iframe.allowFullscreen = true;
      iframe.referrerPolicy = 'strict-origin-when-cross-origin';
      mediaBox.appendChild(iframe);
    } else if (mediaUrl || thumbnailUrl) {
      var image = document.createElement('img');
      image.src = mediaUrl || thumbnailUrl;
      image.alt = title;
      mediaBox.appendChild(image);
    }

    modal.hidden = false;
    document.body.classList.add('gallery-modal-open');

    var closeButton = modal.querySelector('.gallery-grid-modal__close');
    if (closeButton) closeButton.focus({ preventScroll: true });
  }

  openers.forEach(function (opener) {
    opener.addEventListener('click', function () {
      openModal(opener);
    });
  });

  closeButtons.forEach(function (button) {
    button.addEventListener('click', closeModal);
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && !modal.hidden) {
      event.preventDefault();
      closeModal();
    }
  });
}

document.addEventListener('DOMContentLoaded', function () {
  initGalleryGridDemo();
  initGalleryModal();
});
