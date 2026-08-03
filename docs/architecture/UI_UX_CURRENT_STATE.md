# UI/UX Engineering — Current State and Progress Ledger

Status: `BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-03
Repository: `Asyraf2003/schoolai`
Source main before batch: `4bb9a00eab448aa4b33d28119b2421e3736976db`
Target branch: `main`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, performance, or lifecycle completion.

## Active production batch

Blueprint: `blueprints/2026-08-03-home-values-card-story.md`

- ID: `HOME-VALUES-001`
- State: `IMPLEMENTING`
- Surface: homepage Values section `#nilai`
- Owner goal: Lusion-informed four-card scroll story using Al Mustaqbal content
- Protected: Hero, Vision/Mission, Programs, Gallery, Articles, navigation,
  footer, DB/admin/routes, disabled About, and disabled Testimonial

## Owner-accepted reference contract

The owner supplied a chronological screenshot sequence from Lusion About and
accepted the following SchoolAI translation:

- blue full-viewport field with oversized localized Values heading;
- four readable white cards using current school value content;
- sequential right-to-left Y-axis flips;
- school-owned geometric line backs, not copied Lusion art;
- fan, centered stack, and final departure;
- six responsive tiers from 360px;
- one shared UI and motion meaning for ID/EN/AR and LTR/RTL;
- direct publication to `Asyraf2003/schoolai` `main`.

Screenshots prove desired chronology/composition only. They do not prove source
ownership, browser parity, performance, or runtime behavior.

## Inspected source FACT

- `school-values.blade.php` previously rendered four `.nilai-card` buttons.
- `resources/js/pages/welcome/value-cards.js` previously owned click, focus, and
  hover active-card state.
- Values CSS remains distributed through legacy welcome modules `004`, `009`,
  `010`, and `011`, but those selectors no longer match the new Values DOM.
- `resources/js/pages/welcome.js` now imports the dedicated Values controller.
- `welcome.blade.php` and `vite.config.js` own the new Values stylesheet entry.
- `BuildsHomePage` supplies `schoolValues` from `lang/{id,en,ar}/home.php`.
- All locales expose four cards in the same semantic Q/I/G/N order.
- ID/EN use Inter/LTR; AR uses Cairo/RTL through current typography adapters.
- No WebGL or third-party dependency is required for this surface.

## Implemented source contract

- Values now uses one semantic section, localized heading/description, and four
  article cards with meaningful front faces.
- Card backs are decorative and hidden from assistive technology.
- One dedicated CSS route entry owns the blue field, cards, 3D faces, six-tier
  composition, static fallback, and reduced-motion result.
- One Values controller owns scroll progress, inertia, measurement,
  intersection/visibility suspension, resize, reverse scroll, and BFCache
  restoration.
- One deterministic timeline owns enter, sequential flip, fan, stack, and exit.
- New source uses a dedicated `.values-*` namespace; legacy `.nilai-*` CSS no
  longer matches rendered Values DOM.
- The old `value-cards.js` controller and import are removed.
- No content data, route, DB, WebGL, media, or unrelated section changes.

## Six-tier source contract

| Tier | Source composition |
|---|---|
| XS 360–639 | one readable stack, compact fan, one-column static fallback |
| SM 640–767 | 2x2 fronts/fallback with wider fan |
| MD 768–1023 | larger 2x2 field and balanced vertical spacing |
| LG 1024–1279 | four cards in one row |
| XL 1280–1535 | wider four-card cinematic field |
| 2XL >=1536 | bounded cards/copy with expanded blue background |

Card width also clamps to short viewport height. Navigation is untouched, so
1180/1181 remains outside active proof.

## Locale/direction source contract

- One DOM, controller, timeline, and physical card order serves ID, EN, and AR.
- Locale changes copy, `lang`, `dir`, family, and natural text alignment only.
- Neutral Y-axis flips, vertical scroll time, fan order, and stack order do not
  reverse in RTL.
- No mixed-language duplicate DOM or locale-specific component fork exists.

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Mandatory docs/current main | `PASS_SOURCE` | docs and main inspected |
| Owner art-direction decision | `PASS` | exact prompt + screenshots |
| Existing DOM/CSS/JS/Vite/lang audit | `PASS_SOURCE` | current owners inspected |
| Dedicated semantic Values DOM | `IMPLEMENTED_SOURCE` | bounded Blade replacement |
| Static/reduced fallback | `IMPLEMENTED_SOURCE` | no readiness class required |
| Sequential flip/fan/stack/exit | `IMPLEMENTED_SOURCE` | one deterministic controller |
| Six-tier architecture | `IMPLEMENTED_SOURCE` | CSS tier variables + one DOM |
| ID/EN/AR and RTL architecture | `IMPLEMENTED_SOURCE` | existing content/type owners |
| Focused feature test | `IMPLEMENTED_SOURCE` | DOM/state contract added |
| JavaScript syntax | `PASS_LOCAL_PATCH` | `node --check` on new modules |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector cannot run repo command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| PHP/focused tests | `BLOCKED_BY_MISSING_EVIDENCE` | not run on resulting main |
| Chromium/WebKit six-tier runtime | `BLOCKED_BY_MISSING_EVIDENCE` | no rendered matrix yet |
| Accessibility/zoom/reduced motion | `BLOCKED_BY_MISSING_EVIDENCE` | runtime proof absent |
| Lighthouse/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | no comparable runs |

## Prior production state

Vision/Mission momentum and completion-beat source changes are published on
`main`, but their full browser/performance matrix remains unproven. Gallery
refinement history remains outside this Values batch and is not modified.

## Progress ledger

| Stage | Status |
|---|---|
| V01 mandatory docs and current source | `PASS_SOURCE` |
| V02 reference chronology and owner decision | `PASS` |
| V03 DOM/CSS/JS/Vite/content audit | `PASS_SOURCE` |
| V04 accepted Values blueprint | `IMPLEMENTING` |
| V05 semantic/static surface | `IMPLEMENTED_SOURCE` |
| V06 scroll motion and lifecycle | `IMPLEMENTED_SOURCE` |
| V07 six-tier/locale adapters | `IMPLEMENTED_SOURCE` |
| V08 focused test and publication | `IMPLEMENTED_SOURCE` |
| V09 automated proof | `BLOCKED_BY_MISSING_EVIDENCE` |
| V10 browser/accessibility/performance matrix | `BLOCKED_BY_MISSING_EVIDENCE` |

## STATUS

The Values card story is implemented in source and published directly to
`main`. It is not runtime or release `PASS`.

## NEXT VALID STEP

Pull resulting `main`, run the focused Values test plus mandatory automated
gates, then capture one XL Chromium scroll recording from front cards through
flip, fan, stack, and exit before expanding to the remaining tiers and WebKit.
