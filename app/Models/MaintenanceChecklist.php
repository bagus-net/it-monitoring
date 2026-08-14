<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_item_id',
        'year',
        'month',
        'checked_at',
        'reported_by',
        'acknowledged_by',
        'notes',
    ];

    protected $casts = [
        'checked_at' => 'date',
    ];

    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    public function entries()
    {
        return $this->hasMany(MaintenanceChecklistEntry::class);
    }
}
