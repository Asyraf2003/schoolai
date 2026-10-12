import test from 'node:test';
import assert from 'node:assert/strict';
import { storiesCheckpoints, storiesCopyPose, storiesWordPose, storiesMediaPose }
    from '../../resources/js/sections/stories-motion.js';

test('next title centers exactly as the previous copy exits the upper edge', () => {
    const stageHeight = 900, heights = [164, 136, 183, 155], offsets = [-14, -10, -25, -8];
    const { centers, anchors, travel } = storiesCheckpoints(heights, stageHeight, false, offsets);
    assert.equal(travel, centers[3]);
    assert.ok(travel > 0);
    for (let i = 1; i < centers.length; i++) {
        const previousCopyCenter = anchors[i - 1] + centers[i - 1] - centers[i];
        const arrivingCopyCenter = anchors[i];
        assert.equal(previousCopyCenter + heights[i - 1] / 2, 0);
        assert.equal(arrivingCopyCenter + offsets[i], stageHeight / 2);
        assert.equal(storiesMediaPose(centers[i] - .01, centers, stageHeight, false).current, i - 1);
        assert.equal(storiesMediaPose(centers[i], centers, stageHeight, false).current, i);
    }
});

test('entry alone has word blur; exit off top keeps full opacity and zero blur', () => {
    const stageHeight = 900, heights = [164, 136, 183, 155], offset = -10;
    const { centers, anchors } = storiesCheckpoints(heights, stageHeight, false, [0, offset, 0, 0]);
    const below = storiesCopyPose(0, centers[1], anchors[1], heights[1], stageHeight, offset);
    const arrival = storiesCopyPose(centers[1], centers[1], anchors[1], heights[1], stageHeight, offset);
    const departing = storiesCopyPose(centers[2], centers[1], anchors[1], heights[1], stageHeight, offset);
    assert.equal(below.enters, 0);
    assert.equal(arrival.enters, 1);
    assert.equal(departing.enters, 1);
    assert.deepEqual(storiesWordPose(arrival.enters, 12, 13), { opacity: 1, blur: 0 });
    assert.deepEqual(storiesWordPose(departing.enters, 12, 13), { opacity: 1, blur: 0 });
    assert.ok(storiesWordPose(.3, 1, 13).blur > 0);
});

test('phone first title starts low and media rises after next heading center', () => {
    const stageHeight = 780, heights = [164, 136, 183, 155], offsets = [-10, -24, -12, -18];
    const { centers, anchors } = storiesCheckpoints(heights, stageHeight, true, offsets);
    assert.ok(anchors[0] > stageHeight / 2);
    assert.equal(anchors[1] + offsets[1], stageHeight / 2);
    const before = storiesMediaPose(centers[1] - 1, centers, stageHeight, true);
    const at = storiesMediaPose(centers[1], centers, stageHeight, true);
    const after = storiesMediaPose(centers[1] + stageHeight * .28, centers, stageHeight, true);
    assert.equal(before.current, 0);
    assert.equal(at.current, 1);
    assert.ok(before.layers[1].y > 0);
    assert.ok(before.layers[1].y < 1);
    assert.equal(at.layers[1].y, 0);
    assert.equal(after.layers[1].y, 0);
    assert.equal(after.layers[0].y, 0);
});

test('last media is fully visible when the white footer can begin rising', () => {
    const stageHeight = 780, heights = [164, 136, 183, 155];
    const { centers, travel } = storiesCheckpoints(heights, stageHeight, true, [-10, -24, -12, -18]);
    assert.equal(travel, centers[3]);
    const final = storiesMediaPose(travel, centers, stageHeight, true);
    assert.equal(final.current, 3);
    assert.equal(final.layers[3].y, 0);
});
