<?php

namespace App\Http\Requests\Device;

class GetMqttDataRequest extends DeviceAccessRequest
{
    public function rules(): array
    {
        return [
            'deviceID' => ['required', 'string'],
        ];
    }
}
