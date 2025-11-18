#!/bin/sh
set -e

# Lock file to prevent script from running multiple times (outside volume mount)
LOCK_FILE=/tmp/.entrypoint-lock
SETUP_FILE=/var/www/html/.setup-complete

# If setup is already complete, just start PHP-FPM
if [ -f "$SETUP_FILE" ]; then
    echo "Setup already completed, starting PHP-FPM directly..."
    exec php-fpm -F
fi

# Prevent concurrent execution
if [ -f "$LOCK_FILE" ]; then
    echo "Another setup is in progress, waiting..."
    while [ -f "$LOCK_FILE" ]; do
        sleep 1
    done
    exec php-fpm -F
fi

touch "$LOCK_FILE"
trap "rm -f $LOCK_FILE" EXIT

# Function to wait for MySQL
wait_for_mysql() {
    echo "Waiting for MySQL to be ready..."
    for i in 1 2 3 4 5 6 7 8 9 10; do
        if mysql -h mysql -u root -psecret -e "SELECT 1" > /dev/null 2>&1; then
            echo "MySQL is ready!"
            return 0
        fi
        echo "MySQL is unavailable - sleeping ($i/10)"
        sleep 2
    done
    echo "MySQL connection timeout, but continuing..."
    return 1
}

# Check if Laravel dependencies are installed (vendor directory exists)
if [ ! -d "vendor" ]; then
    echo "Vendor directory not found. Installing dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader || {
        echo "Failed to install dependencies"
        exec php-fpm -F
    }
fi

# Generate application key if not exists
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
fi

# Update database configuration for MySQL
echo "Updating database configuration..."
sed -i "s/DB_CONNECTION=.*/DB_CONNECTION=mysql/" .env
sed -i "s/DB_HOST=.*/DB_HOST=${DB_HOST:-mysql}/" .env
sed -i "s/# DB_HOST=/DB_HOST=/" .env
sed -i "s/DB_PORT=.*/DB_PORT=${DB_PORT:-3306}/" .env
sed -i "s/# DB_PORT=/DB_PORT=/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE:-slipstream_demo}/" .env
sed -i "s/# DB_DATABASE=/DB_DATABASE=/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME:-root}/" .env
sed -i "s/# DB_USERNAME=/DB_USERNAME=/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD:-secret}/" .env
sed -i "s/# DB_PASSWORD=/DB_PASSWORD=/" .env

# Update session driver to database
sed -i "s/SESSION_DRIVER=.*/SESSION_DRIVER=database/" .env

# Set Vite dev server URL for Docker environment
if ! grep -q "VITE_DEV_SERVER_URL=" .env 2>/dev/null; then
    echo "VITE_DEV_SERVER_URL=http://localhost:5173" >> .env
else
    sed -i "s|VITE_DEV_SERVER_URL=.*|VITE_DEV_SERVER_URL=http://localhost:5173|" .env
fi

# Check if APP_KEY is set
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "Generating application key..."
    php artisan key:generate || echo "Failed to generate key"
fi

# Try to setup database if MySQL is available
if wait_for_mysql; then
    # Run migrations only if database is accessible
    echo "Running migrations..."
    php artisan migrate --force || echo "Migration failed or already run"

    # Run seeders
    echo "Running seeders..."
    php artisan db:seed --force || echo "Seeder failed or already run"
else
    echo "Skipping migrations - MySQL not available"
fi

# Fix permissions for storage and bootstrap/cache directories
echo "Fixing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear and cache config
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "Application is ready! Marking setup as complete..."
touch "$SETUP_FILE"

echo "Starting PHP-FPM in foreground mode..."
# Remove lock before starting PHP-FPM
rm -f "$LOCK_FILE"

# Start PHP-FPM in foreground mode (this replaces the shell process)
exec php-fpm -F

