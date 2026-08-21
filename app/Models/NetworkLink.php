<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkLink extends Model
{
    use HasFactory;

    protected $fillable = ['source_node_id', 'target_node_id', 'link_type', 'bandwidth', 'status', 'notes'];

    public function source() { return $this->belongsTo(NetworkNode::class, 'source_node_id'); }
    public function target() { return $this->belongsTo(NetworkNode::class, 'target_node_id'); }
}
