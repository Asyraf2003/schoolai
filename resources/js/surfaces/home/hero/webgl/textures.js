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

  if (!updateHeroTexture(gl, texture, source)) {
    gl.deleteTexture(texture);
    return null;
  }

  return texture;
}

export function updateHeroTexture(gl, texture, source) {
  try {
    gl.bindTexture(gl.TEXTURE_2D, texture);
    gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
    gl.texImage2D(
      gl.TEXTURE_2D,
      0,
      gl.RGBA,
      gl.RGBA,
      gl.UNSIGNED_BYTE,
      source.element
    );
    return true;
  } catch (error) {
    return false;
  }
}
