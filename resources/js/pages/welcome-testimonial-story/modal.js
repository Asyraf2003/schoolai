import { copyForLocale, escapeAttribute } from './core.js';

export function createModalActions(state) {
        function ensureModal() {
            if (state.modal) return;

            var copy = copyForLocale();
            var modal = document.createElement('div');
            modal.className = 'testimonial-media-modal';
            modal.setAttribute('role', 'dialog');
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('aria-label', copy.title);
            modal.hidden = true;
            modal.innerHTML = [
                '<button type="button" class="testimonial-media-modal__backdrop" data-testimonial-modal-close aria-label="' + escapeAttribute(copy.close) + '"></button>',
                '<article class="testimonial-media-modal__panel">',
                '  <button type="button" class="testimonial-media-modal__close" data-testimonial-modal-close aria-label="' + escapeAttribute(copy.close) + '">' + copy.close + '</button>',
                '  <div class="testimonial-media-modal__media" data-testimonial-modal-media></div>',
                '</article>'
            ].join('');

            document.body.appendChild(modal);
            state.modal = modal;
            state.modalMedia = modal.querySelector('[data-testimonial-modal-media]');

            Array.prototype.slice.call(modal.querySelectorAll('[data-testimonial-modal-close]')).forEach(function (button) {
                button.addEventListener('click', closeModal);
            });
        }

        function clearModalMedia() {
            if (!state.modalMedia) return;
            state.modalMedia.replaceChildren();
        }

        function closeModal() {
            if (!state.modal || state.modal.hidden) return;

            state.modal.hidden = true;
            clearModalMedia();
            document.body.style.overflow = state.previousBodyOverflow;

            if (state.lastFocused && typeof state.lastFocused.focus === 'function') {
                state.lastFocused.focus();
            }
        }

        function openModal(trigger) {
            var type = trigger.getAttribute('data-testimonial-media-type') || 'photo';
            var source = trigger.getAttribute('data-testimonial-media-source') || 'upload';
            var url = trigger.getAttribute('data-testimonial-media-url') || '';

            if (!url) return;

            ensureModal();
            clearModalMedia();
            state.lastFocused = document.activeElement;
            state.previousBodyOverflow = document.body.style.overflow;

            if (type === 'video' && source === 'embed') {
                var iframe = document.createElement('iframe');
                iframe.src = url;
                iframe.title = copyForLocale().openVideo;
                iframe.allow = 'autoplay; encrypted-media; fullscreen; picture-in-picture; web-share';
                iframe.allowFullscreen = true;
                iframe.referrerPolicy = 'strict-origin-when-cross-origin';
                iframe.style.width = '100%';
                iframe.style.height = 'min(76svh, 760px)';
                iframe.style.border = '0';
                state.modalMedia.appendChild(iframe);
            } else if (type === 'video') {
                var video = document.createElement('video');
                video.src = url;
                video.controls = true;
                video.autoplay = true;
                video.playsInline = true;
                video.setAttribute('playsinline', '');
                video.setAttribute('webkit-playsinline', '');
                state.modalMedia.appendChild(video);
            } else {
                var image = document.createElement('img');
                image.src = url;
                image.alt = trigger.getAttribute('aria-label') || '';
                image.decoding = 'async';
                state.modalMedia.appendChild(image);
            }

            state.modal.hidden = false;
            document.body.style.overflow = 'hidden';

            var closeButton = state.modal.querySelector('.testimonial-media-modal__close');
            if (closeButton) closeButton.focus();
        }

    return { closeModal, openModal };
}
