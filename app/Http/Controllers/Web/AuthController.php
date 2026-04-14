<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $role = $user->roles->first()->name ?? 'student';

            // Role-based redirect with proper route mapping
            $redirectRoutes = [
                'principal' => 'dashboard.principal',
                'admin' => 'dashboard.admin',
                'teacher' => 'teacher.dashboard',
                'class_teacher' => 'teacher.dashboard',
                'subject_teacher' => 'teacher.dashboard',
                'staff' => 'staff.dashboard',
                'student' => 'dashboard.student',
                'accountant' => 'dashboard.accountant',
                'accounts_staff' => 'dashboard.accounts_staff',
                'office' => 'dashboard.office',
                'librarian' => 'dashboard.librarian',
                'hod_commerce' => 'teacher.dashboard',
                'hod_science' => 'teacher.dashboard',
                'hod_management' => 'teacher.dashboard',
                'hod_arts' => 'teacher.dashboard',
            ];

            $route = $redirectRoutes[$role] ?? 'dashboard.accountant';

            return redirect()->route($route);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}