<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Http\Requests\Device\GetMqttDataRequest;
use App\Http\Requests\Device\StoreDeviceRequest;
use App\Http\Requests\Device\UpdateSwitchNamesRequest;
use App\Services\Device\DeviceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function __construct(
        protected DeviceService $devices
    ) {}

    public function list(): View
    {
        return view('devices', [
            'devices' => $this->devices->paginatedList(auth()->user()),
        ]);
    }

    public function createForm(): View
    {
        return view('devices-create', [
            'users' => $this->devices->assignableUsers(),
        ]);
    }

    public function create(StoreDeviceRequest $request): RedirectResponse
    {
        $this->devices->create(
            $this->devices->deviceAttributesFromRequest($request->validated())
        );

        return redirect()
            ->route('devices.list')
            ->with('success', 'Device added successfully.');
    }

    public function view(Device $device): View
    {
        $device = $this->devices->findForDisplay($device->id, auth()->user());

        return view($device->dashboardView(), compact('device'));
    }

    public function updateSwitchName(UpdateSwitchNamesRequest $request, Device $device): JsonResponse
    {
        $device = $this->devices->findByAssignId($device->id, auth()->user());
        $this->devices->updateSwitchNames($device, $request->switchNames());

        return response()->json([
            'status' => 'success',
            'message' => 'Switch names updated successfully',
        ]);
    }

    public function getMqttData(GetMqttDataRequest $request): JsonResponse
    {
        $this->devices->findByHardwareId($request->input('deviceID'), auth()->user());

        $response = $this->devices->latestMqttResponse($request->input('deviceID'));

        return response()->json([
            'status' => true,
            'data' => $response?->message,
            'received_at' => $response?->received_at?->toDateTimeString(),
        ]);
    }
}
