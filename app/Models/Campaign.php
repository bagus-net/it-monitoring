<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'objective', 'channel', 'audience', 'status', 'owner_user_id',
        'start_date', 'end_date', 'budget', 'target_value', 'achieved_value', 'target_unit',
        'description', 'notes', 'created_by_user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'target_value' => 'decimal:2',
        'achieved_value' => 'decimal:2',
    ];

    public function owner() { return $this->belongsTo(User::class, 'owner_user_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by_user_id'); }
    public function tasks() { return $this->hasMany(CampaignTask::class)->orderBy('sort_order')->orderBy('due_date'); }

    public function getProgressAttribute(): float
    {
        if (!$this->target_value || (float) $this->target_value <= 0) return 0;
        return min(100, round(((float) $this->achieved_value / (float) $this->target_value) * 100, 1));
    }
}
