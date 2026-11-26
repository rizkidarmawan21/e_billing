import { defineConfig } from '@playwright/test';
import { testEnv } from './tests/e2e/environment.mjs';

export default defineConfig({
  testDir: './tests/e2e',
  outputDir: './tests/e2e/artifacts/results',
  fullyParallel: false,
  workers: 1,
  retries: 0,
  timeout: 30000,
  expect: { timeout: 5000 },
  reporter: [['list'], ['json', { outputFile: 'tests/e2e/artifacts/results.json' }], ['html', { outputFolder: 'tests/e2e/artifacts/report', open: 'never' }]],
  use: {
    baseURL: 'http://127.0.0.1:8010', browserName: 'chromium',
    trace: 'retain-on-failure', screenshot: 'only-on-failure',
  },
  projects: [320, 390, 768, 1024, 1440].map(width => ({
    name: `chromium-${width}`, use: { viewport: { width, height: 900 } },
  })),
  webServer: {
    command: 'node tests/e2e/server.mjs', url: 'http://127.0.0.1:8010/up',
    env: testEnv, reuseExistingServer: false, timeout: 60000,
  },
});
