<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\NhanVien;
use App\Models\PhongBan;
use App\Models\ChucVu;

class NhanVienController extends Controller
{
    /**
     * Danh sách nhân viên chính thức (CHỈ hiển thị tài khoản đã duyệt, không lấy trang_thai = 2)
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $q = trim((string) $request->get('q', ''));
        $role = $request->get('role', '');

        // Chỉ lấy những tài khoản không nằm trong trạng thái Đợi duyệt (2)
        $query = NhanVien::with(['phongban', 'chucvu'])->where('trang_thai', '!=', 2);

        if ($status === 'active') {
            $query->where('trang_thai', 1);
        } elseif ($status === 'inactive') {
            $query->where('trang_thai', 0);
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
            'total' => NhanVien::where('trang_thai', '!=', 2)->count(),
            'active' => NhanVien::where('trang_thai', 1)->count(),
            'inactive' => NhanVien::where('trang_thai', 0)->count(),
            'pending' => NhanVien::where('trang_thai', 2)->count(),
            'admins' => NhanVien::where('trang_thai', 1)->where('vai_tro', 'Admin')->count(),
        ];

        return view('nhanvien.index', compact('danhsachNV', 'stats', 'status', 'q', 'role'));
    }

    /**
     * Tab Duyệt nhân viên (Danh sách tài khoản đăng ký đang đợi duyệt: trang_thai = 2)
     */
    public function pending(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $query = NhanVien::with(['phongban', 'chucvu'])->where('trang_thai', 2);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('ho_ten', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('ma_nv', 'like', "%{$q}%");
            });
        }

        $pendingUsers = $query->orderByDesc('id')->get();
        $phongbans = PhongBan::all();
        $chucvus = ChucVu::all();

        return view('nhanvien.pending', compact('pendingUsers', 'phongbans', 'chucvus', 'q'));
    }

    /**
     * Duyệt tài khoản -> Chuyển thành nhân viên chính thức (trang_thai = 1)
     */
    public function approve(Request $request, $id)
    {
        $nv = NhanVien::findOrFail($id);

        $request->validate([
            'phongban_id' => 'nullable|exists:phongban,id',
            'chucvu_id' => 'nullable|exists:chucvu,id',
            'vai_tro' => 'nullable|in:Admin,Manager,Employee',
        ]);

        if ($request->filled('phongban_id')) {
            $nv->phongban_id = $request->phongban_id;
        }
        if ($request->filled('chucvu_id')) {
            $nv->chucvu_id = $request->chucvu_id;
        }
        if ($request->filled('vai_tro')) {
            $nv->vai_tro = $request->vai_tro;
        }

        $nv->trang_thai = 1; // Chính thức được duyệt
        $nv->save();

        return redirect()->route('nhanvien.pending')
            ->with('status', "Đã duyệt tài khoản {$nv->ho_ten} ({$nv->ma_nv}) vào danh sách nhân viên chính thức!");
    }

    /**
     * Từ chối / Xóa tài khoản đợi duyệt
     */
    public function reject($id)
    {
        $nv = NhanVien::where('trang_thai', 2)->findOrFail($id);

        if ($nv->anh_dai_dien && file_exists(public_path($nv->anh_dai_dien))) {
            @unlink(public_path($nv->anh_dai_dien));
        }
        if ($nv->cv_file && file_exists(public_path($nv->cv_file))) {
            @unlink(public_path($nv->cv_file));
        }

        $nv->delete();

        return redirect()->route('nhanvien.pending')
            ->with('status', 'Đã từ chối và xóa hồ sơ đăng ký.');
    }

    /**
     * Tab mới: Trang Hồ sơ cá nhân & Gửi Ảnh / CV cho người đăng ký
     */
    public function myProfile()
    {
       $nv = NhanVien::with(['phongban', 'chucvu'])->findOrFail(Auth::id());
        $shiftCount = \App\Models\PhanCa::where('nhanvien_id', $nv->id)->count();

        return view('nhanvien.profile', compact('nv', 'shiftCount'));
    }

    /**
     * Xử lý tải lên Ảnh đại diện và File CV từ tab Gửi Ảnh / CV
     */
    public function updateProfile(Request $request)
    {
        $nv = NhanVien::findOrFail(Auth::id());

        // 1. Nếu người dùng bấm nút Remove xóa ảnh đại diện
        if ($request->boolean('remove_avatar')) {
            if ($nv->anh_dai_dien && file_exists(public_path($nv->anh_dai_dien))) {
                @unlink(public_path($nv->anh_dai_dien));
            }
            $nv->anh_dai_dien = null;
            $nv->save();

            return redirect()->route('hoso.index')
                ->with('status', 'Đã xóa ảnh đại diện.');
        }

        // 2. Nếu người dùng đổi mật khẩu ở tab Password
        if ($request->filled('new_password')) {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
            ], [
                'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
                'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            ]);

            if (!Hash::check($request->current_password, $nv->mat_khau)) {
                return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
            }

            $nv->mat_khau = Hash::make($request->new_password);
            $nv->save();

            return redirect()->route('hoso.index')
                ->with('status', 'Đã đổi mật khẩu thành công!');
        }

        // 3. Cập nhật thông tin cá nhân, Ảnh đại diện và File CV ở tab Edit Profile
        $request->validate([
            'ho_ten' => 'required|string|max:100',
            'so_dien_thoai' => 'nullable|string|max:15',
            'anh_dai_dien' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:10240',
        ], [
            'anh_dai_dien.image' => 'File ảnh đại diện phải là định dạng hình ảnh.',
            'anh_dai_dien.max' => 'Ảnh đại diện tối đa 4MB.',
            'cv_file.mimes' => 'File CV chỉ hỗ trợ PDF, DOC, DOCX, JPG, PNG.',
            'cv_file.max' => 'File CV tối đa 10MB.',
        ]);

        $nv->ho_ten = $request->ho_ten;
        $nv->so_dien_thoai = $request->so_dien_thoai;

        if ($request->hasFile('anh_dai_dien')) {
            $avatarDir = public_path('uploads/avatars');
            if (!is_dir($avatarDir)) {
                mkdir($avatarDir, 0775, true);
            }
            if ($nv->anh_dai_dien && file_exists(public_path($nv->anh_dai_dien))) {
                @unlink(public_path($nv->anh_dai_dien));
            }
            $file = $request->file('anh_dai_dien');
            $filename = $nv->ma_nv . '_avatar_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($avatarDir, $filename);
            $nv->anh_dai_dien = 'uploads/avatars/' . $filename;
        }

        if ($request->hasFile('cv_file')) {
            $cvDir = public_path('uploads/cvs');
            if (!is_dir($cvDir)) {
                mkdir($cvDir, 0775, true);
            }
            if ($nv->cv_file && file_exists(public_path($nv->cv_file))) {
                @unlink(public_path($nv->cv_file));
            }
            $file = $request->file('cv_file');
            $filename = $nv->ma_nv . '_CV_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($cvDir, $filename);
            $nv->cv_file = 'uploads/cvs/' . $filename;
        }

        $nv->save();

        return redirect()->route('hoso.index')
            ->with('status', 'Đã cập nhật thông tin cá nhân, Ảnh và CV thành công!');
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
