<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Filter products based on various criteria.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function filterProducts(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'category' => 'required|string',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0|gte:min_price',
                'in_stock_only' => 'nullable|boolean',
                'search' => 'nullable|string|max:255',
                'page' => 'nullable|integer|min:1',
            ], [
                'category.required' => 'Category is required for filtering products.',
                'min_price.numeric' => 'Minimum price must be a valid number.',
                'max_price.gte' => 'Maximum price must be greater than or equal to minimum price.',
                'search.max' => 'Search term cannot exceed 255 characters.',
            ]);

            // Build the query with scopes
            $query = Product::query()
                ->byCategory($validated['category'])
                ->byPriceRange($validated['min_price'] ?? null, $validated['max_price'] ?? null)
                ->inStock($validated['in_stock_only'] ?? false)
                ->search($validated['search'] ?? null)
                ->orderBy('created_at', 'desc');

            // Paginate results (15 per page)
            $products = $query->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $products->items(),
                'meta' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ],
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while filtering products.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add a product to the cart.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addToCart(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ], [
                'product_id.required' => 'Product ID is required.',
                'product_id.exists' => 'The selected product does not exist.',
                'quantity.required' => 'Quantity is required.',
                'quantity.integer' => 'Quantity must be a valid integer.',
                'quantity.min' => 'Quantity must be at least 1.',
            ]);

            // Get the product
            $product = Product::findOrFail($validated['product_id']);

            // Check stock availability
            if ($product->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available.',
                    'available_stock' => $product->stock,
                ], 400);
            }

            // Check if out of stock
            if (!$product->isInStock()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is currently out of stock.',
                ], 400);
            }

            // Get authenticated user
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required to add items to cart.',
                ], 401);
            }

            // Check if item already exists in cart
            $cartItem = Cart::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                // Update existing cart item
                $newQuantity = $cartItem->quantity + $validated['quantity'];

                // Check if new quantity exceeds stock
                if ($newQuantity > $product->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Adding this quantity would exceed available stock.',
                        'available_stock' => $product->stock,
                        'current_cart_quantity' => $cartItem->quantity,
                    ], 400);
                }

                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                // Create new cart item
                $cartItem = Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => $validated['quantity'],
                ]);
            }

            // Load the product relationship
            $cartItem->load('product');

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully.',
                'data' => [
                    'id' => $cartItem->id,
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'image' => $product->image,
                    ],
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->quantity * $product->price,
                ],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding product to cart.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the quantity of a cart item.
     *
     * @param Request $request
     * @param int $cartItemId
     * @return JsonResponse
     */
    public function updateCartItem(Request $request, int $cartItemId): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'quantity' => 'required|integer|min:0',
            ], [
                'quantity.required' => 'Quantity is required.',
                'quantity.integer' => 'Quantity must be a valid integer.',
                'quantity.min' => 'Quantity must be at least 0.',
            ]);

            // Find the cart item
            $cartItem = Cart::find($cartItemId);

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found.',
                ], 404);
            }

            // Check authorization - user can only modify their own cart
            $user = Auth::user();
            if (!$user || $cartItem->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You can only modify your own cart items.',
                ], 403);
            }

            // If quantity is 0, delete the cart item
            if ($validated['quantity'] === 0) {
                $cartItem->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Cart item removed successfully.',
                ], 200);
            }

            // Load the product to check stock
            $product = $cartItem->product;

            // Check stock availability
            if ($validated['quantity'] > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requested quantity exceeds available stock.',
                    'available_stock' => $product->stock,
                    'requested_quantity' => $validated['quantity'],
                ], 400);
            }

            // Update the cart item quantity
            $cartItem->update(['quantity' => $validated['quantity']]);

            // Reload relationships
            $cartItem->load('product');

            return response()->json([
                'success' => true,
                'message' => 'Cart item updated successfully.',
                'data' => [
                    'id' => $cartItem->id,
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'image' => $product->image,
                    ],
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->quantity * $product->price,
                ],
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating cart item.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
