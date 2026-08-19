<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItRepairTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'equipment_id',
        'repair_category',
        'software_name',
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
        'technician_id',
        'approved_by',
        'approved_at',
        'started_at',
        'resolved_at',
        'notes',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
