# Desktop Sound alignment and water-tap proof — 2026-10-06

Scoped PASS; local feat/home-v2-about. No push/merge/R2/dependency changes.

Desktop Sound now shares nav control font size/weight/height and label baseline
metrics. Before weight400, Sound span y25.59/h19; after weight600, y21.703/h28.594,
exactly matching the adjacent menu label. Font remains fluid; at1920 both15.2px.
Transparent baseline reservation keeps Sound free of the nav's yellow underline.
Desktop side padding removed; overall Header height/Logo/menu geometry unchanged.

One brief transparent water ripple appears only on Sound click, with two concentric
current-color rings. Pointer uses click position; native keyboard click uses center.
Native CSS transform/opacity runs680ms once. One temporary aria-hidden span is
removed after completion; repeated taps replace it. No asset, canvas or new RAF.
Reduced motion skips it. Hidden/concealed/compact/resize/pagehide/dispose cancel it.
Existing Hero audio port and native click semantics remain unchanged. Compact
wave math/lifecycle are byte-identical after excluding the new ripple wiring.
Desktop effect also works when a canvas context is unavailable.

[Before](header-sound-before.png) · [Ripple capture](header-sound-ripple.png).
The ripple capture uses a real pointer click during normal native Header motion;
menu-roll letters can be mid-transition in that frame. Screenshot inspected.

Validation:
-33 ID/EN/AR viewport cases including360/390/640/768/1024 and1180/1181 boundaries,
  wide/tall orientation,1280/1536/1920. Desktop font/weight/line/y/height equality,
  transparent border, no overflow; compact wave display intact PASS.
- Three browser Sound tests PASS: real Hero audio mute/unmute, pointer/Enter,
  repeated clicks, animation-end/hidden/reduced/resize cleanup, no-canvas fallback.
  Pagehide/BFCache/dispose lifecycle signals were synthetic and are labeled as such.
- Nine existing About/Header/lifecycle/fallback browser regressions PASS.
- Node14 and PHP15/142 PASS; build/structure262/diff PASS.
- Full PHP319/173 passed/71 failures/75 errors: no new names vs previous baseline.
  Existing legacy migration debt remains outside this scope.
-68 protected Header state/adapter/DOM, Hero/About/locale/Cursor/media sources
  unchanged. Existing audio request listener preserved. No extra audio owner.

Production files: resources/css/sections/header.css,
resources/css/sections/header-sound.css, resources/js/sections/header-sound.js.
New test: tests/Unit/HeaderSoundBrowserRuntime.test.mjs.
Proof: header-sound-before.json, alignment.json, interaction.json, fallback.json,
source.json and full-php.json under the same header-sound prefix; existing
regressions captured with header-sound-regression prefix.

Run against the existing isolated local proof server and Chromium CDP9224:

```bash
ABOUT_BROWSER_URL=http://127.0.0.1:8123 node --test tests/Unit/HeaderSoundBrowserRuntime.test.mjs
```

Other engines/physical devices/manual accessibility/performance certification
are unclaimed. Next valid step: owner reviews local Sound presentation.
