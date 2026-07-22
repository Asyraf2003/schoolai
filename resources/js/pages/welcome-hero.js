/* Homepage mixed-media slider and deterministic desktop mega-menu. */

function onMediaQueryChange(query, listener) {
    if (typeof query.addEventListener === 'function') {
        query.addEventListener('change', listener);
        return function () { query.removeEventListener('change', listener); };
    }

    query.addListener(listener);
    return function () { query.removeListener(listener); };
}

function initMegaMenus() {
    var header = document.getElementById('navbar');
    var menus = Array.prototype.slice.call(document.querySelectorAll('[data-nav-mega]'));

    if (!header || !menus.length) return;

    function postMegaVideoCommand(frame, command) {
        if (!frame || !frame.contentWindow || !frame.hasAttribute('src')) return;

        frame.contentWindow.postMessage(JSON.stringify({
            event: 'command',
            func: command,
            args: [],
        }), '*');
    }

    function syncMegaVideo(panel, isOpen) {
        var frame = panel.querySelector('[data-nav-mega-video]');
        if (!frame || !window.matchMedia('(min-width: 1181px)').matches) return;

        if (isOpen) {
            if (!frame.hasAttribute('src')) {
                var source = frame.getAttribute('data-src');
                if (!source) return;

                frame.setAttribute('src', source);
            }

            postMegaVideoCommand(frame, 'playVideo');
        } else {
            postMegaVideoCommand(frame, 'pauseVideo');
        }
    }

    function setMenuState(menu, isOpen, focusFirstLink) {
        var toggle = menu.querySelector('[data-nav-mega-toggle]');
        var panel = menu.querySelector('[data-nav-mega-panel]');

        if (!toggle || !panel) return;

        menu.classList.toggle('is-open', isOpen);
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        panel.inert = !isOpen;
        syncMegaVideo(panel, isOpen);

        if (isOpen && focusFirstLink) {
            var firstLink = panel.querySelector('a[href]');
            if (firstLink) firstLink.focus();
        }
    }

    function syncHeaderState() {
        header.classList.toggle('has-open-menu', menus.some(function (menu) {
            return menu.classList.contains('is-open');
        }));
    }

    function closeMenus(exceptMenu) {
        menus.forEach(function (menu) {
            if (menu !== exceptMenu) setMenuState(menu, false, false);
        });
        syncHeaderState();
    }

    menus.forEach(function (menu) {
        var toggle = menu.querySelector('[data-nav-mega-toggle]');
        var panel = menu.querySelector('[data-nav-mega-panel]');

        if (!toggle || !panel) return;

        var videoFrame = panel.querySelector('[data-nav-mega-video]');
        if (videoFrame) {
            videoFrame.addEventListener('load', function () {
                var media = videoFrame.closest('.nav-mega__media');

                window.setTimeout(function () {
                    if (media) media.classList.add('is-video-ready');
                }, 720);

                if (menu.classList.contains('is-open')) {
                    postMegaVideoCommand(videoFrame, 'playVideo');
                }
            });
        }

        setMenuState(menu, false, false);

        toggle.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var shouldOpen = !menu.classList.contains('is-open');
            closeMenus(menu);
            setMenuState(menu, shouldOpen, false);
            syncHeaderState();

            var languageMenu = document.querySelector('.nav-language.is-open');
            if (languageMenu) {
                languageMenu.classList.remove('is-open');
                var languageButton = languageMenu.querySelector('.nav-language__button');
                if (languageButton) languageButton.setAttribute('aria-expanded', 'false');
            }
        });

        toggle.addEventListener('keydown', function (event) {
            if (event.key !== 'ArrowDown') return;

            event.preventDefault();
            closeMenus(menu);
            setMenuState(menu, true, true);
            syncHeaderState();
        });

        panel.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;

            event.preventDefault();
            event.stopPropagation();
            setMenuState(menu, false, false);
            syncHeaderState();
            toggle.focus();
        });

        panel.querySelectorAll('a[href]').forEach(function (link) {
            link.addEventListener('click', function () {
                setMenuState(menu, false, false);
                syncHeaderState();
            });
        });
    });

    document.addEventListener('click', function (event) {
        if (event.target.closest('[data-nav-mega]')) return;
        closeMenus();
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.nav-language__button')) return;
        closeMenus();
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        var openMenu = menus.find(function (menu) {
            return menu.classList.contains('is-open');
        });

        if (!openMenu) return;

        var toggle = openMenu.querySelector('[data-nav-mega-toggle]');
        closeMenus();
        if (toggle) toggle.focus();
    });
}

function initHeroSlider(root) {
    var slides = Array.prototype.slice.call(root.querySelectorAll('[data-hero-slide]'));
    var dots = Array.prototype.slice.call(root.querySelectorAll('[data-hero-dot]'));
    var previousButton = root.querySelector('[data-hero-previous]');
    var nextButton = root.querySelector('[data-hero-next]');
    var playbackButton = root.querySelector('[data-hero-playback]');
    var currentLabel = root.querySelector('[data-hero-current]');
    var liveRegion = root.querySelector('[data-hero-live]');
    var progressBar = root.querySelector('[data-hero-progress]');
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var statusTemplate = root.getAttribute('data-slide-label') || 'Slide :current of :total';
    var duration = parseInt(root.getAttribute('data-autoplay-interval'), 10);
    var currentIndex = Math.max(0, slides.findIndex(function (slide) {
        return slide.classList.contains('is-active');
    }));
    var timer = null;
    var transitionTimer = null;
    var transitionDuration = 1060;
    var userPaused = false;
    var pointerStart = null;
    var hasPresentedInitialSlide = false;

    if (!slides.length) return;
    if (!Number.isFinite(duration) || duration < 4000) duration = 7000;

    root.style.setProperty('--hero-autoplay-duration', duration + 'ms');
    root.setAttribute('data-enhanced', 'true');

    function formatStatus(index) {
        return statusTemplate
            .replace(':current', String(index + 1))
            .replace(':total', String(slides.length));
    }

    function hydrateSlide(slide, allowVideo) {
        if (!slide) return;

        slide.querySelectorAll('img[data-src]').forEach(function (image) {
            image.src = image.getAttribute('data-src');
            image.removeAttribute('data-src');
        });

        if (!allowVideo) return;

        var video = slide.querySelector('[data-hero-video]');
        if (!video || video.getAttribute('data-hydrated') === 'true') return;

        var hydratedSource = false;
        video.querySelectorAll('source[data-src]').forEach(function (source) {
            source.src = source.getAttribute('data-src');
            source.removeAttribute('data-src');
            hydratedSource = true;
        });

        if (hydratedSource) {
            video.setAttribute('data-hydrated', 'true');
            video.preload = 'metadata';
            video.load();
        }
    }

    function syncVideos() {
        slides.forEach(function (slide, index) {
            var video = slide.querySelector('[data-hero-video]');
            if (!video) return;

            if (index !== currentIndex) {
                video.pause();
                try { video.currentTime = 0; } catch (error) { /* Metadata may not exist yet. */ }
                return;
            }

            hydrateSlide(slide, true);

            if (reducedMotion.matches || document.hidden) {
                video.pause();
                return;
            }

            var playAttempt = video.play();
            if (playAttempt && typeof playAttempt.catch === 'function') {
                playAttempt.catch(function () {
                    slide.classList.add('has-video-playback-fallback');
                });
            }
        });
    }

    function canAutoplay() {
        return slides.length > 1 && !userPaused && !reducedMotion.matches && !document.hidden;
    }

    function resetProgress() {
        root.classList.remove('is-autoplaying');

        if (progressBar) {
            progressBar.style.animation = 'none';
            void progressBar.offsetWidth;
            progressBar.style.animation = '';
        }

        if (canAutoplay()) root.classList.add('is-autoplaying');
    }

    function clearTimer() {
        if (timer !== null) {
            window.clearTimeout(timer);
            timer = null;
        }
    }

    function clearTransition() {
        if (transitionTimer !== null) {
            window.clearTimeout(transitionTimer);
            transitionTimer = null;
        }

        slides.forEach(function (slide) {
            slide.classList.remove('is-entering', 'is-leaving');
        });
    }

    function scheduleNext() {
        clearTimer();
        resetProgress();

        if (!canAutoplay()) return;

        timer = window.setTimeout(function () {
            showSlide(currentIndex + 1, false);
        }, duration);
    }

    function updatePlaybackButton() {
        if (!playbackButton) return;

        var isPaused = userPaused || reducedMotion.matches;
        playbackButton.classList.toggle('is-paused', isPaused);
        playbackButton.setAttribute('aria-pressed', isPaused ? 'true' : 'false');
        playbackButton.setAttribute(
            'aria-label',
            isPaused
                ? (playbackButton.getAttribute('data-play-label') || 'Play slideshow')
                : (playbackButton.getAttribute('data-pause-label') || 'Pause slideshow')
        );
    }

    function showSlide(requestedIndex, announce) {
        var nextIndex = (requestedIndex + slides.length) % slides.length;
        var previousIndex = currentIndex;
        var shouldAnimate = hasPresentedInitialSlide &&
            nextIndex !== previousIndex &&
            !reducedMotion.matches;

        clearTransition();
        currentIndex = nextIndex;

        slides.forEach(function (slide, index) {
            var isActive = index === currentIndex;

            slide.classList.toggle('is-active', isActive);
            slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            slide.inert = !isActive;

            if (shouldAnimate && index === previousIndex) {
                slide.classList.add('is-leaving');
            }

            if (shouldAnimate && isActive) {
                slide.classList.add('is-entering');
            }
        });

        if (shouldAnimate) {
            transitionTimer = window.setTimeout(function () {
                slides.forEach(function (slide) {
                    slide.classList.remove('is-entering', 'is-leaving');
                });
                transitionTimer = null;
            }, transitionDuration);
        }

        hasPresentedInitialSlide = true;

        dots.forEach(function (dot, index) {
            var isActive = index === currentIndex;
            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
            dot.tabIndex = isActive ? 0 : -1;
        });

        if (currentLabel) {
            currentLabel.textContent = String(currentIndex + 1).padStart(2, '0');
        }

        hydrateSlide(slides[currentIndex], true);

        var nextSlide = slides[(currentIndex + 1) % slides.length];
        if (nextSlide && !nextSlide.querySelector('[data-hero-video]')) {
            hydrateSlide(nextSlide, false);
        }

        syncVideos();

        if (announce && liveRegion) {
            liveRegion.textContent = formatStatus(currentIndex) + ': ' +
                (slides[currentIndex].getAttribute('data-slide-title') || '');
        }

        scheduleNext();
    }

    if (previousButton) {
        previousButton.addEventListener('click', function () {
            showSlide(currentIndex - 1, true);
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', function () {
            showSlide(currentIndex + 1, true);
        });
    }

    dots.forEach(function (dot, index) {
        dot.addEventListener('click', function () {
            showSlide(index, true);
        });
    });

    if (playbackButton) {
        playbackButton.addEventListener('click', function () {
            userPaused = !userPaused;
            updatePlaybackButton();
            scheduleNext();
        });
    }

    root.addEventListener('keydown', function (event) {
        if (event.altKey || event.ctrlKey || event.metaKey) return;

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            showSlide(currentIndex - 1, true);
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            showSlide(currentIndex + 1, true);
        }
    });

    root.addEventListener('pointerdown', function (event) {
        if (event.pointerType === 'mouse' || event.target.closest('a, button, form')) return;
        pointerStart = { x: event.clientX, y: event.clientY, id: event.pointerId };
    }, { passive: true });

    root.addEventListener('pointerup', function (event) {
        if (!pointerStart || pointerStart.id !== event.pointerId) return;

        var deltaX = event.clientX - pointerStart.x;
        var deltaY = event.clientY - pointerStart.y;
        pointerStart = null;

        if (Math.abs(deltaX) < 48 || Math.abs(deltaX) <= Math.abs(deltaY)) return;
        showSlide(currentIndex + (deltaX < 0 ? 1 : -1), true);
    }, { passive: true });

    root.addEventListener('pointercancel', function () {
        pointerStart = null;
    }, { passive: true });

    function handleVisibilityChange() {
        syncVideos();
        scheduleNext();
    }

    document.addEventListener('visibilitychange', handleVisibilityChange);

    var removeMotionListener = onMediaQueryChange(reducedMotion, function () {
        updatePlaybackButton();
        syncVideos();
        scheduleNext();
    });

    window.addEventListener('pagehide', function () {
        clearTimer();
        clearTransition();
        removeMotionListener();
        slides.forEach(function (slide) {
            var video = slide.querySelector('[data-hero-video]');
            if (video) video.pause();
        });
    }, { once: true });

    updatePlaybackButton();
    showSlide(currentIndex, false);
}

document.addEventListener('DOMContentLoaded', function () {
    initMegaMenus();

    document.querySelectorAll('[data-hero-slider]').forEach(function (root) {
        initHeroSlider(root);
    });
});
