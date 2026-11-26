import path from 'node:path';
import { fileURLToPath } from 'node:url';

export const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
export const artifacts = path.join(root, 'tests/e2e/artifacts');
export const testEnv = {
  ...process.env,
  APP_ENV: 'testing', APP_DEBUG: 'true', APP_URL: 'http://127.0.0.1:8010',
  APP_KEY: `base64:${Buffer.alloc(32, 17).toString('base64')}`,
  APP_CONFIG_CACHE: path.join(artifacts, 'config.php'),
  APP_ROUTES_CACHE: path.join(artifacts, 'routes.php'),
  APP_EVENTS_CACHE: path.join(artifacts, 'events.php'),
  VIEW_COMPILED_PATH: path.join(artifacts, 'views'),
  DB_CONNECTION: 'sqlite', DB_DATABASE: path.join(artifacts, 'mobile.sqlite'),
  SYNC_ENABLED: 'false', QUEUE_CONNECTION: 'sync', MAIL_MAILER: 'array',
  CACHE_DRIVER: 'array', CACHE_STORE: 'array', SESSION_DRIVER: 'cookie',
  SESSION_COOKIE: 'mobile_e2e_session', SESSION_SECURE_COOKIE: 'false',
  LOG_CHANNEL: 'stderr', BCRYPT_ROUNDS: '4',
};
