# Homepage Hero Scope Correction

BLUEPRINT ID: `HOME-HERO-SCOPE-CORRECTION-001`
STATUS: `PROVEN_PENDING_SQUASH`
OWNER: Asyraf Mubarak
DATE: 2026-08-01
SOURCE MAIN SHA: `763492d173cd504ad6f9b51c2626e0108166e6e5`
PROVEN BRANCH SHA: `e6f15793a538dea6392abd7ebacece1cb9f9a250`
ACTIVE SURFACE: homepage Hero presentation, media playback, and Demo 1 transition
TARGET BRANCH: `agent/home-hero-scope-correction-001`

## FACT

- The owner-provided rejected screenshot showed a dark Hero, a bottom transport
  rail, dots, counter, and a visible pause/play control.
- The owner-provided accepted earlier screenshot showed a bright Hero, compact
  lower-left copy, and two large yellow chevron controls floating in the
  lower-right area. It had no visible rail, dots, counter, or playback control.
- The accepted earlier brightness came from the former
  `resources/css/pages/welcome-hero-visual.css`, which replaced the heavy media
  overlay with a very light vertical gradient.
- The accepted earlier composition and chevrons came from the former
  `partials/home-hero-copy-layout.blade.php`. That partial was incorrectly
  coupled through the language-flag partial and was removed during Hero
  stabilization.
- The current owner modules correctly separate Hero Blade, CSS, controller,
  media, and deferred WebGL. The old language-flag coupling must not return.
- The rejected WebGL adaptation selected a ready poster before an incoming video
  produced a drawable frame. The canvas therefore displayed a static poster
  while the native video advanced underneath.
- The current fallback video can be served from a third-party origin. Sampling it
  as a WebGL texture would require a CORS-clean media contract that the Hero does
  not own and must not assume.
- The Demo 1 direction policy and transition lifecycle remain accepted.

## GAP

- Real Safari/WebKit and measured GPU/frame/color evidence remain unavailable.
- GitHub CI proves Chromium behavior and source contracts, but final visual
  acceptance still requires owner inspection after pulling the squash commit.

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
- Start the incoming native video immediately and reveal that same live element
  through the Demo 1 mask instead of replacing it with a poster or sampled copy.
- Keep the native video playing continuously; transition completion must not
  call `play()`, `load()`, or change `currentTime`.
- Add focused source/runtime proof for restored UI and continuous native video.

## SCOPE OUT

- Navbar, About, Testimonial, other homepage sections, DB/schema, translations,
  content, media crop/focal data, autoplay timing, and Demo 1 shader art style.
- New animation libraries, renderer engines, or package changes.
- Reintroducing the deleted language-flag/Hero coupling or anonymous overrides.
- Depending on third-party CORS headers to upload external video frames into
  WebGL textures.

## DECISION

1. Keep the current semantic Hero and one controller owner.
2. Replace the rejected control rail in Blade with the two proven native arrow
   buttons and their existing triple-chevron SVGs.
3. Move the accepted earlier visual values into the current named Hero modules:
   `layout.css`, `media.css`, `controls.css`, `responsive.css`, and `locale.css`.
4. Use one light vertical media overlay for both LTR and RTL. Text readability is
   carried by bounded text shadows, not a full-screen dark wash.
5. Preserve WebGL canvas layering beneath the same light overlay.
6. Select native-video reveal mode immediately when the incoming slide owns a
   video element. The transition must not wait for video dimensions or attempt
   to copy its pixels.
7. Keep the WebGL canvas transparent. Demo 1 retains its noisy directional mask,
   but the revealed region becomes transparent so the same native video playing
   underneath remains visible and continuous.
8. The native poster remains the media element's own loading/failure fallback.
   The shader must never restart, reload, seek, or duplicate the incoming video.
9. Image-to-image transitions continue using two ordinary textures.
10. CSS fallback remains complete when WebGL initialization fails.

## OWNERSHIP

- Hero Blade: visible controls and semantic media/content.
- `layout.css`: accepted copy geometry and text treatment.
- `media.css`: accepted light overlay and native poster/video visibility.
- `controls.css`: floating chevron geometry and focus/hover treatment.
- `responsive.css`: six-tier/short-height chevron and copy adaptation.
- `locale.css`: RTL semantic mirroring only, not a separate visual design.
- `textures.js`: immediate native-video reveal metadata and normal image texture
  acquisition; no external video pixel upload.
- `shaders.js`: Demo 1 mask plus transparent native-video reveal mode.
- `renderer.js`: transparent canvas composition, resource cleanup, and normal
  image texture transitions.
- Controller/events/direction: preserved unless proof exposes a direct defect.

## STATE CONTRACT

```text
active slide selected
-> native incoming video hydrates and play() is attempted immediately
-> Demo 1 starts immediately in native-video reveal mode
-> outgoing frame remains visible on the transparent WebGL canvas
-> mask progressively makes the canvas transparent
-> poster is visible only while the native video itself is still loading
-> the same advancing native video appears underneath when it starts playing
-> canvas releases
-> native video remains at the same playback position
```

Fallback:

```text
native video is slow or fails
-> its existing native poster/error fallback remains underneath the mask
-> no WebGL video texture is created
-> no restart, reload, or seek occurs on canvas release
```

## RESPONSIVE AND LOCALE CONTRACT

- Preserve one DOM and one controller across XS, SM, MD, LG, XL, and 2XL.
- Restore the floating chevrons fluidly on desktop/tablet and retain usable
  bounded positions on mobile.
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

Proven on branch SHA `e6f15793a538dea6392abd7ebacece1cb9f9a250`:

- diff hygiene: PASS;
- Vite 8.1.3 production build: PASS, 90 modules transformed;
- focused Hero/navigation: 11 passed, 236 assertions;
- Chromium matrix: 33 ID/EN/AR viewport cases PASS;
- exactly two visible Hero chevrons and no rail/dots/playback: PASS;
- copy/chevron collision and horizontal overflow: PASS;
- physical next/previous direction: PASS;
- automatic video-ended and image-timer direction for ID and AR: PASS;
- light overlay and corrected source ownership/equivalence: PASS;
- standard interaction, reduced-motion, no-JavaScript, failure, and BFCache
  behavior: PASS;
- native live-video continuity through the Demo 1 canvas: PASS;
- no stale canvas or transient slide state after settlement: PASS.

The runtime video proof creates a test-only animated canvas in Chromium and
attaches `canvas.captureStream()` to the existing native `<video>` element. It
verifies advancing video frames while the Demo 1 canvas is active, verifies the
same `MediaStream` remains attached after settlement, and does not change any
production media URL, database content, bundle, or playback code.

Known unrelated repository results remain recorded honestly:

- three pre-existing Vision/Mission source-limit failures;
- one stale About test for the intentionally disabled protected section;
- one pre-existing high-severity npm audit issue;
- package and lockfiles were not changed by this correction.

External proof remains:

- owner visual acceptance after pulling the squash commit;
- real Safari/WebKit visual and playback acceptance;
- measured Lighthouse/PageSpeed/CWV, GPU frame timing, and color parity on
  representative devices.

## ROLLBACK

Revert the correction squash commit. No package, schema, content, or database
migration is involved.

## NEXT VALID STEP

Publish one squash commit to `main`, then let the owner compare the actual page
against the accepted screenshot. Do not begin another Hero redesign or homepage
surface from this correction.