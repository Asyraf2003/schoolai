# UI/UX Engineering — Current State and Progress Ledger

Status: `BLOCKED_BY_MISSING_EVIDENCE`
Updated: 2026-08-03
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Active blueprint: `blueprints/2026-08-03-home-values-card-story.md`

Commit publication proves source state only. It does not prove build, browser,
responsive, accessibility, performance, or lifecycle completion.

## Active production batch

- ID: `HOME-VALUES-001`
- State: `IMPLEMENTING`
- Surface: homepage Values section `#nilai` and its visual handoff from the final
  Mission scene
- Protected: Hero, Vision/Mission scene content and motion, Programs, Gallery,
  Articles, navigation, footer, DB/admin/routes, About, and Testimonial

## Owner-accepted storyboard

The Values journey must run in this order:

1. Final Mission color/content dissolves through blur into the Values blue field.
2. Localized heading line one enters bottom-to-top.
3. Heading line two enters top-to-bottom.
4. One front-facing card rises below the heading.
5. The remaining cards become visible as a four-card deck.
6. The deck spreads smoothly into the tier-appropriate independent layout.
7. Cards flip right-to-left with about 30% temporal overlap.
8. A white line grows from nothing and advances like a moving snake throughout
   the scroll journey.
9. Cards rise and leave only after the overlapping flip sequence completes.
10. Scroll then hands off to the next homepage section.

One DOM, physical choreography, and controller serve ID, EN, and AR. Locale and
RTL alter content, font, direction, and natural text alignment only.

## Runtime feedback FACT

### Runtime 1: sticky failure

Owner screenshots on local 1920x1080 Brave/Chromium proved:

- the stage travelled under the navbar;
- cards were small and weakly opaque;
- the heading did not enter;
- a long empty blue area remained.

Root cause was `overflow: hidden` on the sticky ancestor plus excessively long
entry and release timing. The bounded sticky correction was published.

### Runtime 2: storyboard and face failure

Fresh owner screenshots proved the sticky correction worked, but the composition
still failed the accepted storyboard:

- Mission and Values met at a hard color edge;
- the heading appeared as one static block rather than two opposing line entries;
- cards spread/flip in the wrong chronology;
- one flip completed before the next began;
- front content appeared mirrored after `180deg` instead of revealing the back;
- the white decoration was two static circles rather than a scroll-drawn line;
- final cards did not follow the requested rise-after-flip handoff.

This runtime is a `FAIL` for art direction despite correct sticky containment.

## Implemented source correction

Source head before this ledger update:
`fdc60f49ebf798941cf0f05ff988b488b2988081`.

- A negative-overlap, backdrop-blurred transition layer blends the final Mission
  green into Values blue without changing Mission scene content/controller.
- Heading lines have independent CSS variables and opposite vertical entry.
- The first card is the only visible lead card.
- Remaining cards reveal into a compact deck before any spread.
- Deck-to-independent layout uses one eased interpolation.
- Flip duration is `0.15` progress with `0.045` start offsets, so each next card
  begins when the previous card is about 30% through its flip.
- Rightmost card starts first; physical order is shared in RTL.
- Y rotation moved from the outer card pose to `.values-card__inner`, allowing
  front/back `backface-visibility` to work instead of mirroring the front.
- One SVG Bezier path replaces static circles.
- Main stroke, glow, and moving head share scroll-driven dash progress.
- Cards exit upward only after the flip window.
- Story travel remains explicit for XS, SM, MD, LG, XL, and 2XL.
- Reduced-motion and unsupported CSS engines keep the semantic static layout.

## Six-tier source contract

| Tier | Layout | Travel |
|---|---|---|
| XS 360–639 | readable compact fan/stack | `640svh` |
| SM 640–767 | 2x2 | `620svh` |
| MD 768–1023 | larger 2x2 | `600svh` |
| LG 1024–1279 | four-card row | `580svh` |
| XL 1280–1535 | cinematic row | `560svh` |
| 2XL >=1536 | bounded wide row | `580svh` |

## Source ownership

- Blade: `resources/views/home/sections/school-values.blade.php`
- CSS entry: `resources/css/pages/welcome-values-story.css`
- CSS modules: `resources/css/surfaces/home/values/*`
- Controller: `resources/js/surfaces/home/values/controller.js`
- Layout/timeline: `resources/js/surfaces/home/values/layout.js`
- Inertia: `resources/js/surfaces/home/values/motion.js`
- Painting/cleanup: `resources/js/surfaces/home/values/paint.js`
- Focused test: `tests/Feature/HomeValuesStoryTest.php`

## Proof status

| Gate | Status | Evidence/blocker |
|---|---|---|
| Mandatory source audit | `PASS_SOURCE` | current owners inspected |
| Owner storyboard | `PASS` | prompt and chronological screenshots |
| Runtime 1 | `FAIL` | sticky composition failure |
| Sticky root-cause correction | `PASS_SOURCE` | runtime 2 proves pinning works |
| Runtime 2 | `FAIL` | chronology, faces, line, and transition wrong |
| Corrected storyboard source | `IMPLEMENTED_SOURCE` | bounded Blade/CSS/JS patch |
| Split title source | `IMPLEMENTED_SOURCE` | independent line variables |
| Deck/spread/overlap source | `IMPLEMENTED_SOURCE` | deterministic timeline |
| True front/back source | `IMPLEMENTED_SOURCE` | inner-card Y rotation |
| Scroll-drawn line source | `IMPLEMENTED_SOURCE` | SVG dash progression |
| Focused feature test | `IMPLEMENTED_SOURCE` | updated DOM contract |
| JavaScript syntax | `PASS_LOCAL_PATCH` | controller/layout/paint checked |
| Source line limit | `PASS_LOCAL_PATCH` | all changed source <=200 lines |
| `git diff --check` | `BLOCKED_BY_MISSING_EVIDENCE` | connector cannot run repo command |
| `npm run check:structure` | `BLOCKED_BY_MISSING_EVIDENCE` | not run after correction |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not run after correction |
| PHP/focused tests | `BLOCKED_BY_MISSING_EVIDENCE` | not run after correction |
| Corrected Chromium runtime | `BLOCKED_BY_MISSING_EVIDENCE` | fresh recording absent |
| WebKit/six-tier/RTL runtime | `BLOCKED_BY_MISSING_EVIDENCE` | matrix absent |
| Accessibility/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | comparable runs absent |

## STATUS

The second runtime result failed the accepted art direction. The transition,
split heading, lead/deck/spread ordering, 30%-overlap flips, true card backs,
scroll-drawn line, and upward exit are corrected in source and published to
`main`. Runtime and release status remain unproven.

## NEXT VALID STEP

Pull current `main` and capture one 1920x1080 Brave/Chromium scroll sequence from
the final Mission scene through Values title, lead card, deck, spread, overlapping
flips, animated line, upward exit, and next-section handoff.
