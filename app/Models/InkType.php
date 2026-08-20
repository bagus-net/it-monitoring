<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InkType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'brand', 'color', 'unit', 'minimum_stock', 'current_stock', 'notes'];

    protected $casts = ['minimum_stock' => 'integer', 'current_stock' => 'integer'];

    public function transactions()
    {
        return $this->hasMany(InkTransaction::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}
