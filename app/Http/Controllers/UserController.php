<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function list(){
        $users = User::latest()->paginate(10);
        return view('users', compact('users'));
    }

    public function createForm(){
        return view('users-create');
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|digits:10',
            'role' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try{
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'password' => bcrypt($request->password),
                'status' => 1,
            ]);

            return redirect()->route('users.list')->with('success', 'User created successfully');
        }
        catch(\Exception $e){
            Log::error($e);
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }
}
