@php
    $dayNames = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];
@endphp

<div class="panel">
    <div class="table-responsive">
        <table class="table users-table schedule-table mb-0">
            <thead>
                <tr>
                    <th style="min-width:200px">NHÂN VIÊN</th>
                    @foreach($days as $i => $day)
                        <th class="text-center day-cell">
                            {{ $dayNames[$i] }}<br>
                            <span class="fw-normal">{{ $day->format('d/m') }}</span>
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
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $nv->ho_ten }}</div>
                            <div class="muted">{{ $nv->ma_nv }} · {{ $nv->phongBan->ten_phongban ?? 'Chưa xếp PB' }}</div>
                        </td>
                        @foreach($days as $day)
                            @php
                                $key = $day->toDateString();
                                $items = $byDate->get($key, collect());
                            @endphp
                            <td class="text-center">
                                @forelse($items as $pc)
                                    <span class="shift-badge" style="background: {{ optional($pc->caLamViec)->mau_nhan ?: '#8b5cf6' }}">
                                        {{ optional($pc->caLamViec)->ten_ca }}
                                        @if($canManage)
                                            <form action="{{ route('phanca.destroy', $pc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Gỡ ca này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Gỡ ca">&times;</button>
                                            </form>
                                        @endif
                                    </span>
                                @empty
                                    <div class="muted small">Nghỉ</div>
                                @endforelse

                                @if($canManage)
                                    <button type="button" class="btn-add-shift mt-1 js-assign"
                                        data-nv="{{ $nv->id }}"
                                        data-name="{{ $nv->ho_ten }}"
                                        data-ngay="{{ $key }}">+ Gán ca</button>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            {{ $canManage ? 'Chưa có nhân viên.' : 'Bạn chưa được phân công lịch trong tuần này.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
