# Setup script for Slipstream Demo (PowerShell)
# This script installs Laravel 11 if not already installed

Write-Host "🚀 Slipstream Demo - Setup Script" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan

# Check if Laravel is already installed
if (Test-Path "vendor") -and (Test-Path "artisan") {
    Write-Host "✅ Laravel is already installed!" -ForegroundColor Green
    exit 0
}

Write-Host "📦 Installing Laravel 11..." -ForegroundColor Yellow

# Check if Composer is available
if (Get-Command composer -ErrorAction SilentlyContinue) {
    Write-Host "Using local Composer..." -ForegroundColor Cyan
    composer create-project laravel/laravel:^11.0 temp-laravel
} elseif (Get-Command docker -ErrorAction SilentlyContinue) {
    Write-Host "Using Docker Composer..." -ForegroundColor Cyan
    docker run --rm -v "${PWD}:/app" composer create-project laravel/laravel:^11.0 temp-laravel
} else {
    Write-Host "❌ Error: Neither Composer nor Docker is available." -ForegroundColor Red
    Write-Host "Please install Composer or Docker to continue." -ForegroundColor Red
    exit 1
}

# Move Laravel files to current directory
Write-Host "📂 Moving Laravel files..." -ForegroundColor Yellow
Get-ChildItem -Path temp-laravel -Force | Move-Item -Destination . -Force
Remove-Item -Path temp-laravel -Recurse -Force

# Copy environment file if it doesn't exist
if (-not (Test-Path .env)) {
    Write-Host "📝 Creating .env file..." -ForegroundColor Yellow
    Copy-Item .env.example .env
}

Write-Host "✅ Setup complete!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Run: docker-compose up -d --build"
Write-Host "2. Access: http://localhost"

