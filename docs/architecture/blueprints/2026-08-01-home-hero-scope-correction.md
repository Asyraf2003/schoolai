# Homepage Hero Scope Correction

BLUEPRINT ID: `HOME-HERO-SCOPE-CORRECTION-001`
STATUS: `OWNER_ACCEPTED`
OWNER: Asyraf Mubarak
DATE: 2026-08-01
SOURCE MAIN SHA: `763492d173cd504ad6f9b51c2626e0108166e6e5`
ACTIVE SURFACE: homepage Hero presentation, media playback, and Demo 1 transition
TARGET BRANCH: `agent/home-hero-scope-correction-001`

## FACT

- The owner-provided current screenshot shows a dark Hero, a bottom transport
  rail, dots, counter, and a visible pause/play control.
- The owner-provided accepted earlier screenshot shows a bright Hero, compact
  lower-left copy, and two large yellow chevron controls floating in the
  lower-right area. It has no visible rail, dots, counter, or playback control.
- The accepted earlier brightness came from
  `resources/css/pages/welcome-hero-visual.css`, which replaced the heavy media
  overlay with a very light vertical gradient.
- The accepted earlier composition and chevrons came from
  `partials/home-hero-copy-layout.blade.php`. That partial was incorrectly
  coupled through the language-flag partial and was removed during Hero
  stabilization.
- The current owner modules correctly separate Hero Blade, CSS, controller,
  media, and deferred WebGL. The old language-flag coupling must not return.
- The current WebGL texture selector returns a ready poster immediately when an
  incoming video has not produced a drawable frame. The canvas then displays a
  static poster while the native video plays underneath.
- The Demo 1 direction policy and transition lifecycle are accepted and must
  remain intact.

## GAP

- Real Safari/WebKit and measured GPU/frame/color evidence remain unavailable.
- GitHub CI can prove Chromium behavior and source contracts, but final visual
  acceptance still requires owner inspection after pull.

## GOAL

Restore the owner-approved bright Hero composition and floating chevron UI,
remove the unintended dark treatment, and keep incoming native video visibly
continuous through the Demo 1 wipe without weakening transition direction,
fallback, lifecycle, responsive, locale, or accessibility behavior.

## SCOPE IN

- Restore the accepted Hero media overlay and text-shadow treatment inside the
  current Hero CSS owners.
- Restore the accepted copy geometry and floating previous/next chevrons.
- Remove the unrequested visible progress rail, counter, dots, and playback UI
  from Hero Blade.
- Keep native previous/next buttons, keyboard, swipe, autoplay, locale direction,
  live region, one-active-slide state, and reduced-motion behavior.
- Make incoming video texture selection wait for a live drawable video frame
  before falling back to poster.
- Keep the native video playing continuously; transition completion must not
  call `play()`, `load()`, or change `currentTime`.
- Add focused source/runtime proof for restored UI and continuous video texture.

## SCOPE OUT

- Navbar, About, Testimonial, other homepage sections, DB/schema, translations,
  content, media crop/focal data, autoplay timing, and Demo 1 shader art style.
- New animation libraries, renderer engines, or package changes.
- Reintroducing the deleted language-flag/Hero coupling or anonymous overrides.

## DECISION

1. Keep the current semantic Hero and one controller owner.
2. Replace the current control rail in Blade with the two proven native arrow
   buttons and their existing triple-chevron SVGs.
3. Move the accepted earlier visual values into the current named Hero modules:
   `layout.css`, `media.css`, `controls.css`, `responsive.css`, and `locale.css`.
4. Use one light vertical media overlay for both LTR and RTL. Text readability is
   carried by the previously accepted bounded text shadows, not a full-screen
   dark wash.
5. Preserve WebGL canvas layering beneath the same light overlay.
6. For an incoming video slide, wait up to the existing bounded texture window
   for a drawable live video frame. Use poster/image only after that wait fails.
7. Once a video source is selected, update that texture every RAF as already
   supported by the renderer. Do not restart or seek the video.
8. CSS fallback remains complete when WebGL or live texture acquisition fails.

## OWNERSHIP

- Hero Blade: visible controls and semantic media/content.
- `layout.css`: accepted copy geometry and text treatment.
- `media.css`: accepted light overlay and native poster/video visibility.
- `controls.css`: floating chevron geometry and focus/hover treatment.
- `responsive.css`: six-tier/short-height chevron and copy adaptation.
- `locale.css`: RTL semantic mirroring only, not a separate visual design.
- `textures.js`: live-video-first incoming texture acquisition.
- `renderer.js`: unchanged continuous dynamic texture upload and cleanup.
- Controller/events/direction: preserved unless proof exposes a direct defect.

## STATE CONTRACT

```text
active slide selected
-> native incoming video hydrates and plays immediately
-> outgoing frame remains visible on canvas
-> live incoming video frame becomes drawable
-> Demo 1 wipe samples the advancing video every RAF
-> canvas releases
-> the same native video remains visible at the same playback position
```

Fallback:

```text
live frame unavailable within bounded wait
-> poster/image texture used
-> native media remains the underlying owner
-> no restart/seek on canvas release
```

## RESPONSIVE AND LOCALE CONTRACT

- Preserve one DOM and one controller across XS, SM, MD, LG, XL, and 2XL.
- Restore the proven floating chevrons fluidly on desktop/tablet and retain
  usable bounded positions on mobile.
- Preserve ID/EN LTR and AR RTL copy/font contracts.
- Manual controls keep physical direction; automatic changes keep locale
  direction.
- RTL mirrors glyph direction and logical placement without changing brightness.

## ACCESSIBILITY AND FALLBACK

- Previous/next remain native buttons with localized accessible names.
- Live region remains.
- Keyboard, pointer, and swipe behavior remain.
- No-JS first slide remains meaningful.
- Reduced motion disables autoplay/WebGL and retains complete static content.
- Canvas remains decorative and `aria-hidden`.

## PROOF

Required automated proof:

- diff hygiene and source structure;
- Vite production build;
- focused Hero/navigation tests;
- full Laravel result recorded honestly;
- Chromium 33-case locale/width matrix;
- physical and automatic direction preserved;
- exactly two visible Hero navigation buttons and no visible rail/dots/playback;
- light overlay source contract and no heavy uniform overlay token;
- incoming video transition chooses a dynamic video texture when a frame becomes
  available within the bounded wait;
- no second `play()`, `load()`, or active-video seek at transition settlement;
- no stale canvas, RAF, transient class, listener, or timer.

External proof remains:

- real Safari/WebKit visual acceptance;
- owner screenshot comparison;
- measured GPU/frame and color parity on representative devices.

## ROLLBACK

Revert the correction squash commit. No package, schema, content, or database
migration is involved.

## ACTIVE STEP

Implement this correction only, run the focused Chromium proof, update current
state with exact evidence, then publish one reviewable PR. Do not begin another
surface.