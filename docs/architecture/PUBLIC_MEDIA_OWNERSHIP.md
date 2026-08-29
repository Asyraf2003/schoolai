# Public Media Ownership

Status: ACTIVE
Updated: 2026-08-30

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

Current surfaces include:

- `site/brand/`
- `site/footer/`
- `site/hero/`
- `site/navigation/`
- `site/ornaments/`
- `site/providers/`
- `site/school-life/`
- `site/seo/`
- `site/testimonials/`
- `site/vision/`

Static runtime media must be resolved through `config/media.php`, not fixed
`/media/...` or other cPanel-public paths in views, composers, CSS, or JS.

Stable static objects that receive one-year immutable browser caching use a
versioned key where applicable. If bytes behind a versioned object need to
change, publish a new `-vN` key and update `config/media.php`. Never replace
different bytes behind an already published immutable version.

### 3. User/admin uploaded media

Runtime owner:

- `R2MediaStorage`
- Cloudflare R2

Examples include:

- gallery media;
- article thumbnails and Canvas media;
- PPDB media;
- other admin-owned public uploads.

Do not copy these into cPanel `public_html`.

### 4. cPanel `public/` media

Long-lived content media is not owned by Laravel `public/`.

The repository intentionally keeps content media out of `public/media/**`,
`public/images/**`, root favicon files, and root Apple touch icon files.
`public/build/**` remains valid because it is generated Vite output, not public
content-media ownership.

Do not introduce new fixed `/media/...` or `/images/...` runtime references for
content media. A new long-lived public image/video should be published to R2
first, registered in `config/media.php`, and covered by an ownership test.

## Completed static R2 migration

The bounded static publish migration completed successfully on 2026-08-30.
The final publish reported all 21 manifest objects uploaded with `failed = 0`.
The resulting R2 inventory also includes the real school-life and testimonial
objects used by Program, Gallery, and Testimonial presentation.

After successful upload and repository conversion:

- `StaticPublicMediaPublisher` was retired;
- `media:publish-static-r2` was removed;
- the temporary `static_publish` manifest was removed;
- migrated local content-media source files were removed from `public/`;
- runtime ownership tests now reject legacy Vite `/media/` references and
  verify that homepage ID/EN/AR output does not emit stock-media hosts;
- CSP no longer permits the retired Unsplash or Finalsite image hosts.

The removed publisher was deliberately one-shot migration tooling. Do not
restore it as permanent infrastructure merely to publish an occasional static
asset. Future static additions should use a bounded operational upload step,
a new immutable key where needed, then a config/test change in the application.

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

## 2026-08-30 static-media closure

The later PageSpeed/static-media audit found remaining Laravel-origin media,
stock fallbacks, school-life placeholders, and testimonial background objects.
The closure batch:

- centralized public static ownership in `config/media.php`;
- published the bounded static inventory to R2 before deleting local sources;
- added 17 canonical school-life objects and 22 versioned testimonial objects;
- moved Program, Gallery, Testimonial, browser icons, ornaments, Vision art,
  footer/provider marks, SEO media, and article fallback presentation onto R2;
- migrated existing Gallery stock rows and placements to canonical school-life
  media while preserving the current Gallery section model;
- removed content-media binaries from Laravel `public/`;
- canonicalized legacy locale fallback media before presentation rather than
  duplicating media ownership across three translation trees;
- removed obsolete stock-image CSP permissions;
- passed the final security/dependency/regression workflow on `main`.

Legacy stock URLs can remain only as non-rendered compatibility/migration/test
fixtures. They are not runtime media owners and must never be emitted by public
HTML.
