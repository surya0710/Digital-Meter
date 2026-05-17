<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devices;

class EnergyMeterController extends Controller
{
    public function view($id){
        $device = Devices::with('switchNames')->where('id', $id)->first();
        return view('energy-meter-view', compact('device'));
    }
}
