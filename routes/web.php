<?php

use App\Http\Controllers\ProfileController;
use App\Http\Livewire\CartView;
use App\Http\Livewire\ProductFilter;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', ProductFilter::class);

Route::get('/products', ProductFilter::class)->name('products.index');

Route::get('/cart', CartView::class)->name('cart.index');

Route::get('/dashboard', function () {
    return redirect()->route('cart.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
