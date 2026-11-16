<?php

namespace App\Http\Controllers;

use App\Models\CaLamViec;
use App\Models\PhanCa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $query = User::with(['phongBan', 'phanCas' => function ($q) use ($weekStart, $weekEnd) {
            $q->whereBetween('ngay_lam_viec', [
                $weekStart->toDateString(),
                $weekEnd->toDateString(),
            ])->with('caLamViec');
        }])->where('trang_thai', 1)->orderBy('ho_ten');

        if (!$canManage) {
            $query->where('id', $user->id);
        }

        $nhanViens = $query->get();
        $caLamViecs = CaLamViec::orderBy('gio_bat_dau')->get();

        $prevWeek = $weekStart->copy()->subWeek()->toDateString();
        $nextWeek = $weekStart->copy()->addWeek()->toDateString();

        return view('phanca.index', compact(
            'nhanViens',
            'caLamViecs',
            'days',
            'weekStart',
            'weekEnd',
            'prevWeek',
            'nextWeek',
            'canManage'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nhanvien_id' => 'required|exists:nhanvien,id',
            'calamviec_id' => 'required|exists:calamviec,id',
            'ngay_lam_viec' => 'required|date',
            'ghi_chu' => 'nullable|string|max:255',
        ]);

        $exists = PhanCa::where('nhanvien_id', $data['nhanvien_id'])
            ->where('calamviec_id', $data['calamviec_id'])
            ->where('ngay_lam_viec', $data['ngay_lam_viec'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Nhân viên đã được phân ca này trong ngày đó.');
        }

        PhanCa::create($data);

        return back()->with('status', 'Đã phân công lịch làm việc.');
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
