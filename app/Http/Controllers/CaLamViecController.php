<?php

namespace App\Http\Controllers;

use App\Models\CaLamViec;
use App\Models\PhanCa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CaLamViecController extends Controller
{
    public function index()
    {
        $caLamViecs = CaLamViec::orderBy('gio_bat_dau')->get()->map(function ($ca) {
            $ca->so_luot_phan = PhanCa::where('calamviec_id', $ca->id)->count();
            return $ca;
        });

        return view('calamviec.index', compact('caLamViecs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ma_ca' => 'required|string|max:20|unique:calamviec,ma_ca',
            'ten_ca' => 'required|string|max:100',
            'gio_bat_dau' => 'required',
            'gio_ket_thuc' => 'required',
            'so_gio_cong' => 'nullable|numeric|min:0.5|max:24',
            'mau_nhan' => 'required|string|max:10',
        ], [
            'ma_ca.required' => 'Vui lòng nhập mã ca.',
            'ma_ca.unique' => 'Mã ca đã tồn tại.',
            'ten_ca.required' => 'Vui lòng nhập tên ca.',
        ]);

        if (empty($data['so_gio_cong'])) {
            $data['so_gio_cong'] = $this->tinhSoGio($data['gio_bat_dau'], $data['gio_ket_thuc']);
        }

        $data['ma_ca'] = strtoupper(trim($data['ma_ca']));
        CaLamViec::create($data);

        return redirect()->route('calamviec.index')
            ->with('status', 'Đã thêm ca làm việc mới thành công.');
    }

    public function update(Request $request, $id)
    {
        $ca = CaLamViec::findOrFail($id);

        $data = $request->validate([
            'ma_ca' => 'required|string|max:20|unique:calamviec,ma_ca,' . $ca->id,
            'ten_ca' => 'required|string|max:100',
            'gio_bat_dau' => 'required',
            'gio_ket_thuc' => 'required',
            'so_gio_cong' => 'nullable|numeric|min:0.5|max:24',
            'mau_nhan' => 'required|string|max:10',
        ]);

        if (empty($data['so_gio_cong'])) {
            $data['so_gio_cong'] = $this->tinhSoGio($data['gio_bat_dau'], $data['gio_ket_thuc']);
        }

        $data['ma_ca'] = strtoupper(trim($data['ma_ca']));
        $ca->update($data);

        return redirect()->route('calamviec.index')
            ->with('status', 'Đã cập nhật ca làm việc.');
    }

    public function destroy($id)
    {
        $ca = CaLamViec::findOrFail($id);
        $inUse = PhanCa::where('calamviec_id', $ca->id)->exists();

        if ($inUse) {
            return redirect()->route('calamviec.index')
                ->with('error', 'Không thể xóa ca đang được gán trong lịch phân ca.');
        }

        $ca->delete();

        return redirect()->route('calamviec.index')
            ->with('status', 'Đã xóa ca làm việc.');
    }

    private function tinhSoGio($start, $end)
    {
        try {
            $s = Carbon::parse($start);
            $e = Carbon::parse($end);
            if ($e->lessThanOrEqualTo($s)) {
                $e->addDay();
            }
            return round($s->diffInMinutes($e) / 60, 1);
        } catch (\Exception $ex) {
            return 8.0;
        }
    }
}
