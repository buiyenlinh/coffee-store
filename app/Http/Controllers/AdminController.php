<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\User;

class AdminController extends Controller
{
    public function viewLogin() {
        return view('admin.login');
    }

    public function login(Request $request) {
        if (Auth::check()) {
            return redirect()->intended('/admin/');
        }

        $request->validate(
            [
                'username' => 'required',
                'password' => 'required'
            ],
            [
                'username.required' => 'Tên đăng nhập là bắt buộc',
                'password.required' => 'Mật khẩu là bắt buộc'
            ]
        );
        $username = $request->old('username');
        $login = [
            'username' => $request->input('username'),
            'password' => $request->input('password')
        ];

        if (!Auth::attempt($login)) {
            return back()->withInput()->with('error', 'Tên đăng nhập hoặc mật khẩu sai');
        }
        return redirect()->route('home');
    }

    public function viewHome() {
        return view('admin.home');
    }
}
