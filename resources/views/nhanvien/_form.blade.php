@php $nv = $nv ?? null; @endphp
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Mã nhân viên</label>
        <input type="text" name="ma_nv" class="form-control" required placeholder="VD: NV003" value="{{ old('ma_nv', $nv->ma_nv ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Họ và tên</label>
        <input type="text" name="ho_ten" class="form-control" required value="{{ old('ho_ten', $nv->ho_ten ?? '') }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Email</label>
        <input type="email" name="email" class="form-control" required value="{{ old('email', $nv->email ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Số điện thoại</label>
        <input type="text" name="so_dien_thoai" class="form-control" value="{{ old('so_dien_thoai', $nv->so_dien_thoai ?? '') }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Mật khẩu {{ $nv ? '(để trống nếu không đổi)' : '' }}</label>
        <input type="password" name="mat_khau" class="form-control" {{ $nv ? '' : 'required' }} minlength="6">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Vai trò</label>
        <select name="vai_tro" class="form-select">
            @php $role = old('vai_tro', $nv->vai_tro ?? 'Employee'); @endphp
            <option value="Employee" {{ $role === 'Employee' || $role === 'User' ? 'selected' : '' }}>User</option>
            <option value="Manager" {{ $role === 'Manager' ? 'selected' : '' }}>Manager</option>
            <option value="Admin" {{ $role === 'Admin' ? 'selected' : '' }}>Admin</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Phòng ban</label>
        <select name="phongban_id" class="form-select">
            <option value="">-- Chọn phòng ban --</option>
            @foreach($phongbans as $pb)
                <option value="{{ $pb->id }}" {{ (string) old('phongban_id', $nv->phongban_id ?? '') === (string) $pb->id ? 'selected' : '' }}>
                    {{ $pb->ten_phongban }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Chức vụ</label>
        <select name="chucvu_id" class="form-select">
            <option value="">-- Chọn chức vụ --</option>
            @foreach($chucvus as $cv)
                <option value="{{ $cv->id }}" {{ (string) old('chucvu_id', $nv->chucvu_id ?? '') === (string) $cv->id ? 'selected' : '' }}>
                    {{ $cv->ten_chucvu }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-bold">Trạng thái</label>
        @php $st = (string) old('trang_thai', $nv->trang_thai ?? 1); @endphp
        <select name="trang_thai" class="form-select">
            <option value="1" {{ $st === '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ $st === '0' ? 'selected' : '' }}>Inactive</option>
            <option value="2" {{ $st === '2' ? 'selected' : '' }}>Pending</option>
        </select>
    </div>
</div>
