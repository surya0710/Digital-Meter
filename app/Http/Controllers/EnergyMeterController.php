<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;

class EnergyMeterController extends Controller
{
    public function view($id){
        $device = Device::with('switchNames')->findOrFail($id);
        return view('energy-meter-view', compact('device'));
    }
}
