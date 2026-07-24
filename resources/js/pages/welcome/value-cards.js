document.addEventListener('DOMContentLoaded', function () {
  var valueCards = Array.prototype.slice.call(document.querySelectorAll('[data-school-value-card]'));

  if (!valueCards.length) return;

  function activateValueCard(activeCard) {
    valueCards.forEach(function (card) {
      var isActive = card === activeCard;
      card.classList.toggle('is-active', isActive);
      card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
  }

  valueCards.forEach(function (card) {
    card.addEventListener('click', function () {
      activateValueCard(card);
    });

    card.addEventListener('focus', function () {
      activateValueCard(card);
    });

    card.addEventListener('mouseenter', function () {
      if (window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        activateValueCard(card);
      }
    });
  });
});
