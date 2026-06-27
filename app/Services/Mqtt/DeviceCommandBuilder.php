<?php

namespace App\Services\Mqtt;

class DeviceCommandBuilder
{
    public static function setRelay(int|string $relay, int $state, ?string $msgId = 'q2'): array
    {
        return [
            'msgId' => $msgId,
            'cmd' => 'setRelay',
            'data' => [
                'relay' => $relay,
                'state' => $state,
            ],
        ];
    }

    public static function setAllRelays(int $state, string $msgId = 'all_off'): array
    {
        return [
            'msgId' => $msgId,
            'cmd' => 'setAllRelays',
            'data' => [
                'state' => $state,
            ],
        ];
    }

    public static function getTimers(int|string $relay): array
    {
        return [
            'cmd' => 'getTimers',
            'data' => [
                'relay' => $relay,
            ],
        ];
    }

    public static function deleteTimer(int|string $timerId): array
    {
        return [
            'msgId' => $timerId,
            'cmd' => 'deleteTimer',
            'data' => [
                'timerId' => $timerId,
            ],
        ];
    }

    public static function createTimer(
        int|string $relay,
        mixed $days,
        string $startTime,
        string $endTime,
        mixed $enabled
    ): array {
        return [
            'cmd' => 'createTimer',
            'data' => [
                'relay' => $relay,
                'days' => $days,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'enabled' => $enabled,
            ],
        ];
    }

    public static function setRate(mixed $rate): array
    {
        return [
            'cmd' => 'setRate',
            'data' => [
                'rate' => $rate,
            ],
        ];
    }

    public static function getRate(): array
    {
        return ['cmd' => 'getRate'];
    }

    public static function getMemoryStatus(): array
    {
        return ['cmd' => 'getMemoryStatus'];
    }

    public static function getVoltageCalibration(): array
    {
        return ['cmd' => 'getVoltageCalibration'];
    }

    public static function getVoltageLimits(): array
    {
        return ['cmd' => 'getVoltageLimits'];
    }

    public static function setVoltageCalibration(mixed $voltage): array
    {
        return [
            'cmd' => 'setVoltageCalibration',
            'data' => [
                'voltage' => $voltage,
            ],
        ];
    }

    public static function setCurrentCalibration(mixed $actual, int|string $channel): array
    {
        return [
            'cmd' => 'setCurrentCalibration',
            'data' => [
                'actual' => $actual,
                'channel' => $channel,
            ],
        ];
    }

    public static function setVoltageProtection(mixed $min, mixed $max): array
    {
        return [
            'cmd' => 'setVoltageProtection',
            'data' => [
                'min' => $min,
                'max' => $max,
            ],
        ];
    }

    public static function setCurrentLimit(mixed $limit, int|string $channel): array
    {
        return [
            'cmd' => 'setCurrentLimit',
            'data' => [
                'limit' => $limit,
                'channel' => $channel,
            ],
        ];
    }

    public static function getCurrentLimit(int|string $channel): array
    {
        return [
            'cmd' => 'getCurrentLimit',
            'data' => [
                'channel' => $channel,
            ],
        ];
    }
}
