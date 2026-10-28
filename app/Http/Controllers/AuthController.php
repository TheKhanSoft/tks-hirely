<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser();
        }
        return view('auth.login');
    }

    /**
     * Process login attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirectUser();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Process logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('message', 'Successfully logged out.');
    }

    /**
     * Redirect users based on role.
     */
    protected function redirectUser()
    {
        $user = Auth::user();
        
        if ($user->hasRole('super-admin') || $user->hasRole('registrar')) {
            return redirect()->intended('/admin/dashboard');
        }

        if ($user->hasRole('scrutiny-committee')) {
            return redirect()->intended('/admin/dashboard');
        }

        if ($user->hasRole('appellate-committee')) {
            return redirect()->intended('/admin/appeals');
        }

        if ($user->hasRole('selection-board-member')) {
            return redirect()->intended('/admin/dashboard');
        }

        if ($user->hasRole('academic-reviewer')) {
            return redirect()->intended('/reviewer/dashboard');
        }

        if ($user->hasRole('applicant')) {
            return redirect()->intended('/applicant/dashboard');
        }

        return redirect()->intended('/');
    }
}
