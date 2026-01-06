<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Devices;
use App\Models\User;
use App\Services\MqttService;

class DeviceController extends Controller
{

    protected $mqtt;

    public function __construct(MqttService $mqtt)
    {
        $this->mqtt = $mqtt;
    }

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

    public function switch(Request $request){
        $validator = Validator::make($request->all(), [
            'deviceID' => 'required',
            'relayID'   => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            if($request->status == 0){
                $switch = 1;
            }else{
                $switch = 0;
            }
            $topic = $request->deviceID."/request";
            $message = json_encode(["msgId"=>"q2", "cmd"=>"setRelay", "data"=>["relay"=> $request->relayID, "state"=>$switch]]);
            
            $result = $this->mqtt->publish($topic, $message);
            
            return response()->json([
                'status' => $result,
                'message' => $result ? 'Success' : 'Failed to publish'
            ]);
        }

        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function fetchTimer(Request $request){
        $validator = Validator::make($request->all(), [
            'deviceID' => 'required',
            'relayID'   => 'required'
        ]);

        if($validator->fails()){
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $topic = $request->deviceID."/request";
            $message = json_encode(["cmd"=>"getTimers", "data"=>["relay"=> $request->relayID]]);
            $result = $this->mqtt->publish($topic, $message);
            return response()->json([
                'status' => $result,
                'message' => $result ? 'Success' : 'Failed to publish'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deleteTimer(Request $request){
        $validator = Validator::make($request->all(), [
            'timerID' => 'required',
            'deviceID' => 'required',
            'relayID'   => 'required'
        ]);

        if($validator->fails()){
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $topic = $request->deviceID."/request";
            $message = json_encode(["msgId"=> $request->timerID,"cmd"=>"deleteTimer", "data"=>["timerId"=> $request->timerID]]);
            $result = $this->mqtt->publish($topic, $message);
            return response()->json([
                'status' => $result,
                'message' => $result ? 'Success' : 'Failed to publish'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function createTimer(Request $request){
        $validator = Validator::make($request->all(), [
            'deviceID' => 'required',
            'relayID'   => 'required',
            "days" => 'required',
            "start_time" => 'required',
            "end_time" => 'required',
            "enabled" => 'required'
        ]);

        if($validator->fails()){
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $topic = $request->deviceID."/request";
            $message = json_encode(["cmd"=>"createTimer", "data"=>[
                "relay" => $request->relayID,
                "days" => $request->days,
                "startTime" => $request->start_time.":00",
                "endTime" => $request->end_time.":00",
                "enabled" => $request->enabled
            ]]);
            $result = $this->mqtt->publish($topic, $message);
            return response()->json([
                'status' => $result,
                'message' => $result ? 'Success' : 'Failed to publish'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function shutdownAll(Request $request){
        $validator = Validator::make($request->all(), [
            "deviceID" => 'required',
        ]);

        if($validator->fails()){
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $topic = $request->deviceID."/request";
            $message = json_encode([
                "msgId" => "all_off",
                "cmd"=>"setAllRelays", 
                "data"=>[
                    "state"=> 0
                ]
            ]);
            $result = $this->mqtt->publish($topic, $message);
            return response()->json([
                'status' => $result,
                'message' => $result ? 'Success' : 'Failed to publish'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
