# TALL Stack eCommerce Project

A modern, production-ready eCommerce platform built with the TALL stack (Tailwind CSS, Alpine.js, Laravel, Livewire).

![TALL Stack](https://img.shields.io/badge/TALL-Stack-blue)
![Laravel](https://img.shields.io/badge/Laravel-9.x-red)
![Livewire](https://img.shields.io/badge/Livewire-2.x-pink)
![Tests](https://img.shields.io/badge/Tests-26%2B-green)

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Seeding Test Data](#seeding-test-data)
- [Running the Application](#running-the-application)
- [Testing](#testing)
- [API Endpoints](#api-endpoints)
- [Project Structure](#project-structure)
- [Assumptions & Notes](#assumptions--notes)

## ✨ Features

### Core Features
- ✅ **Product Filtering System**
  - Filter by category
  - Price range filter (min/max)
  - In-stock only toggle
  - Real-time search (name & description)
  - Sort by: Newest, Price (Low/High), Name (A-Z)

- ✅ **Shopping Cart Management**
  - Add/remove items
  - Update quantities with stock validation
  - Real-time cart badge updates
  - Cart total calculation with 10% tax

- ✅ **User Experience**
  - Responsive design (mobile-first)
  - Skeleton loading states
  - Toast notifications for actions
  - Empty state messaging
  - Accessible UI (WCAG compliant)

### Bonus Features
- ✅ Filter persistence (localStorage)
- ✅ API rate limiting (30 req/min on cart endpoints)
- ✅ Comprehensive testing (26+ tests, 80%+ coverage)
- ✅ Query string parameters for filter sharing

## 🛠 Tech Stack

- **Backend**: Laravel 9.x
- **Frontend**:
  - Livewire 2.x (Full-page components)
  - Alpine.js 3.x (Reactive UI)
  - Tailwind CSS 3.x (Utility-first styling)
- **Database**: MySQL/SQLite
- **Authentication**: Laravel Breeze + Sanctum
- **Testing**: PEST PHP

## 📦 Requirements

- PHP >= 8.0
- Composer
- Node.js >= 16.x
- NPM or Yarn
- MySQL 5.7+ or SQLite

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd Ecommerce
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install JavaScript Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Database

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=your_password
```

Or use SQLite for quick setup:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

## 🗄 Database Setup

### Run Migrations

```bash
php artisan migrate
```

This creates the following tables:
- `users` - User accounts
- `products` - Product catalog
- `carts` - Shopping cart items
- `personal_access_tokens` - API tokens (Sanctum)

## 🌱 Seeding Test Data

### Create Database Seeder

Create a seeder to populate test data:

```bash
php artisan make:seeder ProductSeeder
```

Add this to `database/seeders/ProductSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['name' => 'Wireless Bluetooth Headphones', 'category' => 'electronics', 'price' => 79.99, 'stock' => 50],
            ['name' => 'Smart Watch Pro', 'category' => 'electronics', 'price' => 299.99, 'stock' => 30],
            ['name' => 'Laptop Stand Aluminum', 'category' => 'accessories', 'price' => 49.99, 'stock' => 100],
            ['name' => 'Mechanical Gaming Keyboard', 'category' => 'electronics', 'price' => 129.99, 'stock' => 25],
            ['name' => 'USB-C Hub 7-in-1', 'category' => 'accessories', 'price' => 39.99, 'stock' => 75],
            ['name' => 'Wireless Mouse Ergonomic', 'category' => 'electronics', 'price' => 34.99, 'stock' => 60],
            ['name' => 'Leather Laptop Bag', 'category' => 'accessories', 'price' => 89.99, 'stock' => 40],
            ['name' => '4K Webcam HD Pro', 'category' => 'electronics', 'price' => 149.99, 'stock' => 20],
            ['name' => 'Phone Stand Adjustable', 'category' => 'accessories', 'price' => 19.99, 'stock' => 150],
            ['name' => 'Portable SSD 1TB', 'category' => 'electronics', 'price' => 179.99, 'stock' => 35],
            ['name' => 'Desk Organizer Bamboo', 'category' => 'accessories', 'price' => 24.99, 'stock' => 80],
            ['name' => 'Bluetooth Speaker Waterproof', 'category' => 'electronics', 'price' => 59.99, 'stock' => 45],
            ['name' => 'Monitor Arm Dual', 'category' => 'accessories', 'price' => 119.99, 'stock' => 28],
            ['name' => 'Noise Cancelling Earbuds', 'category' => 'electronics', 'price' => 199.99, 'stock' => 15],
            ['name' => 'Cable Management Kit', 'category' => 'accessories', 'price' => 14.99, 'stock' => 200],
            ['name' => 'RGB LED Strip 5M', 'category' => 'electronics', 'price' => 29.99, 'stock' => 90],
            ['name' => 'Laptop Sleeve 15 inch', 'category' => 'accessories', 'price' => 24.99, 'stock' => 110],
            ['name' => 'Wireless Charging Pad', 'category' => 'electronics', 'price' => 34.99, 'stock' => 65],
            ['name' => 'Screen Cleaning Kit', 'category' => 'accessories', 'price' => 12.99, 'stock' => 180],
            ['name' => 'USB Flash Drive 128GB', 'category' => 'electronics', 'price' => 24.99, 'stock' => 120],
            ['name' => 'Desk Mat Extended', 'category' => 'accessories', 'price' => 29.99, 'stock' => 95],
            ['name' => 'Portable Charger 20000mAh', 'category' => 'electronics', 'price' => 44.99, 'stock' => 55],
            ['name' => 'Phone Case Premium', 'category' => 'accessories', 'price' => 19.99, 'stock' => 140],
            ['name' => 'HDMI Cable 4K 2M', 'category' => 'electronics', 'price' => 15.99, 'stock' => 160],
            ['name' => 'Laptop Cooling Pad', 'category' => 'accessories', 'price' => 39.99, 'stock' => 70],
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'description' => 'High-quality ' . strtolower($product['name']) . ' for your tech needs.',
                'price' => $product['price'],
                'category' => $product['category'],
                'stock' => $product['stock'],
                'sku' => 'SKU-' . strtoupper(substr(md5($product['name']), 0, 8)),
            ]);
        }
    }
}
```

Update `database/seeders/DatabaseSeeder.php`:

```php
public function run()
{
    // Create test user
    \App\Models\User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password')
    ]);

    // Seed products
    $this->call([
        ProductSeeder::class,
    ]);
}
```

### Run the Seeder

```bash
php artisan db:seed
```

Or migrate and seed in one command:

```bash
php artisan migrate:fresh --seed
```

## 🏃 Running the Application

### 1. Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 2. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`

### 3. Access the Application

- **Homepage/Products**: `http://127.0.0.1:8000`
- **Cart**: `http://127.0.0.1:8000/cart`
- **Login**: `http://127.0.0.1:8000/login`
- **Register**: `http://127.0.0.1:8000/register`

### Default Test Credentials

```
Email: test@example.com
Password: password
```

## 🧪 Testing

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
# Feature tests
php artisan test --testsuite=Feature

# Unit tests
php artisan test --testsuite=Unit
```

### Run With Coverage

```bash
php artisan test --coverage
```

### Test Structure

```
tests/
├── Feature/
│   ├── ProductControllerTest.php (13 tests)
│   ├── ProductFilterLivewireTest.php (13 tests)
│   └── Auth/ (6 tests)
└── Unit/
    └── ExampleTest.php
```

### Key Test Cases Covered

**ProductController**
- Filter products by category
- Filter by price range
- Filter in-stock products only
- Search products
- Add to cart validation
- Stock availability checks
- Authorization checks

**ProductFilter Livewire**
- Component rendering
- Filter functionality
- Cart operations
- Pagination
- Empty states

## 🔌 API Endpoints

### Products

```http
GET /api/products/filter
```

Query Parameters:
- `category` (string, required)
- `min_price` (float, optional)
- `max_price` (float, optional)
- `in_stock_only` (boolean, optional)
- `search` (string, optional)
- `page` (integer, optional)

Response:
```json
{
  "data": [...],
  "current_page": 1,
  "total": 25,
  "per_page": 15
}
```

### Cart (Authenticated)

**Add to Cart**
```http
POST /api/cart
Authorization: Bearer {token}
Content-Type: application/json

{
  "product_id": 1,
  "quantity": 2
}
```

**Update Cart Item**
```http
PUT /api/cart/{cartItemId}
Authorization: Bearer {token}
Content-Type: application/json

{
  "quantity": 3
}
```

### Rate Limiting

Cart endpoints are rate limited to **30 requests per minute** per user.

Response when rate limited:
```json
{
  "message": "Too many cart requests. Please slow down.",
  "retry_after": 60
}
```

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── ProductController.php    # API endpoints
│   └── Livewire/
│       ├── ProductFilter.php        # Product listing component
│       └── CartView.php             # Cart management component
├── Models/
│   ├── Product.php                  # Product model with scopes
│   └── Cart.php                     # Cart model with relationships
└── Providers/
    └── RouteServiceProvider.php     # Rate limiting configuration

resources/
├── css/
│   └── app.css                      # Tailwind configuration
├── js/
│   └── app.js                       # Alpine.js setup
└── views/
    ├── layouts/
    │   └── shop.blade.php           # Main layout with nav
    └── livewire/
        ├── product-filter.blade.php # Product grid view
        └── cart-view.blade.php      # Cart view

routes/
├── api.php                          # API routes
└── web.php                          # Web routes

tests/
├── Feature/
│   ├── ProductControllerTest.php
│   └── ProductFilterLivewireTest.php
└── Unit/
```

## 📝 Assumptions & Notes

### Design Decisions

1. **Authentication**: Used Laravel Breeze for simplicity. Guests can view products, but must login to add to cart.

2. **Cart Management**: Cart is user-specific and persists in database. No guest cart implementation.

3. **Stock Management**: Stock is checked on add-to-cart but not reserved. For production, consider implementing stock reservation.

4. **Pricing**: Prices are in USD. Tax is calculated at 10% (hardcoded). For production, implement dynamic tax rates.

5. **Images**: Product images are optional (placeholders shown). To add images, store them in `public/images/products/` and update the `image` column.

6. **Pagination**: 15 products per page. Configurable in `ProductFilter.php`.

### TALL Stack Patterns Used

- **Livewire**: Full-page components with `->layout()`
- **Alpine.js**: Used only for UI interactivity (no vanilla JS DOM manipulation)
- **Browser Events**: `dispatchBrowserEvent()` for cross-component communication
- **Query Strings**: Filter state persisted in URL for sharing
- **localStorage**: Filter preferences saved client-side

### Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11 not supported (Alpine.js requirement)

### Performance Notes

- Query scopes used for reusable filtering logic
- Eager loading implemented in CartView (`with('product')`)
- Rate limiting prevents API abuse
- Skeleton loading improves perceived performance

## 🚀 Deployment

### Build for Production

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev

# Build assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Environment Variables (Production)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Use strong random key
APP_KEY=

# Production database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-db-name

# Cache driver
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgments

Built with:
- [Laravel](https://laravel.com)
- [Livewire](https://laravel-livewire.com)
- [Alpine.js](https://alpinejs.dev)
- [Tailwind CSS](https://tailwindcss.com)

---

**Made with ❤️ using the TALL Stack**
# ecommerce
