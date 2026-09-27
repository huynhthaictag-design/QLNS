<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhanCaController;

Route::get('/', function () {
    return redirect()->route('phanca.index');
});

Route::get('/phan-ca', [PhanCaController::class, 'index'])->name('phanca.index');
