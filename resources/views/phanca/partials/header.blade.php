<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="mb-0 fw-bold">{{ $canManage ? 'Phân công lịch làm việc' : 'Lịch làm của tôi' }}</h3>
        <p class="text-muted small mb-0">
            Tuần {{ $weekStart->format('d/m') }} - {{ $weekEnd->format('d/m/Y') }}
            @if(!$canManage)
                · Bạn chỉ xem được ca đã được phân công
            @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-light rounded-pill" href="{{ route('phanca.index', ['tuan' => $prevWeek]) }}">&larr; Tuần trước</a>
        <a class="btn btn-light rounded-pill" href="{{ route('phanca.index') }}">Tuần này</a>
        <a class="btn btn-light rounded-pill" href="{{ route('phanca.index', ['tuan' => $nextWeek]) }}">Tuần sau &rarr;</a>
        @if($canManage)
            <button class="btn-create" data-bs-toggle="modal" data-bs-target="#assignModal">+ Phân ca</button>
        @endif
    </div>
</div>
