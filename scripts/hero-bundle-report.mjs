import fs from 'node:fs';
import path from 'node:path';
import zlib from 'node:zlib';

const root = process.cwd();
const manifestPath = path.join(root, 'public/build/manifest.json');
const outputPath = path.join(root, 'storage/app/hero-proof/bundle-report.json');
const entries = [
  'resources/css/pages/welcome-hero.css',
  'resources/css/pages/welcome-navigation.css',
  'resources/js/pages/welcome-hero.js',
  'resources/js/pages/welcome.js',
];

if (!fs.existsSync(manifestPath)) {
  throw new Error(`Missing Vite manifest: ${manifestPath}`);
}

const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));
const report = {};

function measure(record, key) {
  if (!record?.file) throw new Error(`Missing manifest asset: ${key}`);

  const assetPath = path.join(root, 'public/build', record.file);
  const source = fs.readFileSync(assetPath);
  return {
    key,
    file: record.file,
    rawBytes: source.length,
    gzipBytes: zlib.gzipSync(source, { level: 9 }).length,
  };
}

for (const entry of entries) {
  const record = manifest[entry];
  if (!record) throw new Error(`Missing manifest entry: ${entry}`);
  const measured = measure(record, entry);
  delete measured.key;
  report[entry] = measured;
}

const heroEntry = manifest['resources/js/pages/welcome-hero.js'];
const pending = [...(heroEntry.dynamicImports ?? [])];
const visited = new Set();
const deferred = [];

while (pending.length) {
  const key = pending.shift();
  if (!key || visited.has(key)) continue;
  visited.add(key);

  const record = manifest[key];
  if (!record) throw new Error(`Missing deferred Hero manifest entry: ${key}`);
  deferred.push(measure(record, key));
  pending.push(...(record.dynamicImports ?? []));
}

report.heroDeferred = {
  assets: deferred,
  rawBytes: deferred.reduce((total, asset) => total + asset.rawBytes, 0),
  gzipBytes: deferred.reduce((total, asset) => total + asset.gzipBytes, 0),
};

fs.mkdirSync(path.dirname(outputPath), { recursive: true });
fs.writeFileSync(outputPath, `${JSON.stringify(report, null, 2)}\n`);
console.log(JSON.stringify(report, null, 2));
