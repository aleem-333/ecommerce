<div class="bg-gray-50" x-data="{
    showMobileMenu: @entangle('showMobileFilters')
}" @load-filter-preferences.window="
    const filters = localStorage.getItem('productFilters');
    if (filters) {
        const parsed = JSON.parse(filters);
        @this.set('category', parsed.category || '');
        @this.set('minPrice', parsed.minPrice || null);
        @this.set('maxPrice', parsed.maxPrice || null);
        @this.set('inStockOnly', parsed.inStockOnly || false);
        @this.set('search', parsed.search || '');
        @this.set('sortBy', parsed.sortBy || 'newest');
    }
" @save-filter-preferences.window="
    localStorage.setItem('productFilters', JSON.stringify($event.detail.filters));
">
    {{-- Mobile Filter Overlay --}}
    <div x-show="showMobileMenu"
         @click="showMobileMenu = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
         style="display: none;">
    </div>

    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Mobile Filter Button --}}
            <button @click="showMobileMenu = true"
                    class="lg:hidden btn-primary flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <span>Filters</span>
            </button>

            {{-- Sidebar Filters (Desktop) / Slide-over (Mobile) --}}
            <aside class="hidden lg:block lg:w-64 flex-shrink-0"
                   :class="{'!block fixed inset-y-0 left-0 z-50 w-full transform transition-transform duration-300 ease-in-out translate-x-0': showMobileMenu}">
                <div class="bg-white rounded-lg shadow-md p-4 lg:sticky lg:top-20" :class="{'h-screen overflow-y-auto': showMobileMenu}">
                    {{-- Mobile Close Button --}}
                    <div class="lg:hidden flex justify-between items-center mb-3">
                        <h2 class="text-lg font-bold text-gray-900">Filters</h2>
                        <button @click="showMobileMenu = false" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <h2 class="text-base font-semibold text-gray-900 mb-3 hidden lg:block">Filters</h2>

                    {{-- Search --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text"
                               wire:model.debounce.500ms="search"
                               placeholder="Search products..."
                               class="input-field">
                    </div>

                    {{-- Category Filter --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select wire:model="category" class="input-field">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Price Range Filter --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Min Price</label>
                                <input type="number"
                                       wire:model.lazy="minPrice"
                                       placeholder="$0"
                                       min="0"
                                       step="0.01"
                                       class="input-field">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Max Price</label>
                                <input type="number"
                                       wire:model.lazy="maxPrice"
                                       placeholder="Any"
                                       min="0"
                                       step="0.01"
                                       class="input-field">
                            </div>
                        </div>
                    </div>

                    {{-- In Stock Only Toggle --}}
                    <div class="mb-4">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox"
                                   wire:model="inStockOnly"
                                   class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm font-medium text-gray-700">In Stock Only</span>
                        </label>
                    </div>

                    {{-- Reset Filters Button --}}
                    <button wire:click="resetFilters"
                            class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Reset Filters
                    </button>
                </div>
            </aside>

            {{-- Main Content Area --}}
            <main class="flex-1">
                {{-- Sort Bar - Above Product Grid --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                    <div>
                        <span class="text-lg font-semibold text-gray-900">{{ $products->total() }}</span>
                        <span class="text-sm text-gray-600 ml-1">Products</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600 hidden sm:inline">Sort:</span>
                        <select wire:model="sortBy" class="text-sm px-3 py-1.5 border border-gray-300 rounded-md bg-white hover:border-primary-500 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-colors">
                            <option value="newest">Newest First</option>
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                            <option value="name">Name: A to Z</option>
                        </select>
                    </div>
                </div>

                {{-- Skeleton Loading - Show while filtering --}}
                <div wire:loading.delay wire:target="category,minPrice,maxPrice,inStockOnly,search,sortBy,resetFilters">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        @for($i = 0; $i < 6; $i++)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden animate-pulse">
                            {{-- Skeleton Image --}}
                            <div class="bg-gray-300 h-64"></div>

                            {{-- Skeleton Content --}}
                            <div class="p-4 space-y-3">
                                {{-- Skeleton Category Badge --}}
                                <div class="h-6 bg-gray-200 rounded-full w-20"></div>

                                {{-- Skeleton Title --}}
                                <div class="space-y-2">
                                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                                </div>

                                {{-- Skeleton Description --}}
                                <div class="space-y-2">
                                    <div class="h-3 bg-gray-200 rounded"></div>
                                    <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                                </div>

                                {{-- Skeleton Price --}}
                                <div class="h-8 bg-gray-200 rounded w-24"></div>

                                {{-- Skeleton Button --}}
                                <div class="h-10 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

                {{-- Product Grid --}}
                <div wire:loading.remove wire:target="category,minPrice,maxPrice,inStockOnly,search,sortBy,resetFilters">
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        @foreach($products as $product)
                            <div class="product-card group cursor-pointer transform transition-all duration-300 ease-in-out hover:-translate-y-2 hover:shadow-2xl flex flex-col h-full"
                                 wire:key="product-{{ $product->id }}">
                                {{-- Product Image --}}
                                <div class="relative bg-gray-200 h-64 overflow-hidden flex-shrink-0">
                                    @if($product->image)
                                        <img src="{{ asset($product->image) }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-110">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400 transition-colors group-hover:text-gray-500">
                                            <svg class="w-16 h-16 transform transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif

                                    {{-- Overlay on Hover --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                    {{-- Stock Status Badge --}}
                                    <div class="absolute top-3 right-3 transform transition-all duration-300 group-hover:scale-110">
                                        @if($product->stock <= 0)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500 text-white shadow-lg">
                                                Out of Stock
                                            </span>
                                        @elseif($product->stock <= 10)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-500 text-white shadow-lg">
                                                Low Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500 text-white shadow-lg">
                                                In Stock
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Product Details --}}
                                <div class="p-4 flex flex-col flex-grow">
                                    <div class="mb-2">
                                        <span class="inline-block px-3 py-1 text-xs font-bold text-primary-700 bg-primary-100 rounded-full transform transition-all duration-300 group-hover:bg-primary-600 group-hover:text-white group-hover:scale-105">
                                            {{ ucfirst($product->category) }}
                                        </span>
                                    </div>

                                    <h3 class="text-base font-bold text-gray-900 mb-2 h-12 line-clamp-2 group-hover:text-primary-600 transition-colors duration-300">
                                        {{ $product->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-3 h-10 line-clamp-2 group-hover:text-gray-700 transition-colors duration-300">
                                        {{ $product->description }}
                                    </p>

                                    <div class="flex items-center justify-between mb-3 mt-auto">
                                        <span class="text-2xl font-bold text-primary-600 transform transition-all duration-300 group-hover:scale-110 group-hover:text-primary-700">
                                            ${{ number_format($product->price, 2) }}
                                        </span>
                                        <span class="text-xs text-gray-500 font-medium bg-gray-100 px-2 py-1 rounded">{{ $product->sku }}</span>
                                    </div>

                                    {{-- Add to Cart Section --}}
                                    @auth
                                        <div class="space-y-2">
                                            <button wire:click="addToCart({{ $product->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="addToCart({{ $product->id }})"
                                                    @if($product->stock <= 0) disabled @endif
                                                    class="w-full btn-primary text-sm py-2.5 flex items-center justify-center space-x-2 transform transition-all duration-300 hover:scale-105 active:scale-95 shadow-md hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                                <svg wire:loading.remove wire:target="addToCart({{ $product->id }})" class="w-5 h-5 transform transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                <svg wire:loading wire:target="addToCart({{ $product->id }})" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <span wire:loading.remove wire:target="addToCart({{ $product->id }})" class="font-semibold">{{ $product->stock <= 0 ? 'Out of Stock' : 'Add to Cart' }}</span>
                                                <span wire:loading wire:target="addToCart({{ $product->id }})" class="font-semibold">Adding...</span>
                                            </button>
                                        </div>
                                    @else
                                        <div class="text-center py-2 bg-gray-100 rounded-lg group-hover:bg-gray-200 transition-colors duration-300">
                                            <p class="text-sm text-gray-600 font-medium">Please login to add to cart</p>
                                        </div>
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="bg-white rounded-lg shadow-md p-12 text-center">
                        <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
                        <p class="text-gray-600 mb-4">Try adjusting your filters or search criteria</p>
                        <button wire:click="resetFilters" class="btn-primary">
                            Reset Filters
                        </button>
                    </div>
                @endif
                </div>
            </main>
        </div>
    </div>
</div>
