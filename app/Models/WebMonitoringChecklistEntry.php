<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebMonitoringChecklistEntry extends Model
{
    use HasFactory;

    protected $fillable = ['web_monitoring_checklist_id', 'item_code', 'item_name', 'result', 'remarks'];

    public function checklist()
    {
        return $this->belongsTo(WebMonitoringChecklist::class, 'web_monitoring_checklist_id');
    }
}
