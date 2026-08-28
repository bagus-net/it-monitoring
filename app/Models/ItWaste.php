<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItWaste extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'waste_code',
        'waste_date',
        'waste_type',
        'description',
        'quantity',
        'unit',
        'equipment_id',
        'it_waste_batch_id',
        'box_code',
        'collection_status',
        'storage_location',
        'handling_method',
        'handover_recipient',
        'handed_over_at',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'waste_date' => 'date',
        'handed_over_at' => 'date',
        'quantity' => 'decimal:2',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function batch()
    {
        return $this->belongsTo(ItWasteBatch::class, 'it_waste_batch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
