<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';

    protected $fillable = ['name','asset_tag','photo_path','serial_number','model','operating_system','equipment_type_id','manufacturer_id','vendor_name','location_id','owner_name','department','capacity','specification','purchase_date','manufacture_year','warranty_expiry','support_contract_end','ip_address','status','condition','criticality','notes'];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'support_contract_end' => 'date',
    ];

    public function type()
    {
        return $this->belongsTo(EquipmentType::class, 'equipment_type_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function assetLocation()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function maintenanceSchedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function schedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function monthlySchedules()
    {
        return $this->hasMany(MonthlySchedule::class);
    }

    public function logs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function maintenanceChecklistEntries()
    {
        return $this->hasMany(MaintenanceChecklistEntry::class);
    }
}
