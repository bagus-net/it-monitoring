<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItWasteBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_code',
        'opened_at',
        'storage_location',
        'status',
        'handover_recipient',
        'handed_over_at',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'opened_at' => 'date',
        'handed_over_at' => 'date',
    ];

    public function wastes()
    {
        return $this->hasMany(ItWaste::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
