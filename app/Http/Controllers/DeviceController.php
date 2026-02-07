<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Devices;
use App\Models\User;
use App\Models\DeviceSwitchName as SwitchName;
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
        $device = Devices::with('switchNames')->where('id', $id)->first();
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
                'status' => true,
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

    public function updateSwitchName(Request $request, $deviceID)
    {
        $validator = Validator::make($request->all(), [
            'switch0' => 'required|string',
            'switch1' => 'required|string',
            'switch2' => 'required|string',
            'switch3' => 'required|string',
            'switch4' => 'required|string',
            'switch5' => 'required|string',
            'switch6' => 'required|string',
            'switch7' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        SwitchName::updateOrCreate(
            ['assign_device_id' => $deviceID],
            [
                'switch0' => $request->switch0,
                'switch1' => $request->switch1,
                'switch2' => $request->switch2,
                'switch3' => $request->switch3,
                'switch4' => $request->switch4,
                'switch5' => $request->switch5,
                'switch6' => $request->switch6,
                'switch7' => $request->switch7,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Switch names updated successfully'
        ]);
    }

    public function setRefreshRate(Request $request){
        $validator = Validator::make($request->all(), [
            'refreshRate' => 'required',
            'deviceID' => 'required',
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
                "cmd" => "setRate",
                "data" => [
                    "rate" => $request->refreshRate
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

    public function fetchMemory(Request $request){
        $validator = Validator::make($request->all(), [
            'deviceID' => 'required',
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
                "cmd" => "getMemoryStatus"
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

    public function fetchRefreshRate(Request $request){
        $validator = Validator::make($request->all(), [
            'deviceID' => 'required',
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
                "cmd" => "getRate"
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

    public function fetchVoltageCalibration(Request $request){
        $validator = Validator::make($request->all(), [
            'deviceID' => 'required',
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
                "cmd" => "getVoltageCalibration"
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

    public function setCalibratedVoltage(Request $request){
        $validator = Validator::make($request->all(), [
            'voltage' => 'required',
            'deviceID' => 'required',
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
                "cmd" => "setVoltageCalibration",
                "data" => [
                    "voltage" => $request->voltage
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

    public function setCalibratedCurrent(Request $request){
        $validator = Validator::make($request->all(), [
            'current' => 'required',
            'deviceID' => 'required',
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
                "cmd" => "setCurrentCalibration",
                "data" => [
                    "actual" => $request->current,
                    "channel" => $request->index
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
