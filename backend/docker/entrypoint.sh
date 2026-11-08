#!/bin/sh

echo "=================================================="
echo "    🚀 STARTING AURA WEB COMPANION ENGINE         "
echo "=================================================="

# 1. Ensure .env file exists so Laravel key & config work reliably
if [ ! -f /var/www/html/.env ]; then
    echo "==> Creating .env from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# 2. Setup dynamic port if provided (Railway, Render, Cloud Run, etc.)
PORT="${PORT:-8080}"
echo "==> Detected target port: ${PORT}"
if [ -n "$PORT" ] && [ "$PORT" != "80" ] && [ "$PORT" != "8080" ] && [ "$PORT" != "3000" ]; then
    echo "==> Injecting extra listen port ${PORT} into Nginx..."
    sed -i "s/server_name _;/listen ${PORT};\n    listen [::]:${PORT};\n    server_name _;/g" /etc/nginx/http.d/default.conf 2>/dev/null || true
fi

# 3. Prepare required directories
mkdir -p /var/www/html/database \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache \
         /run/nginx \
         /run/supervisord \
         /var/log

# 4. Setup SQLite database file
DB_CONN="${DB_CONNECTION:-sqlite}"
if [ "$DB_CONN" = "sqlite" ]; then
    SQLITE_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    if [ ! -f "$SQLITE_PATH" ]; then
        echo "==> Initializing SQLite database file at $SQLITE_PATH..."
        touch "$SQLITE_PATH"
    fi
    chmod 666 "$SQLITE_PATH" 2>/dev/null || true
fi

# 5. Ensure full directory permissions for web and storage
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# 6. Ensure APP_KEY exists
if [ -z "$APP_KEY" ]; then
    if ! grep -q "^APP_KEY=base64:" /var/www/html/.env; then
        echo "==> Generating application key..."
        php artisan key:generate --force || true
    fi
fi

# 7. Run database migrations safely
echo "==> Running database migrations..."
php artisan migrate --force || echo "[WARN] Migration notice, proceeding..."

# 8. Run database seeders safely
echo "==> Seeding master character assets & life system..."
php artisan db:seed --force || echo "[WARN] Seeding notice, proceeding..."

# 9. Optimize configuration & route caching
echo "==> Optimizing caches..."
php artisan config:cache || php artisan config:clear
php artisan route:cache || php artisan route:clear
php artisan view:cache || php artisan view:clear

echo "=================================================="
echo "    ✨ AURA IS READY & LISTENING ON PORTS 80, 8080 & ${PORT} "
echo "=================================================="

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
