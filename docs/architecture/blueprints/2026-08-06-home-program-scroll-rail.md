# Homepage Program Scroll Rail Blueprint

State: `OWNER_ACCEPTED / IMPLEMENTING`
Date: 2026-08-06
Surface: homepage Program `#program`
Source baseline: `18c80280afd585c45fc2910d7f4f881c47285564`

## OWNER GOAL

Turn the six Program items into one continuous scroll experience. Parents only
scroll down or back up; they are not required to press previous/next controls.
Each active frame synchronizes a title, description, image, detail link, and a
side rail. After the sixth frame, the surface washes into blue with white lines
before Values.

## FACT

- The single Program heading and introduction currently finish the Vision/Mission
  story in `vision-mission.blade.php`.
- Program already has six localized items from `lang/{id,en,ar}/home.php`.
- Program is immediately followed by Values.
- The production CSP already permits `https://images.unsplash.com`.
- The existing Program controller and CSS are isolated owners imported from the
  homepage Program entry.
- Mobile navigation establishes the preferred blur, vertical travel, and
  `cubic-bezier(.16,1,.3,1)` motion character.

## SCOPE

Editable:

- `resources/views/home/sections/featured-programs.blade.php`
- `resources/css/pages/welcome/program-showcase-desktop.css`
- `resources/css/pages/welcome/program-journey/*`
- `resources/js/pages/welcome/program-cards.js`
- `resources/js/surfaces/home/program-journey/*`
- focused Program test and architecture records

Read-only constraints:

- Vision/Mission heading remains the single section heading.
- Values, Gallery, Articles, About, and Testimonial remain unchanged.
- Homepage order remains Vision/Mission -> Program -> Values.

## VISIBLE RESULT

1. The Program surface begins with the current Vision/Mission Program title and
   description as its visual handoff state.
2. During entry, the title settles toward inline-start; the description occupies
   a stable supporting area.
3. Six ordinary HTML image frames move vertically with native document scroll.
4. The active frame updates title, summary, description, detail link, accent,
   count, and side rail through blur/vertical WAAPI transitions.
5. The side rail stays at inline-end in LTR and inline-start in RTL. Its active
   line remains centered while the six-item rail moves behind it.
6. After scrolling stops inside the frame range, a cancellable RAF settle moves
   gently to the nearest frame boundary.
7. The final frame transitions into a blue full-viewport field with white lines,
   then releases naturally into Values.

## RESPONSIVE / LOCALE CONTRACT

- XS/SM: centered title, description below, side rail hidden, reduced font/media
  dimensions, same six vertical frames.
- MD: centered title and description; rail remains hidden where space is tight.
- LG 1024-1180: centered title/description with the rail enabled when space fits.
- XL/2XL >=1181: title at inline-start, rail at inline-end, description held in a
  neutral lower-center position, large editorial media.
- ID/EN use shared LTR composition. AR mirrors only title/rail placement through
  logical properties; vertical image progression and time are unchanged.
- One DOM and one controller serve all tiers/locales.

## MOTION / INPUT

- Native wheel, touch, keyboard, and scrollbar scrolling remain available.
- No wheel interception, full-page lock, mandatory horizontal travel, or required
  previous/next interaction.
- WAAPI owns copy and rail transitions.
- RAF owns only scroll-linked geometry and the cancellable soft settle.
- Any new wheel, touch, pointer, or key input cancels the settle immediately.
- Reduced motion removes WAAPI travel and automatic settling while preserving all
  content, images, links, and ordinary scroll.

## SEMANTIC / FAILURE RESULT

- All six articles, headings, descriptions, images, and PPDB links render in HTML.
- Without JS, each article displays its own text below its image.
- With enhancement, fallback text becomes visually hidden but remains available
  to assistive technology; the visible changing copy is decorative to avoid
  duplicated announcements.
- External image failure does not remove program text or the action path.

## MEDIA DECISION

Use six temporary static `images.unsplash.com` URLs. They are placeholders for
owner review and must later be replaced by licensed Al-Mustaqbal media with
program-specific crop, alt text, dimensions, provenance, and performance proof.

## PROOF GATES

- `git diff --check`
- `npm run check:structure`
- `npm run build`
- `php artisan test`
- six-frame DOM test in ID/EN/AR
- Chromium and WebKit visual review at all six width tiers
- forward/reverse/fast scroll, interruption, resize, reduced motion, RTL
- external-media failure fallback and PageSpeed delta
