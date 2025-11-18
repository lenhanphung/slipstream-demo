#!/bin/sh
set -e

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
for i in 1 2 3 4 5 6 7 8 9 10; do
    if mysql -h mysql -u root -psecret -e "SELECT 1" > /dev/null 2>&1; then
        echo "MySQL is ready!"
        break
    fi
    echo "MySQL is unavailable - sleeping ($i/10)"
    sleep 2
done

# Check if Laravel is installed (vendor directory exists)
if [ ! -d "vendor" ]; then
    echo "Laravel not installed. Please install Laravel first."
    echo "Run: composer create-project laravel/laravel:^11.0 ."
    exec "$@"
    exit 0
fi

# Generate application key if not exists
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
fi

# Check if APP_KEY is set
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Run migrations only if database is accessible
echo "Running migrations..."
php artisan migrate --force || echo "Migration failed or already run"

# Run seeders
echo "Running seeders..."
php artisan db:seed --force || echo "Seeder failed or already run"

# Clear and cache config
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "Application is ready!"

exec "$@"

