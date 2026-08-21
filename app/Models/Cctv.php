<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cctv extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'camera_type', 'brand', 'model', 'ip_address', 'web_url',
        'stream_url', 'username', 'password', 'network_zone_id', 'location_detail',
        'status', 'last_checked_at', 'notes',
    ];

    protected $hidden = ['password', 'stream_url'];
    protected $casts = ['password' => 'encrypted', 'stream_url' => 'encrypted', 'last_checked_at' => 'datetime'];

    public function networkZone()
    {
        return $this->belongsTo(NetworkZone::class);
    }

    public function recorderConnections()
    {
        return $this->hasMany(CctvConnection::class, 'recorder_id');
    }

    public function cameraConnections()
    {
        return $this->hasMany(CctvConnection::class, 'camera_id');
    }
}
