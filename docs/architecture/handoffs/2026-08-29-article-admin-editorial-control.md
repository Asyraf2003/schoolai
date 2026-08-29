# Article Admin Editorial Control Handoff

Date: 2026-08-29
Repository: `Asyraf2003/schoolai`
Branch target: `main`

## Decision

Article editorial placement is centralized on `/admin/artikel`.

The admin should not need separate pages to decide where an Article appears. Create, edit, and Canvas remain dedicated authoring surfaces; placement and ordering live on the Article index.

## Homepage Article

Public composition is now four cards:

1. Head / Lead
2. Rail
3. Rail
4. Rail

Editorial rule:

- maximum four pinned Articles;
- pinned Articles are ordered explicitly with `homepage_position`;
- empty slots are automatically filled from the newest publicly visible Articles;
- pinned Articles always precede automatic latest-fill Articles;
- future-dated, draft, and archived Articles cannot be newly pinned;
- removing a pin restores automatic latest-fill behavior for that slot.

Database state:

- `homepage_position = null`: not pinned;
- `homepage_position = 1..4`: pinned priority/order.

The public homepage query remains lean and reads presentation fields only.

## Hero Spotlight

Opening video remains fixed first.

Article Spotlight rule:

- zero to three Article slides after Opening;
- no automatic filling;
- only explicitly promoted public Articles appear;
- intended for current/high-value news, major announcements, and achievements;
- `hero_position = 1..3` controls order;
- runtime hard-limits Article Spotlight to three even if legacy data contains additional positions.

`/admin/hero` now owns Opening copy/CTA only. Article promotion and ordering are controlled from `/admin/artikel`.

## Article index UX

`/admin/artikel` now exposes two editorial trays above the Article list:

- Homepage Article: pinned rows plus AUTO latest-fill preview;
- Hero Spotlight: fixed Opening row plus pinned Spotlight Articles.

Pinned rows support:

- drag ordering;
- keyboard/button move up/down fallback;
- explicit save-order action;
- unpin without editing Article content.

Each Article row exposes placement toggles directly:

- `+ Homepage` / `Homepage #N`;
- `+ Spotlight` / `Spotlight #N`.

Placement controls are disabled for Articles that are not publicly visible yet.

The normal index action flow is now:

- Preview;
- Edit or Canvas;
- Archive.

The old Detail page remains route-compatible but is no longer the primary index workflow. External Article create/update returns to the Article index so placement can be managed immediately.

## Archive behavior

Archiving an Article clears both:

- `homepage_position`;
- `hero_position`.

Remaining positions are compacted. Restore does not silently restore old placement; editorial placement must be chosen again if still relevant.

## Visibility correctness

Public Article visibility now applies `published_at <= now()` to both native and external Articles.

This prevents a future-dated external Article from entering:

- `/artikel`;
- Homepage Article AUTO fill;
- Homepage pin eligibility;
- Hero Spotlight eligibility.

## Verification contracts

Focused tests updated/added:

- `tests/Feature/HomepageArticleSectionTest.php`;
- `tests/Feature/HomeArticleStorySourceTest.php`;
- `tests/Feature/Admin/HeroSlideAdminTest.php`;
- `tests/Feature/Admin/ArticlePlacementAdminTest.php`;
- `tests/Feature/ArticleR2MediaLifecycleTest.php`.

Recommended local proof after pulling `main`:

```bash
php artisan migrate
php artisan test --filter=HomepageArticleSectionTest
php artisan test --filter=HomeArticleStorySourceTest
php artisan test --filter=HeroSlideAdminTest
php artisan test --filter=ArticlePlacementAdminTest
php artisan test --filter=ArticleR2MediaLifecycleTest
npm run build
```

If focused proof passes, run the complete Article/Admin suite before deployment.

## Remaining debt

This change does not solve unrelated Article debt:

1. Arabic Canvas autosave parity;
2. inline Article R2 media reconciliation / orphan cleanup;
3. Article index search/status/source filtering if the content volume later requires it;
4. explicit permanent-retention/purge policy for archived Articles.
