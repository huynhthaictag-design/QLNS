@extends('layouts.app')
@section('title', 'Chấm công')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="mb-1 fw-bold">{{ $canManage ? 'Quản lý Chấm công' : 'Chấm công của tôi' }}</h3>
        <p class="text-muted small mb-0">Ghi nhận Check-in / Check-out theo ca làm việc và theo dõi lịch sử công hàng tháng</p>
    </div>
    @if($canManage)
        <a href="{{ route('chamcong.export', ['thang' => $thang]) }}" class="btn btn-success rounded-pill fw-semibold px-3">
            <i class="fas fa-file-excel me-1"></i> Xuất Excel tháng {{ \Carbon\Carbon::createFromFormat('Y-m', $thang)->format('m/Y') }}
        </a>
    @endif
</div>

{{-- KHU VỰC CHECK-IN / CHECK-OUT HÔM NAY --}}
<div class="panel p-4 mb-4" style="background:linear-gradient(135deg,#ffffff 0%,#faf7ff 100%);">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-0"><i class="fas fa-fingerprint text-primary me-2"></i>Chấm công hôm nay ({{ \Carbon\Carbon::parse($today)->format('d/m/Y') }})</h5>
            <div class="muted">Hệ thống tự động đối chiếu giờ thực tế với khung giờ ca được phân</div>
        </div>
        <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2 fw-bold" id="liveClock">--:--:--</span>
    </div>

    <div class="row g-3">
        @forelse($caHomNay as $pc)
            <div class="col-md-6">
                <div class="p-3 rounded-4 bg-white border d-flex justify-content-between align-items-center flex-wrap gap-3" style="border-color:#ebe4f7!important;">
                    <div>
                        <span class="shift-badge mb-1" style="background: {{ optional($pc->caLamViec)->mau_nhan ?: '#7c3aed' }}">
                            {{ optional($pc->caLamViec)->ten_ca }}
                        </span>
                        <div class="small text-muted mt-1">
                            {{ \Carbon\Carbon::parse(optional($pc->caLamViec)->gio_bat_dau)->format('H:i') }} – {{ \Carbon\Carbon::parse(optional($pc->caLamViec)->gio_ket_thuc)->format('H:i') }}
                            · {{ optional($pc->caLamViec)->so_gio_cong }} giờ
                        </div>
                        @if($pc->chamCong)
                            <div class="small mt-1">
                                <span class="text-success fw-semibold">In: {{ $pc->chamCong->gio_checkin ? \Carbon\Carbon::parse($pc->chamCong->gio_checkin)->format('H:i:s') : '—' }}</span>
                                ·
                                <span class="text-primary fw-semibold">Out: {{ $pc->chamCong->gio_checkout ? \Carbon\Carbon::parse($pc->chamCong->gio_checkout)->format('H:i:s') : '—' }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        @if(!$pc->chamCong || !$pc->chamCong->gio_checkin)
                            <form action="{{ route('chamcong.checkin', $pc->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success rounded-pill fw-semibold px-3">
                                    <i class="fas fa-right-to-bracket me-1"></i> Check-in
                                </button>
                            </form>
                        @elseif(!$pc->chamCong->gio_checkout)
                            <form action="{{ route('chamcong.checkout', $pc->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary rounded-pill fw-semibold px-3">
                                    <i class="fas fa-right-from-bracket me-1"></i> Check-out
                                </button>
                            </form>
                        @else
                            <span class="badge rounded-pill text-bg-success px-3 py-2">
                                <i class="fas fa-check me-1"></i> Hoàn tất
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-muted small">Hôm nay bạn chưa được phân ca làm việc nào.</div>
            </div>
        @endforelse
    </div>
</div>

{{-- THỐNG KÊ NHANH --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon purple"><i class="fas fa-calendar-week"></i></div>
                <div><h3>{{ $stats['tong_ca'] }}</h3><div class="label">Tổng ca trong tháng</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon green"><i class="fas fa-circle-check"></i></div>
                <div><h3>{{ $stats['dung_gio'] }}</h3><div class="label">Đúng giờ</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon amber"><i class="fas fa-clock"></i></div>
                <div><h3>{{ $stats['di_tre'] }}</h3><div class="label">Đi trễ</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon pink"><i class="fas fa-triangle-exclamation"></i></div>
                <div><h3>{{ $stats['ve_som_vang'] }}</h3><div class="label">Về sớm / Vắng mặt</div></div>
            </div>
        </div>
    </div>
</div>

{{-- BẢNG LỊCH SỬ CHẤM CÔNG --}}
<div class="panel">
    <form method="GET" action="{{ route('chamcong.index') }}" class="panel-toolbar pb-3">
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <input type="month" name="thang" value="{{ $thang }}" class="btn-filter" onchange="this.form.submit()">
            @if($canManage)
                <select name="nhanvien_id" class="btn-filter" onchange="this.form.submit()">
                    <option value="">Tất cả nhân viên</option>
                    @foreach($nhanViens as $nv)
                        <option value="{{ $nv->id }}" {{ (string)$nhanVienId === (string)$nv->id ? 'selected' : '' }}>{{ $nv->ho_ten }} ({{ $nv->ma_nv }})</option>
                    @endforeach
                </select>
            @endif
            <select name="trang_thai" class="btn-filter" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="DungGio" {{ $trangThai === 'DungGio' ? 'selected' : '' }}>Đúng giờ</option>
                <option value="DiTre" {{ $trangThai === 'DiTre' ? 'selected' : '' }}>Đi trễ</option>
                <option value="VeSom" {{ $trangThai === 'VeSom' ? 'selected' : '' }}>Về sớm</option>
                <option value="VangMat" {{ $trangThai === 'VangMat' ? 'selected' : '' }}>Vắng mặt</option>
                <option value="ChuaCham" {{ $trangThai === 'ChuaCham' ? 'selected' : '' }}>Chưa chấm công</option>
            </select>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table users-table align-middle mb-0">
            <thead>
                <tr>
                    <th>NGÀY</th>
                    <th>NHÂN VIÊN</th>
                    <th>CA LÀM VIỆC</th>
                    <th>CHECK-IN</th>
                    <th>CHECK-OUT</th>
                    <th>TRẠNG THÁI</th>
                    @if($canManage)
                        <th class="text-end">ĐIỀU CHỈNH</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($danhSachPhanCa as $pc)
                    @php
                        $cc = $pc->chamCong;
                        $stMap = [
                            'DungGio' => ['Đúng giờ', 'text-bg-success'],
                            'DiTre' => ['Đi trễ', 'text-bg-warning'],
                            'VeSom' => ['Về sớm', 'text-bg-info'],
                            'VangMat' => ['Vắng mặt', 'text-bg-danger'],
                        ];
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ \Carbon\Carbon::parse($pc->ngay_lam_viec)->format('d/m/Y') }}</td>
                        <td>
                            <div class="fw-bold">{{ optional($pc->nhanVien)->ho_ten }}</div>
                            <div class="muted">{{ optional($pc->nhanVien)->ma_nv }}</div>
                        </td>
                        <td>
                            <span class="shift-badge" style="background: {{ optional($pc->caLamViec)->mau_nhan ?: '#7c3aed' }}">
                                {{ optional($pc->caLamViec)->ten_ca }}
                            </span>
                        </td>
                        <td>{{ $cc && $cc->gio_checkin ? \Carbon\Carbon::parse($cc->gio_checkin)->format('H:i:s') : '—' }}</td>
                        <td>{{ $cc && $cc->gio_checkout ? \Carbon\Carbon::parse($cc->gio_checkout)->format('H:i:s') : '—' }}</td>
                        <td>
                            @if($cc)
                                <span class="badge rounded-pill {{ $stMap[$cc->trang_thai][1] ?? 'text-bg-secondary' }}">
                                    {{ $stMap[$cc->trang_thai][0] ?? $cc->trang_thai }}
                                </span>
                            @else
                                <span class="badge rounded-pill text-bg-light text-muted border">Chưa chấm</span>
                            @endif
                        </td>
                        @if($canManage)
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-light rounded-pill js-manual-cc"
                                    data-id="{{ $pc->id }}"
                                    data-nv="{{ optional($pc->nhanVien)->ho_ten }}"
                                    data-checkin="{{ $cc && $cc->gio_checkin ? \Carbon\Carbon::parse($cc->gio_checkin)->format('Y-m-d\TH:i') : ($pc->ngay_lam_viec . 'T' . \Carbon\Carbon::parse(optional($pc->caLamViec)->gio_bat_dau)->format('H:i')) }}"
                                    data-checkout="{{ $cc && $cc->gio_checkout ? \Carbon\Carbon::parse($cc->gio_checkout)->format('Y-m-d\TH:i') : ($pc->ngay_lam_viec . 'T' . \Carbon\Carbon::parse(optional($pc->caLamViec)->gio_ket_thuc)->format('H:i')) }}"
                                    data-status="{{ $cc ? $cc->trang_thai : 'DungGio' }}">
                                    <i class="fas fa-pen-to-square me-1"></i> Cập nhật
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canManage ? 7 : 6 }}" class="text-center py-5 text-muted">Không có dữ liệu chấm công trong tháng này.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($canManage)
<div class="modal fade" id="manualChamCongModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px;">
            <form method="POST" id="manualChamCongForm">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Cập nhật chấm công: <span id="cc_nv_name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giờ Check-in</label>
                        <input type="datetime-local" name="gio_checkin" id="cc_checkin" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giờ Check-out</label>
                        <input type="datetime-local" name="gio_checkout" id="cc_checkout" class="form-control">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select name="trang_thai" id="cc_status" class="form-select" required>
                            <option value="DungGio">Đúng giờ</option>
                            <option value="DiTre">Đi trễ</option>
                            <option value="VeSom">Về sớm</option>
                            <option value="VangMat">Vắng mặt</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-create">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    function updateClock() {
        var el = document.getElementById('liveClock');
        if (!el) return;
        var now = new Date();
        el.textContent = now.toLocaleTimeString('vi-VN', { hour12: false });
    }
    updateClock();
    setInterval(updateClock, 1000);

    document.querySelectorAll('.js-manual-cc').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('manualChamCongForm').action = "{{ url('/cham-cong') }}/" + this.dataset.id + "/cap-nhat";
            document.getElementById('cc_nv_name').textContent = this.dataset.nv;
            document.getElementById('cc_checkin').value = this.dataset.checkin;
            document.getElementById('cc_checkout').value = this.dataset.checkout;
            document.getElementById('cc_status').value = this.dataset.status;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('manualChamCongModal')).show();
        });
    });
</script>
@endsection
