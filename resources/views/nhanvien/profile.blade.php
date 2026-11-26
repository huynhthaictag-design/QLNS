@extends('layouts.app')
@section('title', 'Thông tin cá nhân - Profile')

@section('styles')
<style>
    /* Khung Banner Tím Đậm (Hero Profile Card) */
    .profile-hero {
        background: radial-gradient(circle at 20% 25%, #3b1d70 0%, #1b1233 55%, #140d26 100%);
        border-radius: 22px;
        color: #fff;
        overflow: hidden;
        box-shadow: 0 14px 34px rgba(27, 18, 51, .16);
        margin-bottom: 24px;
    }
    .profile-hero-top {
        padding: 28px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
    }
    .profile-hero-left {
        display: flex;
        align-items: center;
        gap: 22px;
        flex-wrap: wrap;
    }
    .hero-avatar-wrap {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        padding: 4px;
        background: linear-gradient(135deg, #a855f7, #6366f1);
        box-shadow: 0 0 24px rgba(168, 85, 247, .45);
        flex-shrink: 0;
    }
    .hero-avatar-inner {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        overflow: hidden;
        background: #2d1f55;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.85rem;
        font-weight: 800;
        color: #fff;
        border: 3px solid #1b1233;
    }
    .hero-avatar-inner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .hero-name {
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0 0 4px;
        color: #fff;
    }
    .hero-role {
        font-size: .92rem;
        color: #d8b4fe;
        margin-bottom: 10px;
        font-weight: 500;
    }
    .hero-role span {
        color: #9f94b8;
    }
    .hero-meta {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
        font-size: .82rem;
        color: #b8add0;
    }
    .hero-meta i {
        color: #a855f7;
        margin-right: 5px;
    }
    .hero-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .btn-hero-share {
        background: rgba(255, 255, 255, .08);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 999px;
        padding: 9px 18px;
        font-size: .85rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: .2s;
    }
    .btn-hero-share:hover {
        background: rgba(255, 255, 255, .15);
        color: #fff;
    }
    .btn-hero-edit {
        background: linear-gradient(90deg, #9333ea, #7c3aed);
        color: #fff;
        border: none;
        border-radius: 999px;
        padding: 9px 20px;
        font-size: .85rem;
        font-weight: 700;
        box-shadow: 0 8px 20px rgba(147, 51, 234, .4);
        display: inline-flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
    }
    .profile-hero-stats {
        border-top: 1px solid rgba(255, 255, 255, .08);
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        background: rgba(15, 10, 28, .28);
    }
    .hero-stat-item {
        padding: 16px 12px;
        text-align: center;
        border-right: 1px solid rgba(255, 255, 255, .08);
    }
    .hero-stat-item:last-child {
        border-right: none;
    }
    .hero-stat-item h4 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: #fff;
    }
    .hero-stat-item span {
        font-size: .76rem;
        color: #9b93b3;
    }

    /* Cột trái: Skills & On the Web */
    .profile-card {
        background: #fff;
        border-radius: 20px;
        padding: 22px 24px;
        box-shadow: 0 10px 26px rgba(90, 60, 140, .05);
        margin-bottom: 22px;
    }
    .profile-card-title {
        font-size: .95rem;
        font-weight: 800;
        color: #2b2440;
        margin-bottom: 16px;
    }
    .skills-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .skill-tag {
        background: #f3eefc;
        color: #6d28d9;
        border-radius: 999px;
        padding: 6px 14px;
        font-size: .78rem;
        font-weight: 600;
    }
    .web-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .web-item {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: #4c4463;
        font-size: .85rem;
        font-weight: 500;
        word-break: break-all;
    }
    .web-item:hover {
        color: #7c3aed;
    }
    .web-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #f3eefc;
        color: #7c3aed;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        flex-shrink: 0;
    }

    /* Cột phải: Tabs & Form */
    .profile-tabs {
        background: #f3eefc;
        padding: 5px;
        border-radius: 999px;
        display: inline-flex;
        gap: 4px;
        margin-bottom: 22px;
    }
    .profile-tab-btn {
        border: none;
        background: transparent;
        color: #6b647c;
        font-weight: 600;
        font-size: .84rem;
        padding: 7px 18px;
        border-radius: 999px;
        cursor: pointer;
        transition: .2s;
    }
    .profile-tab-btn.active {
        background: #fff;
        color: #7c3aed;
        box-shadow: 0 4px 12px rgba(90, 60, 140, .08);
    }
    .photo-upload-box {
        border: 1.5px dashed #e2d8f3;
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 22px;
        background: #fcfbff;
        flex-wrap: wrap;
    }
    .photo-preview {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        overflow: hidden;
        background: linear-gradient(135deg, #60a5fa, #7c3aed);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .btn-upload-pill {
        border: 1px solid #e2d8f3;
        background: #fff;
        color: #3b3354;
        border-radius: 999px;
        padding: 6px 16px;
        font-size: .8rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: .15s;
    }
    .btn-upload-pill:hover {
        border-color: #7c3aed;
        color: #7c3aed;
    }
    .btn-remove-pill {
        border: 1px solid #fecdd3;
        background: #fff;
        color: #e11d48;
        border-radius: 999px;
        padding: 6px 16px;
        font-size: .8rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: .15s;
    }
    .btn-remove-pill:hover {
        background: #ffe4e6;
    }
    .profile-label {
        font-size: .82rem;
        font-weight: 700;
        color: #2b2440;
        margin-bottom: 6px;
    }
    .profile-input {
        border: 1px solid #eae3f5;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: .88rem;
        color: #2b2440;
        background: #fff;
        width: 100%;
        transition: .2s;
    }
    .profile-input:focus {
        outline: none;
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, .12);
    }
    .profile-input[readonly] {
        background: #f8f5fd;
        color: #7c7394;
    }
    @media (max-width: 768px) {
        .profile-hero-stats { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection

@section('content')
@if((int) $nv->trang_thai === 2)
    <div class="alert alert-warning border-0 shadow-sm mb-3" style="border-radius:14px">
        <i class="fas fa-hourglass-half me-2"></i>
        <strong>Tài khoản đang chờ Admin phê duyệt:</strong> Bạn vui lòng tải lên <strong>Ảnh đại diện (Profile photo)</strong> và <strong>File CV</strong> bên dưới để Quản trị viên duyệt vào danh sách nhân viên chính thức.
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-3" style="border-radius:14px">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- BANNER TÍM ĐẬM TRÊN CÙNG (HERO BANNER) -->
<div class="profile-hero">
    <div class="profile-hero-top">
        <div class="profile-hero-left">
            <div class="hero-avatar-wrap">
                <div class="hero-avatar-inner">
                    @if($nv->anh_dai_dien)
                        <img id="heroAvatarImg" src="{{ asset($nv->anh_dai_dien) }}" alt="{{ $nv->ho_ten }}">
                    @else
                        <span id="heroAvatarInitials">{{ $nv->initials() }}</span>
                        <img id="heroAvatarImg" src="" alt="{{ $nv->ho_ten }}" style="display:none">
                    @endif
                </div>
            </div>
            <div>
                <h3 class="hero-name">{{ $nv->ho_ten }}</h3>
                <div class="hero-role">
                    {{ $nv->chucvu ? $nv->chucvu->ten_chucvu : 'Nhân viên / Ứng viên' }}
                    <span>&bull; {{ $nv->phongban ? $nv->phongban->ten_phongban : 'HappyTime HR System' }}</span>
                </div>
                <div class="hero-meta">
                    <span><i class="fas fa-location-dot"></i> Long Xuyên, An Giang</span>
                    <span><i class="far fa-envelope"></i> {{ $nv->email }}</span>
                    <span><i class="far fa-calendar"></i> Member since {{ $nv->created_at ? $nv->created_at->format('Y') : '2026' }}</span>
                </div>
            </div>
        </div>

        <div class="hero-actions">
            @if($nv->cv_file)
                <a href="{{ asset($nv->cv_file) }}" target="_blank" class="btn-hero-share">
                    <i class="fas fa-file-pdf"></i> Xem CV
                </a>
            @else
                <span class="btn-hero-share">
                    <i class="fas fa-share-nodes"></i> {{ $nv->ma_nv }}
                </span>
            @endif
            <button type="button" class="btn-hero-edit" onclick="switchTab('edit')">
                <i class="fas fa-pen"></i> Edit Profile
            </button>
        </div>
    </div>

    <div class="profile-hero-stats">
        <div class="hero-stat-item">
            <h4>{{ $shiftCount ?? 0 }}</h4>
            <span>Ca làm việc</span>
        </div>
        <div class="hero-stat-item">
            <h4>{{ $nv->chucvu ? number_format($nv->chucvu->he_so_luong, 1) : '1.0' }}</h4>
            <span>Hệ số lương</span>
        </div>
        <div class="hero-stat-item">
            <h4>{{ $nv->roleLabel() }}</h4>
            <span>Vai trò</span>
        </div>
        <div class="hero-stat-item">
            <h4>{{ (int) $nv->trang_thai === 1 ? 'Active' : ((int) $nv->trang_thai === 2 ? 'Pending' : 'Inactive') }}</h4>
            <span>Trạng thái</span>
        </div>
    </div>
</div>

<!-- BỐ CỤC 2 CỘT BÊN DƯỚI -->
<div class="row g-4">
    <!-- CỘT TRÁI: SKILLS & ON THE WEB -->
    <div class="col-lg-4">
        <div class="profile-card">
            <div class="profile-card-title">Skills</div>
            <div class="skills-wrap">
                <span class="skill-tag">{{ $nv->phongban ? $nv->phongban->ten_phongban : 'Nhân sự' }}</span>
                <span class="skill-tag">{{ $nv->chucvu ? $nv->chucvu->ten_chucvu : 'Nhân viên' }}</span>
                <span class="skill-tag">Làm việc nhóm</span>
                <span class="skill-tag">Quản lý thời gian</span>
                <span class="skill-tag">Tin học văn phòng</span>
                <span class="skill-tag">Giao tiếp</span>
            </div>

            <hr class="my-3" style="border-color:#f1ecf8">
            <div class="profile-card-title mb-2">Hồ sơ CV đính kèm</div>
            @if($nv->cv_file)
                <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background:#f8f5fd;border:1px solid #eee7f8">
                    <div class="d-flex align-items-center gap-2 text-truncate">
                        <i class="fas fa-file-lines text-primary"></i>
                        <span class="small fw-semibold text-truncate">{{ basename($nv->cv_file) }}</span>
                    </div>
                    <a href="{{ asset($nv->cv_file) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3" style="background:#7c3aed;border:none;font-size:.75rem">
                        Mở xem
                    </a>
                </div>
            @else
                <div class="small text-muted">Chưa tải lên tệp CV nào.</div>
            @endif
        </div>

        <div class="profile-card">
            <div class="profile-card-title">On the Web</div>
            <div class="web-list">
                <a href="mailto:{{ $nv->email }}" class="web-item">
                    <span class="web-icon"><i class="fas fa-globe"></i></span>
                    <span>{{ $nv->email }}</span>
                </a>
                <div class="web-item">
                    <span class="web-icon"><i class="fas fa-phone"></i></span>
                    <span>{{ $nv->so_dien_thoai ?: 'Chưa cập nhật số điện thoại' }}</span>
                </div>
                <div class="web-item">
                    <span class="web-icon"><i class="fas fa-id-card"></i></span>
                    <span>Mã nhân viên: <strong>{{ $nv->ma_nv }}</strong></span>
                </div>
                <div class="web-item">
                    <span class="web-icon"><i class="fas fa-building"></i></span>
                    <span>{{ $nv->phongban ? $nv->phongban->ten_phongban : 'Chưa phân phòng ban' }}</span>
                </div>
                <div class="web-item">
                    <span class="web-icon"><i class="fas fa-briefcase"></i></span>
                    <span>{{ $nv->chucvu ? $nv->chucvu->ten_chucvu : 'Chưa phân chức vụ' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: OVERVIEW / EDIT PROFILE / PASSWORD -->
    <div class="col-lg-8">
        <div class="profile-card">
            <!-- Thanh chuyển Tab -->
            <div class="profile-tabs">
                <button type="button" class="profile-tab-btn" id="tabBtn-overview" onclick="switchTab('overview')">Overview</button>
                <button type="button" class="profile-tab-btn active" id="tabBtn-edit" onclick="switchTab('edit')">Edit Profile</button>
                <button type="button" class="profile-tab-btn" id="tabBtn-password" onclick="switchTab('password')">Password</button>
            </div>

            <!-- TAB 1: OVERVIEW -->
            <div id="tabPane-overview" style="display:none">
                <h6 class="fw-bold mb-2">Giới thiệu hồ sơ</h6>
                <p class="text-muted small mb-4">
                    Hồ sơ nhân sự của <strong>{{ $nv->ho_ten }}</strong> ({{ $nv->ma_nv }}) thuộc hệ thống Quản lý Nhân sự & Phân ca <strong>HappyTime</strong>.
                </p>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background:#f8f5fd">
                            <div class="small text-muted">Họ và tên</div>
                            <div class="fw-bold">{{ $nv->ho_ten }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background:#f8f5fd">
                            <div class="small text-muted">Mã nhân viên</div>
                            <div class="fw-bold">{{ $nv->ma_nv }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background:#f8f5fd">
                            <div class="small text-muted">Email</div>
                            <div class="fw-bold">{{ $nv->email }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background:#f8f5fd">
                            <div class="small text-muted">Số điện thoại</div>
                            <div class="fw-bold">{{ $nv->so_dien_thoai ?: 'Chưa cập nhật' }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background:#f8f5fd">
                            <div class="small text-muted">Phòng ban</div>
                            <div class="fw-bold">{{ $nv->phongban ? $nv->phongban->ten_phongban : 'Chưa xếp phòng' }}</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background:#f8f5fd">
                            <div class="small text-muted">Chức vụ</div>
                            <div class="fw-bold">{{ $nv->chucvu ? $nv->chucvu->ten_chucvu : 'Chưa phân chức vụ' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: EDIT PROFILE (MẶC ĐỊNH HIỂN THỊ GIỐNG ẢNH MẪU) -->
            <div id="tabPane-edit">
                <form action="{{ route('hoso.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Khung Profile photo viền nét đứt -->
                    <div class="photo-upload-box">
                        <div class="photo-preview">
                            @if($nv->anh_dai_dien)
                                <img id="formAvatarImg" src="{{ asset($nv->anh_dai_dien) }}" alt="{{ $nv->ho_ten }}">
                            @else
                                <span id="formAvatarInitials">{{ $nv->initials() }}</span>
                                <img id="formAvatarImg" src="" alt="{{ $nv->ho_ten }}" style="display:none">
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold" style="font-size:.92rem">Profile photo</div>
                            <div class="text-muted" style="font-size:.78rem;margin-bottom:10px">
                                PNG, JPG hoặc WEBP, khuyến nghị tối thiểu 400x400px (tối đa 4MB).
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <label for="avatarInput" class="btn-upload-pill">
                                    <i class="fas fa-arrow-up-from-bracket"></i> Upload
                                </label>
                                <input type="file" id="avatarInput" name="anh_dai_dien" accept="image/*" class="d-none" onchange="previewSelectedAvatar(this)">

                                @if($nv->anh_dai_dien)
                                    <button type="submit" name="remove_avatar" value="1" class="btn-remove-pill" onclick="return confirm('Xóa ảnh đại diện hiện tại?')">
                                        <i class="far fa-trash-can"></i> Remove
                                    </button>
                                @endif
                                <span id="selectedAvatarName" class="small text-success fw-semibold ms-1"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Các trường thông tin cá nhân giống bố cục 2 ảnh mẫu -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="profile-label">Full Name</label>
                            <input type="text" name="ho_ten" value="{{ old('ho_ten', $nv->ho_ten) }}" class="profile-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="profile-label">Job Title</label>
                            <input type="text" value="{{ $nv->chucvu ? $nv->chucvu->ten_chucvu : 'Nhân viên (' . $nv->roleLabel() . ')' }}" class="profile-input" readonly>
                        </div>

                        <div class="col-12">
                            <label class="profile-label">About</label>
                            <textarea name="ghi_chu_ho_so" rows="3" class="profile-input" readonly>Nhân sự tại hệ thống quản lý & phân ca HappyTime.</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="profile-label">Company / Department</label>
                            <input type="text" value="{{ $nv->phongban ? $nv->phongban->ten_phongban : 'HappyTime - Chưa xếp phòng ban' }}" class="profile-input" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="profile-label">Country</label>
                            <input type="text" value="Vietnam" class="profile-input" readonly>
                        </div>

                        <div class="col-12">
                            <label class="profile-label">Address</label>
                            <input type="text" value="Long Xuyên, An Giang, Việt Nam" class="profile-input" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="profile-label">Phone</label>
                            <input type="text" name="so_dien_thoai" value="{{ old('so_dien_thoai', $nv->so_dien_thoai) }}" class="profile-input" placeholder="Nhập số điện thoại...">
                        </div>
                        <div class="col-md-6">
                            <label class="profile-label">Email</label>
                            <input type="email" value="{{ $nv->email }}" class="profile-input" readonly>
                        </div>

                        <div class="col-12">
                            <label class="profile-label">CV / Resume đính kèm (PDF, DOC, DOCX, Ảnh - Tối đa 10MB)</label>
                            <input type="file" name="cv_file" accept=".pdf,.doc,.docx,image/*" class="profile-input">
                            @if($nv->cv_file)
                                <div class="mt-2 small">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    Đã tải lên: <a href="{{ asset($nv->cv_file) }}" target="_blank" class="fw-semibold text-decoration-none">{{ basename($nv->cv_file) }}</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="submit" class="btn-create border-0 px-4 py-2">
                            <i class="fas fa-floppy-disk me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- TAB 3: PASSWORD (ĐỔI MẬT KHẨU) -->
            <div id="tabPane-password" style="display:none">
                <form action="{{ route('hoso.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_action" value="password">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="profile-label">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" class="profile-input" placeholder="Nhập mật khẩu hiện tại..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="profile-label">Mật khẩu mới</label>
                            <input type="password" name="new_password" class="profile-input" placeholder="Tối thiểu 6 ký tự..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="profile-label">Xác nhận mật khẩu mới</label>
                            <input type="password" name="new_password_confirmation" class="profile-input" placeholder="Nhập lại mật khẩu mới..." required>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn-create border-0 px-4 py-2">
                            <i class="fas fa-key me-1"></i> Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var activeTab = @json(session('active_tab', 'edit'));
        if (['overview', 'edit', 'password'].includes(activeTab)) {
            switchTab(activeTab);
        }
    });

    function switchTab(tabName) {
        ['overview', 'edit', 'password'].forEach(function(name) {
            document.getElementById('tabPane-' + name).style.display = (name === tabName) ? 'block' : 'none';
            document.getElementById('tabBtn-' + name).classList.toggle('active', name === tabName);
        });
    }

    function previewSelectedAvatar(input) {
        if (input.files && input.files[0]) {
            var file = input.files[0];
            document.getElementById('selectedAvatarName').textContent = 'Đã chọn: ' + file.name;
            var reader = new FileReader();
            reader.onload = function(e) {
                var formImg = document.getElementById('formAvatarImg');
                var heroImg = document.getElementById('heroAvatarImg');
                var formInit = document.getElementById('formAvatarInitials');
                var heroInit = document.getElementById('heroAvatarInitials');

                if (formImg) { formImg.src = e.target.result; formImg.style.display = 'block'; }
                if (heroImg) { heroImg.src = e.target.result; heroImg.style.display = 'block'; }
                if (formInit) { formInit.style.display = 'none'; }
                if (heroInit) { heroInit.style.display = 'none'; }
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
