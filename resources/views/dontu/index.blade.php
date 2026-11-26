@extends('layouts.app')
@section('title', 'Quản lý Đơn từ')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="mb-1 fw-bold">{{ $canManage ? 'Quản lý Đơn từ' : 'Đơn từ của tôi' }}</h3>
        <p class="text-muted small mb-0">Gửi và theo dõi các đơn xin nghỉ phép, đổi ca làm việc hoặc đi trễ / về sớm</p>
    </div>
    <button class="btn-create" data-bs-toggle="modal" data-bs-target="#createDonTuModal">
        <i class="fas fa-plus me-1"></i> Tạo đơn mới
    </button>
</div>

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-3" style="border-radius:14px;">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon purple"><i class="fas fa-file-lines"></i></div>
                <div><h3>{{ $stats['total'] }}</h3><div class="label">Tổng số đơn</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
                <div><h3>{{ $stats['choduyet'] }}</h3><div class="label">Chờ duyệt</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon green"><i class="fas fa-circle-check"></i></div>
                <div><h3>{{ $stats['daduyet'] }}</h3><div class="label">Đã duyệt</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon pink"><i class="fas fa-circle-xmark"></i></div>
                <div><h3>{{ $stats['tuchoi'] }}</h3><div class="label">Từ chối</div></div>
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <form method="GET" action="{{ route('dontu.index') }}" class="panel-toolbar pb-3">
        <div class="status-tabs">
            <a href="{{ route('dontu.index', array_filter(['loai_don' => $loaiDon])) }}" class="{{ $trangThai === 'all' ? 'active' : '' }}">
                Tất cả <span class="count">{{ $stats['total'] }}</span>
            </a>
            <a href="{{ route('dontu.index', array_filter(['trang_thai' => 'ChoDuyet', 'loai_don' => $loaiDon])) }}" class="{{ $trangThai === 'ChoDuyet' ? 'active' : '' }}">
                Chờ duyệt <span class="count">{{ $stats['choduyet'] }}</span>
            </a>
            <a href="{{ route('dontu.index', array_filter(['trang_thai' => 'DaDuyet', 'loai_don' => $loaiDon])) }}" class="{{ $trangThai === 'DaDuyet' ? 'active' : '' }}">
                Đã duyệt <span class="count">{{ $stats['daduyet'] }}</span>
            </a>
            <a href="{{ route('dontu.index', array_filter(['trang_thai' => 'TuChoi', 'loai_don' => $loaiDon])) }}" class="{{ $trangThai === 'TuChoi' ? 'active' : '' }}">
                Từ chối <span class="count">{{ $stats['tuchoi'] }}</span>
            </a>
        </div>
        <div class="toolbar-right">
            @if($trangThai !== 'all')
                <input type="hidden" name="trang_thai" value="{{ $trangThai }}">
            @endif
            <select name="loai_don" class="btn-filter" onchange="this.form.submit()">
                <option value="">Tất cả loại đơn</option>
                <option value="NghiPhep" {{ $loaiDon === 'NghiPhep' ? 'selected' : '' }}>Xin nghỉ phép</option>
                <option value="DoiCa" {{ $loaiDon === 'DoiCa' ? 'selected' : '' }}>Xin đổi ca</option>
                <option value="DiTreVeSom" {{ $loaiDon === 'DiTreVeSom' ? 'selected' : '' }}>Đi trễ / Về sớm</option>
            </select>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table users-table align-middle mb-0">
            <thead>
                <tr>
                    <th>NHÂN VIÊN</th>
                    <th>LOẠI ĐƠN</th>
                    <th>NGÀY ÁP DỤNG</th>
                    <th>LÝ DO</th>
                    <th>TRẠNG THÁI</th>
                    <th class="text-end">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                @forelse($danhSachDon as $don)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ optional($don->nhanVien)->ho_ten }}</div>
                            <div class="muted">Nộp ngày {{ \Carbon\Carbon::parse($don->ngay_nop)->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            <span class="badge rounded-pill text-bg-light border text-dark px-3 py-2 fw-semibold">
                                {{ $don->loaiDonLabel() }}
                            </span>
                        </td>
                        <td class="fw-semibold">{{ \Carbon\Carbon::parse($don->ngay_ap_dung)->format('d/m/Y') }}</td>
                        <td style="max-width:300px;">
                            <div class="small">{{ $don->ly_do }}</div>
                            @if($don->nguoiDuyet)
                                <div class="muted" style="font-size:.75rem;">Xử lý bởi: {{ $don->nguoiDuyet->ho_ten }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge rounded-pill px-3 py-2 {{ $don->trang_thai === 'DaDuyet' ? 'text-bg-success' : ($don->trang_thai === 'TuChoi' ? 'text-bg-danger' : 'text-bg-warning') }}">
                                {{ $don->trangThaiLabel() }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                @if($canManage && $don->trang_thai === 'ChoDuyet')
                                    <form action="{{ route('dontu.status', $don->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="trang_thai" value="DaDuyet">
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">Duyệt</button>
                                    </form>
                                    <form action="{{ route('dontu.status', $don->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="trang_thai" value="TuChoi">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Từ chối</button>
                                    </form>
                                @endif

                                @if($canManage || ((int)$don->nhanvien_id === (int)auth()->id() && $don->trang_thai === 'ChoDuyet'))
                                    <form action="{{ route('dontu.destroy', $don->id) }}" method="POST" onsubmit="return confirm('Xóa đơn từ này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger rounded-pill" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Chưa có đơn từ nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tạo Đơn Mới --}}
<div class="modal fade" id="createDonTuModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px;">
            <form method="POST" action="{{ route('dontu.store') }}">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Tạo đơn từ mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Loại đơn</label>
                        <select name="loai_don" class="form-select" required>
                            <option value="NghiPhep">Xin nghỉ phép</option>
                            <option value="DoiCa">Xin đổi ca làm việc</option>
                            <option value="DiTreVeSom">Xin đi trễ / về sớm</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ngày áp dụng</label>
                        <input type="date" name="ngay_ap_dung" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Lý do chi tiết</label>
                        <textarea name="ly_do" rows="4" class="form-control" placeholder="Trình bày rõ lý do xin nghỉ / đổi ca..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-create">Gửi đơn</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
