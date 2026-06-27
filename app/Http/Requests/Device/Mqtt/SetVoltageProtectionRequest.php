<?php

namespace App\Http\Requests\Device\Mqtt;

use App\Http\Requests\Device\DeviceAccessRequest;

class SetVoltageProtectionRequest extends DeviceAccessRequest
{
    public function rules(): array
    {
        return [
            'underVoltage' => ['required'],
            'overVoltage' => ['required'],
        ];
    }

    public function hardwareDeviceId(): string
    {
        return (string) $this->resolveDevice()?->device_id;
    }
}
