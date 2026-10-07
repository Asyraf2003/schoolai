# About label + sharper shadow proof — 2026-10-06

Scoped PASS, local feat/home-v2-about. No push/merge/R2/dependency changes.
Only production change: resources/css/sections/about.css.

Labels now use fluid15.2–17px instead of12.8px, with a small horizontal rule
after the translated text. Two intrinsic equal grid columns match line length
to each label. CSS direction mirrors the arrangement naturally for Arabic.
At1920, text/rule widths: About67.64px, Vision67.59px, Mission83.23px.
The rule is decorative, with empty pseudo-element content; DOM/lang unchanged.

Shadows are sharper and stronger: less blur and higher opacity in both existing
contact/cast layers. Physical offsets stay positive X/Y, casting right/down from
upper-left light. Shadow scale remains fluid. No all-around halo or animation.
Headline, body, media dimensions, stage alignment, palette and behavior unchanged.

[Before Vision](about-label-before-vision.png) ·
[After Vision](about-label-after-vision.png) ·
[After About](about-label-after-about.png) ·
[After Mission](about-label-after-mission.png).
Vision and Arabic mobile were inspected visually.

Validation: nine browser tests PASS in Chromium153/Linux;54 wide three-locale
story cases plus18 menus,30 tier/locale cells including360px; line/text width
agreement and native RTL, overflow, geometry, media/modal/network/fallback PASS.
PHP15/142,Node14,build/structure262/diff PASS. Full PHP319/173 passed/71 failures/
75 errors unchanged; no new failure/error names. All protected source hashes match.

One test originally scrolled mobile Vision to-.125px while the next Mission frame
was fully visible. The accepted most-visible observer therefore selected Mission.
The test now centers intended media before asserting activation, removing the
ambiguous two-frame viewport; no production observer/lifecycle code changed.

Test edits: AboutCompositionRuntime.test.mjs captures/asserts label geometry;
AboutBrowserRuntime.test.mjs centers the mobile target. Proof artifacts use the
about-label prefix; source/full-PHP JSONs record actual results. Prior type proof
is preserved. Other-engine/physical-device/performance certification unclaimed.
Next valid step: owner reviews the local result.
