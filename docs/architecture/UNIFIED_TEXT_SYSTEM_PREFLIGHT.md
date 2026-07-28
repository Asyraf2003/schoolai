# Unified Text System — Repository Preflight

Status: IN_PROGRESS  
Purpose: freeze repository-state evidence before M00 text-role inventory begins  
Execution rule: no typography implementation while this preflight is incomplete

## 1. Why this preflight exists

The unified text-system project depends on `main` being the correct product baseline. Old agent branches may be unmerged by ancestry even when their intent has already been absorbed, reimplemented, or deliberately superseded by later work.

Branch ancestry alone is therefore not evidence that functionality is missing.

Classification uses:

```text
branch ancestry
+ patch uniqueness/equivalence
+ current-main feature evidence
+ later product policy
+ architectural intent
= branch status
```

Allowed statuses:

- `PRESENT_IN_MAIN`
- `SUPERSEDED_BY_MAIN`
- `SUPERSEDED_BY_MAIN_POLICY`
- `MERGE_ONLY_NO_UNIQUE_PATCH`
- `NEEDS_DEEP_AUDIT`
- `MISSING_FROM_MAIN`

No old branch is merged merely because `git branch --no-merged` or `git cherry` reports unique history.

## 2. Product baseline

The code baseline confirmed before preflight documentation began was:

```text
branch: main
product HEAD: a1ccaea180d5e2e7780f23c9fabe71d5b2dc6353
working tree: clean
```

Later `main` commits in this preflight are documentation-only unless explicitly recorded otherwise.

The unified-text planning documents are:

- `docs/architecture/UNIFIED_TEXT_SYSTEM_DOD.md`
- `docs/architecture/UNIFIED_TEXT_SYSTEM_RATIONALE.md`
- this preflight document

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

Historical intent:

- create one `resources/css/pages/admin.css` file;
- load it through `resources/css/app.css`;
- remove the large inline admin stylesheet from `layouts/admin.blade.php`.

Current-main evidence:

- the historical branch creates a monolithic `admin.css` of 1,863 lines;
- current `main` instead has the newer split architecture under `resources/views/layouts/admin/styles/*` plus current admin-panel CSS entries;
- `layouts/admin.blade.php` explicitly includes focused style fragments for foundation, topbar, forms, desktop shell, gallery management, statistics, notifications, delete dialog, and related admin surfaces.

Status:

```text
SUPERSEDED_BY_MAIN
```

Decision: do not merge. Reintroducing the branch would regress the newer split-source hardening architecture.

## 4. Remote feature families

Unmerged remote branches are audited by feature family rather than by raw branch count.

Families identified:

1. Hero DB/admin/playback — PASS
2. Admin dashboard/unified UI — PENDING
3. Article canvas Arabic/contextual UI — PENDING
4. Gallery create/media-entry flow — PENDING
5. Gallery video embeds — PENDING

Every family must reach a final classification before M00 begins.

## 5. Hero family audit

Family status:

```text
PASS
```

No Hero branch currently provides evidence of required product functionality missing from `main`.

### 5.1 Current-main Hero evidence

Current `main` contains:

- `HeroSlide` model;
- Hero migrations and seeding;
- Hero admin controller split into focused concerns;
- Hero admin index/form/partials;
- `HeroServiceProvider` and focused provider concerns;
- article-backed Hero placement;
- DB Hero injection and fallback behavior;
- homepage Hero rendering;
- Hero JS split into media and playback modules;
- focused feature/unit coverage for admin Hero, DB fallback, article placement, homepage Hero, and Hero video URL rules.

Current policy also explicitly rejects YouTube as Hero background media and removes legacy YouTube Hero media. Native/direct video or image media is the current supported Hero policy.

### 5.2 `origin/agent/hero-db-admin-clean`

Unique branch content is verification/materialization infrastructure:

- encoded patch payload chunks;
- temporary Hero clean-verification workflow;
- verification/debug chores.

Status:

```text
SUPERSEDED_BY_MAIN
```

Decision: do not merge.

### 5.3 `origin/agent/hero-db-admin-verify`

Unique branch content is verification/debug infrastructure:

- patch payload chunks;
- verification markers and captured failures;
- temporary verification/debug workflows;
- repeated verification/retrigger commits.

Status:

```text
SUPERSEDED_BY_MAIN
```

Decision: do not merge.

### 5.4 `origin/agent/hero-db-admin-final`

Historical product intent includes:

- HeroSlide model/migration;
- Hero admin controller/index/form;
- Hero service-provider integration;
- Hero video URL normalization;
- tests for admin Hero, DB fallback, and video normalization;
- admin link to Hero manager.

Current `main` contains those capabilities and has evolved them further into smaller concerns, article-backed placement, additional migrations, seeding, and current tests.

Status:

```text
SUPERSEDED_BY_MAIN
```

Decision: do not merge.

### 5.5 `origin/agent/hero-media-duration`

Patch evidence:

```text
- 06c19e0 feat: wait for hero media playback before advancing
+ 5aee09e chore: continue hero timing verification after dependency advisories
```

The actual product patch is patch-equivalent to `main`. The only patch-unique commit is verification/audit continuation.

Current-main runtime source further confirms the intent: `resources/js/pages/welcome-hero/slider-playback.js` states that video slides own their duration and advance after the media ends, while ordinary slides use the configured timer.

Status:

```text
PRESENT_IN_MAIN
```

Decision: do not merge.

### 5.6 `origin/agent/hero-youtube-background-clean`

Patch evidence:

```text
- 61b3922 fix: harden YouTube hero background playback
- aabdbfa test: cover clean YouTube hero playback parameters
- a9f202f fix: render YouTube hero as clean background media
+ f4771ed ci: continue audits for temporary hero verification
```

All three product/test patches are patch-equivalent in repository history. The only patch-unique commit is temporary CI continuation.

However, current `main` intentionally moved beyond that behavior:

- Hero admin validation rejects YouTube media;
- Hero media management reports YouTube as unsupported;
- migration `2026_07_22_000003_remove_legacy_youtube_hero_media.php` removes/replaces legacy YouTube Hero content;
- current tests assert Hero output does not render YouTube media.

Therefore the historical YouTube-background capability is not missing. It was later superseded by an explicit product policy.

Status:

```text
SUPERSEDED_BY_MAIN_POLICY
```

Decision: do not merge. Reintroducing this branch would conflict with current Hero media policy.

## 6. Hero family conclusion

Classification matrix:

| Branch | Status | Action |
| --- | --- | --- |
| `hero-db-admin-clean` | `SUPERSEDED_BY_MAIN` | do not merge |
| `hero-db-admin-final` | `SUPERSEDED_BY_MAIN` | do not merge |
| `hero-db-admin-verify` | `SUPERSEDED_BY_MAIN` | do not merge |
| `hero-media-duration` | `PRESENT_IN_MAIN` | do not merge |
| `hero-youtube-background-clean` | `SUPERSEDED_BY_MAIN_POLICY` | do not merge |

Hero family gate:

```text
HERO_PREFLIGHT=PASS
MISSING_REQUIRED_HERO_FUNCTIONALITY=NO_EVIDENCE
MERGE_OLD_HERO_BRANCHES=NO
```

## 7. Preflight gate

Overall preflight remains:

```text
STATUS=IN_PROGRESS
```

M00 may begin only when every identified remote feature family is classified and there is no unresolved evidence of required product functionality missing from `main`.

## 8. Next valid evidence

Audit exactly one remaining family next. Do not merge, delete, or rewrite branches during evidence collection.
