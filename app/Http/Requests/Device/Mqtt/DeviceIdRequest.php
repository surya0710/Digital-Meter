<?php

namespace App\Http\Requests\Device\Mqtt;

use App\Http\Requests\Device\DeviceAccessRequest;

class DeviceIdRequest extends DeviceAccessRequest
{
    public function rules(): array
    {
        return [
            'deviceID' => ['required', 'string'],
        ];
    }
}
