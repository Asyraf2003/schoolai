import test from 'node:test';
import assert from 'node:assert/strict';
import { bentPlane } from '../../resources/js/gallery-spatial-geometry.js';

test('the media surface has curved interior vertices and unchanged image UVs', () => {
    const geometry = bentPlane(4.8, 3.2);
    const positions = geometry.attributes.position, uv = geometry.attributes.uv;
    const depths = new Set();
    for (let i = 0; i < positions.count; i++) {
        depths.add(positions.getZ(i).toFixed(4));
        assert.ok(uv.getX(i) >= 0 && uv.getX(i) <= 1);
        assert.ok(uv.getY(i) >= 0 && uv.getY(i) <= 1);
    }
    assert.equal(positions.count, 49 * 13);
    assert.ok(depths.size > 20, 'interior columns occupy distinct depths');
    assert.equal(uv.getX(0), 0); assert.equal(uv.getY(0), 1);
    assert.equal(uv.getX(48), 1); assert.equal(uv.getY(positions.count - 1), 0);
    assert.ok(positions.getZ(0) < -.5);
    assert.equal(positions.getZ(24), 0);
    geometry.dispose();
});
