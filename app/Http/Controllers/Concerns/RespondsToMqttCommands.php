<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;

trait RespondsToMqttCommands
{
    protected function mqttJson(bool $result, bool $alwaysTrueStatus = false): JsonResponse
    {
        return response()->json([
            'status' => $alwaysTrueStatus ? true : $result,
            'message' => $result ? 'Success' : 'Failed to publish',
        ]);
    }

    protected function mqttError(\Throwable $e): JsonResponse
    {
        return response()->json([
            'status' => false,
            'error' => $e->getMessage(),
        ]);
    }
}
