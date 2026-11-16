<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PhanCaController;
use App\Http\Controllers\NhanVienController;

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();
    if ((int) $user->trang_thai === 2) {
        return redirect()->route('hoso.index');
    }

    return $user->canManage()
        ? redirect()->route('nhanvien.index')
        : redirect()->route('phanca.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login.store');
    Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout');

    // Tab mới: Gửi Ảnh & CV dành cho người đăng ký và nhân viên
    Route::get('/ho-so-ca-nhan', [NhanVienController::class, 'myProfile'])->name('hoso.index');
    Route::post('/ho-so-ca-nhan', [NhanVienController::class, 'updateProfile'])->name('hoso.update');

    // Lịch phân ca
    Route::get('/phan-ca', [PhanCaController::class, 'index'])->name('phanca.index');

    Route::middleware('manage')->group(function () {
        Route::post('/phan-ca', [PhanCaController::class, 'store'])->name('phanca.store');
        Route::delete('/phan-ca/{id}', [PhanCaController::class, 'destroy'])->name('phanca.destroy');

        // Tab mới: Duyệt nhân viên (Tài khoản đợi duyệt)
        Route::get('/nhan-vien/cho-duyet', [NhanVienController::class, 'pending'])->name('nhanvien.pending');
        Route::post('/nhan-vien/{id}/duyet', [NhanVienController::class, 'approve'])->name('nhanvien.approve');
        Route::delete('/nhan-vien/{id}/tu-choi', [NhanVienController::class, 'reject'])->name('nhanvien.reject');

        // Quản lý nhân viên chính thức (đã duyệt)
        Route::get('/nhan-vien', [NhanVienController::class, 'index'])->name('nhanvien.index');
        Route::get('/nhan-vien/them', [NhanVienController::class, 'create'])->name('nhanvien.create');
        Route::post('/nhan-vien/them', [NhanVienController::class, 'store'])->name('nhanvien.store');
        Route::get('/nhan-vien/{id}', [NhanVienController::class, 'show'])->name('nhanvien.show');
        Route::get('/nhan-vien/{id}/sua', [NhanVienController::class, 'edit'])->name('nhanvien.edit');
        Route::put('/nhan-vien/{id}', [NhanVienController::class, 'update'])->name('nhanvien.update');
        Route::delete('/nhan-vien/{id}', [NhanVienController::class, 'destroy'])->name('nhanvien.destroy');
    });
});
