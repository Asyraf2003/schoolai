import fs from 'node:fs/promises';
import path from 'node:path';

// Browser-only reference: serve unchanged archived owners without adding a route,
// Vite input or OLD dependency to the production application.
export async function oldValuesReference(context, page) {
    const markup = await page.locator('[data-values-cards-stage]').evaluate(stage => {
        const cards = [...stage.children].map(card => card.cloneNode(true));
        for (const card of cards) {
            card.removeAttribute('style');
            const original = stage.children[cards.indexOf(card)];
            card.style.setProperty('--values-accent', original.style.getPropertyValue('--values-card-accent'));
            for (const node of [card, ...card.querySelectorAll('*')]) {
                if (node !== card) node.removeAttribute('style');
                node.className = node.className.replaceAll('values__card-brand', 'values-card__back-brand')
                    .replaceAll('values__card-', 'values-card__').replace('values__card', 'values-card');
            }
            for (const [name, role] of Object.entries({ index: 'meta', summary: 'subtitle', title: 'component-title', body: 'description' })) {
                card.querySelector(`.values-card__${name}`).dataset.textRole = role;
            }
        }
        return { cards: cards.map(card => card.outerHTML).join(''), lang: document.documentElement.lang,
            dir: document.documentElement.dir, pattern: document.querySelector('[data-values-cards-track]').style.getPropertyValue('--values-pattern') };
    });
    const reference = await context.newPage();
    reference.setDefaultTimeout(15000);
    const origin = new URL(page.url()).origin;
    await reference.route('**/__values_reference__/**', async route => {
        const name = new URL(route.request().url()).pathname.split('/__values_reference__/')[1];
        if (name === 'index.html') {
            return route.fulfill({ contentType: 'text/html', body: `<!doctype html><html lang="${markup.lang}" dir="${markup.dir}"><head><meta charset="utf-8">
                <link rel="stylesheet" href="resources_old/css/text-system.css">
                <link rel="stylesheet" href="resources_old/css/arabic-typography.css">
                <link rel="stylesheet" href="resources_old/css/public-latin-inter.css">
                <link rel="stylesheet" href="resources_old/css/pages/welcome-values-story.css">
                <style>*{box-sizing:border-box}body{margin:0;font-family:Inter,sans-serif;background:#2038ff;--program-values-final-color:#2038ff;--nav-h:72px}
                html[lang=ar] body{font-family:Cairo,sans-serif} .values-story{margin:0} .values-story__entry,.values-story__exit{display:none}
                </style></head><body class="home-page"><template id="reference-content"><div style="height:900px"></div>
                <section class="values-story" data-values-story style="--values-card-pattern-image:${markup.pattern}">
                <header data-values-heading></header><div class="values-story__timeline" data-values-timeline>
                <div class="values-story__clip" data-values-stage><div data-values-spatial class="values-story__spatial"></div>
                <div class="values-story__perspective" data-values-perspective><div class="values-story__grid" data-values-cards>${markup.cards}</div></div>
                </div></div></section><div style="height:900px"></div></template>
                <script type="module">import {createValuesStory} from './resources_old/js/surfaces/home/values/controller.js';
                // OLD uses optional Inter: load before inserting text so the
                // reference captures its intended font, not the cold-load fallback.
                await Promise.all(['400 16px Inter','500 16px Cairo','600 16px Cairo','700 16px Cairo'].map(font=>document.fonts.load(font)));
                const template=document.querySelector('#reference-content');template.replaceWith(template.content);
                window.referenceDispose=createValuesStory(document.querySelector('[data-values-story]'));</script></body></html>` });
        }
        const safe = path.resolve(name);
        if (!safe.startsWith(`${process.cwd()}/`) || !/^(resources_old|resources|node_modules)\//.test(name)) return route.abort();
        try {
            let body = await fs.readFile(safe);
            if (name.endsWith('.css')) body = body.toString().replaceAll('"@fontsource/', '"/__values_reference__/node_modules/@fontsource/');
            const contentType = name.endsWith('.css') ? 'text/css' : name.endsWith('.js') ? 'application/javascript' : 'font/woff2';
            return route.fulfill({ body, contentType });
        } catch { return route.abort(); }
    });
    await reference.goto(`${origin}/__values_reference__/index.html`);
    await reference.evaluate(() => document.fonts.ready);
    await reference.waitForFunction(() => document.querySelector('[data-values-story]')?.dataset.valuesReady === 'prepared');
    return reference;
}
