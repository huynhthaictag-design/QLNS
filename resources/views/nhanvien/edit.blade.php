@extends('layouts.app')
@section('title', 'Sửa nhân viên')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 fw-bold">Sửa nhân viên</h3>
        <p class="text-muted small mb-0">Cập nhật thông tin và quyền của {{ $nv->ho_ten }}.</p>
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

    <form action="{{ route('nhanvien.update', $nv->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('nhanvien._form', ['nv' => $nv])
        <div class="text-end mt-3">
            <button type="submit" class="btn-create">Lưu thay đổi</button>
        </div>
    </form>
</div>
@endsection
