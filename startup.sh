#!/bin/bash
echo "Configuring Nginx and Laravel for Hirely on Azure Web App..."

# 1. Configure Nginx to point to /public
if [ -f /home/site/wwwroot/default ]; then
    cp /home/site/wwwroot/default /etc/nginx/sites-available/default
    service nginx reload || true
fi

# 2. Clear compiled views, route cache, config cache, and event cache
cd /home/site/wwwroot
php artisan optimize:clear || true

# 2.5 Clean up any orphaned legacy migrations consolidated into baseline
rm -f /home/site/wwwroot/database/migrations/2026_10_*.php
rm -f /home/site/wwwroot/database/migrations/2026_11_*.php

# 3. Run database migrations safely
php artisan migrate --force || true

# 4. Ensure storage directories and permissions
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chmod -R 775 storage bootstrap/cache || true

echo "Hirely startup complete."
