@extends('layouts.app')
@section('title', 'Tổng quan hệ thống')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="mb-1 fw-bold">Xin chào, {{ auth()->user()->ho_ten }}</h3>
        <p class="text-muted small mb-0">
            Hôm nay {{ \Carbon\Carbon::parse($today)->format('d/m/Y') }} · Tuần {{ \Carbon\Carbon::parse($weekStart)->format('d/m') }} – {{ \Carbon\Carbon::parse($weekEnd)->format('d/m/Y') }}
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('chamcong.index') }}" class="btn btn-light rounded-pill fw-semibold">
            <i class="fas fa-fingerprint me-1 text-primary"></i> Chấm công ngay
        </a>
        <a href="{{ route('phanca.index') }}" class="btn-create">
            <i class="fas fa-calendar-days me-1"></i> {{ $canManage ? 'Bảng phân ca' : 'Lịch của tôi' }}
        </a>
    </div>
</div>

@if($canManage)
    {{-- KPI CARDS CHO ADMIN / MANAGER --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('nhanvien.index') }}" class="text-decoration-none text-reset d-block h-100">
                <div class="stat-card">
                    <div class="stat-left">
                        <div class="stat-icon purple"><i class="fas fa-users"></i></div>
                        <div>
                            <h3>{{ $stats['nhanvien_active'] }}</h3>
                            <div class="label">Nhân sự hoạt động</div>
                        </div>
                    </div>
                    @if($stats['nhanvien_pending'] > 0)
                        <span class="trend down">{{ $stats['nhanvien_pending'] }} đợi duyệt</span>
                    @else
                        <span class="trend up">Ổn định</span>
                    @endif
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('phanca.index') }}" class="text-decoration-none text-reset d-block h-100">
                <div class="stat-card">
                    <div class="stat-left">
                        <div class="stat-icon green"><i class="fas fa-calendar-check"></i></div>
                        <div>
                            <h3>{{ $stats['phanca_tuan'] }}</h3>
                            <div class="label">Lượt phân ca tuần này</div>
                        </div>
                    </div>
                    <span class="trend up">{{ $stats['phanca_homnay'] }} hôm nay</span>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('chamcong.index') }}" class="text-decoration-none text-reset d-block h-100">
                <div class="stat-card">
                    <div class="stat-left">
                        <div class="stat-icon amber"><i class="fas fa-clock"></i></div>
                        <div>
                            <h3>{{ $stats['chamcong_homnay'] }}/{{ $stats['phanca_homnay'] }}</h3>
                            <div class="label">Đã chấm công hôm nay</div>
                        </div>
                    </div>
                    <span class="trend up">Check-in</span>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('dontu.index', ['trang_thai' => 'ChoDuyet']) }}" class="text-decoration-none text-reset d-block h-100">
                <div class="stat-card">
                    <div class="stat-left">
                        <div class="stat-icon pink"><i class="fas fa-file-signature"></i></div>
                        <div>
                            <h3>{{ $stats['dontu_choduyet'] }}</h3>
                            <div class="label">Đơn từ chờ duyệt</div>
                        </div>
                    </div>
                    <span class="trend {{ $stats['dontu_choduyet'] > 0 ? 'down' : 'up' }}">
                        {{ $stats['dontu_choduyet'] > 0 ? 'Cần xử lý' : 'Đã xong' }}
                    </span>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- LỊCH TRỰC HÔM NAY --}}
        <div class="col-lg-7">
            <div class="panel h-100">
                <div class="panel-toolbar pb-3 border-bottom" style="border-color:#f1ecf8!important;">
                    <div>
                        <h5 class="mb-0 fw-bold">Lịch trực hôm nay</h5>
                        <div class="muted">Theo dõi nhân sự phân ca và tình trạng check-in</div>
                    </div>
                    <a href="{{ route('phanca.index') }}" class="btn btn-sm btn-light rounded-pill fw-semibold">Xem lịch tuần</a>
                </div>
                <div class="table-responsive">
                    <table class="table users-table mb-0">
                        <thead>
                            <tr>
                                <th>NHÂN VIÊN</th>
                                <th>CA LÀM</th>
                                <th>CHẤM CÔNG</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lichHomNay as $pc)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ optional($pc->nhanVien)->ho_ten }}</div>
                                        <div class="muted">{{ optional(optional($pc->nhanVien)->phongBan)->ten_phongban ?? 'Chưa xếp PB' }}</div>
                                    </td>
                                    <td>
                                        <span class="shift-badge" style="background: {{ optional($pc->caLamViec)->mau_nhan ?: '#7c3aed' }}">
                                            {{ optional($pc->caLamViec)->ten_ca }}
                                            ({{ \Carbon\Carbon::parse(optional($pc->caLamViec)->gio_bat_dau)->format('H:i') }}-{{ \Carbon\Carbon::parse(optional($pc->caLamViec)->gio_ket_thuc)->format('H:i') }})
                                        </span>
                                    </td>
                                    <td>
                                        @if($pc->chamCong && $pc->chamCong->gio_checkin)
                                            <span class="badge rounded-pill text-bg-success">
                                                In: {{ \Carbon\Carbon::parse($pc->chamCong->gio_checkin)->format('H:i') }}
                                            </span>
                                            @if($pc->chamCong->gio_checkout)
                                                <span class="badge rounded-pill text-bg-primary">
                                                    Out: {{ \Carbon\Carbon::parse($pc->chamCong->gio_checkout)->format('H:i') }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge rounded-pill text-bg-light text-muted border">Chưa check-in</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Hôm nay chưa có ca làm việc nào được phân công.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ĐƠN TỪ CHỜ DUYỆT & PHÒNG BAN --}}
        <div class="col-lg-5 d-flex flex-column gap-4">
            <div class="panel">
                <div class="panel-toolbar pb-3 border-bottom" style="border-color:#f1ecf8!important;">
                    <h5 class="mb-0 fw-bold">Đơn từ chờ duyệt</h5>
                    <a href="{{ route('dontu.index') }}" class="btn btn-sm btn-light rounded-pill fw-semibold">Tất cả</a>
                </div>
                <div class="p-3">
                    @forelse($donChoDuyet as $don)
                        <div class="d-flex justify-content-between align-items-start gap-2 py-2 border-bottom" style="border-color:#f6f2fb!important;">
                            <div>
                                <div class="fw-bold small">{{ optional($don->nhanVien)->ho_ten }}</div>
                                <div class="muted" style="font-size:.78rem;">
                                    {{ $don->loaiDonLabel() }} · Ngày {{ \Carbon\Carbon::parse($don->ngay_ap_dung)->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <form action="{{ route('dontu.status', $don->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="trang_thai" value="DaDuyet">
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-2 py-0" title="Duyệt"><i class="fas fa-check"></i></button>
                                </form>
                                <form action="{{ route('dontu.status', $don->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="trang_thai" value="TuChoi">
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" title="Từ chối"><i class="fas fa-xmark"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted small">Không có đơn từ nào đang chờ duyệt.</div>
                    @endforelse
                </div>
            </div>

            <div class="panel">
                <div class="panel-toolbar pb-2">
                    <h5 class="mb-0 fw-bold">Nhân sự theo phòng ban</h5>
                </div>
                <div class="p-3 pt-1">
                    @foreach($thongKePhongBan as $pb)
                        @php
                            $pct = $stats['nhanvien_active'] > 0 ? round(($pb->so_nhan_vien / $stats['nhanvien_active']) * 100) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-semibold mb-1">
                                <span>{{ $pb->ten_phongban }}</span>
                                <span>{{ $pb->so_nhan_vien }} NV ({{ $pct }}%)</span>
                            </div>
                            <div class="progress" style="height:8px;border-radius:999px;background:#f3eefc;">
                                <div class="progress-bar" style="width:{{ $pct }}%;background:linear-gradient(90deg,#8b5cf6,#7c3aed);border-radius:999px;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@else
    {{-- DASHBOARD CHO NHÂN VIÊN --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-left">
                    <div class="stat-icon purple"><i class="fas fa-calendar-day"></i></div>
                    <div>
                        <h3>{{ $stats['phanca_homnay'] }}</h3>
                        <div class="label">Ca làm hôm nay</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-left">
                    <div class="stat-icon green"><i class="fas fa-business-time"></i></div>
                    <div>
                        <h3>{{ $stats['tong_gio_tuan'] }}h</h3>
                        <div class="label">Giờ công tuần này ({{ $stats['phanca_tuan'] }} ca)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-left">
                    <div class="stat-icon amber"><i class="fas fa-fingerprint"></i></div>
                    <div>
                        <h3>{{ $stats['chamcong_thang'] }}</h3>
                        <div class="label">Lượt chấm công tháng</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-left">
                    <div class="stat-icon pink"><i class="fas fa-paper-plane"></i></div>
                    <div>
                        <h3>{{ $stats['dontu_choduyet'] }}</h3>
                        <div class="label">Đơn đang chờ duyệt</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="panel p-4 h-100">
                <h5 class="fw-bold mb-3">Ca làm việc hôm nay của bạn</h5>
                @forelse($lichHomNay as $pc)
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 mb-3 rounded-4" style="background:#faf8ff;border:1px solid #ede7f8;">
                        <div>
                            <span class="shift-badge mb-1" style="background: {{ optional($pc->caLamViec)->mau_nhan ?: '#7c3aed' }}">
                                {{ optional($pc->caLamViec)->ten_ca }}
                            </span>
                            <div class="small text-muted mt-1">
                                <i class="far fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse(optional($pc->caLamViec)->gio_bat_dau)->format('H:i') }} – {{ \Carbon\Carbon::parse(optional($pc->caLamViec)->gio_ket_thuc)->format('H:i') }}
                                · {{ optional($pc->caLamViec)->so_gio_cong }} giờ công
                            </div>
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
                                    <i class="fas fa-check-circle me-1"></i> Đã hoàn tất ca
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">Hôm nay bạn không có lịch trực nào.</div>
                @endforelse
            </div>
        </div>
        <div class="col-lg-5">
            <div class="panel p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Đơn từ gần đây</h5>
                    <a href="{{ route('dontu.index') }}" class="btn btn-sm btn-light rounded-pill">Gửi đơn</a>
                </div>
                @forelse($donCuaToi as $don)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color:#f6f2fb!important;">
                        <div>
                            <div class="fw-semibold small">{{ $don->loaiDonLabel() }}</div>
                            <div class="muted" style="font-size:.78rem;">Áp dụng: {{ \Carbon\Carbon::parse($don->ngay_ap_dung)->format('d/m/Y') }}</div>
                        </div>
                        <span class="badge rounded-pill {{ $don->trang_thai === 'DaDuyet' ? 'text-bg-success' : ($don->trang_thai === 'TuChoi' ? 'text-bg-danger' : 'text-bg-warning') }}">
                            {{ $don->trangThaiLabel() }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">Bạn chưa gửi đơn từ nào.</div>
                @endforelse
            </div>
        </div>
    </div>
@endif
@endsection
