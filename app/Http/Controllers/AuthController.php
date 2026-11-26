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
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->mat_khau)) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
        }

        if ((int) $user->trang_thai === 0) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Tài khoản đã bị khóa hoặc từ chối duyệt.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ((int) $user->trang_thai === 2) {
            return redirect()->route('hoso.index')
                ->with('status', 'Tài khoản của bạn đang chờ Admin duyệt. Vui lòng cập nhật Ảnh thẻ & CV tại đây.');
        }

        return redirect()->intended(route('dashboard.index'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'ho_ten' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:nhanvien,email',
            'so_dien_thoai' => 'nullable|string|max:15',
            'password' => 'required|min:6|confirmed',
        ]);

        $max = (int) User::whereRaw("ma_nv REGEXP '^NV[0-9]+$'")
            ->selectRaw("MAX(CAST(SUBSTRING(ma_nv, 3) AS UNSIGNED)) as n")
            ->value('n');

        do {
            $max++;
            $maNv = 'NV' . str_pad($max, 3, '0', STR_PAD_LEFT);
        } while (User::where('ma_nv', $maNv)->exists());

        $user = User::create([
            'ma_nv' => $maNv,
            'ho_ten' => $request->ho_ten,
            'email' => $request->email,
            'so_dien_thoai' => $request->so_dien_thoai,
            'mat_khau' => Hash::make($request->password),
            'vai_tro' => 'Employee',
            'trang_thai' => 2,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('hoso.index')
            ->with('status', 'Đăng ký thành công! Tài khoản đang ở trạng thái Đợi duyệt. Hãy tải lên Ảnh đại diện và CV để Admin xét duyệt.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
