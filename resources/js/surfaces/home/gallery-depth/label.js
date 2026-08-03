export class DepthLabel {
    constructor(root, gallery, config) {
        this.gallery = gallery;
        this.config = config;
        this.title = root.querySelector('[data-depth-gallery-title]');
        this.caption = root.querySelector('[data-depth-gallery-caption]');
        this.activeIndex = -1;
    }

    getTargetIndex(cameraZ) {
        const blend = this.gallery.getPlaneBlendData(cameraZ);
        if (!blend) return -1;
        return blend.blend >= 0.5
            ? blend.nextPlaneIndex
            : blend.currentPlaneIndex;
    }

    update(camera) {
        const index = this.getTargetIndex(camera.position.z);
        if (index < 0 || index === this.activeIndex) return;
        const item = this.config[index];
        if (!item) return;
        this.activeIndex = index;
        if (this.title) this.title.textContent = item.title;
        if (this.caption) this.caption.textContent = item.caption;
    }

    clear() {
        if (this.title) this.title.textContent = '';
        if (this.caption) this.caption.textContent = '';
        this.activeIndex = -1;
    }
}
