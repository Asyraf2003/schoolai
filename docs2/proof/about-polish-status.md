# About composition + Header scale proof — 2026-10-06

Historical checkpoint before the latest owner typography/mascot/shadow correction.
Current visual delivery: [MAP-V2-05 proof](about-type-status.md).
The decorative mascot and typography below describe this earlier checkpoint.

Scoped status: PASS. Blueprint: PROVEN for declared cases.
Branch: local feat/home-v2-about; HEAD08039bba; main freshly fetched a44d484f.
No push, merge, commit, R2 object mutation, or dependency changes.

## Measured before/after
Same system Chromium153/Linux,1920x1080, EN and reduced-motion posters, so
video frames do not confound visual comparison. Measurements are DOM geometry.

| Metric | Before | After |
|---|---:|---:|
| Header bar |108px|72px|
| Logo |64x64px|48x48px|
| Nav text |16.64px|15.2px|
| Dropdown media |416x277.33px|352x234.66px|
| About media |761.61x428.39px|873.09x491.11px|
| Media radius |24px|2px|
| Headline |56px|76px|
| Copy/media center mismatch |93.62px|less than .02px|

Wide Header uses shared72px cap and38–48px logo, retaining existing short-height
10svh cap. Compact Header remains unchanged. About uses the exact same Header
block token for usable height; at1080px each story/sticky stage is1008px, content
center576px. Media geometry and mascot coordinates remain fixed across stories.
Outer margins are symmetric, gutter fluid, media dominant, copy bounded42rem.
Mission spacing is tightened only on wide layouts so all localized stories fit.
Short heights/text expansion can naturally extend copy without clipping it.

Base colors: About #fff0c2, Vision #dff3fc, Mission #d4eed5. Only background-color
transitions700ms cubic-bezier(.4,0,.2,1); existing observer updates active state.
geometry33 repeats at the previous fluid scale, static50%50%, pseudo-element
opacity12%. No second motif, crossfade, blur, drift, canvas or background motion.
Reduced motion sets duration0s. Existing media crossfade is unchanged.

Owner confirmed decorative mascot: existing cwo2.webp (3250bytes), configured
without changing any prior URL, lazy image in the stage.32–48px, anchored at
gutter/media center line, outside video content; CSS mirrors it for RTL.
Global cursor assets/controller/behavior are unchanged. No new image created.

## Same-viewport screenshots
Each row provides before and after at1920x1080:

| View | Before | After |
|---|---|---|
| Hero→About entry |[before](about-polish-before-entry.png)|[after](about-polish-after-entry.png)|
| About |[before](about-polish-before-about.png)|[after](about-polish-after-about.png)|
| Vision |[before](about-polish-before-vision.png)|[after](about-polish-after-vision.png)|
| Mission |[before](about-polish-before-mission.png)|[after](about-polish-after-mission.png)|
| Header/Nav |[before](about-polish-before-header.png)|[after](about-polish-after-header.png)|
| Dropdown |[before](about-polish-before-menu.png)|[after](about-polish-after-menu.png)|

All three story screenshots were visually inspected. Existing Header concealment
still follows Hero visibility, so it can be concealed in later story frames.

## Tests / evidence
- PHP About/V2/translation parity:15 tests/142 assertions PASS.
- Active V2/About Node contracts:14 PASS.
- Browser:4 composition/Header tests and5 existing lifecycle/fallback tests PASS.
- Composition:54 ID/EN/AR wide story cells +18 menu cells; center alignment,
  unclipped submenu copy, media dimensions, fixed mascot and RTL direction PASS.
- Existing matrix:30 tier/locale cells,360px minimum and1023/1024/1180/1181
  boundaries, narrow media→text order, no overflow, reduced posters PASS.
- Native Header pointer dropdown/unique underline, independent Language modal,
  Escape and sound on/off PASS; no Header logic changed.
- About/Mission modal, full src reset, hidden/offscreen pause/resume, single
  preview playback, no ordinary full-video request, no legacy Vision request PASS.
- Normal color samples confirm intermediate colors and final sky color; only
  background-color700ms changes, texture position/transform/filter remain static.
- npm run build, npm run check:structure (262 files), Pint and git diff --check PASS.
- Full PHP:319 tests,173 passed,71 failures,75 errors. Identical failure/error
  names to prior About baseline; no new failures. Existing legacy migration debt.

Evidence: about-polish-before.json, about-polish-after.json,
about-polish-locales.json, about-polish-color-motion.json,
about-polish-header-interactions.json, about-polish-source.json,
about-polish-full-php.json; updated About lifecycle/responsive/fallback evidence.
65 protected sources (all JS controllers, Hero CSS/DOM, Header DOM, lang) are
byte-identical to entry. JS bundle remains index-Bl3sYOf4.js. Media config and
presenter are exact entry snapshots plus the one decorative mascot field/key.

## Exact changed files for this correction
Implementation:
- resources/css/foundation/tokens.css
- resources/css/sections/header.css
- resources/css/sections/about.css
- resources/views/landing/about.blade.php
- config/media.php
- app/View/Presenters/LandingAboutPresenter.php

Tests:
- tests/Feature/LandingAboutV2Test.php (decorative image contract)
- tests/Unit/AboutBrowserRuntime.test.mjs (texture assertions read pseudo-element)
- tests/Unit/AboutCompositionRuntime.test.mjs (new visual/runtime proof)

Docs:
- docs2/README.md
- docs2/protocols/working-contract.md
- docs2/blueprints/homepage-v2-about-polish.md
- docs2/proof/about-status.md (marks prior checkpoint historical)
- docs/architecture/UI_UX_CURRENT_STATE.md
- this docs2/proof/about-polish-status.md

New artifacts: all12 before/after PNGs and seven JSONs named above.
Regenerated regression artifacts: about-responsive.json, about-lifecycle.json,
about-fallbacks.json, about-en-desktop.png, about-ar-desktop.png,
about-ar-mobile.png. Prior uncommitted About files outside this list were preserved.

Run against the existing isolated local SQLite proof server and system Chromium
on CDP9224 (setup in about-status.md):

```bash
ABOUT_BROWSER_URL=http://127.0.0.1:8123 node --test --test-concurrency=1 \
  tests/Unit/AboutCompositionRuntime.test.mjs tests/Unit/AboutBrowserRuntime.test.mjs \
  tests/Unit/AboutBrowserFallbackRuntime.test.mjs
```

## Remaining visual evidence / next valid step
No observed composition gap in declared profiles. Physical devices, other browser
engines and manual screen-reader review were not tested here; no such result or
Lighthouse/field performance claim is made. Owner can review local before/after.
