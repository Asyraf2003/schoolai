import crypto from 'node:crypto';
import fs from 'node:fs';
import path from 'node:path';
import process from 'node:process';

const root = process.cwd();
const sourceRoots = ['app', 'database', 'resources', 'routes'];
const sourcePattern = /(?:\.blade\.php|\.php|\.m?js|\.css)$/;
const maxLines = 200;
const failures = [];
const files = [];
const importers = new Map();
const assetEntries = new Set();

const relative = (target) => path.relative(root, target).split(path.sep).join('/');
const lineCount = (source) => source
    ? (source.match(/\n/g) ?? []).length + (source.endsWith('\n') ? 0 : 1)
    : 0;
const sha256 = (source) => crypto.createHash('sha256').update(source).digest('hex');

function walk(directory) {
    if (!fs.existsSync(directory)) {
        failures.push(`Missing source root: ${relative(directory)}`);
        return;
    }

    for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
        const target = path.join(directory, entry.name);

        if (entry.isDirectory()) {
            walk(target);
        } else {
            if (/\.(?:js|css)\.part$/.test(entry.name)) {
                failures.push(`${relative(target)} uses a forbidden source fragment extension`);
            }
            if (sourcePattern.test(entry.name)) files.push(target);
        }
    }
}

function resolveImport(importer, request) {
    if (!request.startsWith('.')) return null;

    const target = path.resolve(path.dirname(importer), request);
    const candidates = [target, `${target}.js`, `${target}.css`, path.join(target, 'index.js')];
    return candidates.find((candidate) => fs.existsSync(candidate)) ?? target;
}

function recordImports(file, source) {
    const patterns = file.endsWith('.css')
        ? [/@import\s+(?:url\(\s*)?['"]([^'"]+)['"]/g]
        : [
            /\b(?:import|export)\s+(?:[^'"]*?\s+from\s+)?['"]([^'"]+)['"]/g,
            /\bimport\(\s*['"]([^'"]+)['"]\s*\)/g,
        ];

    for (const pattern of patterns) {
        for (const match of source.matchAll(pattern)) {
            const target = resolveImport(file, match[1]);
            if (!target) continue;

            if (!fs.existsSync(target)) {
                failures.push(`${relative(file)} imports missing source: ${match[1]}`);
                continue;
            }

            const key = path.resolve(target);
            importers.set(key, (importers.get(key) ?? 0) + 1);
        }
    }
}

function importedClassNames(source) {
    const imported = new Set();
    for (const match of source.matchAll(/^use\s+(?!function\b|const\b)([^;]+);/gm)) {
        const [target, alias] = match[1].trim().split(/\s+as\s+/i);
        imported.add(alias ?? target.split('\\').at(-1));
    }
    return imported;
}

function checkConcernImports(file, source) {
    const imported = importedClassNames(source);
    const patterns = [
        /\bnew\s+([A-Z][A-Za-z0-9_]*)/g, /\b([A-Z][A-Za-z0-9_]*)::/g,
        /\binstanceof\s+([A-Z][A-Za-z0-9_]*)/g, /\bcatch\s*\(\s*([A-Z][A-Za-z0-9_]*)/g,
        /\b([A-Z][A-Za-z0-9_]*)\s+\$[A-Za-z_][A-Za-z0-9_]*/g,
        /\)\s*:\s*\??([A-Z][A-Za-z0-9_]*)/g,
    ];
    const missing = new Set();

    for (const pattern of patterns) {
        for (const match of source.matchAll(pattern)) {
            const name = match[1];
            const offset = match.index + match[0].indexOf(name);
            if (source[offset - 1] !== '\\' && !imported.has(name)) missing.add(name);
        }
    }
    for (const name of missing) failures.push(`${relative(file)} references unimported class: ${name}`);
}

function checkBladeReferences(file, source) {
    const references = [
        [/@include\(\s*['"]([^'"]+)['"]/g, (name) => `resources/views/${name.replaceAll('.', '/')}.blade.php`],
        [/resource_path\(\s*['"]views\/([^'"]+)['"]\s*\)/g, (name) => `resources/views/${name}`],
    ];

    for (const [pattern, targetPath] of references) {
        for (const match of source.matchAll(pattern)) {
            const target = path.join(root, targetPath(match[1]));
            if (!fs.existsSync(target)) failures.push(`${relative(file)} references missing view: ${match[1]}`);
        }
    }

    for (const match of source.matchAll(/['"](resources\/(?:js|css)\/[^'"]+\.(?:js|css))['"]/g)) {
        assetEntries.add(match[1]);
    }
}

for (const sourceRoot of sourceRoots) walk(path.join(root, sourceRoot));

for (const file of files) {
    const source = fs.readFileSync(file, 'utf8');
    const count = lineCount(source);
    if (count > maxLines) failures.push(`${relative(file)} has ${count} lines (limit: ${maxLines})`);
    if (source.includes('@fragment')) failures.push(`${relative(file)} contains a forbidden fragment directive`);
    if (file.endsWith('.php') && relative(file).includes('/Concerns/')) checkConcernImports(file, source);
    if (file.endsWith('.blade.php')) checkBladeReferences(file, source);
    if (/\.(?:m?js|css)$/.test(file)) recordImports(file, source);
}

const viteSource = fs.readFileSync(path.join(root, 'vite.config.js'), 'utf8');
for (const match of viteSource.matchAll(/['"](resources\/(?:js|css)\/[^'"]+\.(?:js|css))['"]/g)) {
    assetEntries.add(match[1]);
}

for (const file of files.filter((target) => /resources\/(?:js|css)\/.+\.(?:js|css)$/.test(relative(target)))) {
    if (relative(file).startsWith('resources/css/reference/')) continue;
    if (!assetEntries.has(relative(file)) && !importers.has(path.resolve(file))) {
        failures.push(`${relative(file)} is not referenced by an entry point or local import`);
    }
}

const manifestPath = path.join(root, 'docs/architecture/source-module-equivalence.json');
const overridesPath = path.join(root, 'docs/architecture/source-module-equivalence-overrides.json');
const baseManifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));
const overrides = fs.existsSync(overridesPath)
    ? JSON.parse(fs.readFileSync(overridesPath, 'utf8'))
    : {};
const manifest = {
    ...baseManifest,
    css: { ...(baseManifest.css ?? {}), ...(overrides.css ?? {}) },
};

for (const [entry, data] of Object.entries(manifest.css ?? {})) {
    const entryPath = path.join(root, entry);
    const entrySource = fs.readFileSync(entryPath, 'utf8');
    const imports = Array.from(entrySource.matchAll(/@import\s+['"]([^'"]+)['"]/g), (match) => match[1]);
    const expected = data.orderedModules.map((module) => module.path);
    if (JSON.stringify(imports) !== JSON.stringify(expected)) failures.push(`${entry} changed CSS import order`);

    const combined = expected.map((request) =>
        fs.readFileSync(path.resolve(path.dirname(entryPath), request), 'utf8')
    ).join('');
    if (sha256(combined) !== data.sourceSha256) failures.push(`${entry} no longer matches its source checksum`);
}

if (failures.length) {
    console.error('Source structure check failed:\n');
    failures.forEach((failure) => console.error(`- ${failure}`));
    process.exit(1);
}

console.log(`Source structure check passed: ${files.length} files, maximum ${maxLines} lines each.`);
