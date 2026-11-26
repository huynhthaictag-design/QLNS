<?php

namespace App\Http\Controllers;

use App\Mail\ThongBaoNhanSuMail;
use App\Models\ChucVu;
use App\Models\NhanVien;
use App\Models\PhongBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
     * Xuất danh sách nhân viên ra file CSV/Excel (mở trực tiếp bằng Microsoft Excel chuẩn UTF-8)
     */
    public function exportExcel()
    {
        $rows = NhanVien::with(['phongban', 'chucvu'])
            ->where('trang_thai', '!=', 2)
            ->orderBy('id')
            ->get();

        $filename = 'danh-sach-nhan-vien-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Mã NV', 'Họ tên', 'Email', 'Số điện thoại', 'Phòng ban', 'Chức vụ', 'Vai trò', 'Trạng thái']);

            foreach ($rows as $nv) {
                fputcsv($out, [
                    $nv->ma_nv,
                    $nv->ho_ten,
                    $nv->email,
                    $nv->so_dien_thoai ?? '',
                    optional($nv->phongban)->ten_phongban ?? '',
                    optional($nv->chucvu)->ten_chucvu ?? '',
                    $nv->vai_tro,
                    (int) $nv->trang_thai === 1 ? 'Hoạt động' : 'Đã khóa',
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Tải file mẫu CSV để nhập danh sách nhân viên
     */
    public function downloadTemplate()
    {
        $filename = 'mau-nhap-nhan-vien.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['ma_nv', 'ho_ten', 'email', 'so_dien_thoai', 'phongban_id', 'chucvu_id', 'vai_tro', 'mat_khau']);
            fputcsv($out, ['NV010', 'Nguyễn Văn A', 'nva@happytime.vn', '0901234567', '1', '2', 'Employee', '123456']);
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Nhập danh sách nhân viên hàng loạt từ file CSV (Excel Save As CSV UTF-8)
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ], [
            'file_excel.required' => 'Vui lòng chọn tập tin CSV.',
            'file_excel.mimes' => 'Vui lòng sử dụng file CSV (UTF-8) xuất từ Excel hoặc file mẫu.',
        ]);

        $path = $request->file('file_excel')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Không thể đọc tập tin tải lên.');
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'Tập tin trống.');
        }

        // Xóa UTF-8 BOM nếu có ở cột đầu tiên
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
        $header = array_map(function ($h) {
            return strtolower(trim((string) $h));
        }, $header);

        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) {
                continue;
            }
            $assoc = [];
            foreach ($header as $idx => $col) {
                $assoc[$col] = isset($row[$idx]) ? trim((string) $row[$idx]) : '';
            }

            $maNv = $assoc['ma_nv'] ?? '';
            $hoTen = $assoc['ho_ten'] ?? '';
            $email = $assoc['email'] ?? '';

            if ($maNv === '' || $hoTen === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                continue;
            }

            if (NhanVien::where('ma_nv', $maNv)->orWhere('email', $email)->exists()) {
                $skipped++;
                continue;
            }

            $vaiTro = in_array($assoc['vai_tro'] ?? '', ['Admin', 'Manager', 'Employee'], true)
                ? $assoc['vai_tro']
                : 'Employee';

            if ($vaiTro === 'Admin' && !Auth::user()->isAdmin()) {
                $vaiTro = 'Employee';
            }

            $pbId = !empty($assoc['phongban_id']) && PhongBan::where('id', $assoc['phongban_id'])->exists()
                ? (int) $assoc['phongban_id']
                : null;
            $cvId = !empty($assoc['chucvu_id']) && ChucVu::where('id', $assoc['chucvu_id'])->exists()
                ? (int) $assoc['chucvu_id']
                : null;

            $nv = new NhanVien();
            $nv->ma_nv = $maNv;
            $nv->ho_ten = $hoTen;
            $nv->email = $email;
            $nv->so_dien_thoai = $assoc['so_dien_thoai'] ?: null;
            $nv->phongban_id = $pbId;
            $nv->chucvu_id = $cvId;
            $nv->vai_tro = $vaiTro;
            $nv->trang_thai = 1;
            $nv->mat_khau = Hash::make(!empty($assoc['mat_khau']) ? $assoc['mat_khau'] : '123456');
            $nv->save();
            $imported++;
        }

        fclose($handle);

        return redirect()->route('nhanvien.index')
            ->with('status', "Đã nhập thành công {$imported} nhân viên" . ($skipped > 0 ? " (bỏ qua {$skipped} dòng trùng hoặc không hợp lệ)." : '.'));
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
        $nv = NhanVien::where('trang_thai', 2)->findOrFail($id);

        $request->validate([
            'phongban_id' => 'nullable|exists:phongban,id',
            'chucvu_id' => 'nullable|exists:chucvu,id',
            'vai_tro' => 'nullable|in:Admin,Manager,Employee',
        ]);

        if ($request->input('vai_tro') === 'Admin' && !Auth::user()->isAdmin()) {
            return redirect()->route('nhanvien.pending')
                ->with('error', 'Chỉ Admin mới có quyền cấp vai trò Admin.');
        }

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

        // Gửi email thông báo duyệt tài khoản (theo hướng dẫn P08, an toàn khi chưa cấu hình SMTP)
        if ($nv->email) {
            try {
                Mail::to($nv->email)->send(new ThongBaoNhanSuMail(
                    'Tài khoản của bạn đã được phê duyệt - HappyTime QLNS',
                    $nv->ho_ten,
                    'Chúc mừng! Tài khoản nhân viên của bạn trên hệ thống HappyTime QLNS đã được quản trị viên phê duyệt và kích hoạt.',
                    [
                        'Mã nhân viên' => $nv->ma_nv,
                        'Email đăng nhập' => $nv->email,
                        'Vai trò' => $nv->vai_tro,
                    ],
                    route('login'),
                    'Đăng nhập ngay'
                ));
            } catch (\Exception $e) {
                Log::warning('Không thể gửi email duyệt nhân viên: ' . $e->getMessage());
            }
        }

        return redirect()->route('nhanvien.pending')
            ->with('status', "Đã duyệt tài khoản {$nv->ho_ten} ({$nv->ma_nv}) vào danh sách nhân viên chính thức!");
    }

    /**
     * Từ chối / Xóa tài khoản đợi duyệt
     */
    public function reject($id)
    {
        $nv = NhanVien::where('trang_thai', 2)->findOrFail($id);

        $this->deleteUploadedFiles($nv);
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
        if ($request->input('form_action') === 'password' || $request->filled('current_password') || $request->filled('new_password')) {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
            ], [
                'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
                'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
                'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
                'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->with('active_tab', 'password');
            }

            if (!Hash::check($request->current_password, $nv->mat_khau)) {
                return back()
                    ->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.'])
                    ->with('active_tab', 'password');
            }

            $nv->mat_khau = Hash::make($request->new_password);
            $nv->save();

            return redirect()->route('hoso.index')
                ->with('status', 'Đã đổi mật khẩu thành công!')
                ->with('active_tab', 'password');
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

        $safeMaNv = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $nv->ma_nv) ?: ('NV' . $nv->id);

        if ($request->hasFile('anh_dai_dien')) {
            $avatarDir = public_path('uploads/avatars');
            if (!is_dir($avatarDir)) {
                mkdir($avatarDir, 0775, true);
            }
            if ($nv->anh_dai_dien && file_exists(public_path($nv->anh_dai_dien))) {
                @unlink(public_path($nv->anh_dai_dien));
            }
            $file = $request->file('anh_dai_dien');
            $ext = $file->extension() ?: $file->getClientOriginalExtension();
            $filename = $safeMaNv . '_avatar_' . time() . '_' . Str::random(6) . '.' . $ext;
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
            $ext = $file->extension() ?: $file->getClientOriginalExtension();
            $filename = $safeMaNv . '_CV_' . time() . '_' . Str::random(6) . '.' . $ext;
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
            'ma_nv' => 'required|string|max:20|unique:nhanvien,ma_nv',
            'ho_ten' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:nhanvien,email',
            'so_dien_thoai' => 'nullable|string|max:15',
            'phongban_id' => 'nullable|exists:phongban,id',
            'chucvu_id' => 'nullable|exists:chucvu,id',
            'vai_tro' => 'nullable|in:Admin,Manager,Employee',
            'trang_thai' => 'nullable|in:0,1,2',
            'mat_khau' => 'required|min:6',
        ]);

        $vaiTro = $request->vai_tro ?: 'Employee';
        if ($vaiTro === 'Admin' && !Auth::user()->isAdmin()) {
            return back()->withInput()
                ->withErrors(['vai_tro' => 'Chỉ Admin mới có quyền tạo tài khoản Admin.']);
        }

        $nv = new NhanVien();
        $nv->ma_nv = $request->ma_nv;
        $nv->ho_ten = $request->ho_ten;
        $nv->email = $request->email;
        $nv->so_dien_thoai = $request->so_dien_thoai;
        $nv->phongban_id = $request->phongban_id ?: null;
        $nv->chucvu_id = $request->chucvu_id ?: null;
        $nv->vai_tro = $vaiTro;
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

        if ($nv->vai_tro === 'Admin' && !Auth::user()->isAdmin()) {
            return redirect()->route('nhanvien.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa tài khoản Admin.');
        }

        $phongbans = PhongBan::all();
        $chucvus = ChucVu::all();

        return view('nhanvien.edit', compact('nv', 'phongbans', 'chucvus'));
    }

    public function update(Request $request, $id)
    {
        $nv = NhanVien::findOrFail($id);

        if ($nv->vai_tro === 'Admin' && !Auth::user()->isAdmin()) {
            return redirect()->route('nhanvien.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa tài khoản Admin.');
        }

        $request->validate([
            'ma_nv' => 'required|string|max:20|unique:nhanvien,ma_nv,' . $nv->id,
            'ho_ten' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:nhanvien,email,' . $nv->id,
            'so_dien_thoai' => 'nullable|string|max:15',
            'phongban_id' => 'nullable|exists:phongban,id',
            'chucvu_id' => 'nullable|exists:chucvu,id',
            'vai_tro' => 'nullable|in:Admin,Manager,Employee',
            'trang_thai' => 'nullable|in:0,1,2',
            'mat_khau' => 'nullable|min:6',
        ]);

        $vaiTro = $request->vai_tro ?: 'Employee';
        $trangThai = $request->trang_thai !== null ? (int) $request->trang_thai : (int) $nv->trang_thai;

        if ($vaiTro === 'Admin' && !Auth::user()->isAdmin()) {
            return back()->withInput()
                ->withErrors(['vai_tro' => 'Chỉ Admin mới có quyền cấp vai trò Admin.']);
        }

        if ((int) $nv->id === (int) Auth::id()) {
            if ($trangThai !== 1) {
                return back()->withInput()
                    ->withErrors(['trang_thai' => 'Không thể tự khóa hoặc chuyển trạng thái tài khoản đang đăng nhập.']);
            }
            if ($nv->vai_tro === 'Admin' && $vaiTro !== 'Admin') {
                return back()->withInput()
                    ->withErrors(['vai_tro' => 'Không thể tự hạ quyền Admin của chính mình.']);
            }
        }

        $nv->ma_nv = $request->ma_nv;
        $nv->ho_ten = $request->ho_ten;
        $nv->email = $request->email;
        $nv->so_dien_thoai = $request->so_dien_thoai;
        $nv->phongban_id = $request->phongban_id ?: null;
        $nv->chucvu_id = $request->chucvu_id ?: null;
        $nv->vai_tro = $vaiTro;
        $nv->trang_thai = $trangThai;

        if ($request->filled('mat_khau')) {
            $nv->mat_khau = Hash::make($request->mat_khau);
        }

        $nv->save();

        return redirect()->route('nhanvien.index')->with('status', 'Đã cập nhật nhân viên thành công!');
    }

    public function destroy($id)
    {
        $nv = NhanVien::findOrFail($id);

        if ((int) $nv->id === (int) Auth::id()) {
            return redirect()->route('nhanvien.index')
                ->with('error', 'Không thể tự xóa tài khoản đang đăng nhập.');
        }

        if ($nv->vai_tro === 'Admin' && !Auth::user()->isAdmin()) {
            return redirect()->route('nhanvien.index')
                ->with('error', 'Bạn không có quyền xóa tài khoản Admin.');
        }

        $this->deleteUploadedFiles($nv);
        $nv->delete();

        return redirect()->route('nhanvien.index')->with('status', 'Đã xóa nhân viên.');
    }

    private function deleteUploadedFiles(NhanVien $nv): void
    {
        if ($nv->anh_dai_dien && file_exists(public_path($nv->anh_dai_dien))) {
            @unlink(public_path($nv->anh_dai_dien));
        }
        if ($nv->cv_file && file_exists(public_path($nv->cv_file))) {
            @unlink(public_path($nv->cv_file));
        }
    }
}
