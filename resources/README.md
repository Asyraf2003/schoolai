# Homepage V2 resources

Status: ACTIVE — Shell + Menu + Hero, EN-only.

- Laravel home route delivers `views/landing/index.blade.php`.
- `index.html` is the historical empty shell, not a parallel running frontend.
- `css/foundation/` owns baseline; `css/sections/{header,hero}.css` owns components.
- `js/sections/*-state.js` contains pure policy; sibling modules adapt browser/DOM/media.
- `js/index.js` connects component ports and lifecycle only.
- Font assets include their existing OFL license. Business media keeps canonical CF URLs.
- No runtime import from `resources_old/` is allowed.
- Locale/direction/responsive skeleton files remain available; ID/AR are not activated.

Source of truth: `docs2/README.md` → active MAP-V2-01 and proof status.
No section beyond Menu + Hero may be added before Hero CLOSED and owner scope changes.
