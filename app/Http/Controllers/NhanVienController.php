<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\NhanVien;
use App\Models\PhongBan;
use App\Models\ChucVu;

class NhanVienController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $q = trim((string) $request->get('q', ''));
        $role = $request->get('role', '');

        $query = NhanVien::with(['phongban', 'chucvu']);

        if ($status === 'active') {
            $query->where('trang_thai', 1);
        } elseif ($status === 'inactive') {
            $query->where('trang_thai', 0);
        } elseif ($status === 'pending') {
            $query->where('trang_thai', 2);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('ho_ten', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('ma_nv', 'like', "%{$q}%");
            });
        }

        if ($role !== '') {
            $query->where('vai_tro', $role);
        }

        $danhsachNV = $query->orderByDesc('id')->get();

        $stats = [
            'total' => NhanVien::count(),
            'active' => NhanVien::where('trang_thai', 1)->count(),
            'inactive' => NhanVien::where('trang_thai', 0)->count(),
            'pending' => NhanVien::where('trang_thai', 2)->count(),
            'admins' => NhanVien::where('vai_tro', 'Admin')->count(),
        ];

        return view('nhanvien.index', compact('danhsachNV', 'stats', 'status', 'q', 'role'));
    }

    public function create()
    {
        $phongbans = PhongBan::all();
        $chucvus = ChucVu::all();

        return view('nhanvien.create', compact('phongbans', 'chucvus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ma_nv' => 'required|unique:nhanvien,ma_nv',
            'ho_ten' => 'required',
            'email' => 'required|email|unique:nhanvien,email',
            'mat_khau' => 'required|min:6',
        ]);

        $nv = new NhanVien();
        $nv->ma_nv = $request->ma_nv;
        $nv->ho_ten = $request->ho_ten;
        $nv->email = $request->email;
        $nv->so_dien_thoai = $request->so_dien_thoai;
        $nv->phongban_id = $request->phongban_id ?: null;
        $nv->chucvu_id = $request->chucvu_id ?: null;
        $nv->vai_tro = $request->vai_tro ?: 'Employee';
        $nv->trang_thai = $request->trang_thai !== null ? (int) $request->trang_thai : 1;
        $nv->mat_khau = Hash::make($request->mat_khau);
        $nv->save();

        return redirect()->route('nhanvien.index')->with('status', 'Đã thêm nhân viên thành công!');
    }

    public function show($id)
    {
        $nv = NhanVien::with(['phongban', 'chucvu'])->findOrFail($id);

        return view('nhanvien.show', compact('nv'));
    }

    public function edit($id)
    {
        $nv = NhanVien::findOrFail($id);
        $phongbans = PhongBan::all();
        $chucvus = ChucVu::all();

        return view('nhanvien.edit', compact('nv', 'phongbans', 'chucvus'));
    }

    public function update(Request $request, $id)
    {
        $nv = NhanVien::findOrFail($id);

        $request->validate([
            'ma_nv' => 'required|unique:nhanvien,ma_nv,' . $nv->id,
            'ho_ten' => 'required',
            'email' => 'required|email|unique:nhanvien,email,' . $nv->id,
            'mat_khau' => 'nullable|min:6',
        ]);

        $nv->ma_nv = $request->ma_nv;
        $nv->ho_ten = $request->ho_ten;
        $nv->email = $request->email;
        $nv->so_dien_thoai = $request->so_dien_thoai;
        $nv->phongban_id = $request->phongban_id ?: null;
        $nv->chucvu_id = $request->chucvu_id ?: null;
        $nv->vai_tro = $request->vai_tro ?: 'Employee';
        $nv->trang_thai = $request->trang_thai !== null ? (int) $request->trang_thai : $nv->trang_thai;

        if ($request->filled('mat_khau')) {
            $nv->mat_khau = Hash::make($request->mat_khau);
        }

        $nv->save();

        return redirect()->route('nhanvien.index')->with('status', 'Đã cập nhật nhân viên thành công!');
    }

    public function destroy($id)
    {
        $nv = NhanVien::findOrFail($id);
        $nv->delete();

        return redirect()->route('nhanvien.index')->with('status', 'Đã xóa nhân viên.');
    }
}
