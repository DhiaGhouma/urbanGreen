#!/bin/bash
set -e

echo "🚀 Starting UrbanGreen Application..."

# Wait for potential database connection (if using external DB)
echo "⏳ Checking environment..."

# Check if .env exists, if not copy from .env.example
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Create SQLite database if it doesn't exist
if [ ! -f database/database.sqlite ]; then
    echo "📦 Creating SQLite database..."
    touch database/database.sqlite
fi

# Run migrations
echo "🔄 Running database migrations..."
php artisan migrate --force

# Clear and cache config
echo "⚙️ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "✅ Application ready!"

# Execute the main command
exec "$@"
