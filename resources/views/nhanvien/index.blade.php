@extends('layouts.app')
@section('title', 'Users List')

@section('content')
@if(session('status'))
    <div class="alert alert-success border-0 shadow-sm mb-3" style="border-radius:14px">{{ session('status') }}</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon purple"><i class="fas fa-users"></i></div>
                <div>
                    <h3>{{ $stats['total'] }}</h3>
                    <div class="label">Total Users</div>
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
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <h3>{{ $stats['pending'] }}</h3>
                    <div class="label">Pending Invites</div>
                </div>
            </div>
            <span class="trend down"><i class="fas fa-arrow-down me-1"></i>{{ $stats['pending'] }}</span>
        </div>
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
            <a href="{{ route('nhanvien.index', array_filter(['status' => 'pending', 'q' => $q, 'role' => $role])) }}" class="{{ $status === 'pending' ? 'active' : '' }}">
                Pending <span class="count">{{ $stats['pending'] }}</span>
            </a>
        </div>
        <div class="toolbar-right">
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
                    <th>LAST ACTIVE</th>
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
                                <div class="avatar-circle" style="background: {{ $nv->avatarColor() }}">{{ $nv->initials() }}</div>
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
                        <td class="muted">{{ $nv->created_at ? $nv->created_at->diffForHumans() : '—' }}</td>
                        <td class="muted">{{ $nv->created_at ? $nv->created_at->format('M j, Y') : '—' }}</td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="action-btn" data-bs-toggle="dropdown"><i class="fas fa-ellipsis"></i></button>
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
                        <td colspan="7" class="text-center py-5 text-muted">Chưa có nhân viên nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
