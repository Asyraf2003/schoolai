# MAP-V2-14 — Values cards integration

Status: CLOSED for scoped V2 Values cards; merged main 34b017969bf0ca0afcfebef675f17d3782e8d9d9 via PR #77.

## VERIFIED — 2026-10-08

- [V2 cards scoped workflow](https://github.com/Asyraf2003/schoolai/actions/runs/37739051161) PASS: composer install, npm ci, structure check, Vite build, focused Laravel tests, Chromium cards test.
- Chromium covered ID/EN/AR × widths 360/768/1024/1280/1440/1536, four semantic cards, non-overflowing local cards, scroll-reversible CSS3D, physical grid counts, and text fitting card bounds. No-JS fallback PASS after stylesheet load; reduced-motion static PASS.
- Legacy [Security audit](https://github.com/Asyraf2003/schoolai/actions/runs/37739051097) retains npm dependency advisories (three total: one high/two critical); no package/lock changes occurred in this scope. Do not label full CI green.
- Firefox/WebKit native devices, 200% zoom and short-height physical performance were not executed; retain as cross-browser follow-up, not false PASS.

## FACT

- Baseline main: 0f33fbd (PR #74), 20 alternating background rows at 4.5s.
- Current V2 Values section: 500svh scroll-driven white SVG path, two-line heading, shared Program/Values type plane.
- Legacy Values source: four localized Q/I/G/N text cards with CSS3D front/back; old 670svh scroll controller must not be imported.
- Source of truth: lang/{id,en,ar}/home.php under nilai_sekolah.items.

## DECISION

Render four semantic article cards via LandingValuesPresenter with escaped localized fields and 01–04 identifiers.
Move front/back identity and ornament into V2, not legacy Three.js/controller.
Use exactly one existing 500svh scroll world and line. Line-ready layout overlays cards at distinct responsive positions.
Desktop >=1280px uses four-card sticky deck with progress-driven reversible stack/fan/flip/upright/exit.
Mobile uses four vertically separated cards; tablet 768–1279px uses a 2x2 layout.
Scroll progress on cards is set by a small controller using requestAnimationFrame on scroll only; no new dependency or continuous loop.
No-JS, reduced-motion, absent GSAP/IO: articles stay readable in natural grid flow.
No card photos/canvas; one decorative back pattern is reused from canonical media config.

## UNCHANGED

Program dialog and its kinetic type, 20 background lines, 4.5s opposing directions,
Program/Values morph, heading introduction and exact SVG path/draw owner.

## PROOF PLAN

- Run PHP tests: tests/Feature/LandingValuesV2Test.php for 3 locales/4 cards/escaping.
- Run JS syntax/build, scope regression and existing Program/Values Chromium browser suites.
- Check mobile 360,390,640; tablet 768,1024; desktop 1280,1440,1536; ID EN AR.
- Verify 200% zoom, short landscape, RTL font clipping, no horizontal overflow, pagehide/bfcache, reduced motion, missing GSAP, line reversibility, browser engines.
- Current branch contains implementation; no green/browser performance claim is implied without executing these gates.

## OWNER_ACCEPTED follow-up #92 / PR93 — 2026-10-11

OWNER_RAW: entry from below, flip and upward departure must respond at the same
speed; completed cards currently spring/rush upward. Owner explicitly assigns
Terminal Codex takeover after Gallery implementation, preserving AI Web work.
Latest Gallery title must follow actual Values bodies with a constant dynamic gap.
FACT: PR93 adds native pixel exit but still has a time-based spring follower.
Its exact endpoint browser test compares clamped data to unclamped rounded input,
causing CI timeout at1.0; source/lifecycle audit complete before edits.
GOAL: one direct scroll response for entry/fan/flip; native1px/1px upward exit
without a time tail or a transformed-body/native-slot mismatch.
DECISION: preserve all pre-exit pose curves and40% hold. Retain PR93 physical
scroll offset function to place a shorter inner sticky boundary at exit start;
browser supplies the entire native departure, outer365svh timing remains fixed.
One neutral wrapper; no translated exit body. Replace spring follower with direct
progress, retaining existing math API and zero momentum. Same DOM/typography.
SCOPE: Values card wrapper/layout, scroll response/controller, related tests,
existing blueprint/current-state and PR93 workflow. No SVG path/draw, heading,
marquee, Hero/Header/Program/Gallery product edits or dependency/schema changes.
XS360/390, SM640 retain natural1-column; MD768/LG1024 keep2x2. XL1280 and
2XL1536 use the same shorter native sticky boundary. Flow, short height, noJS,
reduced and missing capability keep all four readable cards. ID/EN/AR share the
same physical timing; current Inter/Cairo and RTL text stay unchanged.
PROOF gates: direct stopped/fast/reverse progress; entry/flip response; physical
1px/1px exit before/through release; actual Gallery-body gap in combined runtime;
Chromium/WebKit, all tiers/locales, reduced/short/lifecycle, protected pre-exit
fidelity, hold pacing, diff/structure/build/focused and baseline PHP/Pint.
STATUS: OWNER_ACCEPTED / IMPLEMENTING. Work status BLOCKED_BY_MISSING_EVIDENCE.
NEXT: implement bounded native boundary and renew original PR93 plus combined
Gallery seam proof; merge only relevant PASS, preserve owner merge/stash.

PROOF checkpoint: structure301/build/diff/Pint PASS; pure timing/direct response
5/5 PASS; focused PHP13/264assertions PASS. Native18 locale/width profiles PASS
Chromium/WebKit, including immediate entry/flip, stopped-pose no tail, physical
1px/1px exit/reversal and release. Remaining fallback/fidelity and combined
Gallery body/title seam plus scoped CI pending; no merge claim.
