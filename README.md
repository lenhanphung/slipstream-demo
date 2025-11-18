# Slipstream Demo - Customer Management System

A modern web application for managing customers and their contacts, built with Laravel 11, Vue.js 3, and MySQL.

## 📋 Overview

This application provides a complete customer relationship management (CRM) system with the following capabilities:

- **Customer Management**: Full CRUD operations for customers with categories
- **Contact Management**: Nested contact management within customers
- **Search & Filter**: Advanced search and category filtering
- **Modern UI**: Responsive design with modal-based forms and real-time updates

## ✨ Features

- ✅ Customer CRUD operations (Create, Read, Update, Delete)
- ✅ Contact management nested within customers
- ✅ Search customers by name/reference
- ✅ Filter customers by category
- ✅ Modal-based forms for intuitive UX
- ✅ Real-time validation and error handling
- ✅ Toast notifications for user feedback
- ✅ Responsive design (mobile-friendly)
- ✅ Docker containerization for easy deployment

## 🛠 Tech Stack

### Backend
- **Framework**: Laravel 11
- **PHP**: 8.2+
- **Database**: MySQL 8.0

### Frontend
- **Framework**: Vue.js 3 (Composition API)
- **Build Tool**: Vite
- **Styling**: Tailwind CSS
- **HTTP Client**: Axios

### Infrastructure
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx
- **PHP Runtime**: PHP-FPM

## 📦 Prerequisites

### Option 1: Docker (Recommended)
- Docker Desktop (v20.10+) or Docker Engine + Docker Compose (v2.0+)

### Option 2: Manual Installation
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ and npm
- MySQL 8.0+
- Git

## 🚀 Installation

### Option 1: Using Docker (Recommended - Easiest)

This is the simplest way to get started. Everything is pre-configured and ready to run.

#### Step 1: Clone the Repository
```bash
git clone git@github.com:lenhanphung/slipstream-demo.git
cd slipstream-demo
```

#### Step 2: Start Docker Containers
```bash
docker-compose up -d
```

This command will:
- Build the PHP application container
- Start MySQL database container
- Start Nginx web server
- Start Node.js service for asset compilation
- Automatically run migrations and seeders
- Set up all necessary configurations

#### Step 3: Access the Application
Open your browser and navigate to:
```
http://localhost
```

The application is now ready to use! 🎉

#### Additional Docker Commands

**View logs:**
```bash
docker-compose logs -f
```

**Stop containers:**
```bash
docker-compose down
```

**Stop and remove volumes (clean reset):**
```bash
docker-compose down -v
```

**Rebuild containers (after code changes):**
```bash
docker-compose up -d --build
```

**Execute commands in app container:**
```bash
docker-compose exec app php artisan [command]
docker-compose exec app composer [command]
```

**Execute commands in node container:**
```bash
docker-compose exec node npm [command]
```

### Option 2: Manual Installation

For developers who prefer to run the application locally without Docker.

#### Step 1: Clone the Repository
```bash
git clone git@github.com:lenhanphung/slipstream-demo.git
cd slipstream-demo
```

#### Step 2: Install PHP Dependencies
```bash
composer install
```

#### Step 3: Install Node.js Dependencies
```bash
npm install
```

#### Step 4: Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file and configure database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=slipstream_demo
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### Step 5: Database Setup
Create MySQL database:
```sql
CREATE DATABASE slipstream_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run migrations and seeders:
```bash
php artisan migrate --seed
```

#### Step 6: Build Frontend Assets

**For development (with hot reload):**
```bash
npm run dev
```

**For production:**
```bash
npm run build
```

#### Step 7: Start Development Server
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## 📖 Usage

### Accessing the Application

- **URL**: `http://localhost` (Docker) or `http://localhost:8000` (Manual)
- The customer management page is set as the home page

### Features Guide

1. **View Customers**: The main page displays all customers in a table
2. **Create Customer**: Click the "Create" button in the header
3. **Edit Customer**: Click "Edit" button in the customer row
4. **Delete Customer**: Click "Delete" button (with confirmation)
5. **Search**: Type in the search bar to filter customers
6. **Filter by Category**: Select a category from the dropdown and click "Apply"
7. **Manage Contacts**: 
   - Open customer edit modal to see contacts
   - Click "Create" in contacts section to add a contact
   - Edit/Delete contacts directly from the customer modal
   - Create contacts before saving customer (they'll be saved when customer is saved)

## 🔌 API Endpoints

The application provides RESTful API endpoints:

### Customer Categories
- `GET /api/customer-categories` - Get all customer categories

### Customers
- `GET /api/customers` - List all customers (with search/filter query params)
- `POST /api/customers` - Create a new customer
- `GET /api/customers/{id}` - Get a specific customer
- `PUT /api/customers/{id}` - Update a customer
- `DELETE /api/customers/{id}` - Delete a customer

### Contacts
- `GET /api/customers/{customer}/contacts` - Get all contacts for a customer
- `GET /api/contacts` - List all contacts
- `POST /api/contacts` - Create a new contact
- `GET /api/contacts/{id}` - Get a specific contact
- `PUT /api/contacts/{id}` - Update a contact
- `DELETE /api/contacts/{id}` - Delete a contact

### API Request/Response Format

**Request Headers:**
```
Accept: application/json
Content-Type: application/json
```

**Response Format:**
```json
{
  "data": {
    "id": 1,
    "name": "Customer Name",
    "reference": "REF001",
    ...
  }
}
```

**Error Response:**
```json
{
  "message": "Validation error message",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

## 🧪 Testing

### Run Tests
```bash
# Docker
docker-compose exec app php artisan test

# Manual
php artisan test
```

### Run Tests with Coverage
```bash
php artisan test --coverage
```

## 📁 Project Structure

```
slipstream-demo/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── CustomerController.php
│   │   │       ├── ContactController.php
│   │   │       └── CustomerCategoryController.php
│   │   ├── Requests/
│   │   │   ├── StoreCustomerRequest.php
│   │   │   ├── UpdateCustomerRequest.php
│   │   │   ├── StoreContactRequest.php
│   │   │   └── UpdateContactRequest.php
│   │   └── Resources/
│   │       ├── CustomerResource.php
│   │       ├── ContactResource.php
│   │       └── CustomerCategoryResource.php
│   └── Models/
│       ├── Customer.php
│       ├── Contact.php
│       └── CustomerCategory.php
├── database/
│   ├── migrations/
│   └── seeders/
├── docker/
│   ├── docker-entrypoint.sh
│   ├── nginx/
│   └── php/
├── resources/
│   ├── js/
│   │   ├── views/
│   │   │   └── CustomerIndex.vue
│   │   ├── components/
│   │   │   ├── CustomerTable.vue
│   │   │   ├── CustomerModal.vue
│   │   │   ├── ContactTable.vue
│   │   │   ├── ContactModal.vue
│   │   │   ├── SearchBar.vue
│   │   │   ├── Sidebar.vue
│   │   │   ├── ConfirmDialog.vue
│   │   │   ├── Toast.vue
│   │   │   └── BaseButton.vue
│   │   ├── composables/
│   │   │   ├── useCustomers.js
│   │   │   ├── useContacts.js
│   │   │   └── useModal.js
│   │   ├── services/
│   │   │   └── api.js
│   │   └── app.js
│   ├── css/
│   │   └── app.css
│   └── views/
│       └── customers/
│           └── index.blade.php
├── routes/
│   ├── api.php
│   └── web.php
├── docker-compose.yml
├── Dockerfile
└── README.md
```

## 🛠 Development

### Code Style

**PHP:**
- Follow PSR-12 coding standard
- Use Laravel conventions

**JavaScript/Vue:**
- Use Composition API
- Follow Vue.js style guide
- Use ES6+ syntax

### Git Workflow

1. Create feature branch from `main`
2. Make changes and commit with descriptive messages
3. Push and create pull request
4. Code review and merge

### Database Migrations

```bash
# Create new migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset database (WARNING: deletes all data)
php artisan migrate:fresh --seed
```

### Frontend Development

**Development mode (hot reload):**
```bash
# Docker
docker-compose exec node npm run dev

# Manual
npm run dev
```

**Production build:**
```bash
npm run build
```

## 🐛 Troubleshooting

### Docker Issues

**Port already in use:**
- Stop other services using ports 80, 3306, or 5173
- Or change ports in `docker-compose.yml`

**Permission errors:**
```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

**Container won't start:**
```bash
docker-compose down
docker-compose up -d --build
```

**Clear Docker volumes (fresh start):**
```bash
docker-compose down -v
docker-compose up -d
```

### Database Issues

**Connection refused:**
- Check MySQL container is running: `docker-compose ps`
- Verify database credentials in `.env`
- Wait for MySQL health check to complete

**Migration errors:**
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

### Frontend Issues

**Assets not loading:**
- Check Vite dev server is running (port 5173)
- Clear browser cache
- Restart Node container: `docker-compose restart node`

**Hot reload not working:**
- Check `VITE_DEV_SERVER_URL` in `.env`
- Verify port 5173 is accessible

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👥 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📧 Contact

For questions or support, please open an issue on GitHub.

---

**Happy Coding! 🚀**
