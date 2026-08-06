# Homepage Program Visual-Lerp Scroll Blueprint

State: `OWNER_ACCEPTED / IMPLEMENTING`
Date: 2026-08-06
Surface: homepage Program `#program`
Source baseline: `c0d8ff61acd3d5321bdb4c74c4d4b0bbed5befd8`

## OWNER GOAL

Parents experience all six Program frames through ordinary vertical scrolling.
The title moves from the Vision/Mission handoff into the Program corner, while
the existing description stays at the same viewport coordinate and only changes
text. Media remains edge-to-edge. The side rail is normally only lines and
reveals all program names when the rail is hovered or keyboard-focused.

## FACT

- Vision/Mission owns the single Program title and introductory description.
- Program renders six localized semantic articles and six temporary Unsplash
  images.
- Program is followed immediately by Values.
- The Gallery smooths visual state by letting a rendered value lerp toward native
  scroll rather than rewriting document scroll every animation frame.
- The current Program spring writes document scroll during its RAF loop and does
  not match the Gallery motion model.

## SCOPE

Editable:

- `resources/css/pages/welcome/program-journey/*`
- `resources/js/surfaces/home/program-journey/*`
- focused Program test
- this blueprint and `UI_UX_CURRENT_STATE.md`

Read-only:

- Program content and temporary media contract
- Vision/Mission semantic heading source
- Values, Gallery, Articles, About, and Testimonial
- homepage order and public authentication/PPDB contracts

## VISIBLE RESULT

1. The original title remains the title node and moves toward the responsive
   Program title slot.
2. The original description node is anchored to its measured viewport position;
   it does not travel to a new lower or central composition.
3. The description text changes through the shared blur/vertical WAAPI grammar.
4. Six `100vw × 100dvh` media frames remain physically stacked without card
   borders, radius, margins, or shadows.
5. The complete media stack visually lags native scroll through one lerp loop,
   producing the same target/current separation used by Gallery.
6. Active title, description, count, accent, and rail index derive from the same
   rendered scroll value.
7. The collapsed rail shows only six lines. Hover or focus-within reveals every
   program name; the active line remains longer and accented while collapsed.
8. After frame six, the existing blue and white-line handoff releases to Values.

## MOTION / INPUT

- Native wheel, touch, keyboard, scrollbar, and browser momentum remain owners of
  actual document scrolling.
- Program tracks `target`, `current`, `previous`, velocity, and input velocity.
- Each RAF uses `current = lerp(current, target, 0.08)` and moves only the visual
  media stack by `target - current`.
- No RAF callback writes `window.scrollTo`.
- After input becomes quiet, projected velocity selects the nearest frame. One
  native smooth-scroll request aligns the document anchor while visual lerp
  continues to absorb the movement.
- New wheel, touch, pointer, or key input cancels an active native snap.
- Reduced motion makes rendered position equal native target and disables
  automatic snapping.

## RESPONSIVE / LOCALE

- XS/SM/MD: centered responsive title; rail hidden; description keeps its
  handoff coordinate and readable width.
- LG 1024–1180: centered title with rail available at inline-end.
- XL/2XL: title at inline-start and rail at inline-end.
- AR mirrors title and rail through logical properties. Description uses the
  coordinate measured from the RTL origin; vertical time and media are unchanged.
- One semantic DOM and one controller serve all tiers and locales.

## SEMANTIC / FAILURE RESULT

- Without JS, all six images, titles, descriptions, and links remain present.
- External image failure does not remove text or the action path.
- Hover is not required to identify the active item: line length, accent, and
  `aria-current` remain available; focus-within reveals labels for keyboard use.
- Cleanup removes RAF, timers, listeners, transforms, and moved-node ownership.

## PROOF GATES

- `git diff --check`
- `npm run check:structure`
- `npm run build`
- `php artisan test`
- focused DOM/source contract test
- forward, reverse, fast, interrupted, and slow scroll
- mouse wheel, touchpad, touch, keyboard, and scrollbar
- ID/EN/AR, LTR/RTL, reduced motion, resize, short height, and BFCache
- Chromium and WebKit at all declared tiers
- PageSpeed/CWV delta and external-media failure fallback
