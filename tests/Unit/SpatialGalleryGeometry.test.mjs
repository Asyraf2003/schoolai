import test from 'node:test';
import assert from 'node:assert/strict';
import { trackPlane, deformTrack, trackPoint, mediaIndex, nearestOccurrence } from '../../resources/js/gallery-spatial-geometry.js';
const curve = { radius: 4.4, depth: 2.4 };
const close = (a,b) => assert.ok(Math.abs(a-b)<.00001,`${a} != ${b}`);

test('adjacent panel vertices sample one world curve, including the common edge and tangent', () => {
    const left = trackPlane(), right = trackPlane();
    deformTrack(left,-2,4,3,curve); deformTrack(right,2,4,3,curve);
    const a = left.attributes.position, b = right.attributes.position;
    // The same global coordinate is identical regardless of panel ownership.
    for (const axis of ['getX','getY','getZ']) close(a[axis](48), b[axis](0));
    close((a.getZ(48)-a.getZ(47)), -(b.getZ(1)-b.getZ(0)));
    assert.ok(a.getZ(24)<-.2, 'panel center at -2 must not reset its depth to zero');
    assert.ok(b.getZ(24)<-.2, 'panel center at +2 must not reset its depth to zero');
    const uv = left.attributes.uv;
    assert.equal(uv.getX(0),0);assert.equal(uv.getX(48),1);assert.equal(uv.getY(0),1);
    assert.equal(a.count,49*13);
    left.dispose();right.dispose();
});

test('the same vertex crosses a media-cycle seam continuously without resetting progress', () => {
    for (const count of [1,2,3,12,1000]) {
        assert.equal(mediaIndex(-1,count),count-1);assert.equal(mediaIndex(count,count),0);
        const cycleSlot=count;
        const before=trackPoint((cycleSlot-(count-.001))*5,0,curve);
        const after=trackPoint((cycleSlot-(count+.001))*5,0,curve);
        close(before.x-after.x,.01);close(before.z,after.z);
        assert.equal(nearestOccurrence(0,count+.1,count),count);
        assert.equal(nearestOccurrence(count-1,-1.1,count),-1);
    }
});

test('a side panel becomes the center by moving its sampling interval, not rotating its mesh', () => {
    const geometry=trackPlane();
    deformTrack(geometry,5,4,3,curve);
    const side=geometry.attributes.position.getZ(24);
    deformTrack(geometry,0,4,3,curve);
    assert.ok(side<-1);close(geometry.attributes.position.getZ(24),0);
    geometry.dispose();
});
