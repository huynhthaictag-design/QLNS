@php
    $shortDays = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
@endphp

<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius:20px;">
            <form method="POST" action="{{ route('phanca.store') }}">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div>
                        <h5 class="modal-title fw-bold mb-0"><i class="fas fa-calendar-plus text-primary me-2"></i>Phân công ca làm việc</h5>
                        <div class="muted">Chọn nhân viên, khung ca và một hoặc nhiều ngày trong tuần</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- 1. Chọn nhân viên --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nhân viên</label>
                        <select name="nhanvien_id" id="assign_nv" class="form-select" required>
                            <option value="">-- Chọn nhân viên --</option>
                            @foreach($allActiveNhanViens as $nv)
                                <option value="{{ $nv->id }}">
                                    {{ $nv->ho_ten }} ({{ $nv->ma_nv }}){{ $nv->phongBan ? ' - ' . $nv->phongBan->ten_phongban : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Chọn ngày làm (cho phép tick nhiều ngày trong tuần đang xem) --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold mb-0">Ngày áp dụng (chọn nhanh trong tuần)</label>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-light rounded-pill py-0 px-2" style="font-size:.74rem;" id="btnSelectWeekdays">T2–T6</button>
                                <button type="button" class="btn btn-sm btn-light rounded-pill py-0 px-2" style="font-size:.74rem;" id="btnSelectAllDays">Cả tuần</button>
                                <button type="button" class="btn btn-sm btn-light rounded-pill py-0 px-2" style="font-size:.74rem;" id="btnClearDays">Bỏ chọn</button>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach($days as $idx => $d)
                                <label class="day-toggle-pill">
                                    <input type="checkbox" name="ngay_lam_viecs[]" value="{{ $d->toDateString() }}" class="js-day-check">
                                    <span>
                                        <small>{{ $shortDays[$idx] }}</small>
                                        <strong>{{ $d->format('d/m') }}</strong>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <div class="small text-muted">
                            Hoặc chọn ngày cụ thể khác:
                            <input type="date" name="ngay_lam_viec" id="assign_ngay" class="form-control form-control-sm d-inline-block ms-1" style="width:auto;">
                        </div>
                    </div>

                    {{-- 3. Chọn ca làm việc dạng thẻ trực quan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chọn ca làm việc</label>
                        <div class="row g-2">
                            @foreach($caLamViecs as $index => $ca)
                                <div class="col-md-4 col-sm-6">
                                    <input type="radio" name="calamviec_id" id="ca_opt_{{ $ca->id }}" value="{{ $ca->id }}" class="d-none shift-option-input" {{ $index === 0 ? 'checked' : '' }} required>
                                    <label for="ca_opt_{{ $ca->id }}" class="shift-option-card h-100" style="border-left:4px solid {{ $ca->mau_nhan ?: '#7c3aed' }};">
                                        <div class="fw-bold small">{{ $ca->ten_ca }}</div>
                                        <div class="muted" style="font-size:.75rem;">
                                            <i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($ca->gio_bat_dau)->format('H:i') }} – {{ \Carbon\Carbon::parse($ca->gio_ket_thuc)->format('H:i') }}
                                        </div>
                                        <div class="muted" style="font-size:.72rem;">{{ $ca->so_gio_cong }} giờ công</div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 4. Ghi chú & tùy chọn gửi email --}}
                    <div class="row g-3 align-items-center">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Ghi chú (không bắt buộc)</label>
                            <input type="text" name="ghi_chu" class="form-control" placeholder="VD: Trực quầy lễ tân, hỗ trợ sự kiện...">
                        </div>
                        <div class="col-md-5 pt-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="send_email" value="1" id="sendEmailSwitch">
                                <label class="form-check-label small fw-semibold" for="sendEmailSwitch">
                                    Gửi email thông báo cho NV
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn-create px-4"><i class="fas fa-check me-1"></i> Lưu phân công</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    (function () {
        var modalEl = document.getElementById('assignModal');
        var nvSelect = document.getElementById('assign_nv');
        var dateInput = document.getElementById('assign_ngay');
        var dayChecks = document.querySelectorAll('.js-day-check');

        function clearAllDayChecks() {
            dayChecks.forEach(function (cb) { cb.checked = false; });
        }

        // Bấm "+ Gán ca" tại một ô cụ thể (nhân viên + ngày)
        document.querySelectorAll('.js-assign').forEach(function (btn) {
            btn.addEventListener('click', function () {
                nvSelect.value = this.dataset.nv;
                var targetDate = this.dataset.ngay;
                clearAllDayChecks();

                var matched = false;
                dayChecks.forEach(function (cb) {
                    if (cb.value === targetDate) {
                        cb.checked = true;
                        matched = true;
                    }
                });
                dateInput.value = matched ? '' : targetDate;
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            });
        });

        // Bấm nút "+ Phân ca" chung ở header
        document.querySelectorAll('.js-open-assign-general').forEach(function (btn) {
            btn.addEventListener('click', function () {
                clearAllDayChecks();
                dateInput.value = "{{ now()->toDateString() }}";
            });
        });

        // Nút chọn nhanh T2-T6, Cả tuần, Bỏ chọn
        var btnWeekdays = document.getElementById('btnSelectWeekdays');
        var btnAll = document.getElementById('btnSelectAllDays');
        var btnClear = document.getElementById('btnClearDays');

        if (btnWeekdays) {
            btnWeekdays.addEventListener('click', function () {
                dayChecks.forEach(function (cb, idx) { cb.checked = idx < 5; });
                dateInput.value = '';
            });
        }
        if (btnAll) {
            btnAll.addEventListener('click', function () {
                dayChecks.forEach(function (cb) { cb.checked = true; });
                dateInput.value = '';
            });
        }
        if (btnClear) {
            btnClear.addEventListener('click', function () {
                clearAllDayChecks();
            });
        }
    })();
</script>
@endsection
