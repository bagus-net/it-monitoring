<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IsoDocumentFile extends Model
{
    protected $fillable = [
        'iso_document_id',
        'file_path',
        'file_name',
        'file_size',
        'uploaded_by_user_id',
    ];

    public function isoDocument()
    {
        return $this->belongsTo(IsoDocument::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
