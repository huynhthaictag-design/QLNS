<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:18px">
            <form method="POST" action="{{ route('phanca.store') }}">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Phân công ca làm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nhân viên</label>
                        <select name="nhanvien_id" id="assign_nv" class="form-select" required>
                            <option value="">-- Chọn nhân viên --</option>
                            @foreach($nhanViens as $nv)
                                <option value="{{ $nv->id }}">{{ $nv->ho_ten }} ({{ $nv->ma_nv }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ngày làm</label>
                        <input type="date" name="ngay_lam_viec" id="assign_ngay" class="form-control" required value="{{ now()->toDateString() }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ca làm việc</label>
                        <select name="calamviec_id" class="form-select" required>
                            <option value="">-- Chọn ca --</option>
                            @foreach($caLamViecs as $ca)
                                <option value="{{ $ca->id }}">
                                    {{ $ca->ten_ca }} ({{ \Carbon\Carbon::parse($ca->gio_bat_dau)->format('H:i') }} - {{ \Carbon\Carbon::parse($ca->gio_ket_thuc)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Ghi chú</label>
                        <input type="text" name="ghi_chu" class="form-control" placeholder="Không bắt buộc">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-create">Lưu phân công</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.querySelectorAll('.js-assign').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('assign_nv').value = this.dataset.nv;
            document.getElementById('assign_ngay').value = this.dataset.ngay;
            var modal = new bootstrap.Modal(document.getElementById('assignModal'));
            modal.show();
        });
    });
</script>
@endsection
