<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\LoginLink;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $super_admin = User::firstWhere('role', 'superadmin');

        if (!isset($super_admin)) {
            return redirect('/setup');
        } else {
            //TODO: Switch to using signed URL.
            $super_admin->tokens()->delete();
            $token = $super_admin->createToken('default');
            session(['token' => $token->plainTextToken]);
            $message = "Please use **$token->plainTextToken** as login token for " . config('app.name') . ".";
            $super_admin->notify(new LoginLink($message));
        }

        return view('auth.login', ['url' => [
            'setup' => route('setup', [], false),
            'login' => route('login', [], false),
            'reset' => route('reset', [], false),
            'email' => route('auth.email', [], false),
            'azure' => route('auth.azure', [], false),
            'webex' => route('auth.webex', [], false),
        ]]);
    }


    protected function validateLogin(Request $request)
    {
        $super_admin = User::firstWhere('role', 'superadmin');

        if (!$super_admin->exists() || $request->session()->missing('token')) {
            abort(403);
        }

        $request->validate([
            'token' => ['required', 'string']
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        $super_admin = User::firstWhere('role', 'superadmin');

        if ($request->session()->get('token') === $request->input('token')) {
            $super_admin->tokens()->delete();
            Auth::login($super_admin);
            return true;
        }

        return false;
    }
}
