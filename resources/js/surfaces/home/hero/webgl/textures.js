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

export function textureSourceForSlide(slide) {
  if (!slide) return null;

  return videoSource(slide.querySelector('[data-hero-video]')) ||
    imageSource(slide.querySelector('[data-hero-poster]')) ||
    imageSource(slide.querySelector('[data-hero-image]'));
}

export function waitForTextureSource(slide, timeout = 1200) {
  var available = textureSourceForSlide(slide);
  if (available) return Promise.resolve(available);
  if (!slide) return Promise.resolve(null);

  var candidates = Array.from(slide.querySelectorAll(
    '[data-hero-video], [data-hero-poster], [data-hero-image]'
  ));
  if (!candidates.length) return Promise.resolve(null);

  return new Promise(function (resolve) {
    var settled = false;
    var timer = window.setTimeout(finish, timeout);
    var events = ['loadeddata', 'playing', 'load', 'error'];

    function cleanup() {
      window.clearTimeout(timer);
      candidates.forEach(function (candidate) {
        events.forEach(function (eventName) {
          candidate.removeEventListener(eventName, finish);
        });
      });
    }

    function finish() {
      if (settled) return;
      var source = textureSourceForSlide(slide);
      if (!source && performance.now() && timer) return;
      settled = true;
      cleanup();
      resolve(source);
    }

    candidates.forEach(function (candidate) {
      events.forEach(function (eventName) {
        candidate.addEventListener(eventName, finish);
      });
    });

    window.clearTimeout(timer);
    timer = window.setTimeout(function () {
      settled = true;
      cleanup();
      resolve(textureSourceForSlide(slide));
    }, timeout);
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
