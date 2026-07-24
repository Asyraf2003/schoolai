import fs from 'node:fs';
import path from 'node:path';

const fragmentPattern = /^\s*(?:\/\*|\/\/)\s*@fragment\s+(.+?)(?:\s*\*\/)?\s*$/gm;

function manifestFragments(source) {
    return Array.from(source.matchAll(fragmentPattern), (match) => match[1].trim());
}

export function orderedFragments() {
    return {
        name: 'schoolai-ordered-source-fragments',
        enforce: 'pre',

        load(id) {
            const cleanId = id.split('?', 1)[0];

            if (!/\.(?:css|js)$/.test(cleanId) || !fs.existsSync(cleanId)) {
                return null;
            }

            const source = fs.readFileSync(cleanId, 'utf8');
            const fragments = manifestFragments(source);

            if (fragments.length === 0) {
                return null;
            }

            const code = fragments.map((relativePath) => {
                const fragmentPath = path.resolve(path.dirname(cleanId), relativePath);

                this.addWatchFile(fragmentPath);

                return fs.readFileSync(fragmentPath, 'utf8');
            }).join('');

            return { code, map: null };
        },
    };
}
