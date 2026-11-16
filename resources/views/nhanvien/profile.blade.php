@extends('layouts.app')
@section('title', 'Hồ sơ cá nhân & Gửi Ảnh / CV')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h4 class="fw-bold mb-1">Hồ sơ cá nhân & Gửi Ảnh / CV</h4>
                    <div class="text-muted" style="font-size:.88rem">
                        Mã tài khoản: <strong>{{ $nv->ma_nv }}</strong> &bull; Email: <strong>{{ $nv->email }}</strong>
                    </div>
                </div>
                <div>
                    @if((int) $nv->trang_thai === 2)
                        <span class="status-badge status-pending">
                            <i class="fas fa-hourglass-half me-1"></i> Đang đợi Admin duyệt
                        </span>
                    @elseif((int) $nv->trang_thai === 1)
                        <span class="status-badge status-active">
                            <i class="fas fa-circle-check me-1"></i> Nhân viên chính thức
                        </span>
                    @endif
                </div>
            </div>

            @if((int) $nv->trang_thai === 2)
                <div class="alert alert-warning border-0" style="border-radius:12px">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Tài khoản của bạn đang nằm trong danh sách đợi duyệt.</strong>
                    Vui lòng tải lên <strong>Ảnh chân dung</strong> và <strong>File CV</strong> bên dưới để Quản trị viên xét duyệt hồ sơ vào danh sách nhân viên chính thức.
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0" style="border-radius:12px">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('hoso.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Họ và tên</label>
                        <input type="text" name="ho_ten" value="{{ old('ho_ten', $nv->ho_ten) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Số điện thoại</label>
                        <input type="text" name="so_dien_thoai" value="{{ old('so_dien_thoai', $nv->so_dien_thoai) }}" class="form-control" placeholder="Nhập số điện thoại...">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tải lên Ảnh chân dung / Ảnh đại diện (JPG, PNG, WEBP)</label>
                        <input type="file" name="anh_dai_dien" accept="image/*" class="form-control">
                        @if($nv->anh_dai_dien)
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <img src="{{ asset($nv->anh_dai_dien) }}" alt="Ảnh hiện tại" style="width:64px;height:64px;object-fit:cover;border-radius:12px;border:1px solid #ddd">
                                <a href="{{ asset($nv->anh_dai_dien) }}" target="_blank" class="small text-decoration-none">Xem ảnh đã gửi</a>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tải lên CV xin việc (PDF, DOC, DOCX, Ảnh)</label>
                        <input type="file" name="cv_file" accept=".pdf,.doc,.docx,image/*" class="form-control">
                        @if($nv->cv_file)
                            <div class="mt-2">
                                <a href="{{ asset($nv->cv_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-arrow-down me-1"></i> Xem CV đã tải lên
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn-create border-0">
                        <i class="fas fa-cloud-arrow-up"></i> Lưu thông tin & Gửi Ảnh / CV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
