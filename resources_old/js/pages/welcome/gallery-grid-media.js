function galleryColumnCount() {
  if (window.matchMedia('(max-width: 400px)').matches) return 1;
  if (window.matchMedia('(max-width: 900px)').matches) return 2;
  return 3;
}

export function layoutGalleryGrid(grid) {
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

export function waitForGalleryMedia(grid, callback) {
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

