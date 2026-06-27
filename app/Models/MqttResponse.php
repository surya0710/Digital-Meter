<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MqttResponse extends Model
{
    protected $fillable = [
        'device_id',
        'topic',
        'message',
        'received_at',
    ];

    protected $casts = [
        'message' => 'array',
        'received_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }

    public static function latestForDevice(string $deviceId): ?self
    {
        return static::query()
            ->where('device_id', $deviceId)
            ->latest('received_at')
            ->first();
    }
}
