import test from 'node:test';
import assert from 'node:assert/strict';
import { createVideoFullscreenController } from '../../resources/js/pages/welcome/video-fullscreen.js';
import { VIDEO_LABELS } from '../../resources/js/pages/welcome/video-utilities.js';

test('fullscreen controller retains locale labels, browser fallback and rejected exit behavior', async () => {
    const original = globalThis.document;
    const state = new Map();
    const shell = { classList: { toggle: (key, value) => state.set(key, value) } };
    const button = { setAttribute: (key, value) => state.set(key, value) };
    globalThis.document = { fullscreenElement: null };
    try {
        for (const labels of Object.values(VIDEO_LABELS)) {
            const controller = createVideoFullscreenController(shell, button, labels);
            controller.updateFullscreenState();
            assert.equal(state.get('aria-label'), labels.enterFullscreen);
            assert.equal(state.get('is-fullscreen'), false);
            shell.webkitRequestFullscreen = () => { document.webkitFullscreenElement = shell; };
            controller.toggleFullscreen();
            controller.updateFullscreenState();
            assert.equal(state.get('aria-label'), labels.exitFullscreen);
            assert.equal(state.get('is-fullscreen'), true);
            document.webkitExitFullscreen = () => { document.webkitFullscreenElement = null; };
            controller.toggleFullscreen();
            assert.equal(controller.isShellFullscreen(), false);
            document.exitFullscreen = () => { throw new Error('denied'); };
            assert.equal(controller.exitFullscreen(), null);
            delete document.exitFullscreen;
        }
    } finally {
        globalThis.document = original;
    }
});
