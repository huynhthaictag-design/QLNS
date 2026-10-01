@extends('layouts.app')
@section('content')

<div class="container-fluid py-4" style="background-color: #f8f9fc;">
    <!-- Phần tiêu đề và nút Thêm mới -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        @if(session('status'))
    <div class="alert alert-success mt-3 shadow-sm">
        {{ session('status') }}
        </div>
`       @endif

        <div>
            <h3 class="mb-0 fw-bold">Users</h3>
            <p class="text-muted small">Manage your team members and their account permissions.</p>
        </div>
        <a href="{{ route('nhanvien.create') }}" class="btn btn-primary" style="background-color: #6f42c1; border: none; border-radius: 8px;">
            + Add User
        </a>
    </div>


    <!-- Bảng danh sách nhân viên bọc trong Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
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
                                    <!-- Hiển thị Avatar giả lập bằng chữ cái đầu của tên -->
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
                                <!-- Định dạng màu sắc Nhãn (Badge) tùy theo Vai trò -->
                                @if($nv->vai_tro == 'Admin')
                                    <span class="badge rounded-pill px-3 py-2" style="background-color: #ffe5ea; color: #dc3545;">Admin</span>
                                @elseif($nv->vai_tro == 'Manager')
                                    <span class="badge rounded-pill px-3 py-2" style="background-color: #fff3cd; color: #ffc107;">Manager</span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2" style="background-color: #cff4fc; color: #0dcaf0;">User</span>
                                @endif
                            </td>
                            <td>
                                <!-- Định dạng màu sắc Trạng thái (Active/Inactive) -->
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
                                <!-- Các nút thao tác dạng biểu tượng nhỏ -->
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
