function compileShader(gl, type, source) {
    var shader = gl.createShader(type);
    gl.shaderSource(shader, source);
    gl.compileShader(shader);
    if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
        gl.deleteShader(shader);
        return null;
    }
    return shader;
}

export function createReelWarp(root) {
    var canvas = root.querySelector('[data-about-reel-warp]');
    var video = root.querySelector('[data-about-reel-video]');
    var image = root.querySelector('[data-about-reel-image]');
    var gl = canvas
        ? canvas.getContext('webgl', { alpha: true, antialias: true })
        : null;
    var enabled = false;
    var destroyed = false;
    var resizeObserver = null;

    if (!canvas || !gl) {
        return { render: function render() {}, setEnabled: function setEnabled() {}, destroy: function destroy() {} };
    }

    var vertexSource = [
        'attribute vec2 aPosition;',
        'attribute vec2 aUv;',
        'uniform float uProgress;',
        'uniform float uBend;',
        'varying vec2 vUv;',
        'void main() {',
        '  vec2 pos = aPosition;',
        '  float diagonal = (pos.x + pos.y) * 0.5;',
        '  float wave = sin((diagonal + uProgress * 0.7) * 3.14159265);',
        '  float edge = pow(abs(pos.x), 1.35);',
        '  pos *= 1.0 - uBend * 0.24;',
        '  pos.y += wave * uBend * (0.34 + edge * 0.24);',
        '  pos.x += wave * uBend * 0.14;',
        '  float twist = uBend * 0.12;',
        '  float s = sin(twist);',
        '  float c = cos(twist);',
        '  pos = mat2(c, -s, s, c) * pos;',
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
    var vertexShader = compileShader(gl, gl.VERTEX_SHADER, vertexSource);
    var fragmentShader = compileShader(gl, gl.FRAGMENT_SHADER, fragmentSource);
    if (!vertexShader || !fragmentShader) {
        canvas.hidden = true;
        return { render: function render() {}, setEnabled: function setEnabled() {}, destroy: function destroy() {} };
    }

    var program = gl.createProgram();
    gl.attachShader(program, vertexShader);
    gl.attachShader(program, fragmentShader);
    gl.linkProgram(program);
    if (!gl.getProgramParameter(program, gl.LINK_STATUS)) {
        canvas.hidden = true;
        return { render: function render() {}, setEnabled: function setEnabled() {}, destroy: function destroy() {} };
    }
    gl.useProgram(program);

    var columns = 28;
    var rows = 16;
    var vertices = [];
    var indices = [];
    for (var row = 0; row <= rows; row += 1) {
        for (var column = 0; column <= columns; column += 1) {
            var u = column / columns;
            var v = row / rows;
            vertices.push(u * 2 - 1, v * 2 - 1, u, 1 - v);
        }
    }
    for (row = 0; row < rows; row += 1) {
        for (column = 0; column < columns; column += 1) {
            var a = row * (columns + 1) + column;
            var b = a + 1;
            var c = a + columns + 1;
            var d = c + 1;
            indices.push(a, b, c, b, d, c);
        }
    }

    var vertexBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, vertexBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(vertices), gl.STATIC_DRAW);
    var stride = 4 * Float32Array.BYTES_PER_ELEMENT;
    var position = gl.getAttribLocation(program, 'aPosition');
    var uv = gl.getAttribLocation(program, 'aUv');
    gl.enableVertexAttribArray(position);
    gl.vertexAttribPointer(position, 2, gl.FLOAT, false, stride, 0);
    gl.enableVertexAttribArray(uv);
    gl.vertexAttribPointer(uv, 2, gl.FLOAT, false, stride, 2 * Float32Array.BYTES_PER_ELEMENT);

    var indexBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, indexBuffer);
    gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, new Uint16Array(indices), gl.STATIC_DRAW);
    var progressUniform = gl.getUniformLocation(program, 'uProgress');
    var bendUniform = gl.getUniformLocation(program, 'uBend');
    var texture = gl.createTexture();
    gl.bindTexture(gl.TEXTURE_2D, texture);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
    gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, false);

    function resize() {
        var ratio = Math.min(window.devicePixelRatio || 1, 2);
        var width = Math.max(1, Math.round(canvas.clientWidth * ratio));
        var height = Math.max(1, Math.round(canvas.clientHeight * ratio));
        if (canvas.width !== width || canvas.height !== height) {
            canvas.width = width;
            canvas.height = height;
            gl.viewport(0, 0, width, height);
        }
    }

    function textureSource() {
        if (video && video.readyState >= 2) return video;
        if (image && image.complete && image.naturalWidth > 0) return image;
        return null;
    }

    function render(progress) {
        if (!enabled || destroyed) return;
        var source = textureSource();
        if (!source) return;
        resize();
        try {
            gl.bindTexture(gl.TEXTURE_2D, texture);
            gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, source);
        } catch (error) {
            canvas.hidden = true;
            return;
        }
        canvas.hidden = false;
        gl.clearColor(0, 0, 0, 0);
        gl.clear(gl.COLOR_BUFFER_BIT);
        gl.uniform1f(progressUniform, progress);
        gl.uniform1f(bendUniform, Math.sin(Math.PI * progress));
        gl.drawElements(gl.TRIANGLES, indices.length, gl.UNSIGNED_SHORT, 0);
    }

    function setEnabled(nextEnabled) {
        enabled = Boolean(nextEnabled);
        canvas.hidden = true;
    }

    function destroy() {
        destroyed = true;
        enabled = false;
        if (resizeObserver) resizeObserver.disconnect();
        gl.deleteTexture(texture);
        gl.deleteBuffer(vertexBuffer);
        gl.deleteBuffer(indexBuffer);
        gl.deleteProgram(program);
        canvas.hidden = true;
    }

    if (typeof ResizeObserver === 'function') {
        resizeObserver = new ResizeObserver(resize);
        resizeObserver.observe(canvas);
    }
    resize();
    canvas.addEventListener('webglcontextlost', function onContextLost(event) {
        event.preventDefault();
        canvas.hidden = true;
    });
    return { render: render, setEnabled: setEnabled, destroy: destroy };
}
