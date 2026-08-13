<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'is_active',
        'check_interval_minutes',
        'last_status',
        'last_status_code',
        'last_response_time_ms',
        'last_checked_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(MonitoringLog::class)->latest();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Ambil N log terakhir, urut dari yang paling lama -> terbaru (cocok untuk grafik).
     */
    public function recentLogs(int $limit = 50)
    {
        return $this->logs()->limit($limit)->get()->reverse()->values();
    }

    public function uptimePercentage(int $limit = 50): ?float
    {
        $logs = $this->recentLogs($limit);
        if ($logs->isEmpty()) {
            return null;
        }
        $up = $logs->where('status', 'UP')->count();
        return round(($up / $logs->count()) * 100, 1);
    }
}
