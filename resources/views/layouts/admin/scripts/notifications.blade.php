  <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    (() => {
      const stack = document.querySelector('[data-admin-toast-stack]');
      if (!stack) return;

      const messages = Array.from(document.querySelectorAll('.flash-message, .admin-error-box'));

      function hideToast(toast) {
        if (!toast || toast.classList.contains('is-hiding')) return;

        toast.classList.add('is-hiding');

        window.setTimeout(() => {
          toast.remove();
        }, 240);
      }

      messages.forEach((toast, index) => {
        if (toast.closest('[data-admin-toast-stack]')) return;

        const isError = toast.classList.contains('admin-error-box');

        toast.classList.add('admin-toast', isError ? 'admin-toast--error' : 'admin-toast--success');
        toast.setAttribute('role', isError ? 'alert' : 'status');
        toast.setAttribute('data-admin-toast', '');

        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'admin-toast__close';
        closeButton.setAttribute('aria-label', 'Tutup notifikasi');
        closeButton.textContent = '×';
        closeButton.addEventListener('click', () => hideToast(toast));

        toast.appendChild(closeButton);
        stack.appendChild(toast);

        window.setTimeout(() => hideToast(toast), 3000 + (index * 120));
      });
    })();
  </script>
