<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ── Register ──────────────────────────────────────────────
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'user',
            'status'   => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Welcome, ' . $user->name . '!');
    }

    // ── Login ─────────────────────────────────────────────────
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            if (!Auth::user()->isActive()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated. Please contact admin.'])->withInput();
            }
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
    }

    // ── Logout ────────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
