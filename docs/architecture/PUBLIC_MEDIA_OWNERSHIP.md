# Public Media Ownership

Status: ACTIVE
Updated: 2026-08-29

## Purpose

This document defines the runtime owner of public media for SchoolAI /
Al-Mustaqbal.

The goal is to prevent runtime assets from being deleted, duplicated between
origins, or silently falling back to cPanel `public_html`.

## Ownership rules

### 1. Vite-owned UI assets

Source:

- `resources/css/**`
- `resources/js/**`
- UI images/fonts intentionally imported by Vite

Runtime:

- `/build/assets/<hashed-file>`

Policy:

- hashed;
- one-year immutable browser cache;
- owned by the application release.

### 2. Static public runtime media

Runtime owner:

- Cloudflare R2
- `https://media.almustaqbal.sch.id`

Canonical prefix:

- `site/<surface>/...`

Examples:

- `site/hero/hero-school.webp`
- `site/navigation/activity.webp`
- `site/navigation/library.webp`
- `site/navigation/teaching.webp`
- `site/vision/vision-paper-01.webp`
- `site/vision/vision-paper-02.webp`
- `site/vision/vision-paper-03.webp`

Static runtime media should be referenced through `config/media.php`, not
hardcoded to `/media/...` paths in views/composers.

### 3. User/admin uploaded media

Runtime owner:

- `R2MediaStorage`
- Cloudflare R2

Examples include:

- hero uploads;
- gallery media;
- article thumbnails;
- testimonials;
- PPDB media.

Do not copy these into cPanel `public_html`.

### 4. `public/media/**`

`public/media/**` is legacy/source-only by default.

A file existing there does NOT automatically make it a supported runtime media
owner.

New long-lived public raster/video media should normally be moved to R2.

Do not introduce new fixed `/media/...` runtime references without an explicit
architecture reason.

## Cleanup safety rule

NEVER classify media as unused solely from one `rg`/source search.

Before deletion or quarantine, check all applicable owners:

1. Blade/PHP references;
2. CSS `url(...)`;
3. JavaScript hydration/runtime references;
4. `config/**`;
5. database media URLs;
6. generated HTML;
7. production browser/network trace.

Production network evidence is authoritative when static source ownership is
ambiguous.

## 2026-08-29 Vision/Mission incident

`vision-paper-01.webp`, `vision-paper-02.webp`, and
`vision-paper-03.webp` were incorrectly classified as unused during a public
media cleanup.

The files were still generated at runtime by
`HomeVisionMissionComposer` as `/media/home/vision-paper-XX.webp`.

After removal from the production public tree, Lighthouse reported three 404
responses and the Vision/Mission visual disappeared while its video player
remained functional.

Resolution:

- upload the three WebP assets to R2 under `site/vision/`;
- reference them through `config/media.php`;
- remove the legacy local runtime ownership;
- lock the R2 URLs in feature tests.

This incident is the reason production/runtime evidence is required before
future media deletion.
