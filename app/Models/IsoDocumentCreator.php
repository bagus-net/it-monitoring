<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsoDocumentCreator extends Model
{
    use HasFactory;

    protected $table = 'iso_document_creators';

    protected $fillable = ['user_id'];
}
