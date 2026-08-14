<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = ['equipment_id','checklist_item_id','performed_at','performed_by','result','remarks'];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class, 'checklist_item_id');
    }
}
