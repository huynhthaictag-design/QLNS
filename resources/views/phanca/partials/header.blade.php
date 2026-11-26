<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="mb-1 fw-bold">
            {{ $canManage ? 'Bảng phân ca làm việc' : 'Lịch làm việc của tôi' }}
        </h3>
        <p class="text-muted small mb-0">
            Tuần từ <strong>{{ $weekStart->format('d/m/Y') }}</strong> đến <strong>{{ $weekEnd->format('d/m/Y') }}</strong>
            @if(!$canManage)
                · Theo dõi lịch trực cá nhân trong tuần
            @endif
        </p>
    </div>

    <div class="d-flex align-items-center flex-wrap gap-2">
        {{-- Điều hướng tuần dạng pill --}}
        <div class="week-nav-pill">
            <a href="{{ route('phanca.index', array_filter(['tuan' => $prevWeek, 'phongban_id' => $phongBanId, 'calamviec_id' => $caFilterId, 'q' => $keyword])) }}" title="Tuần trước">
                <i class="fas fa-chevron-left"></i>
            </a>
            <span class="week-range">{{ $weekStart->format('d/m') }} – {{ $weekEnd->format('d/m/Y') }}</span>
            <a href="{{ route('phanca.index', array_filter(['tuan' => $nextWeek, 'phongban_id' => $phongBanId, 'calamviec_id' => $caFilterId, 'q' => $keyword])) }}" title="Tuần sau">
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="{{ route('phanca.index', array_filter(['phongban_id' => $phongBanId, 'calamviec_id' => $caFilterId, 'q' => $keyword])) }}" class="text-primary">
                Tuần này
            </a>
        </div>

        @if($canManage)
            <form method="POST" action="{{ route('phanca.copy') }}" class="d-inline" onsubmit="return confirm('Sao chép toàn bộ lịch phân ca từ tuần trước sang tuần {{ $weekStart->format('d/m') }} - {{ $weekEnd->format('d/m') }}?')">
                @csrf
                <input type="hidden" name="tuan" value="{{ $weekStart->toDateString() }}">
                <button type="submit" class="btn btn-light rounded-pill fw-semibold border" style="font-size:.84rem;" title="Sao chép lịch từ tuần trước">
                    <i class="fas fa-copy me-1 text-primary"></i> Chép tuần trước
                </button>
            </form>

            <a href="{{ route('phanca.export', ['tuan' => $weekStart->toDateString()]) }}" class="btn btn-light rounded-pill fw-semibold border text-success" style="font-size:.84rem;">
                <i class="fas fa-file-excel me-1"></i> Xuất Excel
            </a>

            <a href="{{ route('calamviec.index') }}" class="btn btn-light rounded-pill fw-semibold border" style="font-size:.84rem;">
                <i class="fas fa-sliders me-1 text-secondary"></i> Quản lý ca
            </a>

            <button type="button" class="btn-create js-open-assign-general" data-bs-toggle="modal" data-bs-target="#assignModal">
                <i class="fas fa-plus"></i> Phân ca
            </button>
        @endif
    </div>
</div>
