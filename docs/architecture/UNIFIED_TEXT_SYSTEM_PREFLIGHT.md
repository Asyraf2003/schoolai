# Unified Text System — Repository Preflight

Status: IN_PROGRESS  
Purpose: freeze repository-state evidence before M00 text-role inventory begins  
Execution rule: no typography implementation while this preflight is incomplete

## 1. Why this preflight exists

The unified text-system project depends on `main` being the correct product baseline. Old agent branches may be unmerged by ancestry even when their intent has already been absorbed or superseded by later work. Therefore branch ancestry alone is not sufficient evidence that functionality is missing.

Classification must be based on:

```text
branch ancestry
+ patch uniqueness
+ current-main feature evidence
+ architectural intent
= branch status
```

Possible statuses:

- `PRESENT_IN_MAIN`
- `SUPERSEDED_BY_MAIN`
- `MERGE_ONLY_NO_UNIQUE_PATCH`
- `NEEDS_DEEP_AUDIT`
- `MISSING_FROM_MAIN`

No old branch is merged merely because `git branch --no-merged` or `git cherry` reports unique history.

## 2. Confirmed main baseline

Confirmed local/remote baseline after fetch/pull:

```text
branch: main
local main: a1ccaea180d5e2e7780f23c9fabe71d5b2dc6353
origin/main: a1ccaea180d5e2e7780f23c9fabe71d5b2dc6353
divergence: 0 / 0
working tree: clean
```

This baseline includes:

- `docs/architecture/UNIFIED_TEXT_SYSTEM_DOD.md`
- `docs/architecture/UNIFIED_TEXT_SYSTEM_RATIONALE.md`

## 3. Local branch audit

### `agent/native-article-canvas`

Evidence:

- branch appears ahead only because of four merge commits from `origin/main`;
- no unique non-merge commits were found;
- `git cherry origin/main agent/native-article-canvas` returned no patch entries.

Status:

```text
MERGE_ONLY_NO_UNIQUE_PATCH
```

Decision: do not merge.

### `agent/admin-ui-foundation`

Unique historical intent:

- create one `resources/css/pages/admin.css` file;
- load it through `resources/css/app.css`;
- remove the large inline admin stylesheet from `layouts/admin.blade.php`.

Evidence from current `main`:

- the old branch creates a monolithic `admin.css` of 1,863 lines;
- current `main` instead has a newer split admin architecture under `resources/views/layouts/admin/styles/*` plus current admin panel CSS entries;
- `layouts/admin.blade.php` explicitly includes focused style fragments such as foundation, topbar, forms, desktop shell, gallery management, statistics, notifications, and delete dialog.

Status:

```text
SUPERSEDED_BY_MAIN
```

Decision: do not merge. Reintroducing the branch would regress the newer split-source hardening architecture.

## 4. Remote branch families detected

Unmerged remote branches are being audited by feature family rather than individually.

Families currently identified:

1. Hero DB/admin/playback
2. Admin dashboard/unified UI
3. Article canvas Arabic/contextual UI
4. Gallery create/media-entry flow
5. Gallery video embeds

Each family must reach a final classification before M00 begins.

## 5. Hero family audit

### Current main evidence

Current `main` already contains a mature Hero implementation, including:

- `app/Models/HeroSlide.php`
- `app/Http/Controllers/Admin/HeroSlideAdminController.php`
- focused Hero controller concerns
- `app/Providers/HeroServiceProvider.php`
- Hero provider concerns for article-backed slides, DB injection, normalization, and route integration
- Hero migrations and seeder
- admin Hero index/form/partials
- homepage Hero rendering
- Hero JS split into slider-media and slider-playback modules
- feature/unit tests for admin Hero, database fallback, article placement, homepage rendering, and Hero video URLs

Current Hero behavior also explicitly rejects YouTube as Hero background media and uses native/direct video or image media. Current tests assert that legacy YouTube Hero media does not render.

### `origin/agent/hero-db-admin-clean`

Unique files are verification payload/workflow artifacts only:

- `.github/hero-db-admin.patch.gz.b64.part*`
- `.github/workflows/hero-db-admin-clean-verify.yml`

Unique commits are all verification/materialization chores rather than product implementation.

Status:

```text
SUPERSEDED_BY_MAIN
```

Decision: do not merge.

### `origin/agent/hero-db-admin-verify`

Unique files are verification/debug payloads only:

- patch payload chunks;
- verification markers/error capture files;
- temporary GitHub Actions verification workflows.

Unique commits are verification/debug/retrigger commits rather than product implementation.

Status:

```text
SUPERSEDED_BY_MAIN
```

Decision: do not merge.

### `origin/agent/hero-db-admin-final`

Historical branch intent includes:

- HeroSlide model/migration;
- Hero admin controller/index/form;
- Hero service-provider integration;
- Hero video URL normalization;
- tests for admin Hero, DB fallback, and video normalization;
- admin link to Hero manager.

Current `main` contains all of those product-level capabilities and has evolved them further into smaller concerns, article-backed Hero placement, additional migrations, seeding, and current tests.

Patch identity differs because the implementation continued evolving after the branch, but current-main functional evidence covers the branch's product intent.

Status:

```text
SUPERSEDED_BY_MAIN
```

Decision: do not merge.

### Pending Hero branches

The following branches still require evidence because the previous CLI audit was interrupted by the terminal pager before their output was captured:

- `origin/agent/hero-media-duration`
- `origin/agent/hero-youtube-background-clean`

Do not classify them solely from branch names or commit counts.

## 6. Preflight gate

Preflight remains:

```text
STATUS=IN_PROGRESS
```

M00 may begin only when every identified remote feature family is classified and there is no unresolved evidence of product functionality missing from `main`.

## 7. Next valid evidence

Capture the remaining two Hero branches with the Git pager disabled. Only after those two are classified may the Hero family be marked complete.
