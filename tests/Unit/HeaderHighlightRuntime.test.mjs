import test from 'node:test';
import assert from 'node:assert/strict';
import { highlightedIndex } from '../../resources/js/sections/header-highlight.js';

test('Main highlight temporarily follows hover, then open dropdown, then current section', () => {
    assert.equal(highlightedIndex({ current: 0 }), 0);
    assert.equal(highlightedIndex({ current: 0, panel: 2 }), 2);
    assert.equal(highlightedIndex({ current: 0, panel: 2, hover: 3 }), 3);
    assert.equal(highlightedIndex({ current: 0, panel: 2, hover: null }), 2);
    assert.equal(highlightedIndex({ current: 0, panel: null }), 0);
});
