<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id', 'swap_group', 'from_user_id', 'to_user_id', 'from_owner_name', 'to_owner_name',
        'from_department', 'to_department', 'from_location_id', 'to_location_id', 'reason',
        'effective_date', 'status', 'requested_by', 'approved_by', 'approved_at',
        'completed_by', 'completed_at', 'notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function equipment() { return $this->belongsTo(Equipment::class); }
    public function fromUser() { return $this->belongsTo(User::class, 'from_user_id'); }
    public function toUser() { return $this->belongsTo(User::class, 'to_user_id'); }
    public function fromLocation() { return $this->belongsTo(Location::class, 'from_location_id'); }
    public function toLocation() { return $this->belongsTo(Location::class, 'to_location_id'); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function completer() { return $this->belongsTo(User::class, 'completed_by'); }
}
