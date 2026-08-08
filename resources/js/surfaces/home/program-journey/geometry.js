export function collectProgramDom(root) {
  return {
    root,
    cardsWrap: root.querySelector('[data-program-cards]'),
    cards: [...root.querySelectorAll('[data-program-card]')],
    triggers: [...root.querySelectorAll('[data-program-open]')],
    type: root.querySelector('[data-program-type]'),
    typeLines: [...root.querySelectorAll('[data-program-type-line]')],
    layer: root.querySelector('[data-program-detail-layer]'),
    details: [...root.querySelectorAll('[data-program-detail]')],
    back: root.querySelector('[data-program-back]'),
  };
}

export function showDetail(dom, index) {
  dom.details.forEach((detail, position) => {
    detail.hidden = position !== index;
  });
  return dom.details[index] ?? null;
}

export function hideDetails(dom) {
  dom.details.forEach((detail) => {
    detail.hidden = true;
  });
}

export function detailParts(detail) {
  if (!detail) return null;
  return {
    copy: [...detail.querySelectorAll('.program-kinetic__detail-number, .program-kinetic__detail-eyebrow, .program-kinetic__detail-copy h3, .program-kinetic__detail-intro, .program-kinetic__detail-description, .program-kinetic__detail-next')],
    imageWrap: detail.querySelector('[data-program-detail-image-wrap]'),
    image: detail.querySelector('[data-program-detail-image]'),
  };
}
