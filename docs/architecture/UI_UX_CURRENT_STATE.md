# UI/UX Engineering — Current State and Progress Ledger

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-08
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active batch: `HOME-PROGRAM-002-KINETIC-TYPE-TRANSITION`
Source baseline: `252720aadb6a802c671d0ac7fa405c33a9450719`
Blueprint commit: `be9872c883e246f0aeee73b7c24b586d0453a093`
Implementation commit: `45ee8adbd7cd1784bcc025ac7e45c7c0959f7938`
Active blueprint: `blueprints/2026-08-08-home-program-kinetic-type-transition.md`

## Latest owner decision

- Replace the Program section completely with the interaction concept from
  `https://github.com/codrops/KineticTypePageTransition`.
- Program contains exactly six paths: PG, TK, SD, TQ, MB, LT.
- Use relevant remote royalty-free/free-use photography rather than Codrops
  media assets.
- Program remains directly below the independent About/Visi/Misi surface.
- ID/EN/AR public copy must be locale-owned and RTL/LTR must remain correct.

## Reference facts

The Codrops reference is MIT licensed and its core experience is click-driven,
not scroll-driven. Its source uses staggered image items, a repeated oversized
type field, GSAP scale/rotation/lateral text motion, alternating item exit,
then an article/detail reveal with image wrapper and image moving in opposite
directions.

SchoolAI translates that choreography without copying the reference copy,
fonts, images, colors, dimensions, or global body layout.

## Implemented source contract

- The former sticky seven-frame Program journey, HUD, rail, anchors and scroll
  choreography are removed from active Program markup.
- Idle Program now presents six focusable media cards in a kinetic editorial
  composition on wide screens and a readable grid/stack on compact screens.
- Activating a card opens a full-viewport detail experience.
- Repeated background typography scales to 2.7x, rotates a quarter turn and
  travels laterally with staggered line motion.
- Cards exit with alternating vertical displacement.
- Detail copy enters in stagger and its media wrapper/image use opposite Y
  reveals, preserving the central reference behavior.
- Back button and Escape reverse the state and restore focus to the originating
  program card.
- Detail mode locks overflow only while the dialog experience is active.
- Reduced motion removes the large kinetic type movement and uses immediate
  state changes through the same semantic content.
- No GSAP, WebGL, continuous RAF loop, wheel hijacking or document-scroll
  choreography was added.

## Locale contract

Public Program content now lives in:

- `lang/id/home_program.php`
- `lang/en/home_program.php`
- `lang/ar/home_program.php`

Each locale owns the section label, open/back UI copy, six program categories,
titles, summaries, descriptions and sequence codes. Blade contains no locale
`match` block for Program public text.

## Media contract

Six existing Unsplash CDN image URLs are reused/reassigned as relevant remote
education media. The Program does not copy Codrops image files. Images remain
remote and should be reviewed later for final school-specific art direction and
network/performance implications.

## Responsive and direction contract

- below 640px: one-column cards;
- 640–1023px: two-column cards;
- 1024px and above: six-card staggered full-viewport composition;
- detail layout becomes two-column on wide screens;
- Arabic uses the existing Cairo stack;
- kinetic rotation/lateral travel mirrors under RTL;
- logical program order remains PG -> TK -> SD -> TQ -> MB -> LT.

## Focused source tests

`HomeProgramJourneyTest.php` now verifies:

- six localized cards and six localized detail articles;
- all three locales render representative Program copy;
- ten kinetic type lines and twelve Unsplash image references are present;
- obsolete sticky/rail/frame markers are absent;
- native WAAPI is used;
- GSAP, wheel handling and `scrollTo` are absent from the Program controller.

## Existing proof before this Program batch

Owner-local output supplied before the Program rewrite:

- Laravel suite: `197 passed (1659 assertions)` in `13.87s`.
- `npm run check:structure` was already failing before this Program batch due to:
  - `resources/js/surfaces/home/vision-story/entry.js` orphan reference;
  - `resources/js/surfaces/home/vision-story/typography.js` orphan reference;
  - `resources/css/pages/welcome-hero.css` checksum drift.

Those failures are outside Program scope and were intentionally not repaired in
this batch. The prior Laravel pass is a baseline only and is not post-change
proof for implementation commit `45ee8ad...`.

## Blocked proof

This GitHub execution channel cannot run the local browser/runtime matrix. The
following remain required against current `main`:

```bash
git diff --check
git status --short
php artisan test --filter=HomeProgramJourneyTest
php artisan test
npm run check:structure
npm run build
```

The last two are expected to remain blocked by the three pre-existing structure
failures until those unrelated issues are repaired in their own scope.

Runtime proof is required for:

- all six cards opening the correct detail;
- back and Escape closure;
- repeated open/close without stale overflow lock;
- focus restoration and Tab containment;
- ID/EN/AR and RTL/LTR motion direction;
- reduced motion;
- 360, 390, 640, 768, 1024, 1180/1181, 1280, 1440, 1536, 1920 widths;
- Chromium and WebKit;
- mobile touch and desktop pointer interaction;
- clean About/Visi/Misi -> Program -> next-section flow;
- remote-image crop/loading behavior and PageSpeed/CWV delta.

## Progress / status

Program source redesign and publication are complete. Browser, accessibility,
responsive, post-change test, build and performance completion are not yet
proven.

STATUS: `BLOCKED_BY_MISSING_EVIDENCE`

NEXT EXECUTION CHANNEL: `owner/local terminal`

NEXT VALID STEP: pull current `main`, run the focused Program feature test, and
report its exact output before visual tuning.