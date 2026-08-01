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

for (const entry of entries) {
  const record = manifest[entry];
  if (!record?.file) throw new Error(`Missing manifest entry: ${entry}`);

  const assetPath = path.join(root, 'public/build', record.file);
  const source = fs.readFileSync(assetPath);
  report[entry] = {
    file: record.file,
    rawBytes: source.length,
    gzipBytes: zlib.gzipSync(source, { level: 9 }).length,
  };
}

fs.mkdirSync(path.dirname(outputPath), { recursive: true });
fs.writeFileSync(outputPath, `${JSON.stringify(report, null, 2)}\n`);
console.log(JSON.stringify(report, null, 2));
