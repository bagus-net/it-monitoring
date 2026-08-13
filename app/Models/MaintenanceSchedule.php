<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['equipment_id','frequency','day_of_month','month','week_of_month','year','checklist_item_id','assigned_to','notes'];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class);
    }
}
