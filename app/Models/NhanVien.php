<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanVien extends Model
{
    protected $table = 'nhanvien';
    const UPDATED_AT = null;

    protected $fillable = [
        'ma_nv',
        'ho_ten',
        'email',
        'mat_khau',
        'so_dien_thoai',
        'phongban_id',
        'chucvu_id',
        'vai_tro',
        'trang_thai',
    ];

    protected $hidden = ['mat_khau'];

    public function phongban()
    {
        return $this->belongsTo(PhongBan::class, 'phongban_id');
    }

    public function chucvu()
    {
        return $this->belongsTo(ChucVu::class, 'chucvu_id');
    }

    public function roleLabel()
    {
        if ($this->vai_tro === 'Employee' || $this->vai_tro === 'User') {
            return 'User';
        }

        return $this->vai_tro ?: 'User';
    }

    public function statusLabel()
    {
        if ((int) $this->trang_thai === 1) {
            return 'Active';
        }
        if ((int) $this->trang_thai === 2) {
            return 'Pending';
        }

        return 'Inactive';
    }

    public function initials()
    {
        $parts = preg_split('/\s+/', trim((string) $this->ho_ten));
        if (!$parts || $parts[0] === '') {
            return '?';
        }
        if (count($parts) === 1) {
            return mb_strtoupper(mb_substr($parts[0], 0, 1));
        }

        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1));
    }

    public function avatarColor()
    {
        $palette = ['#8b5cf6', '#06b6d4', '#f59e0b', '#ec4899', '#10b981', '#3b82f6', '#ef4444', '#14b8a6'];

        return $palette[$this->id % count($palette)];
    }
}
