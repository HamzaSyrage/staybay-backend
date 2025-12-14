<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {

    }
    public function verify(User $user){
        // Prevent double verification
        if ($user->user_verified_at !== null) {
            return back()->with('error', 'User already verified.');
        }
        $user->update([
                'user_verified_at' => now(),
        ]);

        return back()->with('success', 'User verified successfully.');
    }
    public function destroy(User $user){
       if($user->deleteOrFail())
            return back()->with('success', 'User verified successfully.');
       return back()->with('error', "Couuldn't delete user");
    }
}
