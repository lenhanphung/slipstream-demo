# Slipstream Demo - Customer Management System

## Overview
A Customer and Contact management system built with Laravel 11, Vue.js 3, and MySQL. This application provides CRUD operations for customers and their contacts with a modern, modal-based UI.

## Features
- Customer CRUD operations with modal-based forms
- Contact management (nested within customers)
- Search and filter functionality
- Responsive design with Tailwind CSS
- Docker-based development environment

## Tech Stack
- **Backend**: Laravel 11
- **Frontend**: Vue.js 3 (Composition API)
- **Database**: MySQL 8.0
- **Styling**: Tailwind CSS
- **Containerization**: Docker & Docker Compose

## Prerequisites
- Docker Desktop (Windows/Mac) or Docker Engine + Docker Compose (Linux)
- Git
- Composer (for initial Laravel installation, or use Docker)

## Initial Setup

### Option 1: Install Laravel Locally (Recommended for first time)
If the repository doesn't contain Laravel yet, install it first:

```bash
# Clone repository
git clone git@github.com:lenhanphung/slipstream-demo.git
cd slipstream-demo

# Install Laravel 11
composer create-project laravel/laravel:^11.0 temp-laravel
mv temp-laravel/* temp-laravel/.* . 2>/dev/null || true
rm -rf temp-laravel

# Copy environment file
cp .env.example .env
```

### Option 2: Install Laravel via Docker (Alternative)
If you don't have Composer installed locally:

```bash
# Clone repository
git clone git@github.com:lenhanphung/slipstream-demo.git
cd slipstream-demo

# Use Docker to install Laravel
docker run --rm -v ${PWD}:/app composer create-project laravel/laravel:^11.0 temp-laravel
mv temp-laravel/* temp-laravel/.* . 2>/dev/null || true
rm -rf temp-laravel

# Copy environment file
cp .env.example .env
```

## Quick Start with Docker

### 1. Start the Application
```bash
docker-compose up -d --build
```

This command will:
- Build the PHP application container
- Start MySQL database
- Start Nginx web server
- Start Node.js for asset compilation
- Automatically run migrations and seeders
- Install all dependencies

### 3. Access the Application
- **Web Application**: http://localhost
- **MySQL**: localhost:3306
  - Database: `slipstream_demo`
  - Username: `root`
  - Password: `secret`

### 4. View Logs (Optional)
```bash
# View all logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql
```

## Development Commands

### Run Artisan Commands
```bash
# Run migrations
docker-compose exec app php artisan migrate

# Run seeders
docker-compose exec app php artisan db:seed

# Clear cache
docker-compose exec app php artisan cache:clear

# Generate application key
docker-compose exec app php artisan key:generate
```

### Run NPM Commands
```bash
# Install dependencies
docker-compose exec node npm install

# Build assets for production
docker-compose exec node npm run build

# Watch for changes (development)
docker-compose exec node npm run dev
```

### Access Container Shell
```bash
# PHP container
docker-compose exec app bash

# Node container
docker-compose exec node sh

# MySQL container
docker-compose exec mysql bash
```

## Project Structure
```
slipstream-demo/
├── app/                    # Laravel application
├── database/               # Migrations and seeders
├── resources/              # Views, JS, CSS
├── routes/                 # API and web routes
├── docker/                 # Docker configuration files
│   ├── nginx/             # Nginx configuration
│   ├── php/               # PHP configuration
│   └── docker-entrypoint.sh # Setup script
├── docker-compose.yml      # Docker services definition
├── Dockerfile             # PHP application image
└── .env.example           # Environment variables template
```

## Docker Services

### Services Overview
- **app**: PHP 8.2-FPM with Laravel application
- **mysql**: MySQL 8.0 database
- **nginx**: Nginx web server
- **node**: Node.js 18 for asset compilation

### Ports
- **80**: Web application (Nginx)
- **3306**: MySQL database

## Troubleshooting

### Port Already in Use
If port 80 or 3306 is already in use, modify the ports in `docker-compose.yml`:
```yaml
ports:
  - "8080:80"  # Change 80 to 8080
```

### Permission Issues
If you encounter permission issues:
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Rebuild Containers
If you need to rebuild containers:
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Reset Database
To reset the database:
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

## API Endpoints

### Customers
- `GET /api/customers` - List customers (with search & filter)
- `POST /api/customers` - Create customer
- `GET /api/customers/{id}` - Show customer
- `PUT /api/customers/{id}` - Update customer
- `DELETE /api/customers/{id}` - Delete customer

### Contacts
- `GET /api/customers/{id}/contacts` - List contacts for customer
- `POST /api/contacts` - Create contact
- `GET /api/contacts/{id}` - Show contact
- `PUT /api/contacts/{id}` - Update contact
- `DELETE /api/contacts/{id}` - Delete contact

### Categories
- `GET /api/customer-categories` - List all categories

## Testing
```bash
# Run tests
docker-compose exec app php artisan test

# Run with coverage
docker-compose exec app php artisan test --coverage
```

## Stop the Application
```bash
# Stop containers (keep data)
docker-compose stop

# Stop and remove containers (keep volumes)
docker-compose down

# Stop and remove everything including volumes
docker-compose down -v
```

## Production Deployment

For production deployment:
1. Update `.env` with production values
2. Set `APP_DEBUG=false`
3. Run `npm run build` to compile assets
4. Optimize Laravel: `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`

## License
MIT License

## Support
For issues or questions, please open an issue on GitHub.

