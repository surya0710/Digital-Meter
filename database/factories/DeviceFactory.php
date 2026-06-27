<?php

namespace Database\Factories;

use App\Enums\DeviceType;
use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Device>
 */
class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'device_id' => strtoupper(fake()->bothify('??:??:??:??:??:??')),
            'device_name' => fake()->words(2, true),
            'device_type' => DeviceType::Panel,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function energyMeter(): static
    {
        return $this->state(fn () => ['device_type' => DeviceType::EnergyMeter]);
    }
}
