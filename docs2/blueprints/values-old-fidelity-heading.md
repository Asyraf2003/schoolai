# MAP-V2-15 — Faithful OLD Values cards and autonomous heading

STATUS: IMPLEMENTING. Blueprint OWNER_ACCEPTED by the explicit 2026-10-08 request.
Source main: 2b879e944c93b19244829287ab42d51936f4a04d.
Branch: fix/values-old-fidelity-heading. Channel: Terminal Codex.

## OWNER_RAW

“Gunakan implementasi OLD sebagai sumber kebenaran visual dan animasi.”
“dua baris muncul sempurna → reveal selesai → baris kedua otomatis bergerak ke
tengah dengan timing tetap, terlepas dari perilaku scroll.”
Implement both targets, compare OLD/V2 browser phases, open PR and merge only
after relevant checks PASS. Protect Header/Hero/About/Program/background/SVG.

## AI_TRANSLATION

Restore OLD card geometry, typography, front/back decoration and transform
hierarchy: semantic slot → pose → float → flip → faces. Port the pure OLD
desktop curves and spring, not its cross-section controller. Adapt measurement
and progress to the existing V2 500svh line field. Preserve current heading
reveal directions/easing/durations and final horizontal distance.

## AI_ASSUMPTIONS

No new art direction. Current OLD source wins over historical storyboard
numbers: current flip starts .052 with .012 stagger, .22 duration, −18° overshoot,
upright during flip, exit .895; float is 3s phase-offset. Historical three-bounce
and .38 flip-start descriptions do not match current source.

## OWNER_CONFIRMED

OLD visual and motion fidelity, original ID/EN/AR content; V2 architecture;
existing SVG/background/Program protected; separate branch, PR and gated merge.

## SCOPE

Editable: landing/values Blade, values CSS/card modules, values heading/card JS,
focused browser tests and docs2 proof/map/current-state. Existing values.js may
delegate heading lifecycle; its color calculation stays unchanged.
Read-only: OLD Values sources, translations and presenter. Focused existing
browser assertions may be adapted to actual completion and visibility.

## OUT_OF_SCOPE

Header, Hero, About, Program cards/detail, typography background 4.5s, color
morph, SVG geometry/timeline/runway, dependencies, DB and locale copy.

## LEGACY_REFERENCE

resources_old/views/home/sections/school-values.blade.php; Values CSS/JS family;
docs/architecture/blueprints/2026-08-03-home-values-card-story.md and Values
handoffs. OLD typography owners are archived under resources_old/css; V2 live
owners are foundation/fonts.css, base.css and locale/ar.css.

## FACT / GAP / GOAL / IMPACT

V2 reduced front typography/spacing, removed float, changed back ornament and
replaced shared perspective with per-card perspective. Current heading resets
on scroll and disables transitions above viewport. Goal: restore OLD identity
and eliminate scroll ownership after heading trigger. No new engine required:
OLD CSS3D is active; its Three.js bridge is disabled.
GAP: browser equivalence and all changed lifecycle behavior need fresh proof.

## DECISION / BLUEPRINT

- XS360–639/SM640–767: OLD 84vw single column, gap-limited skew and smooth
  negative180→0 flip at half→full visibility. MD768–1023/LG1024–1279: OLD
  91.6667vw two-column/4.1667vw gap, paired .16→.78 visibility flip.
- XL1280–1535/2XL1536+: OLD 90vw four-column/2vw gap, measured stage center,
  shared height-driven perspective, split→fan/overlapping flip→upright→exit.
- Retain .717 ratio unless real content needs more height; content-fit sizing
  must grow the semantic slot rather than clip text. Short viewport/zoom uses
  natural flow if the deck cannot fit; line geometry remains unchanged.
- ID/EN Inter; AR card-only Cairo500/600/700 from OLD, RTL face content,
  physical Q/I/G/N order and neutral
  card flip unchanged. Heading horizontal sign mirrors existing RTL token.
- Heading idle→revealing→shifting→complete once per page; actual reveal
  animation completion for both lines gates an880ms horizontal animation.
  Scroll only checks the idle trigger. No scroll reset/instant shortcut.
- Reduced/missing capabilities: readable front grid and complete heading.
  BFCache suspension preserves current time; resume continues. Permanent
  disposal cancels owned work and invalidates asynchronous completions.
- Card RAF runs only when visible and spring unsettled; float pauses offscreen,
  hidden or suspended. No canvas/Three payload, dependencies or new assets.

## ACTIVE STEP / EXECUTION

Implement the bounded Values capability, then compare equivalent OLD/V2 phases.

## PROOF

Chromium/WebKit: six tiers plus boundaries, ID/EN/AR, masks/content/overflow,
slow/fast/reverse scroll, stopped scroll, resize/orientation, reduced/no-JS/
missing IO/3D/GSAP/media, locale reload and page lifecycle. Preserve regression
checks for SVG, background and Program. Record screenshots and measured timing.
Run diff/structure/build/focused tests/full PHP; compare full failures against
same-environment baseline. Report actual engines; native devices/field CWV are
not implied. Measure frame work against16.7ms desktop/33.3ms compact profiles.

## GIT

User authorizes this branch/PR and merge to Asyraf2003/schoolai main after scoped
PASS. SSH fetch and authenticated CLI work; issue #78 tracks this scope.

## NEXT VALID STEP

Terminal Codex: implement and prove this Values capability; no other surface.
