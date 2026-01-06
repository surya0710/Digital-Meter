<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MqttResponse extends Model
{
    protected $fillable = ['device_id', 'topic', 'message', 'received_at'];
    
    protected $casts = [
        'message' => 'array',
        'received_at' => 'datetime',
    ];
}