<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparepartType extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'category', 'brand', 'unit', 'minimum_stock', 'current_stock', 'notes'];

    protected $casts = ['minimum_stock' => 'integer', 'current_stock' => 'integer'];

    public function transactions()
    {
        return $this->hasMany(SparepartTransaction::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}
