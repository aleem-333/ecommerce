<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            // Electronics Category
            [
                'name' => 'Wireless Bluetooth Headphones',
                'description' => 'High-quality wireless headphones with noise cancellation and 30-hour battery life. Perfect for music lovers and professionals.',
                'price' => 149.99,
                'category' => 'electronics',
                'stock' => 45,
                'sku' => 'ELEC-001',
                'image' => 'images/products/product-1.jpg',
            ],
            [
                'name' => 'Smart Watch Pro',
                'description' => 'Advanced smartwatch with fitness tracking, heart rate monitor, and GPS. Compatible with iOS and Android.',
                'price' => 299.99,
                'category' => 'electronics',
                'stock' => 28,
                'sku' => 'ELEC-002',
                'image' => 'images/products/product-2.jpg',
            ],
            [
                'name' => '4K Webcam',
                'description' => 'Professional 4K webcam with auto-focus and built-in microphone. Ideal for streaming and video calls.',
                'price' => 89.99,
                'category' => 'electronics',
                'stock' => 8,
                'sku' => 'ELEC-003',
                'image' => 'images/products/product-3.jpg',
            ],
            [
                'name' => 'Portable Power Bank',
                'description' => '20000mAh power bank with fast charging and multiple USB ports. Keep your devices charged on the go.',
                'price' => 39.99,
                'category' => 'electronics',
                'stock' => 0,
                'sku' => 'ELEC-004',
                'image' => 'images/products/product-4.jpg',
            ],
            [
                'name' => 'Mechanical Gaming Keyboard',
                'description' => 'RGB backlit mechanical keyboard with customizable keys and anti-ghosting technology.',
                'price' => 129.99,
                'category' => 'electronics',
                'stock' => 15,
                'sku' => 'ELEC-005',
                'image' => 'images/products/product-5.jpg',
            ],

            // Clothing Category
            [
                'name' => 'Classic Denim Jacket',
                'description' => 'Vintage-style denim jacket made from premium cotton. Perfect for casual outings.',
                'price' => 79.99,
                'category' => 'clothing',
                'stock' => 32,
                'sku' => 'CLTH-001',
                'image' => 'images/products/product-6.jpg',
            ],
            [
                'name' => 'Athletic Running Shoes',
                'description' => 'Lightweight running shoes with breathable mesh and cushioned sole. Ideal for runners and athletes.',
                'price' => 119.99,
                'category' => 'clothing',
                'stock' => 6,
                'sku' => 'CLTH-002',
                'image' => 'images/products/product-7.jpg',
            ],
            [
                'name' => 'Wool Winter Coat',
                'description' => 'Elegant wool coat with thermal lining. Stay warm and stylish during winter.',
                'price' => 199.99,
                'category' => 'clothing',
                'stock' => 18,
                'sku' => 'CLTH-003',
                'image' => 'images/products/product-8.jpg',
            ],
            [
                'name' => 'Cotton T-Shirt Pack',
                'description' => 'Set of 5 premium cotton t-shirts in assorted colors. Comfortable and durable.',
                'price' => 49.99,
                'category' => 'clothing',
                'stock' => 50,
                'sku' => 'CLTH-004',
                'image' => 'images/products/product-9.jpg',
            ],
            [
                'name' => 'Designer Sunglasses',
                'description' => 'Polarized sunglasses with UV protection and stylish frames. Perfect for sunny days.',
                'price' => 159.99,
                'category' => 'clothing',
                'stock' => 0,
                'sku' => 'CLTH-005',
                'image' => 'images/products/product-10.jpg',
            ],

            // Home Category
            [
                'name' => 'Stainless Steel Blender',
                'description' => 'Powerful 1000W blender with multiple speed settings. Perfect for smoothies and soups.',
                'price' => 89.99,
                'category' => 'home',
                'stock' => 22,
                'sku' => 'HOME-001',
                'image' => 'images/products/product-1.jpg',
            ],
            [
                'name' => 'Memory Foam Pillow',
                'description' => 'Ergonomic memory foam pillow for better sleep and neck support.',
                'price' => 39.99,
                'category' => 'home',
                'stock' => 40,
                'sku' => 'HOME-002',
                'image' => 'images/products/product-2.jpg',
            ],
            [
                'name' => 'LED Desk Lamp',
                'description' => 'Adjustable LED desk lamp with touch controls and USB charging port.',
                'price' => 34.99,
                'category' => 'home',
                'stock' => 9,
                'sku' => 'HOME-003',
                'image' => 'images/products/product-3.jpg',
            ],
            [
                'name' => 'Ceramic Cookware Set',
                'description' => '10-piece ceramic non-stick cookware set. Dishwasher safe and eco-friendly.',
                'price' => 179.99,
                'category' => 'home',
                'stock' => 12,
                'sku' => 'HOME-004',
                'image' => 'images/products/product-4.jpg',
            ],
            [
                'name' => 'Robot Vacuum Cleaner',
                'description' => 'Smart robot vacuum with app control and automatic charging. Keep your floors clean effortlessly.',
                'price' => 349.99,
                'category' => 'home',
                'stock' => 5,
                'sku' => 'HOME-005',
                'image' => 'images/products/product-5.jpg',
            ],

            // Books Category
            [
                'name' => 'The Art of Programming',
                'description' => 'Comprehensive guide to modern programming practices and design patterns.',
                'price' => 49.99,
                'category' => 'books',
                'stock' => 35,
                'sku' => 'BOOK-001',
                'image' => 'images/products/product-6.jpg',
            ],
            [
                'name' => 'Cooking Masterclass',
                'description' => 'Professional cooking techniques and recipes from world-renowned chefs.',
                'price' => 34.99,
                'category' => 'books',
                'stock' => 28,
                'sku' => 'BOOK-002',
                'image' => 'images/products/product-7.jpg',
            ],
            [
                'name' => 'History of Innovation',
                'description' => 'Explore the greatest innovations that shaped our modern world.',
                'price' => 29.99,
                'category' => 'books',
                'stock' => 0,
                'sku' => 'BOOK-003',
                'image' => 'images/products/product-8.jpg',
            ],
            [
                'name' => 'Mindfulness and Meditation',
                'description' => 'Practical guide to mindfulness and meditation techniques for better mental health.',
                'price' => 24.99,
                'category' => 'books',
                'stock' => 42,
                'sku' => 'BOOK-004',
                'image' => 'images/products/product-9.jpg',
            ],
            [
                'name' => 'Digital Marketing Handbook',
                'description' => 'Complete guide to digital marketing strategies and social media management.',
                'price' => 44.99,
                'category' => 'books',
                'stock' => 7,
                'sku' => 'BOOK-005',
                'image' => 'images/products/product-10.jpg',
            ],

            // Sports Category
            [
                'name' => 'Yoga Mat Premium',
                'description' => 'Extra-thick yoga mat with non-slip surface and carrying strap.',
                'price' => 39.99,
                'category' => 'sports',
                'stock' => 55,
                'sku' => 'SPRT-001',
                'image' => 'images/products/product-1.jpg',
            ],
            [
                'name' => 'Adjustable Dumbbells Set',
                'description' => 'Space-saving adjustable dumbbells from 5 to 50 lbs. Perfect for home workouts.',
                'price' => 299.99,
                'category' => 'sports',
                'stock' => 10,
                'sku' => 'SPRT-002',
                'image' => 'images/products/product-2.jpg',
            ],
            [
                'name' => 'Tennis Racket Pro',
                'description' => 'Professional-grade tennis racket with graphite frame and comfortable grip.',
                'price' => 149.99,
                'category' => 'sports',
                'stock' => 14,
                'sku' => 'SPRT-003',
                'image' => 'images/products/product-3.jpg',
            ],
            [
                'name' => 'Sports Water Bottle',
                'description' => 'Insulated stainless steel water bottle that keeps drinks cold for 24 hours.',
                'price' => 24.99,
                'category' => 'sports',
                'stock' => 0,
                'sku' => 'SPRT-004',
                'image' => 'images/products/product-4.jpg',
            ],
            [
                'name' => 'Resistance Bands Set',
                'description' => 'Set of 5 resistance bands with different strength levels and door anchor.',
                'price' => 29.99,
                'category' => 'sports',
                'stock' => 38,
                'sku' => 'SPRT-005',
                'image' => 'images/products/product-5.jpg',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
