@extends('layouts.app')
@section('title', 'Quản lý Ca làm việc')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="mb-1 fw-bold">Danh mục Ca làm việc</h3>
        <p class="text-muted small mb-0">Thiết lập khung giờ, số giờ công và màu nhãn hiển thị trên bảng phân ca</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('phanca.index') }}" class="btn btn-light rounded-pill fw-semibold">
            <i class="fas fa-arrow-left me-1"></i> Về bảng phân ca
        </a>
        <button class="btn-create" data-bs-toggle="modal" data-bs-target="#createShiftModal">
            <i class="fas fa-plus me-1"></i> Thêm ca mới
        </button>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-3" style="border-radius:14px;">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    @forelse($caLamViecs as $ca)
        <div class="col-md-4 col-sm-6">
            <div class="panel p-4 h-100 d-flex flex-column justify-content-between" style="border-top: 4px solid {{ $ca->mau_nhan ?: '#7c3aed' }};">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge rounded-pill px-3 py-2 text-white" style="background: {{ $ca->mau_nhan ?: '#7c3aed' }}">
                            {{ $ca->ma_ca }}
                        </span>
                        <span class="muted small">{{ $ca->so_luot_phan }} lượt đã phân</span>
                    </div>
                    <h5 class="fw-bold mb-2">{{ $ca->ten_ca }}</h5>
                    <div class="d-flex align-items-center gap-3 small text-muted mb-3">
                        <span><i class="far fa-clock me-1 text-primary"></i>{{ \Carbon\Carbon::parse($ca->gio_bat_dau)->format('H:i') }} – {{ \Carbon\Carbon::parse($ca->gio_ket_thuc)->format('H:i') }}</span>
                        <span><i class="fas fa-hourglass-half me-1 text-warning"></i>{{ $ca->so_gio_cong }} giờ công</span>
                    </div>
                </div>
                <div class="d-flex gap-2 pt-2 border-top" style="border-color:#f3eefc!important;">
                    <button type="button" class="btn btn-sm btn-light rounded-pill flex-fill fw-semibold js-edit-shift"
                        data-id="{{ $ca->id }}"
                        data-ma="{{ $ca->ma_ca }}"
                        data-ten="{{ $ca->ten_ca }}"
                        data-start="{{ \Carbon\Carbon::parse($ca->gio_bat_dau)->format('H:i') }}"
                        data-end="{{ \Carbon\Carbon::parse($ca->gio_ket_thuc)->format('H:i') }}"
                        data-gio="{{ $ca->so_gio_cong }}"
                        data-mau="{{ $ca->mau_nhan }}">
                        <i class="fas fa-pen me-1"></i> Sửa
                    </button>
                    <form action="{{ route('calamviec.destroy', $ca->id) }}" method="POST" onsubmit="return confirm('Xóa ca làm việc {{ $ca->ten_ca }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="panel p-5 text-center text-muted">Chưa có ca làm việc nào. Nhấn "Thêm ca mới" để tạo.</div>
        </div>
    @endforelse
</div>

{{-- Modal Thêm Ca --}}
<div class="modal fade" id="createShiftModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px;">
            <form method="POST" action="{{ route('calamviec.store') }}">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Thêm ca làm việc mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Mã ca</label>
                            <input type="text" name="ma_ca" class="form-control" placeholder="VD: CATOI" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Tên ca</label>
                            <input type="text" name="ten_ca" class="form-control" placeholder="VD: Ca Tối" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giờ bắt đầu</label>
                            <input type="time" name="gio_bat_dau" class="form-control" value="08:00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giờ kết thúc</label>
                            <input type="time" name="gio_ket_thuc" class="form-control" value="12:00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số giờ công</label>
                            <input type="number" step="0.5" name="so_gio_cong" class="form-control" placeholder="Tự tính nếu để trống">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Màu nhãn</label>
                            <input type="color" name="mau_nhan" class="form-control form-control-color w-100" value="#3B82F6" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-create">Lưu ca làm</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Sửa Ca --}}
<div class="modal fade" id="editShiftModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px;">
            <form method="POST" id="editShiftForm">
                @csrf
                @method('PUT')
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Chỉnh sửa ca làm việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Mã ca</label>
                            <input type="text" name="ma_ca" id="edit_ma" class="form-control" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Tên ca</label>
                            <input type="text" name="ten_ca" id="edit_ten" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giờ bắt đầu</label>
                            <input type="time" name="gio_bat_dau" id="edit_start" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giờ kết thúc</label>
                            <input type="time" name="gio_ket_thuc" id="edit_end" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số giờ công</label>
                            <input type="number" step="0.5" name="so_gio_cong" id="edit_gio" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Màu nhãn</label>
                            <input type="color" name="mau_nhan" id="edit_mau" class="form-control form-control-color w-100" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-create">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.js-edit-shift').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('editShiftForm').action = "{{ url('/ca-lam-viec') }}/" + this.dataset.id;
            document.getElementById('edit_ma').value = this.dataset.ma;
            document.getElementById('edit_ten').value = this.dataset.ten;
            document.getElementById('edit_start').value = this.dataset.start;
            document.getElementById('edit_end').value = this.dataset.end;
            document.getElementById('edit_gio').value = this.dataset.gio;
            document.getElementById('edit_mau').value = this.dataset.mau || '#3B82F6';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editShiftModal')).show();
        });
    });
</script>
@endsection
