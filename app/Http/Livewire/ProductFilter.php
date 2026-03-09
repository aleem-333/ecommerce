<?php

namespace App\Http\Livewire;

use App\Models\Product;
use App\Models\Cart;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ProductFilter extends Component
{
    use WithPagination;

    // Filter properties
    public $category = '';
    public $minPrice = null;
    public $maxPrice = null;
    public $inStockOnly = false;
    public $search = '';
    public $sortBy = 'newest'; // newest, price_low, price_high, name

    // UI state
    public $showMobileFilters = false;
    public $selectedProduct = null;
    public $quantities = [];

    // Cart state
    public $cartCount = 0;
    public $cartTotal = 0;

    // Loading state
    public $isLoading = false;

    /**
     * Query string parameters for URL persistence.
     *
     * @var array
     */
    protected $queryString = [
        'category' => ['except' => ''],
        'minPrice' => ['except' => null],
        'maxPrice' => ['except' => null],
        'inStockOnly' => ['except' => false],
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'newest'],
    ];

    /**
     * Event listeners.
     *
     * @var array
     */
    protected $listeners = ['cartUpdated' => 'updateCartSummary'];

    /**
     * Validation rules.
     *
     * @var array
     */
    protected $rules = [
        'category' => 'required|string',
        'minPrice' => 'nullable|numeric|min:0',
        'maxPrice' => 'nullable|numeric|min:0',
        'search' => 'nullable|string|max:255',
    ];

    /**
     * Mount the component and initialize state.
     *
     * @return void
     */
    public function mount(): void
    {
        // Load filter preferences from localStorage on mount
        $this->dispatchBrowserEvent('loadFilterPreferences');
        $this->updateCartSummary();
    }

    /**
     * Updated hook - called when properties are updated.
     *
     * @param string $propertyName
     * @return void
     */
    public function updated($propertyName): void
    {
        // Reset pagination when filters change
        if (in_array($propertyName, ['category', 'minPrice', 'maxPrice', 'inStockOnly', 'search', 'sortBy'])) {
            $this->resetPage();

            // Save filter preferences to localStorage
            $this->dispatchBrowserEvent('saveFilterPreferences', [
                'filters' => [
                    'category' => $this->category,
                    'minPrice' => $this->minPrice,
                    'maxPrice' => $this->maxPrice,
                    'inStockOnly' => $this->inStockOnly,
                    'search' => $this->search,
                    'sortBy' => $this->sortBy,
                ]
            ]);
        }
    }

    /**
     * Get all available categories from the database.
     *
     * @return array
     */
    public function getCategoriesProperty(): array
    {
        return Product::distinct()->pluck('category')->sort()->values()->toArray();
    }

    /**
     * Apply filters and get filtered products.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getProductsProperty()
    {
        $query = Product::query();

        // Apply category filter
        if ($this->category) {
            $query->byCategory($this->category);
        }

        // Apply price range filter
        if ($this->minPrice !== null || $this->maxPrice !== null) {
            $query->byPriceRange($this->minPrice, $this->maxPrice);
        }

        // Apply stock filter
        if ($this->inStockOnly) {
            $query->inStock(true);
        }

        // Apply search filter
        if ($this->search) {
            $query->search($this->search);
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate(15);
    }

    /**
     * Add a product to the cart.
     *
     * @param int $productId
     * @return void
     */
    public function addToCart(int $productId): void
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'Please login to add items to cart.'
            ]);
            return;
        }

        // Get quantity (default to 1 if not set)
        $quantity = $this->quantities[$productId] ?? 1;

        // Validate quantity
        if ($quantity < 1) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'Quantity must be at least 1.'
            ]);
            return;
        }

        // Get the product
        $product = Product::find($productId);

        if (!$product) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'Product not found.'
            ]);
            return;
        }

        // Check stock availability
        if ($product->stock < $quantity) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => "Only {$product->stock} items available in stock."
            ]);
            return;
        }

        // Check if item already exists in cart
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;

            // Check if new quantity exceeds stock
            if ($newQuantity > $product->stock) {
                $this->dispatchBrowserEvent('show-toast', [
                    'type' => 'error',
                    'message' => 'Adding this quantity would exceed available stock.'
                ]);
                return;
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        // Reset quantity input
        $this->quantities[$productId] = 1;

        // Update cart summary
        $this->updateCartSummary();

        // Show success message
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => "{$product->name} added to cart successfully!"
        ]);

        // Dispatch cart updated event with count
        $this->dispatchBrowserEvent('cart-updated', [
            'count' => $this->cartCount
        ]);

        $this->emit('cartUpdated');
    }

    /**
     * Update cart summary (count and total).
     *
     * @return void
     */
    public function updateCartSummary(): void
    {
        if (Auth::check()) {
            $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
            $this->cartCount = $cartItems->sum('quantity');
            $this->cartTotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });
        } else {
            $this->cartCount = 0;
            $this->cartTotal = 0;
        }
    }

    /**
     * Toggle mobile filters visibility.
     *
     * @return void
     */
    public function toggleMobileFilters(): void
    {
        $this->showMobileFilters = !$this->showMobileFilters;
    }

    /**
     * Reset all filters to default values.
     *
     * @return void
     */
    public function resetFilters(): void
    {
        $this->category = '';
        $this->minPrice = null;
        $this->maxPrice = null;
        $this->inStockOnly = false;
        $this->search = '';
        $this->sortBy = 'newest';
        $this->resetPage();

        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Filters reset successfully.'
        ]);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.product-filter', [
            'products' => $this->products,
            'categories' => $this->categories,
        ])->layout('layouts.shop');
    }
}
