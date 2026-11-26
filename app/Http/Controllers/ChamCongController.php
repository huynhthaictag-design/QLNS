<?php

namespace App\Http\Controllers;

use App\Models\ChamCong;
use App\Models\PhanCa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChamCongController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ((int) $user->trang_thai === 2) {
            return redirect()->route('hoso.index')
                ->with('status', 'Tài khoản đang đợi duyệt nên chưa thể chấm công.');
        }

        $canManage = $user->canManage();
        $today = Carbon::today()->toDateString();
        $thang = $request->get('thang', Carbon::today()->format('Y-m'));
        $trangThai = $request->get('trang_thai', '');
        $nhanVienId = $request->get('nhanvien_id', '');

        try {
            $monthDate = Carbon::createFromFormat('Y-m', $thang)->startOfMonth();
        } catch (\Exception $e) {
            $monthDate = Carbon::today()->startOfMonth();
            $thang = $monthDate->format('Y-m');
        }
        $monthEnd = $monthDate->copy()->endOfMonth();

        // Các ca làm việc hôm nay của chính người đang đăng nhập (để Check-in / Check-out nhanh)
        $caHomNay = PhanCa::with(['caLamViec', 'chamCong'])
            ->where('nhanvien_id', $user->id)
            ->where('ngay_lam_viec', $today)
            ->orderBy('calamviec_id')
            ->get();

        // Danh sách lịch sử chấm công / phân ca trong tháng
        $query = PhanCa::with(['nhanVien.phongBan', 'caLamViec', 'chamCong'])
            ->whereBetween('ngay_lam_viec', [$monthDate->toDateString(), $monthEnd->toDateString()])
            ->orderByDesc('ngay_lam_viec')
            ->orderBy('nhanvien_id');

        if (!$canManage) {
            $query->where('nhanvien_id', $user->id);
        } elseif ($nhanVienId !== '') {
            $query->where('nhanvien_id', $nhanVienId);
        }

        if ($trangThai === 'ChuaCham') {
            $query->doesntHave('chamCong');
        } elseif ($trangThai !== '') {
            $query->whereHas('chamCong', function ($q) use ($trangThai) {
                $q->where('trang_thai', $trangThai);
            });
        }

        $danhSachPhanCa = $query->get();

        // Thống kê nhanh
        $stats = [
            'tong_ca' => $danhSachPhanCa->count(),
            'dung_gio' => $danhSachPhanCa->filter(function ($pc) {
                return optional($pc->chamCong)->trang_thai === 'DungGio';
            })->count(),
            'di_tre' => $danhSachPhanCa->filter(function ($pc) {
                return optional($pc->chamCong)->trang_thai === 'DiTre';
            })->count(),
            've_som_vang' => $danhSachPhanCa->filter(function ($pc) {
                return in_array(optional($pc->chamCong)->trang_thai, ['VeSom', 'VangMat'], true);
            })->count(),
        ];

        $nhanViens = $canManage
            ? User::where('trang_thai', 1)->orderBy('ho_ten')->get()
            : collect();

        return view('chamcong.index', compact(
            'canManage',
            'caHomNay',
            'danhSachPhanCa',
            'stats',
            'nhanViens',
            'thang',
            'trangThai',
            'nhanVienId',
            'today'
        ));
    }

    public function checkIn($phanCaId)
    {
        $user = Auth::user();
        $phanCa = PhanCa::with('caLamViec')->findOrFail($phanCaId);

        if ((int) $phanCa->nhanvien_id !== (int) $user->id && !$user->canManage()) {
            abort(403, 'Bạn không có quyền chấm công cho ca này.');
        }

        $chamCong = ChamCong::where('phanca_id', $phanCa->id)->first();
        if ($chamCong && $chamCong->gio_checkin) {
            return back()->with('error', 'Ca làm việc này đã được Check-in trước đó.');
        }

        $now = Carbon::now();
        $shiftStart = Carbon::parse($phanCa->ngay_lam_viec . ' ' . optional($phanCa->caLamViec)->gio_bat_dau);
        $trangThai = $now->greaterThan($shiftStart->copy()->addMinutes(15)) ? 'DiTre' : 'DungGio';

        ChamCong::updateOrCreate(
            ['phanca_id' => $phanCa->id],
            [
                'gio_checkin' => $now->format('Y-m-d H:i:s'),
                'trang_thai' => $trangThai,
            ]
        );

        return back()->with('status', 'Check-in thành công lúc ' . $now->format('H:i:s') . ' (' . ($trangThai === 'DiTre' ? 'Đi trễ' : 'Đúng giờ') . ').');
    }

    public function checkOut($phanCaId)
    {
        $user = Auth::user();
        $phanCa = PhanCa::with('caLamViec')->findOrFail($phanCaId);

        if ((int) $phanCa->nhanvien_id !== (int) $user->id && !$user->canManage()) {
            abort(403, 'Bạn không có quyền chấm công cho ca này.');
        }

        $chamCong = ChamCong::where('phanca_id', $phanCa->id)->first();
        if (!$chamCong || !$chamCong->gio_checkin) {
            return back()->with('error', 'Vui lòng Check-in trước khi Check-out.');
        }

        if ($chamCong->gio_checkout) {
            return back()->with('error', 'Ca làm việc này đã được Check-out trước đó.');
        }

        $now = Carbon::now();
        $shiftEnd = Carbon::parse($phanCa->ngay_lam_viec . ' ' . optional($phanCa->caLamViec)->gio_ket_thuc);

        $trangThai = $chamCong->trang_thai;
        if ($now->lessThan($shiftEnd->copy()->subMinutes(15)) && $trangThai === 'DungGio') {
            $trangThai = 'VeSom';
        }

        $chamCong->update([
            'gio_checkout' => $now->format('Y-m-d H:i:s'),
            'trang_thai' => $trangThai,
        ]);

        return back()->with('status', 'Check-out thành công lúc ' . $now->format('H:i:s') . '.');
    }

    public function updateManual(Request $request, $phanCaId)
    {
        $phanCa = PhanCa::findOrFail($phanCaId);

        $data = $request->validate([
            'gio_checkin' => 'nullable|date',
            'gio_checkout' => 'nullable|date',
            'trang_thai' => 'required|in:DungGio,DiTre,VeSom,VangMat',
        ]);

        ChamCong::updateOrCreate(
            ['phanca_id' => $phanCa->id],
            [
                'gio_checkin' => !empty($data['gio_checkin']) ? Carbon::parse($data['gio_checkin'])->format('Y-m-d H:i:s') : null,
                'gio_checkout' => !empty($data['gio_checkout']) ? Carbon::parse($data['gio_checkout'])->format('Y-m-d H:i:s') : null,
                'trang_thai' => $data['trang_thai'],
            ]
        );

        return back()->with('status', 'Đã cập nhật dữ liệu chấm công.');
    }

    public function exportExcel(Request $request)
    {
        $thang = $request->get('thang', Carbon::today()->format('Y-m'));
        try {
            $monthDate = Carbon::createFromFormat('Y-m', $thang)->startOfMonth();
        } catch (\Exception $e) {
            $monthDate = Carbon::today()->startOfMonth();
            $thang = $monthDate->format('Y-m');
        }
        $monthEnd = $monthDate->copy()->endOfMonth();

        $rows = PhanCa::with(['nhanVien.phongBan', 'caLamViec', 'chamCong'])
            ->whereBetween('ngay_lam_viec', [$monthDate->toDateString(), $monthEnd->toDateString()])
            ->orderBy('ngay_lam_viec')
            ->orderBy('nhanvien_id')
            ->get();

        $filename = 'bang-cham-cong-' . $thang . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM để Microsoft Excel hiển thị chuẩn tiếng Việt
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['STT', 'Ngày làm việc', 'Mã NV', 'Họ tên', 'Phòng ban', 'Ca làm việc', 'Giờ công', 'Check-in', 'Check-out', 'Trạng thái']);

            $statusMap = [
                'DungGio' => 'Đúng giờ',
                'DiTre' => 'Đi trễ',
                'VeSom' => 'Về sớm',
                'VangMat' => 'Vắng mặt',
            ];

            foreach ($rows as $i => $pc) {
                $cc = $pc->chamCong;
                fputcsv($out, [
                    $i + 1,
                    Carbon::parse($pc->ngay_lam_viec)->format('d/m/Y'),
                    optional($pc->nhanVien)->ma_nv,
                    optional($pc->nhanVien)->ho_ten,
                    optional(optional($pc->nhanVien)->phongBan)->ten_phongban ?? '',
                    optional($pc->caLamViec)->ten_ca,
                    optional($pc->caLamViec)->so_gio_cong,
                    $cc && $cc->gio_checkin ? Carbon::parse($cc->gio_checkin)->format('H:i:s d/m/Y') : '',
                    $cc && $cc->gio_checkout ? Carbon::parse($cc->gio_checkout)->format('H:i:s d/m/Y') : '',
                    $cc ? ($statusMap[$cc->trang_thai] ?? $cc->trang_thai) : 'Chưa chấm công',
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
