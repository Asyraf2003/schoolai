# UI/UX Engineering — Current State and Progress Ledger

Status: BLOCKED_BY_MISSING_EVIDENCE
Updated: 2026-08-03
Repository: `Asyraf2003/schoolai`
Source main before correction: `70800053340caf6643998e09a743bd3ea61b5348`
Gallery correction source before ledger: `45c472e06b13eb61a250aeb7811f86751924c300`

Commit publication proves source state only. It does not prove rendering,
responsive parity, accessibility, performance, or browser lifecycle.

## Active production batch

Blueprint: `blueprints/2026-08-03-home-depth-gallery.md`

- ID: `HOME-GALLERY-003`
- State: `IMPLEMENTING`
- Surface: homepage Gallery only
- Reference: Houmahani Kane / Codrops Atmospheric Depth Gallery
- Protected: Hero, Vision/Mission, School Values, Programs, Articles,
  navigation, footer, dedicated Gallery page, translations, authentication,
  database schema, admin CRUD, dependencies, and existing media.

## FACT and owner correction

- The first Gallery implementation did not fully retire the former homepage
  controller/partial and incorrectly placed copy over cropped rounded images.
- Owner feedback requires adjacent copy, square media corners, intrinsic image
  proportions bounded only by maximum width/height, visible atmosphere changes,
  and the same system across all six tiers.
- Homepage Gallery still receives the existing six normalized database/content
  items and reuses the shared accessible lightbox.
- `gallery-story.blade.php`, `gallery-story.js`, and its homepage import are now
  removed. The shared lightbox remains because the new surface owns it.
- Mixed historical CSS modules still contain dormant old Gallery selectors plus
  active Programs/Footer/shared rules. They are not deleted in this atomic batch
  because their ownership and welcome.css checksum require a separate migration.
- No dependency, database, dedicated Gallery page, Hero, or other homepage
  section is changed.

## Corrected source contract

- Media and copy are adjacent siblings. Copy never overlays media.
- Images use `width: auto`, `height: auto`, `max-width`, `max-height`, and
  `object-fit: contain`; no image border radius or forced aspect ratio remains.
- Alternating media/copy order mirrors for RTL without creating a second DOM.
- One scene is fluid from 360px and declares boundaries at 640, 768, 1024,
  1280, and 1536px.
- XS stacks media/copy; SM through 2XL use adjacent composition with progressively
  bounded media and scene dimensions.
- DOM background color blends the active and next palette every render frame.
  WebGL uses the same palette with stronger atmospheric blobs.
- CSS palette progression remains visible if WebGL initialization or context is
  lost.
- One RAF remains suspended offscreen or while the document is hidden.
- Reduced motion remains a static semantic sequence.

## Source proof status

| Gate | Status | Evidence |
|---|---|---|
| Scope diff | `PASS_SOURCE` | compare from `70800053` contains Gallery owners, focused test, blueprint, and ledger only |
| Adjacent copy | `PASS_SOURCE` | Blade siblings plus CSS grid; no overlay positioning on copy |
| Intrinsic media | `PASS_SOURCE` | auto dimensions, max bounds, contain fit, square corners |
| Palette change | `PASS_SOURCE` | controller writes blended `--depth-atmosphere`; renderer consumes same palette |
| Six-tier source contract | `PASS_SOURCE` | fluid XS plus 640/768/1024/1280/1536 boundaries |
| RTL architecture | `PASS_SOURCE` | one DOM with mirrored CSS order and logical text alignment |
| Legacy active owner cleanup | `PASS_SOURCE` | old homepage partial/controller/import removed |
| Lifecycle/fallback | `PASS_SOURCE` | lazy entry, one RAF, offscreen/hidden suspension, context-loss CSS fallback |
| Dependency scope | `PASS_SOURCE` | no package or lockfile change |
| Previous clean worktree/diff | `PASS` | owner command reached structure gate after clean status/diff |
| `npm run check:structure` | `FAIL_PRE_EXISTING` | owner proof: `welcome-hero.css` checksum mismatch before this correction |
| Focused PHP test | `BLOCKED_BY_MISSING_EVIDENCE` | updated source contract not executed locally |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | structure gate remains unresolved |
| Full PHP suite | `BLOCKED_BY_MISSING_EVIDENCE` | not executed after correction |
| Chromium/WebKit runtime | `BLOCKED_BY_MISSING_EVIDENCE` | no browser matrix run after correction |
| Lighthouse/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | no comparable runtime run |

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G01 source/owner re-audit | `PASS` | active and retired Gallery owners inspected |
| G02 owner visual correction | `PASS` | adjacent copy, intrinsic sharp media, visible palette explicitly required |
| G03 blueprint correction | `PASS` | `HOME-GALLERY-003` updated |
| G04 semantic composition | `IMPLEMENTED_SOURCE` | media and copy separated while anchors/lightbox remain |
| G05 intrinsic media sizing | `IMPLEMENTED_SOURCE` | ratio preserved within tier-specific max bounds |
| G06 atmosphere synchronization | `IMPLEMENTED_SOURCE` | DOM and WebGL palette progression share camera state |
| G07 six-tier/RTL adapters | `IMPLEMENTED_SOURCE` | one scene and mirrored adjacent placement |
| G08 retired owner cleanup | `IMPLEMENTED_SOURCE` | old Blade/controller/import removed |
| G09 focused contract test | `IMPLEMENTED_SOURCE` | assertions updated; execution pending |
| G10 automated proof | `BLOCKED_BY_MISSING_EVIDENCE` | pre-existing Hero checksum stops required chain |
| G11 runtime matrix | `BLOCKED_BY_MISSING_EVIDENCE` | tiers/locales/engines/reduced motion untested |

## STATUS

The requested Gallery visual correction is implemented in source. It must not be
called responsive/browser PASS until the local focused test and runtime matrix
are executed. Mixed legacy CSS retirement remains a separate ownership migration,
not a hidden part of this visual patch.

## NEXT VALID STEP

After publication, pull current `main`, run the focused Gallery test directly,
and report its exact output. Do not begin another visual adjustment before that
contract result is known.
