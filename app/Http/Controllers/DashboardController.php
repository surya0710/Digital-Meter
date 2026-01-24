<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Devices;

class DashboardController extends Controller
{
    public function dashboard(){
        $devices = Devices::with('user')->latest()->paginate(10);
        return view('dashboard', compact('devices'));
    }

    public function logout(){
        auth()->logout();
        return redirect('/');
    }

}
