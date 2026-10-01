const clients = new Set();
const dirty = new Set();
let frame = 0;
let revision = 0;
let motionEpoch = 0;
let suspended = false;
let lifecycle = null;

function cancel() {
    if (frame) window.cancelAnimationFrame(frame);
    frame = 0;
}
function schedule() {
    if (!frame && dirty.size && !suspended && !document.hidden) {
        frame = window.requestAnimationFrame(flush);
    }
}
function invalidate() {
    revision++;
    clients.forEach(client => dirty.add(client));
    schedule();
}
function flush(time) {
    frame = 0;
    if (suspended || document.hidden) return;
    const viewport = Object.freeze({
        time, revision, motionEpoch, scrollY: window.scrollY || 0,
        width: window.innerWidth || 1, height: window.innerHeight || 1,
    });
    const batch = [...dirty].filter(client => clients.has(client));
    dirty.clear();
    const snapshots = batch.map(client => ({ client, value: client.read(viewport) }));
    snapshots.forEach(({ client, value }) => {
        if (clients.has(client) && client.write(value, viewport) === true) dirty.add(client);
    });
    schedule();
}
function dispose() {
    cancel();
    dirty.clear();
    clients.clear();
    lifecycle?.abort();
    lifecycle = null;
}
function listen() {
    if (lifecycle) return;
    suspended = false;
    lifecycle = new AbortController();
    const options = { signal: lifecycle.signal };
    window.addEventListener('scroll', invalidate, { passive: true, ...options });
    window.addEventListener('resize', invalidate, { passive: true, ...options });
    window.addEventListener('pagehide', event => {
        suspended = true;
        motionEpoch++;
        cancel();
        if (!event.persisted) dispose();
    }, options);
    window.addEventListener('pageshow', () => { suspended = false; invalidate(); }, options);
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) { motionEpoch++; cancel(); }
        else invalidate();
    }, options);
}

export function subscribeHomepageFrame(read, write) {
    listen();
    const client = { read, write };
    clients.add(client);
    const request = () => {
        if (!clients.has(client)) return;
        dirty.add(client);
        schedule();
    };
    const remove = () => {
        clients.delete(client);
        dirty.delete(client);
        if (!clients.size) dispose();
    };
    request();
    return { request, remove };
}
