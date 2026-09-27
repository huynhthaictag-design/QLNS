<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Phân ca - HappyTime</title>
    <!-- Nhúng Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: sans-serif; }
        .sidebar { min-height: 100vh; background-color: #fff; border-right: 1px solid #dee2e6; }
        .badge-shift { font-size: 0.8rem; padding: 4px 8px; border-radius: 4px; color: #fff; display: inline-block; margin: 2px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Menu bên trái -->
        <div class="col-md-2 sidebar p-3">
            <h4 class="text-primary fw-bold mb-4">HappyTime</h4>
            <ul class="nav flex-column gap-2">
                <li class="nav-item"><a class="nav-link active fw-bold" href="#">📅 Phân ca làm việc</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="#">⏱️ Bảng công</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="#">📝 Đơn từ</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="#">👥 Nhân viên</a></li>
            </ul>
        </div>

        <!-- Bảng nội dung chính bên phải -->
        <div class="col-md-10 p-4">
            <h3 class="fw-bold mb-3">Lịch phân ca làm việc</h3>

            <!-- Chú thích danh mục các ca làm việc -->
            <div class="mb-3 d-flex gap-2 align-items-center flex-wrap">
                <span class="fw-bold">Danh mục ca:</span>
                @foreach($caLamViecs as $ca)
                    <span class="badge-shift" style="background-color: {{ $ca->mau_nhan }}">{{ $ca->ten_ca }} ({{ $ca->ma_ca }})</span>
                @endforeach
            </div>

            <!-- Bảng Ma trận Phân ca -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover m-0 align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 220px;">Mã & Tên Nhân viên</th>
                                <th>Hôm nay</th>
                                <th>Thứ 2</th>
                                <th>Thứ 3</th>
                                <th>Thứ 4</th>
                                <th>Thứ 5</th>
                                <th>Thứ 6</th>
                                <th>Thứ 7</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nhanViens as $nv)
                            <tr>
                                <td>
                                    <strong>{{ $nv->ho_ten }}</strong><br>
                                    <small class="text-muted">{{ $nv->ma_nv }} - {{ $nv->phongBan->ten_phongban ?? 'Chưa xếp PB' }}</small>
                                </td>

                                <!-- Cột hiển thị các ca làm việc của nhân viên trong ngày -->
                                <td class="text-center">
                                    @forelse($nv->phanCas as $pc)
                                        <span class="badge-shift" style="background-color: {{ $pc->caLamViec->mau_nhan }}">
                                            {{ $pc->caLamViec->ten_ca }}
                                        </span>
                                    @empty
                                        <span class="text-muted small">Nghỉ</span>
                                    @endforelse
                                </td>

                                <!-- Các cột ngày khác -->
                                <td class="text-center"><span class="text-muted small">--</span></td>
                                <td class="text-center"><span class="text-muted small">--</span></td>
                                <td class="text-center"><span class="text-muted small">--</span></td>
                                <td class="text-center"><span class="text-muted small">--</span></td>
                                <td class="text-center"><span class="text-muted small">--</span></td>
                                <td class="text-center"><span class="text-muted small">--</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Chưa có dữ liệu nhân viên.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
