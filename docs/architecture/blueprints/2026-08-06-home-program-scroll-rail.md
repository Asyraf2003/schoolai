# Homepage Program Native-Target Sticky Track Blueprint

State: `OWNER_ACCEPTED / IMPLEMENTING`
Date: 2026-08-06
Surface: homepage Program `#program`
Source baseline: `c72c7eb05818b61bbd253391a229053c092270e2`
Batch: `HOME-PROGRAM-017-NATIVE-TARGET-STICKY-TRACK`

## OWNER GOAL

Program must feel like Gallery: native document scroll supplies a target and one
visual current follows it with lerp. The six full-screen HTML media frames move
inside one sticky viewport without snapping or writing document scroll. The
opening description stays at its Visi/Misi viewport coordinate like a sticker;
only its text opacity and light blur may change. The rail is an indicator only.

## FACT AND ROOT CAUSE

- Visi/Misi owns the single original Program title and description nodes.
- Program renders six localized semantic articles and six temporary Unsplash
  images, followed by the blue Values handoff.
- The previous Program implementation combined natural document movement with a
  `target - current` compensation transform.
- Its motion owner also retained projected landing, `snapTimer`, anchor settling,
  `window.scrollTo`, rail click navigation, a separate rail transform timeline,
  description FLIP, and vertical description keyframes.
- Those owners conflict with ordinary native scrolling and can diverge during
  fast, reverse, or interrupted input.

## SCOPE AND OWNER MAP

Editable owners:

- `resources/views/home/sections/featured-programs.blade.php`
- `resources/css/pages/welcome/program-journey/{base,hud,rail,wide}.css`
- `resources/js/surfaces/home/program-journey/{controller,geometry,motion}.js`
- `tests/Feature/HomeProgramJourneyTest.php`
- this blueprint and `UI_UX_CURRENT_STATE.md`

Read-only constraints:

- `resources/views/home/sections/vision-mission.blade.php` as the original node
  and coordinate source
- localized Program data and temporary media contract
- Values, Gallery, Articles, About, Testimonial, header, and every other surface

Ownership after patch:

| Concern | Owner |
|---|---|
| semantic content/media/fallback | Program Blade partial |
| scroll distance and active geometry | `geometry.js` |
| handoff, current state, media/rail/exit synchronization | `controller.js` |
| lerp and copy WAAPI | `motion.js` |
| sticky viewport, full-screen track, HUD, rail, exit | Program CSS modules |
| durable source contracts | focused Program feature test |

## VISIBLE AND INTERACTION RESULT

1. The Program section provides scroll distance while a `100vw × 100dvh` visual
   viewport remains sticky.
2. Six `100vw × 100dvh` HTML frames form one vertical visual track. The track is
   removed from enhanced document flow and moves only by `-visualCurrent`.
3. Native wheel, touchpad, touch, keyboard, scrollbar, and browser momentum own
   document scrolling. There is no wheel interception, automatic landing, timer,
   anchor settling, or Program `window.scrollTo`.
4. `target` is read from native scroll. `current` follows it with the Gallery
   smoothing factor `0.08`; forward and reverse use the same equation.
5. The original title remains the same node and may FLIP to the responsive
   inline-start top slot; RTL uses logical inline-end.
6. The original description is reparented into the sticky HUD only to escape the
   transformed Visi/Misi containing block. Its measured top/left/width are
   preserved, it receives no FLIP, and text swaps use only opacity/light blur.
7. The fixed description area keeps one stable size and coordinate while media
   moves behind it.
8. The rail contains six non-clickable list indicators. The active line follows
   the same `current`; all names reveal only on hover or focus-within.
9. The final blue field and white lines derive from the same current after frame
   six, then the page releases naturally to Values.

## RESPONSIVE, LOCALE, AND FALLBACK

- XS/SM/MD use the existing compact title composition and hide the rail where
  space is insufficient; the sticky track and fixed description contract remain.
- LG 1024–1180 may show the rail at logical inline-end.
- XL/2XL place title at logical inline-start and rail at logical inline-end.
- ID/EN use LTR. AR mirrors title and rail through logical properties; vertical
  time and media order are unchanged.
- One DOM and one controller serve all six tiers and all locales.
- Reduced motion maps current directly to native target and removes copy travel;
  description still uses no positional animation.
- Without JS, all six images, headings, descriptions, and links remain ordinary
  HTML in document flow, followed by the existing blue handoff.
- External image failure cannot remove Program text or the portal action.

## STATE AND CLEANUP

```text
before -> handoff -> active frames -> blue exit -> after
```

Fast/reverse/interrupted scrolling is reversible because all visual state derives
from one current value. Resize remeasures viewport distance and copy coordinates.
BFCache pageshow resynchronizes state. Final disposal cancels RAF/WAAPI, restores
original nodes, removes listeners, and clears enhanced transforms.

## PROOF GATES

Source checks available in the Web AI channel:

- JS syntax for controller, geometry, and motion
- PHP syntax for focused test
- balanced CSS braces
- changed source files at or below 200 lines
- forbidden-token/source contract checks
- atomic fast-forward publication and changed-path verification

Required but unavailable in this channel, therefore
`BLOCKED_BY_MISSING_EVIDENCE` until run from a checkout/runtime:

- `git diff --check`
- `npm run check:structure`
- `npm run build`
- `php artisan test`
- Chromium/WebKit six-tier ID/EN/AR, RTL, reduced-motion, input, resize,
  short-height, zoom, BFCache, image-failure, PageSpeed, and CWV proof
