import fs from 'node:fs';
import path from 'node:path';
import process from 'node:process';

const projectRoot = process.cwd();
const sourceRoots = ['app', 'database', 'resources', 'routes'];
const maxLines = 200;
const sourcePattern = /(?:\.blade\.php|\.php|\.js|\.css|\.js\.part|\.css\.part)$/;
const fragmentPattern = /^\s*(?:\/\*|\/\/)\s*@fragment\s+(.+?)(?:\s*\*\/)?\s*$/gm;
const includePattern = /@include\(\s*['"]([^'"]+)['"]/g;
const requirePattern = /resource_path\(\s*['"]views\/([^'"]+)['"]\s*\)/g;
const concernClassPatterns = [
    /\bnew\s+([A-Z][A-Za-z0-9_]*)/g,
    /\b([A-Z][A-Za-z0-9_]*)::/g,
    /\binstanceof\s+([A-Z][A-Za-z0-9_]*)/g,
    /\bcatch\s*\(\s*([A-Z][A-Za-z0-9_]*)/g,
    /\b([A-Z][A-Za-z0-9_]*)\s+\$[A-Za-z_][A-Za-z0-9_]*/g,
    /\)\s*:\s*\??([A-Z][A-Za-z0-9_]*)/g,
];
const failures = [];
const files = [];

function walk(directory) {
    if (!fs.existsSync(directory)) {
        failures.push(`Missing source root: ${path.relative(projectRoot, directory)}`);
        return;
    }

    for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
        const target = path.join(directory, entry.name);

        if (entry.isDirectory()) {
            walk(target);
        } else if (sourcePattern.test(entry.name)) {
            files.push(target);
        }
    }
}

function lineCount(source) {
    if (source.length === 0) {
        return 0;
    }

    const newlineCount = (source.match(/\n/g) ?? []).length;

    return newlineCount + (source.endsWith('\n') ? 0 : 1);
}

function relative(target) {
    return path.relative(projectRoot, target).split(path.sep).join('/');
}

function importedClassNames(source) {
    const imported = new Set();
    const pattern = /^use\s+(?!function\b|const\b)([^;]+);/gm;

    for (const match of source.matchAll(pattern)) {
        const [target, alias] = match[1].trim().split(/\s+as\s+/i);
        imported.add(alias ?? target.split('\\').at(-1));
    }

    return imported;
}

function unimportedConcernClasses(source) {
    const imported = importedClassNames(source);
    const missing = new Set();

    for (const pattern of concernClassPatterns) {
        for (const match of source.matchAll(pattern)) {
            const name = match[1];
            const nameOffset = match.index + match[0].indexOf(name);

            if (source[nameOffset - 1] === '\\' || imported.has(name)) {
                continue;
            }

            missing.add(name);
        }
    }

    return [...missing];
}

for (const sourceRoot of sourceRoots) {
    walk(path.join(projectRoot, sourceRoot));
}

for (const file of files) {
    const source = fs.readFileSync(file, 'utf8');
    const count = lineCount(source);

    if (count > maxLines) {
        failures.push(`${relative(file)} has ${count} lines (limit: ${maxLines})`);
    }

    if (file.endsWith('.php') && relative(file).includes('/Concerns/')) {
        for (const name of unimportedConcernClasses(source)) {
            failures.push(`${relative(file)} references unimported class: ${name}`);
        }
    }

    if (file.endsWith('.blade.php')) {
        for (const match of source.matchAll(includePattern)) {
            const target = path.join(
                projectRoot,
                'resources/views',
                `${match[1].replaceAll('.', '/')}.blade.php`,
            );

            if (!fs.existsSync(target)) {
                failures.push(`${relative(file)} includes missing view: ${match[1]}`);
            }
        }

        for (const match of source.matchAll(requirePattern)) {
            const target = path.join(projectRoot, 'resources/views', match[1]);

            if (!fs.existsSync(target)) {
                failures.push(`${relative(file)} requires missing view data: ${match[1]}`);
            }
        }
    }

    if (!file.endsWith('.js') && !file.endsWith('.css')) {
        continue;
    }

    const fragments = Array.from(
        source.matchAll(fragmentPattern),
        (match) => match[1].trim(),
    );

    if (fragments.length === 0) {
        continue;
    }

    const manifestOnly = source
        .replace(fragmentPattern, '')
        .trim();

    if (manifestOnly !== '') {
        failures.push(`${relative(file)} mixes fragment directives with source code`);
    }

    if (new Set(fragments).size !== fragments.length) {
        failures.push(`${relative(file)} contains duplicate fragment paths`);
    }

    for (const fragment of fragments) {
        const target = path.resolve(path.dirname(file), fragment);

        if (!fs.existsSync(target)) {
            failures.push(`${relative(file)} references missing fragment: ${fragment}`);
            continue;
        }

        const expectedSuffix = file.endsWith('.js') ? '.js.part' : '.css.part';

        if (!target.endsWith(expectedSuffix)) {
            failures.push(`${relative(file)} references invalid fragment type: ${fragment}`);
        }
    }
}

if (failures.length > 0) {
    console.error('Source structure check failed:\n');

    for (const failure of failures) {
        console.error(`- ${failure}`);
    }

    process.exit(1);
}

console.log(`Source structure check passed: ${files.length} files, maximum ${maxLines} lines each.`);
