<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItRepairTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'equipment_id',
        'equipment_category',
        'error_type',
        'error_photo_path',
        'department',
        'reported_at',
        'problem_description',
        'repair_action',
        'repair_attachment_path',
        'priority',
        'status',
        'reported_by',
        'assigned_to',
        'started_at',
        'resolved_at',
        'notes',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
