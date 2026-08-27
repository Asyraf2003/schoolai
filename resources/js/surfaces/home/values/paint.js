import { exitAmount } from './desktop-keyframes.js';
import { cardFrame, storyFrame } from './layout.js';

const CARD_PROPERTIES = [
    '--values-x', '--values-y', '--values-z', '--values-ry',
    '--values-rz', '--values-scale', '--values-float-y',
];

const ROOT_PROPERTIES = [
    '--values-heading-opacity', '--values-heading-y',
    '--values-line-one-y', '--values-line-two-y',
    '--values-copy-opacity', '--values-copy-y',
    '--values-handoff-progress', '--values-surface-detail',
    '--values-gallery-transition', '--values-gallery-bridge-y',
];

const WORLD_PROPERTIES = [
    '--values-gallery-exit-progress',
    '--values-gallery-kinetic-opacity',
    '--values-gallery-world-opacity-pct',
];

function clamp(value) {
    return Math.max(0, Math.min(1, value));
}

function smoothstep(value) {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
}

function valuesWorldRoot(root, nodes) {
    const siblingWorld = nodes.programRoot?.parentElement;
    if (siblingWorld?.matches('[data-program-values-world]')) {
        return siblingWorld;
    }

    return root.closest('[data-program-values-world]');
}

function kineticOpacity(cardExitProgress) {
    return 1 - smoothstep(cardExitProgress / 0.16);
}

function worldOpacity(cardExitProgress) {
    return 1 - smoothstep(cardExitProgress / 0.06);
}

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

function writeRootFrame(
    root,
    nodes,
    story,
    handoffProgress,
    cardExitProgress,
    galleryHandoffProgress,
) {
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
        '--values-surface-detail',
        story.surfaceDetail.toFixed(4),
    );
    root.style.setProperty('--values-handoff-progress', handoffProgress.toFixed(4));
    root.style.setProperty(
        '--values-gallery-transition',
        cardExitProgress.toFixed(4),
    );
    root.style.setProperty(
        '--values-gallery-bridge-y',
        `${(100 - galleryHandoffProgress * 123).toFixed(2)}%`,
    );

    const worldRoot = valuesWorldRoot(root, nodes);
    if (worldRoot) {
        worldRoot.style.setProperty(
            '--values-gallery-exit-progress',
            galleryHandoffProgress.toFixed(4),
        );
        worldRoot.style.setProperty(
            '--values-gallery-kinetic-opacity',
            kineticOpacity(cardExitProgress).toFixed(4),
        );
        worldRoot.style.setProperty(
            '--values-gallery-world-opacity-pct',
            `${(worldOpacity(cardExitProgress) * 100).toFixed(2)}%`,
        );
    }

    if (nodes.programRoot) {
        nodes.programRoot.style.setProperty(
            '--program-values-handoff',
            handoffProgress.toFixed(4),
        );
        nodes.programRoot.classList.add('is-values-handoff');
    }
}

export function paintValuesStory(
    root,
    cards,
    nodes,
    progress,
    targetProgress,
    geometry,
    momentum,
    headingState,
    handoffProgress,
    galleryHandoffProgress = 0,
) {
    const story = storyFrame(
        progress,
        geometry,
        momentum,
        headingState,
    );
    const cardExitProgress = geometry.mode === 4
        ? exitAmount(targetProgress)
        : 0;

    cards.forEach((card, index) => {
        writeCardFrame(
            card,
            cardFrame(
                index,
                progress,
                geometry,
                momentum,
                targetProgress,
            ),
        );
    });
    writeRootFrame(
        root,
        nodes,
        story,
        handoffProgress,
        cardExitProgress,
        galleryHandoffProgress,
    );
}

export function clearValuesStory(root, cards, nodes) {
    ROOT_PROPERTIES.forEach((name) => root.style.removeProperty(name));
    cards.forEach((card) => {
        CARD_PROPERTIES.forEach((name) => card.style.removeProperty(name));
    });

    const worldRoot = valuesWorldRoot(root, nodes);
    WORLD_PROPERTIES.forEach((name) => worldRoot?.style.removeProperty(name));

    nodes.programRoot?.style.removeProperty('--program-values-handoff');
    nodes.programRoot?.classList.remove('is-values-handoff');
}
