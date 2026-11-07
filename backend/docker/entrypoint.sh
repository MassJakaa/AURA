#!/bin/sh
set -e

echo "=================================================="
echo "    🚀 STARTING AURA WEB COMPANION ENGINE         "
echo "=================================================="

# Support dynamic PORT from cloud hosts (Railway, Render, Koyeb, Cloud Run)
PORT="${PORT:-80}"
echo "==> Configuring Nginx port to: ${PORT}"
sed -i "s/listen 80;/listen ${PORT};/g" /etc/nginx/http.d/default.conf 2>/dev/null || sed -i "s/listen 80;/listen ${PORT};/g" /etc/nginx/conf.d/default.conf 2>/dev/null || true
sed -i "s/listen \[::\]:80;/listen \[::\]:${PORT};/g" /etc/nginx/http.d/default.conf 2>/dev/null || sed -i "s/listen \[::\]:80;/listen \[::\]:${PORT};/g" /etc/nginx/conf.d/default.conf 2>/dev/null || true

# Prepare required directories
mkdir -p /var/www/html/database \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache \
         /run/nginx \
         /run/supervisord

# Setup SQLite database if DB_CONNECTION is sqlite (default)
DB_CONN="${DB_CONNECTION:-sqlite}"
if [ "$DB_CONN" = "sqlite" ]; then
    SQLITE_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    if [ ! -f "$SQLITE_PATH" ]; then
        echo "==> Creating new SQLite database file at $SQLITE_PATH..."
        touch "$SQLITE_PATH"
    fi
    chmod 664 "$SQLITE_PATH" 2>/dev/null || true
fi

# Ensure storage permissions
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Check APP_KEY
if [ -z "$APP_KEY" ]; then
    echo "==> APP_KEY not provided. Generating application encryption key..."
    php artisan key:generate --force
fi

# Run database migrations
echo "==> Running database migrations..."
php artisan migrate --force

# Run database seeders (master character reference + life system)
echo "==> Seeding master character assets and virtual life schedule..."
php artisan db:seed --force

# Optimize Laravel route and config caching for production performance
echo "==> Caching configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=================================================="
echo "    ✨ AURA IS READY & LISTENING ON PORT ${PORT}  "
echo "=================================================="

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
