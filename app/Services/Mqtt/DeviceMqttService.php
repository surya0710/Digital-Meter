<?php

namespace App\Services\Mqtt;

use App\Models\Device;
use App\Services\MqttService;

class DeviceMqttService
{
    public function __construct(
        protected MqttService $mqtt
    ) {}

    public function publishToDevice(string $deviceId, array $payload): bool
    {
        return $this->mqtt->publishToDevice($deviceId, $payload);
    }

    public function publishTo(Device $device, array $payload): bool
    {
        return $this->mqtt->publishToDevice($device->device_id, $payload);
    }

    public function toggleRelay(string $deviceId, int|string $relay, int $currentStatus): bool
    {
        $newState = $currentStatus == 0 ? 1 : 0;

        return $this->setRelay($deviceId, $relay, $newState);
    }

    public function setRelay(string $deviceId, int|string $relay, int $state, ?string $msgId = 'q2'): bool
    {
        return $this->publishToDevice(
            $deviceId,
            DeviceCommandBuilder::setRelay($relay, $state, $msgId)
        );
    }

    public function shutdownAll(string $deviceId): bool
    {
        return $this->publishToDevice(
            $deviceId,
            DeviceCommandBuilder::setAllRelays(0)
        );
    }

    public function fetchTimers(string $deviceId, int|string $relay): bool
    {
        return $this->publishToDevice(
            $deviceId,
            DeviceCommandBuilder::getTimers($relay)
        );
    }

    public function deleteTimer(string $deviceId, int|string $timerId): bool
    {
        return $this->publishToDevice(
            $deviceId,
            DeviceCommandBuilder::deleteTimer($timerId)
        );
    }

    public function createTimer(
        string $deviceId,
        int|string $relay,
        mixed $days,
        string $startTime,
        string $endTime,
        mixed $enabled
    ): bool {
        return $this->publishToDevice(
            $deviceId,
            DeviceCommandBuilder::createTimer($relay, $days, $startTime, $endTime, $enabled)
        );
    }

    public function setRefreshRate(string $deviceId, mixed $rate): bool
    {
        return $this->publishToDevice(
            $deviceId,
            DeviceCommandBuilder::setRate($rate)
        );
    }

    public function fetchRefreshRate(string $deviceId): bool
    {
        return $this->publishToDevice($deviceId, DeviceCommandBuilder::getRate());
    }

    public function fetchMemoryStatus(string $deviceId): bool
    {
        return $this->publishToDevice($deviceId, DeviceCommandBuilder::getMemoryStatus());
    }

    public function fetchVoltageCalibration(string $deviceId): bool
    {
        $calibrationSent = $this->publishToDevice(
            $deviceId,
            DeviceCommandBuilder::getVoltageCalibration()
        );

        $this->publishToDevice($deviceId, DeviceCommandBuilder::getVoltageLimits());

        return $calibrationSent;
    }

    public function setCalibratedVoltage(string $deviceId, mixed $voltage): bool
    {
        return $this->publishToDevice(
            $deviceId,
            DeviceCommandBuilder::setVoltageCalibration($voltage)
        );
    }

    public function setCalibratedCurrent(string $deviceId, mixed $current, int|string $channel): bool
    {
        return $this->publishToDevice(
            $deviceId,
            DeviceCommandBuilder::setCurrentCalibration($current, $channel)
        );
    }

    public function setVoltageProtection(string $deviceId, mixed $underVoltage, mixed $overVoltage): bool
    {
        return $this->publishToDevice(
            $deviceId,
            DeviceCommandBuilder::setVoltageProtection($underVoltage, $overVoltage)
        );
    }

    public function setCurrentProtection(string $deviceId, mixed $maxCurrent, int|string $relay): bool
    {
        return $this->publishToDevice(
            $deviceId,
            DeviceCommandBuilder::setCurrentLimit($maxCurrent, $relay)
        );
    }

    public function fetchCurrentLimit(string $deviceId, int|string $relay): bool
    {
        return $this->publishToDevice(
            $deviceId,
            DeviceCommandBuilder::getCurrentLimit($relay)
        );
    }
}
