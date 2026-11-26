import { test, expect, healthy, login, submit } from './helpers.js';

test('owner can find a customer whose monthly bill has not been created', async ({ page }) => {
  await login(page, 'owner');

  const name = `Ayu ${'NamaPanjang'.repeat(9)}`;
  await page.goto('/customers/create');
  await page.locator('[name=kode_pelanggan]').fill('COVERAGE-TEST');
  await page.locator('[name=nama]').fill(name);
  await page.locator('[name=no_hp]').fill('081234567890');
  await page.locator('[name=alamat]').fill('Alamat tes');
  await submit(page);
  await expect(page).toHaveURL(/\/customers$/);

  await page.goto('/customers?search=Ayu');
  const row = page.getByRole('row').filter({ hasText: 'COVERAGE-TEST' });
  const edit = await row.getByRole('link', { name: /^Edit customer/ }).getAttribute('href');
  await page.goto(edit);
  await page.locator('[name=package_id]').selectOption('1');
  await submit(page);
  await expect(page).toHaveURL(/\/customers$/);

  await healthy(page, '/owner/dashboard?all=1');
  const panel = page.getByRole('region', { name: 'Tagihan Belum Dibuat' });
  await expect(panel).toContainText(/Periode berjalan: \w+ \d{4}/);
  const lastChart = page.getByRole('img', { name: 'Distribusi paket pelanggan' });
  expect((await panel.boundingBox()).y).toBeGreaterThan((await lastChart.boundingBox()).y);
  const customer = panel.getByRole('link', { name, exact: true });
  await expect(customer).toBeVisible();
  await expect(customer.locator('xpath=ancestor::li')).toContainText('Mobile Package 01');
  await customer.focus();
  await expect(customer).toBeFocused();
  await customer.press('Enter');
  await expect(page).toHaveURL(edit.replace(/\/edit$/, ''));
  await expect(page.getByText('COVERAGE-TEST', { exact: true })).toBeVisible();
});
