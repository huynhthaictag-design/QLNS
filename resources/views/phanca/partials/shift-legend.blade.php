<div class="mb-3 d-flex gap-2 align-items-center flex-wrap">
    <span class="fw-bold">Danh mục ca:</span>
    @forelse($caLamViecs as $ca)
        <span class="shift-badge" style="background: {{ $ca->mau_nhan }}">
            {{ $ca->ten_ca }} ({{ \Carbon\Carbon::parse($ca->gio_bat_dau)->format('H:i') }}-{{ \Carbon\Carbon::parse($ca->gio_ket_thuc)->format('H:i') }})
        </span>
    @empty
        <span class="text-muted">Chưa có ca làm việc.</span>
    @endforelse
</div>
