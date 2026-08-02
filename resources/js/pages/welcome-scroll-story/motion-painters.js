export const clamp = (value, min = 0, max = 1) => Math.min(max, Math.max(min, value));

function unitProgress(progress, index, total) {
    const spread = total > 24 ? .84 : .72;
    const start = total > 1 ? (index / (total - 1)) * spread : 0;
    return clamp((progress - start) / Math.max(.16, 1 - spread));
}

function paintUnit(unit, effect, progress, index, rtl) {
    const p = clamp(progress);
    const inverse = 1 - p;
    const side = (index % 2 ? 1 : -1) * (rtl ? -1 : 1);
    let transform = `scaleY(${Math.max(.001, p)})`;
    let filter = 'none';

    if (effect === 'fan') {
        transform = `translate3d(${side * inverse * 1.25}em, ${inverse * .9}em, 0) rotate(${side * inverse * 16}deg)`;
    } else if (effect === 'perspective') {
        transform = `perspective(900px) translate3d(0, ${inverse * .8}em, 0) rotateX(${-inverse * 78}deg)`;
    } else if (effect === 'focus') {
        transform = `translate3d(0, ${inverse * .55}em, 0) scale(${.76 + p * .24})`;
        filter = `blur(${inverse * 11}px)`;
    } else if (effect === 'wave') {
        transform = `translate3d(0, ${side * inverse * .7}em, 0) rotate(${side * inverse * 11}deg) scaleY(${.72 + p * .28})`;
    }

    unit.style.opacity = String(.04 + p * .96);
    unit.style.filter = filter;
    unit.style.transform = transform;
}

export function paintText(item, progress, rtl) {
    if (Math.abs((item.lastProgress ?? -1) - progress) < .0005) return;
    item.lastProgress = progress;
    const total = item.units.length;

    item.units.forEach((unit, index) => {
        paintUnit(unit, item.effect, unitProgress(progress, index, total), index, rtl);
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
    const baseY = '-50%';

    if (motion === 'orbit') {
        const angle = centered * Math.PI * 1.35;
        const x = Math.cos(angle) * width * .075 * direction;
        const y = Math.sin(angle) * height * .075;
        return `translate3d(${x}px, calc(${baseY} + ${y}px), 0) rotate(${centered * 68}deg) scale(${.92 + progress * .12})`;
    }

    if (motion === 'sweep') {
        const x = centered * width * .38 * direction;
        const y = Math.sin(progress * Math.PI) * height * .045;
        return `translate3d(${x}px, calc(${baseY} + ${y}px), 0) rotate(${centered * -16}deg) scale(${1.04 - Math.abs(centered) * .08})`;
    }

    if (motion === 'zoom') {
        const y = -centered * height * .1;
        return `translate3d(0, calc(${baseY} + ${y}px), 0) rotate(${centered * 9}deg) scale(${.72 + progress * .48})`;
    }

    const x = centered * width * .12 * direction;
    return `perspective(950px) translate3d(${x}px, ${baseY}, 0) rotateY(${centered * 72 * direction}deg) scale(${.94 + progress * .1})`;
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
