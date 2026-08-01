import { HERO_FRAGMENT_SHADER, HERO_VERTEX_SHADER } from './shaders.js';
import {
  coverScale,
  createHeroTexture,
  textureSourceForSlide,
  updateHeroTexture
} from './textures.js';

function compileShader(gl, type, source) {
  var shader = gl.createShader(type);
  if (!shader) return null;
  gl.shaderSource(shader, source);
  gl.compileShader(shader);

  if (gl.getShaderParameter(shader, gl.COMPILE_STATUS)) return shader;
  gl.deleteShader(shader);
  return null;
}

function createProgram(gl) {
  var vertex = compileShader(gl, gl.VERTEX_SHADER, HERO_VERTEX_SHADER);
  var fragment = compileShader(gl, gl.FRAGMENT_SHADER, HERO_FRAGMENT_SHADER);
  if (!vertex || !fragment) return null;

  var program = gl.createProgram();
  gl.attachShader(program, vertex);
  gl.attachShader(program, fragment);
  gl.linkProgram(program);
  gl.deleteShader(vertex);
  gl.deleteShader(fragment);

  if (gl.getProgramParameter(program, gl.LINK_STATUS)) return program;
  gl.deleteProgram(program);
  return null;
}

export function createHeroWebglRenderer(root) {
  var canvas = document.createElement('canvas');
  var gl = null;
  var program = null;
  var buffer = null;
  var uniforms = null;
  var frame = 0;
  var active = null;
  var failed = false;

  canvas.className = 'hero-cinema__webgl';
  canvas.setAttribute('aria-hidden', 'true');

  function initialize() {
    if (gl && program && buffer) return true;
    if (failed) return false;

    gl = canvas.getContext('webgl', {
      alpha: false,
      antialias: false,
      depth: false,
      powerPreference: 'high-performance',
      premultipliedAlpha: false
    });
    if (!gl) return false;

    program = createProgram(gl);
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
    window.cancelAnimationFrame(frame);
    frame = 0;
    releaseTextures();
    active = null;
    canvas.classList.remove('is-active');
    canvas.remove();
    root.classList.remove('is-webgl-transitioning');
    root.dataset.heroWebglActive = 'false';
  }

  function draw(timestamp) {
    if (!active || !gl) return;
    var elapsed = Math.min(1, (timestamp - active.startedAt) / active.duration);
    var progress = 1 - Math.pow(1 - elapsed, 2);

    if (active.fromSource.dynamic) updateHeroTexture(gl, active.fromTexture, active.fromSource, true);
    if (active.toSource.dynamic) updateHeroTexture(gl, active.toTexture, active.toSource, true);

    gl.activeTexture(gl.TEXTURE0);
    gl.bindTexture(gl.TEXTURE_2D, active.fromTexture);
    gl.activeTexture(gl.TEXTURE1);
    gl.bindTexture(gl.TEXTURE_2D, active.toTexture);
    gl.uniform1f(uniforms.progress, progress);
    gl.uniform1f(uniforms.direction, active.direction);
    gl.drawArrays(gl.TRIANGLES, 0, 6);

    if (elapsed < 1) frame = window.requestAnimationFrame(draw);
    else cancel();
  }

  function play(fromSlide, toSlide, direction, duration) {
    cancel();
    if (!initialize()) return false;

    var fromSource = textureSourceForSlide(fromSlide);
    var toSource = textureSourceForSlide(toSlide);
    var media = toSlide?.querySelector('.hero-cinema__media');
    if (!fromSource || !toSource || !media) return false;

    var fromTexture = createHeroTexture(gl, fromSource);
    var toTexture = createHeroTexture(gl, toSource);
    if (!fromTexture || !toTexture) {
      if (fromTexture) gl.deleteTexture(fromTexture);
      if (toTexture) gl.deleteTexture(toTexture);
      return false;
    }

    active = {
      media,
      fromSource,
      toSource,
      fromTexture,
      toTexture,
      direction,
      duration,
      startedAt: performance.now()
    };
    media.appendChild(canvas);
    canvas.classList.add('is-active');
    root.classList.add('is-webgl-transitioning');
    root.dataset.heroWebglActive = 'true';
    root.dataset.heroWebglDirection = direction > 0 ? 'right-to-left' : 'left-to-right';
    resize();
    draw(active.startedAt);
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
