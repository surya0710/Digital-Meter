<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\Mqtt\DeviceMqttService;
use App\Models\MqttResponse;
use Illuminate\Support\Facades\Validator;

class DeviceControl extends Component
{
    public $deviceID = '';
    public $relayID = '';
    public $relayState = '';
    public $statusMessage = '';
    public $statusType = '';
    public $lastResponse = null;
    public $isLoading = false;

    protected $rules = [
        'deviceID' => 'required|string',
        'relayID' => 'required',
        'relayState' => 'required',
    ];

    public function mount()
    {
        // Initialize
    }

    public function updatedDeviceID()
    {
        // When device ID changes, load the latest response
        if ($this->deviceID) {
            $this->loadLatestResponse();
        }
    }

    public function loadLatestResponse()
    {
        if ($this->deviceID) {
            $this->lastResponse = MqttResponse::where('device_id', $this->deviceID)
                ->latest('received_at')
                ->first();
        }
    }

    public function sendCommand()
    {
        $this->validate();
        
        $this->isLoading = true;
        
        try {
            $result = app(DeviceMqttService::class)->setRelay(
                $this->deviceID,
                $this->relayID,
                (int) $this->relayState,
                uniqid()
            );
            
            if ($result) {
                $this->statusMessage = 'Command sent successfully! Waiting for device response...';
                $this->statusType = 'success';
            } else {
                $this->statusMessage = 'Failed to send command';
                $this->statusType = 'error';
            }
            
        } catch (\Exception $e) {
            $this->statusMessage = 'Error: ' . $e->getMessage();
            $this->statusType = 'error';
        }
        
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.device-control');
    }
}