# Homepage Atmospheric Depth Gallery

BLUEPRINT ID: `HOME-GALLERY-003`
STATUS: `IMPLEMENTING`
OWNER: Asyraf
DATE: 2026-08-03
SOURCE MAIN SHA: `d954427cfee91575e32540a3c7fb74bca00718a5`
ACTIVE ROUTE/SURFACE: homepage Gallery only
TARGET EXECUTION CHANNEL: Web AI with GitHub connector
REFERENCE: `houmahani/codrops-depth-gallery`
RUNTIME EVIDENCE: owner screenshots at desktop Chromium, 2026-08-03

## Owner goal

Use the reference's media-first atmospheric depth journey with current
Al-Mustaqbal gallery data. The media must remain the visual focus. Text is only a
small readable title and optional description beside the media. Do not show
number, type, date, large editorial copy, rounded image frames, or a full-width
card composition.

Add the reference-like thin trail, retain visible background palette changes,
and use one composition across XS, SM, MD, LG, XL, 2XL plus ID/EN/AR.

## Runtime FACT

Owner screenshots proved that the previous source result still behaved unlike
the reference:

- a large card and oversized white copy competed with the media;
- inactive/even media sat too far from the active composition;
- the progress treatment was a straight bottom bar rather than a thin spatial
  trail;
- the result still looked like the previous Gallery content system placed in a
  depth scene.

Reference source inspection proves:

- media planes are the focal objects;
- label overlays use very small text at the side of the viewport;
- plane X positions are modest relative to their scale;
- a thin curved trail advances through the depth journey;
- mood colors blend with camera depth.

## Scope

SCOPE IN:
- `resources/views/home/sections/gallery-depth.blade.php`;
- dedicated Gallery depth CSS modules;
- scene positioning and trail progress orchestration;
- focused Gallery test;
- this blueprint and current-state ledger.

SCOPE OUT:
- `/galeri` page;
- Hero, Vision/Mission, Values, Programs, Articles, navigation, footer;
- DB/admin/gallery data normalization;
- dependencies and package lock;
- unrelated historical CSS migration;
- pre-existing Hero checksum mismatch.

## Corrected semantic composition

- Each item remains one semantic anchor with media, title, and optional caption.
- Title and caption are adjacent to media, never overlaid.
- Number, type, category, and date are retained only as lightbox data attributes
  where needed; they are not visible in the depth scene.
- No-JS and reduced-motion results remain normal readable media sequences.
- The canvas and trail are decorative and `aria-hidden`.
- Existing accessible lightbox behavior remains.

## Visual contract

- Media owns the majority of visual area.
- Images keep intrinsic ratio using auto dimensions, max width/height, and
  `object-fit: contain`.
- Corners are square and no forced crop exists.
- Label title uses restrained body typography around 14–17px depending on fluid
  size; caption is smaller.
- Label color is black with no oversized display treatment.
- Alternating media/copy order remains but the entire item receives only a small
  horizontal displacement capped at 54px.
- A thin curved white trail sits behind media and reveals with scroll progress.
- The former straight bottom progress bar is removed.
- Background and WebGL atmosphere share the same blended palette.

## Six-tier contract

| Tier | Composition |
|---|---|
| XS 360–639 | media above small copy; almost full-width bounded media; compact trail |
| SM 640–767 | adjacent media/copy; item <=780px; media <=500px |
| MD 768–1023 | adjacent item <=840px; media <=540px |
| LG 1024–1279 | item <=900px; media <=580px |
| XL 1280–1535 | item <=960px; media <=620px |
| 2XL >=1536 | item <=1020px; media <=660px |

All tiers use one DOM, one controller, one canvas, one trail, and one content
source. Short-height profiles constrain media by viewport height.

## Locale and direction

- ID and EN use LTR.
- AR uses the same DOM and scene in RTL.
- Text alignment follows logical start.
- Alternating media/copy order mirrors for RTL.
- Camera depth, vertical scroll, palette sequence, and trail chronology do not
  reverse for Arabic.

## Trail contract

- Trail is a responsive SVG path behind the media scene.
- Main line is approximately 1.35 CSS pixels with a restrained glow.
- `pathLength="1"` normalizes reveal math.
- The existing single RAF writes `stroke-dashoffset` from scene progress.
- Reduced motion shows the complete static trail without animation.
- Trail never captures pointer or focus.

## Performance and lifecycle

- No package or texture upload is introduced.
- Existing raw WebGL canvas remains lazy, DPR-capped, and low-power.
- One RAF owns media transforms, palette blend, and trail progress.
- Offscreen/hidden/BFCache/context-loss behavior remains bounded.
- WebGL failure retains the DOM palette and SVG trail.

## Proof gates

Source:
- removed visible number/type/date treatment;
- small black adjacent labels;
- intrinsic square-corner media;
- max 54px alternating displacement;
- curved SVG trail and progress binding;
- five tier boundaries above fluid XS;
- one RTL adapter;
- source files <=200 lines.

Automated:
- `git diff --check`;
- focused `HomeDepthGalleryTest`;
- `npm run check:structure`;
- `npm run build`;
- `php artisan test`.

Runtime:
- 360, 640, 768, 1024, 1280, 1536 widths;
- ID, EN, AR;
- Chromium and WebKit;
- normal/reduced motion, pointer/touch/keyboard, short height;
- trail progression, palette transition, media spacing, and lightbox focus.

## Known blocker

`npm run check:structure` previously failed on the unrelated
`resources/css/pages/welcome-hero.css` checksum. This Gallery batch does not
alter or conceal that blocker. Source publication is not runtime proof.
