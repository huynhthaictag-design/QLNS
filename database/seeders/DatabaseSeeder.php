<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CaLamViec;
use App\Models\User;
use App\Models\PhanCa;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Khởi tạo đầy đủ 3 biến ca làm việc mẫu
        $caSang = CaLamViec::create([
            'ma_ca' => 'CASANG',
            'ten_ca' => 'Ca Sáng',
            'gio_bat_dau' => '08:00:00',
            'gio_ket_thuc' => '12:00:00',
            'so_gio_cong' => 4.0,
            'mau_nhan' => '#F97316'
        ]);

        $caChieu = CaLamViec::create([
            'ma_ca' => 'CACHIEU',
            'ten_ca' => 'Ca Chiều',
            'gio_bat_dau' => '13:00:00',
            'gio_ket_thuc' => '17:00:00',
            'so_gio_cong' => 4.0,
            'mau_nhan' => '#A855F7'
        ]);

        $caExcel = CaLamViec::create([
            'ma_ca' => 'CAEXCEL',
            'ten_ca' => 'Ca Excel',
            'gio_bat_dau' => '08:30:00',
            'gio_ket_thuc' => '17:30:00',
            'so_gio_cong' => 8.0,
            'mau_nhan' => '#22C55E'
        ]);

        // 2. Tạo tài khoản nhân viên mẫu
        $user1 = User::create([
            'ma_nv' => 'NV001',
            'ho_ten' => 'Nguyễn Huy Minh (CEO)',
            'email' => 'ceo@happytime.vn',
            'mat_khau' => Hash::make('123456'),
            'vai_tro' => 'Admin',
            'trang_thai' => 1
        ]);

        $user2 = User::create([
            'ma_nv' => 'NV002',
            'ho_ten' => 'Nguyễn Huy Minh (TP)',
            'email' => 'tp@happytime.vn',
            'mat_khau' => Hash::make('123456'),
            'vai_tro' => 'Manager',
            'trang_thai' => 1
        ]);

        // 3. Phân ca mẫu (Gắn đúng biến $caSang đã tạo ở trên)
        PhanCa::create([
            'nhanvien_id' => $user1->id,
            'calamviec_id' => $caSang->id,
            'ngay_lam_viec' => date('Y-m-d')
        ]);

        PhanCa::create([
            'nhanvien_id' => $user1->id,
            'calamviec_id' => $caExcel->id,
            'ngay_lam_viec' => date('Y-m-d')
        ]);

        PhanCa::create([
            'nhanvien_id' => $user2->id,
            'calamviec_id' => $caChieu->id,
            'ngay_lam_viec' => date('Y-m-d')
        ]);
    }
}
