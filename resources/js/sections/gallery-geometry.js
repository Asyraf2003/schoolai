export function galleryGeometry(width, height, rtl = false) {
    const mobile = width < 768;
    const gap = Math.max(12, Math.min(width * .022, 32));
    const col = mobile ? (width * .9 - gap) / 2 : Math.min(width * .275, 480);
    const openingScale = Math.min(1, height * .72 / (width * .45));
    const center = (width - col) / 2;
    const locations = [[1, 0], [0, 0], [0, 1], [0, 2], [1, 1], [1, 2], [1, 3], [2, 0], [2, 1], [2, 2]];
    return locations.map(([column, row], index) => {
        const side = column !== 1;
        const ratio = side ? .75 + gap / (3 * col) : .5625;
        let x = center + (column - 1) * (col + gap);
        let y = height * .12 + row * (col * ratio + gap) + (side ? col * .25 : 0);
        if (mobile) {
            column = index % 2;
            row = Math.floor(index / 2);
            x = width * .05 + column * (col + gap);
            y = height * .18 + row * (col * .75 + gap);
        }
        if (rtl) x = width - col - x;
        return { x, y, width: col, height: col * ratio, scale: col / (width * .8),
            openingScale, fromX: index === 0 ? (width - width * .8 * openingScale) / 2 : x + (column === 0 ? -width : column === 2 || mobile ? width : 0) * (rtl ? -1 : 1),
            fromY: index === 0 ? Math.max(height * .22, (height - width * .45 * openingScale) / 2) : height + row * col * .25,
            row: locations[index][1], column: locations[index][0] === 0 ? 'left' : locations[index][0] === 2 ? 'right' : 'center' };
    });
}
