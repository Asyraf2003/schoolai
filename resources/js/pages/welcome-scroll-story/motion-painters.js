export const clamp = (value, min = 0, max = 1) => Math.min(max, Math.max(min, value));

const seeded = (index, salt = 1) => {
    const value = Math.sin((index + 1) * 12.9898 + salt * 78.233) * 43758.5453;
    return value - Math.floor(value);
};

function sequentialProgress(progress, index, total, spread) {
    const start = total > 1 ? (index / (total - 1)) * spread : 0;
    return clamp((progress - start) / Math.max(.12, 1 - spread));
}

function effectProgress(effect, progress, index, total) {
    if (effect === 'effect22') return sequentialProgress(progress, index, total, .18);
    if (effect === 'effect23') return sequentialProgress(progress, index, total, .5);
    if (effect === 'effect27') {
        const start = seeded(index, 27) * .68;
        return clamp((progress - start) / .32);
    }
    if (effect === 'effect28') {
        const middle = Math.max(1, (total - 1) / 2);
        const distance = Math.abs(index - middle) / middle;
        const start = distance * .32;
        return clamp((progress - start) / .68);
    }
    return sequentialProgress(progress, index, total, total > 24 ? .84 : .72);
}

function effect22(unit, progress, index, total, rtl, languageScale) {
    const inverse = 1 - progress;
    const middle = Math.max(1, (total - 1) / 2);
    const signed = (index - middle) / middle;
    const side = Math.sign(signed || 1) * (rtl ? -1 : 1);
    const distance = Math.abs(signed);
    const x = side * inverse * (1.4 + distance * 5.2) * languageScale;
    const y = inverse * (2.4 - distance * 1.4) * languageScale;
    const rotateY = -270 * inverse * side;
    const rotateZ = side * distance * 16 * inverse;

    unit.style.opacity = String(.08 + progress * .92);
    unit.style.filter = 'none';
    unit.style.transform = `perspective(1000px) translate3d(${x}em, ${y}em, 0) rotateY(${rotateY}deg) rotateZ(${rotateZ}deg)`;
}

function effect23(unit, progress, index, rtl, languageScale) {
    const inverse = 1 - progress;
    const group = Math.floor(index / 5);
    const side = (group % 2 ? 1 : -1) * (rtl ? -1 : 1);
    const distance = ((index % 5) + 1) * .55 * languageScale;
    const scale = Math.max(.01, .01 + progress * .99);

    unit.style.opacity = String(.04 + progress * .96);
    unit.style.filter = 'none';
    unit.style.transform = `translate3d(${side * inverse * distance}em, 0, 0) scale(${scale})`;
}

function effect27(unit, progress, index, rtl, languageScale) {
    const inverse = 1 - progress;
    const direction = rtl ? -1 : 1;
    const x = (seeded(index, 3) * 200 - 100) * inverse * direction * languageScale;
    const y = (seeded(index, 7) * 20 - 10) * inverse * languageScale;
    const z = (500 + seeded(index, 11) * 450) * inverse;
    const rotateX = (seeded(index, 17) * 180 - 90) * inverse;

    unit.style.opacity = String(progress);
    unit.style.filter = 'none';
    unit.style.transform = `perspective(1000px) translate3d(${x}%, ${y}%, ${z}px) rotateX(${rotateX}deg)`;
}

function effect28(unit, progress, index, total, rtl, languageScale) {
    const inverse = 1 - progress;
    const middle = Math.max(1, (total - 1) / 2);
    const signed = (index - middle) / middle;
    const center = 1 - Math.min(1, Math.abs(signed));
    const startScale = .5 + center * 1.6;
    const scale = 1 + (startScale - 1) * inverse;
    const y = center * 60 * inverse * languageScale;
    const rotate = signed * 4 * inverse * (rtl ? -1 : 1);

    unit.style.opacity = String(progress);
    unit.style.filter = `blur(${inverse * 12}px)`;
    unit.style.transform = `translate3d(0, ${y}px, 0) rotate(${rotate}deg) scale(${scale})`;
}

function effect25(unit, progress) {
    unit.style.opacity = String(.04 + progress * .96);
    unit.style.filter = 'none';
    unit.style.transform = `scaleY(${Math.max(.001, progress)})`;
}

function paintUnit(unit, item, progress, index, total, rtl) {
    const languageScale = item.arabic ? .68 : 1;

    if (item.effect === 'effect22') {
        effect22(unit, progress, index, total, rtl, languageScale);
    } else if (item.effect === 'effect23') {
        effect23(unit, progress, index, rtl, languageScale);
    } else if (item.effect === 'effect27') {
        effect27(unit, progress, index, rtl, languageScale);
    } else if (item.effect === 'effect28') {
        effect28(unit, progress, index, total, rtl, languageScale);
    } else {
        effect25(unit, progress);
    }
}

export function paintText(item, progress, rtl) {
    if (Math.abs((item.lastProgress ?? -1) - progress) < .0005) return;
    item.lastProgress = progress;
    const total = item.units.length;

    item.units.forEach((unit, index) => {
        const value = effectProgress(item.effect, progress, index, total);
        paintUnit(unit, item, value, index, total, rtl);
    });
}

function visionTransform(progress, depth, index, width, height, rtl) {
    const centered = progress - .5;
    const side = index % 2 ? -1 : 1;
    const x = centered * width * (.012 + depth * .01) * side * (rtl ? -1 : 1);
    const y = centered * height * (.018 + depth * .008) * (index < 2 ? 1 : -1);
    const rotate = centered * (depth % 2 ? 8 : -7);
    const scale = 1 + Math.abs(centered) * .055 + depth * .008;
    return `translate3d(${x}px, ${y}px, 0) rotate(${rotate}deg) scale(${scale})`;
}

function missionTransform(motion, progress, depth, width, height, rtl) {
    const centered = progress - .5;
    const direction = rtl ? -1 : 1;

    if (motion === 'orbit') {
        const angle = centered * Math.PI * 1.35;
        const x = Math.cos(angle) * width * .075 * direction;
        const y = Math.sin(angle) * height * .075;
        return `translate3d(${x}px, calc(-50% + ${y}px), 0) rotate(${centered * 68}deg) scale(${.92 + progress * .12})`;
    }
    if (motion === 'sweep') {
        const x = centered * width * .38 * direction;
        const y = Math.sin(progress * Math.PI) * height * .045;
        return `translate3d(${x}px, calc(-50% + ${y}px), 0) rotate(${centered * -16}deg)`;
    }
    if (motion === 'zoom') {
        const y = -centered * height * .1;
        return `translate3d(0, calc(-50% + ${y}px), 0) rotate(${centered * 9}deg) scale(${.72 + progress * .48})`;
    }

    const x = centered * width * .12 * direction;
    return `perspective(950px) translate3d(${x}px, -50%, 0) rotateY(${centered * 72 * direction}deg)`;
}

export function paintScene(scene, progress, rtl, reduced) {
    const transition = reduced ? 0 : clamp((progress - .78) / .22);
    scene.element.style.setProperty('--scene-progress', progress.toFixed(4));
    scene.element.style.setProperty('--scene-transition', transition.toFixed(4));
    if (reduced) return;

    const width = window.innerWidth || 1;
    const height = window.innerHeight || 1;

    scene.arts.forEach((art, index) => {
        const depth = Number(art.dataset.storyDepth || index + 1);
        art.style.transform = scene.motion === 'drift'
            ? visionTransform(progress, depth, index, width, height, rtl)
            : missionTransform(scene.motion, progress, depth, width, height, rtl);
    });
}

export function clearPaint(item) {
    delete item.lastProgress;
    item.units.forEach((unit) => {
        unit.style.removeProperty('opacity');
        unit.style.removeProperty('filter');
        unit.style.removeProperty('transform');
    });
}
