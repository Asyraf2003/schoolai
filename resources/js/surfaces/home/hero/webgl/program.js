import { HERO_FRAGMENT_SHADER, HERO_VERTEX_SHADER } from './shaders.js';

function compileShader(gl, type, source) {
  var shader = gl.createShader(type);
  if (!shader) return null;

  gl.shaderSource(shader, source);
  gl.compileShader(shader);

  if (gl.getShaderParameter(shader, gl.COMPILE_STATUS)) return shader;
  gl.deleteShader(shader);
  return null;
}

export function createHeroProgram(gl) {
  var vertex = compileShader(gl, gl.VERTEX_SHADER, HERO_VERTEX_SHADER);
  var fragment = compileShader(gl, gl.FRAGMENT_SHADER, HERO_FRAGMENT_SHADER);
  if (!vertex || !fragment) return null;

  var program = gl.createProgram();
  if (!program) return null;

  gl.attachShader(program, vertex);
  gl.attachShader(program, fragment);
  gl.linkProgram(program);
  gl.deleteShader(vertex);
  gl.deleteShader(fragment);

  if (gl.getProgramParameter(program, gl.LINK_STATUS)) return program;
  gl.deleteProgram(program);
  return null;
}
