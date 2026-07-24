document.addEventListener('DOMContentLoaded', function () {
  var missionCards = Array.prototype.slice.call(document.querySelectorAll('[data-mission-card]'));

  if (!missionCards.length) return;

  function activateMissionCard(activeCard) {
    missionCards.forEach(function (card) {
      var isActive = card === activeCard;
      card.classList.toggle('is-active', isActive);
      card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
  }

  missionCards.forEach(function (card) {
    card.addEventListener('click', function () {
      activateMissionCard(card);
    });

    card.addEventListener('focus', function () {
      activateMissionCard(card);
    });

    card.addEventListener('mouseenter', function () {
      if (window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        activateMissionCard(card);
      }
    });
  });
});
