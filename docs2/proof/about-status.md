# Homepage V2 About proof — 2026-10-06

Historical engineering checkpoint before the owner-approved visual correction.
Current composition, colors, Header scale and source proof:
[MAP-V2-04 proof](about-polish-status.md). Earlier measurements/hashes below are
checkpoint evidence, not claims about the subsequent visual state.

Scoped status: PASS. Blueprint: PROVEN for declared About cases.
Repository-wide gates: FAIL on existing legacy references; no new PHP failures.
Local branch: feat/home-v2-about, based on 08039bba (established V2 branch).
Fetched main: a44d484f. No commit, push, PR, merge or R2 mutation performed.

## Delivery
About → Vision → Mission follows Hero at #visi-misi. One semantic story DOM;
at 1024px and wider, normal copy flow + CSS sticky 16:9 stage with two reusable
media layers; below 1024px, media→copy interleaves in normal document flow.
IntersectionObserver owns activation. CSS opacity transitions wait for an image
or decoded video frame. Only one About preview plays. Pause on hidden/offscreen/
modal/pagehide/reduced motion; cached sources reused. Native dialog only for
About/Mission, controlled full source attach on explicit open and load/reset
after clearing src on close. Vision is not a full-video action.

Lang home_about in ID/EN/AR adapts existing home/home_vision content and the five
home_parity mission assurances; no marketing copy invented or duplicated.
Only localized accessibility open/close labels are new. Obsolete CTA/media-label
fields do not enter V2 data. Historical strings/consumers remain intact.
media.about_v2 maps three prepared posters/previews and two full videos.
Background resolves existing media.static.ornaments.geometry_33; static CSS
repeat, fluid14–22rem tiles. No geometry32 or legacy Vision source in About graph.
Cairo font faces affect only the new Arabic About adapter; Inter is unchanged.

## Checks
- PHP About/V2/translation parity:15 tests/140 assertions PASS.
- Active Node contracts:14 PASS (10 prior V2,4 About lifecycle/state).
- Explicit browser run:5 PASS, system Chromium/Linux, real R2 media.
- git diff --check, npm run check:structure (262 source files), npm run build,
  vendor/bin/pint --dirty --format agent PASS.
- Full php artisan test:319 tests,173 passed,71 failures,75 errors. Names exactly
  match correction-php.json; see about-full-php.json. Existing legacy view/source
  migration remains unresolved and outside About scope.
- Complete Node glob:14 pass,7 legacy missing-import failures,5 browser tests
  skipped without opt-in environment. Explicit browser execution above passed5.
- 21 protected Hero/Header/Menu/Cursor files are byte-identical to parent;
  hashes in about-source.json. No dependency/lockfile changes.

## Browser evidence
- about-responsive.json:30 cells, three locales, all six tiers, minimum360px,
  1023/1024 and1180/1181 boundaries. No overflow; correct RTL; reduced motion
  requests no previews/full videos; media/copy order remains semantic.
- about-lifecycle.json: stable desktop geometry, ready crossfade samples, one
  playing derivative, hidden-document/offscreen pause/resume, native Escape and
  focus restoration, full src removal. ordinaryRequests explicitly excludes
  About full, Mission full and legacy Vision; full requests follow modal actions.
- about-fallbacks.json: Arabic narrow real pointer-gesture full playback and
  close/resume, short400px height, 200% font expansion, blocked preview posters,
  missing IntersectionObserver and JavaScript-free semantic fallback.
- about-en-desktop.png, about-ar-desktop.png, about-ar-mobile.png: captured
  compositions, inspected visually. Screenshots alone are not lifecycle proof.

Tests are repeatable against a local app using an isolated migrated SQLite DB,
not the user's DB, and system Chromium started with remote debugging9224.
No browser npm dependency was added. Run with the server and browser running:

```bash
ABOUT_BROWSER_URL=http://127.0.0.1:8123 node --test --test-concurrency=1 \
  tests/Unit/AboutBrowserRuntime.test.mjs tests/Unit/AboutBrowserFallbackRuntime.test.mjs
```

The test server used APP_ENV=testing, DB_CONNECTION=sqlite,
DB_DATABASE=/tmp/schoolai-about-proof.sqlite, DB_URL empty, SESSION_DRIVER=file,
CACHE_STORE=array. Installed PHP modules were enabled via a temporary INI scan
directory for iconv/pdo_sqlite/sqlite3/intl/gd/exif; no project .env or system
PHP configuration was changed.

## Remaining evidence / next valid step
Chromium viewport emulation is not native Safari/Firefox/WebKit certification,
physical-device testing, manual screen-reader proof, Lighthouse100, or field CWV.
No such result is claimed. Owner can review the local About delivery; broader
legacy-suite migration and other-engine certification require separate scope.
