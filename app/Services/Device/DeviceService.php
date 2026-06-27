<?php

namespace App\Services\Device;

use App\Enums\DeviceType;
use App\Models\Device;
use App\Models\DeviceSwitchName;
use App\Models\MqttResponse;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DeviceService
{
    public function paginatedList(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return Device::query()
            ->with('user')
            ->when(! $user->isAdmin(), fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->paginate($perPage);
    }

    public function assignableUsers()
    {
        return User::query()
            ->select('id', 'name')
            ->orderByDesc('id')
            ->get();
    }

    public function create(array $attributes): Device
    {
        return Device::create($attributes);
    }

    public function findForDisplay(int $id, User $user): Device
    {
        $device = Device::with('switchNames')->findOrFail($id);

        $this->ensureUserCanAccess($user, $device);

        return $device;
    }

    public function findByHardwareId(string $deviceId, User $user): Device
    {
        $device = Device::query()
            ->where('device_id', $deviceId)
            ->firstOrFail();

        $this->ensureUserCanAccess($user, $device);

        return $device;
    }

    public function findByAssignId(int $assignDeviceId, User $user): Device
    {
        $device = Device::findOrFail($assignDeviceId);

        $this->ensureUserCanAccess($user, $device);

        return $device;
    }

    public function updateSwitchNames(Device $device, array $names): DeviceSwitchName
    {
        return DeviceSwitchName::updateOrCreate(
            ['assign_device_id' => $device->id],
            $names
        );
    }

    public function latestMqttResponse(string $deviceId): ?MqttResponse
    {
        return MqttResponse::latestForDevice($deviceId);
    }

    public function ensureUserCanAccess(User $user, Device $device): void
    {
        if ($user->isAdmin() || $device->user_id === $user->id) {
            return;
        }

        abort(403, 'You do not have permission to access this device.');
    }

    public function deviceAttributesFromRequest(array $validated): array
    {
        $attributes = [
            'user_id' => $validated['user_id'],
            'device_id' => $validated['device_id'],
            'device_name' => $validated['device_name'] ?? null,
            'is_active' => filter_var($validated['status'], FILTER_VALIDATE_BOOLEAN),
            'device_type' => isset($validated['device_type'])
                ? DeviceType::from($validated['device_type'])
                : DeviceType::Panel,
        ];

        if ($this->isLegacyEnergyMeter($attributes['device_id'])) {
            $attributes['device_type'] = DeviceType::EnergyMeter;
        }

        return $attributes;
    }

    protected function isLegacyEnergyMeter(string $deviceId): bool
    {
        return in_array(
            $deviceId,
            config('digital-meter.legacy_energy_meter_ids', []),
            true
        );
    }
}
