<?php

namespace App\Http\Controllers;

use App\Services\MqttService;
use Illuminate\Http\Request;

class MqttController extends Controller
{
    protected $mqtt;

    public function __construct(MqttService $mqtt)
    {
        $this->mqtt = $mqtt;
    }

    public function publish(Request $request)
    {
        $topic = $request->input('topic');
        $message = $request->input('message');
        
        $result = $this->mqtt->publish($topic, $message);
        
        return response()->json([
            'success' => $result,
            'message' => $result ? 'Message published' : 'Failed to publish'
        ]);
    }
}