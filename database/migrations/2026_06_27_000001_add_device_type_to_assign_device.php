<?php

use App\Enums\DeviceType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assign_device', function (Blueprint $table) {
            $table->string('device_type', 32)
                ->default(DeviceType::Panel->value)
                ->after('device_name');
        });

        $legacyIds = config('digital-meter.legacy_energy_meter_ids', []);

        if (! empty($legacyIds)) {
            DB::table('assign_device')
                ->whereIn('device_id', $legacyIds)
                ->update(['device_type' => DeviceType::EnergyMeter->value]);
        }
    }

    public function down(): void
    {
        Schema::table('assign_device', function (Blueprint $table) {
            $table->dropColumn('device_type');
        });
    }
};
