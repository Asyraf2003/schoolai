// Adapted for SchoolAI from Demo 1 of akella/webGLImageTransitions.
// The original integration-friendly license is documented in the Hero blueprint.
export const HERO_VERTEX_SHADER = `
attribute vec2 aPosition;
varying vec2 vUv;

void main() {
  vUv = aPosition * 0.5 + 0.5;
  gl_Position = vec4(aPosition, 0.0, 1.0);
}
`;

export const HERO_FRAGMENT_SHADER = `
precision highp float;

uniform sampler2D uFrom;
uniform sampler2D uTo;
uniform float uProgress;
uniform float uDirection;
uniform float uWidth;
uniform vec2 uNoiseScale;
uniform vec2 uFromScale;
uniform vec2 uToScale;

varying vec2 vUv;

float hash(vec2 point) {
  return fract(sin(dot(point, vec2(127.1, 311.7))) * 43758.5453123);
}

float noise(vec2 point) {
  vec2 cell = floor(point);
  vec2 fraction = fract(point);
  fraction = fraction * fraction * (3.0 - 2.0 * fraction);

  float a = hash(cell);
  float b = hash(cell + vec2(1.0, 0.0));
  float c = hash(cell + vec2(0.0, 1.0));
  float d = hash(cell + vec2(1.0, 1.0));

  return mix(mix(a, b, fraction.x), mix(c, d, fraction.x), fraction.y);
}

float parabola(float value) {
  return 4.0 * value * (1.0 - value);
}

vec2 coverUv(vec2 uv, vec2 scale) {
  return (uv - vec2(0.5)) * scale + vec2(0.5);
}

void main() {
  float progress = clamp(uProgress, 0.0, 1.0);
  float axis = uDirection > 0.0 ? vUv.x : 1.0 - vUv.x;
  float width = max(0.001, uWidth * parabola(progress));
  float sweep = axis + mix(-width * 0.5, 1.0 - width * 0.5, progress);
  float grain = noise(vUv * uNoiseScale);
  float mask = smoothstep(1.0 - width, 1.0, sweep + grain * width);
  vec4 outgoing = texture2D(uFrom, coverUv(vUv, uFromScale));
  vec4 incoming = texture2D(uTo, coverUv(vUv, uToScale));

  gl_FragColor = mix(outgoing, incoming, mask);
}
`;
