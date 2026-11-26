#!/usr/bin/env bash
set -euo pipefail

PORT="${PORT:-10000}"

# Bind Apache to Render's dynamic PORT
sed -ri "s/Listen [0-9]+/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

mkdir -p storage/app/private/materi storage/app/private/tugas storage/app/private/submission \
    storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

php artisan config:clear || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

# Seed demo data only if explicitly enabled AND tb_user is still empty
if [ "${RUN_SEED_ONCE:-false}" = "true" ]; then
    USER_COUNT=$(php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); echo Illuminate\Support\Facades\DB::table('tb_user')->count();")
    if [ "${USER_COUNT}" = "0" ]; then
        php artisan db:seed --force
    fi
fi

exec apache2-foreground
