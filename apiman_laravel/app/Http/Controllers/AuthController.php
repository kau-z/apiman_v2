<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login', [
            'title' => 'Cirrus API - Login'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        $user = User::where('username', $username)->first();

        if ($user && $user->verifyPassword($password)) {
            Auth::login($user);
            $request->session()->regenerate();
            $request->session()->put('userID', $user->id);
            $request->session()->put('username', $user->username);

            return redirect()->intended(route('sync-api.create'));
        }

        return back()->withInput()->with('error', 'Invalid Username or Password');
    }

    public function showRegister()
    {
        return view('auth.register', [
            'title' => 'Register - Cirrus API'
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:tbl_users,username|max:100',
            'password' => 'required|string|min:4',
            'email' => 'nullable|email|max:150',
            'full_name' => 'nullable|string|max:150',
        ]);

        $user = User::create([
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'email' => $request->input('email'),
            'full_name' => $request->input('full_name'),
            'status' => 1,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('userID', $user->id);
        $request->session()->put('username', $user->username);

        return redirect()->route('sync-api.create')->with('success', 'Registration successful!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
