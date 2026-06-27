<script type="application/json" id="device-dashboard-config">
{!! json_encode([
    'deviceId' => $device->device_id,
    'assignId' => $device->id,
    'routes' => [
        'updateSwitchName' => route('devices.updateSwitchName', $device),
        'setVoltageProtection' => route('devices.setVoltageProtection', $device),
        'setCurrentProtection' => route('devices.setCurrentProtection', $device),
        'getRefreshRate' => route('devices.getRefreshRate'),
        'getVoltageCalibration' => route('devices.getVoltageCalibration'),
        'setRefreshRate' => route('devices.setRefreshRate'),
        'setCalibratedVoltage' => route('devices.setCalibratedVoltage'),
        'setCalibratedCurrent' => route('devices.setCalibratedCurrent'),
        'fetchMemory' => route('devices.fetchMemory'),
        'getCurrentLimit' => route('devices.getCurrentLimit'),
        'fetchTimer' => route('devices.fetchTimer'),
        'switch' => route('devices.switch'),
        'deleteTimer' => route('devices.deleteTimer'),
        'saveTimer' => route('devices.saveTimer'),
        'shutdownAll' => route('devices.shutdownAll'),
    ],
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}
</script>
