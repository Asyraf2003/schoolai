export function createProgramDialogIntegration(dom) {
  let trigger = null;

  const lock = (origin) => {
    trigger = origin;
    document.documentElement.classList.add('program-kinetic-dialog');
    document.body.classList.add('program-kinetic-dialog');
    if (dom.cardsWrap) dom.cardsWrap.inert = true;
  };

  const unlock = () => {
    document.documentElement.classList.remove('program-kinetic-dialog');
    document.body.classList.remove('program-kinetic-dialog');
    if (dom.cardsWrap) dom.cardsWrap.inert = false;
    trigger?.focus({ preventScroll: true });
    trigger = null;
  };

  const focusables = () => {
    if (!dom.layer || dom.layer.hidden) return [];
    return [...dom.layer.querySelectorAll('button:not([disabled]), a[href], [tabindex]:not([tabindex="-1"])')].filter((element) => !element.closest('[hidden]'));
  };

  const trapTab = (event) => {
    const items = focusables();
    if (!items.length) return;
    const first = items[0];
    const last = items[items.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  };

  return { lock, unlock, trapTab };
}
