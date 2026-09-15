<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentQuarantine extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'quarantined_by_user_id',
        'quarantined_at',
        'outcome',
        'reason',
        'notes',
        'released_at',
    ];

    protected $casts = [
        'quarantined_at' => 'date',
        'released_at' => 'date',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function quarantinedBy()
    {
        return $this->belongsTo(User::class, 'quarantined_by_user_id');
    }
}
