# Landing V2 — proof/status

## Latest OWNER_RAW — nav cap / one Hero action / slow motion

VERIFIED: desktop nav<=10vh, logo/control center, preserved side and logo/media
spacing; nine centering cases and 24 additional height/width cases. Media layouts
including tablet portrait use four fixed quarter slots, phone remains intrinsic
with no media. 99 three-engine width/locale/state cells PASS. Hero shared tone
and no underline/one PPDB action PASS for H1, description and CTA (nine cases).
Nine normal-motion cases PASS:1440ms (4.5×320), cubic-bezier(.45,0,.2,1), sequential
collapse/expand and reversal. Desktop panel reveal does not expand nav height;
Header theme remains until physical close. Edge154 smoke PASS. Playback,
Language, Cursor/Sound regressions PASS. Diff/structure255/build/Pint,Node10 and
PHP7/46 PASS. Full PHP remains FAIL:312 total,166 pass,71 failures,75 errors.
Evidence: [nav frame](header-nav-frame.json), [menu](nav-frame-menu.png),
[Language](nav-frame-language.png). Physical Safari/manual/PSI/field gates remain
BLOCKED_BY_MISSING_EVIDENCE. Hero NOT CLOSED; same branch/PR64 draft, no merge.
One NEXT: authorized branch publication then owner UI review. Historical sections
below are checkpoint evidence.


## Latest OWNER_RAW correction — desktop slots / playback isolation

Latest source supersedes intrinsic desktop rows and Language-in-panel state.
VERIFIED: 99 Chromium/Firefox/WebKit UI cells; 1–4 items each keep 1/4 of the
frame equal to media height, desktop S=oldS×2/3, bold main text, red/no-underline
submenu, one main highlight, Language preserves theme and open navigation.
Six normal-motion mobile/tablet cases verify collapse-before-expand, sampled
heights, rapid reversal and resize. Three-engine canonical MP4 checks verify
click/focus/Language/backdrop/menu playback, partial visibility, full offscreen
pause and visible resume with retained currentSrc. Playback checks sample an
established decode (>5 seconds), not startup/decode performance certification.
Media policy and muted/loop application are idempotent. WPE early timestamp
variance is not claimed as Safari behavior. Edge154 smoke PASS for quarter frame,
Language theme independence, real playback, one highlight and compact motion.
Cursor/Sound regressions PASS. Node10, PHP7/46, diff/structure255/build/Pint PASS.
Full PHP retains 71 failures/75 errors on legacy references. Overall certification
remains BLOCKED_BY_MISSING_EVIDENCE (physical Safari/manual a11y/PageSpeed/field).
[Proof](header-slots-lifecycle.json), [menu](slots-menu.png), [language](slots-language.png).
Published source: `aaca6e1bcb7f9c9d0f6c8a5a6239b8373bb6a8c7` on feat/home-v2-hero.
PR64 description updated and draft/unmerged verified; remote main a44d484f unchanged.
Hero NOT CLOSED. One NEXT: owner review of desktop slot type and accordion.
Older sections below are historical checkpoint evidence.


## Latest owner-review patch — 2026-10-05

Header uses one shared spacing rhythm, landscape 3:2 media and intrinsic
submenu rows. Language is a text entry (localized via existing source), opening
only three flags over the legacy blur/tint; no X/card, Escape/background close,
POST locale switching. Cursor randomly chooses one cwo/cwe identity once, uses
only default/interactive, and mounts visually only for hover+fine pointer.
Sound, conditional desktop underline and compact no-underline remain preserved.

VERIFIED: 99 geometry/Language cells (11 viewport/orientation cases × ID/EN/AR
× Chromium/WebKit/Firefox); Edge desktop/mobile viewport Language smoke.
Cursor identity through 10 hover cycles, link/button/modal state, selected two
asset URLs only and touch exclusion PASS in all three Linux engines. Sound
idle/desktop/pagehide/pageshow/reduced-motion and DPR cap PASS in Chromium/WebKit.
Tinted no-blur fallback and keyboard/locale POST PASS in Chromium390.
Diff/structure253/build/Pint PASS; Node8 and focused PHP7/46 PASS.
Full PHP FAIL: 312 total, 166 passed, 71 failures, 75 errors (legacy references).
Source hashes and exact case results: [evidence](header-rhythm-language-cursor.json).
Visual review: [menu](rhythm-menu.png), [language](rhythm-language.png).
Native Safari, exhaustive manual accessibility/zoom, PageSpeed and field CWV
remain BLOCKED_BY_MISSING_EVIDENCE. No claim those gates passed.

Published source checkpoint: `65cc3de2d5279ec6b7a79b2cf4b82619185fb4d4` on
feat/home-v2-hero. PR #64 updated via REST; OPEN/DRAFT and remote head verified.
Remote main remains a44d484f; no merge performed.
Hero NOT CLOSED. One NEXT: owner reviews spacing, flag scale/blur and cursor UI
in the draft PR. Historical sections below are checkpoint evidence only.


> HISTORICAL CHECKPOINT 2026-10-04. Arahan terbaru:
> [MAP-V2-02](../blueprints/menu-hero-correction.md). EN-only sudah diganti
> dengan bahasa aktif EN/ID/AR. Dilarang merge sampai owner UI review.


Date: 2026-10-04. Base main: `a44d484f4cd8bc532514f0152e4423a2fcdb9781`.
Issue: [#63](https://github.com/Asyraf2003/schoolai/issues/63). Branch: `feat/home-v2-hero`.
Draft PR: [#64](https://github.com/Asyraf2003/schoolai/pull/64).
Implementation checkpoint: `0edd5aa6c4aaeab4ba39ed030263a1666dd96f53`.
STATUS: BLOCKED_BY_MISSING_EVIDENCE for final closure. Hero is NOT CLOSED.
Implementation: Shell + Menu + Hero EN, available for review; no additional sections.

## FACT / implementation

HomeController now delivers `landing.index`; it no longer invokes the whole legacy homepage.
Request locale is EN, saved language session/cookie untouched.
Hero data retains the existing DB/PPDB/promoted-article rules through server-side traits.
No legacy frontend CSS/JS/Blade import. Native details preserve navigation without JS.
Header and Hero each have a pure state core and browser adapters; composition wires ports.
Header audio controls send intent to Hero and receive status. Main is inert/aria-hidden
while compact navigation is open, then restored on close.
Foundation owns font/reset/tokens/focus; Header/Hero own their responsive CSS/motion.
Local Inter font bytes copied unchanged; OFL text retained (trailing whitespace trimmed); business media remains on canonical CF URLs.

## OWNER_CONFIRMED

Section links remain active with original URLs although targets are absent.
EN only; ID/AR not activated. No About, footer, other sections, admin/auth work, loader,
or Hero→About transition. Existing system URLs retained; destination pages not rebuilt.

## Required gates

| Gate | Result | Evidence |
| --- | --- | --- |
| git diff --check | PASS | Executed after edits |
| npm run check:structure | PASS | 245 source files; maximum 200 lines |
| npm run build | PASS | Vite 8.1.3; CSS ~9.62 kB, JS ~11.26 kB before gzip |
| vendor/bin/pint --dirty --format agent | PASS | Applied formatting |
| php artisan test --compact tests/Feature/LandingV2Test.php | PASS | 4 tests, 22 assertions |
| node --test tests/Unit/LandingV2Runtime.test.mjs | PASS | 3 policy tests |
| php artisan test | FAIL | 309 tests: 162 passed, 72 failed, 75 errors |
| node --test tests/Unit/*Runtime.test.mjs | FAIL | Legacy runtime imports still target archived paths |

Before implementation, PHP suite: 305 tests, 152 passed, 78 failed, 75 errors.
Failure-name comparison: zero new failing test names; six baseline failures resolved.
This does not turn the full suite green. Many old tests require archived views,
other sections, ID/AR behavior, or old selectors. They were not deleted or weakened.
See [baseline](php-baseline.json) and [current](php-final.json).
Local PHP 8.5.4 / Laravel 13.34.0 / Pest 4.7.8; existing uncommitted composer.lock
changes were present before this task and are excluded from the PR. These results use
the installed local dependencies; they are not proof of a clean install from committed lock.

## Browser matrix — BROWSER VERIFIED only for named checks

EN/LTR; heights 900px unless listed otherwise; DPR default 1; headless desktop engines.
Widths: 360, 390, 640, 768, 1024, 1180, 1181, 1280, 1536.
Checks: no horizontal overflow, correct compact/desktop mode, Hero text visible,
submenu open, Escape close, reduced-motion paused media, no pageerror.

- Chromium 153.0.8010.12: PASS for all nine widths.
- Firefox 155.0 (Playwright build): PASS for all nine widths.
- WebKit 26.6 (Linux WPE MiniBrowser): PASS for all nine widths. **Not branded Safari**.
- Microsoft Edge 154.0.4258.53 (Windows native executable via CDP): PASS for all nine widths.

See [matrix](browser.json) and [Edge](edge.json).
The three-engine width matrix preceded the final compact-menu grid refinement;
that refinement was rechecked with the short-menu cases below. Edge matrix was rerun after it.
Playwright 1.63.0 used from the existing external proof-tool cache; no npm dependency change.
Firefox was downloaded into the browser cache for the requested Firefox checks.
Chromium/Firefox use existing cached Linux shared libraries; WebKit uses existing WPE wrapper.

## Interaction / media / accessibility proof

- Real canonical MP4 reached `readyState=4` and advancing currentTime in Chromium,
  WebKit and Firefox; audio enable and pause PASS: [playback](playback.json).
- Edge media playback/audio/pause result is recorded separately in [Edge evidence](edge.json).
- Chromium: JS disabled still exposes native submenu and readable Hero.
- Chromium: blocked MP4 keeps poster/text and reports fallback; no JS exception.
- Chromium: 200% text enlargement has no horizontal overflow; this is text resize,
  not certification of browser page zoom at every tier.
- Chromium: compact menu focus loop stays in Header; resizing to desktop releases scroll lock.
- Final compact-menu grid: Chromium/WebKit/Firefox at 390×844 (100%/200% text),
  844×390 and 360×640 (200%) can scroll to last item, without horizontal overflow;
  main aria-hidden/inert lifecycle restores on close. [short-menu](short-menu.json).
- axe-core 4.13.0: zero detected violations at 390, 1181 and 1536px with menu closed/open.
  This is automated evidence, not a complete manual screen-reader or contrast certification.
  [axe results](a11y.json), [other interactions](interactions.json).

Canonical video HTTP HEAD: 200, video/mp4, Accept-Ranges bytes, 94,907,995 bytes.
URL/content left unchanged. Poster is baseline; video is hydrated only when playback is allowed.
No Lighthouse/PageSpeed/Core Web Vitals scores claimed.

Two-slide browser fixture (no DB mutation): Chromium/WebKit/Firefox passed next/previous,
wraparound, ArrowLeft, focus return, inactive-slide focus exclusion and overflow checks.
[Carousel evidence](carousel.json). Fixture preserves canonical image URL; synthetic story text
is test-only and never written into production data.

## Visual artifacts

Reduced-motion poster captures using current local DB (PPDB open; zero promoted articles):
[landing 390](landing-390.png), [landing 1536](landing-1536.png),
[menu 390](menu-390.png), [menu 1536](menu-1536.png).
Source fingerprints: [hashes](source-hashes.json).

## GAP / limitations

- Full pixel/composition/motion comparison against running legacy is not proven.
  Source-informed rewrite is available, but full visual/behavior parity is not claimed.
- Native Safari / iOS Safari unavailable; WebKit checks support compatible-by-design work,
  but do not equal Safari device verification.
- Carousel core, server selection and two-slide browser fixture are tested.
  Long-running automatic transition timing and full visual/motion equivalence remain unproven.
- Full suite and legacy JS runtime gates remain FAIL; CI/clean-lock proof also required.
- Lighthouse/performance, RUM, exhaustive zoom/keyboard/screen-reader/device evidence absent.
- Public destination routes remain registered, but non-landing views are archived;
  route existence is not a claim those pages render successfully.

## NEXT VALID STEP

Review draft PR and visual artifacts; close bounded remaining proof gaps for Menu + Hero.
Do not merge while required gates fail; do not add another section or rebuild admin/auth
just to make legacy tests green. Main remains unchanged by this implementation checkpoint.

## GIT handoff

Implementation checkpoint pushed and draft PR created. Main remains `a44d484`.
Only pre-existing local AGENTS.md, CLAUDE.md and composer.lock edits remain outside the commit.
CI run [37205933857](https://github.com/Asyraf2003/schoolai/actions/runs/37205933857)
on checkpoint `a7f22cb48d9355f5b9d7eca8062d774827934486`: FAIL at frontend runtime contracts.
Three V2 tests PASS; seven legacy test files FAIL due to archived implementation paths.
GitGuardian SUCCESS. Build/PHP steps after that failure were not executed in this run.
No CI pass or merge claim. Later documentation-only commits do not resolve this blocker.
