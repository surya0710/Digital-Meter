<?php

namespace App\Http\Requests\Device;

use App\Models\Device;
use App\Services\Device\DeviceService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSwitchNamesRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! $this->user()) {
            return false;
        }

        $device = Device::find($this->route('device'));

        if (! $device) {
            return true;
        }

        app(DeviceService::class)->ensureUserCanAccess($this->user(), $device);

        return true;
    }

    public function rules(): array
    {
        $switches = [];

        for ($i = 0; $i <= 7; $i++) {
            $switches['switch'.$i] = ['required', 'string', 'max:255'];
        }

        return $switches;
    }

    public function switchNames(): array
    {
        return $this->only([
            'switch0', 'switch1', 'switch2', 'switch3',
            'switch4', 'switch5', 'switch6', 'switch7',
        ]);
    }
}
