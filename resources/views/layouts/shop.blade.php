<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'eCommerce - ShopZone')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50">

    <!-- Modern Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50"
         x-data="{
             mobileMenuOpen: false,
             userMenuOpen: false,
             @if(Auth::check())
             cartCount: {{ \App\Models\Cart::where('user_id', Auth::id())->sum('quantity') }}
             @else
             cartCount: 0
             @endif
         }"
         @cart-updated.window="cartCount = $event.detail.count">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-8">
                    <a href="{{ route('products.index') }}" class="flex items-center space-x-2">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span class="text-2xl font-bold text-primary-600">ShopZone</span>
                    </a>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex space-x-1">
                        <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-lg @if(Request::is('/') || Request::is('products*')) bg-primary-50 text-primary-600 @else text-gray-700 hover:bg-primary-50 hover:text-primary-600 @endif font-medium transition-colors duration-200 flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span>Products</span>
                        </a>
                        @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium transition-colors duration-200 flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                        @endauth
                    </div>
                </div>

                <!-- Right Side Icons -->
                <div class="flex items-center space-x-4">
                    <!-- Cart Icon with Badge -->
                    @auth
                        <a href="{{ route('cart.index') }}" class="relative p-2 rounded-lg @if(Request::is('cart*')) bg-primary-50 @else hover:bg-primary-50 @endif transition-colors duration-200 group">
                            <svg class="w-6 h-6 @if(Request::is('cart*')) text-primary-600 @else text-gray-700 group-hover:text-primary-600 @endif transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span x-show="cartCount > 0"
                                  x-text="cartCount"
                                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center ring-2 ring-white">
                            </span>
                        </a>
                    @endauth

                    <!-- User Menu (Desktop) -->
                    @auth
                        <div class="hidden md:block relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-primary-50 transition-colors duration-200">
                                <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 border border-gray-200"
                                 style="display: none;">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                    Dashboard
                                </a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                    Profile Settings
                                </a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition-colors">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest Links -->
                        <div class="hidden md:flex items-center space-x-2">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 hover:text-primary-600 font-medium transition-colors">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-md hover:shadow-lg">
                                Sign Up
                            </a>
                        </div>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden pb-4"
                 style="display: none;">
                <div class="space-y-1">
                    <a href="{{ route('products.index') }}" class="block px-4 py-3 @if(Request::is('/') || Request::is('products*')) bg-primary-50 text-primary-600 @else text-gray-700 hover:bg-primary-50 hover:text-primary-600 @endif font-medium rounded-lg transition-colors">
                        Products
                    </a>
                    @auth
                        <a href="{{ route('cart.index') }}" class="block px-4 py-3 @if(Request::is('cart*')) bg-primary-50 text-primary-600 @else text-gray-700 hover:bg-primary-50 hover:text-primary-600 @endif font-medium rounded-lg transition-colors">
                            Cart
                            <span x-show="cartCount > 0"
                                  x-text="cartCount"
                                  class="ml-2 bg-red-500 text-white text-xs font-bold rounded-full px-2 py-1 inline-block">
                            </span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="block px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium rounded-lg transition-colors">
                            Dashboard
                        </a>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium rounded-lg transition-colors">
                            Profile Settings
                        </a>
                        <hr class="my-2">
                        <div class="px-4 py-2 text-sm text-gray-500">
                            Signed in as <span class="font-semibold text-gray-700">{{ Auth::user()->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-3 text-red-600 hover:bg-red-50 font-medium rounded-lg transition-colors">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 font-medium rounded-lg transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="block mx-4 my-2 px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg text-center transition-colors">
                            Sign Up
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Toast Notification Component -->
    <div x-data="{
        show: false,
        message: '',
        type: 'success'
    }"
    x-on:show-toast.window="
        message = $event.detail.message;
        type = $event.detail.type;
        show = true;
        setTimeout(() => show = false, 3000);
    "
    x-show="show"
    x-cloak
    x-transition:enter="transform ease-out duration-300"
    x-transition:enter-start="translate-x-full opacity-0"
    x-transition:enter-end="translate-x-0 opacity-100"
    x-transition:leave="transform ease-in duration-200"
    x-transition:leave-start="translate-x-0 opacity-100"
    x-transition:leave-end="translate-x-full opacity-0"
    class="fixed top-20 right-4 z-[9999] max-w-sm">
        <div class="rounded-lg shadow-2xl p-4 border-2"
             :class="{
                 'bg-green-500 text-white border-green-600': type === 'success',
                 'bg-red-500 text-white border-red-600': type === 'error',
                 'bg-yellow-500 text-white border-yellow-600': type === 'warning',
                 'bg-blue-500 text-white border-blue-600': type === 'info'
             }">
            <div class="flex items-center space-x-3">
                <svg x-show="type === 'success'" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg x-show="type === 'error'" class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <p class="font-medium" x-text="message"></p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    {{ $slot }}

    @livewireScripts

    @stack('scripts')
</body>
</html>
