# Unified Text System — M00 Public Gallery Evidence

Status: IN_PROGRESS
Scope: `/galeri` public gallery page only
Purpose: persist M00 baseline evidence for the public gallery surface without repeating homepage discovery.
Implementation rule: M00 is discovery/documentation only. No text-system implementation or styling cleanup in this file.

## 1. Route and layout

Route:

- `GET /galeri` -> `GalleryPageController`.

View:

- `resources/views/pages/galeri.blade.php`.

Shared layout:

- `resources/views/layouts/public.blade.php`.

The public layout renders locale-aware `<html lang>` / `dir`, shared navbar/footer, and loads:

- `resources/css/pages/welcome.css`;
- `resources/css/pages/welcome-hero.css`;
- `resources/css/arabic-typography.css`;
- homepage/shared public JS entries.

Therefore `/galeri` inherits the same large welcome CSS cascade already proven on the homepage, but its route-specific selectors must still be measured at runtime where cascade ambiguity matters.

## 2. Content-source map

### 2.1 Page shell

`GalleryPageController` loads `__('pages.galeri')` and passes it as `$page`.

Locale files:

- `lang/id/pages.php`;
- `lang/en/pages.php`;
- `lang/ar/pages.php`.

The Blade view uses `$page` for:

- page metadata/title/description;
- Hero heading;
- Hero subtitle;
- decorative Hero flash-card labels;
- gallery wall section title;
- fallback gallery items when DB items are absent;
- localized lightbox labels through `pages.common.*`.

### 2.2 Main gallery items

`GalleryPageController::galleryItems()` uses published `GalleryItem` rows when `gallery_items` exists.

Locale-selected DB content includes:

- title;
- type label/badge.

Media URL and thumbnail are presentation/media data, not text typography data.

The Blade view prefers DB items over `$page['items']` fallback.

### 2.3 Gallery subsections

`GalleryPageController::gallerySections()` uses published:

- `GalleryPageSection`;
- `GalleryPageMediaItem`.

Locale-selected section text:

- section title;
- section description.

Subsection media items currently receive the section title as their accessibility/data label and do not receive an independent visible item title.

## 3. Rendered text inventory

### Route-specific visible text

| Render location | Selector / element | Source | Proposed role |
|---|---|---|---|
| Gallery page Hero heading | `.gallery-wall-hero-card h1` / `#galeri-title` | `pages.galeri` lang | `page-title` |
| Gallery page Hero subtitle | `.gallery-wall-hero-card p` | `pages.galeri` lang | `subtitle` |
| Hero flash label | `.gallery-wall-flash small` | `pages.galeri` lang | `label` |
| Main wall heading | `.gallery-wall-head h2` | `pages.galeri` lang | `section-title` |
| Subsection heading | `.gallery-wall-subsection__head h2` | DB `GalleryPageSection` | `section-title` |
| Subsection description | `.gallery-wall-subsection__head p` | DB `GalleryPageSection` | `description` |
| Social-video provider brand when fallback cover renders | `.social-video-cover__brand` | item/provider presentation | `label` |
| Social-video play hint when fallback cover renders | `.social-video-cover__hint` | `pages.common.play_media` | `action` / `label` pending exact interaction intent |
| Lightbox visible close button | `.gallery-wall-lightbox__close` | `pages.common.close` | `action` |

### Accessibility-relevant text

| Render location | Source | Role / classification |
|---|---|---|
| Gallery card `alt` | DB/fallback item label | accessibility label, content-derived |
| Gallery card `data-gallery-title` | DB/fallback item label | JS media title source |
| Lightbox dialog `aria-label` | `pages.common.view_gallery` | accessibility action/dialog label |
| Lightbox backdrop `aria-label` | `pages.common.close` | accessibility action |
| JS-created video iframe `title` | card title or localized gallery-video fallback | accessibility media title |
| JS-created image `alt` | card title | accessibility media label |

## 4. Important current-render fact

`resources/views/pages/partials/gallery-wall-card.blade.php` currently renders media only.

It does **not** render a visible `.gallery-wall-card__caption`, card `<h3>`, or visible DB gallery title.

However legacy/current CSS in `resources/css/pages/welcome/041-welcome-cascade-041.css` still contains typography rules for:

- `.gallery-wall-card__caption span`;
- `.gallery-wall-card__caption h3`.

Therefore those selectors are source declarations without a proven current render target on `/galeri` and must not be counted as visible gallery-page typography during M00 unless markup changes or contradictory runtime evidence appears.

This is exactly why M00 classifies rendered text rather than blindly inventorying every CSS selector.

## 5. Route-specific CSS ownership evidence

Primary gallery-wall typography appears in late welcome cascade modules.

### `040-welcome-cascade-040.css`

Contains:

- `.gallery-wall-hero-card h1`: display family, fluid page-title sizing, line-height and negative letter-spacing;
- `.gallery-wall-hero-card p`: fluid subtitle sizing and heavy weight;
- `.gallery-wall-flash small`: heavy label weight;
- `.gallery-wall-head h2`: display family and fluid section-title sizing.

### `041-welcome-cascade-041.css`

Contains:

- dead/unproven card-caption typography noted above;
- `.gallery-wall-lightbox__close`: weight 900;
- media/fallback sizing rules.

### `042-welcome-cascade-042.css`

Contains:

- mobile gallery adjustments;
- `.gallery-wall-subsection__head h2`: display family, fluid size, line-height, negative letter-spacing;
- `.gallery-wall-subsection__head p`: weight 760 and line-height 1.72;
- mobile dead/unproven `.gallery-wall-card__caption h3` sizing.

### `047-welcome-cascade-047.css`

Contains a mobile override for `.social-video-cover__brand` to `1.2rem` below 560px.

Arabic typography adapter is loaded after shared public CSS through `arabic-typography.css`, so Arabic winning family/scale must be proven by runtime rather than inferred from source order alone.

## 6. JS behavior

`resources/js/pages/welcome/gallery-wall.js`:

- reads `data-gallery-title` and media attributes from cards;
- creates iframe/image/fallback media in the lightbox;
- assigns iframe `title` from gallery title or localized fallback;
- assigns image `alt` from gallery title;
- may create an emoji-only fallback span marked `aria-hidden`;
- does not create a visible gallery caption/title;
- does not own typography sizing.

The visible lightbox close button is already rendered by Blade; JS only opens/closes/focuses it.

## 7. Shared text reuse

Shared navbar/footer classifications from homepage M00 remain valid as semantic roles and should not be rediscovered from scratch.

Route-specific runtime may still sample shared navigation/footer only if needed to detect a route/body-class cascade difference. Do not repeat the full homepage shared-component audit by default.

## 8. Current M00 gallery status

Completed:

- route/controller discovery;
- shared layout/assets discovery;
- lang/DB content-source classification;
- route-specific rendered text inventory;
- accessibility-relevant gallery text paths;
- route-specific CSS ownership discovery;
- JS lightbox text behavior discovery;
- dead/unproven gallery-card caption CSS identified and excluded from visible inventory.

Pending:

- runtime computed-style proof for route-specific representative text at 1440 / 768 / 390;
- ID / EN / AR + RTL parity proof;
- confirm which optional/fallback nodes are actually present in current runtime data;
- record baseline hierarchy findings;
- close `/galeri` M00 surface.

## 9. Next valid step

Run one corrected Brave/CDP runtime batch on `/galeri` using the already-proven real language-switch POST flow.

Measure only route-specific representative nodes:

- page title;
- Hero subtitle;
- Hero flash label when present;
- wall section title;
- DB subsection title/description when present;
- social-video fallback brand/hint when present;
- visible lightbox close action.

Required widths/locales:

- ID: 1440 / 768 / 390;
- EN: 1440 / 768 / 390;
- AR: 1440 / 768 / 390 and confirm RTL.

Do not measure dead `.gallery-wall-card__caption` selectors unless runtime markup proves they exist.
