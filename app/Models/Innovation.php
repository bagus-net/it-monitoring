<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Innovation extends Model
{
    use HasFactory;

    protected $fillable = [
        'innovation_date',
        'title',
        'implementation',
        'implementation_date',
        'notes',
        'paper_path',
        'paper_name',
        'created_by_user_id',
    ];

    protected $casts = [
        'innovation_date' => 'date',
        'implementation_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
