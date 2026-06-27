<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function dashboard(): View
    {
        $devices = Device::with('user')->latest()->paginate(10);

        return view('dashboard', compact('devices'));
    }
}
