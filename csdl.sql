USE `qlns`;

-- 1. BẢNG PHÒNG BAN
CREATE TABLE IF NOT EXISTS `phongban` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_phongban` VARCHAR(20) NOT NULL UNIQUE,
    `ten_phongban` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. BẢNG CHỨC VỤ
CREATE TABLE IF NOT EXISTS `chucvu` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ten_chucvu` VARCHAR(100) NOT NULL,
    `he_so_luong` FLOAT DEFAULT 1.0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. BẢNG NHÂN VIÊN
CREATE TABLE IF NOT EXISTS `nhanvien` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_nv` VARCHAR(20) NOT NULL UNIQUE,
    `ho_ten` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `mat_khau` VARCHAR(255) NOT NULL,
    `so_dien_thoai` VARCHAR(15),
    `phongban_id` INT,
    `chucvu_id` INT,
    `vai_tro` ENUM('Admin', 'Manager', 'Employee') DEFAULT 'Employee',
    `trang_thai` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`phongban_id`) REFERENCES `phongban`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`chucvu_id`) REFERENCES `chucvu`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. BẢNG CA LÀM VIỆC
CREATE TABLE IF NOT EXISTS `calamviec` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ma_ca` VARCHAR(20) NOT NULL UNIQUE,
    `ten_ca` VARCHAR(100) NOT NULL,
    `gio_bat_dau` TIME NOT NULL,
    `gio_ket_thuc` TIME NOT NULL,
    `so_gio_cong` FLOAT DEFAULT 8.0,
    `mau_nhan` VARCHAR(10) DEFAULT '#3B82F6'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. BẢNG PHÂN CA
CREATE TABLE IF NOT EXISTS `phanca` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nhanvien_id` INT NOT NULL,
    `calamviec_id` INT NOT NULL,
    `ngay_lam_viec` DATE NOT NULL,
    `ghi_chu` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`calamviec_id`) REFERENCES `calamviec`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. BẢNG CHẤM CÔNG
CREATE TABLE IF NOT EXISTS `chamcong` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `phanca_id` INT NOT NULL,
    `gio_checkin` DATETIME,
    `gio_checkout` DATETIME,
    `trang_thai` ENUM('DungGio', 'DiTre', 'VeSom', 'VangMat') DEFAULT 'DungGio',
    FOREIGN KEY (`phanca_id`) REFERENCES `phanca`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. BẢNG ĐƠN TỪ
CREATE TABLE IF NOT EXISTS `dontu` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nhanvien_id` INT NOT NULL,
    `loai_don` ENUM('NghiPhep', 'DoiCa', 'DiTreVeSom') NOT NULL,
    `ngay_nop` DATE NOT NULL,
    `ngay_ap_dung` DATE NOT NULL,
    `ly_do` TEXT,
    `trang_thai` ENUM('ChoDuyet', 'DaDuyet', 'TuChoi') DEFAULT 'ChoDuyet',
    `nguoi_duyet_id` INT,
    FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`nguoi_duyet_id`) REFERENCES `nhanvien`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- NẠP DỮ LIỆU MẪU BAN ĐẦU
-- =========================================================
INSERT INTO `phongban` (`id`, `ma_phongban`, `ten_phongban`) VALUES
(1, 'PB_NS', 'Phòng Nhân sự'), (2, 'PB_KT', 'Phòng Kỹ thuật');

INSERT INTO `chucvu` (`id`, `ten_chucvu`, `he_so_luong`) VALUES
(1, 'Trưởng phòng', 2.0), (2, 'Nhân viên', 1.0);

INSERT INTO `calamviec` (`id`, `ma_ca`, `ten_ca`, `gio_bat_dau`, `gio_ket_thuc`, `so_gio_cong`, `mau_nhan`) VALUES
(1, 'CASANG', 'Ca Sáng', '08:00:00', '12:00:00', 4.0, '#F97316'),
(2, 'CACHIEU', 'Ca Chiều', '13:00:00', '17:00:00', 4.0, '#A855F7'),
(3, 'CAEXCEL', 'Ca Excel Linh Hoạt', '08:30:00', '17:30:00', 8.0, '#22C55E');

INSERT INTO `nhanvien` (`id`, `ma_nv`, `ho_ten`, `email`, `mat_khau`, `phongban_id`, `chucvu_id`, `vai_tro`) VALUES
(1, 'NV001', 'Nguyễn Huy Minh (Admin)', 'admin@happytime.vn', '\$2y\$10\$e.1234567890abcdef...', 1, 1, 'Admin'),
(2, 'NV002', 'Nguyễn Huy Minh (NV)', 'nv@happytime.vn', '\$2y\$10\$e.1234567890abcdef...', 2, 2, 'Employee');

INSERT INTO `phanca` (`nhanvien_id`, `calamviec_id`, `ngay_lam_viec`) VALUES
(1, 1, CURRENT_DATE), (1, 3, CURRENT_DATE), (2, 2, CURRENT_DATE);