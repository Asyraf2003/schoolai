import { createWarpResources } from './reel-warp-gl.js';

function emptyController() {
    return {
        render: function render() {},
        setEnabled: function setEnabled() {},
        setNear: function setNear() {},
        destroy: function destroy() {}
    };
}

function phase(progress, start, end) {
    return Math.min(Math.max((progress - start) / (end - start), 0), 1);
}

function bendAt(progress) {
    var arrival = Math.sin(Math.PI * phase(progress, 0.36, 0.58));
    var expansion = Math.sin(Math.PI * phase(progress, 0.7, 1));
    return arrival * 0.72 + expansion * 0.38;
}

export function createReelWarp(root) {
    var canvas = root.querySelector('[data-about-reel-warp]');
    var video = root.querySelector('[data-about-reel-video]');
    var image = root.querySelector('[data-about-reel-image]');
    var gl = canvas
        ? canvas.getContext('webgl', { alpha: true, antialias: true })
        : null;
    var enabled = false;
    var near = false;
    var destroyed = false;
    var progress = 0;
    var frame = null;
    var resizeObserver = null;

    if (!canvas || !gl) {
        if (canvas) canvas.hidden = true;
        return emptyController();
    }

    var vertexSource = [
        'attribute vec2 aPosition;',
        'attribute vec2 aUv;',
        'uniform float uProgress;',
        'uniform float uBend;',
        'varying vec2 vUv;',
        'void main() {',
        '  vec2 pos = aPosition;',
        '  float flow = sin((aUv.x * 1.35 + aUv.y * 0.42 + uProgress * 0.55) * 6.2831853);',
        '  float crossFlow = sin((aUv.y * 1.2 - uProgress * 0.35) * 3.14159265);',
        '  pos *= 1.0 - uBend * 0.075;',
        '  pos.y += flow * uBend * (0.065 + abs(pos.x) * 0.015);',
        '  pos.x += crossFlow * uBend * 0.025;',
        '  vUv = aUv;',
        '  gl_Position = vec4(pos, 0.0, 1.0);',
        '}'
    ].join('');
    var fragmentSource = [
        'precision mediump float;',
        'varying vec2 vUv;',
        'uniform sampler2D uTexture;',
        'void main() { gl_FragColor = texture2D(uTexture, vUv); }'
    ].join('');
    var resources = createWarpResources(gl, vertexSource, fragmentSource);
    if (!resources) {
        canvas.hidden = true;
        return emptyController();
    }

    function resize() {
        var ratio = Math.min(window.devicePixelRatio || 1, 2);
        var width = Math.max(1, Math.round(canvas.clientWidth * ratio));
        var height = Math.max(1, Math.round(canvas.clientHeight * ratio));
        if (canvas.width === width && canvas.height === height) return;
        canvas.width = width;
        canvas.height = height;
        gl.viewport(0, 0, width, height);
    }

    function textureSource() {
        if (video && video.readyState >= 2) return video;
        if (image && image.complete && image.naturalWidth > 0) return image;
        return null;
    }

    function requestDraw() {
        if (!enabled || !near || destroyed || frame !== null) return;
        frame = window.requestAnimationFrame(draw);
    }

    function draw() {
        frame = null;
        if (!enabled || !near || destroyed) return;
        var source = textureSource();
        if (!source) {
            if (video) requestDraw();
            return;
        }
        resize();
        try {
            gl.bindTexture(gl.TEXTURE_2D, resources.texture);
            gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, source);
        } catch (error) {
            canvas.hidden = true;
            return;
        }
        canvas.hidden = false;
        gl.clearColor(0, 0, 0, 0);
        gl.clear(gl.COLOR_BUFFER_BIT);
        gl.uniform1f(resources.progressUniform, progress);
        gl.uniform1f(resources.bendUniform, bendAt(progress));
        gl.drawElements(gl.TRIANGLES, resources.indexCount, gl.UNSIGNED_SHORT, 0);
        if (video) requestDraw();
    }

    function render(nextProgress) {
        progress = nextProgress;
        requestDraw();
    }

    function setEnabled(nextEnabled) {
        enabled = Boolean(nextEnabled);
        canvas.hidden = true;
        if (enabled) requestDraw();
    }

    function setNear(nextNear) {
        near = Boolean(nextNear);
        if (near) requestDraw();
        else if (frame !== null) {
            window.cancelAnimationFrame(frame);
            frame = null;
        }
    }

    function destroy() {
        destroyed = true;
        setNear(false);
        enabled = false;
        if (resizeObserver) resizeObserver.disconnect();
        gl.deleteTexture(resources.texture);
        gl.deleteBuffer(resources.vertexBuffer);
        gl.deleteBuffer(resources.indexBuffer);
        gl.deleteProgram(resources.program);
        canvas.hidden = true;
    }

    if (typeof ResizeObserver === 'function') {
        resizeObserver = new ResizeObserver(resize);
        resizeObserver.observe(canvas);
    }
    resize();
    canvas.addEventListener('webglcontextlost', function onContextLost(event) {
        event.preventDefault();
        setNear(false);
        canvas.hidden = true;
    });
    return {
        render: render,
        setEnabled: setEnabled,
        setNear: setNear,
        destroy: destroy
    };
}
