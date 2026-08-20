<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparepartTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['sparepart_type_id', 'equipment_id', 'type', 'quantity', 'stock_before', 'stock_after', 'transaction_date', 'reference', 'recipient', 'notes', 'created_by'];

    protected $casts = ['transaction_date' => 'date', 'quantity' => 'integer', 'stock_before' => 'integer', 'stock_after' => 'integer'];

    public function sparepartType() { return $this->belongsTo(SparepartType::class); }
    public function equipment() { return $this->belongsTo(Equipment::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
