    .admin-toast-stack {
      position: fixed;
      top: 24px;
      right: 24px;
      z-index: 2000;
      display: grid;
      gap: 10px;
      width: min(380px, calc(100vw - 32px));
      pointer-events: none;
    }

    body.admin-desktop-body .flash-message,
    body.admin-desktop-body .admin-error-box {
      position: fixed;
      top: 24px;
      right: 24px;
      z-index: 2000;
      width: min(380px, calc(100vw - 32px));
      margin: 0;
      padding: 14px 44px 14px 16px;
      border-radius: 16px;
      font-weight: 800;
      line-height: 1.45;
      box-shadow: 0 18px 42px rgba(32, 34, 63, 0.18);
      pointer-events: auto;
    }

    .admin-toast-stack .flash-message,
    .admin-toast-stack .admin-error-box,
    .admin-toast {
      position: relative;
      inset: auto;
      width: 100%;
      margin: 0;
      transform-origin: top right;
      animation: admin-toast-in 0.24s ease-out both;
    }

    .admin-toast--success,
    .flash-message {
      border: 1px solid rgba(22, 163, 74, 0.18);
      background: #ecfdf5;
      color: #166534;
    }

    .admin-toast--error,
    .admin-error-box {
      border: 1px solid rgba(185, 28, 28, 0.18);
      background: #fef2f2;
      color: #b91c1c;
    }

    .admin-toast p,
    .admin-error-box p {
      margin: 0;
    }

    .admin-toast p + p,
    .admin-error-box p + p {
      margin-top: 4px;
    }

    .admin-toast__close {
      position: absolute;
      top: 9px;
      right: 10px;
      width: 26px;
      height: 26px;
      display: grid;
      place-items: center;
      border: 0;
      border-radius: 999px;
      background: rgba(32, 34, 63, 0.08);
      color: currentColor;
      font: inherit;
      font-size: 1rem;
      font-weight: 900;
      line-height: 1;
      cursor: pointer;
    }

    .admin-toast__close:hover {
      background: rgba(32, 34, 63, 0.14);
    }

    .admin-toast.is-hiding {
      animation: admin-toast-out 0.22s ease-in both;
    }

    @keyframes admin-toast-in {
      from {
        opacity: 0;
        transform: translateY(-10px) translateX(10px) scale(0.98);
      }

      to {
        opacity: 1;
        transform: translateY(0) translateX(0) scale(1);
      }
    }

    @keyframes admin-toast-out {
      from {
        opacity: 1;
        transform: translateY(0) translateX(0) scale(1);
      }

      to {
        opacity: 0;
        transform: translateY(-8px) translateX(10px) scale(0.98);
      }
    }

    @media (max-width: 640px) {
      .admin-toast-stack,
      body.admin-desktop-body .flash-message,
      body.admin-desktop-body .admin-error-box {
        top: 14px;
        right: 14px;
        left: 14px;
        width: auto;
      }
    }

    body.admin-desktop-body {
      background: #f6f7f9;
      color: #111827;
    }
