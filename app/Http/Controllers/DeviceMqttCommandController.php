<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RespondsToMqttCommands;
use App\Http\Requests\Device\Mqtt\CreateTimerRequest;
use App\Http\Requests\Device\Mqtt\DeleteTimerRequest;
use App\Http\Requests\Device\Mqtt\DeviceIdRequest;
use App\Http\Requests\Device\Mqtt\GetCurrentLimitRequest;
use App\Http\Requests\Device\Mqtt\RelayCommandRequest;
use App\Http\Requests\Device\Mqtt\SetCalibratedCurrentRequest;
use App\Http\Requests\Device\Mqtt\SetCalibratedVoltageRequest;
use App\Http\Requests\Device\Mqtt\SetCurrentProtectionRequest;
use App\Http\Requests\Device\Mqtt\SetRefreshRateRequest;
use App\Http\Requests\Device\Mqtt\SetVoltageProtectionRequest;
use App\Http\Requests\Device\Mqtt\SwitchRelayRequest;
use App\Services\Mqtt\DeviceMqttService;
use Illuminate\Http\JsonResponse;

class DeviceMqttCommandController extends Controller
{
    use RespondsToMqttCommands;

    public function __construct(
        protected DeviceMqttService $deviceMqtt
    ) {}

    public function switch(SwitchRelayRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->toggleRelay(
                $request->deviceID,
                $request->relayID,
                (int) $request->input('status', 0)
            );

            return $this->mqttJson($result);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }

    public function fetchTimer(RelayCommandRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->fetchTimers($request->deviceID, $request->relayID);

            return $this->mqttJson($result, alwaysTrueStatus: true);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }

    public function deleteTimer(DeleteTimerRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->deleteTimer($request->deviceID, $request->timerID);

            return $this->mqttJson($result);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }

    public function createTimer(CreateTimerRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->createTimer(
                $request->deviceID,
                $request->relayID,
                $request->days,
                $request->start_time.':00',
                $request->end_time.':00',
                $request->enabled
            );

            return $this->mqttJson($result);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }

    public function shutdownAll(DeviceIdRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->shutdownAll($request->deviceID);

            return $this->mqttJson($result);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }

    public function setRefreshRate(SetRefreshRateRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->setRefreshRate($request->deviceID, $request->refreshRate);

            return $this->mqttJson($result);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }

    public function fetchMemory(DeviceIdRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->fetchMemoryStatus($request->deviceID);

            return $this->mqttJson($result);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }

    public function fetchRefreshRate(DeviceIdRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->fetchRefreshRate($request->deviceID);

            return $this->mqttJson($result);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }

    public function fetchVoltageCalibration(DeviceIdRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->fetchVoltageCalibration($request->deviceID);

            return $this->mqttJson($result);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }

    public function setCalibratedVoltage(SetCalibratedVoltageRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->setCalibratedVoltage($request->deviceID, $request->voltage);

            return $this->mqttJson($result);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }

    public function setCalibratedCurrent(SetCalibratedCurrentRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->setCalibratedCurrent(
                $request->deviceID,
                $request->current,
                $request->index
            );

            return $this->mqttJson($result);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }

    public function setVoltageProtection(SetVoltageProtectionRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->setVoltageProtection(
                $request->hardwareDeviceId(),
                $request->underVoltage,
                $request->overVoltage
            );

            return $this->mqttJson($result);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }

    public function setCurrentProtection(SetCurrentProtectionRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->setCurrentProtection(
                $request->hardwareDeviceId(),
                $request->max_current,
                $request->relay
            );

            return $this->mqttJson($result);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }

    public function getCurrentLimit(GetCurrentLimitRequest $request): JsonResponse
    {
        try {
            $result = $this->deviceMqtt->fetchCurrentLimit(
                $request->deviceID,
                $request->relayID
            );

            return $this->mqttJson($result);
        } catch (\Throwable $e) {
            return $this->mqttError($e);
        }
    }
}
