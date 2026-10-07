import { inflateSync } from 'node:zlib';

export const headingInk = () => {
    const heading = document.querySelector('[data-program-heading]');
    const lines = [...heading.querySelectorAll('[data-program-heading-line]')].map(line => {
        const css = getComputedStyle(line);
        const probe = document.createElement('i');
        probe.style.cssText = 'display:inline-block;width:0;height:0;padding:0;margin:0;vertical-align:baseline';
        line.append(probe);
        const baseline = probe.getBoundingClientRect().top;
        probe.remove();
        const context = document.createElement('canvas').getContext('2d');
        context.font = `${css.fontWeight} ${css.fontSize} ${css.fontFamily}`;
        const ink = context.measureText(css.textTransform === 'uppercase' ? line.textContent.toUpperCase() : line.textContent);
        const mask = line.parentElement.getBoundingClientRect();
        return { top: baseline - ink.actualBoundingBoxAscent, bottom: baseline + ink.actualBoundingBoxDescent,
            ascent: ink.actualBoundingBoxAscent, descent: ink.actualBoundingBoxDescent, maskTop: mask.top, maskBottom: mask.bottom,
            size: parseFloat(css.fontSize), weight: css.fontWeight, family: css.fontFamily,
            leading: css.lineHeight, duration: css.transitionDuration,
            shiftDuration: getComputedStyle(line.parentElement).transitionDuration,
            shiftDelay: getComputedStyle(line.parentElement).transitionDelay };
    });
    return { language: document.documentElement.lang, width: innerWidth, lines,
        gap: lines.length === 2 ? lines[1].top - lines[0].bottom : null,
        gridGap: getComputedStyle(heading).rowGap,
        maskPadding: getComputedStyle(heading.firstElementChild).paddingBlockStart,
        overflow: document.documentElement.scrollWidth > innerWidth };
};

export const menuState = () => {
    const header = document.querySelector('[data-header]');
    const panel = header.querySelector('[data-panel][open] .site-header__panel');
    const h = header.getBoundingClientRect();
    const p = panel?.getBoundingClientRect();
    const field = getComputedStyle(header, '::before');
    return { surface: header.dataset.surface, open: header.dataset.open, concealed: header.dataset.concealed,
        top: h.top, bottom: h.bottom, height: h.height, logo: header.querySelector('img').getBoundingClientRect().height,
        panelTop: p?.top ?? null, panelWidth: p?.width ?? null, panelFill: panel ? getComputedStyle(panel).backgroundColor : null,
        headerFill: field.backgroundColor, headerImage: field.backgroundImage,
        fieldTransform: field.transform, panelTransform: panel ? getComputedStyle(panel).transform : null,
        colors: [...header.querySelectorAll('.site-header__items > li > a, .site-header__group > summary, .site-header__language > summary')]
            .map(element => getComputedStyle(element).color) };
};

export const startMenuFrames = () => {
    window.menuProofFrames = [];
    window.menuProofRunning = true;
    const tick = () => {
        if (!window.menuProofRunning) return;
        const header = document.querySelector('[data-header]');
        const panel = header.querySelector('[data-panel][open] .site-header__panel');
        const field = getComputedStyle(header, '::before');
        window.menuProofFrames.push({ color: getComputedStyle(header).color,
            fill: field.backgroundColor, image: field.backgroundImage,
            bottom: header.getBoundingClientRect().bottom, top: header.getBoundingClientRect().top,
            panelTop: panel?.getBoundingClientRect().top ?? null,
            panelTransform: panel ? getComputedStyle(panel).transform : null,
            fieldTransform: field.transform });
        requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
};

// Read real screenshot pixels without adding a repository image dependency.
export function whitePixels(png) {
    const width = png.readUInt32BE(16), height = png.readUInt32BE(20);
    const channels = png[25] === 6 ? 4 : 3;
    const parts = [];
    for (let offset = 8; offset < png.length;) {
        const length = png.readUInt32BE(offset);
        if (png.toString('ascii', offset + 4, offset + 8) === 'IDAT') parts.push(png.subarray(offset + 8, offset + 8 + length));
        offset += length + 12;
    }
    const data = inflateSync(Buffer.concat(parts));
    const stride = width * channels;
    let prior = Buffer.alloc(stride), white = 0;
    const paeth = (a, b, c) => {
        const p = a + b - c, x = Math.abs(p - a), y = Math.abs(p - b), z = Math.abs(p - c);
        return x <= y && x <= z ? a : y <= z ? b : c;
    };
    for (let y = 0; y < height; y++) {
        const filter = data[y * (stride + 1)];
        const row = Buffer.alloc(stride);
        for (let i = 0; i < stride; i++) {
            const a = i >= channels ? row[i - channels] : 0, b = prior[i], c = i >= channels ? prior[i - channels] : 0;
            row[i] = (data[y * (stride + 1) + i + 1] + [0, a, b, Math.floor((a + b) / 2), paeth(a, b, c)][filter]) & 255;
        }
        for (let i = 0; i < stride; i += channels) if (row[i] === 255 && row[i + 1] === 255 && row[i + 2] === 255 && (channels === 3 || row[i + 3] === 255)) white++;
        prior = row;
    }
    return { white, total: width * height, width, height };
}
