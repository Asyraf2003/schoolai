# MAP-V2-10 — Menu continuity and Program heading correction

Blueprint: OWNER_ACCEPTED / IMPLEMENTING, 2026-10-07.
Branch feat/home-v2-about HEAD08039bba; freshly fetched main a44d484f.
Channel Terminal Codex; local only, no commit/push/merge/dependency changes.

## OWNER_CONFIRMED / GOAL

Exactly three targets: compare/correct desktop menu visual parity, reduce visible
Latin Program inter-line gap to about one third, section heading motion760ms
(720–780 accepted) with editorial ease. Latest instruction replaces Map09's
desktop Hero-open transparent state and5800ms ID heading travel.

## FACT / GAP

Old welcome-mega-menu owns a shared nav-mega-surface; has-open-menu lights Header
and full-width panel. V2 headerSurface excludes desktop open panels from light
state, deliberately following the now superseded owner contract. Separate V2
panel white constant and Header gradient therefore remain while open over Hero.
Panel top100% already follows Header geometry; measure actual painted seam.
Heading grid gap.12em, wrapper padding.08em each, leading.9. Visible glyph gap
requires actual font/ink measurements, not treating CSS gap as the entire gap.
Section vertical transform900ms; horizontal1450ms×IDfactor4=5800ms after900ms.
Detail kinetics has its own unchanged GSAP owner. No timing confusion remains.

## SCOPE / OWNERS / PROTECTED

Editable: Header state policy/shared surface token only if baseline confirms;
Program heading wrapper/line geometry and section reveal CSS, remove obsolete
heading timing multiplier; focused existing/new tests, map/ledger/proof.
Read-only legacy named Blade/CSS/JS references, content/media/lang/DB/presenters,
About/Hero, Header size/logo/media geometry, Program card/grid/detail/Codrops/
modal/media/cursor/future anchors. No Values, blue boundary or scroll line.
No markup/controller redesign, new background sheet or layer escalation.

## DECISION / SEMANTIC / ADAPTERS

Preserve fluid V2 proportions: Header72; logo38 at1440/48 at1920; shared token inherited by panel and
Header light field. Open/rendered panel holds light state until closing finishes.
Hover alone never activates it;48/24 scroll hysteresis retained. Existing compact
navigation and accepted landscape mode unchanged. Full-width current geometry
is retained unless actual visual proof demonstrates another regression.
One semantic h2/copy DOM, ID/EN Inter and AR Cairo. Latin gap corrected through
mask-safe line geometry without shrinking font. AR one-line leading/pose unchanged.
Keep existing ID4/EN2/AR0 distance; execute section transforms within one760ms
entrance, no delayed multi-second travel. Cards900ms and detail timing untouched.
No JS/reduced motion remains readable at final pose; reverse/reentry cancels via
existing observer/CSS. No animation library or scroll owner added.

## SIX TIERS / BROWSER / PERFORMANCE

360/640/768/1024/1280/1536 retain existing CSS composition; heading measured in
ID/EN/AR at390/768/1440/1920 plus tier/boundary regressions including1180/1181.
Current Chromium/Edge/Firefox/WebKit: normal/reduced/no-JS, keyboard and touch.
Desktop top/hover/open/close/scrolled/scrolled-open/rapid/reverse/visible-open.
Measure Header/panel rects, styles, painted pixels at join and independent motion.
Fonts/media/nodes unchanged; no extra payload/RAF/timer/library. Source<200lines.

## ACTIVE STEP / PROOF

1. DONE: read-only visual baseline; exact gap/ink/size/timing, menu seam pixels,
   screenshots and shared-state difference. No product editing before baseline.
2. DONE: minimal menu correction and physical/state regression proof.
3. DONE: heading gap and760ms unified section entrance, proof before/during/
   settled; existing focused tests, PHP, diff/structure/build/Pint/full PHP.
Record durable proof before next atomic step. Full suite compared with Map09's
known failures; no unrelated remediation. Native device certification not claimed.

## NEXT VALID STEP

Scoped contracts PASS; full repository gate FAIL with unchanged baseline names.
Native Safari and baseline decorative paint after fast resize remain evidence gaps.
No active code step; blueprint is not marked full-DOD PROVEN. Report:
../proof/menu-heading-status.md. Owner/local terminal: review three corrections.
A separate decorative-paint audit needs its own scope; no hidden patch added.
