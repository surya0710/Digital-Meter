<?php

namespace App\Http\Requests\Device\Mqtt;

use App\Http\Requests\Device\DeviceAccessRequest;

class CreateTimerRequest extends DeviceAccessRequest
{
    public function rules(): array
    {
        return [
            'deviceID' => ['required', 'string'],
            'relayID' => ['required'],
            'days' => ['required'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'enabled' => ['required'],
        ];
    }
}
