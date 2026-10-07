export function selectVisibleStory(entries, current) {
    const visible = entries.filter(entry => entry.isIntersecting);
    if (!visible.length) return current;
    return visible.reduce((best, entry) => entry.intersectionRatio > best.intersectionRatio ? entry : best).target;
}

export function observeAboutStories(stories, wide, select) {
    const entries = new Map();
    const targets = wide ? stories : stories.map(story => story.querySelector('[data-about-inline]'));
    let current = targets[0];
    const observer = new IntersectionObserver(changes => {
        changes.forEach(entry => entries.set(entry.target, entry));
        current = selectVisibleStory([...entries.values()], current);
        select(wide ? current : current.closest('[data-about-story]'));
    }, {
        rootMargin: wide ? `${-innerHeight * .2}px 0px ${-innerHeight * .45}px 0px` : '0px',
        threshold: [0, .1, .25, .5, .75, 1],
    });
    targets.forEach(target => observer.observe(target));
    return () => observer.disconnect();
}
