<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CctvConnection extends Model
{
    use HasFactory;

    protected $fillable = ['recorder_id', 'camera_id', 'channel', 'status', 'notes'];

    public function recorder() { return $this->belongsTo(Cctv::class, 'recorder_id'); }
    public function camera() { return $this->belongsTo(Cctv::class, 'camera_id'); }
}
