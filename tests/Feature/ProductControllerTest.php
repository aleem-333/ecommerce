<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test filtering products by category.
     *
     * @return void
     */
    public function test_can_filter_products_by_category()
    {
        // Create products in different categories
        Product::factory()->create(['category' => 'electronics', 'name' => 'Laptop']);
        Product::factory()->create(['category' => 'electronics', 'name' => 'Phone']);
        Product::factory()->create(['category' => 'clothing', 'name' => 'Shirt']);

        $response = $this->getJson('/api/products/filter?category=electronics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'category', 'price', 'stock']
                ],
                'meta'
            ])
            ->assertJson([
                'success' => true,
                'meta' => ['total' => 2]
            ]);
    }

    /**
     * Test filtering products by price range.
     *
     * @return void
     */
    public function test_can_filter_products_by_price_range()
    {
        Product::factory()->create(['category' => 'electronics', 'price' => 50.00]);
        Product::factory()->create(['category' => 'electronics', 'price' => 100.00]);
        Product::factory()->create(['category' => 'electronics', 'price' => 200.00]);

        $response = $this->getJson('/api/products/filter?category=electronics&min_price=75&max_price=150');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'meta' => ['total' => 1]]);
    }

    /**
     * Test filtering in-stock products only.
     *
     * @return void
     */
    public function test_can_filter_in_stock_products_only()
    {
        Product::factory()->create(['category' => 'electronics', 'stock' => 10]);
        Product::factory()->create(['category' => 'electronics', 'stock' => 0]);
        Product::factory()->create(['category' => 'electronics', 'stock' => 5]);

        $response = $this->getJson('/api/products/filter?category=electronics&in_stock_only=1');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'meta' => ['total' => 2]]);
    }

    /**
     * Test searching products by name and description.
     *
     * @return void
     */
    public function test_can_search_products()
    {
        Product::factory()->create(['category' => 'electronics', 'name' => 'Wireless Mouse', 'description' => 'Great mouse']);
        Product::factory()->create(['category' => 'electronics', 'name' => 'Keyboard', 'description' => 'Wireless keyboard']);
        Product::factory()->create(['category' => 'electronics', 'name' => 'Monitor', 'description' => 'HD display']);

        $response = $this->getJson('/api/products/filter?category=electronics&search=wireless');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'meta' => ['total' => 2]]);
    }

    /**
     * Test filter products requires category.
     *
     * @return void
     */
    public function test_filter_products_requires_category()
    {
        $response = $this->getJson('/api/products/filter');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category']);
    }

    /**
     * Test adding product to cart successfully.
     *
     * @return void
     */
    public function test_can_add_product_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/cart', [
                'product_id' => $product->id,
                'quantity' => 2
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Product added to cart successfully.'
            ]);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }

    /**
     * Test adding product to cart requires authentication.
     *
     * @return void
     */
    public function test_adding_to_cart_requires_authentication()
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/cart', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test cannot add out of stock product to cart.
     *
     * @return void
     */
    public function test_cannot_add_out_of_stock_product_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 0]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/cart', [
                'product_id' => $product->id,
                'quantity' => 1
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Insufficient stock available.'
            ]);
    }

    /**
     * Test cannot add more than available stock to cart.
     *
     * @return void
     */
    public function test_cannot_add_more_than_available_stock()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/cart', [
                'product_id' => $product->id,
                'quantity' => 10
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Insufficient stock available.'
            ]);
    }

    /**
     * Test updating cart item quantity.
     *
     * @return void
     */
    public function test_can_update_cart_item_quantity()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        $cartItem = Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/cart/{$cartItem->id}", [
                'quantity' => 5
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cart item updated successfully.'
            ]);

        $this->assertDatabaseHas('carts', [
            'id' => $cartItem->id,
            'quantity' => 5
        ]);
    }

    /**
     * Test deleting cart item when quantity is zero.
     *
     * @return void
     */
    public function test_cart_item_deleted_when_quantity_is_zero()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        $cartItem = Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/cart/{$cartItem->id}", [
                'quantity' => 0
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cart item removed successfully.'
            ]);

        $this->assertDatabaseMissing('carts', [
            'id' => $cartItem->id
        ]);
    }

    /**
     * Test user can only update own cart items.
     *
     * @return void
     */
    public function test_user_can_only_update_own_cart_items()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $cartItem = Cart::create([
            'user_id' => $user1->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->actingAs($user2, 'sanctum')
            ->putJson("/api/cart/{$cartItem->id}", [
                'quantity' => 5
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized. You can only modify your own cart items.'
            ]);
    }

    /**
     * Test updating non-existent cart item returns 404.
     *
     * @return void
     */
    public function test_updating_non_existent_cart_item_returns_404()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/cart/999', [
                'quantity' => 5
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Cart item not found.'
            ]);
    }
}
