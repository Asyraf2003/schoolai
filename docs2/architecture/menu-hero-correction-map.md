# Technical map — Menu/Hero correction

OWNER_RAW: [verbatim](../owner/menu-hero-correction-raw.md).
OWNER_CONFIRMED: [active blueprint](../blueprints/menu-hero-correction.md).
Status: IMPLEMENTING; final closure requires owner UI review, merge prohibited.

## SOURCE / CURRENT BEHAVIOR / ROOT CAUSE
Audit table and patch locations: active blueprint. No new legacy runtime import.
Read-only existing business sources: SiteNavbarPresenter, SiteNavbarMenuPresenter,
lang/{en,id,ar}, SetLocale and language.switch route. No translation data copied to JS.

## OWNER / RESPONSIBILITY / INPUT / OUTPUT
| Owner | Input | State/output | Boundary |
|---|---|---|---|
| Header core | viewport, panel, focus, Hero outside | open, panel, concealed, scrolled | Pure header-state.js |
| Header adapter | DOM, matchMedia, Hero boundary port | details, focus, white surface, modal lock | header.js |
| Header motion | pointer, menu:open, reduced motion | WAAPI label effects, cancellable exit | header-motion.js / Header adapter |
| Language | native summary + form submit | session/cookie, page reload | Blade + existing language.switch/SetLocale |
| Hero core | manual step, audio, visibility, reduced motion | slide, play/advance permission | hero-state.js |
| Hero media | core state, actual media events | readiness, fallback, audio feedback | hero-media.js |
| Image fallback | load/error/complete/naturalWidth | data-ready; caption remains underneath | media-fallback.js |
| Hero title | available width, font metrics | bounded content fit | hero-title.js |

## INVARIANT / CSS ownership
- Header CSS owns all nav geometry, border accents, audio treatment, language popup.
- Desktop grid: media width min(20vw, available viewport-height budget * 2/3),
  ratio 2:3; nav stretched to same frame. 4 explicit equal rows, column auto-flow.
  Header height counts within top viewport inset; no overlay/shadow.
- <768: OWNER_RAW latest says menu only. Entire media figure hidden, no caption.
  768+ compact tablet uses image alongside four slots in shared frame.
- Full-menu capacity: >=1181px or >=1024px landscape. Coarse pointer has sound glyph;
  hover effects require hover + fine pointer. No device names or physical-inch guessing.
- Typography title:description approximately 2:1; readable minimum takes precedence
  over exact ratio at narrow widths. Empty slots stay empty.
- Core has no DOM/browser calls. Entry only wires Header audio and Hero boundary ports.
- Header color turns dark only after Hero bottom <=0, or when menu is open.
  No next section was added to create this boundary.
- Title baseline stays in Hero CSS; fitting adapter only handles measured content
  overflow, down to 1.5rem. It resets to baseline on resize/fonts-ready and never clips.
  ResizeObserver optional; resize listener fallback. No per-title patches or copy changes.

## MEDIA / FALLBACK / DEPENDENCY
Canonical CF URLs unchanged. Image opacity follows actual readiness, not bandwidth guesses.
Video poster remains until playing. Waiting/stalled hides video; after 8 seconds without
readiness the adapter marks failure, pauses and lets slideshow advance. This is an
AI_TRANSLATION technical loading budget, not a business requirement or universal
network-performance claim. Audio unavailable on failed media; existing semantic Hero
copy remains visible when both video/poster fail. Reduced motion prevents autoplay;
explicit audio intent may start video, but automatic slide advance stays disabled.
Focus within Hero copy temporarily suspends progression to preserve keyboard use.

## LANGUAGE / DIRECTION
Existing session/cookie switching, CSRF forms, presenter option labels and canonical flags.
Home delivery defaults EN only when no saved locale exists; it no longer overwrites
saved ID/AR. html lang/dir dynamic. Logical CSS + direction-aware manual Hero arrows.
RTL FOUNDATION READY; RTL VISUAL TUNING PENDING. No engine replacement, locale bundles,
Arabic pixel-perfect tuning, or unrelated middleware/auth changes.

## BROWSER SUPPORT / CLEANUP
Native details/forms function without JS. Optional WAAPI skipped if absent; reduced motion
skips motion and exit delay. Compact exit waits for animation.finished, not a timeout.
Listeners, frame callbacks, observers, timers and WAAPI animations cleaned at disposal.
Media/browser restrictions downgrade presentation to poster/content. WebKit automation
must never be reported as native Safari verification.

## PROOF / LEGACY REPLACEMENT
See [patch status](../proof/menu-hero-correction-status.md). Old EN-only and 1180 breakpoint
records are historical and superseded here. No admin/auth/section/CI migration performed.
