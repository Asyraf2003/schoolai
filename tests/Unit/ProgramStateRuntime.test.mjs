import test from 'node:test';
import assert from 'node:assert/strict';
import { createProgramState } from '../../resources/js/sections/program-state.js';

test('one selected detail owns the opening and closing lifecycle', () => {
    const state = createProgramState(8);
    const token = state.begin(2);
    assert.equal(state.phase, 'preparing');
    assert.equal(state.index, 2);
    assert.equal(state.begin(5), null);
    assert.equal(state.opened(token, true), true);
    assert.equal(state.phase, 'opening');
    state.revealed();
    assert.equal(state.phase, 'detail');
    assert.equal(state.close(), true);
    assert.equal(state.close(), false);
    state.reset();
    assert.equal(state.phase, 'idle');
    assert.equal(state.index, -1);
});

test('Escape during preparation rejects a late load and allows a fresh selection', () => {
    const state = createProgramState(8);
    const cancelled = state.begin(0);
    state.close(); state.reset();
    const current = state.begin(7);
    assert.equal(state.accepts(cancelled), false);
    assert.equal(state.opened(cancelled, true), false);
    assert.equal(state.opened(current, false), true);
    assert.equal(state.phase, 'detail');
    assert.equal(state.index, 7);
});

test('invalid selections and disposed state never take background ownership', () => {
    const state = createProgramState(8);
    for (const invalid of [-1, 8, 1.1, NaN, '1']) assert.equal(state.begin(invalid), null);
    const token = state.begin(0);
    state.dispose();
    assert.equal(state.opened(token, true), false);
    assert.equal(state.begin(0), null);
    assert.equal(state.close(), false);
});
