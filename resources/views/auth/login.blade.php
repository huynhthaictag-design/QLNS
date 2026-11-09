@extends('layouts.guest')
@section('title', 'Dang nhap - HappyTime')

@section('content')
<div class="brand">
    <span class="mark"><i class="fas fa-bolt"></i></span> HappyTime
</div>
<h3 class="fw-bold mb-1">Đăng nhập</h3>
<p class="muted mb-4">Quản lý lịch làm việc của bạn.</p>

@if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('login.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
    </div>
    <div class="mb-4">
        <label class="form-label fw-semibold">Mật khẩu</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn-auth" type="submit">Đăng nhập</button>
</form>
<p class="text-center mt-4 mb-0 muted">
    Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký</a>
</p>
@endsection
