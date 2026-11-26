# Mobile UI regressions

## Run

```sh
npm ci
npx playwright install chromium
npm run build
php artisan test --filter=MobileUiTest
npm run test:e2e
npm run test:e2e -- --project=chromium-390
npm run test:e2e:report
```

`npm run test:e2e -- --list` lists the full matrix. Chromium projects cover 320, 390, 768, 1024 and 1440px with admin and owner sessions. This is not Safari/iOS device verification.

## Isolation

- Playwright starts its own `php artisan serve --host=127.0.0.1 --port=8010 --tries=1 --no-reload`. An existing listener on that port is an error, never silently reused.
- The only SQLite database used is the absolute `tests/e2e/artifacts/mobile.sqlite` path. Every browser test recreates this test database before login. Keep one worker; do not run multiple suites against this same artifact directory concurrently.
- Fixtures refuse non-testing environments, non-SQLite connections, paths outside the test-owned directory, symlinks, and unsafe effective Laravel configuration before migration.
- Configuration/route/event cache paths and compiled views are test-owned. Cookie sessions use a separate cookie name. Cache is array, mail is array, queue is sync, sync integration is disabled and logs go to stderr. `.env` and the developer database are not edited.
- All non-local browser traffic is blocked; monitoring is checked as a rendered page, not by contacting its external service. CDN-dependent application code is a regression, not silently mocked.
- Test-only credentials: `admin@mobile.test` / `owner@mobile.test`, password `MobileTest!234`. These are freshly generated isolated fixtures, never a local user's account.
- Fixture data includes 12 long-name customers and packages, 24 paid/unpaid payments in the current year, and 12 secondary admins for pagination. Destructive workflows reset fixtures independently.
- `artifacts/` contains SQLite data, compiled views, JSON/HTML reports, screenshots and traces. It must be ignored by the repository's `.gitignore` (owned by the integration agent); never commit it.

## Assertions

All active GET page families are covered, including forms/details/profile/auth and owner report/monitoring/admin management. Each rendered page checks HTTP/render health, headings, document overflow, duplicate IDs, field labels, record column labels and keyboard-operable password toggles. Table wrappers are focusable and remain real tables. Shell tests exercise offcanvas focus containment, Escape, close controls, breakpoint resizing and the keyboard account dropdown.

Browser workflows cover customer/package/payment/admin create/edit/delete, server validation and old-input retention, delete dismissal/acceptance, customer and unpaid pagination/search, unpaid edit-return filters, profile updates and password update followed by a new login. Runtime JavaScript and console errors fail tests even when the workflow assertions pass. PHP tests independently cover the rendering contract, role authorization, invalid requests and unpaid redirects using an in-memory SQLite database.

A rendered 500, blank response, JavaScript error or broken existing controller remains a real failing assertion. There are no skips or expected-failure annotations hiding integration defects.
