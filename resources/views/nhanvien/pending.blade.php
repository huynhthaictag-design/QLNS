@extends('layouts.app')
@section('title', 'Duyệt nhân viên (Tài khoản đợi duyệt)')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Duyệt tài khoản đăng ký mới</h4>
        <p class="text-muted mb-0" style="font-size:.9rem">
            Chỉ khi được duyệt tại đây, tài khoản mới xuất hiện trong Danh sách nhân viên và Lịch phân ca.
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('nhanvien.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fas fa-users me-1"></i> Danh sách nhân viên chính thức
        </a>
        <a href="{{ route('nhanvien.pending') }}" class="btn btn-warning btn-sm px-3 fw-bold">
            <i class="fas fa-user-clock me-1"></i> Đợi duyệt ({{ $pendingUsers->count() }})
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4" style="border-radius:16px">
    <div class="card-body">
        <form method="GET" action="{{ route('nhanvien.pending') }}" class="row g-2 align-items-center">
            <div class="col-md-8">
                <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Tìm theo họ tên, email hoặc mã NV...">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search me-1"></i> Tìm kiếm</button>
                <a href="{{ route('nhanvien.pending') }}" class="btn btn-light border">Đặt lại</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius:16px">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ỨNG VIÊN / TÀI KHOẢN</th>
                    <th>ẢNH ĐẠI DIỆN</th>
                    <th>FILE CV ĐÍNH KÈM</th>
                    <th>PHÂN BỔ PHÒNG BAN & CHỨC VỤ KHI DUYỆT</th>
                    <th class="text-end">THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingUsers as $uv)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $uv->ho_ten }} <span class="badge bg-secondary ms-1">{{ $uv->ma_nv }}</span></div>
                            <div class="text-muted" style="font-size:.84rem"><i class="fas fa-envelope me-1"></i>{{ $uv->email }}</div>
                            <div class="text-muted" style="font-size:.84rem"><i class="fas fa-phone me-1"></i>{{ $uv->so_dien_thoai ?: 'Chưa cập nhật' }}</div>
                        </td>
                        <td>
                            @if($uv->anh_dai_dien)
                                <a href="{{ asset($uv->anh_dai_dien) }}" target="_blank">
                                    <img src="{{ asset($uv->anh_dai_dien) }}" alt="Avatar" style="width:56px;height:56px;object-fit:cover;border-radius:12px;border:1px solid #e2dbf0">
                                </a>
                            @else
                                <span class="badge bg-light text-secondary border">Chưa gửi ảnh</span>
                            @endif
                        </td>
                        <td>
                            @if($uv->cv_file)
                                <a href="{{ asset($uv->cv_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-pdf me-1"></i> Xem / Tải CV
                                </a>
                            @else
                                <span class="badge bg-light text-secondary border">Chưa nộp CV</span>
                            @endif
                        </td>
                        <td>
                            <form id="approve-form-{{ $uv->id }}" action="{{ route('nhanvien.approve', $uv->id) }}" method="POST" class="row g-2">
                                @csrf
                                <div class="col-md-4">
                                    <select name="phongban_id" class="form-select form-select-sm">
                                        <option value="">-- Chọn Phòng ban --</option>
                                        @foreach($phongbans as $pb)
                                            <option value="{{ $pb->id }}" {{ $uv->phongban_id == $pb->id ? 'selected' : '' }}>
                                                {{ $pb->ten_phongban }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="chucvu_id" class="form-select form-select-sm">
                                        <option value="">-- Chọn Chức vụ --</option>
                                        @foreach($chucvus as $cv)
                                            <option value="{{ $cv->id }}" {{ $uv->chucvu_id == $cv->id ? 'selected' : '' }}>
                                                {{ $cv->ten_chucvu }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select name="vai_tro" class="form-select form-select-sm">
                                        <option value="Employee" selected>Employee</option>
                                        <option value="Manager">Manager</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>
                            </form>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <button type="submit" form="approve-form-{{ $uv->id }}" class="btn btn-success btn-sm px-3">
                                    <i class="fas fa-check me-1"></i> Duyệt
                                </button>
                                <form action="{{ route('nhanvien.reject', $uv->id) }}" method="POST" onsubmit="return confirm('Từ chối và xóa tài khoản đăng ký này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-xmark me-1"></i> Từ chối
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success d-block"></i>
                            Hiện không có tài khoản nào đang đợi duyệt.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
