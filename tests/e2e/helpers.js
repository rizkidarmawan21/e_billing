import { test as base, expect } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import { root, testEnv } from './environment.mjs';

export const test = base.extend({
  page: async ({ page }, use) => {
    resetFixtures();
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    page.on('console', message => { if (message.type() === 'error') errors.push(message.text()); });
    // Never follow external monitoring/auth links or rely on a CDN for assets.
    await page.route('**/*', route => {
      const url = new URL(route.request().url());
      return ['127.0.0.1', 'localhost'].includes(url.hostname) || ['data:', 'blob:'].includes(url.protocol)
        ? route.continue() : route.abort('blockedbyclient');
    });
    await use(page);
    expect(errors, 'Browser runtime/console errors').toEqual([]);
  },
});
export { expect };
export function resetFixtures() {
  execFileSync('php', ['tests/e2e/fixtures.php'], { cwd: root, env: testEnv });
}
export async function login(page, role, password = 'MobileTest!234') {
  await page.goto('/login');
  await page.locator('[name=email]').fill(`${role}@mobile.test`);
  await page.locator('[name=password]').fill(password);
  await page.locator('button[type=submit]').click();
  await expect(page).toHaveURL(role === 'owner' ? /\/owner\/dashboard$/ : /\/dashboard$/);
}
export async function healthy(page, url) {
  const response = await page.goto(url);
  expect(response?.status(), url).toBe(200);
  await expect(page.getByRole('heading').first()).toBeVisible();
  await expect(page.locator('body')).not.toContainText('Internal Server Error');
  await expect(page.locator('body')).not.toContainText('Undefined variable');
  await expect.poll(() => page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth), {
    message: `Document overflow at ${url}`,
  }).toBeLessThanOrEqual(1);
  expect(await page.locator('[id]').evaluateAll(nodes => {
    const ids = nodes.map(node => node.id);
    return ids.filter((id, i) => ids.indexOf(id) !== i);
  }), `Duplicate IDs at ${url}`).toEqual([]);
  const tables = page.locator('table.billing-table');
  for (const table of await tables.all()) {
    await expect(table).toHaveCSS('display', 'table');
    await expect(table.locator('..')).toHaveAttribute('tabindex', '0');
    const headings = (await table.locator('thead th').allTextContents()).map(s => s.trim());
    for (const row of await table.locator('tbody tr').all()) {
      for (const [i, cell] of (await row.locator('td').all()).entries()) {
        if (await cell.getAttribute('colspan')) continue;
        await expect(cell).toHaveAttribute('data-label', headings[i]);
      }
    }
  }
  const passwordIds = await page.locator('input[type=password]').evaluateAll(inputs => inputs.map(input => input.id));
  for (const id of passwordIds) {
    const input = page.locator(`input[id="${id}"]`);
    if (!await input.isVisible()) continue;
    expect(id, `Password ID at ${url}`).toBeTruthy();
    const toggle = page.locator(`button[aria-controls="${id}"]`);
    await expect(toggle).toHaveCount(1);
    await expect(toggle).toHaveAccessibleName(/password|sandi/i);
    await toggle.focus();
    await page.keyboard.press('Space');
    await expect(input).toHaveAttribute('type', 'text');
    await page.keyboard.press('Enter');
    await expect(input).toHaveAttribute('type', 'password');
  }
  for (const input of await page.locator('input:not([type=hidden]):not([type=submit]):not([type=button]),select,textarea').all()) {
    if (!await input.isVisible()) continue;
    expect(await input.evaluate(el => Boolean(el.labels?.length || el.getAttribute('aria-label') || el.getAttribute('aria-labelledby'))),
      `Unlabelled control ${await input.getAttribute('name')} at ${url}`).toBeTruthy();
  }
}
export async function submit(page, pattern = /Simpan/) {
  await page.getByRole('button', { name: pattern }).click();
}
export async function deleteRecord(page, row) {
  const button = row.getByRole('button', { name: /Hapus/i });
  page.once('dialog', dialog => dialog.dismiss());
  await button.click();
  await expect(row).toBeVisible();
  page.once('dialog', dialog => dialog.accept());
  await button.click();
  await expect(row).toHaveCount(0);
}
