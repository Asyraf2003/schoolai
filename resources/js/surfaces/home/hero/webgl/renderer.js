import { createHeroProgram } from './program.js';
import { coverScale, createHeroTexture, textureSourceForSlide, waitForTextureSource } from './textures.js';

export function createHeroWebglRenderer(root) {
  var canvas = document.createElement('canvas');
  var gl = null;
  var program = null;
  var buffer = null;
  var uniforms = null;
  var frame = 0;
  var active = null;
  var generation = 0;
  var failed = false;
  canvas.className = 'hero-cinema__webgl';
  canvas.setAttribute('aria-hidden', 'true');

  function initialize() {
    if (gl && program && buffer) return true;
    if (failed) return false;
    gl = canvas.getContext('webgl', {
      alpha: true,
      antialias: false,
      depth: false,
      powerPreference: 'high-performance',
      premultipliedAlpha: false
    });
    if (!gl) return false;
    program = createHeroProgram(gl);
    buffer = gl.createBuffer();
    if (!program || !buffer) return false;
    gl.bindBuffer(gl.ARRAY_BUFFER, buffer);
    gl.bufferData(
      gl.ARRAY_BUFFER,
      new Float32Array([-1, -1, 1, -1, -1, 1, -1, 1, 1, -1, 1, 1]),
      gl.STATIC_DRAW
    );
    uniforms = {
      position: gl.getAttribLocation(program, 'aPosition'),
      progress: gl.getUniformLocation(program, 'uProgress'),
      direction: gl.getUniformLocation(program, 'uDirection'),
      width: gl.getUniformLocation(program, 'uWidth'),
      revealLive: gl.getUniformLocation(program, 'uRevealLive'),
      noiseScale: gl.getUniformLocation(program, 'uNoiseScale'),
      fromScale: gl.getUniformLocation(program, 'uFromScale'),
      toScale: gl.getUniformLocation(program, 'uToScale'),
      from: gl.getUniformLocation(program, 'uFrom'),
      to: gl.getUniformLocation(program, 'uTo')
    };
    gl.useProgram(program);
    gl.enableVertexAttribArray(uniforms.position);
    gl.vertexAttribPointer(uniforms.position, 2, gl.FLOAT, false, 0, 0);
    gl.uniform1i(uniforms.from, 0);
    gl.uniform1i(uniforms.to, 1);
    gl.uniform1f(uniforms.width, 0.5);
    gl.uniform1f(uniforms.revealLive, 0);
    gl.uniform2f(uniforms.noiseScale, 40, 40);
    return true;
  }

  function resize() {
    if (!active || !gl) return;
    var rect = active.media.getBoundingClientRect();
    var dpr = Math.min(window.devicePixelRatio || 1, 1.5);
    var width = Math.max(1, Math.round(rect.width * dpr));
    var height = Math.max(1, Math.round(rect.height * dpr));
    if (canvas.width !== width || canvas.height !== height) {
      canvas.width = width;
      canvas.height = height;
      gl.viewport(0, 0, width, height);
    }
    var fromScale = coverScale(active.fromSource, rect.width, rect.height);
    var toScale = coverScale(active.toSource, rect.width, rect.height);
    gl.uniform2f(uniforms.fromScale, fromScale[0], fromScale[1]);
    gl.uniform2f(uniforms.toScale, toScale[0], toScale[1]);
  }

  function releaseTextures() {
    if (!gl || !active) return;
    gl.deleteTexture(active.fromTexture);
    gl.deleteTexture(active.toTexture);
  }

  function cancel() {
    generation += 1;
    window.cancelAnimationFrame(frame);
    frame = 0;
    releaseTextures();
    active = null;
    canvas.classList.remove('is-active');
    canvas.remove();
    root.classList.remove('is-webgl-transitioning');
    root.dataset.heroWebglActive = 'false';
  }

  function render(progress) {
    if (!active || !gl) return;
    gl.activeTexture(gl.TEXTURE0);
    gl.bindTexture(gl.TEXTURE_2D, active.fromTexture);
    gl.activeTexture(gl.TEXTURE1);
    gl.bindTexture(gl.TEXTURE_2D, active.toTexture);
    gl.uniform1f(uniforms.progress, progress);
    gl.uniform1f(uniforms.direction, active.direction);
    gl.uniform1f(uniforms.revealLive, active.revealLive ? 1 : 0);
    gl.drawArrays(gl.TRIANGLES, 0, 6);
  }

  function draw(timestamp) {
    if (!active) return;
    var elapsed = Math.min(1, (timestamp - active.startedAt) / active.duration);
    render(1 - Math.pow(1 - elapsed, 2));
    if (elapsed < 1) frame = window.requestAnimationFrame(draw);
    else cancel();
  }

  function beginIncoming(token, source) {
    if (!active || active.token !== token || !source || !gl) {
      if (active?.token === token) cancel();
      return;
    }
    var revealLive = source.kind === 'live-video';
    var texture = revealLive ? active.toTexture : createHeroTexture(gl, source);
    if (!texture) {
      cancel();
      return;
    }
    if (!revealLive) {
      gl.deleteTexture(active.toTexture);
      active.toTexture = texture;
    }
    active.toSource = source;
    active.revealLive = revealLive;
    active.startedAt = performance.now();
    root.dataset.heroWebglIncomingSource = source.kind || 'image';
    resize();
    frame = window.requestAnimationFrame(draw);
  }

  function play(fromSlide, toSlide, direction, duration, textureWait) {
    cancel();
    if (!initialize()) return false;
    var fromSource = textureSourceForSlide(fromSlide);
    var media = toSlide?.querySelector('.hero-cinema__media');
    if (!fromSource || !media) return false;
    var fromTexture = createHeroTexture(gl, fromSource);
    var placeholderTexture = createHeroTexture(gl, fromSource);
    if (!fromTexture || !placeholderTexture) {
      if (fromTexture) gl.deleteTexture(fromTexture);
      if (placeholderTexture) gl.deleteTexture(placeholderTexture);
      return false;
    }
    var token = generation;
    active = {
      token,
      media,
      fromSource,
      toSource: fromSource,
      fromTexture,
      toTexture: placeholderTexture,
      direction,
      duration,
      revealLive: false,
      startedAt: 0
    };
    media.appendChild(canvas);
    canvas.classList.add('is-active');
    root.classList.add('is-webgl-transitioning');
    root.dataset.heroWebglActive = 'true';
    root.dataset.heroWebglDirection = direction > 0 ? 'right-to-left' : 'left-to-right';
    root.dataset.heroWebglIncomingSource = 'pending';
    resize();
    render(0);
    waitForTextureSource(toSlide, textureWait).then(function (source) {
      beginIncoming(token, source);
    });
    return true;
  }

  canvas.addEventListener('webglcontextlost', function (event) {
    event.preventDefault();
    failed = true;
    cancel();
    root.dataset.heroWebgl = 'failed';
  });

  function dispose() {
    cancel();
    if (gl && buffer) gl.deleteBuffer(buffer);
    if (gl && program) gl.deleteProgram(program);
    gl?.getExtension('WEBGL_lose_context')?.loseContext();
    gl = null;
    program = null;
    buffer = null;
  }

  return { cancel, dispose, play, resize };
}
