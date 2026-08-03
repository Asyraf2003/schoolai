/*
 * Adapted from Houmahani Kane / Codrops Atmospheric Depth Gallery.
 * MIT license notice: docs/third-party/codrops-depth-gallery-MIT.txt
 */

export const backgroundVertexShader = `
varying vec2 vUv;

void main() {
    vUv = uv;
    gl_Position = vec4(position.xy, 0.0, 1.0);
}
`;

export const backgroundFragmentShader = `
varying vec2 vUv;

uniform vec3 uBackgroundColor;
uniform vec3 uBlob1Color;
uniform vec3 uBlob2Color;
uniform float uNoiseStrength;
uniform float uBlobRadius;
uniform float uBlobRadiusSecondary;
uniform float uBlobStrength;
uniform float uTime;
uniform float uVelocityIntensity;

float random(vec2 coord) {
    return fract(sin(dot(coord, vec2(12.9898, 78.233))) * 43758.5453123);
}

void main() {
    vec3 color = uBackgroundColor;
    float animTime = uTime * 0.00028;
    vec2 blob1Center = vec2(
        0.50 + sin(animTime * 1.000) * 0.13 + sin(animTime * 1.618) * 0.05,
        0.48 + cos(animTime * 0.794) * 0.09 + cos(animTime * 1.272) * 0.03
    );
    vec2 blob2Center = vec2(
        0.35 + cos(animTime * 0.927) * 0.11 + cos(animTime * 1.414) * 0.04,
        0.55 + sin(animTime * 1.175) * 0.07 + sin(animTime * 0.618) * 0.03
    );
    float blob1 = smoothstep(uBlobRadius, 0.0, distance(vUv, blob1Center));
    float blob2 = smoothstep(uBlobRadiusSecondary, 0.0, distance(vUv, blob2Center));
    vec3 blob1SoftColor = mix(uBlob1Color, uBackgroundColor, 0.35);
    vec3 blob2SoftColor = mix(uBlob2Color, uBackgroundColor, 0.35);
    color = mix(color, blob1SoftColor, blob1 * uBlobStrength);
    color = mix(color, blob2SoftColor, blob2 * uBlobStrength);
    color += uVelocityIntensity * 0.10;
    float grain = random(vUv * vec2(1387.13, 947.91)) - 0.5;
    color += grain * uNoiseStrength;
    gl_FragColor = vec4(clamp(color, 0.0, 1.0), 1.0);
}
`;
