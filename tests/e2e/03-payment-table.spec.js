import { test, expect, healthy } from './helpers.js';

for (const role of ['owner', 'admin']) {
  test(`${role} can read and use the compact payment tables`, async ({ page }, testInfo) => {
    await page.goto('/login');
    const response = await page.request.post('/login', {
      form: {
        _token: await page.locator('[name=_token]').inputValue(),
        email: `${role}@mobile.test`,
        password: 'MobileTest!234',
      },
      maxRedirects: 0,
    });
    expect(response.status()).toBe(302);
    expect(response.headers().location).toMatch(role === 'owner' ? /\/owner\/dashboard$/ : /\/dashboard$/);

    for (const path of ['/customers', '/payments', '/payments/unpaid', '/payments/paid']) {
      await page.goto(path);
      const search = page.getByRole('textbox', { name: 'Cari Nama Customer' });
      const form = search.locator('xpath=ancestor::form');
      const submit = form.getByRole('button', { name: 'Cari', exact: true });
      await expect(form.locator('.input-group-text .bi-search')).toBeVisible();
      if (page.viewportSize().width < 768 && path !== '/payments/unpaid') {
        expect((await submit.boundingBox()).width).toBeCloseTo((await form.locator('.input-group').boundingBox()).width, 0);
      }
      await search.fill('Mobile Customer');
      if (path === '/payments/unpaid') {
        await form.getByRole('button', { name: 'Filter Bulan' }).click();
        await form.getByLabel('Aktifkan Filter Bulan').check();
        await form.getByLabel('Januari', { exact: true }).check();
        await form.locator('.filter-menu').getByRole('button', { name: 'Terapkan' }).click();
        await expect(page).toHaveURL(url => url.searchParams.get('filter_active') === '1' && url.searchParams.get('months[]') === 'Januari');
      }
      await submit.click();
      await expect(page).toHaveURL(url => url.pathname === path && url.searchParams.get('search') === 'Mobile Customer' && (path !== '/payments/unpaid' || url.searchParams.get('months[]') === 'Januari'));
      const reset = form.locator('.customer-search-actions').getByRole('link', { name: 'Reset' });
      await expect(reset).toBeVisible();
      const [submitBox, resetBox] = await Promise.all([submit.boundingBox(), reset.boundingBox()]);
      expect(resetBox.y).toBeCloseTo(submitBox.y, 0);
      expect(resetBox.x).toBeGreaterThan(submitBox.x);
      if (page.viewportSize().width < 768) {
        expect(resetBox.width).toBeCloseTo(submitBox.width, 0);
      }
      await reset.click();
      await expect(search).toHaveValue('');
    }

    for (const [url, label] of [['/payments', 'Daftar payment'], ['/payments/unpaid', 'Tagihan belum dibayar']]) {
      await healthy(page, url);
      const region = page.getByRole('region', { name: label, exact: true });
      const table = region.getByRole('table');
      await expect(table.getByRole('columnheader').first()).toHaveText('No.');
      await expect(table.getByRole('columnheader', { name: 'Aksi' })).toBeVisible();
      const row = table.locator('tbody tr').filter({ has: page.getByRole('link', { name: 'Detail', exact: true }) }).first();
      await expect(row).toHaveCSS('display', 'table-row');
      const statusBadge = row.locator('td[data-label="Status"] .payment-status');
      await expect(statusBadge).toHaveText('Belum Bayar');
      await expect(statusBadge).toHaveCSS('background-color', 'rgb(220, 53, 69)');
      await expect(statusBadge).toHaveCSS('color', 'rgb(255, 255, 255)');
      await expect(statusBadge).toHaveCSS('white-space', 'nowrap');
      expect(await statusBadge.evaluate(element => {
        const text = document.createRange();
        text.selectNodeContents(element);
        return text.getClientRects().length;
      })).toBe(1);

      if (page.viewportSize().width < 768) {
        await expect(region.getByText('Geser tabel ke samping')).toBeVisible();
        await region.focus();
        await page.keyboard.press('ArrowRight');
        await expect.poll(() => region.evaluate(el => el.scrollLeft)).toBeGreaterThan(0);
      }

      const actions = row.locator('.record-actions');
      await expect(actions).toHaveCSS('justify-content', 'center');
      await expect(actions.getByRole('link', { name: 'Detail' })).toHaveClass(/btn-outline-primary/);
      await expect(actions.getByRole('link', { name: 'Edit' })).toHaveClass(/btn-outline-secondary/);
      await expect(actions.getByRole('button', { name: 'Hapus' })).toHaveClass(/btn-outline-danger/);
      const boxes = await actions.locator('a.btn, button.btn').evaluateAll(nodes => nodes.map(node => {
        const { y, width, height } = node.getBoundingClientRect();
        return { y, width, height, text: node.textContent.trim(), icon: getComputedStyle(node.querySelector('.bi'), '::before').content };
      }));
      expect(boxes).toHaveLength(3);
      for (const box of boxes) {
        expect(box.width).toBe(44);
        expect(box.height).toBe(44);
        expect(box.text).toBe('');
        expect(box.icon).not.toBe('none');
        expect(Math.abs(box.y - boxes[0].y)).toBeLessThanOrEqual(1);
      }

      if (page.viewportSize().width === 390 && role === 'owner' && url === '/payments') {
        await region.evaluate(el => { el.scrollLeft = 0; });
        await page.screenshot({ path: testInfo.outputPath('payment-table-mobile.png') });
      }
    }

    const mobile = page.viewportSize().width < 992;
    const navigation = page.getByRole('group', { name: 'Payments' });
    for (const [name, path] of [['Lunas', '/payments/paid'], ['All', '/payments'], ['Belum Bayar', '/payments/unpaid'], ['Lunas', '/payments/paid']]) {
      if (mobile) await page.getByRole('button', { name: 'Buka menu navigasi' }).click();
      await navigation.getByRole('link', { name, exact: true }).click();
      await expect(page).toHaveURL(new RegExp(`${path}$`));
      if (mobile) await page.getByRole('button', { name: 'Buka menu navigasi' }).click();
      await expect(navigation.getByRole('link', { name, exact: true })).toHaveAttribute('aria-current', 'page');
      if (mobile) await page.getByRole('button', { name: 'Tutup menu navigasi' }).click();
    }
    const paid = page.getByRole('region', { name: 'Daftar tagihan lunas' });
    await expect(paid.locator('td[data-label="Status"]').first()).toHaveText('Lunas');
    await expect(paid.locator('.payment-status').first()).toHaveClass(/text-bg-success/);
    expect((await paid.locator('td[data-label="Status"]').allTextContents()).every(text => text.trim() === 'Lunas')).toBe(true);
    await paid.getByRole('link', { name: 'Detail' }).first().click();
    await page.getByRole('link', { name: /Kembali/ }).first().click();
    await expect(page).toHaveURL(/\/payments\/paid\?page=1$/);

    if (page.viewportSize().width < 768) {
      await healthy(page, '/packages');
      const descriptions = page.locator('td[data-label="Deskripsi"]');
      await expect(descriptions.first()).toHaveCSS('min-width', '224px');
      if (role === 'owner') {
        await healthy(page, '/owner/admin');
        const roleCell = page.locator('td[data-label="Role"]').first();
        await expect(roleCell).toHaveText('Admin');
        await expect(roleCell.locator('.badge')).toHaveCount(0);
      }
      await healthy(page, '/customers');
      const table = page.getByRole('region', { name: 'Daftar pelanggan' }).getByRole('table');
      await expect(table.getByRole('columnheader').first()).toHaveText('No.');
      const packageCell = table.locator('td[data-label="Paket"]').filter({ has: page.locator('.package-name') }).first();
      await expect(packageCell).toHaveCSS('min-width', '144px');
      await expect(packageCell.locator('.package-name')).not.toHaveClass(/\bbadge\b/);
    }
  });
}
