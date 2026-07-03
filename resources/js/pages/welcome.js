import { on, qs } from '../core/dom.js';

// Page module welcome: semua interaksi homepage hidup hanya saat body[data-page='welcome'] aktif.
export function mount() {
    mountFooterYear();
    mountMobileNavigation();
    mountSmoothNavigation();
    mountScrollState();
    mountCounters();
    mountExtracurricularFilter();
    mountGalleryLightbox();
    mountRevealAnimation();
    mountHeroTilt();
}

function mountFooterYear() {
    const yearEl = qs('#currentYear');

    if (yearEl) {
        yearEl.textContent = new Date().getFullYear();
    }
}

function mountMobileNavigation() {
    const hamburgerBtn = qs('#hamburgerBtn');
    const navMenu = qs('#navMenu');
    const navOverlay = qs('#navOverlay');

    if (! hamburgerBtn || ! navMenu || ! navOverlay) {
        return;
    }

    const openMenu = () => {
        navMenu.classList.add('active');
        navOverlay.classList.add('active');
        hamburgerBtn.setAttribute('aria-expanded', 'true');
        hamburgerBtn.setAttribute('aria-label', 'Tutup menu');
        document.body.style.overflow = 'hidden';
    };

    const closeMenu = () => {
        navMenu.classList.remove('active');
        navOverlay.classList.remove('active');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
        hamburgerBtn.setAttribute('aria-label', 'Buka menu');
        document.body.style.overflow = '';
    };

    on(hamburgerBtn, 'click', () => {
        navMenu.classList.contains('active') ? closeMenu() : openMenu();
    });

    on(navOverlay, 'click', closeMenu);
    on(document, 'keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });

    navMenu.querySelectorAll('a').forEach((link) => {
        on(link, 'click', closeMenu);
    });
}

function mountSmoothNavigation() {
    const navbar = qs('#navbar');
    const navLinks = [...document.querySelectorAll('.nav-link')];

    if (! navbar || navLinks.length === 0) {
        return;
    }

    const sections = navLinks
        .map((link) => {
            const targetId = link.getAttribute('href');
            const section = targetId?.startsWith('#') ? qs(targetId) : null;

            return section ? { id: targetId, el: section, link } : null;
        })
        .filter(Boolean);

    navLinks.forEach((link) => {
        const targetId = link.getAttribute('href');
        const target = targetId?.startsWith('#') ? qs(targetId) : null;

        if (! target) {
            return;
        }

        on(link, 'click', (event) => {
            event.preventDefault();

            const navHeight = navbar.offsetHeight;
            const targetPos = target.getBoundingClientRect().top + window.scrollY - navHeight + 1;

            window.scrollTo({ top: targetPos, behavior: 'smooth' });
        });
    });

    const updateActiveNavLink = () => {
        const scrollPos = window.scrollY + navbar.offsetHeight + 40;
        let current = sections[0];

        sections.forEach((section) => {
            if (section.el.offsetTop <= scrollPos) {
                current = section;
            }
        });

        navLinks.forEach((link) => link.classList.remove('active'));
        current?.link.classList.add('active');
    };

    on(window, 'scroll', updateActiveNavLink, { passive: true });
    updateActiveNavLink();
}

function mountScrollState() {
    const navbar = qs('#navbar');
    const scrollTopBtn = qs('#scrollTopBtn');

    const update = () => {
        if (navbar) {
            navbar.classList.toggle('is-scrolled', window.scrollY > 40);
        }

        if (scrollTopBtn) {
            scrollTopBtn.hidden = window.scrollY <= 500;
        }
    };

    on(window, 'scroll', update, { passive: true });
    on(scrollTopBtn, 'click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    update();
}

function mountCounters() {
    const counters = [...document.querySelectorAll('[data-count]')];

    if (counters.length === 0) {
        return;
    }

    const animateCounter = (counter) => {
        const target = Number(counter.dataset.count || 0);
        const suffix = counter.dataset.suffix || '';
        const duration = 900;
        const start = performance.now();

        const step = (time) => {
            const progress = Math.min((time - start) / duration, 1);
            counter.textContent = `${Math.floor(target * progress)}${suffix}`;

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };

        requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries, currentObserver) => {
        entries.forEach((entry) => {
            if (! entry.isIntersecting) {
                return;
            }

            animateCounter(entry.target);
            currentObserver.unobserve(entry.target);
        });
    }, { threshold: 0.45 });

    counters.forEach((counter) => observer.observe(counter));
}

function mountExtracurricularFilter() {
    const buttons = [...document.querySelectorAll('[data-filter]')];
    const cards = [...document.querySelectorAll('[data-category]')];

    if (buttons.length === 0 || cards.length === 0) {
        return;
    }

    buttons.forEach((button) => {
        on(button, 'click', () => {
            const filter = button.dataset.filter;

            buttons.forEach((item) => item.classList.remove('active'));
            button.classList.add('active');

            cards.forEach((card) => {
                const shouldShow = filter === 'all' || card.dataset.category === filter;
                card.classList.toggle('is-hidden', ! shouldShow);
            });
        });
    });
}

function mountGalleryLightbox() {
    const lightbox = qs('#lightbox');
    const backdrop = qs('#lightboxBackdrop');
    const closeBtn = qs('#lightboxClose');
    const visual = qs('#lightboxVisual');
    const caption = qs('#lightboxCaption');
    const items = [...document.querySelectorAll('[data-gallery-item]')];

    if (! lightbox || items.length === 0) {
        return;
    }

    const close = () => {
        lightbox.hidden = true;
        document.body.style.overflow = '';
    };

    items.forEach((item) => {
        on(item, 'click', () => {
            if (visual) {
                visual.innerHTML = item.dataset.galleryVisual || item.innerHTML;
            }

            if (caption) {
                caption.textContent = item.dataset.galleryCaption || item.getAttribute('aria-label') || '';
            }

            lightbox.hidden = false;
            document.body.style.overflow = 'hidden';
        });
    });

    on(backdrop, 'click', close);
    on(closeBtn, 'click', close);
    on(document, 'keydown', (event) => {
        if (event.key === 'Escape' && ! lightbox.hidden) {
            close();
        }
    });
}

function mountRevealAnimation() {
    const revealItems = [...document.querySelectorAll('.reveal')];

    if (revealItems.length === 0) {
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
            }
        });
    }, { threshold: 0.16 });

    revealItems.forEach((item) => observer.observe(item));
}

function mountHeroTilt() {
    const target = qs('#tiltIllustration');

    if (! target) {
        return;
    }

    on(target, 'pointermove', (event) => {
        const rect = target.getBoundingClientRect();
        const x = (event.clientX - rect.left) / rect.width - 0.5;
        const y = (event.clientY - rect.top) / rect.height - 0.5;

        target.style.transform = `rotateX(${y * -5}deg) rotateY(${x * 6}deg)`;
    });

    on(target, 'pointerleave', () => {
        target.style.transform = '';
    });
}
