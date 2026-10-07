# MAP-V2-08 — Program heading + navigation correction

Blueprint: OWNER_ACCEPTED → PROVEN for local surface contracts, 2026-10-07.
Work status: PASS scoped proof; FAIL full repository gate; physical-device proof
BLOCKED_BY_MISSING_EVIDENCE. See ../proof/nav-program-status.md.
Branch feat/home-v2-about; HEAD08039bba; freshly fetched main a44d484f.
Channel Terminal Codex; local edits/proof only, no push/merge/dependencies.

## OWNER_RAW

> "judul itu bagian bawah bergeser sedikit ke tenngah"
> "itu animasi bukan by scroll"
> "ketika sub menu di klik saya harap percepat ui nya 2x lipat"
> "kegagalan masa lalu jadi data antisipasi saat ini"

## OWNER_CONFIRMED — implementation translation of explicit constraints

Program: lower heading line shifts slightly inward after reveal, time-based,
not scrubbed by scroll; inspect old source/docs and retain localized content.
Blue Program→Values belongs to future Values work, deferred explicitly.
Desktop nav: transparent Hero header stays white even with submenu open;
white header background makes main labels dark, including open/reverse states.
Center Latin submenu chevron against text; restore old Latin/Arabic families.
Submenu UI2× faster on desktop/tablet/phone. Prepare submenu media beforehand
and retain it through repeat opening. Real phone production may differ from
desktop inspection; do not claim physical-phone proof from emulation.

## FACT / GAP

Header CSS combines data-open with data-scrolled, forcing white/dark on Hero
click. Color transition220ms can lag an immediate gradient→white surface.
Label reserves .25em+2px under text; SVG is centered against the whole label box.
Accordion duration1440ms in both desktop and compact modes.
Media img is lazy inside closed details; no Header-specific warm/decode owner.
Current Header inherits Inter even in AR; old adapters use Inter ID/EN, Cairo AR.
Old editorial Program CSS has middle-shift clamp1.125rem/3.25vw/4rem, mirrored
for RTL; tablet smaller. Current Center Split has only vertical transforms.
AR heading source is one complete word; no second line/content is invented.
Owner supplied production https://almustaqbal.sch.id/ and Redmi12; both Android
and iPhone matter. Old production is anticipation data. Physical devices are
unavailable locally; emulation must remain explicitly labelled.

## AI_TRANSLATION / DECISION

Two sequential bounded steps. Preserve one DOM/data source and CSS final geometry.
1. PROVEN across four engines: vertical reveal then autonomous inward clip transform;
   old proportional shift/easing, no JS timeline/scroll math. Lower ID/EN line;
   single AR line with mirrored movement. Phone gets a small fluid shift per
   explicit owner request. Reserve available width so shifted text cannot overflow.
2. PROVEN Header: separate desktop open from background trigger; compact open
   retains accepted white overlay. Text and bar tone switch together. Chevron
   uses the same reserved underline extent as label, not an arbitrary pixel patch.
   Narrow Cairo Header locale adapter; no type changes to Hero/About.
   Accordion1440→720ms, preserve sequential/reversal/accessibility policy.
   One Header media adapter primes existing image nodes on media-capable widths
   >=768, low fetch priority, decodes once, keeps same src/node through close/open.
   Phone media remains absent; no request to warm invisible phone menu images.

## AI_ASSUMPTIONS

No new art direction/copy/controller assumption. Mobile test393×873,DPR2.75,
Android13 UA is an approximate laboratory profile, not supplied device/OS data.
Owner supplied Redmi12 and production URL, and asked for both Android/iPhone.
Application code does not branch on these UA strings. Physical-device proof
remains separate; cached native image requests are not called cold payload.

## LEGACY_REFERENCE

Read-only resources_old/css/pages/welcome-editorial-program.css for inward shift;
resources_old/css/surfaces/home/section-display-heading.css for heading width;
resources_old/css/public-latin-inter.css and arabic-typography-base.css for families.
Existing Program blueprints/source supply Center Split and detail contracts.
No legacy CSS/JS is imported into runtime.

## OWNERS / SCOPE

Editable: Program heading CSS and narrow RTL variable; Header CSS, accordion,
Header mounting, new Header-media adapter, narrow AR adapter, Header Blade media
hooks, focused existing/new tests, this map/proof/current ledger/entrypoints.
Read-only: resources_old, all lang/DB/media config, presenters/content/URLs,
About/Hero/Program cards/detail/background, Sound/cursor/Language internals.
Forbidden: Values, blue compositor, future line, legacy imports, dependencies,
routes/admin/auth, marketing rewrite, remote publication, unrelated cleanup.

## SIX-TIER / LOCALE CONTRACT

| Tier | Result |
| --- | --- |
| XS360–639 | small bounded heading shift; compact720ms submenu, no media |
| SM640–767 | same responsive topology; compact no media |
| MD768–1023 | smaller proportional shift; prepared media in existing compact grid |
| LG1024–1279 | accepted landscape desktop/portrait compact through1180 unchanged |
| XL1280–1535 | desktop tone/media, lower heading shift bounded by content width |
| 2XL1536+ | same fluid topology and bounded shift, no fixed device coordinates |
Test1180/1181 portrait,1023/1024 landscape, and all global tier boundaries.
ID/EN Inter LTR; AR Cairo RTL. Font/text/content remain localization-owned.
Semantic copy order unchanged; AR word joining not split. Locale POST/reload intact.

## MOTION / ACCESSIBILITY / LIFECYCLE

Heading sequence: hidden→opposite vertical reveal900ms→inward shift1450ms.
Leaving resets/cancels delayed movement; reverse reentry restarts finite sequence.
Reduced/no JS/missing IO: readable final shifted composition without animation.
No GSAP or continuous scroll engine for heading.
Normal horizontal transitions require active reveal and no-preference. Otherwise
viewport changes resolve the final pose immediately. A reduced-motion specificity
conflict was reproduced, removed at its owner and covered by180 tier/locale cases.
Header open/close720ms; replacement group still closes prior group first;
rapid reverse/resize/reduced/dispose continue or settle existing policy.
Background state remains independent of submenu state on desktop.
Existing Escape/focus/highlight/Language/Sound/cursor contracts preserved.
Media readiness is actual load/decode, not a guessed timeout. Failed media retains
localized fallback caption; waiting media avoids a title flash. No menu waits on
media to expose links. Dispose removes listeners/ignores late decode; BFCache keeps
native loaded images. No src rewrite, clones, per-open fetch or global preload.

## PERFORMANCE / PROOF

Owner explicitly requests early menu-media preparation; wide-profile requests
are measured, low priority; phone has no warm-image payload. No100/field claims.
Run diff/structure/build/Pint/focused PHP/Node/full PHP baseline comparison.
Browser Chromium/Edge/Firefox/WebKit: six tiers/three locales, tone transition
frames, Hero open white, section/reverse white-bar dark, label/arrow centers,
720ms WAAPI, rapid switch/reverse/keyboard/touch, prepared/repeat-request media,
failure/delayed decode, resize/portrait/landscape/reduced, no overflow.
Program: stage timestamps, lower line movement after Y settles, RTL direction,
scroll held fixed, reentry/cancel, reduced/no-JS final pose, snapshots.
Physical Safari/phone production remain separately BLOCKED_BY_MISSING_EVIDENCE.

### Source-grounded locale correction
Header data audit found three localized label fallbacks still literal in
PreparesNavbarMediaMenus. Owner's explicit no-hardcoded-copy requirement authorizes
moving these exact existing strings into shared.navbar.labels in ID/EN/AR and
using the injected Translator. No copy rewrite, query/route/media changes.
Only this trait and those three small language dictionaries leave read-only scope.
One old Gallery contract required these literals inside the PHP trait. Keep its
query/route assertions and exact ID/EN/AR labels, but assert dictionary ownership;
V2 response tests also assert the exact rendered labels and translation override.
No legacy Program assertions are loosened; the literal-PHP constraint conflicts
with the owner's explicit language-source requirement.
Owner clarifies old production failure is anticipation data, not a legacy-fix task.
Fresh production HTML uses welcome-* assets; V2 stays the only editable frontend.

## NEXT VALID STEP

No further implementation in this bounded map. Local source/build/Pint/PHP focus
and20 browser tests PASS;16 pure JS tests PASS. Full PHP retains71 failures and75
errors from the accepted baseline, with no new names. Physical Redmi12/iOS,
screen-reader/real zoom and production-cache conditions remain unverified.
Owner review may use the linked screenshots. Values/blue transition/line remain
separate future work; no push, merge, publication or dependencies added.
