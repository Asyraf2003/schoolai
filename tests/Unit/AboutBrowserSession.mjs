import fs from 'node:fs/promises';

export async function openBrowserSession(port = 9224) {
    const target = await (await fetch(`http://127.0.0.1:${port}/json/new?about:blank`, { method: 'PUT' })).json();
    const socket = new WebSocket(target.webSocketDebuggerUrl);
    await new Promise(resolve => socket.addEventListener('open', resolve, { once: true }));
    let id = 0;
    const pending = new Map();
    const listeners = new Map();
    socket.addEventListener('message', event => {
        const message = JSON.parse(event.data);
        if (message.id) {
            const task = pending.get(message.id);
            pending.delete(message.id);
            if (message.error) task.reject(new Error(JSON.stringify(message.error)));
            else task.resolve(message.result);
        } else listeners.get(message.method)?.forEach(callback => callback(message.params));
    });
    const send = (method, params = {}) => new Promise((resolve, reject) => {
        pending.set(++id, { resolve, reject });
        socket.send(JSON.stringify({ id, method, params }));
    });
    const evaluate = async expression => {
        const result = await send('Runtime.evaluate', { expression, awaitPromise: true, returnByValue: true });
        if (result.exceptionDetails) throw new Error(JSON.stringify(result.exceptionDetails));
        return result.result.value;
    };
    return {
        send, evaluate,
        on(method, callback) { listeners.set(method, [...(listeners.get(method) ?? []), callback]); },
        async wait(expression, timeout = 15000) {
            const end = Date.now() + timeout;
            while (Date.now() < end) {
                if (await evaluate(expression)) return;
                await new Promise(resolve => setTimeout(resolve, 80));
            }
            throw new Error(`Timed out: ${expression}`);
        },
        async navigate(url) {
            await send('Page.navigate', { url });
            await this.wait('document.readyState === "complete" && !!document.querySelector("[data-about]")');
            await this.wait('document.querySelector("[data-about]").dataset.wide !== undefined');
        },
        async screenshot(path) {
            const shot = await send('Page.captureScreenshot', { format: 'png' });
            await fs.writeFile(path, Buffer.from(shot.data, 'base64'));
        },
        async close() {
            socket.close();
            await fetch(`http://127.0.0.1:${port}/json/close/${target.id}`);
        },
    };
}
