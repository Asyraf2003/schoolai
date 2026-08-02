const VERTEX_SHADER = `
attribute vec2 position;
varying vec2 vUv;
void main() {
    vUv = position * 0.5 + 0.5;
    gl_Position = vec4(position, 0.0, 1.0);
}`;

const FRAGMENT_SHADER = `
precision mediump float;
varying vec2 vUv;
uniform vec2 resolution;
uniform vec2 pointer;
uniform float time;
uniform float velocity;
uniform vec3 backgroundColor;
uniform vec3 blobAColor;
uniform vec3 blobBColor;

void main() {
    vec2 uv = vUv;
    float aspect = resolution.x / max(resolution.y, 1.0);
    vec2 p = vec2((uv.x - 0.5) * aspect, uv.y - 0.5);
    vec2 drift = pointer * 0.09;
    vec2 aCenter = vec2(-0.34, 0.20) + drift;
    vec2 bCenter = vec2(0.34, -0.18) - drift;
    aCenter += vec2(sin(time * 0.28), cos(time * 0.24)) * 0.045;
    bCenter += vec2(cos(time * 0.22), sin(time * 0.31)) * 0.055;
    float a = smoothstep(0.78, 0.02, length(p - aCenter));
    float b = smoothstep(0.82, 0.03, length(p - bCenter));
    float pulse = 0.5 + 0.5 * sin(time * 0.7 + length(p) * 7.0);
    vec3 color = backgroundColor;
    color = mix(color, blobAColor, a * (0.62 + min(abs(velocity) * 3.0, 0.2)));
    color = mix(color, blobBColor, b * (0.56 + pulse * 0.1));
    float vignette = smoothstep(1.08, 0.2, length(p));
    color *= 0.9 + vignette * 0.1;
    gl_FragColor = vec4(color, 1.0);
}`;

function compileShader(gl, type, source) {
    const shader = gl.createShader(type);
    if (!shader) return null;
    gl.shaderSource(shader, source);
    gl.compileShader(shader);
    if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
        gl.deleteShader(shader);
        return null;
    }
    return shader;
}

function hexToRgb(hex) {
    const value = hex.trim().replace('#', '');
    const normalized = value.length === 3
        ? value.split('').map((part) => part + part).join('')
        : value.padEnd(6, '0').slice(0, 6);
    const number = Number.parseInt(normalized, 16);
    return [
        ((number >> 16) & 255) / 255,
        ((number >> 8) & 255) / 255,
        (number & 255) / 255,
    ];
}

export class DepthGalleryRenderer {
    constructor(canvas, onFailure) {
        this.canvas = canvas;
        this.onFailure = onFailure;
        this.gl = null;
        this.program = null;
        this.buffer = null;
        this.locations = {};
        this.pointer = [0, 0];
        this.onContextLost = (event) => {
            event.preventDefault();
            this.onFailure?.();
        };
    }

    init() {
        const gl = this.canvas.getContext('webgl', {
            alpha: false,
            antialias: false,
            powerPreference: 'low-power',
        });
        if (!gl) return false;

        const vertex = compileShader(gl, gl.VERTEX_SHADER, VERTEX_SHADER);
        const fragment = compileShader(gl, gl.FRAGMENT_SHADER, FRAGMENT_SHADER);
        if (!vertex || !fragment) return false;

        const program = gl.createProgram();
        if (!program) return false;
        gl.attachShader(program, vertex);
        gl.attachShader(program, fragment);
        gl.linkProgram(program);
        gl.deleteShader(vertex);
        gl.deleteShader(fragment);
        if (!gl.getProgramParameter(program, gl.LINK_STATUS)) return false;

        const buffer = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, buffer);
        gl.bufferData(
            gl.ARRAY_BUFFER,
            new Float32Array([-1, -1, 1, -1, -1, 1, -1, 1, 1, -1, 1, 1]),
            gl.STATIC_DRAW,
        );

        this.gl = gl;
        this.program = program;
        this.buffer = buffer;
        this.locations = {
            position: gl.getAttribLocation(program, 'position'),
            resolution: gl.getUniformLocation(program, 'resolution'),
            pointer: gl.getUniformLocation(program, 'pointer'),
            time: gl.getUniformLocation(program, 'time'),
            velocity: gl.getUniformLocation(program, 'velocity'),
            background: gl.getUniformLocation(program, 'backgroundColor'),
            blobA: gl.getUniformLocation(program, 'blobAColor'),
            blobB: gl.getUniformLocation(program, 'blobBColor'),
        };
        this.canvas.addEventListener('webglcontextlost', this.onContextLost, false);
        this.resize();
        return true;
    }

    resize() {
        if (!this.gl) return;
        const dpr = Math.min(window.devicePixelRatio || 1, 1.5);
        const width = Math.max(1, Math.round(this.canvas.clientWidth * dpr));
        const height = Math.max(1, Math.round(this.canvas.clientHeight * dpr));
        if (this.canvas.width === width && this.canvas.height === height) return;
        this.canvas.width = width;
        this.canvas.height = height;
        this.gl.viewport(0, 0, width, height);
    }

    render(palette, time, velocity) {
        const gl = this.gl;
        if (!gl || !this.program || !this.buffer) return;
        gl.useProgram(this.program);
        gl.bindBuffer(gl.ARRAY_BUFFER, this.buffer);
        gl.enableVertexAttribArray(this.locations.position);
        gl.vertexAttribPointer(this.locations.position, 2, gl.FLOAT, false, 0, 0);
        gl.uniform2f(this.locations.resolution, this.canvas.width, this.canvas.height);
        gl.uniform2f(this.locations.pointer, this.pointer[0], this.pointer[1]);
        gl.uniform1f(this.locations.time, time * 0.001);
        gl.uniform1f(this.locations.velocity, velocity);
        gl.uniform3fv(this.locations.background, palette.background);
        gl.uniform3fv(this.locations.blobA, palette.blobA);
        gl.uniform3fv(this.locations.blobB, palette.blobB);
        gl.drawArrays(gl.TRIANGLES, 0, 6);
    }

    setPointer(x, y) {
        this.pointer = [x, y];
    }

    static color(hex) {
        return hexToRgb(hex);
    }

    dispose() {
        const gl = this.gl;
        this.canvas.removeEventListener('webglcontextlost', this.onContextLost, false);
        if (gl && this.buffer) gl.deleteBuffer(this.buffer);
        if (gl && this.program) gl.deleteProgram(this.program);
        this.gl = null;
        this.program = null;
        this.buffer = null;
    }
}
