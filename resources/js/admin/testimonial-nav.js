export function installTestimonialAdminNav() {
    const nav = document.querySelector('.admin-side-nav');

    if (!nav || nav.querySelector('[data-admin-testimonial-link]')) {
        return;
    }

    const link = document.createElement('a');
    const isActive = window.location.pathname.startsWith('/admin/testimoni');

    link.href = '/admin/testimoni';
    link.className = 'admin-side-link' + (isActive ? ' is-active' : '');
    link.setAttribute('data-admin-testimonial-link', '');
    link.innerHTML = [
        '<span class="admin-side-link__icon" aria-hidden="true">💬</span>',
        '<span>Testimoni</span>'
    ].join('');

    const galleryLink = Array.from(nav.querySelectorAll('a')).find((item) => {
        try {
            return new URL(item.href, window.location.origin).pathname === '/admin/galeri';
        } catch (error) {
            return false;
        }
    });

    if (galleryLink) {
        galleryLink.insertAdjacentElement('afterend', link);
    } else {
        nav.appendChild(link);
    }
}
