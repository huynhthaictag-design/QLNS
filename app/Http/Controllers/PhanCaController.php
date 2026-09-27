<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CaLamViec;
use Illuminate\Http\Request;

class PhanCaController extends Controller
{
    public function index()
    {
        // 1. Lấy danh sách tất cả nhân viên kèm lịch phân ca và thông tin ca làm việc
        $nhanViens = User::with(['phanCas.caLamViec', 'phongBan'])->get();

        // 2. Lấy danh mục các ca làm việc để hiển thị chú thích màu sắc
        $caLamViecs = CaLamViec::all();

        // 3. Trả dữ liệu sang giao diện Blade
        return view('phanca.index', compact('nhanViens', 'caLamViecs'));
    }
}
