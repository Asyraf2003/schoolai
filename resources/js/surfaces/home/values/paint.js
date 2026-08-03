import { cardFrame, storyFrame } from './layout.js';

const CARD_PROPERTIES = [
    '--values-x', '--values-y', '--values-z', '--values-rx',
    '--values-ry', '--values-rz', '--values-scale', '--values-opacity',
];

const ROOT_PROPERTIES = [
    '--values-progress', '--values-heading-opacity',
    '--values-line-one-y', '--values-line-two-y',
    '--values-copy-opacity', '--values-copy-y',
    '--values-trail-progress', '--values-trail-opacity',
    '--values-trail-y',
];

function writeCardFrame(card, state) {
    card.style.setProperty('--values-x', `${state.x.toFixed(2)}px`);
    card.style.setProperty('--values-y', `${state.y.toFixed(2)}px`);
    card.style.setProperty('--values-z', `${state.z.toFixed(2)}px`);
    card.style.setProperty('--values-rx', `${state.rx.toFixed(2)}deg`);
    card.style.setProperty('--values-ry', `${state.ry.toFixed(2)}deg`);
    card.style.setProperty('--values-rz', `${state.rz.toFixed(2)}deg`);
    card.style.setProperty('--values-scale', state.scale.toFixed(4));
    card.style.setProperty('--values-opacity', state.opacity.toFixed(4));
}

export function paintValuesStory(
    root,
    cards,
    progress,
    geometry,
    momentum,
) {
    cards.forEach((card, index) => {
        writeCardFrame(
            card,
            cardFrame(index, progress, geometry, momentum),
        );
        card.style.zIndex = String(20 - index);
    });

    const story = storyFrame(progress, geometry.viewportHeight, momentum);
    root.style.setProperty('--values-progress', story.progress.toFixed(5));
    root.style.setProperty(
        '--values-heading-opacity',
        story.headingOpacity.toFixed(4),
    );
    root.style.setProperty(
        '--values-line-one-y',
        `${story.lineOneY.toFixed(2)}px`,
    );
    root.style.setProperty(
        '--values-line-two-y',
        `${story.lineTwoY.toFixed(2)}px`,
    );
    root.style.setProperty(
        '--values-copy-opacity',
        story.copyOpacity.toFixed(4),
    );
    root.style.setProperty('--values-copy-y', `${story.copyY.toFixed(2)}px`);
    root.style.setProperty(
        '--values-trail-progress',
        story.trailProgress.toFixed(5),
    );
    root.style.setProperty(
        '--values-trail-opacity',
        story.trailOpacity.toFixed(4),
    );
    root.style.setProperty(
        '--values-trail-y',
        `${story.trailY.toFixed(2)}px`,
    );
}

export function clearValuesStory(root, cards) {
    ROOT_PROPERTIES.forEach((name) => root.style.removeProperty(name));
    cards.forEach((card) => {
        CARD_PROPERTIES.forEach((name) => card.style.removeProperty(name));
    });
}
