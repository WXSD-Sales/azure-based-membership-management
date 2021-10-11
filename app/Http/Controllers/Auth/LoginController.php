<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\RetrieveWebexUsers;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @return Application|Factory|View
     */
    public function showLoginForm()
    {
        $super_admin_exists = User::where('role', 'superadmin')->exists();

        if (!$super_admin_exists) {
            return redirect('/setup');
        }

        return view('auth.login', ['url' => [
            'setup' => route('setup', [], false),
            'reset' => route('reset', [], false),
            'email' => route('auth.email', [], false),
            'azure' => route('auth.azure', [], false),
            'webex' => route('auth.webex', [], false),
        ]]);
    }
}
