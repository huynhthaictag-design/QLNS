@extends('layouts.app')
@section('title', $nv->ho_ten)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Hồ sơ nhân viên</h3>
        <p class="text-muted small mb-0">Chi tiết tài khoản và thông tin công việc.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('nhanvien.edit', $nv->id) }}" class="btn-create">Sửa</a>
        <a href="{{ route('nhanvien.index') }}" class="btn btn-light rounded-pill">Quay lại</a>
    </div>
</div>

<div class="panel p-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="avatar-circle" style="width:64px;height:64px;font-size:1.2rem;background:{{ $nv->avatarColor() }}">{{ $nv->initials() }}</div>
        <div>
            <h4 class="mb-1 fw-bold">{{ $nv->ho_ten }}</h4>
            <div class="text-muted">{{ $nv->email }} · {{ $nv->ma_nv }}</div>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-4"><div class="muted">Vai trò</div><div class="fw-semibold">{{ $nv->roleLabel() }}</div></div>
        <div class="col-md-4"><div class="muted">Trạng thái</div><div class="fw-semibold">{{ $nv->statusLabel() }}</div></div>
        <div class="col-md-4"><div class="muted">Điện thoại</div><div class="fw-semibold">{{ $nv->so_dien_thoai ?: '—' }}</div></div>
        <div class="col-md-4"><div class="muted">Phòng ban</div><div class="fw-semibold">{{ $nv->phongban->ten_phongban ?? '—' }}</div></div>
        <div class="col-md-4"><div class="muted">Chức vụ</div><div class="fw-semibold">{{ $nv->chucvu->ten_chucvu ?? '—' }}</div></div>
        <div class="col-md-4"><div class="muted">Ngày vào</div><div class="fw-semibold">{{ $nv->created_at ? $nv->created_at->format('d/m/Y') : '—' }}</div></div>
    </div>
</div>
@endsection
