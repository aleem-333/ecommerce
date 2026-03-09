<div class="bg-gray-50 min-h-screen" x-data="{
    showToast: false,
    toastMessage: '',
    toastType: 'success'
}" @show-toast.window="
    toastMessage = $event.detail.message;
    toastType = $event.detail.type;
    showToast = true;
    setTimeout(() => showToast = false, 3000);
">
    {{-- Toast Notification --}}
    <div x-show="showToast"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-full opacity-0"
         class="fixed top-4 right-4 z-50 max-w-sm"
         style="display: none;">
        <div class="rounded-lg shadow-lg p-4"
             :class="{
                 'bg-green-500 text-white': toastType === 'success',
                 'bg-red-500 text-white': toastType === 'error',
                 'bg-yellow-500 text-white': toastType === 'warning',
                 'bg-blue-500 text-white': toastType === 'info'
             }">
            <div class="flex items-center space-x-3">
                <svg x-show="toastType === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg x-show="toastType === 'error'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <p class="font-medium" x-text="toastMessage"></p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
            <a href="{{ route('products.index') }}" class="text-primary-600 hover:text-primary-700 font-medium flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Continue Shopping</span>
            </a>
        </div>

        @auth
            @if(count($cartItems) > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Cart Items --}}
                    <div class="lg:col-span-2 space-y-4">
                        @foreach($cartItems as $item)
                            <div class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow duration-300" wire:key="cart-{{ $item['id'] }}">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    {{-- Product Image --}}
                                    <div class="w-full sm:w-32 h-32 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($item['product']['image'])
                                            <img src="{{ asset($item['product']['image']) }}"
                                                 alt="{{ $item['product']['name'] }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Product Details --}}
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $item['product']['name'] }}</h3>
                                            <p class="text-sm text-gray-600 mb-2">{{ $item['product']['description'] }}</p>
                                            <span class="inline-block px-2 py-1 text-xs font-semibold text-primary-700 bg-primary-100 rounded">
                                                {{ ucfirst($item['product']['category']) }}
                                            </span>
                                        </div>

                                        <div class="mt-4 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3">
                                            {{-- Quantity Selector --}}
                                            <div class="flex items-center space-x-3">
                                                <label class="text-sm font-medium text-gray-700">Quantity:</label>
                                                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                                    <button wire:click="updateQuantity({{ $item['id'] }}, {{ max(1, $item['quantity'] - 1) }})"
                                                            class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition-colors">
                                                        -
                                                    </button>
                                                    <input type="number"
                                                           value="{{ $item['quantity'] }}"
                                                           wire:change="updateQuantity({{ $item['id'] }}, $event.target.value)"
                                                           min="1"
                                                           max="{{ $item['product']['stock'] }}"
                                                           class="w-16 text-center border-x border-gray-300 py-1 focus:outline-none">
                                                    <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})"
                                                            @if($item['quantity'] >= $item['product']['stock']) disabled @endif
                                                            class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                        +
                                                    </button>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $item['product']['stock'] }} available</span>
                                            </div>

                                            {{-- Price and Remove --}}
                                            <div class="flex items-center space-x-4">
                                                <div class="text-right">
                                                    <p class="text-sm text-gray-500">Price</p>
                                                    <p class="text-xl font-bold text-primary-600">${{ number_format($item['product']['price'] * $item['quantity'], 2) }}</p>
                                                </div>
                                                <button wire:click="removeItem({{ $item['id'] }})"
                                                        class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                        title="Remove from cart">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Clear Cart Button --}}
                        <div class="text-center pt-4">
                            <button wire:click="clearCart"
                                    onclick="return confirm('Are you sure you want to clear your cart?')"
                                    class="text-red-600 hover:text-red-700 font-medium flex items-center justify-center space-x-2 mx-auto">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span>Clear Cart</span>
                            </button>
                        </div>
                    </div>

                    {{-- Order Summary --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h2>

                            <div class="space-y-3 mb-4">
                                <div class="flex justify-between text-gray-700">
                                    <span>Subtotal ({{ count($cartItems) }} items)</span>
                                    <span class="font-semibold">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-700">
                                    <span>Tax (10%)</span>
                                    <span class="font-semibold">${{ number_format($tax, 2) }}</span>
                                </div>
                                <div class="border-t pt-3 flex justify-between text-lg font-bold text-gray-900">
                                    <span>Total</span>
                                    <span class="text-primary-600">${{ number_format($total, 2) }}</span>
                                </div>
                            </div>

                            <button class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <span>Proceed to Checkout</span>
                            </button>

                            <div class="mt-6 space-y-3">
                                <div class="flex items-start space-x-3 text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Free shipping on orders over $100</span>
                                </div>
                                <div class="flex items-start space-x-3 text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    <span>Secure checkout with SSL encryption</span>
                                </div>
                                <div class="flex items-start space-x-3 text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span>30-day easy returns</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Empty Cart --}}
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <svg class="mx-auto h-32 w-32 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">Your cart is empty</h2>
                    <p class="text-gray-600 mb-6">Start adding some products to your cart!</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center space-x-2 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span>Start Shopping</span>
                    </a>
                </div>
            @endif
        @else
            {{-- Not Logged In --}}
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="mx-auto h-32 w-32 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">Please log in to view your cart</h2>
                <p class="text-gray-600 mb-6">You need to be logged in to manage your shopping cart</p>
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('login') }}" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                        Log In
                    </a>
                    <a href="{{ route('register') }}" class="bg-white hover:bg-gray-50 text-primary-600 border-2 border-primary-600 font-bold py-3 px-6 rounded-lg transition duration-200">
                        Register
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>
