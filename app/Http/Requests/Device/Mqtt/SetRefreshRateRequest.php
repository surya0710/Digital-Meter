<?php

namespace App\Http\Requests\Device\Mqtt;

use App\Http\Requests\Device\DeviceAccessRequest;

class SetRefreshRateRequest extends DeviceAccessRequest
{
    public function rules(): array
    {
        return [
            'deviceID' => ['required', 'string'],
            'refreshRate' => ['required'],
        ];
    }
}
