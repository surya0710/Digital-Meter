<?php

namespace App\Enums;

enum DeviceType: string
{
    case Panel = 'panel';
    case EnergyMeter = 'energy_meter';

    public function label(): string
    {
        return match ($this) {
            self::Panel => 'Smart Panel',
            self::EnergyMeter => 'Energy Meter',
        };
    }

    public function viewName(): string
    {
        return match ($this) {
            self::Panel => 'devices-view',
            self::EnergyMeter => 'energy-meter-view',
        };
    }
}
