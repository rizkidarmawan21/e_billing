import { test, expect } from '@playwright/test';
import { spawnSync } from 'node:child_process';
import { root, testEnv } from './environment.mjs';

for (const overrides of [
  { APP_ENV: 'production' },
  { APP_ENV: 'local' },
  { DB_DATABASE: `${root}/database/database.sqlite` },
  { DB_CONNECTION: 'mysql' },
]) {
  test(`fixtures refuse unsafe settings ${JSON.stringify(overrides)}`, () => {
    const result = spawnSync('php', ['tests/e2e/fixtures.php'], {
      cwd: root, env: { ...testEnv, ...overrides }, encoding: 'utf8',
    });
    expect(result.status).toBe(1);
    expect(result.stderr).toContain('Refusing fixtures');
    expect(result.stdout).toBe('');
  });
}
