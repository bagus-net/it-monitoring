<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlySchedule extends Model
{
    use HasFactory;

    protected $fillable = ['checklist_item_id', 'equipment_id', 'month', 'year', 'dates', 'notes'];

    protected $casts = [
        'dates' => 'array',
    ];

    protected $attributes = [
        'dates' => '[]',
    ];

    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}

