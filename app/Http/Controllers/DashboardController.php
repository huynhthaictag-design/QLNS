<?php

namespace App\Http\Controllers;

use App\Models\CaLamViec;
use App\Models\ChamCong;
use App\Models\DonTu;
use App\Models\NhanVien;
use App\Models\PhanCa;
use App\Models\PhongBan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ((int) $user->trang_thai === 2) {
            return redirect()->route('hoso.index')
                ->with('status', 'Tài khoản của bạn đang đợi Admin duyệt.');
        }

        $today = Carbon::today()->toDateString();
        $weekStart = Carbon::today()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd = Carbon::today()->endOfWeek(Carbon::SUNDAY)->toDateString();
        $canManage = $user->canManage();

        if ($canManage) {
            $stats = [
                'nhanvien_active' => NhanVien::where('trang_thai', 1)->count(),
                'nhanvien_pending' => NhanVien::where('trang_thai', 2)->count(),
                'phanca_tuan' => PhanCa::whereBetween('ngay_lam_viec', [$weekStart, $weekEnd])->count(),
                'phanca_homnay' => PhanCa::where('ngay_lam_viec', $today)->count(),
                'dontu_choduyet' => DonTu::where('trang_thai', 'ChoDuyet')->count(),
                'chamcong_homnay' => ChamCong::whereHas('phanCa', function ($q) use ($today) {
                    $q->where('ngay_lam_viec', $today);
                })->count(),
                'calamviec_count' => CaLamViec::count(),
            ];

            $lichHomNay = PhanCa::with(['nhanVien.phongBan', 'caLamViec', 'chamCong'])
                ->where('ngay_lam_viec', $today)
                ->orderBy('calamviec_id')
                ->get();

            $donChoDuyet = DonTu::with('nhanVien.phongBan')
                ->where('trang_thai', 'ChoDuyet')
                ->orderByDesc('id')
                ->take(6)
                ->get();

            $thongKePhongBan = PhongBan::all()->map(function ($pb) {
                $pb->so_nhan_vien = NhanVien::where('phongban_id', $pb->id)
                    ->where('trang_thai', 1)
                    ->count();
                return $pb;
            });

            return view('dashboard.index', compact(
                'canManage',
                'stats',
                'lichHomNay',
                'donChoDuyet',
                'thongKePhongBan',
                'today',
                'weekStart',
                'weekEnd'
            ));
        }

        // Dashboard dành cho Nhân viên (Employee)
        $lichHomNay = PhanCa::with(['caLamViec', 'chamCong'])
            ->where('nhanvien_id', $user->id)
            ->where('ngay_lam_viec', $today)
            ->orderBy('calamviec_id')
            ->get();

        $lichTuan = PhanCa::with('caLamViec')
            ->where('nhanvien_id', $user->id)
            ->whereBetween('ngay_lam_viec', [$weekStart, $weekEnd])
            ->get();

        $tongGioTuan = $lichTuan->sum(function ($pc) {
            return (float) optional($pc->caLamViec)->so_gio_cong;
        });

        $donCuaToi = DonTu::where('nhanvien_id', $user->id)
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $stats = [
            'phanca_tuan' => $lichTuan->count(),
            'tong_gio_tuan' => $tongGioTuan,
            'phanca_homnay' => $lichHomNay->count(),
            'dontu_choduyet' => DonTu::where('nhanvien_id', $user->id)->where('trang_thai', 'ChoDuyet')->count(),
            'chamcong_thang' => ChamCong::whereHas('phanCa', function ($q) use ($user) {
                $q->where('nhanvien_id', $user->id)
                    ->whereMonth('ngay_lam_viec', Carbon::today()->month)
                    ->whereYear('ngay_lam_viec', Carbon::today()->year);
            })->count(),
        ];

        return view('dashboard.index', compact(
            'canManage',
            'stats',
            'lichHomNay',
            'donCuaToi',
            'today',
            'weekStart',
            'weekEnd'
        ));
    }
}
