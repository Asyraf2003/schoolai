import test from 'node:test';
import assert from 'node:assert/strict';
import { chooseCharacter, cursorState } from '../../resources/js/sections/cursor.js';

test('Cursor picks one of the existing identities from the supplied initialization sample', () => {
    let calls = 0;
    assert.equal(chooseCharacter(() => { calls++; return 0.25; }), 'cwo');
    assert.equal(calls, 1);
    assert.equal(chooseCharacter(() => 0.75), 'cwe');
});

test('Cursor treats disabled and inert controls as default while actionable controls are interactive', () => {
    assert.equal(cursorState(null), 'default');
    const target = (disabled, inert) => ({ closest: () => ({ matches: () => disabled, closest: () => inert }) });
    assert.equal(cursorState(target(false, null)), 'interactive');
    assert.equal(cursorState(target(true, null)), 'default');
    assert.equal(cursorState(target(false, {})), 'default');
});
