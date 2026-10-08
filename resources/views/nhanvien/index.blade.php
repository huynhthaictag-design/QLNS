@extends('layouts.app')

@section('content')

<!-- BỔ SUNG CSS VÀO ĐÂY (hoặc đưa vào file .css của dự án) -->
<style>
    .custom-card {
        border-radius: 16px;
        min-width: 260px; /* Đảm bảo card không bị quá nhỏ */
    }
    .icon-box {
        width: 45px;
        height: 45px;
        background-color: #f3e8ff;
        color: #8b5cf6;
        border-radius: 12px;
    }
    .trend-badge {
        background-color: #d1fae5;
        color: #059669;
        font-weight: 600;
        padding: 5px 10px;
        font-size: 0.85rem;
    }
    .table-wrapper {
        border-radius: 12px;
        overflow: hidden; /* Giúp bo góc của table không bị tràn */
    }
</style>
<!-- END CSS -->

<div class="container-fluid py-4" style="background-color: #f8f9fc; min-height: 100vh;">

    <!-- Hiển thị thông báo (đã sửa lỗi dấu backtick ` dư thừa) -->
    @if(session('status'))
        <div class="alert alert-success shadow-sm mb-4">
            {{ session('status') }}
        </div>
    @endif

    <!-- PHẦN 1: HEADER (Tiêu đề bên trái - Nút bên phải) -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold">Users</h3>
            <p class="text-muted small mb-0">Manage your team members and their account permissions.</p>
        </div>
        <a href="{{ route('nhanvien.create') }}" class="btn text-white px-4 py-2" style="background-color: #6f42c1; border: none; border-radius: 8px;">
            + Add User
        </a>
    </div>

    <!-- PHẦN 2: STATS CARDS (Các ô thống kê) -->
    <div class="row mb-4">
        <div class="col-auto"> <!-- Dùng col-auto để card chỉ chiếm diện tích vừa đủ -->
            <div class="card border-0 shadow-sm custom-card">
                <div class="card-body d-flex justify-content-between align-items-center p-3">

                    <!-- Phần bên trái: Icon và Số lượng -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box d-flex justify-content-center align-items-center">
                            <!-- Đảm bảo layout app.blade.php của bạn đã link thư viện Bootstrap Icons -->
                            <i class="bi bi-people fs-5"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">248</h4>
                            <span class="text-secondary" style="font-size: 0.9rem;">Total Users</span>
                        </div>
                    </div>

                    <!-- Phần bên phải: Badge chỉ số tăng -->
                    <span class="badge rounded-pill trend-badge d-flex align-items-center gap-1">
                        <i class="bi bi-graph-up-arrow"></i>
                    </span>
                </div>
            </div>
        </div>

        <!-- BẠN CÓ THỂ COPY THÊM THẺ <div class="col-auto">...</div> XUỐNG ĐÂY ĐỂ TẠO THÊM Ô ACTIVE, INACTIVE... NHƯ TRONG HÌNH GỐC -->
    </div>


    <!-- PHẦN 3: BẢNG DANH SÁCH NHÂN VIÊN -->
    <div class="card border-0 shadow-sm table-wrapper">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted" style="font-size: 0.85rem;">
                        <tr>
                            <th class="ps-4">USER</th>
                            <th>PHÒNG BAN</th>
                            <th>ROLE</th>
                            <th>STATUS</th>
                            <th class="text-center pe-4">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody style="border-top-width: 0;">
                        @foreach($danhsachNV as $nv)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-secondary rounded-circle text-white d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                        {{ substr($nv->ho_ten, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="color: #6f42c1;">{{ $nv->ho_ten }}</h6>
                                        <small class="text-muted">{{ $nv->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $nv->phongban ? $nv->phongban->ten_phongban : '--' }}</td>
                            <td>
                                @if($nv->vai_tro == 'Admin')
                                    <span class="badge rounded-pill px-3 py-2" style="background-color: #ffe5ea; color: #dc3545;">Admin</span>
                                @elseif($nv->vai_tro == 'Manager')
                                    <span class="badge rounded-pill px-3 py-2" style="background-color: #fff3cd; color: #ffc107;">Manager</span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2" style="background-color: #cff4fc; color: #0dcaf0;">User</span>
                                @endif
                            </td>
                            <td>
                                @if($nv->trang_thai == 1)
                                    <span class="badge rounded-pill px-3 py-2" style="background-color: #d1e7dd; color: #198754;">
                                        <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Active
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2" style="background-color: #e2e3e5; color: #6c757d;">
                                        <i class="fas fa-circle me-1" style="font-size: 8px;"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <a href="#" class="text-secondary mx-1" title="Xem"><i class="fas fa-eye"></i></a>
                                <a href="#" class="text-secondary mx-1" title="Sửa"><i class="fas fa-edit"></i></a>
                                <form action="#" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-secondary p-0 mx-1 border-0" onclick="return confirm('Bạn có chắc chắn muốn xóa?')" title="Xóa">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
