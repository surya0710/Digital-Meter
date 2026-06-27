<?php

namespace App\Http\Requests\Device;

use App\Models\Device;
use Illuminate\Foundation\Http\FormRequest;

abstract class DeviceAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! $this->user()) {
            return false;
        }

        $device = $this->resolveDevice();

        if (! $device) {
            return true;
        }

        app(\App\Services\Device\DeviceService::class)->ensureUserCanAccess($this->user(), $device);

        return true;
    }

    protected function resolveDevice(): ?Device
    {
        $routeDevice = $this->route('device');

        if ($routeDevice instanceof Device) {
            return $routeDevice;
        }

        if (is_string($routeDevice) && $routeDevice !== '') {
            return Device::query()
                ->where('device_id', $routeDevice)
                ->first()
                ?? (is_numeric($routeDevice) ? Device::find($routeDevice) : null);
        }

        if (is_numeric($routeDevice)) {
            return Device::find($routeDevice);
        }

        if ($this->filled('deviceID')) {
            return Device::query()
                ->where('device_id', $this->input('deviceID'))
                ->first();
        }

        return null;
    }
}
