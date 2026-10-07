# MAP-V2-07 — Program migration

STATUS: OWNER_ACCEPTED / IMPLEMENTING owner visual correction.
Release certification remains BLOCKED_BY_MISSING_EVIDENCE; full repo tests FAIL.
Main freshly fetched: a44d484f4cd8bc532514f0152e4423a2fcdb9781.
Local branch feat/home-v2-about; HEAD 08039bbad2f59ba37cac996bc15730fbee7d52e9.
Channel: Terminal Codex. Local edits/proof only; no push, merge or dependencies.

## OWNER_RAW
Target only: migrate legacy Homepage Program to Homepage V2 after accepted About.
Preserve localized copy/media, Center Split, staircase, kinetic open/close, detail
geometry, Back above title, RTL/LTR. IO/CSS for heading/formation; GSAP allowed
only for detail and lazy on intent. Do not implement Values or future scroll line.
"semuanya soal skala"; "hexagonal yg sangat modular"; "semua by data";
"dukung edge chromium safari firefox"; reports use simple Indonesian.

## AI_TRANSLATION / GOAL / IMPACT
One semantic Program section; CSS owns final geometry. Pure interaction policy
with browser/dialog and optional animation adapters; composition root wires ports.
About remains byte-identical unless seam proof needs an adjacent-only correction.

## AI_ASSUMPTIONS
No new marketing copy, art, engine or cross-surface owner is inferred.
Technical interpretation: fluid values and intrinsic content sizing replace fixed
coordinates; borders, aspect ratios and minimum touch sizes remain invariants.

## OWNER_CONFIRMED / DECISION
The current detailed implementation request accepts the bounded architecture.
Reference source, not historical counts, determines selection: TQ KH LC SJ TS IT FD SC.
8 cards; all 19 source records remain locale-owned and unchanged.
Arabic heading source has one complete word; preserve one line and its below
reveal rather than inventing a second line. ID/EN keep two opposite block reveals.
Native disclosure fallback preserves descriptions when JS cannot mount. Native
dialog supplies background inertness/focus containment for enhanced cinematic UI.
GSAP 3.7.1 is the existing CDN authority, loaded only on card intent; no npm change.

## SCOPE / OWNER MAP
Editable: new Program presenter, Blade, section CSS, observer/state/dialog/kinetic
JS; minimal composition additions in HomeController and landing/CSS/JS indexes;
narrow Program Cairo adapter; V2 tests; this map, proof and current-state entries.
Read-only: legacy Program/composer/locale/media/reference tests; About/Header/Hero/
cursor/locale lifecycle and current styles; existing config/font foundations.
Forbidden: resources_old edits/imports, Values, graphics/line, unrelated routes,
dependencies, About story/media/modal/observer/type changes, remote mutations.

## LEGACY_REFERENCE / FACT
featured-programs Blade, program-journey CSS/JS and program-cards entry audited.
HomeProgramComposer selects 8 cards and configured CF school-life images.
Legacy actual CSS: phone 2 columns, tablet 4 staggered columns, desktop 4x2 ascending
staircase; 4:3 media. Detail uses top20svh/80svh, overlapping dominant title,
Back immediately above, copy30% and flexible media. Length tiers <=12/<=20/>20.
Codrops KineticTypePageTransition source checked: scale2.7, rotation90deg,
directional line travel, stagger .04, 1.4/2.5s type transitions and reverse return.
V2 active owners: local Inter/Cairo, page gutter clamp, About geometry33 repeat
at12%, final Mission mint#d4eed5. Mandatory docs exist; .ai/rules absent.

## BLUEPRINT / SEAM
Program owns an ordinary-flow entry band18svh. Fade existing About geometry33
across its final12svh with a mask; mint resolves to white through the entry band.
Unsupported :has uses the configured texture in the band as the narrow fallback.
No giant section, scroll math, sticky handoff, timeout or animation library.
Heading follows the band; field outer margins share --v2-space-page.

## SIX-TIER CONTRACT
| Tier | Geometry / content / controls |
| --- | --- |
| XS360–639 | Two compact columns, 4:3 media, natural copy; single-column detail |
| SM640–767 | Two wider columns with small proportional stagger; detail scrollable |
| MD768–1023 | Four deliberate staggered columns as legacy where text fits |
| LG1024–1279 | Four columns/two staircase rows; overlapping detail title/media |
| XL1280–1535 | Same fluid staircase; dominant length-aware title; no offset patches |
| 2XL1536+ | Same topology, bounded V2 field and readable copy; scalable media |
All tiers use logical grid and gaps; no JS final-position calculation. Detail
uses proportional20/80 viewport region, intrinsic rows, and scroll for short
height/text expansion. Test both sides of changed boundaries and interiors.

## MOTION / LIFECYCLE / LOCALE
IO observes heading and stable card boxes. CSS transforms child reveal wrappers;
once seen, retain visibility to prevent hiding focus/content on reverse scroll.
No timers drive formation; reduced motion and missing IO show all content.
idle -> preparing -> opening -> detail -> closing -> idle; Escape may cancel
preparing/opening; repeated click cannot duplicate owners. Suspend/dispose cancels
animation and restores disclosure, focus and overflow. BFCache can remount/resume.
Detail copy/media reveal uses GSAP only within bounded open/close timelines.
Library/import failure resolves to usable static dialog; no semantic gate waits
forever. Preserve ID/EN/AR reload lifecycle and mirror directional type/Back only.
Cairo adapter normalizes Arabic tracking/leading; no split component/locale tree.

## FUTURE BOUNDARY / PERFORMANCE
Stable ordinary-flow markers: data-program-story-start, data-program-story-end,
data-program-values-seam, data-values-entry-anchor. No Values controller/content,
SVG, path, shared RAF or exposed transform math. Line will follow these boxes.
Explicit card media aspect/dimensions and lazy decoding. Only selected detail
media hydrates; no preload. No WebGL/canvas/global scroll/frame sampler.

## PROOF / GAP
Run diff/structure/build/Pint, focused V2 PHP and JS/browser tests, legacy tests
where applicable, full PHP with baseline comparison; do not weaken legacy tests.
Browser: six tiers/boundaries, locales, reduced/normal, Back/Escape/focus/inert,
scroll/resize/short height/zoom/failed library/noJS, all requested screenshot views.
Engines: Chromium, available Firefox/WebKit/Edge; native Safari is separately
labeled. Physical devices, screen readers, field CWV/PSI are unproven until measured.
Default PHP lacked enabled SQLite/GD; temporary CLI scan config resolved test access.
PROOF: 228 layout cells,480 reduced details,36 kinetic cases across four engines;
focus/inert/Back/Escape/native wheel,23 browser tests and16 pure Node cases.
Focused PHP23/285 PASS; repo328/182 PASS/71 FAIL/75 errors, same baseline failures.
471 protected sources identical; five authorized entry/Program adapters changed.
Gap: About ID label overflows at390 with200% text; Hero cold CLS~.55 also occurs
with Program removed in browser-only counterfactuals. Protected owners unchanged.
Evidence and exact files: ../proof/program-status.md; ../proof/program-gates.json.

## ACTIVE STEP / NEXT VALID STEP
OWNER_RAW correction: background type missing; color transition missing; use
Codrops radius, remove underline and gently enlarge media/copy on hover;
heading needs slight inward shift; progressive appearance/disappearance missing.
FACT: type lives inside closed dialog; tiny .2/.4rem radii differ from Codrops
12px/17px reference; underline is explicit CSS; IO unobserves once revealed.
DECISION: retain one type DOM, transfer its viewport from Program background host
to native dialog and back. Program alone owns the background; no Values runtime.
Restore fluid radius near reference and CSS-only small hover/focus enlargement.
Replace one-shot formation with IO semantic-frontier reveal that resets below
the entry boundary during reverse scroll; retain passed/focused content above it.
Reduced/missing IO show content immediately. No frame sampler or GSAP scroll.
DECISION from active legacy CSS: header90% gives a small symmetric inset from
the wider card field; same logical heading edge and vertical Center Split remain.
Existing white/light-blue detail field now fades over700ms, mirrored on return.
Background type fades spatially across18svh entry/8svh exit masks without scroll JS.
Runtime GAP resolved: an above→below native jump can leave IO's non-intersecting
state unchanged (reproduced AR Hero return). Reconcile visibility once on native
scrollend, resize and restored-detail event; eight bounded reads, no frame loop,
continuous scroll listener, final-position mutation or Values dependency.
GAP: exact additional color stage and possible horizontal heading choreography
await owner clarification. No Program→Values color compositor is inferred.
Six tiers, localized content/RTL, grid final positions and protected owners remain.
PROOF: before/after computed styles; all engines, locales, widths; normal/reduced,
reverse fade, hover reset, one type plane through open/close/cancel; existing gates.
PROGRESS: named source corrections and28 browser tests PASS; exact additional
color stage/horizontal choreography remains BLOCKED_BY_MISSING_EVIDENCE.
Evidence ../proof/program-feedback-status.md and program-feedback-gates.json.
ACTIVE: bounded correction proof complete; no inferred additional choreography.
NEXT channel owner/local terminal: clarify color stage/heading motion if needed.
No publication. Values/line and protected-owner fixes require a new bounded scope.
