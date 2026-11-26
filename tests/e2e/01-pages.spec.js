import { test, expect, healthy, login } from './helpers.js';

const shared = ['/dashboard', '/customers', '/customers/create', '/customers/1', '/customers/1/edit',
  '/packages', '/packages/create', '/packages/1', '/packages/1/edit', '/payments', '/payments/create',
  '/payments/1', '/payments/1/edit', '/payments/unpaid', '/profile', '/confirm-password', '/verify-email'];
for (const role of ['admin', 'owner']) {
  test.describe(`${role} complete GET coverage`, () => {
    test.beforeEach(async ({ page }) => login(page, role));
    for (const url of [...shared, ...(role === 'owner' ? ['/owner/dashboard', '/owner/laporan', '/owner/monitoring', '/owner/admin', '/owner/admin/create', '/owner/admin/2/edit'] : [])]) {
      test(`renders ${url}`, async ({ page }) => healthy(page, url));
    }
    test('navigation drawer, focus trap, Escape, close and desktop resize', async ({ page }) => {
      await page.goto('/customers');
      const drawer = page.locator('#panelNavigation');
      const open = page.getByRole('button', { name: 'Buka menu navigasi', exact: true });
      if (page.viewportSize().width >= 992) {
        await expect(drawer).toBeVisible();
        await expect(open).toBeHidden();
        await page.setViewportSize({ width: 390, height: 900 });
      }
      await expect(drawer).toBeHidden();
      await open.click();
      await expect(drawer).toBeVisible();
      await expect.poll(() => drawer.evaluate(el => el.contains(document.activeElement))).toBe(true);
      for (let i = 0; i < 18; i++) {
        await page.keyboard.press('Tab');
        expect(await drawer.evaluate(el => el.contains(document.activeElement)), 'Focus stays in drawer').toBe(true);
      }
      await page.keyboard.press('Escape');
      await expect(drawer).toBeHidden();
      await expect(open).toBeFocused();
      await open.click();
      await page.getByRole('button', { name: 'Tutup menu navigasi', exact: true }).click();
      await expect(drawer).toBeHidden();
      await open.click();
      await page.setViewportSize({ width: 1024, height: 900 });
      await expect(drawer).toBeVisible();
      await expect(page.locator('.offcanvas-backdrop')).toHaveCount(0);
      await page.setViewportSize({ width: 320, height: 900 });
      await expect(drawer).toBeHidden();
      await expect(page.locator('body')).not.toHaveCSS('overflow', 'hidden');
    });
    test('account dropdown opens by keyboard and closes with Escape', async ({ page }) => {
      const trigger = page.getByRole('button', { name: 'Menu akun', exact: true });
      await trigger.focus();
      await page.keyboard.press('Enter');
      await expect(trigger).toHaveAttribute('aria-expanded', 'true');
      await expect(page.locator('.dropdown-menu.show')).toBeVisible();
      await page.keyboard.press('Escape');
      await expect(trigger).toHaveAttribute('aria-expanded', 'false');
      await expect(trigger).toBeFocused();
    });
  });
}
for (const url of ['/login', '/forgot-password', '/reset-password/mobile-test-token?email=admin%40mobile.test']) {
  test(`guest ${url}`, async ({ page }) => healthy(page, url));
}
test('password visibility toggles are linked and keyboard operable', async ({ page }) => {
  await page.goto('/login');
  const password = page.locator('[name=password]');
  await password.fill('MobileTest!234');
  const id = await password.getAttribute('id');
  const toggle = page.locator(`button[aria-controls="${id}"]`);
  await expect(password).toHaveAttribute('type', 'password');
  await expect(toggle).toHaveAccessibleName(/password|sandi/i);
  await toggle.focus();
  await page.keyboard.press('Space');
  await expect(password).toHaveAttribute('type', 'text');
  await expect(password).toHaveValue('MobileTest!234');
  await page.keyboard.press('Enter');
  await expect(password).toHaveAttribute('type', 'password');
});
