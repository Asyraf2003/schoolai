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

function createProgram(gl, vertexSource, fragmentSource) {
    var vertexShader = compileShader(gl, gl.VERTEX_SHADER, vertexSource);
    var fragmentShader = compileShader(gl, gl.FRAGMENT_SHADER, fragmentSource);
    if (!vertexShader || !fragmentShader) return null;

    var program = gl.createProgram();
    gl.attachShader(program, vertexShader);
    gl.attachShader(program, fragmentShader);
    gl.linkProgram(program);
    gl.deleteShader(vertexShader);
    gl.deleteShader(fragmentShader);
    if (gl.getProgramParameter(program, gl.LINK_STATUS)) return program;

    gl.deleteProgram(program);
    return null;
}

export function createWarpResources(gl, vertexSource, fragmentSource) {
    var program = createProgram(gl, vertexSource, fragmentSource);
    if (!program) return null;
    gl.useProgram(program);

    var columns = 40;
    var rows = 24;
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
    var texture = gl.createTexture();
    gl.bindTexture(gl.TEXTURE_2D, texture);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
    gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, false);

    return {
        program: program,
        vertexBuffer: vertexBuffer,
        indexBuffer: indexBuffer,
        indexCount: indices.length,
        progressUniform: gl.getUniformLocation(program, 'uProgress'),
        bendUniform: gl.getUniformLocation(program, 'uBend'),
        pullUniform: gl.getUniformLocation(program, 'uPull'),
        texture: texture
    };
}
