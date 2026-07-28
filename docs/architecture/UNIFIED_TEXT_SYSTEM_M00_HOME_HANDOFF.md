# Unified Text System — M00 Homepage Handoff

Status: IN_PROGRESS
Scope: public homepage only
Purpose: compact continuation state for future sessions. Read this together with `UNIFIED_TEXT_SYSTEM_M00_HOME.md`; do not repeat completed discovery/runtime work.

## Authoritative evidence ledger

Primary evidence ledger:

- `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_HOME.md`
- primary ledger already records homepage render/source inventory, semantic-role observations, 598 typography declaration hits, CSS import order, Brave/CDP/Node harness proof, and EN computed-style proof at 1440 / 768 / 390.

## Latest local sync proof

User runtime proof after the M00 ledger commit:

```text
cd /home/asyraf/Code/laravel/school/schoolai && git pull --ff-only origin main
From github.com:Asyraf2003/schoolai
 * branch            main       -> FETCH_HEAD
Already up to date.
```

Conclusion: local checkout is synchronized with current `main` at this evidence point.

## Locale switch source proof

Public locale route in `routes/web/public.php`:

- method: `POST /bahasa/{locale}`;
- accepted locales: `id`, `en`, `ar`;
- writes session key `locale`;
- writes persistent cookie `site_locale`;
- route name: `language.switch`.

Locale middleware in `app/Http/Middleware/SetLocale.php` resolves locale in this order:

1. session `locale`;
2. cookie `site_locale`;
3. `config('app.locale')`.

Only `id`, `en`, `ar` are accepted. The selected locale is applied with `App::setLocale($locale)`.

Runtime audit decision: a fresh temporary Brave profile may set `site_locale` through CDP before page navigation. With no session locale present, this follows the application's real middleware fallback path and does not fake `document.documentElement.lang` or typography in the DOM.

## Current M00 state

Completed:

- repository preflight PASS;
- local sync PASS;
- homepage source/render inventory;
- content ownership classification;
- semantic-role baseline;
- 598 typography declaration source hits;
- CSS cascade/import evidence;
- Brave headless + CDP + Node WebSocket harness;
- EN/LTR runtime proof at 1440px;
- EN/LTR runtime proof at 768px;
- EN/LTR runtime proof at 390px.

Pending:

- ID/LTR runtime parity at 1440 / 768 / 390;
- AR/RTL runtime parity at 1440 / 768 / 390;
- compare EN / ID / AR semantic hierarchy;
- finalize homepage role inventory;
- close homepage M00 gate.

Approximate homepage-M00 progress: 90%.

## Next valid step

Run one read-only Brave/CDP audit covering ID and AR at all three required widths using the same representative selectors already used for EN. Confirm actual rendered `lang`, `dir`, font family, size, weight, line-height, and letter-spacing.

Do not repeat source discovery, import-order discovery, browser capability probes, or EN runtime audits.
