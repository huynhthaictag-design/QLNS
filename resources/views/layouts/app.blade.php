<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quan ly nhan vien') - HappyTime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar: #1b1233;
            --sidebar-hover: rgba(255,255,255,.06);
            --sidebar-active: #2d1f55;
            --accent: #7c3aed;
            --accent-2: #8b5cf6;
            --page-bg: #f3eefc;
            --text-muted: #8b8499;
        }
        * { box-sizing: border-box; }
        body {
            background: var(--page-bg);
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            color: #2b2440;
            min-height: 100vh;
        }
        .app-shell { display: flex; min-height: 100vh; }
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #1b1233 0%, #160e2c 100%);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar-logo {
            padding: 22px 22px 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            font-weight: 800;
            font-size: 1.15rem;
        }
        .sidebar-logo .mark {
            width: 32px; height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, #a78bfa, #7c3aed);
            display: flex; align-items: center; justify-content: center;
        }
        .menu-label {
            padding: 18px 22px 8px;
            font-size: .68rem;
            letter-spacing: .12em;
            color: #7c7394;
            font-weight: 700;
        }
        .sidebar .nav-link {
            color: #c5bdd8;
            padding: 9px 18px;
            margin: 2px 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: .92rem;
            font-weight: 500;
        }
        .sidebar .nav-link i { width: 18px; text-align: center; }
        .sidebar .nav-link:hover { background: var(--sidebar-hover); color: #fff; }
        .sidebar .nav-link.active {
            background: var(--sidebar-active);
            color: #fff;
            box-shadow: inset 3px 0 0 var(--accent-2);
        }
        .sidebar .subnav { padding-left: 8px; }
        .sidebar .subnav .nav-link {
            font-size: .86rem;
            padding: 7px 16px 7px 42px;
            color: #9b93b3;
        }
        .sidebar .subnav .nav-link.active {
            background: #6d3ce8;
            color: #fff;
            box-shadow: none;
        }
        .sidebar-footer {
            margin-top: auto;
            padding: 16px;
        }
        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,.06);
            border-radius: 14px;
            padding: 10px 12px;
            color: #fff;
        }
        .user-chip .avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #60a5fa, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .85rem;
        }
        .main-wrap { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 28px 8px;
            gap: 16px;
        }
        .top-search {
            flex: 1;
            max-width: 520px;
            position: relative;
        }
        .top-search input {
            width: 100%;
            border: none;
            background: #fff;
            border-radius: 999px;
            padding: 10px 16px 10px 42px;
            box-shadow: 0 6px 18px rgba(90, 60, 140, .06);
            font-size: .9rem;
        }
        .top-search i {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: #9aa0b5;
        }
        .top-actions { display: flex; align-items: center; gap: 10px; }
        .icon-btn {
            width: 40px; height: 40px;
            border-radius: 50%;
            border: none;
            background: #fff;
            color: #5b5470;
            box-shadow: 0 6px 16px rgba(90, 60, 140, .07);
            display: inline-flex; align-items: center; justify-content: center;
            position: relative;
            text-decoration: none;
        }
        .icon-btn .dot {
            position: absolute; top: 8px; right: 8px;
            width: 8px; height: 8px; border-radius: 50%;
        }
        .btn-create {
            background: linear-gradient(90deg, #8b5cf6, #7c3aed);
            color: #fff !important;
            border: none;
            border-radius: 999px;
            padding: 10px 18px;
            font-weight: 700;
            font-size: .9rem;
            box-shadow: 0 8px 18px rgba(124, 58, 237, .28);
            text-decoration: none;
        }
        .page-body { padding: 8px 28px 32px; }
        .stat-card {
            background: #fff;
            border-radius: 18px;
            padding: 18px 20px;
            box-shadow: 0 10px 24px rgba(90, 60, 140, .05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
        }
        .stat-left { display: flex; align-items: center; gap: 14px; }
        .stat-icon {
            width: 46px; height: 46px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
        .stat-icon.purple { background: #efe7ff; color: #7c3aed; }
        .stat-icon.green { background: #e7f8ef; color: #16a34a; }
        .stat-icon.amber { background: #fff4d6; color: #d97706; }
        .stat-icon.pink { background: #ffe4ea; color: #e11d48; }
        .stat-card h3 { margin: 0; font-weight: 800; font-size: 1.45rem; }
        .stat-card .label { color: #8b8499; font-size: .86rem; }
        .trend {
            font-size: .78rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 999px;
        }
        .trend.up { background: #e7f8ef; color: #16a34a; }
        .trend.down { background: #ffe4ea; color: #e11d48; }
        .panel {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 12px 30px rgba(90, 60, 140, .06);
            overflow: hidden;
        }
        .panel-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 18px 8px;
            flex-wrap: wrap;
        }
        .status-tabs { display: flex; gap: 6px; flex-wrap: wrap; }
        .status-tabs a {
            text-decoration: none;
            color: #6b647c;
            font-weight: 600;
            font-size: .88rem;
            padding: 8px 14px;
            border-radius: 999px;
        }
        .status-tabs a.active {
            background: #7c3aed;
            color: #fff;
        }
        .status-tabs .count {
            font-size: .75rem;
            opacity: .85;
            margin-left: 4px;
        }
        .toolbar-right { display: flex; gap: 8px; }
        .search-users {
            position: relative;
        }
        .search-users input {
            border: 1px solid #eee7f8;
            background: #f8f5fd;
            border-radius: 999px;
            padding: 8px 14px 8px 36px;
            font-size: .86rem;
            min-width: 200px;
        }
        .search-users i {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #9aa0b5; font-size: .8rem;
        }
        .btn-filter {
            border: 1px solid #eee7f8;
            background: #fff;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: .86rem;
            font-weight: 600;
            color: #5b5470;
        }
        .users-table { margin: 0; }
        .users-table thead th {
            font-size: .72rem;
            letter-spacing: .06em;
            color: #9aa0b5;
            font-weight: 700;
            border-bottom: 1px solid #f1ecf8;
            background: #fff;
            padding: 12px 14px;
        }
        .users-table tbody td {
            padding: 14px;
            border-color: #f6f2fb;
            vertical-align: middle;
        }
        .users-table tbody tr:hover { background: #fbf9ff; }
        .user-cell { display: flex; align-items: center; gap: 12px; }
        .avatar-circle {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: .85rem; flex-shrink: 0;
            overflow: hidden;
        }
        .user-name { font-weight: 700; font-size: .92rem; margin: 0; color: #2b2440; }
        .user-mail { color: #9aa0b5; font-size: .78rem; }
        .role-pill, .status-pill {
            display: inline-flex; align-items: center; gap: 6px;
            border-radius: 999px; padding: 4px 10px;
            font-size: .75rem; font-weight: 700;
        }
        .role-admin { background: #ffe4ea; color: #e11d48; }
        .role-manager { background: #fff4d6; color: #d97706; }
        .role-user { background: #e0f4ff; color: #0284c7; }
        .status-active { color: #16a34a; }
        .status-inactive { color: #94a3b8; }
        .status-pending { color: #d97706; }
        .status-dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; background: currentColor; }
        .action-btn {
            width: 32px; height: 32px; border: none; background: transparent;
            color: #8b8499; border-radius: 8px;
        }
        .action-btn:hover { background: #f3eefc; color: #7c3aed; }
        .muted { color: #9aa0b5; font-size: .84rem; }
        @media (max-width: 992px) {
            .sidebar { display: none; }
            .page-body, .topbar { padding-left: 16px; padding-right: 16px; }
        }
    </style>
    @yield('styles')
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <span class="mark"><i class="fas fa-bolt"></i></span>
            HappyTime
        </div>

        <div class="menu-label">OVERVIEW</div>
        <nav class="nav flex-column">
            <a href="{{ route('phanca.index') }}" class="nav-link {{ request()->routeIs('phanca.*') ? 'active' : '' }}">
                <i class="fas fa-house"></i> Home
            </a>
            <a href="{{ route('phanca.index') }}" class="nav-link">
                <i class="fas fa-chart-pie"></i> Dashboards
            </a>
            <a href="{{ route('nhanvien.index') }}" class="nav-link {{ request()->routeIs('nhanvien.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Users
                <i class="fas fa-chevron-down ms-auto" style="font-size:.7rem;opacity:.6"></i>
            </a>
            @if(request()->routeIs('nhanvien.*'))
            <div class="subnav nav flex-column">
                <a href="{{ route('nhanvien.index') }}" class="nav-link {{ request()->routeIs('nhanvien.index') ? 'active' : '' }}">Users List</a>
                <a href="{{ request()->routeIs('nhanvien.show') ? url()->current() : route('nhanvien.index') }}" class="nav-link {{ request()->routeIs('nhanvien.show') ? 'active' : '' }}">User View</a>
                <a href="{{ request()->routeIs('nhanvien.edit') ? url()->current() : route('nhanvien.create') }}" class="nav-link {{ request()->routeIs('nhanvien.edit') || request()->routeIs('nhanvien.create') ? 'active' : '' }}">User Edit</a>
            </div>
            @endif
        </nav>

        <div class="menu-label">APPS</div>
        <nav class="nav flex-column">
            <a href="{{ route('phanca.index') }}" class="nav-link">
                <i class="fas fa-calendar-days"></i> Phân ca
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="avatar">JD</div>
                <div>
                    <div style="font-weight:700;font-size:.85rem;line-height:1.1">John Doe</div>
                    <div style="font-size:.7rem;color:#9b93b3;letter-spacing:.04em">ADMINISTRATOR</div>
                </div>
            </div>
        </div>
    </aside>

    <div class="main-wrap">
        <header class="topbar">
            <div class="top-search">
                <i class="fas fa-magnifying-glass"></i>
                <input type="text" placeholder="Search anything...">
            </div>
            <div class="top-actions">
                <a href="{{ route('nhanvien.create') }}" class="btn-create"><i class="fas fa-plus me-1"></i> Create</a>
                <span class="icon-btn"><i class="fas fa-moon"></i></span>
                <span class="icon-btn">
                    <i class="fas fa-bell"></i>
                    <span class="dot" style="background:#ef4444"></span>
                </span>
                <span class="icon-btn">
                    <i class="fas fa-comment-dots"></i>
                    <span class="dot" style="background:#f59e0b"></span>
                </span>
                <span class="icon-btn" style="width:auto;padding:0 10px 0 4px;border-radius:999px;gap:8px">
                    <span class="avatar-circle" style="width:30px;height:30px;background:linear-gradient(135deg,#60a5fa,#7c3aed);font-size:.75rem">JD</span>
                    <span style="font-weight:700;font-size:.85rem">John Doe</span>
                </span>
            </div>
        </header>
        <main class="page-body">
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
