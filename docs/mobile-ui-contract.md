# Mobile UI implementation contract

Approved scope: docs/prd/mobile-responsive-panel.md. All active admin/owner pages including CRUD/detail/profile/auth. No business/permission/schema changes. Branch feat/ui-mobile, no commit/push without request.

## Design reference

Keep existing Bootstrap 5 blue branding, system font, light surfaces. 320px: compact header with labeled Menu button and avatar; offcanvas navigation, content full width with 16px gutters. Desktop >=992px: persistent 250px sidebar and existing page proportions, max readable content. Page header stacks description/actions on phones; CRUD list records vertically labeled on phones, full tables >=768px. Forms single-column on phones, explicit wrapped action rows. No dark-mode/rebrand/new decorative animation.

## Parent-owned Blade components

- `<x-ui.page-header title="..." description="...">optional left content<x-slot:actions>existing controls</x-slot:actions></x-ui.page-header>`: optional props; outputs `.page-toolbar`, default slot if no title/description; actions slot responsive.
- `<x-ui.form-actions>existing buttons/forms</x-ui.form-actions>`: div `.form-actions`; attributes merged. Do not nest forms.
- `<x-ui.table>`: actual table with `.table.table-hover.align-middle.mb-0.mobile-records`, role=table, attribute merging; slot is thead/tbody. Keep existing table-responsive wrappers. Every normal td must have data-label matching column heading; colspan empty rows exempt. Parent CSS below768 uses block record layout and td::before labels; multiple child nodes remain naturally stacked. No duplicated mobile/desktop fields/forms.
- `<x-ui.field name="..." label="..." :required="true" help="...">input/select/textarea markup</x-ui.field>`: field wrapper renders label for name (optional id override), helper and errors. Slot controls retain exact name/id/value/old/autocomplete attributes and validation classes. Supports error-bag prop default='default'. Use where repeating pattern fits; do not force complex custom selectors.
- `<x-ui.password-input id="..." name="..." autocomplete="current-password" required />`: input-group, password default, Alpine visibility toggle, forwards input attrs including class, value forbidden. Outer label/errors remain caller-owned. Button 44px, accessible label changes, icons Bootstrap eye/eye-slash. Use for active login/reset/confirm/profile/admin-password fields, retain required/autocomplete and validation.
- `<x-ui.empty-state title="..." description="...">optional existing CTA</x-ui.empty-state>`: text-only state reusable where practical.

## CSS contracts

Parent owns resources/css/app.css, resources/js/app.js, layouts/admin and owner, components/ui/*. Scope panel styles to `.billing-app`; auth `.billing-auth`. Helpers: `.page-toolbar`, `.form-actions`, `.filter-menu` (max viewport width), `.dashboard-chart`, `.mobile-records`, `.record-actions` (wrap). No styling global .d-flex/.table outside scope. Tablet <=991.98 offcanvas. Mobile <=767.98 records; >=768 tables.

## Work ownership

1. Parent: shell/components/CSS/shared JS + integration verification.
2. CRUD-A: customers/*, packages/*, owner/admin/*, profile/*, auth/*, layouts/guest.blade.php (auth-only layout). Existing login edits preserved then replaced with reusable control.
3. CRUD-B: payments/* and vendor/pagination/* only.
4. Dashboard: dashboard.blade.php, owner/dashboard.blade.php, owner/laporan.blade.php, owner/monitoring.blade.php only.
5. Testing: package.json/package-lock.json, playwright.config.*, tests/e2e/*, tests/Feature/MobileUiTest.php. Use isolated SQLite testing DB and server 127.0.0.1:8010; never mutate local user data. Browser setup may add dev-only Playwright dependency. Fixtures explicitly test-only, sync disabled, queue sync/mail array. Protect seed script against non-testing env and DB path outside tests temp directory.

## Verification

php artisan test; npm run build; git diff --check. Browser test matrix 320/390/768/1024/1440, admin+owner all reachable screens including create/edit/detail, long fixture values, pagination, error states; drawer open/close/Escape/focus/resize, profile dropdown, passwords keyboard toggle, full CRUD and delete cancel/confirm. Strict document overflow check with only explicit local analytic scroll allowed. Test runtime errors and duplicated IDs. Chromium is not actual Safari/iOS coverage.
