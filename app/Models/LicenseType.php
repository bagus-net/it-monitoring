<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseType extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'category', 'vendor', 'license_key', 'total_seats', 'used_seats', 'start_date', 'expiry_date', 'cost', 'status', 'notes'];

    protected $casts = ['total_seats' => 'integer', 'used_seats' => 'integer', 'start_date' => 'date', 'expiry_date' => 'date', 'cost' => 'decimal:2'];

    public function transactions() { return $this->hasMany(LicenseTransaction::class); }
    public function getAvailableSeatsAttribute(): int { return max(0, $this->total_seats - $this->used_seats); }
    public function getIsExpiringAttribute(): bool { return $this->expiry_date && $this->expiry_date->lte(now()->addDays(30)); }
}
