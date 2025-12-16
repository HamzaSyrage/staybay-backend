<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {

    }
    public function show(User $user){
        return view('User.show',['user'=>$user]);
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

    public function update(UpdateUserRequest $request,User $user){
        $validated = $request->validated();
        if(isset($validated['password']))
            $validated['password'] = Hash::make($validated['password']);
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = 'storage/' . $path;
        }
        elseif ($request->filled('avatar_url')) {
            $user->avatar = $request->avatar_url;
        }

        if ($request->hasFile('id_card')) {
            $path = $request->file('id_card')->store('id_cards', 'public');
            $validated['id_card'] = 'storage/' . $path;
        }elseif ($request->filled('id_card_url')) {
            $user->id_card = $request->id_card_url;
        }
        $user->save();
        if($user->updateOrFail($validated)){
            return redirect("/dashboard",201,['message'=>'User Updated successfully.']);
        }
        return back()->with('message', "Couuldn't Update user");
    }
}
