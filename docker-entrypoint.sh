#!/bin/sh
set -e

cd /var/www/html

# --- 1. DB host fallback ---
# 127.0.0.1 / localhost di dalam container = container itu sendiri, bukan service db.
# Kalau kosong atau loopback, pakai nama service compose "db".
case "$DB_HOST" in
  ""|127.0.0.1|localhost)
    export DB_HOST=db
    ;;
esac
export DB_PORT="${DB_PORT:-3306}"

# --- 2. APP_KEY ---
if [ -z "$APP_KEY" ]; then
  echo "[entrypoint] APP_KEY kosong, generate..."
  php artisan key:generate --force || echo "[entrypoint] WARNING: gagal generate APP_KEY"
fi

# --- 3. Tunggu database siap (maks ~60s) ---
echo "[entrypoint] menunggu database ${DB_HOST}:${DB_PORT} ..."
i=0
until php -r 'try { new PDO("mysql:host=".getenv("DB_HOST").";port=".getenv("DB_PORT").";dbname=".getenv("DB_DATABASE"), getenv("DB_USERNAME"), getenv("DB_PASSWORD")); exit(0); } catch (Exception $e) { exit(1); }' >/dev/null 2>&1; do
  i=$((i + 1))
  if [ "$i" -ge 30 ]; then
    echo "[entrypoint] WARNING: database belum siap setelah 60s, lanjut start"
    break
  fi
  sleep 2
done

# --- 4. Migrate + optimasi ---
php artisan migrate --force || echo "[entrypoint] WARNING: migrate gagal, cek kredensial DB"
php artisan storage:link 2>/dev/null || true
php artisan config:cache || echo "[entrypoint] WARNING: config:cache gagal"
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

echo "[entrypoint] starting: $*"
exec "$@"
