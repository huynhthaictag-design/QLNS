@extends('layouts.app')
@section('title', 'Danh sách nhân viên')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon purple"><i class="fas fa-users"></i></div>
                <div>
                    <h3>{{ $stats['total'] }}</h3>
                    <div class="label">Total Users (Đã duyệt)</div>
                </div>
            </div>
            <span class="trend up"><i class="fas fa-arrow-up me-1"></i>{{ $stats['total'] }}</span>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon green"><i class="fas fa-user-check"></i></div>
                <div>
                    <h3>{{ $stats['active'] }}</h3>
                    <div class="label">Active</div>
                </div>
            </div>
            <span class="trend up"><i class="fas fa-arrow-up me-1"></i>{{ $stats['active'] }}</span>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <a href="{{ route('nhanvien.pending') }}" class="text-decoration-none text-reset d-block">
            <div class="stat-card">
                <div class="stat-left">
                    <div class="stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
                    <div>
                        <h3>{{ $stats['pending'] }}</h3>
                        <div class="label">Đợi duyệt (Pending)</div>
                    </div>
                </div>
                <span class="trend down"><i class="fas fa-arrow-right me-1"></i>Duyệt ngay</span>
            </div>
        </a>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon pink"><i class="fas fa-shield-halved"></i></div>
                <div>
                    <h3>{{ $stats['admins'] }}</h3>
                    <div class="label">Admins</div>
                </div>
            </div>
            <span class="trend down"><i class="fas fa-arrow-down me-1"></i>{{ $stats['admins'] }}</span>
        </div>
    </div>
</div>

<div class="panel">
    <form method="GET" action="{{ route('nhanvien.index') }}" class="panel-toolbar">
        <div class="status-tabs">
            <a href="{{ route('nhanvien.index', array_filter(['q' => $q, 'role' => $role])) }}" class="{{ $status === 'all' ? 'active' : '' }}">
                All <span class="count">{{ $stats['total'] }}</span>
            </a>
            <a href="{{ route('nhanvien.index', array_filter(['status' => 'active', 'q' => $q, 'role' => $role])) }}" class="{{ $status === 'active' ? 'active' : '' }}">
                Active <span class="count">{{ $stats['active'] }}</span>
            </a>
            <a href="{{ route('nhanvien.index', array_filter(['status' => 'inactive', 'q' => $q, 'role' => $role])) }}" class="{{ $status === 'inactive' ? 'active' : '' }}">
                Inactive <span class="count">{{ $stats['inactive'] }}</span>
            </a>
            <a href="{{ route('nhanvien.pending') }}" class="{{ request()->routeIs('nhanvien.pending') ? 'active' : '' }}">
                Duyệt nhân viên <span class="count">{{ $stats['pending'] }}</span>
            </a>
        </div>
        <div class="toolbar-right flex-wrap">
            @if($status !== 'all')
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="search-users">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" name="q" value="{{ $q }}" placeholder="Search users...">
            </div>
            <select name="role" class="btn-filter" onchange="this.form.submit()">
                <option value="">Filter</option>
                <option value="Admin" {{ $role === 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Manager" {{ $role === 'Manager' ? 'selected' : '' }}>Manager</option>
                <option value="Employee" {{ $role === 'Employee' ? 'selected' : '' }}>User</option>
            </select>
            <button type="button" class="btn-filter text-warning-emphasis" data-bs-toggle="modal" data-bs-target="#importExcelModal">
                <i class="fas fa-file-import me-1"></i> Nhập Excel
            </button>
            <a href="{{ route('nhanvien.export') }}" class="btn-filter text-success text-decoration-none d-inline-flex align-items-center">
                <i class="fas fa-file-excel me-1"></i> Xuất Excel
            </a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table users-table align-middle">
            <thead>
                <tr>
                    <th style="width:42px"><input type="checkbox" class="form-check-input"></th>
                    <th>USER</th>
                    <th>ROLE</th>
                    <th>STATUS</th>
                    <th>CV / HỒ SƠ</th>
                    <th>JOINED</th>
                    <th class="text-end">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($danhsachNV as $nv)
                    @php
                        $roleClass = $nv->roleLabel() === 'Admin' ? 'role-admin' : ($nv->roleLabel() === 'Manager' ? 'role-manager' : 'role-user');
                        $statusClass = $nv->statusLabel() === 'Active' ? 'status-active' : ($nv->statusLabel() === 'Pending' ? 'status-pending' : 'status-inactive');
                    @endphp
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        <td>
                            <div class="user-cell">
                                @if($nv->anh_dai_dien)
                                    <img src="{{ asset($nv->anh_dai_dien) }}" alt="{{ $nv->ho_ten }}" class="avatar-circle" style="object-fit:cover;">
                                @else
                                    <div class="avatar-circle" style="background: {{ $nv->avatarColor() }}">{{ $nv->initials() }}</div>
                                @endif
                                <div>
                                    <p class="user-name">{{ $nv->ho_ten }}</p>
                                    <div class="user-mail">{{ $nv->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="role-pill {{ $roleClass }}">
                                <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block"></span>
                                {{ $nv->roleLabel() }}
                            </span>
                        </td>
                        <td>
                            <span class="status-pill {{ $statusClass }}">
                                <span class="status-dot"></span> {{ $nv->statusLabel() }}
                            </span>
                        </td>
                        <td>
                            @if($nv->cv_file)
                                <a href="{{ asset($nv->cv_file) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size:.78rem;border-radius:8px;">
                                    <i class="fas fa-file-lines me-1"></i>Xem CV
                                </a>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                        <td class="muted">{{ $nv->created_at ? $nv->created_at->format('M j, Y') : '—' }}</td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="action-btn" data-bs-toggle="dropdown" data-bs-boundary="viewport"><i class="fas fa-ellipsis"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:12px">
                                    <li><a class="dropdown-item" href="{{ route('nhanvien.show', $nv->id) }}"><i class="fas fa-eye me-2"></i>Xem</a></li>
                                    <li><a class="dropdown-item" href="{{ route('nhanvien.edit', $nv->id) }}"><i class="fas fa-pen me-2"></i>Sửa</a></li>
                                    <li>
                                        <form action="{{ route('nhanvien.destroy', $nv->id) }}" method="POST" onsubmit="return confirm('Xóa nhân viên này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger" type="submit"><i class="fas fa-trash me-2"></i>Xóa</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Chưa có nhân viên chính thức nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Nhập Excel (CSV) theo hướng dẫn P05 --}}
<div class="modal fade" id="importExcelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px;">
            <form action="{{ route('nhanvien.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-file-import text-primary me-2"></i>Nhập nhân viên từ Excel (CSV)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Tải tệp mẫu và điền thông tin nhân viên (lưu dạng <strong>CSV UTF-8</strong> trong Excel), sau đó tải lên hệ thống.
                    </p>
                    <div class="mb-3">
                        <a href="{{ route('nhanvien.template') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="fas fa-download me-1"></i> Tải tập tin mẫu (.csv)
                        </a>
                    </div>
                    <div class="mb-0">
                        <label for="file_excel" class="form-label fw-semibold">Chọn tập tin CSV</label>
                        <input type="file" name="file_excel" id="file_excel" class="form-control @error('file_excel') is-invalid @enderror" accept=".csv,.txt" required>
                        @error('file_excel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-create">Nhập dữ liệu</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@error('file_excel')
<script>
    bootstrap.Modal.getOrCreateInstance(document.getElementById('importExcelModal')).show();
</script>
@enderror
@endsection
