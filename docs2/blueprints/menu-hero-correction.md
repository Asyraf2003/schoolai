# MAP-V2-02 — Koreksi Header V2

Latest OWNER_RAW update at the end supersedes earlier geometry/Language decisions.

Blueprint: OWNER_ACCEPTED → IMPLEMENTING, arahan owner 2026-10-05.
Main fetched: `a44d484f4cd8bc532514f0152e4423a2fcdb9781`.
Local branch: `feat/home-v2-hero`; channel: Terminal Codex. No publication.

## FACT → GAP → GOAL → IMPACT
Runtime baseline Chromium 1440×900: all labels have a yellow 2px border;
media is 288×432 (2:3). Source forces four rows and mobile minimum 24rem.
Language summary has no current flag, options are an anchored dropdown.
SVG wave scales/skews; touch capability incorrectly enables it on desktop layout.
Legacy header uses 48px/24px scroll hysteresis, SVG chevron, centered flags.
Goal: correct only Header navigation, language chooser, sound visual.
GAP: final cross-engine/locale/lifecycle/performance proof remains pending.
Typography paths named by historical docs now live in resources_old; active
V2 font/base/locale sources and index import order were inspected.

## DECISION / superseding owner corrections
- Desktop underline only hovered, focused, pressed, current or open menu.
- Compact navigation has no yellow underline, including active items.
- Desktop menu media width:height 3:2; compact rows follow actual link count.
- Whole summary row opens one group; opening another closes the previous first.
- Chevron uses centered SVG geometry with open rotation.
- Current flag visible; chooser centered with existing locale POST forms.
- Sound is text On/Off whenever desktop navigation is active, including landscape
  tablet. Compact navigation alone uses one decorative line↔wave canvas.
- No directional hover morph. CSS owns colors, size and circular surface.
- Retain accepted 1024px landscape / 1181px desktop mode boundary, test both,
  including portrait 1180/1181. Burger diameter must not exceed logo diameter.
- Text stays white at top; dark in white open/scrolled header only. Legacy
  48/24 scroll hysteresis is restored independently of Hero concealment policy.

## Scope / owners
Editable: resources/views/landing/header.blade.php; Header CSS/JS and owned
Sound/Language modules; focused Header tests; this blueprint and current ledger.
Read-only: resources_old, locale/config/media data, Hero, Vite composition.
Forbidden: other surfaces, admin/auth, dependencies, existing unrelated edits,
main/PR mutations. Existing section URLs and real Hero audio port stay unchanged.
One DOM and content source across ID/EN/AR, logical spacing and neutral wave.

## Six tiers / semantics / lifecycle
XS 360–639: compact, no menu media; SM 640–767: same, fluid spacing.
MD 768–1023: compact intrinsic submenu, media beside links follows their intrinsic height.
LG 1024–1279: landscape desktop; portrait compact through 1180.
XL 1280–1535 and 2XL >=1536: desktop bounded media and four-row columns.
Short compact menus scroll; content never reserves missing rows.
ID/EN/AR retain existing strings and POST+redirect. Active flag matches locale.
Native details/forms work without JS. Enhanced chooser uses native modal dialog,
Escape/backdrop/close restore trigger focus and nested compact scroll lock.
Wave: off line → amplitude ramp → organic wave → ramp down → idle no RAF.
Reduced motion draws static state. Desktop/hidden/concealed/unload pauses;
BFCache resumes; dispose cancels RAF/listeners/observers. Canvas failure keeps
accessible On/Off label. Resize reads CSS geometry; backing DPR capped at 2.
No graphics engine/assets/dependencies. Canvas is aria-hidden; button label and
aria-pressed remain the semantic state. No media loading owned by Sound.

## Reference audit
https://lusion.co/_astro/hoisted.CUO_IjfL.js, fetched 2026-10-05.
Read only Header.updateSoundWave, canvas resize, pointer angle and Audios.volume
transition. Visual model: 32 segments, width .4D, amplitude .28D × volume,
half-amplitude sine with eased edge envelope; phase advances .6 × delta.
Volume reference ramps linearly at 1/second; V2 uses visual amplitude only.
Pointer angle/morph audited but excluded by latest owner request.

## ACTIVE STEP / PROOF
Implement this bounded Header correction and verify rendered behavior.
Run diff, structure, build, focused Node/PHP and full PHP gates. Browser:
Chromium/WebKit, six tiers/boundaries, three locales, normal/reduced motion,
whole-row accordion, chooser focus/POST, sound resize/lifecycle, keyboard,
short height and text expansion. Compare three local performance samples;
missing Safari/field/PageSpeed evidence stays explicit, never inferred.
Reject overflow, inaccessible controls, duplicate owners, active hidden loops.

## STATUS / NEXT
Local implementation complete. Diff/structure/build/Pint PASS; focused Node 6
and PHP 7/46 PASS. Header 108 Chromium/WebKit locale/viewport cases PASS;
Sound lifecycle and one-link/text-expansion/orientation/scroll checks PASS.
Evidence: ../proof/header-correction-browser.json.
Full PHP FAIL: 71 failures / 75 errors from legacy source/view references.
STATUS: BLOCKED_BY_MISSING_EVIDENCE for final certification; native Safari,
manual accessibility/zoom and comparative performance/PageSpeed remain absent.
One NEXT: Terminal Codex reviews owner feedback and closes remaining proof gaps.
No publication; blueprint remains IMPLEMENTING.

## OWNER_ACCEPTED update — shared rhythm / Language / cursor
Latest OWNER_RAW supersedes earlier flag-only entry and four desktop slots.
One ACTIVE bounded patch: Header geometry + direct Language/Cursor dependencies.
Editable: existing Header owners, new V2 cursor owner, composition imports,
focused tests and docs2 proof/report/current ledger. Legacy, Hero internals,
other surfaces and unrelated AGENTS/CLAUDE/composer.lock changes are read-only.
Main remains a44d484f. Publish only feat/home-v2-hero, update draft PR #64;
no merge, Hero NOT CLOSED pending owner UI review.
Geometry: existing page spacing token becomes Header S; desktop bar padding S,
panel starts after bar bottom padding S, panel sides/bottom S. Landscape media
3:2 and submenu intrinsic rows share available capacity. XS/SM compact without
media, MD portrait compact intrinsic rows, LG landscape full navigation as
previously accepted, XL/2XL bounded media. ID/EN/AR share semantic DOM/logical CSS.
Language entry text; three flags only, no X/card/heading. Native modal dialog
full viewport with transparent surface and legacy dark blur backdrop, 14px
blur/saturation enhancement plus usable tinted fallback. Old 96–142px desktop
and 76–104px mobile sizing, old gaps and active border/ring. Escape/background
close, flag POST closes, focus restored, duplicate submission blocked.
Cursor: random cwo/cwe once during initialization; only default/interactive.
Semantic actionable detection, fine+hover gating, two assets only for selected
character. Native pointer remains during load/failure. Event-driven RAF stops
idle/hidden/pagehide, BFCache resumes identity. Cursor observes modal top layer
without coupling Language/Header to its internals. No legacy runtime import.
Proof: shared S geometry at multiple desktop/landscape widths, dynamic one-item
submenu, locale POST/Escape/backdrop/focus/blur/fallback/no-X, cursor identity and
two states plus touch exclusion. Chromium/WebKit required; Firefox/Edge where
available. Build/structure/diff/V2 tests/full PHP with frozen legacy failures.


## Latest execution proof / publication readiness
Shared rhythm + Language + two-state Cursor implemented. 99 three-engine
geometry/locale cases PASS; Edge Language smoke PASS; Cursor two assets/identity,
modal host and touch gating PASS; Sound regression PASS. Node8/PHP7/46,
structure253/build/diff/Pint PASS. Full PHP retains 71 failures/75 errors.
Evidence: ../proof/header-rhythm-language-cursor.json. Native Safari/full manual
accessibility/PageSpeed/CWV absent; blueprint remains IMPLEMENTING, Hero NOT CLOSED.
Publication verified: `65cc3de2d5279ec6b7a79b2cf4b82619185fb4d4` pushed to feat/home-v2-hero;
PR64 description updated and draft status verified. Main remains a44d484f.
One NEXT: owner UI review. No main mutation or merge; Hero NOT CLOSED.

## OWNER_ACCEPTED — desktop slots / state separation / accordion
Latest OWNER_RAW supersedes intrinsic desktop rows: frame equals media height;
each real item keeps one quarter slot, blank remaining slots preserved. Active
surface: Header/Menu + direct Hero playback dependencies; branch feat/home-v2-hero,
main a44d484f read-only, draft PR64 update permitted, no merge, Hero NOT CLOSED.
FACT: Language participates in Header.panel; all labels match underline CSS;
Hero canPlay depends on focused; compact details toggle before height motion.
GOAL/IMPACT: correct geometry and isolated state/lifecycle without replacing
Cursor/Sound/Language visuals or tablet media composition.
Editable: Header foundation/layout/state/motion adapters, independent Language,
Hero state/media controller, focused Node contracts, docs2 proof/report/ledger.
Read-only: legacy, locale/DB/media, Cursor/Sound internals, other surfaces and
unrelated AGENTS/CLAUDE/composer.lock work. Single channel: Terminal Codex.
One ACTIVE patch: foundation S × 2/3 → desktop frame/slots → Header/Language
state split → unique main highlight → sequential compact accordion → playback.
Six tiers: XS/SM compact no media, MD portrait existing media; LG landscape full
navigation as already accepted, portrait compact through1180; XL/2XL desktop.
Desktop font/slot hierarchy fits actual ID/EN/AR; four slots never redistributed.
Menu radius is height/15 via circular percentage radius at fixed3:2 aspect.
Language own open state only; preserves panel, highlight and Header theme.
Compact navigation still owns inert/scroll lock, Language native dialog owns
modal inertness. No overlay state pauses video. Focus pauses carousel advance
for keyboard access only; it does not pause video. IntersectionObserver uses
zero threshold: pause only when completely outside viewport (conservative,
no source unload), plus hidden/pagehide and existing reduced-motion preference.
Accordion adapter keeps closing details open until collapse finishes, then
opens next and expands. 320ms easing, actual-height endpoints; interruption
cancels old work and proceeds from rendered height; resize/mode/reduced/dispose
settle state. No timers or duplicate details toggle controller.
Proof: geometry S ratio / 3:2 / equal frame / one-quarter slots1–4 / radius,
normal white and only navigation-open or legacy scroll dark triggers, exactly
one main underline (hover/open/return), red submenu roll, language independent,
real video time advances through clicks/overlay/menu and pauses offscreen,
sequential compact height samples/reverse/resize/reduced. Chromium/Firefox/
WebKit plus Edge smoke, diff/structure/build/Node/V2 PHP/full PHP. Missing
nativeSafari/manual/accessibility/performance evidence remains explicit.


## Latest proof checkpoint — quarter slots / independent state
Execution complete for declared cases; 99 UI cells, six compact motion cases,
real canonical video established-playback/offscreen/resume in three engines and
Edge smoke PASS. Node10/PHP7/46, diff/structure255/build/Pint PASS. Full PHP keeps
71 failures/75 errors legacy. Source/property idempotence is owned by HeroMedia;
focus never pauses video, but still holds automatic carousel advance.
Evidence ../proof/header-slots-lifecycle.json. Physical Safari/manual/PSI/field
remain absent; blueprint IMPLEMENTING, Hero NOT CLOSED. One NEXT: authorized
owner UI review. Source `aaca6e1bcb7f9c9d0f6c8a5a6239b8373bb6a8c7` is pushed;
PR64 updated/draft/unmerged verified, main a44d484f unchanged.

## OWNER_ACCEPTED — nav cap / shared Hero action / slow dropdown
Parent e8e27d1a, main a44d484f fetched. Same branch/PR64, no merge, Hero NOT CLOSED.
One ACTIVE bounded patch: nav geometry → Hero action DOM → tablet frame → motion.
FACT: desktop bar height=logo+2S; portrait resets rows/height; copy children have
individual underline; compact motion320ms, desktop immediate.
GOAL: nav<=10vh centered, preserve S horizontally and logo→media gap; fixed
quarter-slot tablet frame; one Hero hover; slow directional reveal/collapse.
Editable: foundation/header/Hero layout, hero-copy Blade, accordion adapter and
focused proof/docs. Header/Language/playback/Cursor/Sound state, legacy/media/DB/
other surfaces and AGENTS/CLAUDE/composer.lock local work are read-only.
Nav cap is a distinct token. Bar=min(10vh,logo+2S), vertical padding0; panel top
padding=S-(bar-logo)/2 preserves gap. Logo retains current size whenever it fits;
short-height cap scales it only enough to stay within10vh. Side/bottom/media S
remain unchanged. CSS owns sizing and type; JS owns intent/motion only.
Hero H1/description/CTA share one wrapper and tone. PPDB common destination is
one semantic anchor; eyebrow stays separate. Other source destinations remain
as authored, within the same visual hover wrapper. No underline/yellow line.
XS/SM retain no media/intrinsic links; MD portrait and compact LG with media
use 3:2 frame and four quarter slots; full LG/XL/2XL retain desktop slots.
Tablet columns may rebalance locally for readable type and frame capacity;
no width/locale/component forks. ID/EN/AR one source/DOM, RTL logical layout.
Accordion states closed/opening/open/closing, duration1440ms (4.5×320), easing
cubic-bezier(.45,0,.2,1). Compact height drives downward/upward flow; desktop
panel clip reveal shares same orchestration without changing nav geometry.
A closes before B opens; interruption continues from rendered height/clip;
resize/reduced/hidden menu/dispose settle. No timer cascade or CSS override owner.
Proof: nav cap/centers/S/gap at multiple desktop heights, shared hover/no-line/
common link, tablet slots1–4 and type fit, mobile no-media unchanged, phase
samples/easing/reversal/sequential switching across three engines plus Edge;
regressions for Language/theme/media/Cursor/Sound. Required gates retain legacy
full-suite FAIL and missing physicalSafari/performance/manual certification.
NEXT CHANNEL: Terminal Codex. NEXT: implement and prove this bounded patch.


## Nav-frame proof checkpoint
99 locale/width cells, nine centering and nine Hero action cases, 24 extra heights,
nine slow-motion cases, Edge smoke and playback/Cursor/Sound regressions PASS.
Diff/structure255/build/Pint/Node10/PHP7/46 PASS. Full PHP71fail/75errors legacy.
Evidence ../proof/header-nav-frame.json. Blueprint IMPLEMENTING; Hero NOT CLOSED.
One NEXT: authorized branch publish, PR64 draft update, then owner UI review.
