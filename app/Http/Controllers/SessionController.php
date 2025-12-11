<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginDashboardRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SessionController extends Controller
{
    public function index()
    {

    }
    public function create(){
        return view('login');
    }
    public function login(LoginDashboardRequest $request){
        $validated = $request->validated();
        $user = User::where('phone', $validated['phone'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid credentials',
                'data' => null
            ], 401);
        }


        //TODO Create DashBoard
        return redirect('/Dashboard');

    }
}
