@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold">Thêm nhân viên mới</h3>
        <a href="{{ route('nhanvien.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-4">
            <!-- Form gửi dữ liệu tới route nhanvien.store bằng phương thức POST -->
            <form action="{{ route('nhanvien.store') }}" method="POST">
                <!-- Khung hiển thị lỗi nếu dữ liệu nhập vào không hợp lệ -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @csrf <!-- Bắt buộc phải có để bảo mật form -->

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Mã nhân viên</label>
                        <input type="text" name="ma_nv" class="form-control" required placeholder="VD: NV003">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Họ và Tên</label>
                        <input type="text" name="ho_ten" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Vai trò (Role)</label>
                        <select name="vai_tro" class="form-select">
                            <option value="User">User</option>
                            <option value="Manager">Manager</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Phòng ban</label>
                        <select name="phongban_id" class="form-select">
                            <option value="">-- Chọn phòng ban --</option>
                            @foreach($phongbans as $pb)
                                <option value="{{ $pb->id }}">{{ $pb->ten_phongban }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Chức vụ</label>
                        <select name="chucvu_id" class="form-select">
                            <option value="">-- Chọn chức vụ --</option>
                            @foreach($chucvus as $cv)
                                <option value="{{ $cv->id }}">{{ $cv->ten_chucvu }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary" style="background-color: #6f42c1; border: none;">
                        Lưu thông tin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
