#!/bin/bash

# Setup script for Slipstream Demo
# This script installs Laravel 11 if not already installed

set -e

echo "🚀 Slipstream Demo - Setup Script"
echo "=================================="

# Check if Laravel is already installed
if [ -d "vendor" ] && [ -f "artisan" ]; then
    echo "✅ Laravel is already installed!"
    exit 0
fi

echo "📦 Installing Laravel 11..."

# Check if Composer is available
if command -v composer &> /dev/null; then
    echo "Using local Composer..."
    composer create-project laravel/laravel:^11.0 temp-laravel
elif command -v docker &> /dev/null; then
    echo "Using Docker Composer..."
    docker run --rm -v "$(pwd):/app" composer create-project laravel/laravel:^11.0 temp-laravel
else
    echo "❌ Error: Neither Composer nor Docker is available."
    echo "Please install Composer or Docker to continue."
    exit 1
fi

# Move Laravel files to current directory
echo "📂 Moving Laravel files..."
mv temp-laravel/* temp-laravel/.* . 2>/dev/null || true
rm -rf temp-laravel

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
fi

echo "✅ Setup complete!"
echo ""
echo "Next steps:"
echo "1. Run: docker-compose up -d --build"
echo "2. Access: http://localhost"

