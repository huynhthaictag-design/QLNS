<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\NhanVien;
use App\Models\PhongBan;
use App\Models\ChucVu;

class NhanVienController extends Controller
{
    public function index()
    {
        // Lấy danh sách nhân viên kèm theo thông tin từ bảng phongban và chucvu
        $danhsachNV = NhanVien::with(['phongban', 'chucvu'])->get();

        return view('nhanvien.index', compact('danhsachNV'));
    }

public function create()
    {
        // Lấy danh sách để đổ vào thẻ <select>
        $phongbans = PhongBan::all();
        $chucvus = ChucVu::all();

        return view('nhanvien.create', compact('phongbans', 'chucvus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_nv' => 'required|unique:nhanvien',
            'ho_ten' => 'required',
            'email' => 'required|email|unique:nhanvien',
        ]);

        $nv = new NhanVien();
        $nv->ma_nv = $request->ma_nv;
        $nv->ho_ten = $request->ho_ten;
        $nv->email = $request->email;
        $nv->phongban_id = $request->phongban_id;
        $nv->chucvu_id = $request->chucvu_id;
        $nv->vai_tro = $request->vai_tro;
        $nv->trang_thai = 1;
        $nv->mat_khau = Hash::make($request->mat_khau);

        $nv->save();

        return redirect()->route('nhanvien.index')->with('status', 'Đã thêm nhân viên thành công!');
    }
}
