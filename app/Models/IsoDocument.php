<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IsoDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_number',
        'title',
        'category',
        'revision',
        'document_date',
        'description',
        'file_path',
        'file_name',
        'created_by_user_id',
    ];

    protected $casts = ['document_date' => 'date'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function permittedUsers()
    {
        return $this->belongsToMany(User::class, 'iso_document_user')->withTimestamps();
    }
}
