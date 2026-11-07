<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function loginProcess(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password yang dimasukkan salah',
        ])->onlyInput('username');
    }
        
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    //Rest API
    public function apiLogin(Request $request)
{
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    $user = User::with('role')
        ->where('username', $request->username)
        ->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Username atau password salah'
        ], 401);
    }

    // APK hanya untuk Guru, Organisasi, dan PIC
    if (!in_array($user->role_id, [3, 4, 5])) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Akun ini hanya dapat digunakan melalui website.'
        ], 403);
    }

    $token = $user->createToken('rapi-flutter')->plainTextToken;

    return response()->json([
        'status'  => 'success',
        'message' => 'Login API berhasil',
        'data'    => [
            'user'       => $user,
            'token'      => $token,
            'token_type' => 'Bearer'
        ]
    ], 200);
}

    public function me(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data'   => $request->user()->load('role')
        ], 200);
    }

    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout API berhasil'
        ], 200);
    }
}
