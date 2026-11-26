import fs from 'node:fs';
import path from 'node:path';
import { spawn, execFileSync } from 'node:child_process';
import { artifacts, root, testEnv } from './environment.mjs';

if (fs.existsSync(artifacts) && fs.lstatSync(artifacts).isSymbolicLink()) {
  throw new Error('Refusing symlink artifacts directory');
}
fs.mkdirSync(path.join(artifacts, 'views'), { recursive: true });
if (fs.existsSync(testEnv.DB_DATABASE) && fs.lstatSync(testEnv.DB_DATABASE).isSymbolicLink()) {
  throw new Error('Refusing symlink database');
}
fs.closeSync(fs.openSync(testEnv.DB_DATABASE, 'a'));
execFileSync('php', ['tests/e2e/fixtures.php'], { cwd: root, env: testEnv, stdio: 'inherit' });
const server = spawn('php', ['artisan', 'serve', '--host=127.0.0.1', '--port=8010', '--tries=1', '--no-reload'], {
  cwd: root, env: testEnv, stdio: 'inherit',
});
for (const signal of ['SIGINT', 'SIGTERM']) process.on(signal, () => server.kill(signal));
server.on('exit', code => process.exit(code ?? 1));
