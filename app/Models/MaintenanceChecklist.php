<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceChecklist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'checklist_item_id',
        'year',
        'month',
        'checked_at',
        'reported_by',
        'reported_by_user_id',
        'acknowledged_by',
        'acknowledged_by_user_id',
        'acknowledged_at',
        'notes',
    ];

    protected $casts = [
        'checked_at' => 'date',
        'acknowledged_at' => 'datetime',
    ];

    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    public function entries()
    {
        return $this->hasMany(MaintenanceChecklistEntry::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    public function acknowledger()
    {
        return $this->belongsTo(User::class, 'acknowledged_by_user_id');
    }
}
