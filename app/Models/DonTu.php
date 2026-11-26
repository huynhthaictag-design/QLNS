<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonTu extends Model
{
    protected $table = 'dontu';
    public $timestamps = false;

    protected $fillable = [
        'nhanvien_id',
        'loai_don',
        'ngay_nop',
        'ngay_ap_dung',
        'ly_do',
        'trang_thai',
        'nguoi_duyet_id',
    ];

    public function nhanVien()
    {
        return $this->belongsTo(User::class, 'nhanvien_id');
    }

    public function nguoiDuyet()
    {
        return $this->belongsTo(User::class, 'nguoi_duyet_id');
    }

    public function loaiDonLabel()
    {
        switch ($this->loai_don) {
            case 'NghiPhep':
                return 'Xin nghỉ phép';
            case 'DoiCa':
                return 'Xin đổi ca';
            case 'DiTreVeSom':
                return 'Đi trễ / Về sớm';
            default:
                return $this->loai_don;
        }
    }

    public function trangThaiLabel()
    {
        switch ($this->trang_thai) {
            case 'DaDuyet':
                return 'Đã duyệt';
            case 'TuChoi':
                return 'Từ chối';
            default:
                return 'Chờ duyệt';
        }
    }
}
