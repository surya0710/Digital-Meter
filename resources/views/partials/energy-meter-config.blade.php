<script type="application/json" id="device-dashboard-config">
{!! json_encode([
    'deviceId' => $device->device_id,
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) !!}
</script>
