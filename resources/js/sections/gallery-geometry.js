export function galleryGeometry(width, height, rtl = false) {
    const mobile = width < 768;
    const gap = Math.max(6, Math.min(width * .011, 16));
    const col = mobile ? (width * .9 - gap) / 2 : (width - gap * 2) / 3;
    const baseWidth = width * 26 / 30;
    const openingScale = Math.min(1, height * .84 / (baseWidth * .5625));
    const openingWidth = baseWidth * openingScale;
    const openingX = (width - openingWidth) / 2;
    const openingY = gap;
    const openingBottom = openingY + openingWidth * .5625;
    const center = (width - col) / 2;
    const locations = [[1, 0], [0, 0], [0, 1], [0, 2], [1, 1], [1, 2], [1, 3], [2, 0], [2, 1], [2, 2]];
    return locations.map(([column, row], index) => {
        const side = column !== 1;
        const ratio = side ? .75 + gap / (3 * col) : .5625;
        let x = center + (column - 1) * (col + gap);
        const middleY = (height - col * .5625) / 2;
        let y = middleY + row * (col * ratio + gap) + (side ? col * .25 : 0);
        if (mobile) {
            column = index % 2;
            row = Math.floor(index / 2);
            x = width * .05 + column * (col + gap);
            y = middleY + row * (col * .75 + gap);
        }
        let fromX = side ? x + (column - 1) * width * .25 : center;
        let fromY = openingBottom + height * (side ? .95 : 1.1) + (side ? row : row - 1) * (col * ratio + gap);
        if (mobile) { fromX = x; fromY = openingBottom + height + Math.floor((index - 1) / 2) * (col * .75 + gap); }
        if (rtl) { x = width - col - x; fromX = width - col - fromX; }
        x = Math.max(0, Math.min(width - col, x));
        return { x, y, width: col, height: col * ratio, scale: col / baseWidth,
            openingScale, fromX: index === 0 ? openingX : fromX,
            fromY: index === 0 ? openingY : fromY,
            row: locations[index][1], column: locations[index][0] === 0 ? 'left' : locations[index][0] === 2 ? 'right' : 'center' };
    });
}
