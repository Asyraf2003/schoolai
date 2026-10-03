  <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    (() => {
      const modal = document.querySelector('[data-admin-delete-modal]');
      if (!modal) return;

      const messageEl = modal.querySelector('[data-admin-delete-modal-message]');
      const confirmButton = modal.querySelector('[data-admin-delete-confirm]');
      const cancelButtons = Array.from(modal.querySelectorAll('[data-admin-delete-cancel]'));
      const deleteForms = Array.from(document.querySelectorAll('[data-admin-delete-form]'));

      let pendingForm = null;
      let lastFocused = null;

      function openDeleteModal(form) {
        pendingForm = form;
        lastFocused = document.activeElement;

        if (messageEl) {
          messageEl.textContent = form.getAttribute('data-admin-delete-message') || 'Data yang dihapus tidak bisa dikembalikan.';
        }

        modal.hidden = false;

        window.requestAnimationFrame(() => {
          if (confirmButton) confirmButton.focus();
        });
      }

      function closeDeleteModal() {
        modal.hidden = true;
        pendingForm = null;

        if (lastFocused && typeof lastFocused.focus === 'function') {
          lastFocused.focus();
        }

        lastFocused = null;
      }

      deleteForms.forEach((form) => {
        const trigger = form.querySelector('[data-admin-delete-trigger]') || form.querySelector('button');

        if (trigger) {
          trigger.type = 'button';
          trigger.setAttribute('data-admin-delete-trigger', '');
          trigger.addEventListener('click', () => openDeleteModal(form));
        }

        form.addEventListener('submit', (event) => {
          if (form.getAttribute('data-admin-delete-confirmed') === '1') {
            return;
          }

          event.preventDefault();
          openDeleteModal(form);
        });
      });

      cancelButtons.forEach((button) => {
        button.addEventListener('click', closeDeleteModal);
      });

      if (confirmButton) {
        confirmButton.addEventListener('click', () => {
          if (!pendingForm) return;

          pendingForm.setAttribute('data-admin-delete-confirmed', '1');

          if (typeof pendingForm.requestSubmit === 'function') {
            pendingForm.requestSubmit();
            return;
          }

          pendingForm.submit();
        });
      }

      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
          closeDeleteModal();
        }
      });
    })();
  </script>
