<?php

namespace App\Http\Controllers;

use App\Mail\ThongBaoNhanSuMail;
use App\Models\CaLamViec;
use App\Models\PhanCa;
use App\Models\PhongBan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class PhanCaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ((int) $user->trang_thai === 2) {
            return redirect()->route('hoso.index')
                ->with('status', 'Tài khoản của bạn đang đợi Admin duyệt nên chưa có lịch phân ca.');
        }

        $weekStart = $this->weekStart($request->get('tuan'));
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $weekStart->copy()->addDays($i);
        }

        $canManage = $user->canManage();
        $phongBanId = $request->get('phongban_id', '');
        $caFilterId = $request->get('calamviec_id', '');
        $keyword = trim((string) $request->get('q', ''));

        $query = User::with(['phongBan', 'chucvu', 'phanCas' => function ($q) use ($weekStart, $weekEnd, $caFilterId) {
            $q->whereBetween('ngay_lam_viec', [
                $weekStart->toDateString(),
                $weekEnd->toDateString(),
            ])->with(['caLamViec', 'chamCong']);

            if ($caFilterId !== '') {
                $q->where('calamviec_id', $caFilterId);
            }
        }])->where('trang_thai', 1)->orderBy('ho_ten');

        if (!$canManage) {
            $query->where('id', $user->id);
        } else {
            if ($phongBanId !== '') {
                $query->where('phongban_id', $phongBanId);
            }
            if ($keyword !== '') {
                $query->where(function ($sub) use ($keyword) {
                    $sub->where('ho_ten', 'like', "%{$keyword}%")
                        ->orWhere('ma_nv', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            }
        }

        $nhanViens = $query->get();
        $allActiveNhanViens = $canManage
            ? User::with('phongBan')->where('trang_thai', 1)->orderBy('ho_ten')->get()
            : $nhanViens;

        $caLamViecs = CaLamViec::orderBy('gio_bat_dau')->get();
        $phongBans = PhongBan::orderBy('ten_phongban')->get();

        // Thống kê nhanh cho tuần đang xem
        $tongCaTuan = 0;
        $tongGioTuan = 0;
        $soNvCoLich = 0;
        $dailyCounts = [];
        foreach ($days as $d) {
            $dailyCounts[$d->toDateString()] = 0;
        }

        foreach ($nhanViens as $nv) {
            if ($nv->phanCas->count() > 0) {
                $soNvCoLich++;
            }
            foreach ($nv->phanCas as $pc) {
                $tongCaTuan++;
                $tongGioTuan += (float) optional($pc->caLamViec)->so_gio_cong;
                $dateKey = Carbon::parse($pc->ngay_lam_viec)->toDateString();
                if (isset($dailyCounts[$dateKey])) {
                    $dailyCounts[$dateKey]++;
                }
            }
        }

        $todayKey = Carbon::today()->toDateString();
        $summary = [
            'tong_ca' => $tongCaTuan,
            'tong_gio' => $tongGioTuan,
            'nv_co_lich' => $soNvCoLich,
            'tong_nv' => $nhanViens->count(),
            'ca_hom_nay' => $dailyCounts[$todayKey] ?? PhanCa::where('ngay_lam_viec', $todayKey)->count(),
        ];

        $prevWeek = $weekStart->copy()->subWeek()->toDateString();
        $nextWeek = $weekStart->copy()->addWeek()->toDateString();

        return view('phanca.index', compact(
            'nhanViens',
            'allActiveNhanViens',
            'caLamViecs',
            'phongBans',
            'days',
            'weekStart',
            'weekEnd',
            'prevWeek',
            'nextWeek',
            'canManage',
            'phongBanId',
            'caFilterId',
            'keyword',
            'summary',
            'dailyCounts',
            'todayKey'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nhanvien_id' => [
                'required',
                Rule::exists('nhanvien', 'id')->where('trang_thai', 1),
            ],
            'calamviec_id' => 'required|exists:calamviec,id',
            'ngay_lam_viec' => 'nullable|date',
            'ngay_lam_viecs' => 'nullable|array',
            'ngay_lam_viecs.*' => 'date',
            'ghi_chu' => 'nullable|string|max:255',
            'send_email' => 'nullable',
        ], [
            'nhanvien_id.exists' => 'Chỉ có thể phân ca cho nhân viên đang hoạt động.',
        ]);

        $dates = [];
        if (!empty($data['ngay_lam_viecs'])) {
            $dates = array_unique($data['ngay_lam_viecs']);
        } elseif (!empty($data['ngay_lam_viec'])) {
            $dates = [$data['ngay_lam_viec']];
        }

        if (empty($dates)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một ngày làm việc.');
        }

        $createdDates = [];
        $skippedCount = 0;

        foreach ($dates as $ngay) {
            $exists = PhanCa::where('nhanvien_id', $data['nhanvien_id'])
                ->where('calamviec_id', $data['calamviec_id'])
                ->where('ngay_lam_viec', $ngay)
                ->exists();

            if ($exists) {
                $skippedCount++;
                continue;
            }

            PhanCa::create([
                'nhanvien_id' => $data['nhanvien_id'],
                'calamviec_id' => $data['calamviec_id'],
                'ngay_lam_viec' => $ngay,
                'ghi_chu' => $data['ghi_chu'] ?? null,
            ]);
            $createdDates[] = Carbon::parse($ngay)->format('d/m/Y');
        }

        if (empty($createdDates)) {
            return back()->with('error', 'Nhân viên đã được phân ca này vào (các) ngày đã chọn.');
        }

        // Gửi email thông báo lịch làm việc nếu quản lý chọn
        if ($request->boolean('send_email')) {
            $nv = User::find($data['nhanvien_id']);
            $ca = CaLamViec::find($data['calamviec_id']);
            if ($nv && $nv->email && $ca) {
                try {
                    Mail::to($nv->email)->send(new ThongBaoNhanSuMail(
                        'Thông báo lịch phân ca mới - HappyTime QLNS',
                        $nv->ho_ten,
                        'Bạn vừa được quản lý phân công lịch làm việc mới trên hệ thống HappyTime.',
                        [
                            'Ca làm việc' => $ca->ten_ca . ' (' . Carbon::parse($ca->gio_bat_dau)->format('H:i') . ' - ' . Carbon::parse($ca->gio_ket_thuc)->format('H:i') . ')',
                            'Ngày áp dụng' => implode(', ', $createdDates),
                            'Ghi chú' => $data['ghi_chu'] ?: 'Không có',
                        ],
                        route('phanca.index'),
                        'Xem lịch làm của tôi'
                    ));
                } catch (\Exception $e) {
                    Log::warning('Không thể gửi email phân ca: ' . $e->getMessage());
                }
            }
        }

        $msg = 'Đã phân công ' . count($createdDates) . ' ca làm việc thành công.';
        if ($skippedCount > 0) {
            $msg .= " (Bỏ qua {$skippedCount} ngày bị trùng ca.)";
        }

        return back()->with('status', $msg);
    }

    public function copyPreviousWeek(Request $request)
    {
        $weekStart = $this->weekStart($request->input('tuan'));
        $prevStart = $weekStart->copy()->subWeek();
        $prevEnd = $prevStart->copy()->endOfWeek(Carbon::SUNDAY);

        $prevShifts = PhanCa::whereBetween('ngay_lam_viec', [
            $prevStart->toDateString(),
            $prevEnd->toDateString(),
        ])->whereHas('nhanVien', function ($q) {
            $q->where('trang_thai', 1);
        })->get();

        if ($prevShifts->isEmpty()) {
            return back()->with('error', 'Tuần trước không có dữ liệu phân ca để sao chép.');
        }

        $copied = 0;
        foreach ($prevShifts as $shift) {
            $targetDate = Carbon::parse($shift->ngay_lam_viec)->addWeek()->toDateString();
            $exists = PhanCa::where('nhanvien_id', $shift->nhanvien_id)
                ->where('calamviec_id', $shift->calamviec_id)
                ->where('ngay_lam_viec', $targetDate)
                ->exists();

            if (!$exists) {
                PhanCa::create([
                    'nhanvien_id' => $shift->nhanvien_id,
                    'calamviec_id' => $shift->calamviec_id,
                    'ngay_lam_viec' => $targetDate,
                    'ghi_chu' => $shift->ghi_chu,
                ]);
                $copied++;
            }
        }

        if ($copied === 0) {
            return back()->with('status', 'Toàn bộ lịch từ tuần trước đã tồn tại trong tuần này.');
        }

        return back()->with('status', "Đã sao chép {$copied} lượt phân ca từ tuần trước sang tuần này.");
    }

    public function exportExcel(Request $request)
    {
        $weekStart = $this->weekStart($request->get('tuan'));
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $weekStart->copy()->addDays($i);
        }

        $nhanViens = User::with(['phongBan', 'phanCas' => function ($q) use ($weekStart, $weekEnd) {
            $q->whereBetween('ngay_lam_viec', [
                $weekStart->toDateString(),
                $weekEnd->toDateString(),
            ])->with('caLamViec');
        }])->where('trang_thai', 1)->orderBy('ho_ten')->get();

        $dayNames = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];
        $filename = 'lich-phan-ca-' . $weekStart->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($nhanViens, $days, $dayNames) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $headers = ['Mã NV', 'Họ tên', 'Phòng ban'];
            foreach ($days as $i => $d) {
                $headers[] = $dayNames[$i] . ' (' . $d->format('d/m') . ')';
            }
            $headers[] = 'Tổng số ca';
            $headers[] = 'Tổng giờ công';
            fputcsv($out, $headers);

            foreach ($nhanViens as $nv) {
                $byDate = $nv->phanCas->groupBy(function ($pc) {
                    return Carbon::parse($pc->ngay_lam_viec)->toDateString();
                });

                $row = [
                    $nv->ma_nv,
                    $nv->ho_ten,
                    optional($nv->phongBan)->ten_phongban ?? '',
                ];

                $tongGio = 0;
                foreach ($days as $d) {
                    $items = $byDate->get($d->toDateString(), collect());
                    $labels = [];
                    foreach ($items as $pc) {
                        $ca = $pc->caLamViec;
                        if ($ca) {
                            $labels[] = $ca->ten_ca . ' (' . Carbon::parse($ca->gio_bat_dau)->format('H:i') . '-' . Carbon::parse($ca->gio_ket_thuc)->format('H:i') . ')';
                            $tongGio += (float) $ca->so_gio_cong;
                        }
                    }
                    $row[] = empty($labels) ? 'Nghỉ' : implode(' | ', $labels);
                }

                $row[] = $nv->phanCas->count();
                $row[] = $tongGio;
                fputcsv($out, $row);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function destroy($id)
    {
        $phanCa = PhanCa::findOrFail($id);
        $phanCa->delete();

        return back()->with('status', 'Đã gỡ ca làm việc.');
    }

    private function weekStart($tuan)
    {
        try {
            $date = $tuan ? Carbon::parse($tuan) : Carbon::today();
        } catch (\Exception $e) {
            $date = Carbon::today();
        }

        return $date->startOfWeek(Carbon::MONDAY);
    }
}
