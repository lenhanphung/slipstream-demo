# Laravel 11 Customer Management System - Development Instructions

## Overview
This document provides detailed step-by-step instructions for building a Customer and Contact management system using Laravel 11, Vue.js, and MySQL. This guide is designed for AI agents or developers to follow systematically.

**Repository**: `git@github.com:lenhanphung/slipstream-demo.git`

---

## Project Requirements Summary

### Functional Requirements
1. **Customer Management**: CRUD operations with modal-based forms
2. **Contact Management**: Nested CRUD within customer context
3. **Search & Filter**: Text search and category filter
4. **UI Components**: Modals, tables, buttons, form validations

### UI Design Requirements
**IMPORTANT**: The UI must match the design in `developer_task_diagram.pdf`:

**Main Page Layout**:
- Header: "Customers" title with "Create" button (top-right)
- Search Bar: Horizontal layout with Search input, Category dropdown, Clear & Apply buttons
- Table: 5 columns (Name, Reference, Category, No of Contacts, Edit|Delete)

**Customer Modal Layout**:
- 2-column form layout:
  - Left column (General): Name, Reference, Category
  - Right column (Details): Start Date (with date picker), Description (textarea)
- Contacts section below form (full width): Table with Create button
- Footer: Save (blue) and Back (gray) buttons

**Contact Modal Layout**:
- Simple single-column form: First Name*, Last Name
- Footer: Save and Back buttons

### Technical Stack
- **Backend**: Laravel 11
- **Frontend**: Vue.js 3 with Composition API
- **Database**: MySQL 8.0+
- **Styling**: Tailwind CSS (recommended) or Bootstrap 5
- **Development Environment**: Docker (recommended for reproducibility)

---

## Phase 1: Project Initialization

### Step 1.1: Create Project Directory
```bash
# Create new empty folder
mkdir slipstream-demo
cd slipstream-demo
```

### Step 1.2: Initialize Git Repository
```bash
# Initialize git
git init

# Add remote origin
git remote add origin git@github.com:lenhanphung/slipstream-demo.git

# Create .gitignore for Laravel
cat > .gitignore << 'EOF'
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
docker-compose.override.yml
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
/.idea
/.vscode
EOF

git add .gitignore
git commit -m "Initial commit: Add .gitignore"
```

### Step 1.3: Install Fresh Laravel 11
```bash
# Install Laravel 11 using Composer
composer create-project laravel/laravel:^11.0 .

# Or if starting with empty folder already created
composer create-project laravel/laravel:^11.0 temp-laravel
mv temp-laravel/* temp-laravel/.* . 2>/dev/null
rm -rf temp-laravel

# Commit fresh Laravel installation
git add .
git commit -m "Install fresh Laravel 11"
git push -u origin main
```

---

## Phase 2: Environment Setup

### Step 2.1: Docker Configuration (Recommended)

Create `docker-compose.yml`:
```yaml
# Purpose: Define services for MySQL, PHP, Node.js, and Nginx
# Services needed:
# - app (PHP 8.2+ with Laravel)
# - mysql (MySQL 8.0)
# - node (for Vue.js compilation)
# - nginx (web server)
```

**Tasks**:
- Configure PHP service with required extensions (pdo_mysql, mbstring, gd, etc.)
- Set up MySQL service with port 3306
- Configure persistent volumes for database
- Set up Node.js service for asset compilation
- Configure Nginx for serving Laravel application

Create `.env.docker` for Docker-specific environment variables.

### Step 2.2: Local Environment Configuration

Update `.env` file:
```bash
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=mysql  # or 127.0.0.1 for local
DB_PORT=3306
DB_DATABASE=slipstream_demo
DB_USERNAME=root
DB_PASSWORD=secret

# App Configuration
APP_NAME="Slipstream Demo"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
```

### Step 2.3: Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Install additional packages needed
composer require laravel/breeze --dev  # For authentication scaffolding (optional)

# Install Vue.js 3
npm install vue@next vue-loader@next
npm install @vitejs/plugin-vue

# Install UI dependencies
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

Commit:
```bash
git add .
git commit -m "Setup environment and install dependencies"
```

---

## Phase 3: Database Design and Migration

### Step 3.1: Plan Database Schema

**Tables Needed**:
1. `customer_categories` - Lookup table for categories
2. `customers` - Main customer table
3. `contacts` - Contacts belonging to customers

**Relationships**:
- `customer_categories` 1-to-many `customers`
- `customers` 1-to-many `contacts`

### Step 3.2: Create Migrations

```bash
# Create customer_categories migration
php artisan make:migration create_customer_categories_table

# Create customers migration
php artisan make:migration create_customers_table

# Create contacts migration
php artisan make:migration create_contacts_table
```

**Migration Details**:

#### `customer_categories` table:
- `id` - Primary key
- `name` - string, unique (Gold, Silver, Bronze)
- `timestamps`

#### `customers` table:
- `id` - Primary key
- `name` - string, required
- `reference` - string, unique, required (e.g., CUST001)
- `customer_category_id` - foreign key to customer_categories
- `start_date` - date, required
- `description` - text, nullable
- `timestamps`
- `softDeletes` (optional for better data management)

#### `contacts` table:
- `id` - Primary key
- `customer_id` - foreign key to customers
- `first_name` - string, required
- `last_name` - string, required
- `timestamps`
- `softDeletes` (optional)

### Step 3.3: Create Seeders

```bash
# Create seeder for customer categories
php artisan make:seeder CustomerCategorySeeder

# Create seeder for demo customers (optional)
php artisan make:seeder CustomerSeeder

# Create seeder for demo contacts (optional)
php artisan make:seeder ContactSeeder
```

**Seeder Tasks**:
- `CustomerCategorySeeder`: Insert Gold, Silver, Bronze
- `CustomerSeeder`: Create 10-20 sample customers
- `ContactSeeder`: Create 2-5 contacts per customer

Update `DatabaseSeeder.php` to call these seeders.

### Step 3.4: Run Migrations
```bash
# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed
```

Commit:
```bash
git add .
git commit -m "Create database migrations and seeders"
```

---

## Phase 4: Backend Development (Laravel)

### Step 4.1: Create Models

```bash
# Create Customer model with relationships
php artisan make:model Customer

# Create CustomerCategory model
php artisan make:model CustomerCategory

# Create Contact model
php artisan make:model Contact
```

**Model Tasks**:

#### `Customer` model:
- Define fillable fields: name, reference, customer_category_id, start_date, description
- Relationship: `belongsTo(CustomerCategory)`
- Relationship: `hasMany(Contact)`
- Cast: `start_date` as date
- Accessor: `contacts_count` for number of contacts

#### `CustomerCategory` model:
- Define fillable: name
- Relationship: `hasMany(Customer)`

#### `Contact` model:
- Define fillable: customer_id, first_name, last_name
- Relationship: `belongsTo(Customer)`

### Step 4.2: Create Controllers

```bash
# Create API Resource controllers
php artisan make:controller Api/CustomerController --api
php artisan make:controller Api/ContactController --api
php artisan make:controller Api/CustomerCategoryController --api
```

**Controller Tasks**:

#### `CustomerController`:
- `index()` - List customers with search, filter, pagination
  - Accept query params: `search`, `category_id`
  - Return with `contacts_count`
  - Include `category` relationship
- `store()` - Create customer with validation
- `show()` - Get single customer with contacts
- `update()` - Update customer with validation
- `destroy()` - Delete customer (soft delete if enabled)

#### `ContactController`:
- `index()` - List contacts for a customer
- `store()` - Create contact for a customer
- `show()` - Get single contact
- `update()` - Update contact
- `destroy()` - Delete contact

#### `CustomerCategoryController`:
- `index()` - List all categories for dropdown

### Step 4.3: Create Form Requests (Validation)

```bash
# Create validation requests
php artisan make:request StoreCustomerRequest
php artisan make:request UpdateCustomerRequest
php artisan make:request StoreContactRequest
php artisan make:request UpdateContactRequest
```

**Validation Rules**:

#### `StoreCustomerRequest`:
- `name`: required, string, max:255
- `reference`: required, string, unique:customers, max:50
- `customer_category_id`: required, exists:customer_categories,id
- `start_date`: required, date
- `description`: nullable, string

#### `UpdateCustomerRequest`:
- Same as Store, but `unique` rule should ignore current record

#### `StoreContactRequest`:
- `customer_id`: required, exists:customers,id
- `first_name`: required, string, max:255
- `last_name`: required, string, max:255

#### `UpdateContactRequest`:
- Same as Store

### Step 4.4: Create API Resources (Response Formatting)

```bash
php artisan make:resource CustomerResource
php artisan make:resource ContactResource
php artisan make:resource CustomerCategoryResource
```

**Resource Tasks**:
- Format data structure for JSON responses
- Include relationships as needed
- Add computed fields (e.g., full_name for contacts)

### Step 4.5: Define API Routes

Edit `routes/api.php`:
```php
// API routes structure:
// - GET    /api/customers              - List customers
// - POST   /api/customers              - Create customer
// - GET    /api/customers/{id}         - Show customer
// - PUT    /api/customers/{id}         - Update customer
// - DELETE /api/customers/{id}         - Delete customer
// 
// - GET    /api/customers/{id}/contacts - List contacts for customer
// - POST   /api/contacts               - Create contact
// - GET    /api/contacts/{id}          - Show contact
// - PUT    /api/contacts/{id}          - Update contact
// - DELETE /api/contacts/{id}          - Delete contact
//
// - GET    /api/customer-categories    - List categories
```

Use `Route::apiResource()` for RESTful routing.

Commit:
```bash
git add .
git commit -m "Create models, controllers, and API routes"
```

---

## Phase 5: Frontend Development (Vue.js)

### Step 5.1: Configure Vite for Vue.js

Edit `vite.config.js`:
```javascript
// Configure Vue plugin
// Set up aliases (@components, @views, etc.)
// Configure server proxy for API calls
```

### Step 5.2: Create Vue Application Structure

```bash
# Create directory structure
mkdir -p resources/js/components
mkdir -p resources/js/views
mkdir -p resources/js/composables
mkdir -p resources/js/services
```

**Directory Structure**:
```
resources/js/
├── app.js                 # Main Vue app entry
├── components/
│   ├── CustomerTable.vue      # Customer listing table
│   ├── CustomerModal.vue      # Customer create/edit modal
│   ├── ContactTable.vue       # Contacts table (nested in customer modal)
│   ├── ContactModal.vue       # Contact create/edit modal
│   ├── ConfirmDialog.vue      # Delete confirmation modal
│   ├── SearchBar.vue          # Search and filter component
│   └── BaseButton.vue         # Reusable button component
├── views/
│   └── CustomerIndex.vue      # Main page view
├── composables/
│   ├── useCustomers.js        # Customer CRUD logic
│   ├── useContacts.js         # Contact CRUD logic
│   └── useModal.js            # Modal state management
└── services/
    └── api.js                 # Axios API wrapper
```

### Step 5.3: Create Main View Component

**File**: `resources/js/views/CustomerIndex.vue`

**UI Reference**: Match the layout from `developer_task_diagram.pdf`:
- Header with "Customers" title and "Create" button (top right)
- Search bar with inline filters below header
- Table displaying customer list
- Modal overlays for customer and contact forms

**Responsibilities**:
- Import and compose all child components
- Manage application state (customer list, search query, filters)
- Handle customer table display
- Manage customer modal (create/edit)
- Handle contact modal (create/edit)
- Implement search and filter functionality

### Step 5.4: Create Customer Components

#### `CustomerTable.vue`
**Features**:
- Display customers in table format
- Columns: Name, Reference, Category, No. of Contacts, Actions (Edit|Delete)
- Emit events: `edit`, `delete`
- Props: `customers` (array)

#### `CustomerModal.vue`
**Features**:
- Modal with form for customer details
- **Layout**: 2-column layout (General | Details)
- **General section** (left column):
  - Name (text input)
  - Reference (text input)
  - Category (dropdown: Gold/Silver/Bronze)
- **Details section** (right column):
  - Start Date (date picker with calendar icon)
  - Description (textarea)
- **Contacts section** (below form, full width):
  - Nested contacts table with Create button
  - Shows First Name, Last Name, Actions (Edit|Delete)
- Form validation
- Props: `customer` (object or null for create)
- Emit events: `save`, `close`
- Include `ContactTable` component
- Handle opening `ContactModal`
- Buttons: "Save" (blue/primary), "Back" (gray/secondary)

### Step 5.5: Create Contact Components

#### `ContactTable.vue`
**Features**:
- Display contacts in table format
- Columns: First Name, Last Name, Actions (Edit|Delete)
- Props: `contacts` (array), `customerId` (number)
- Emit events: `edit`, `delete`, `create`

#### `ContactModal.vue`
**Features**:
- Modal with form for contact details
- Fields: 
  - First Name* (required, marked with asterisk)
  - Last Name
- Form validation (First Name is required)
- Props: `contact` (object or null for create), `customerId` (number)
- Emit events: `save`, `close`
- Buttons: "Save" (primary), "Back" (secondary)

### Step 5.6: Create Shared Components

#### `ConfirmDialog.vue`
**Features**:
- Reusable confirmation modal
- Props: `visible`, `title`, `message`
- Emit events: `confirm`, `cancel`

#### `SearchBar.vue`
**Features**:
- **Horizontal layout** with inline elements:
  - Text input labeled "Search" (plain text search)
  - Dropdown labeled "Category" with [...Select...] placeholder
  - "Clear" button (secondary style)
  - "Apply" button (primary style)
- Props: `categories` (array)
- Emit events: `search`, `filter`, `clear`
- Match UI design: inline layout with proper spacing

### Step 5.7: Create Composables (Business Logic)

#### `useCustomers.js`
**Exports**:
- `customers` (ref) - List of customers
- `loading` (ref) - Loading state
- `fetchCustomers(searchQuery, categoryFilter)` - Get customers
- `createCustomer(data)` - Create new customer
- `updateCustomer(id, data)` - Update customer
- `deleteCustomer(id)` - Delete customer

#### `useContacts.js`
**Exports**:
- `contacts` (ref) - List of contacts for current customer
- `loading` (ref) - Loading state
- `fetchContacts(customerId)` - Get contacts for customer
- `createContact(data)` - Create new contact
- `updateContact(id, data)` - Update contact
- `deleteContact(id)` - Delete contact

#### `useModal.js`
**Exports**:
- `isOpen` (ref) - Modal visibility state
- `openModal()` - Open modal
- `closeModal()` - Close modal

### Step 5.8: Create API Service

**File**: `resources/js/services/api.js`

**Purpose**: Centralize all API calls using Axios

**Methods**:
- `getCustomers(params)` - GET /api/customers
- `getCustomer(id)` - GET /api/customers/{id}
- `createCustomer(data)` - POST /api/customers
- `updateCustomer(id, data)` - PUT /api/customers/{id}
- `deleteCustomer(id)` - DELETE /api/customers/{id}
- `getContacts(customerId)` - GET /api/customers/{id}/contacts
- `createContact(data)` - POST /api/contacts
- `updateContact(id, data)` - PUT /api/contacts/{id}
- `deleteContact(id)` - DELETE /api/contacts/{id}
- `getCustomerCategories()` - GET /api/customer-categories

Configure Axios defaults:
- Base URL: `/api`
- Headers: Accept JSON, Content-Type JSON
- Error interceptor for handling API errors

### Step 5.9: Setup Main Entry Point

**File**: `resources/js/app.js`

```javascript
// Import Vue and create app instance
// Import and register components
// Mount app to #app element
// Configure global properties if needed
```

### Step 5.10: Create Main Blade Template

**File**: `resources/views/customers/index.blade.php`

```html
<!-- Include Vite directives for CSS and JS -->
<!-- Create #app div for Vue mounting -->
<!-- Include any necessary meta tags -->
```

### Step 5.11: Create Web Route

Edit `routes/web.php`:
```php
// Add route for main customer page
// Route::get('/customers', function () {
//     return view('customers.index');
// });
```

Commit:
```bash
git add .
git commit -m "Create Vue.js frontend components and views"
```

---

## Phase 6: Styling and UI Polish

### Step 6.1: Configure Tailwind CSS

**File**: `tailwind.config.js`

```javascript
// Configure content paths to include Vue files
// Define custom theme colors matching design
// Add custom utility classes if needed
```

### Step 6.2: Create Global Styles

**File**: `resources/css/app.css`

```css
/* Import Tailwind directives */
/* Define custom CSS classes for modals */
/* Style table components */
/* Define button styles (primary, secondary, danger) */
/* Add form input styles */
/* Configure modal overlay and animations */
```

### Step 6.3: Implement Responsive Design

**Tasks**:
- Ensure table is responsive (scroll on mobile)
- Modal should be full-screen on mobile
- Forms should stack vertically on small screens
- Buttons should be touch-friendly (min 44x44px)

### Step 6.4: Add Loading States

**Components to Update**:
- Show spinner when fetching customers
- Show skeleton loader in tables while loading
- Disable buttons during API calls
- Show loading indicator in modals during save

### Step 6.5: Add Error Handling UI

**Tasks**:
- Display validation errors below form fields
- Show toast notifications for success/error messages
- Handle network errors gracefully
- Add empty state for no customers found

Commit:
```bash
git add .
git commit -m "Add styling, responsive design, and UX improvements"
```

---

## Phase 7: Testing and Quality Assurance

### Step 7.1: Create Feature Tests

```bash
# Create test files
php artisan make:test CustomerControllerTest
php artisan make:test ContactControllerTest
```

**Test Coverage**:
- Test customer CRUD operations
- Test contact CRUD operations
- Test search functionality
- Test filter functionality
- Test validation rules
- Test relationships

### Step 7.2: Create Unit Tests

```bash
php artisan make:test Models/CustomerTest --unit
php artisan make:test Models/ContactTest --unit
```

**Test Coverage**:
- Test model relationships
- Test accessors and mutators
- Test model methods

### Step 7.3: Run Tests

```bash
# Run all tests
php artisan test

# Run with coverage (requires xdebug)
php artisan test --coverage
```

### Step 7.4: Code Quality Checks

```bash
# Install PHP CS Fixer (optional)
composer require --dev friendsofphp/php-cs-fixer

# Run code style fixer
./vendor/bin/php-cs-fixer fix

# Install ESLint for Vue (optional)
npm install --save-dev eslint eslint-plugin-vue

# Run ESLint
npm run lint
```

Commit:
```bash
git add .
git commit -m "Add tests and code quality improvements"
```

---

## Phase 8: Documentation

### Step 8.1: Create README.md

**File**: `README.md` (for Git repository)

**Contents**:
```markdown
# Slipstream Demo - Customer Management System

## Overview
Brief description of the project and its purpose.

## Features
- Customer CRUD operations
- Contact management (nested within customers)
- Search and filter functionality
- Modal-based forms
- Responsive design

## Tech Stack
- Laravel 11
- Vue.js 3
- MySQL 8.0
- Tailwind CSS
- Docker

## Prerequisites
- Docker and Docker Compose
OR
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

## Installation

### Using Docker (Recommended)
1. Clone repository
2. Copy .env.example to .env
3. Run `docker-compose up -d`
4. Run migrations: `docker-compose exec app php artisan migrate --seed`
5. Access at http://localhost

### Manual Installation
1. Clone repository
2. Run `composer install`
3. Run `npm install`
4. Copy .env.example to .env
5. Configure database in .env
6. Generate app key: `php artisan key:generate`
7. Run migrations: `php artisan migrate --seed`
8. Build assets: `npm run build` or `npm run dev`
9. Start server: `php artisan serve`

## Usage
Navigate to `/customers` to access the customer management interface.

## API Documentation
Brief overview of available API endpoints.

## Testing
Run tests with: `php artisan test`

## Project Structure
Overview of directory structure and key files.

## Development Notes
Any important notes for developers.

## License
MIT License
```

### Step 8.2: Add Code Comments

**Tasks**:
- Add PHPDoc blocks to all controller methods
- Document all public methods in models
- Add comments to complex business logic
- Document Vue component props and emits
- Add JSDoc comments to JavaScript functions

### Step 8.3: Create API Documentation

**Option 1**: Use Laravel API Resource (manual)
- Create `docs/API.md` documenting all endpoints

**Option 2**: Use Swagger/OpenAPI
```bash
composer require darkaonline/l5-swagger
php artisan l5-swagger:generate
```

Commit:
```bash
git add .
git commit -m "Add comprehensive documentation"
```

---

## Phase 9: Final Review and Deployment

### Step 9.1: Final Testing Checklist

- [ ] All CRUD operations work correctly
- [ ] Search functionality works
- [ ] Filter by category works
- [ ] Modal forms open and close properly
- [ ] Form validation displays errors
- [ ] Delete confirmation works
- [ ] Contact management within customer modal works
- [ ] Responsive design works on mobile
- [ ] No console errors
- [ ] All API endpoints return correct data
- [ ] Database relationships work correctly

### Step 9.2: Code Review Checklist

- [ ] Follow Laravel best practices
- [ ] Follow Vue.js best practices
- [ ] Proper separation of concerns
- [ ] DRY principle applied
- [ ] Meaningful variable and function names
- [ ] Proper error handling
- [ ] Security considerations (SQL injection, XSS)
- [ ] Input validation on both frontend and backend
- [ ] Proper HTTP status codes in responses

### Step 9.3: Performance Optimization

**Tasks**:
- Optimize database queries (use eager loading)
- Add database indexes where needed
- Minify and bundle assets for production
- Enable caching for customer categories
- Consider pagination for large datasets

### Step 9.4: Prepare for Submission

```bash
# Ensure all changes are committed
git status

# Push final changes
git push origin main

# Create release tag
git tag -a v1.0.0 -m "Initial release for assessment"
git push origin v1.0.0
```

### Step 9.5: Send Notification Email

**Email Content Template**:
```
Subject: Slipstream Developer Assessment - Completed

Hi Slipstream Team,

I have completed the Developer Assessment Task. Here are the details:

Repository: https://github.com/lenhanphung/slipstream-demo
Branch: main
Tag: v1.0.0

The repository includes:
- Fresh Laravel 11 installation
- Customer and Contact management system
- Vue.js frontend with modal-based forms
- Search and filter functionality
- Comprehensive README with installation instructions
- Docker configuration for easy setup
- Automated tests

Please let me know if you need any clarification or have questions.

Best regards,
[Your Name]
```

---

## Phase 10: Post-Submission Improvements (Optional)

These are enhancements that go beyond the requirements but demonstrate advanced skills:

### Optional Features:
- Add export functionality (CSV/Excel)
- Implement import from CSV
- Add sorting to table columns
- Add bulk delete functionality
- Implement activity logging
- Add API rate limiting
- Implement caching strategy
- Add email notifications
- Create admin panel
- Add user authentication

---

## Troubleshooting Guide

### Common Issues:

#### Docker Issues:
- Port 3306 already in use: Change MySQL port in docker-compose.yml
- Permission denied: Run `chmod -R 777 storage bootstrap/cache`

#### Laravel Issues:
- 500 Error: Check `.env` configuration, run `php artisan config:clear`
- Migration errors: Check database connection, verify migration files

#### Vue.js Issues:
- Components not rendering: Check Vite config, ensure Vue plugin is installed
- API calls failing: Check API routes, verify CORS settings

#### Database Issues:
- Connection refused: Verify MySQL is running, check credentials
- Foreign key constraint fails: Ensure parent records exist before creating children

---

## Best Practices Summary

### Laravel:
- Use Form Requests for validation
- Use API Resources for response formatting
- Implement Repository pattern for complex logic (optional)
- Use Eloquent relationships properly
- Follow PSR-12 coding standards

### Vue.js:
- Use Composition API for better code organization
- Create reusable components
- Keep components focused (single responsibility)
- Use props for parent-to-child communication
- Use emits for child-to-parent communication
- Implement proper error handling

### Database:
- Use migrations for schema changes
- Use seeders for test data
- Add proper indexes for performance
- Use foreign key constraints for data integrity

### Git:
- Make atomic commits (one feature/fix per commit)
- Write clear commit messages
- Use meaningful branch names
- Tag releases properly

---

## Folder Structure Reference

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
│   │   ├── xxxx_create_customer_categories_table.php
│   │   ├── xxxx_create_customers_table.php
│   │   └── xxxx_create_contacts_table.php
│   └── seeders/
│       ├── CustomerCategorySeeder.php
│       ├── CustomerSeeder.php
│       └── ContactSeeder.php
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   ├── app.js
│   │   ├── components/
│   │   │   ├── CustomerTable.vue
│   │   │   ├── CustomerModal.vue
│   │   │   ├── ContactTable.vue
│   │   │   ├── ContactModal.vue
│   │   │   ├── ConfirmDialog.vue
│   │   │   └── SearchBar.vue
│   │   ├── views/
│   │   │   └── CustomerIndex.vue
│   │   ├── composables/
│   │   │   ├── useCustomers.js
│   │   │   ├── useContacts.js
│   │   │   └── useModal.js
│   │   └── services/
│   │       └── api.js
│   └── views/
│       └── customers/
│           └── index.blade.php
├── routes/
│   ├── api.php
│   └── web.php
├── tests/
│   ├── Feature/
│   │   ├── CustomerControllerTest.php
│   │   └── ContactControllerTest.php
│   └── Unit/
│       └── Models/
│           ├── CustomerTest.php
│           └── ContactTest.php
├── docker-compose.yml
├── Dockerfile
├── .env.example
├── .gitignore
├── README.md
└── instruction.md (this file)
```

---

## Timeline Estimate

- **Phase 1-2** (Setup): 30 minutes
- **Phase 3** (Database): 1 hour
- **Phase 4** (Backend): 2-3 hours
- **Phase 5** (Frontend): 3-4 hours
- **Phase 6** (Styling): 1-2 hours
- **Phase 7** (Testing): 1-2 hours
- **Phase 8** (Documentation): 1 hour
- **Phase 9** (Final Review): 30 minutes

**Total**: 10-14 hours

---

## Success Criteria

The project will be considered complete when:

1. ✅ Fresh Laravel 11 is installed and committed
2. ✅ Database schema is properly designed with migrations
3. ✅ All API endpoints work correctly
4. ✅ Frontend displays customer list with search and filter
5. ✅ Customer create/edit modal works with all fields
6. ✅ Contact management works within customer modal
7. ✅ Delete confirmation works for both customers and contacts
8. ✅ Form validation works on both frontend and backend
9. ✅ Responsive design works on different screen sizes
10. ✅ Code follows best practices with proper comments
11. ✅ README.md provides clear installation instructions
12. ✅ Repository is organized with meaningful commits
13. ✅ Application can be reproduced by reviewer using README

---

## Notes for AI Agent

When following these instructions:

1. **Execute commands exactly as written** - Don't skip steps
2. **Make separate commits** - Commit after completing each phase
3. **Write meaningful commit messages** - Describe what was done
4. **Follow Laravel conventions** - Use proper naming and structure
5. **Test as you go** - Verify each feature works before moving on
6. **Comment your code** - Explain complex logic in English
7. **Handle errors gracefully** - Add try-catch blocks and user-friendly messages
8. **Think about the reviewer** - They should be able to run your code easily

Remember: The goal is to demonstrate your ability to:
- Translate requirements into working code
- Follow best practices
- Write clean, maintainable code
- Create a professional, working application
