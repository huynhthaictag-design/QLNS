<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'nhanvien';
    public $timestamps = false;

    protected $fillable = [
        'ma_nv',
        'ho_ten',
        'email',
        'mat_khau',
        'so_dien_thoai',
        'anh_dai_dien',
        'cv_file',
        'phongban_id',
        'chucvu_id',
        'vai_tro',
        'trang_thai',
    ];

    protected $hidden = ['mat_khau'];

    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    public function phanCas()
    {
        return $this->hasMany(PhanCa::class, 'nhanvien_id');
    }

    public function phongBan()
    {
        return $this->belongsTo(PhongBan::class, 'phongban_id', 'id');
    }

    public function chucvu()
    {
        return $this->belongsTo(ChucVu::class, 'chucvu_id');
    }

    public function isAdmin()
    {
        return $this->vai_tro === 'Admin';
    }

    public function canManage()
    {
        return in_array($this->vai_tro, ['Admin', 'Manager'], true) && (int) $this->trang_thai === 1;
    }

    public function isActive()
    {
        return (int) $this->trang_thai === 1;
    }

    public function isPending()
    {
        return (int) $this->trang_thai === 2;
    }

    public function roleLabel()
    {
        if ((int) $this->trang_thai === 2) {
            return 'Đợi duyệt';
        }
        if ($this->vai_tro === 'Employee' || $this->vai_tro === 'User') {
            return 'User';
        }

        return $this->vai_tro ?: 'User';
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
}
