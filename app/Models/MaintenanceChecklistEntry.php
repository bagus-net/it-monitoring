<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceChecklistEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_checklist_id',
        'equipment_id',
        'result',
        'remarks',
    ];

    public function maintenanceChecklist()
    {
        return $this->belongsTo(MaintenanceChecklist::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
