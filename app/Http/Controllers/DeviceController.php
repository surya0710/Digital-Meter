<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Devices;
use App\Models\User;
use PhpMqtt\Client\Facades\MQTT;
use App\Services\MqttService;

class DeviceController extends Controller
{
    public function list(){
        $devices = Devices::with('user')->latest()->paginate(10);
        return view('devices', compact('devices'));
    }

    public function createForm(){
        $users = User::select('id', 'name')->orderBy('id', 'desc')->get();
        return view('devices-create', compact('users'));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|exists:users,id',
            'device_id' => 'required',
            'status'    => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {

            Devices::create([
                'user_id'   => $request->user_id,
                'device_id' => $request->device_id,
                'is_active' => $request->status === 'active' ? 1 : 0,
            ]);

            return redirect()
                ->route('devices.list')
                ->with('success', 'Device added successfully');

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->with('error', 'Something went wrong')
                ->withInput();
        }
    }

    public function view($id){
        $device = Devices::with('user')->where('id', $id)->first();
        return view('devices-view', compact('device'));
    }

    public function publish(MqttService $mqtt)
    {
        $mqtt->publish('test/topic', 'Hello from Laravel');
        return 'MQTT message sent';
    }
}
