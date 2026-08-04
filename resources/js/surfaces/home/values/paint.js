import { cardFrame, storyFrame } from './layout.js';

const CARD_PROPERTIES = [
    '--values-x', '--values-y', '--values-z', '--values-ry',
    '--values-rz', '--values-scale', '--values-float-y',
];

const ROOT_PROPERTIES = [
    '--values-heading-opacity', '--values-heading-y',
    '--values-line-one-y', '--values-line-two-y',
    '--values-copy-opacity', '--values-copy-y',
    '--values-trail-progress', '--values-trail-opacity',
    '--values-trail-y',
];

function writeCardFrame(card, state) {
    card.style.setProperty('--values-x', `${state.x.toFixed(2)}px`);
    card.style.setProperty('--values-y', `${state.y.toFixed(2)}px`);
    card.style.setProperty('--values-z', `${state.z.toFixed(2)}px`);
    card.style.setProperty('--values-ry', `${state.ry.toFixed(2)}deg`);
    card.style.setProperty('--values-rz', `${state.rz.toFixed(2)}deg`);
    card.style.setProperty('--values-scale', state.scale.toFixed(4));
    card.style.setProperty(
        '--values-float-y',
        `${state.floatY.toFixed(2)}px`,
    );
}

function readTrailPoint(nodes, geometry, progress) {
    if (geometry.mode !== 4 || !geometry.trailLength || !nodes.trailPath) {
        return null;
    }

    return nodes.trailPath.getPointAtLength(
        geometry.trailLength * progress,
    );
}

function writeRootFrame(root, story) {
    root.style.setProperty(
        '--values-heading-opacity',
        story.headingOpacity.toFixed(4),
    );
    root.style.setProperty(
        '--values-heading-y',
        `${story.headingY.toFixed(2)}px`,
    );
    root.style.setProperty(
        '--values-line-one-y',
        `${story.lineOneY.toFixed(2)}%`,
    );
    root.style.setProperty(
        '--values-line-two-y',
        `${story.lineTwoY.toFixed(2)}%`,
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
    root.style.setProperty('--values-trail-y', `${story.trailY.toFixed(2)}px`);
}

export function paintValuesStory(
    root,
    cards,
    nodes,
    progress,
    geometry,
    momentum,
    headingState,
) {
    const story = storyFrame(
        progress,
        geometry,
        momentum,
        headingState,
    );
    const trailPoint = readTrailPoint(
        nodes,
        geometry,
        story.trailProgress,
    );

    cards.forEach((card, index) => {
        writeCardFrame(
            card,
            cardFrame(index, progress, geometry, momentum),
        );
    });
    writeRootFrame(root, story);

    if (trailPoint && nodes.trailHead) {
        nodes.trailHead.setAttribute('cx', trailPoint.x.toFixed(2));
        nodes.trailHead.setAttribute('cy', trailPoint.y.toFixed(2));
    }
}

export function clearValuesStory(root, cards, nodes) {
    ROOT_PROPERTIES.forEach((name) => root.style.removeProperty(name));
    cards.forEach((card) => {
        CARD_PROPERTIES.forEach((name) => card.style.removeProperty(name));
    });
    nodes.trailHead?.setAttribute('cx', '-120');
    nodes.trailHead?.setAttribute('cy', '770');
}
