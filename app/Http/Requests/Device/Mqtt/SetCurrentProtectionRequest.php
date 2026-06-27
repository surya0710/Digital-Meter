<?php

namespace App\Http\Requests\Device\Mqtt;

use App\Http\Requests\Device\DeviceAccessRequest;

class SetCurrentProtectionRequest extends DeviceAccessRequest
{
    public function rules(): array
    {
        return [
            'max_current' => ['required'],
            'relay' => ['required'],
        ];
    }

    public function hardwareDeviceId(): string
    {
        return (string) $this->resolveDevice()?->device_id;
    }
}
