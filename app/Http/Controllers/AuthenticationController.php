<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    /**
     * Display a registration form.
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:250'],
            'email' => ['required', 'email', 'max:250', 'unique:users'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $validatedData['role'] = 'admin';
        User::create($validatedData);

        return back()
            ->withSuccess('You have successfully registered');
    }

    /**
     * Display a login form.
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Authenticate the user.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Your provided credentials do not match in our records.',
            ])->onlyInput('email');
        }

        return to_route('dashboard')
            ->withSuccess('You have successfully logged in!');
    }

    /**
     * Display a dashboard to authenticated users.
     */
    public function dashboard()
    {
        return view('auth.dashboard');
    }

    /**
     * Log out the user from application.
     *
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login')
            ->withSuccess('You have logged out successfully!');;
    }
}
