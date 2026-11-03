<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhanCaController;
use App\Http\Controllers\NhanVienController;

Route::get('/', function () {
    return redirect()->route('nhanvien.index');
});

Route::get('/phan-ca', [PhanCaController::class, 'index'])->name('phanca.index');

Route::get('/nhan-vien', [NhanVienController::class, 'index'])->name('nhanvien.index');
Route::get('/nhan-vien/them', [NhanVienController::class, 'create'])->name('nhanvien.create');
Route::post('/nhan-vien/them', [NhanVienController::class, 'store'])->name('nhanvien.store');
Route::get('/nhan-vien/{id}', [NhanVienController::class, 'show'])->name('nhanvien.show');
Route::get('/nhan-vien/{id}/sua', [NhanVienController::class, 'edit'])->name('nhanvien.edit');
Route::put('/nhan-vien/{id}', [NhanVienController::class, 'update'])->name('nhanvien.update');
Route::delete('/nhan-vien/{id}', [NhanVienController::class, 'destroy'])->name('nhanvien.destroy');
