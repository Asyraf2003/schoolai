import {
    readGalleryHandoffProgress,
    readHandoffProgress,
    readStoryProgress,
} from './motion.js';

export function supportsStoryMotion() {
    return typeof CSS !== 'undefined'
        && CSS.supports('overflow', 'clip')
        && CSS.supports('position', 'sticky')
        && CSS.supports('perspective', '800px')
        && CSS.supports('transform-style', 'preserve-3d');
}

export function readValuesFrameTarget(root, nodes, geometry) {
    const rootTop = root.getBoundingClientRect().top;
    const headingTop = nodes.heading.getBoundingClientRect().top;
    const timelineTop = nodes.timeline.getBoundingClientRect().top;
    const storyTop = geometry.mode === 4 ? timelineTop : rootTop;

    return {
        handoff: readHandoffProgress(rootTop, geometry.viewportHeight),
        story: readStoryProgress(storyTop, geometry),
        galleryHandoff: readGalleryHandoffProgress(storyTop, geometry),
        headingTop,
        timelineTop,
    };
}
