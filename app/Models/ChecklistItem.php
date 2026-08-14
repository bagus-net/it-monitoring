<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    use HasFactory;

    protected $fillable = ['title','category','description','equipment_type_id','sort_order','frequency','applicable_months'];

    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class, 'equipment_type_id');
    }

    public function maintenanceSchedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function monthlySchedules()
    {
        return $this->hasMany(MonthlySchedule::class);
    }

    public function getScheduleColorAttribute()
    {
        $palette = ['#0f766e', '#2563eb', '#7c3aed', '#c2410c', '#be123c', '#4d7c0f', '#0369a1', '#a16207'];

        return $palette[($this->id ?: 1) % count($palette)];
    }

    public function getScheduleTintAttribute()
    {
        $palette = ['#ccfbf1', '#dbeafe', '#ede9fe', '#ffedd5', '#ffe4e6', '#ecfccb', '#e0f2fe', '#fef3c7'];

        return $palette[($this->id ?: 1) % count($palette)];
    }
}
