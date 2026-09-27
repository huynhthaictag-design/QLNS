<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonTu extends Model
{
    protected $table = 'dontu';
    public $timestamps = false;
    protected $fillable = [
        'nhanvien_id', 'loai_don', 'ngay_nop',
        'ngay_ap_dung', 'ly_do', 'trang_thai', 'nguoi_duyet_id'
    ];
}
