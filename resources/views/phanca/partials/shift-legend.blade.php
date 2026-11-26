{{-- 1. KPI Thống kê nhanh của tuần --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon purple"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <h3>{{ $summary['tong_ca'] }}</h3>
                    <div class="label">Tổng ca trong tuần</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon green"><i class="fas fa-clock"></i></div>
                <div>
                    <h3>{{ $summary['tong_gio'] }}h</h3>
                    <div class="label">Tổng giờ công dự kiến</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon amber"><i class="fas fa-user-clock"></i></div>
                <div>
                    <h3>{{ $summary['nv_co_lich'] }}/{{ $summary['tong_nv'] }}</h3>
                    <div class="label">Nhân sự đã xếp ca</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-left">
                <div class="stat-icon pink"><i class="fas fa-bolt"></i></div>
                <div>
                    <h3>{{ $summary['ca_hom_nay'] }}</h3>
                    <div class="label">Ca làm hôm nay</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. Thanh lọc Phòng ban + Tìm kiếm + Chú thích ca tương tác --}}
<div class="panel p-3 mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        {{-- Danh mục ca (bấm để lọc nhanh theo ca) --}}
        <div class="d-flex align-items-center flex-wrap gap-2">
            <a href="{{ route('phanca.index', array_filter(['tuan' => $weekStart->toDateString(), 'phongban_id' => $phongBanId, 'q' => $keyword])) }}"
               class="shift-chip {{ $caFilterId === '' ? 'active' : '' }}">
                <i class="fas fa-border-all"></i> Tất cả ca
            </a>
            @foreach($caLamViecs as $ca)
                <a href="{{ route('phanca.index', array_filter(['tuan' => $weekStart->toDateString(), 'phongban_id' => $phongBanId, 'calamviec_id' => $ca->id, 'q' => $keyword])) }}"
                   class="shift-chip {{ (string)$caFilterId === (string)$ca->id ? 'active' : '' }}">
                    <span class="chip-dot" style="background: {{ $ca->mau_nhan ?: '#7c3aed' }};"></span>
                    <span>{{ $ca->ten_ca }}</span>
                    <span class="chip-time text-muted fw-normal" style="font-size:.74rem;">
                        {{ \Carbon\Carbon::parse($ca->gio_bat_dau)->format('H:i') }}–{{ \Carbon\Carbon::parse($ca->gio_ket_thuc)->format('H:i') }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Bộ lọc phòng ban và tìm kiếm nhân viên cho Quản lý --}}
        @if($canManage)
            <form method="GET" action="{{ route('phanca.index') }}" class="d-flex align-items-center flex-wrap gap-2">
                <input type="hidden" name="tuan" value="{{ $weekStart->toDateString() }}">
                @if($caFilterId !== '')
                    <input type="hidden" name="calamviec_id" value="{{ $caFilterId }}">
                @endif
                <select name="phongban_id" class="btn-filter" onchange="this.form.submit()">
                    <option value="">Tất cả phòng ban</option>
                    @foreach($phongBans as $pb)
                        <option value="{{ $pb->id }}" {{ (string)$phongBanId === (string)$pb->id ? 'selected' : '' }}>
                            {{ $pb->ten_phongban }}
                        </option>
                    @endforeach
                </select>
                <div class="search-users">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="text" name="q" value="{{ $keyword }}" placeholder="Tìm tên hoặc mã NV..." style="min-width:190px;">
                </div>
            </form>
        @endif
    </div>
</div>
