const locations = [[1, 0], [0, 0], [0, 1], [0, 2], [1, 1], [1, 2], [1, 3], [2, 0], [2, 1], [2, 2]];
const rowsPerGroup = [3, 4, 3];
const basePassCount = 3 * locations.length;

function tilePlacement(index) {
    if (index < basePassCount) {
        const [column, initialRow] = locations[index % locations.length];
        return [column, initialRow + Math.floor(index / locations.length) * rowsPerGroup[column]];
    }
    const extra = index - basePassCount;
    return [extra % 2 === 0 ? 0 : 2, 9 + Math.floor(extra / 2)];
}

export function galleryGeometry(width, height, rtl = false, count = locations.length) {
    const gap = Math.max(6, Math.min(width * .011, 16));
    const track = Math.min(width, height * 3.2);
    const available = track - gap * 2;
    const sideWidth = available * .3;
    const centerWidth = available * .4;
    const inset = (width - track) / 2;
    const baseWidth = width * 26 / 30;
    const openingScale = Math.min(1, height * .84 / (baseWidth * .5625));
    const openingWidth = baseWidth * openingScale;
    const openingX = (width - openingWidth) / 2;
    const openingY = gap;
    const openingBottom = openingY + openingWidth * .5625;
    const center = (width - centerWidth) / 2;
    return Array.from({ length: count }, (_, index) => {
        const [column, row] = tilePlacement(index);
        const side = column !== 1;
        const tileWidth = side ? sideWidth : centerWidth;
        const tileHeight = side ? sideWidth * .75 + gap / 3 : centerWidth * .5625;
        let x = side ? inset + (column === 0 ? 0 : track - sideWidth) : center;
        const middleY = (height - centerWidth * .5625) / 2;
        const y = middleY + row * (tileHeight + gap) + (side ? sideWidth * .25 : 0);
        let fromX = side ? x + (column - 1) * width * .25 : center;
        const fromY = openingBottom + height * (side ? .95 : 1.1) + (side ? row : row - 1) * (tileHeight + gap);
        if (rtl) { x = width - tileWidth - x; fromX = width - tileWidth - fromX; }
        return { x, y, width: tileWidth, height: tileHeight, scale: tileWidth / baseWidth,
            openingScale, fromX: index === 0 ? openingX : fromX,
            fromY: index === 0 ? openingY : fromY,
            row, column: column === 0 ? 'left' : column === 2 ? 'right' : 'center' };
    });
}
