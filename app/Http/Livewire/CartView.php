<?php

namespace App\Http\Livewire;

use App\Models\Cart;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CartView extends Component
{
    public $cartItems = [];
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        if (Auth::check()) {
            $this->cartItems = Cart::with('product')
                ->where('user_id', Auth::id())
                ->get()
                ->toArray();

            $this->calculateTotals();
        }
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        if (!Auth::check()) {
            $this->dispatchBrowserEvent('show-toast', [
                'message' => 'Please login to update cart',
                'type' => 'error'
            ]);
            return;
        }

        $quantity = max(1, intval($quantity));

        $cartItem = Cart::where('id', $cartItemId)
            ->where('user_id', Auth::id())
            ->with('product')
            ->first();

        if (!$cartItem) {
            $this->dispatchBrowserEvent('show-toast', [
                'message' => 'Cart item not found',
                'type' => 'error'
            ]);
            return;
        }

        if ($quantity > $cartItem->product->stock) {
            $this->dispatchBrowserEvent('show-toast', [
                'message' => "Only {$cartItem->product->stock} items available in stock",
                'type' => 'warning'
            ]);
            return;
        }

        $cartItem->quantity = $quantity;
        $cartItem->save();

        $this->loadCart();
        $this->emit('cartUpdated');

        $this->dispatchBrowserEvent('show-toast', [
            'message' => 'Cart updated successfully',
            'type' => 'success'
        ]);
    }

    public function removeItem($cartItemId)
    {
        if (!Auth::check()) {
            $this->dispatchBrowserEvent('show-toast', [
                'message' => 'Please login to remove items',
                'type' => 'error'
            ]);
            return;
        }

        $cartItem = Cart::where('id', $cartItemId)
            ->where('user_id', Auth::id())
            ->first();

        if ($cartItem) {
            $productName = $cartItem->product->name;
            $cartItem->delete();

            $this->loadCart();
            $this->emit('cartUpdated');

            $this->dispatchBrowserEvent('show-toast', [
                'message' => "{$productName} removed from cart",
                'type' => 'success'
            ]);
        }
    }

    public function clearCart()
    {
        if (!Auth::check()) {
            return;
        }

        Cart::where('user_id', Auth::id())->delete();

        $this->loadCart();
        $this->emit('cartUpdated');

        $this->dispatchBrowserEvent('show-toast', [
            'message' => 'Cart cleared successfully',
            'type' => 'success'
        ]);
    }

    private function calculateTotals()
    {
        $this->subtotal = 0;

        foreach ($this->cartItems as $item) {
            $this->subtotal += $item['product']['price'] * $item['quantity'];
        }

        $this->tax = $this->subtotal * 0.10; // 10% tax
        $this->total = $this->subtotal + $this->tax;
    }

    public function render()
    {
        return view('livewire.cart-view')->layout('layouts.shop');
    }
}
