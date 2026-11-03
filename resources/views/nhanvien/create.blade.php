@extends('layouts.app')
@section('title', 'Thêm nhân viên')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Thêm nhân viên</h3>
        <p class="text-muted small mb-0">Tạo tài khoản và phân quyền cho thành viên mới.</p>
    </div>
    <a href="{{ route('nhanvien.index') }}" class="btn btn-light rounded-pill">Quay lại</a>
</div>

<div class="panel p-4">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('nhanvien.store') }}" method="POST">
        @csrf
        @include('nhanvien._form')
        <div class="text-end mt-3">
            <button type="submit" class="btn-create">Lưu nhân viên</button>
        </div>
    </form>
</div>
@endsection
