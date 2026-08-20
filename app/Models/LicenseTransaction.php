<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['license_type_id', 'type', 'quantity', 'seats_before', 'seats_after', 'transaction_date', 'equipment_id', 'user_id', 'reference', 'notes', 'created_by'];
    protected $casts = ['transaction_date' => 'date', 'quantity' => 'integer', 'seats_before' => 'integer', 'seats_after' => 'integer'];

    public function licenseType() { return $this->belongsTo(LicenseType::class); }
    public function equipment() { return $this->belongsTo(Equipment::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
