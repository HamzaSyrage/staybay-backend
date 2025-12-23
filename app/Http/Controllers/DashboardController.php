<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use function Laravel\Prompts\search;

class DashboardController extends Controller
{
    public function index(FormRequest $request)
    {
        $users = User::query()
            ->where('is_admin', false)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            })
            ->orderByRaw("
            CASE
                WHEN user_verified_at IS NULL THEN 0
                ELSE 1
            END,
            user_verified_at ASC
        ")
            ->simplePaginate(10);
//        $users = User::where('is_admin',false)->where('is_admin',false)->orderByRaw("CASE WHEN user_verified_at IS NULL THEN 0 ELSE 1 END, user_verified_at ASC")
//            ->simplePaginate(10);
        return view('User.index', [
            'users'=>$users
        ]);
    }
//    public function search(){
//        return [
//            'user' => fn () => search(
//                label: 'Search for a user:',
//                placeholder: 'E.g. Taylor Otwell',
//                options: fn ($value) => strlen($value) > 0
//                ? User::whereLike('name', "%{$value}%")->pluck('name', 'id')->all()
//                : []
//            ),
//        ];
//    }

}
