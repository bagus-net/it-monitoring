<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkNode extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'zone', 'ip_address', 'vendor', 'status', 'management_url', 'notes'];

    public function outgoingLinks() { return $this->hasMany(NetworkLink::class, 'source_node_id'); }
    public function incomingLinks() { return $this->hasMany(NetworkLink::class, 'target_node_id'); }
}
