@extends('layouts.guest')
@section('title', 'Đăng ký - HappyTime')

@section('content')
<div class="brand">
    <span class="mark"><i class="fas fa-bolt"></i></span> HappyTime
</div>
<h3 class="fw-bold mb-1">Đăng ký nhân viên</h3>
<p class="muted mb-4">Tạo tài khoản để xem lịch làm việc được phân công.</p>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-semibold">Họ và tên</label>
        <input type="text" name="ho_ten" class="form-control" value="{{ old('ho_ten') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Số điện thoại</label>
        <input type="text" name="so_dien_thoai" class="form-control" value="{{ old('so_dien_thoai') }}">
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Mật khẩu</label>
        <input type="password" name="password" class="form-control" required minlength="6">
    </div>
    <div class="mb-4">
        <label class="form-label fw-semibold">Nhập lại mật khẩu</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button class="btn-auth" type="submit">Tạo tài khoản</button>
</form>
<p class="text-center mt-4 mb-0 muted">
    Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
</p>
@endsection
