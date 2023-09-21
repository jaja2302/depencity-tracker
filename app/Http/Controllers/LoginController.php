<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class LoginController extends Controller
{
    public function index(Request $request)
    {
        return view('Login.login');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email_or_nama_lengkap' => 'required',
            'password' => 'required'
        ]);

        $credentials = [
            'email' => $request->email_or_nama_lengkap,
            'password' => $request->password
        ];

        $user = DB::table('pengguna')
            ->where('email', $request->email_or_nama_lengkap)
            ->orWhere('nama_lengkap', $request->email_or_nama_lengkap)
            ->first();

        if (!$user) {
            return back()->with('error', 'Email atau Password Salah');
        }

        if ($request->password != $user->password) {
            return back()->with('error', 'Email atau Password Salah');
        }

        // Manually authenticate the user
        Auth::loginUsingId($user->user_id);

        // Store user data in the session
        Session::put('user_id', $user->user_id);
        Session::put('user_name', $user->nama_lengkap);
        Session::put('departemen', $user->departemen);
        Session::put('lok', $user->lokasi_kerja);
        Session::put('jabatan', $user->jabatan);



        return redirect()->intended('dashboard');
    }

    public function logout(Request $request)
    {
        // Clear the session data
        Session::flush();

        // Logout the user
        Auth::logout();

        return redirect('/');
    }
}
