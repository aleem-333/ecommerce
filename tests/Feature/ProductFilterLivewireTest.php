<?php

namespace Tests\Feature;

use App\Http\Livewire\ProductFilter;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductFilterLivewireTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test component renders successfully.
     *
     * @return void
     */
    public function test_component_renders()
    {
        Livewire::test(ProductFilter::class)
            ->assertStatus(200);
    }

    /**
     * Test filtering products by category.
     *
     * @return void
     */
    public function test_can_filter_by_category()
    {
        Product::factory()->create(['category' => 'electronics', 'name' => 'Laptop']);
        Product::factory()->create(['category' => 'clothing', 'name' => 'Shirt']);

        Livewire::test(ProductFilter::class)
            ->set('category', 'electronics')
            ->assertSee('Laptop')
            ->assertDontSee('Shirt');
    }

    /**
     * Test filtering by price range.
     *
     * @return void
     */
    public function test_can_filter_by_price_range()
    {
        Product::factory()->create(['category' => 'electronics', 'name' => 'Cheap Item', 'price' => 50]);
        Product::factory()->create(['category' => 'electronics', 'name' => 'Mid Item', 'price' => 100]);
        Product::factory()->create(['category' => 'electronics', 'name' => 'Expensive Item', 'price' => 200]);

        Livewire::test(ProductFilter::class)
            ->set('category', 'electronics')
            ->set('minPrice', 75)
            ->set('maxPrice', 150)
            ->assertSee('Mid Item')
            ->assertDontSee('Cheap Item')
            ->assertDontSee('Expensive Item');
    }

    /**
     * Test filtering in-stock products only.
     *
     * @return void
     */
    public function test_can_filter_in_stock_only()
    {
        Product::factory()->create(['category' => 'electronics', 'name' => 'Available', 'stock' => 10]);
        Product::factory()->create(['category' => 'electronics', 'name' => 'Sold Out', 'stock' => 0]);

        Livewire::test(ProductFilter::class)
            ->set('category', 'electronics')
            ->set('inStockOnly', true)
            ->assertSee('Available')
            ->assertDontSee('Sold Out');
    }

    /**
     * Test searching products.
     *
     * @return void
     */
    public function test_can_search_products()
    {
        Product::factory()->create(['category' => 'electronics', 'name' => 'Wireless Mouse', 'description' => 'Great']);
        Product::factory()->create(['category' => 'electronics', 'name' => 'Keyboard', 'description' => 'Wireless']);
        Product::factory()->create(['category' => 'electronics', 'name' => 'Monitor', 'description' => 'Display']);

        Livewire::test(ProductFilter::class)
            ->set('category', 'electronics')
            ->set('search', 'wireless')
            ->assertSee('Wireless Mouse')
            ->assertSee('Keyboard')
            ->assertDontSee('Monitor');
    }

    /**
     * Test resetting filters.
     *
     * @return void
     */
    public function test_can_reset_filters()
    {
        Livewire::test(ProductFilter::class)
            ->set('category', 'electronics')
            ->set('minPrice', 50)
            ->set('maxPrice', 200)
            ->set('inStockOnly', true)
            ->set('search', 'test')
            ->call('resetFilters')
            ->assertSet('category', '')
            ->assertSet('minPrice', null)
            ->assertSet('maxPrice', null)
            ->assertSet('inStockOnly', false)
            ->assertSet('search', '');
    }

    /**
     * Test adding product to cart.
     *
     * @return void
     */
    public function test_can_add_product_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user);

        Livewire::test(ProductFilter::class)
            ->set("quantities.{$product->id}", 2)
            ->call('addToCart', $product->id)
            ->assertDispatchedBrowserEvent('show-toast');

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }

    /**
     * Test cannot add product to cart without authentication.
     *
     * @return void
     */
    public function test_cannot_add_to_cart_without_auth()
    {
        $product = Product::factory()->create(['stock' => 10]);

        Livewire::test(ProductFilter::class)
            ->call('addToCart', $product->id)
            ->assertDispatchedBrowserEvent('show-toast');

        $this->assertDatabaseCount('carts', 0);
    }

    /**
     * Test cart summary updates after adding item.
     *
     * @return void
     */
    public function test_cart_summary_updates()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

        $this->actingAs($user);

        Livewire::test(ProductFilter::class)
            ->assertSet('cartCount', 0)
            ->assertSet('cartTotal', 0)
            ->set("quantities.{$product->id}", 2)
            ->call('addToCart', $product->id)
            ->assertSet('cartCount', 2)
            ->assertSet('cartTotal', 200);
    }

    /**
     * Test pagination resets when filters change.
     *
     * @return void
     */
    public function test_pagination_resets_on_filter_change()
    {
        // Create 20 products to trigger pagination
        Product::factory()->count(20)->create(['category' => 'electronics']);

        Livewire::test(ProductFilter::class)
            ->set('category', 'electronics')
            ->call('nextPage') // Go to page 2
            ->assertSet('page', 2)
            ->set('search', 'test') // Change filter
            ->assertSet('page', 1); // Should reset to page 1
    }

    /**
     * Test categories property returns unique categories.
     *
     * @return void
     */
    public function test_categories_property_returns_unique_categories()
    {
        Product::factory()->create(['category' => 'electronics']);
        Product::factory()->create(['category' => 'electronics']);
        Product::factory()->create(['category' => 'clothing']);
        Product::factory()->create(['category' => 'books']);

        $component = Livewire::test(ProductFilter::class);
        $categories = $component->categories;

        $this->assertCount(3, $categories);
        $this->assertContains('electronics', $categories);
        $this->assertContains('clothing', $categories);
        $this->assertContains('books', $categories);
    }

    /**
     * Test empty state when no products match filters.
     *
     * @return void
     */
    public function test_shows_empty_state_when_no_products_match()
    {
        Product::factory()->create(['category' => 'electronics', 'name' => 'Laptop']);

        Livewire::test(ProductFilter::class)
            ->set('category', 'electronics')
            ->set('search', 'nonexistent')
            ->assertSee('No products found');
    }

    /**
     * Test mobile filters toggle.
     *
     * @return void
     */
    public function test_can_toggle_mobile_filters()
    {
        Livewire::test(ProductFilter::class)
            ->assertSet('showMobileFilters', false)
            ->call('toggleMobileFilters')
            ->assertSet('showMobileFilters', true)
            ->call('toggleMobileFilters')
            ->assertSet('showMobileFilters', false);
    }
}
