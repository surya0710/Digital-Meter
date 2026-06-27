<?php

namespace App\Models;

use App\Enums\DeviceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    use HasFactory;

    protected $table = 'assign_device';

    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'device_type',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'device_type' => DeviceType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function switchNames(): HasOne
    {
        return $this->hasOne(DeviceSwitchName::class, 'assign_device_id')->withDefault();
    }

    public function isEnergyMeter(): bool
    {
        if ($this->device_type === DeviceType::EnergyMeter) {
            return true;
        }

        return in_array(
            $this->device_id,
            config('digital-meter.legacy_energy_meter_ids', []),
            true
        );
    }

    public function isPanel(): bool
    {
        return $this->device_type === DeviceType::Panel;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function mqttRequestTopic(): string
    {
        return "{$this->device_id}/request";
    }

    public function mqttResponseTopic(): string
    {
        return "{$this->device_id}/response";
    }

    public function dashboardView(): string
    {
        return $this->isEnergyMeter()
            ? DeviceType::EnergyMeter->viewName()
            : DeviceType::Panel->viewName();
    }
}
