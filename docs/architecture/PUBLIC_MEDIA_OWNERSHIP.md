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
- `site/brand/*-vN.*`
- `site/footer/*-vN.*`
- `site/providers/*-vN.*`
- `site/ornaments/*-vN.*`
- `site/seo/*-vN.*`

Static runtime media should be referenced through `config/media.php`, not
hardcoded to `/media/...` paths in views/composers.

Stable static objects that receive one-year immutable browser caching use a
versioned key. If bytes change, bump the `-vN` suffix. Never overwrite a
different object behind an already published immutable version.

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

## Versioned static publisher

The repository owns one bounded manifest at `config/media.php` under
`static_publish`.

It covers current static browser/runtime owners including:

- favicon and Apple touch icon;
- navbar/footer/structured-data logos;
- homepage Open Graph image;
- Vision/Gallery Islamic geometry ornaments;
- footer channel and partner marks;
- supported social/video-provider marks.

Publish command:

```bash
php artisan media:publish-static-r2 --dry-run
php artisan media:publish-static-r2
```

`StaticPublicMediaPublisher` must:

- refuse invalid source/key traversal;
- require `site/...` target keys;
- preserve configured immutable cache control;
- verify the object exists after upload;
- verify R2 object size matches the local source;
- treat an existing same-size version as already published;
- fail rather than overwrite an existing version whose size differs.

The source code may reference the future R2 URL before deployment, but
production must not be switched to that release until the publish command
reports `failed = 0`.

Local source files are retained until post-deploy production network evidence
proves the R2 runtime path. Upload success alone is not deletion proof.

## Homepage loading policy

Static ownership and loading priority are separate decisions.

- Opening hero video and its first-party poster are eager critical media.
- Vision paper artwork is R2-owned but emitted as `data-lazy-src` and hydrated
  only near the Vision viewport.
- Vision geometry patterns are R2-owned and only become active when the Vision
  motion compositor is mounted.
- Gallery ornament is R2-owned and only becomes an active CSS background when
  Gallery approaches the viewport.
- Native below-fold image lazy loading remains in place for Gallery/Footer
  media where applicable.

Do not make the opening hero lazy merely to improve a synthetic transfer score.
Its delivery asset must instead remain bounded and appropriate for eager use.

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

## 2026-08-29 PageSpeed static-media follow-up

A later production PageSpeed capture still proved Laravel-origin runtime
requests for `logo-nav.webp` and the two Gallery/Vision ornament files. A source
audit then found additional static public assets still represented by legacy
`public/media/**` paths in locale/config presentation data.

Decision:

- centralize runtime resolution in presenters/config instead of editing three
  large locale files independently;
- publish the complete bounded static manifest to versioned R2 keys before the
  next production release;
- keep legacy locale values as source compatibility data until production
  network proof confirms they are no longer emitted;
- do not deploy a release containing new R2 runtime URLs before the static
  publisher succeeds.
