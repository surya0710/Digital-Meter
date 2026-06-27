<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mqtt\PublishMqttRequest;
use App\Services\MqttService;
use Illuminate\Http\JsonResponse;

class MqttController extends Controller
{
    public function __construct(
        protected MqttService $mqtt
    ) {}

    public function publish(PublishMqttRequest $request): JsonResponse
    {
        $message = $request->input('message');
        if (is_array($message)) {
            $message = json_encode($message);
        }

        $result = $this->mqtt->publish($request->input('topic'), $message);

        return response()->json([
            'success' => $result,
            'message' => $result ? 'Message published' : 'Failed to publish',
        ]);
    }
}
