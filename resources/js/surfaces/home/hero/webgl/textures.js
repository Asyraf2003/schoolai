const corsImages = new Map();

function imageSource(image) {
  if (!image || !image.complete || image.naturalWidth < 1) return null;
  return {
    element: image,
    width: image.naturalWidth,
    height: image.naturalHeight,
    dynamic: false
  };
}

function videoSource(video) {
  if (!video || video.readyState < 2 || video.videoWidth < 1) return null;
  return {
    element: video,
    width: video.videoWidth,
    height: video.videoHeight,
    dynamic: true
  };
}

function imageUrl(image) {
  return image?.currentSrc || image?.getAttribute('src') || image?.dataset.src || '';
}

function isSameOrigin(url) {
  try {
    return new URL(url, window.location.href).origin === window.location.origin;
  } catch (error) {
    return true;
  }
}

function corsImageSource(image) {
  var url = imageUrl(image);
  if (!url || isSameOrigin(url)) return imageSource(image);
  return imageSource(corsImages.get(url)?.image);
}

function prepareCorsImage(image) {
  var url = imageUrl(image);
  if (!url || isSameOrigin(url) || corsImages.has(url)) return;

  var clone = new Image();
  var record = { image: clone };
  corsImages.set(url, record);
  clone.crossOrigin = 'anonymous';
  clone.decoding = 'async';
  clone.src = url;
}

function prepareSlideImages(slide) {
  slide?.querySelectorAll('[data-hero-poster], [data-hero-image]').forEach(prepareCorsImage);
}

export function textureSourceForSlide(slide) {
  if (!slide) return null;
  return videoSource(slide.querySelector('[data-hero-video]')) ||
    corsImageSource(slide.querySelector('[data-hero-poster]')) ||
    corsImageSource(slide.querySelector('[data-hero-image]'));
}

export function waitForTextureSource(slide, timeout = 1200) {
  prepareSlideImages(slide);
  var available = textureSourceForSlide(slide);
  if (available) return Promise.resolve(available);

  return new Promise(function (resolve) {
    var startedAt = performance.now();

    function inspect() {
      var source = textureSourceForSlide(slide);
      if (source || performance.now() - startedAt >= timeout) {
        resolve(source);
        return;
      }
      window.setTimeout(inspect, 40);
    }

    inspect();
  });
}

export function coverScale(source, targetWidth, targetHeight) {
  var sourceAspect = source.height / source.width;
  var targetAspect = targetHeight / targetWidth;
  if (targetAspect > sourceAspect) {
    return [(targetWidth / targetHeight) * sourceAspect, 1];
  }
  return [1, (targetHeight / targetWidth) / sourceAspect];
}

export function createHeroTexture(gl, source) {
  var texture = gl.createTexture();
  if (!texture) return null;
  gl.bindTexture(gl.TEXTURE_2D, texture);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
  gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);

  if (!updateHeroTexture(gl, texture, source, false)) {
    gl.deleteTexture(texture);
    return null;
  }
  return texture;
}

export function updateHeroTexture(gl, texture, source, reuseStorage) {
  try {
    gl.bindTexture(gl.TEXTURE_2D, texture);
    gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
    if (reuseStorage) {
      gl.texSubImage2D(
        gl.TEXTURE_2D,
        0,
        0,
        0,
        gl.RGBA,
        gl.UNSIGNED_BYTE,
        source.element
      );
    } else {
      gl.texImage2D(
        gl.TEXTURE_2D,
        0,
        gl.RGBA,
        gl.RGBA,
        gl.UNSIGNED_BYTE,
        source.element
      );
    }
    return true;
  } catch (error) {
    return false;
  }
}
