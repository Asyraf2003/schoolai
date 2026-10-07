# About fluid typography, cursor-only mascot and shadow — 2026-10-06

Latest small refinement of label size/line and shadow sharpness:
[current proof](about-label-status.md). Headline/body and accepted layout below
remain unchanged; earlier shadow parameters describe the previous checkpoint.

Scoped status: PASS. Local feat/home-v2-about; no push, merge or R2 object changes.
Current blueprint: ../blueprints/homepage-v2-about-type.md.

## Result
Headline remains substantial beside the accepted large media display, with a
slightly lower fluid size curve and weight680. Body gains a little scale and a
darker neutral tone,1.55 leading, closer spacing and a controlled reading measure.
Sizes use clamp,rem andvw. The browser samples below document scaling rather than
define separate viewport layouts. No manual breaks, new copy or locale forks.
Current Arabic Cairo line-composition adapter remains intact.

| Viewport width | Headline | Body |
|---|---:|---:|
|1024|44px|16.99px|
|1181|47.89px|17.27px|
|1280|50.56px|17.44px|
|1536|57.47px|17.89px|
|1920|67.84px|18.56px|

At1920 the former headline/body were76/18px; current paragraph gap15.84px instead
of24px. Vision flows naturally into two headline lines, with the translated text
unchanged. All three stories retain centered copy/media and the same873x491 frame.

The decorative figure next to media is removed, including its sole-purpose
config/presenter field and CSS. Global cursor assets and controller are untouched.
Both shadows use positive physical X/Y offsets, negative spread, and a fluid unit.
They cast below/right, leaving the upper/left edges clean and implying upper-left
light. This physical light direction remains the same in RTL. No shadow animation.

## Screenshots
Same1920x1080 EN/reduced-motion poster profile. Before images are exact copies of
the previous tested checkpoint, whose source hashes were verified before editing.

| State | Before | After |
|---|---|---|
|About|[before](about-type-before-about.png)|[after](about-type-after-about.png)|
|Vision|[before](about-type-before-vision.png)|[after](about-type-after-vision.png)|
|Mission|[before](about-type-before-mission.png)|[after](about-type-after-mission.png)|
|Entry|[before](about-type-before-entry.png)|[after](about-type-after-entry.png)|

After About and Vision were inspected visually; DOM proof covers all three stories.
Header and menu captures remain available with the same about-type prefix.

## Validation
- PHP About/V2/translation parity15 tests/142 assertions PASS.
- Active Node contracts14 PASS; four composition/Header tests +five existing
  browser lifecycle/fallback tests PASS in system Chromium153/Linux.
-54 wide story cases ID/EN/AR and18 menu cases PASS; both type sizes demonstrably
  grow across widths.30 tier/locale cells PASS, including360px minimum, interleaved
  mobile content, no overflow, short height, RTL and200% font expansion.
- Same stable media dimensions/centers. No decorative mascot DOM/data remains.
  Shadow runtime offsets are both positive. Header interaction/color transitions
  and media/modal/full-source unload/network/lazy behavior remain proven.
- npm run build, npm run check:structure262, Pint and git diff --check PASS.
- Full PHP319 tests/173 passed/71 failures/75 errors: no new failure/error names
  relative to about-polish-full-php.json. Existing legacy migration debt remains.
-68 Header/Hero/all-JS/lang/Arabic/cursor source hashes unchanged. JS bundle still
  index-Bl3sYOf4.js. Media config changed only by deleting the obsolete mascot key.

Evidence: about-type-before.json, about-type-after.json, about-type-locales.json,
about-type-color-motion.json, about-type-header-interactions.json,
about-type-source.json, about-type-full-php.json; standard About regression proofs.

Implementation files: resources/css/sections/about.css,
resources/views/landing/about.blade.php, app/View/Presenters/LandingAboutPresenter.php,
config/media.php. Tests: LandingAboutV2Test.php, AboutCompositionRuntime.test.mjs.
Docs: README/current protocol, accepted blueprint, this report and current ledger;
previous polish report marked historical. Existing unrelated work preserved.

Run against the existing isolated proof server/CDP browser:

```bash
ABOUT_BROWSER_URL=http://127.0.0.1:8123 node --test --test-concurrency=1 \
  tests/Unit/AboutCompositionRuntime.test.mjs tests/Unit/AboutBrowserRuntime.test.mjs \
  tests/Unit/AboutBrowserFallbackRuntime.test.mjs
```

Other browser engines, physical devices, manual accessibility and field performance
are not claimed. Next valid step: owner reviews the local visual correction.
