<?php

namespace App\Http\Controllers;

use App\Mail\ThongBaoNhanSuMail;
use App\Models\DonTu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DonTuController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ((int) $user->trang_thai === 2) {
            return redirect()->route('hoso.index')
                ->with('status', 'Tài khoản đang đợi duyệt nên chưa thể gửi đơn từ.');
        }

        $canManage = $user->canManage();
        $trangThai = $request->get('trang_thai', 'all');
        $loaiDon = $request->get('loai_don', '');

        $baseQuery = DonTu::with(['nhanVien.phongBan', 'nguoiDuyet']);
        if (!$canManage) {
            $baseQuery->where('nhanvien_id', $user->id);
        }

        $allForStats = (clone $baseQuery)->get();
        $stats = [
            'total' => $allForStats->count(),
            'choduyet' => $allForStats->where('trang_thai', 'ChoDuyet')->count(),
            'daduyet' => $allForStats->where('trang_thai', 'DaDuyet')->count(),
            'tuchoi' => $allForStats->where('trang_thai', 'TuChoi')->count(),
        ];

        if (in_array($trangThai, ['ChoDuyet', 'DaDuyet', 'TuChoi'], true)) {
            $baseQuery->where('trang_thai', $trangThai);
        }

        if (in_array($loaiDon, ['NghiPhep', 'DoiCa', 'DiTreVeSom'], true)) {
            $baseQuery->where('loai_don', $loaiDon);
        }

        $danhSachDon = $baseQuery->orderByDesc('id')->get();

        return view('dontu.index', compact(
            'canManage',
            'danhSachDon',
            'stats',
            'trangThai',
            'loaiDon'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'loai_don' => 'required|in:NghiPhep,DoiCa,DiTreVeSom',
            'ngay_ap_dung' => 'required|date',
            'ly_do' => 'required|string|min:5|max:1000',
        ], [
            'loai_don.required' => 'Vui lòng chọn loại đơn.',
            'ngay_ap_dung.required' => 'Vui lòng chọn ngày áp dụng.',
            'ly_do.required' => 'Vui lòng nhập lý do.',
            'ly_do.min' => 'Lý do cần ít nhất 5 ký tự.',
        ]);

        DonTu::create([
            'nhanvien_id' => $user->id,
            'loai_don' => $data['loai_don'],
            'ngay_nop' => Carbon::today()->toDateString(),
            'ngay_ap_dung' => $data['ngay_ap_dung'],
            'ly_do' => $data['ly_do'],
            'trang_thai' => 'ChoDuyet',
            'nguoi_duyet_id' => null,
        ]);

        return redirect()->route('dontu.index')
            ->with('status', 'Đã gửi đơn từ thành công, đang chờ quản lý duyệt.');
    }

    public function updateStatus(Request $request, $id)
    {
        $don = DonTu::with('nhanVien')->findOrFail($id);

        $data = $request->validate([
            'trang_thai' => 'required|in:DaDuyet,TuChoi',
        ]);

        $don->trang_thai = $data['trang_thai'];
        $don->nguoi_duyet_id = Auth::id();
        $don->save();

        // Gửi email thông báo cho nhân viên (an toàn nếu chưa cấu hình SMTP)
        if ($don->nhanVien && $don->nhanVien->email) {
            try {
                $statusText = $data['trang_thai'] === 'DaDuyet' ? 'ĐÃ ĐƯỢC DUYỆT' : 'ĐÃ BỊ TỪ CHỐI';
                Mail::to($don->nhanVien->email)->send(new ThongBaoNhanSuMail(
                    'Thông báo kết quả xử lý đơn từ - HappyTime QLNS',
                    $don->nhanVien->ho_ten,
                    "Đơn {$don->loaiDonLabel()} của bạn đã được quản lý xử lý với kết quả: {$statusText}.",
                    [
                        'Loại đơn' => $don->loaiDonLabel(),
                        'Ngày áp dụng' => Carbon::parse($don->ngay_ap_dung)->format('d/m/Y'),
                        'Trạng thái' => $don->trangThaiLabel(),
                        'Người duyệt' => Auth::user()->ho_ten,
                    ],
                    route('dontu.index'),
                    'Xem chi tiết đơn từ'
                ));
            } catch (\Exception $e) {
                Log::warning('Không thể gửi email thông báo đơn từ: ' . $e->getMessage());
            }
        }

        $msg = $data['trang_thai'] === 'DaDuyet'
            ? 'Đã duyệt đơn thành công.'
            : 'Đã từ chối đơn.';

        return back()->with('status', $msg);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $don = DonTu::findOrFail($id);

        if (!$user->canManage() && ((int) $don->nhanvien_id !== (int) $user->id || $don->trang_thai !== 'ChoDuyet')) {
            return back()->with('error', 'Bạn chỉ có thể hủy đơn của mình khi đang ở trạng thái Chờ duyệt.');
        }

        $don->delete();

        return back()->with('status', 'Đã xóa đơn từ.');
    }
}
