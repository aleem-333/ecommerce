<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'stock',
        'sku',
        'image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Scope to filter products by category.
     *
     * @param Builder $query
     * @param string $category
     * @return Builder
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter products by price range.
     *
     * @param Builder $query
     * @param float|null $min
     * @param float|null $max
     * @return Builder
     */
    public function scopeByPriceRange(Builder $query, ?float $min = null, ?float $max = null): Builder
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }

        if ($max !== null) {
            $query->where('price', '<=', $max);
        }

        return $query;
    }

    /**
     * Scope to filter products that are in stock.
     *
     * @param Builder $query
     * @param bool $inStockOnly
     * @return Builder
     */
    public function scopeInStock(Builder $query, bool $inStockOnly = true): Builder
    {
        if ($inStockOnly) {
            return $query->where('stock', '>', 0);
        }

        return $query;
    }

    /**
     * Scope to search products by name or description.
     *
     * @param Builder $query
     * @param string|null $term
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $term = null): Builder
    {
        if ($term) {
            return $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        return $query;
    }

    /**
     * Check if the product is in stock.
     *
     * @return bool
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Check if the product has low stock (less than or equal to 10).
     *
     * @return bool
     */
    public function hasLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= 10;
    }

    /**
     * Get the stock status label.
     *
     * @return string
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'Out of Stock';
        } elseif ($this->stock <= 10) {
            return 'Low Stock';
        } else {
            return 'In Stock';
        }
    }

    /**
     * Get the stock status color.
     *
     * @return string
     */
    public function getStockColorAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'red';
        } elseif ($this->stock <= 10) {
            return 'orange';
        } else {
            return 'green';
        }
    }
}
