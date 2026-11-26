<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaLamViecController;
use App\Http\Controllers\ChamCongController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonTuController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\PhanCaController;

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();
    if ((int) $user->trang_thai === 0) {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')
            ->withErrors(['email' => 'Tài khoản của bạn đã bị khóa hoặc từ chối duyệt.']);
    }

    if ((int) $user->trang_thai === 2) {
        return redirect()->route('hoso.index');
    }

    return redirect()->route('dashboard.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login.store');
    Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware(['auth', 'not_locked'])->group(function () {
    Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout');

    // Tổng quan (Dashboard & Báo cáo)
    Route::get('/tong-quan', [DashboardController::class, 'index'])->name('dashboard.index');

    // Hồ sơ cá nhân (Ảnh & CV)
    Route::get('/ho-so-ca-nhan', [NhanVienController::class, 'myProfile'])->name('hoso.index');
    Route::post('/ho-so-ca-nhan', [NhanVienController::class, 'updateProfile'])->name('hoso.update');

    // Lịch phân ca
    Route::get('/phan-ca', [PhanCaController::class, 'index'])->name('phanca.index');

    // Chấm công (Check-in / Check-out)
    Route::get('/cham-cong', [ChamCongController::class, 'index'])->name('chamcong.index');
    Route::post('/cham-cong/{phanCaId}/checkin', [ChamCongController::class, 'checkIn'])->name('chamcong.checkin');
    Route::post('/cham-cong/{phanCaId}/checkout', [ChamCongController::class, 'checkOut'])->name('chamcong.checkout');

    // Đơn từ (Nghỉ phép / Đổi ca / Đi trễ về sớm)
    Route::get('/don-tu', [DonTuController::class, 'index'])->name('dontu.index');
    Route::post('/don-tu', [DonTuController::class, 'store'])->name('dontu.store');
    Route::delete('/don-tu/{id}', [DonTuController::class, 'destroy'])->name('dontu.destroy');

    Route::middleware('manage')->group(function () {
        // Quản lý Phân ca
        Route::post('/phan-ca', [PhanCaController::class, 'store'])->name('phanca.store');
        Route::post('/phan-ca/sao-chep-tuan-truoc', [PhanCaController::class, 'copyPreviousWeek'])->name('phanca.copy');
        Route::get('/phan-ca/xuat-excel', [PhanCaController::class, 'exportExcel'])->name('phanca.export');
        Route::delete('/phan-ca/{id}', [PhanCaController::class, 'destroy'])->name('phanca.destroy');

        // Quản lý Danh mục Ca làm việc
        Route::get('/ca-lam-viec', [CaLamViecController::class, 'index'])->name('calamviec.index');
        Route::post('/ca-lam-viec', [CaLamViecController::class, 'store'])->name('calamviec.store');
        Route::put('/ca-lam-viec/{id}', [CaLamViecController::class, 'update'])->name('calamviec.update');
        Route::delete('/ca-lam-viec/{id}', [CaLamViecController::class, 'destroy'])->name('calamviec.destroy');

        // Quản lý Chấm công & Xuất Excel
        Route::post('/cham-cong/{phanCaId}/cap-nhat', [ChamCongController::class, 'updateManual'])->name('chamcong.manual');
        Route::get('/cham-cong/xuat-excel', [ChamCongController::class, 'exportExcel'])->name('chamcong.export');

        // Duyệt / Từ chối Đơn từ
        Route::post('/don-tu/{id}/trang-thai', [DonTuController::class, 'updateStatus'])->name('dontu.status');

        // Duyệt nhân viên (Tài khoản đợi duyệt)
        Route::get('/nhan-vien/cho-duyet', [NhanVienController::class, 'pending'])->name('nhanvien.pending');
        Route::post('/nhan-vien/{id}/duyet', [NhanVienController::class, 'approve'])->name('nhanvien.approve');
        Route::delete('/nhan-vien/{id}/tu-choi', [NhanVienController::class, 'reject'])->name('nhanvien.reject');

        // Nhập - Xuất Excel Nhân viên
        Route::get('/nhan-vien/xuat-excel', [NhanVienController::class, 'exportExcel'])->name('nhanvien.export');
        Route::get('/nhan-vien/mau-nhap', [NhanVienController::class, 'downloadTemplate'])->name('nhanvien.template');
        Route::post('/nhan-vien/nhap-excel', [NhanVienController::class, 'importExcel'])->name('nhanvien.import');

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
