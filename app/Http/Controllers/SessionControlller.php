<?php

namespace App\Http\Controllers;


class SessionController extends Controller
{
    public function create(){
        return view('login');
    }
}
