<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhanCaController;
use App\Http\Controllers\NhanVienController;


Route::get('/', function () {
    return redirect()->route('phanca.index');
});

Route::get('/phan-ca', [PhanCaController::class, 'index'])->name('phanca.index');
Route::get('/nhan-vien', [NhanVienController::class, 'index'])->name('nhanvien.index');
Route::get('/nhan-vien/them', [NhanVienController::class, 'create'])->name('nhanvien.create');
Route::post('/nhan-vien/them', [NhanVienController::class, 'store'])->name('nhanvien.store');
