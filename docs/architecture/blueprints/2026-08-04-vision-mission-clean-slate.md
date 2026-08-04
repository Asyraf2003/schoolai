# Vision/Mission Clean-Slate Preparation

BLUEPRINT ID: `HOME-VISION-003-CLEAN`
STATUS: `IMPLEMENTING`
OWNER: Asyraf Mubarak
DATE: 2026-08-04
SOURCE MAIN SHA: `e42308fad7f6d7e48aa1e109eacec1d49947d75f`
SOURCE SEQUENCE BEFORE BLUEPRINT CLOSE: `023d8d6ccffa3350ac558cf2d742d4b9d09dab5d`
ACTIVE ROUTE/SURFACE: homepage Vision/Mission `#visi-misi`
TARGET EXECUTION CHANNEL: owner/local terminal for proof

## Owner goal

Remove the complete active and dormant Vision/Mission presentation system before
a new scroll-linked WAAPI composition is implemented. The cleanup must preserve
localized semantic content and a readable static fallback while deleting the old
continuous RAF, text splitting, scene painters, dedicated CSS, stale reveal
rules, and legacy Vision/Mission selectors.

The later enhancement must not initialize during the initial Hero critical path.
It will be dynamically prepared only after Hero presentation, preferably during
an idle window, and activated by proximity to `#visi-misi`. That enhancement is
outside this cleanup batch.

## FACT

- The prior Vision/Mission owned one dedicated CSS entry and one JavaScript entry.
- The JavaScript entry created its controller at DOM ready.
- Latin copy was split into characters and painted with per-unit inline styles.
- Six long sticky scenes and filtered artwork were driven by a local RAF.
- Legacy Vision/Mission selectors also remained in the mechanically split
  homepage CSS, Arabic typography adapter, and reveal controller.
- ID, EN, AR content and assets `media/home/9.png` through `12.png` remain valid
  content/assets and were not deleted.
- Owner audit showed local and remote `main` at the source SHA before write.
- Two locally modified seeder files are unrelated and protected from this batch.

## Scope

SCOPE IN:

- replace the Vision/Mission Blade presentation with semantic static markup;
- remove old Vision/Mission CSS/JS entries;
- delete the dedicated scroll-story modules and stylesheet;
- remove stale homepage reveal rules and legacy selectors;
- replace the focused feature test with static semantic assertions;
- preserve all locale data and image assets.

SCOPE OUT:

- no WAAPI implementation;
- no Hero, Values, Programs, Gallery, navigation, footer, DB, route, or content
  redesign;
- no page-wide scheduler or virtual scroll;
- no reorder of Values and Programs.

## Static contract

- One `section#visi-misi` remains server-rendered.
- The section has one H2, one Vision article, and an ordered list of four Misi.
- Marked content remains semantic through `strong` elements.
- Arabic honorific text remains written in full.
- No JavaScript, sticky scene, hidden unit, or motion-dependent access is needed.

## Execution result

Published source now:

- renders the static semantic contract in ID, EN, and AR;
- removes the old homepage and Vite entries;
- deletes the dedicated stylesheet, story entry, controller, scroll progress,
  scene renderer, motion painters, and text splitter;
- removes stale reveal rules and known dormant legacy selectors;
- retains the mechanically protected CSS import slots as comment-only files;
- updates the focused feature test to reject all old story hooks;
- records the active source state in `UI_UX_CURRENT_STATE.md`.

No WAAPI, new renderer, new assets, or adjacent-section changes were included.

## Deferred WAAPI contract

The next blueprint may add a single paused WAAPI master timeline controlled by
native scroll progress. It must use bounded wrapper transforms/opacity, retain a
static/reduced result, load after Hero presentation, and initialize only during
idle/proximity. No old controller may be restored or run in parallel.

## Proof gates

Required after pulling the resulting `main`:

```bash
git diff --check
npm run check:structure
npm run build
php artisan test --filter=HomeVisionMissionHeadingTest
php artisan test
```

Also run a runtime-source search for deleted ownership identifiers. Browser,
six-tier, locale, performance, and accessibility status remain
`BLOCKED_BY_MISSING_EVIDENCE` until rendered proof exists. The blueprint cannot
become `PROVEN` before those declared gates pass.
