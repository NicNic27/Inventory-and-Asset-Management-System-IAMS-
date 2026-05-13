<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'ends_with:@deped.gov.ph'],
            'password' => 'required'
        ], [
            'email.ends_with' => 'Only @deped.gov.ph emails are allowed to log in.'
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'Active'
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Login',
                'description' => "User successfully logged in.",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            $role = strtolower(Auth::user()->role); 
            
            if ($role === 'admin') {
                return redirect('/admin/dashboard'); 
            } elseif ($role === 'staff') {
                return redirect('/dashboard'); 
            } elseif ($role === 'frontuser') {
                return redirect('/user/dashboard'); 
            }

            return redirect('/dashboard'); 
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records or your account is inactive.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();

        if ($userId) {
            ActivityLog::create([
                'user_id' => $userId,
                'action' => 'Logout',
                'description' => "User logged out.",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}