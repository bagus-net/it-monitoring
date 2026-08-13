<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitoringLog extends Model
{
    protected $fillable = [
        'site_id',
        'status_code',
        'response_time_ms',
        'status',
        'message',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
