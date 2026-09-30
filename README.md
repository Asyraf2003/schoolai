<div align="center">

# SchoolAI

### Production school digital platform with a multilingual public experience, role-based portals, and an operational CMS

**Laravel 13 · PHP 8.3+ · Tailwind CSS 4 · Vite 8 · Pest 4 · Three.js · S3-compatible media**

[Live Site](https://almustaqbal.sch.id) · [Product Surface](#product-surface) · [UI--CMS Engineering](#ui--cms-engineering) · [Engineering Controls](#engineering-controls) · [Setup](#local-setup) · [License](#license)

</div>

<p align="center">
  <img src=".github/assets/readme/homepage-desktop.png" alt="SchoolAI multilingual school homepage" width="100%">
</p>

> **SchoolAI is not a template-driven school website.** It combines a multilingual public-facing experience with article publishing, gallery composition, admissions presentation, role-specific portals, account/session controls, media workflows, and production deployment concerns in one maintained application.

---

## Why SchoolAI Exists

A school website becomes an operational product once staff need to update content without editing code, visitors need consistent information in multiple languages, students and teachers need separate portal boundaries, admissions content changes over time, and media has to remain manageable outside the application server.

SchoolAI was built around that reality.

The system separates the public presentation layer from authenticated operational surfaces while keeping content, media, account state, and publishing behavior under explicit application control.

---

## Product Surface

| Surface | What it handles |
|---|---|
| Public website | home experience, multilingual navigation, PPDB/admissions, articles, gallery |
| Language system | Indonesian, English, and Arabic public-site locales |
| Article platform | native article pages, article administration, visual canvas workflow, publishing |
| Homepage editorial control | article pinning and ordering for homepage placement |
| Gallery system | gallery items, configurable page sections, visibility, ordering, media placement |
| PPDB / admissions | public admissions surface, admin-controlled settings and showcase items |
| Admin portal | content operations, account administration, editorial and presentation controls |
| Teacher portal | dedicated authenticated teacher entry and dashboard boundary |
| Student portal | dedicated student login, dashboard, account page, and password management |
| Authentication | traditional login, Google OAuth for staff flows, role-aware routing |
| Operational controls | active-account checks, active-session checks, audit logging, account state management |

---

## UI & CMS Engineering

The frontend is treated as product behavior rather than a collection of static pages.

### Multilingual public experience

The public site supports three explicit locales:

- Indonesian (`id`)
- English (`en`)
- Arabic (`ar`)

Locale switching is persisted and guarded so public language navigation does not redirect users into admin or authentication surfaces.

### Native article workflow

The article system goes beyond basic CRUD. The administration layer includes:

- article creation and editing;
- a dedicated article canvas;
- canvas autosave;
- image upload from the editor;
- optional Unsplash search integration;
- explicit publish actions;
- homepage pin/unpin controls;
- homepage ordering;
- soft-delete and restore workflows.

This makes editorial presentation part of the application itself instead of requiring developers to rebuild pages for routine content changes.

### Gallery composition

The gallery administration supports both individual media entries and higher-level page composition:

- gallery item CRUD;
- visibility toggles;
- manual ordering;
- soft delete / restore;
- configurable gallery page sections;
- section-level media placement;
- section activation and ordering behavior.

The important part is that media is not only stored. It is composed into controlled presentation structures.

### PPDB / admissions presentation

Admissions content is controlled from the admin surface through:

- PPDB settings;
- enable/disable behavior;
- showcase items;
- showcase ordering;
- edit, delete, and restore flows.

That keeps a time-sensitive public section editable without turning every admissions update into a deployment.

### Role-specific portal UX

Authentication routes are separated for:

- administrators;
- teachers;
- students.

The system resolves authenticated users into the correct dashboard and applies role, account-state, and active-session middleware before protected surfaces are reached.

---

## Backend Structure

The application is organized beyond controllers and models. The current source tree includes dedicated boundaries for:

| Area | Responsibility |
|---|---|
| `app/Actions` | focused application operations |
| `app/Services` | reusable operational and domain-facing services |
| `app/Rules` | custom validation behavior |
| `app/Enums` | explicit state/value definitions |
| `app/Http` | request, middleware, and controller boundaries |
| `app/Models` | persistence models |
| `app/Support` | cross-cutting application support |
| `app/View` | presentation-oriented application helpers |
| `routes/web` | public and authentication routing |
| `routes/admin` | separated admin feature routing |
| `tests` | feature and unit regression coverage |

The route structure is intentionally split by product area instead of accumulating every concern in one monolithic route file.

---

## Engineering Controls

SchoolAI includes controls for concerns that become relevant once the application is operated rather than merely demonstrated.

### Account and session boundaries

Protected portal routes use explicit middleware for:

- authentication;
- active account state;
- active session state;
- role boundaries;
- internal locale handling.

The codebase also contains dedicated services for account directory behavior, account revision handling, active-session management, audit logging, and PPDB access policy.

### Authentication

The application supports:

- administrator login;
- teacher login;
- student login;
- Google OAuth staff entry;
- throttled OAuth routes;
- role-aware dashboard routing;
- student password updates.

### Media architecture

Media storage is configurable independently from the application filesystem through an S3-compatible disk and external public media URL. The environment contract also supports long-lived cache-control behavior for immutable media assets.

### Regression coverage

The test suite includes coverage around application behavior such as:

- admin presentation;
- authentication flows;
- multilingual seed data;
- article media lifecycle;
- gallery media lifecycle;
- gallery canonical backfills;
- gallery media dimensions;
- hero content placement;
- hero database fallback behavior;
- media-related regression paths.

The intent is not to claim that tests make UI or production behavior magically correct. They exist to make previously defined behavior harder to break quietly.

---

## Technology Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.3+ |
| Authentication | Laravel auth flows, Socialite / Google OAuth |
| Frontend | Blade, Tailwind CSS 4 |
| Build tooling | Vite 8 |
| Interactive graphics | Three.js |
| Media | Laravel filesystem with S3-compatible storage |
| Testing | Pest 4 |
| Static analysis / quality tooling | Larastan, PHP Insights, Pint |
| Database | Laravel-supported relational database layer |
| Deployment | cPanel-oriented production package workflow |

---

## Verification

Useful project checks are kept close to the repository:

```bash
composer test
npm run build
npm run check:structure
```

Development dependencies also include Larastan, PHP Insights, and Pint for static analysis and source-quality workflows.

---

## Deployment

The repository contains a dedicated cPanel packaging workflow rather than assuming an unrestricted VPS environment.

```bash
make deploy
```

The deployment Makefile builds a package around explicit application, public-directory, deployment-directory, and site URL parameters. This keeps the production packaging step reproducible instead of relying on manual file copying as institutional knowledge.

---

## Local Setup

```bash
git clone https://github.com/Asyraf2003/schoolai.git
cd schoolai

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

npm install
npm run build

php artisan serve
```

For the combined development workflow:

```bash
composer run dev
```

Configure OAuth and external media credentials only through environment variables. No production credentials are intended to live in the repository.

---

## What This Repository Demonstrates

SchoolAI is useful as a portfolio project because its complexity is spread across both product design and application behavior:

- building a multilingual public product rather than a single-language landing page;
- maintaining separate admin, teacher, and student interaction boundaries;
- turning content editing into an actual editorial workflow;
- supporting article composition, autosave, media upload, and publishing;
- building configurable gallery and admissions presentation systems;
- enforcing account and active-session rules around authenticated portals;
- keeping media infrastructure externalizable from the application host;
- packaging a modern Laravel application for constrained shared-hosting deployment;
- maintaining test coverage around presentation, media, multilingual data, and regressions.

It represents the product-facing side of my engineering work: substantial UI and content-management behavior backed by explicit server-side controls.

---

## Related Project

For transaction-heavy backend and financial-integrity work, see **[GlassPos](https://github.com/Asyraf2003/GlassPos)**, a workshop POS and operational system built around state coherence, auditability, inventory, payments, refunds, and reporting.

---

## License

Copyright © 2026 Asyraf Mubarak. All rights reserved.

This repository is **source-available for viewing and portfolio evaluation, not open source**. No permission is granted to use, copy, modify, distribute, sublicense, sell, or create derivative works from this project without prior written permission.

See [`LICENSE`](LICENSE) for the full terms.

<div align="center">

### A school website becomes software engineering when content, roles, media, language, and operations all have to stay coherent.

</div>
