document.addEventListener('DOMContentLoaded', function () {
  var programCards = Array.prototype.slice.call(document.querySelectorAll('[data-featured-program-card]'));

  if (!programCards.length) return;

  function activateProgramCard(activeCard) {
    programCards.forEach(function (card) {
      var isActive = card === activeCard;
      card.classList.toggle('is-active', isActive);
      card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
  }

  programCards.forEach(function (card) {
    card.addEventListener('click', function () {
      activateProgramCard(card);
    });

    card.addEventListener('focus', function () {
      activateProgramCard(card);
    });

    card.addEventListener('mouseenter', function () {
      if (window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        activateProgramCard(card);
      }
    });
  });
});
