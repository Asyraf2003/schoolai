import { Texture, SRGBColorSpace, MeshBasicMaterial, DoubleSide } from 'three';

// One texture/material per media identity, shared by every cyclic instance.
export function spatialTextures(links, wake) {
    let disposed = false;
    const assets = new Map();
    function release(index) {
        const asset = assets.get(index);
        if (!asset) return;
        assets.delete(index); asset.request.abort(); asset.material.dispose(); asset.texture?.dispose();
    }
    return {
        get(index) {
            if (assets.has(index)) return assets.get(index);
            const asset = { request: new AbortController(), material: new MeshBasicMaterial({ side: DoubleSide }), texture: null, failed: false };
            assets.set(index, asset);
            // Archive <img> requests can cache a non-CORS variant of this URL.
            fetch(links[index].dataset.poster, { mode: 'cors', cache: 'reload', signal: asset.request.signal })
                .then(response => { if (!response.ok) throw new Error('Texture unavailable'); return response.blob(); })
                .then(async blob => {
                    const url = URL.createObjectURL(blob), image = new Image();
                    image.src = url;
                    try { await image.decode(); } finally { URL.revokeObjectURL(url); }
                    return new Texture(image);
                }).then(texture => {
                    if (disposed || assets.get(index) !== asset) { texture.dispose(); return; }
                    texture.colorSpace = SRGBColorSpace; texture.needsUpdate = true;
                    asset.texture = texture; asset.material.map = texture; asset.material.needsUpdate = true;
                    wake();
                }).catch(error => {
                    if (disposed || assets.get(index) !== asset || error.name === 'AbortError') return;
                    asset.failed = true; wake();
                });
            return asset;
        },
        retain(indices) { for (const index of assets.keys()) if (!indices.has(index)) release(index); },
        dispose() { disposed = true; for (const index of assets.keys()) release(index); },
    };
}
