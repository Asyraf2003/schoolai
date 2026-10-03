function activeFullscreenElement() {
    return document.fullscreenElement ?? document.webkitFullscreenElement ?? null;
}

export function createVideoFullscreenController(shell, fullscreenButton, labels) {
    function isShellFullscreen() {
        return activeFullscreenElement() === shell;
    }

    function updateFullscreenState() {
        fullscreenButton.setAttribute(
            'aria-label',
            isShellFullscreen() ? labels.exitFullscreen : labels.enterFullscreen,
        );
        shell.classList.toggle('is-fullscreen', isShellFullscreen());
    }

    function requestShellFullscreen() {
        const request = shell.requestFullscreen ?? shell.webkitRequestFullscreen;
        if (typeof request !== 'function') return;

        const attempt = request.call(shell);
        if (attempt && typeof attempt.catch === 'function') {
            attempt.catch(() => {});
        }
    }

    function exitFullscreen() {
        const exit = document.exitFullscreen ?? document.webkitExitFullscreen;
        if (typeof exit !== 'function') return null;

        try {
            return exit.call(document);
        } catch {
            return null;
        }
    }

    function toggleFullscreen() {
        if (isShellFullscreen()) {
            const attempt = exitFullscreen();
            if (attempt && typeof attempt.catch === 'function') {
                attempt.catch(() => {});
            }
            return;
        }

        requestShellFullscreen();
    }

    return { isShellFullscreen, updateFullscreenState, toggleFullscreen, exitFullscreen };
}
