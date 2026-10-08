<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HappyTime - Quản lý</title>
    <!-- Nhúng thư viện Bootstrap và FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #e0e1e2; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .nav-link:hover { background-color: #2b2b40 !important; }
    </style>
</head>
<body>
    <div class="d-flex">

        <!-- THANH MENU BÊN TRÁI (SIDEBAR) -->
        <div class="sidebar d-flex flex-column" style="width: 260px; background-color: #1a1a2e; min-height: 100vh; padding-top: 20px;">
            <div class="logo px-4 mb-5">
                <h4 class="text-white fw-bold mb-0">
                    <i class="fas fa-bolt" style="color: #6f42c1;"></i> HappyTime
                </h4>
            </div>

            <!-- Nhóm OVERVIEW -->
            <div class="menu-section mb-4">
                <h6 class="px-4 text-muted mb-2" style="font-size: 0.75rem; letter-spacing: 1px; font-weight: 600;">OVERVIEW</h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white-50 px-4 py-2 d-flex align-items-center">
                            <i class="fas fa-home me-3" style="width: 20px;"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white-50 px-4 py-2 d-flex align-items-center">
                            <i class="fas fa-chart-pie me-3" style="width: 20px;"></i> Dashboards
                        </a>
                    </li>
                    <!-- Link đến trang Nhân viên -->
                    <li class="nav-item my-1">
                        <a href="{{ route('nhanvien.index') }}" class="nav-link text-white px-4 py-2 d-flex align-items-center" style="background-color: #2b2b40; border-left: 3px solid #6f42c1;">
                            <i class="fas fa-users me-3" style="color: #a894ff; width: 20px;"></i> Users
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Nhóm APPS -->
            <div class="menu-section mb-4">
                <h6 class="px-4 text-muted mb-2 mt-3" style="font-size: 0.75rem; letter-spacing: 1px; font-weight: 600;">APPS</h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white-50 px-4 py-2 d-flex align-items-center">
                            <i class="fas fa-calendar-alt me-3" style="width: 20px;"></i> Phân ca
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- KHU VỰC NỘI DUNG BÊN PHẢI -->
        <div class="content flex-grow-1 p-4">
            <!-- Dữ liệu từ các file con (như index.blade.php) sẽ được đổ vào đây -->
            @yield('content')
        </div>

    </div>
</body>
</html>
