@php
    $dayNames = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];
    $avatarPalette = ['#8b5cf6', '#06b6d4', '#f59e0b', '#ec4899', '#10b981', '#3b82f6', '#ef4444', '#14b8a6'];
@endphp

<div class="panel">
    <div class="schedule-matrix-wrap">
        <table class="schedule-matrix">
            <thead>
                <tr>
                    <th class="sticky-col">
                        <div class="d-flex align-items-center justify-content-between">
                            <span style="font-size:.75rem;letter-spacing:.06em;color:#8b8499;font-weight:700;">NHÂN VIÊN ({{ $nhanViens->count() }})</span>
                            <span class="badge rounded-pill bg-light text-muted border fw-semibold" style="font-size:.68rem;">Ca / Giờ</span>
                        </div>
                    </th>
                    @foreach($days as $i => $day)
                        @php
                            $dKey = $day->toDateString();
                            $isToday = $dKey === $todayKey;
                            $isWeekend = $i >= 5;
                        @endphp
                        <th class="day-header-col {{ $isToday ? 'is-today' : ($isWeekend ? 'is-weekend' : '') }}">
                            <div class="day-name">
                                {{ $dayNames[$i] }}
                                @if($isToday)
                                    <span class="badge rounded-pill bg-primary ms-1" style="font-size:.6rem;vertical-align:middle;">Hôm nay</span>
                                @endif
                            </div>
                            <div class="day-num">{{ $day->format('d/m') }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($nhanViens as $nv)
                    @php
                        $byDate = $nv->phanCas->groupBy(function ($pc) {
                            return \Carbon\Carbon::parse($pc->ngay_lam_viec)->toDateString();
                        });
                        $nvTotalShifts = $nv->phanCas->count();
                        $nvTotalHours = $nv->phanCas->sum(function ($pc) {
                            return (float) optional($pc->caLamViec)->so_gio_cong;
                        });
                        $bgColor = $avatarPalette[$nv->id % count($avatarPalette)];
                    @endphp
                    <tr>
                        <td class="sticky-col">
                            <div class="d-flex align-items-start gap-2">
                                @if($nv->anh_dai_dien)
                                    <img src="{{ asset($nv->anh_dai_dien) }}" alt="{{ $nv->ho_ten }}" class="avatar-circle" style="width:38px;height:38px;object-fit:cover;">
                                @else
                                    <div class="avatar-circle" style="width:38px;height:38px;background:{{ $bgColor }};font-size:.8rem;">
                                        {{ $nv->initials() }}
                                    </div>
                                @endif
                                <div class="min-w-0 flex-grow-1">
                                    <div class="fw-bold text-truncate" style="font-size:.88rem;color:#1f1933;" title="{{ $nv->ho_ten }}">
                                        {{ $nv->ho_ten }}
                                    </div>
                                    <div class="muted text-truncate" style="font-size:.75rem;">
                                        {{ $nv->ma_nv }} · {{ optional($nv->phongBan)->ten_phongban ?? 'Chưa xếp PB' }}
                                    </div>
                                    <div class="emp-hours-pill">
                                        <i class="far fa-clock"></i> {{ $nvTotalShifts }} ca · {{ $nvTotalHours }}h
                                    </div>
                                </div>
                            </div>
                        </td>

                        @foreach($days as $i => $day)
                            @php
                                $key = $day->toDateString();
                                $isToday = $key === $todayKey;
                                $isWeekend = $i >= 5;
                                $items = $byDate->get($key, collect());
                            @endphp
                            <td class="{{ $isToday ? 'is-today' : ($isWeekend ? 'is-weekend' : '') }}">
                                @forelse($items as $pc)
                                    @php
                                        $ca = $pc->caLamViec;
                                        $color = ($ca && $ca->mau_nhan) ? $ca->mau_nhan : '#7c3aed';
                                    @endphp
                                    <div class="shift-card" style="border-left-color: {{ $color }}; background: {{ $color }}14;">
                                        @if($canManage)
                                            <form action="{{ route('phanca.destroy', $pc->id) }}" method="POST" onsubmit="return confirm('Gỡ {{ optional($ca)->ten_ca }} của {{ $nv->ho_ten }} ngày {{ $day->format('d/m') }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="shift-card-remove" title="Gỡ ca này">
                                                    <i class="fas fa-xmark"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <div class="shift-card-title">{{ optional($ca)->ten_ca }}</div>
                                        <div class="shift-card-time">
                                            <i class="far fa-clock" style="color:{{ $color }};"></i>
                                            {{ $ca ? \Carbon\Carbon::parse($ca->gio_bat_dau)->format('H:i') : '' }}–{{ $ca ? \Carbon\Carbon::parse($ca->gio_ket_thuc)->format('H:i') : '' }}
                                        </div>

                                        @if($pc->ghi_chu)
                                            <div class="shift-card-note" title="{{ $pc->ghi_chu }}">
                                                <i class="far fa-note-sticky me-1"></i>{{ $pc->ghi_chu }}
                                            </div>
                                        @endif

                                        @if($pc->chamCong && $pc->chamCong->gio_checkin)
                                            <div class="shift-checked-badge">
                                                <i class="fas fa-check"></i> Đã chấm công
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    @if(!$canManage)
                                        <div class="cell-empty-off">Nghỉ</div>
                                    @endif
                                @endforelse

                                @if($canManage)
                                    <button type="button" class="cell-add-btn js-assign"
                                        data-nv="{{ $nv->id }}"
                                        data-name="{{ $nv->ho_ten }}"
                                        data-ngay="{{ $key }}">
                                        <i class="fas fa-plus me-1"></i>{{ $items->isEmpty() ? 'Gán ca' : 'Thêm' }}
                                    </button>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-xmark fa-2x mb-2 opacity-25 d-block"></i>
                            {{ $canManage ? 'Không tìm thấy nhân viên nào phù hợp với bộ lọc.' : 'Bạn chưa được phân công lịch trong tuần này.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($nhanViens->count() > 0)
            <tfoot>
                <tr>
                    <td class="sticky-col">
                        <i class="fas fa-chart-simple me-1 text-primary"></i> Tổng ca trực theo ngày
                    </td>
                    @foreach($days as $day)
                        @php
                            $cnt = $dailyCounts[$day->toDateString()] ?? 0;
                        @endphp
                        <td class="text-center">
                            <span class="badge rounded-pill {{ $cnt > 0 ? 'bg-primary-subtle text-primary' : 'bg-light text-muted border' }} px-3 py-1">
                                {{ $cnt }} ca
                            </span>
                        </td>
                    @endforeach
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
