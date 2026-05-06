<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccessCodeController extends Controller
{
    /** GET /login */
    public function showForm()
    {
        if (auth()->check()) {
            return redirect()->route(auth()->user()->dashboardRoute());
        }

        return view('auth.login');
    }

    /** POST /login */
    public function login(LoginRequest $request)
    {
        $user = User::where('access_code', $request->access_code)->first();

        if (! $user) {
            return back()
                ->withInput()
                ->withErrors(['access_code' => 'الكود غير صحيح، تواصل مع الإدارة']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->dashboardRoute());
    }

    /** POST /logout */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
