#!/bin/sh
# Container entrypoint: prepare Laravel, then run Apache in the foreground.
# artisan boots AppServiceProvider, which reads settings from MySQL — so the DB_* env vars
# must point at a reachable database before the app can start.
set -e
cd /var/www/html

if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY is not set. Generate one with: php artisan key:generate --show" >&2
    exit 1
fi

# storage/ is not in git, and a Render disk mounted over it starts empty
mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions \
         storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Register package service providers (skipped at build time because it needs the DB)
php artisan package:discover --ansi

# Optional one-off tasks, switched on with env vars
if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan migrate --force
fi
if [ "$RUN_SEEDERS" = "true" ]; then
    php artisan db:seed --class=TygSeoServicesSeeder --force
    php artisan db:seed --class=TygServicePackagesSeeder --force
fi

# Cache config + views for speed (config reads the env vars Render provides)
php artisan config:cache
php artisan view:cache

chown -R www-data:www-data storage bootstrap/cache

exec apache2-foreground
