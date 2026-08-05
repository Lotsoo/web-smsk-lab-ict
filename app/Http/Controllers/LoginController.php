<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {

        if (Auth::check()) {
            return redirect('/dashboard')->with('success', 'Login Success!');
        }

        return view('admin.auth.login_admin', [
            'title' => 'Login'
        ]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ], [
            'username.required' => 'required',
            'password.required' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard')->with('success', 'Login Success!');
        }

        return back()->with("loginError", "Login Failed!");
    }


    public function registrasi()
    {
        $admin = "admin";
        $data = User::create([
            "username" => "superadmin",
            "password" => Hash::make("superadmin"),
            "name" => "kepala lab",
            "role" => "superadmin"
        ]);

        User::create([
            "username" => "admin",
            "password" => Hash::make($admin),
            "name" => "spv",
        ]);
        return $data;


    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logout Success!');
    }
}


