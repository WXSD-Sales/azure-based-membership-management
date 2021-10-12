<?php

namespace App\Http\Controllers;

use Illuminate\Console\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        $auth_user_id = auth()->id();
        Log::info("[User #{$auth_user_id}] attempting to visit: {$request->url()}.");

        return view('home');
    }
}
