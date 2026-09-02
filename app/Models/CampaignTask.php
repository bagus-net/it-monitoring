<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignTask extends Model
{
    use HasFactory;

    protected $fillable = ['campaign_id', 'title', 'description', 'status', 'priority', 'assignee_id', 'due_date', 'sort_order'];

    protected $casts = ['due_date' => 'date'];

    public function campaign() { return $this->belongsTo(Campaign::class); }
    public function assignee() { return $this->belongsTo(User::class, 'assignee_id'); }
}
