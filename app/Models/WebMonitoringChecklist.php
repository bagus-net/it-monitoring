<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebMonitoringChecklist extends Model
{
    use HasFactory;

    protected $fillable = ['site_id', 'checklist_type', 'checked_at', 'checked_by', 'notes'];

    protected $casts = ['checked_at' => 'datetime'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function entries()
    {
        return $this->hasMany(WebMonitoringChecklistEntry::class);
    }
}
