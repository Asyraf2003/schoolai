# Unified Text System — M00 Public Gallery Evidence

Status: PASS
Scope: `/galeri` public gallery page only
Purpose: persist the factual M00 baseline for the public gallery surface so later sessions do not repeat discovery or runtime measurement.
Implementation rule: M00 is discovery/documentation only. No text-system implementation, styling change, semantic marker migration, or legacy cleanup was performed.

## 1. Route and layout

- `GET /galeri` -> `GalleryPageController`.
- View: `resources/views/pages/galeri.blade.php`.
- Shared layout: `resources/views/layouts/public.blade.php`.
- The shared public layout owns locale-aware `<html lang>` / `dir`, navbar, footer, and loads the shared welcome CSS plus `arabic-typography.css`.

Conclusion: `/galeri` inherits the same large shared cascade proven during homepage M00, but route-specific winning typography was measured independently at runtime.

## 2. Content-source map

### 2.1 Page shell

`GalleryPageController` loads `__('pages.galeri')` from:

- `lang/id/pages.php`;
- `lang/en/pages.php`;
- `lang/ar/pages.php`.

Locale-backed shell text includes:

- page metadata;
- Hero heading;
- Hero subtitle;
- decorative Hero flash labels;
- gallery wall heading;
- fallback items when DB items are absent;
- lightbox/common labels through `pages.common.*`.

### 2.2 Main gallery items

`GalleryPageController::galleryItems()` uses published `GalleryItem` rows when the table exists.

Locale-selected content includes:

- item title;
- type label/badge.

The Blade view prefers DB items over locale fallback items.

### 2.3 Gallery subsections

`GalleryPageController::gallerySections()` uses published `GalleryPageSection` and `GalleryPageMediaItem` rows.

Locale-selected visible section text:

- section title;
- section description.

Subsection media items currently reuse the section title as accessibility/data label and do not render an independent visible item title.

## 3. Final rendered text-role inventory

| Render location | Selector / element | Content source | Role |
|---|---|---|---|
| Gallery Hero heading | `.gallery-wall-hero-card h1` / `#galeri-title` | `pages.galeri` | `page-title` |
| Gallery Hero subtitle | `.gallery-wall-hero-card p` | `pages.galeri` | `subtitle` |
| Hero flash label | `.gallery-wall-flash small` | `pages.galeri` | `label` |
| Main wall heading | `.gallery-wall-head h2` | `pages.galeri` | `section-title` |
| DB subsection heading | `.gallery-wall-subsection__head h2` | `GalleryPageSection` | `section-title` |
| DB subsection description | `.gallery-wall-subsection__head p` | `GalleryPageSection` | `description` |
| Social-video provider brand, only when fallback cover renders | `.social-video-cover__brand` | provider presentation | `label` |
| Social-video play hint, only when fallback cover renders | `.social-video-cover__hint` | `pages.common.play_media` | `action` / compact UI label; finalize during migration if needed |
| Lightbox visible close button | `.gallery-wall-lightbox__close` | `pages.common.close` | `action` |
| Gallery card/image `alt` | media item label | DB/fallback | accessibility label |
| Gallery card `data-gallery-title` | media item label | DB/fallback | JS media-title source |
| Lightbox dialog/backdrop labels | `pages.common.*` | lang | accessibility action/dialog labels |
| JS-created video iframe `title` | card title or localized fallback | DB/lang | accessibility media title |
| JS-created image `alt` | card title | DB/fallback | accessibility media label |

## 4. Important current-render fact

`resources/views/pages/partials/gallery-wall-card.blade.php` currently renders media only.

It does not render a visible:

- `.gallery-wall-card__caption`;
- card `<h3>`;
- DB gallery title/caption overlay.

Legacy/shared CSS still contains `.gallery-wall-card__caption` typography declarations. These are not counted as visible M00 gallery typography because there is no current render target.

This distinction is intentional: M00 inventories rendered product text, not every historical selector in CSS.

## 5. JS behavior

`resources/js/pages/welcome/gallery-wall.js`:

- reads gallery title/media data from card attributes;
- creates iframe/image/fallback media inside the lightbox;
- assigns iframe `title` and image `alt`;
- may create an emoji-only `aria-hidden` fallback span;
- does not create a visible gallery caption/title;
- does not own typography sizing.

The visible lightbox close action is rendered by Blade. JS only opens, closes, and manages focus.

## 6. Runtime proof

Valid runtime proof used the application's real language-switch POST forms and measured the route at:

- 1440px;
- 768px;
- 390px;
- ID/LTR;
- EN/LTR;
- AR/RTL.

All required route-specific selectors were present for the valid runtime data.

`social-video-cover__brand` and `social-video-cover__hint` returned `found=false` in every measured locale/viewport because the current dataset did not render a provider fallback cover. This is an optional-content absence, not a typography failure.

### 6.1 ID and EN winning typography

ID and EN resolved to the same numeric typography at all measured widths.

| Role / node | 1440px | 768px | 390px | Family / weight |
|---|---:|---:|---:|---|
| `page-title` | 72px | 38.4px | 36px | ui-rounded / 700 |
| Hero `subtitle` | 19.84px | 19.84px | 17.95px | system-ui / 760 |
| flash `label` | 13.3333px | 13.3333px | 13.3333px | system-ui / 950 |
| wall `section-title` | 46.08px | 29.6px | 29.6px | ui-rounded / 700 |
| subsection `section-title` | 43.2px | 27.2px | 27.2px | ui-rounded / 700 |
| subsection `description` | 16px | 16px | 16px | system-ui / 760 |
| lightbox `action` | 16px | 16px | 16px | system-ui / 900 |

Observed responsive behavior is component-specific but stable between ID and EN.

### 6.2 Arabic winning typography

Arabic correctly rendered `lang="ar"` and `dir="rtl"` at all three widths.

| Role / node | 1440px | 768px | 390px | Family / weight |
|---|---:|---:|---:|---|
| `page-title` | 72px | 38.4px | 36px | Cairo / 700 |
| Hero `subtitle` | 36px | 36px | 36px | Lateef / 760 |
| flash `label` | 13.3333px | 13.3333px | 13.3333px | Lateef / 950 |
| wall `section-title` | 46.08px | 29.6px | 29.6px | Cairo / 700 |
| subsection `section-title` | 43.2px | 27.2px | 27.2px | Cairo / 700 |
| subsection `description` | 36px | 36px | 36px | Lateef / 760 |
| lightbox `action` | 16px | 16px | 16px | Cairo / 900 |

Arabic adapter behavior is therefore factual:

- display/heading/action surfaces resolve to Cairo where expected;
- prose/subtitle/description surfaces resolve to Lateef;
- negative Latin letter-spacing is removed;
- Arabic prose has substantially larger optical sizing than ID/EN in the current baseline.

The large Arabic prose scale is recorded as a migration finding, not fixed during M00.

## 7. Baseline conclusions

1. ID and EN have equivalent computed typography on `/galeri` for every measured route-specific node.
2. AR/RTL is functionally active and uses the intended Cairo/Lateef split.
3. Arabic prose sizing is materially larger than ID/EN, for example 36px subtitle/description where ID/EN are roughly 16–20px.
4. `page-title` and section-title responsive scales are stable across locales, apart from Arabic family/letter-spacing adaptation.
5. Optional social-video fallback text was not rendered by the current runtime dataset and must not be invented into the visible baseline.
6. Historical `.gallery-wall-card__caption` CSS has no current visible render target and remains excluded from the visible inventory.
7. No DB presentation fields or JS typography logic are needed to explain the current gallery text behavior.

## 8. M00 gallery closure

Completed:

- route/controller/layout discovery;
- lang/DB content-source classification;
- visible and accessibility-relevant text-role inventory;
- JS-generated/populated text-path inventory;
- route-specific CSS ownership evidence;
- dead/unrendered card-caption selector exclusion;
- ID runtime proof at 1440 / 768 / 390;
- EN runtime proof at 1440 / 768 / 390;
- AR/RTL runtime proof at 1440 / 768 / 390;
- baseline locale/responsive hierarchy findings.

No major visible text group on the current `/galeri` render remains unclassified.

Status: PASS

## 9. Next valid step

Do not repeat `/galeri` M00 discovery or runtime audits unless product markup/data materially changes.

Continue M00 baseline inventory with the next public surface: `/artikel` and the native article reader `/artikel/{article:slug}`.
