<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\User;

class AdminController extends Controller
{
    /**
    *    login page
    */
    public function viewLogin() {
        return view('admin.login');
    }

    /**
     * Send login's data
     */

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

    /**
     * Get user's level
     * @param integer $id
     * @return integer
     */
    private function getLevel($id = 0) {
        if ($id == 0) {
            $id = Auth::user()->role_id;
        }
        $role = Role::find($id);
        return $role->id;
    }

    /**
     * Get menu info
     */
    private function getMenu() {
        $level = $this->getLevel();
        $menu = [];
        $menu[] = [
            'link' => route('home'),
            'title' => 'Trang chủ',
            'icon' => 'fas fa-tachometer-alt'
        ];

        $menu[] = [
            'link' => route('home'),
            'title' => 'Danh sách bàn',
            'icon' => 'fas fa-table'
        ];

        $menu[] = [
            'link' => route('home'),
            'title' => 'Sản phẩm',
            'icon' => 'fas fa-coffee'
        ];

        $menu[] = [
            'link' => route('home'),
            'title' => 'Loại sản phẩm',
            'icon' => 'fas fa-clipboard-list'
        ];
        return $menu;
    }

    /**
     * Get info after login
     */
    private function getData() {
        $user = Auth::user()->toArray();
        $menu = $this->getMenu();
        if (empty($user['avatar'])) {
            $user['avatar'] = 'https://i.pinimg.com/736x/4e/db/ff/4edbff0d52c0d2f8e85fe0c0cc903993.jpg';
        }
        $data = [
            'user' => $user,
            'menu' => $menu
        ];

        return $data;
    }

    /**
     * Admin page
     */
    public function viewHome() {
        $data = $this->getData();
        return view('admin.home', $data);
    }
}
