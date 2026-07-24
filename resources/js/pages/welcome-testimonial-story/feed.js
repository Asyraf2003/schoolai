var FEED_URL = '/testimoni/media';

var FALLBACK_PRIMARY = {
        type: 'video',
        source: 'upload',
        media_url: '/media/hero/shanghai-mega-city.mp4',
        thumbnail_url: '/images/hero-video-poster.svg'
    };

var FALLBACK_NODES = [
        '/media/home/9.png',
        '/media/home/10.png',
        '/media/home/11.png',
        '/media/home/12.png',
        '/media/home/9.png',
        '/media/home/10.png',
        '/media/home/11.png',
        '/media/home/12.png'
    ].map(function (url) {
        return {
            type: 'photo',
            source: 'upload',
            media_url: url,
            thumbnail_url: url
        };
    });

function normalizeItem(item) {
        if (!item || typeof item !== 'object') return null;

        var type = item.type === 'video' ? 'video' : 'photo';
        var source = item.source === 'embed' ? 'embed' : 'upload';
        var mediaUrl = typeof item.media_url === 'string' ? item.media_url.trim() : '';
        var thumbnailUrl = typeof item.thumbnail_url === 'string' ? item.thumbnail_url.trim() : '';

        if (!mediaUrl) return null;
        if (type === 'photo') source = 'upload';

        return {
            id: item.id || null,
            type: type,
            source: source,
            media_url: mediaUrl,
            thumbnail_url: thumbnailUrl
        };
    }

export async function loadMediaFeed() {
        try {
            var response = await fetch(FEED_URL, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' }
            });

            if (!response.ok) return [];

            var payload = await response.json();
            var items = Array.isArray(payload.items) ? payload.items : [];

            return items.map(normalizeItem).filter(Boolean).slice(0, 9);
        } catch (error) {
            return [];
        }
    }

export function buildStoryData(items) {
        if (!items.length) {
            return {
                primary: FALLBACK_PRIMARY,
                nodes: FALLBACK_NODES
            };
        }

        var primaryIndex = items.findIndex(function (item) {
            return item.type === 'video';
        });
        var primary = primaryIndex >= 0 ? items[primaryIndex] : FALLBACK_PRIMARY;
        var nodes = items.filter(function (item, index) {
            return index !== primaryIndex;
        }).slice(0, 8);

        return {
            primary: primary,
            nodes: nodes
        };
    }
