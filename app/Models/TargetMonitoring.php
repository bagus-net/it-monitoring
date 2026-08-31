<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetMonitoring extends Model
{
    use HasFactory;

    protected $fillable = ['year', 'month', 'metric_key', 'value', 'notes', 'updated_by_user_id'];

    protected $casts = ['year' => 'integer', 'month' => 'integer', 'value' => 'integer'];
}