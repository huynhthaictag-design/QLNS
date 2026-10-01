<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanVien extends Model
{
    // Bắt buộc phải có dòng này để Laravel nhận diện đúng tên bảng của bạn
    protected $table = 'nhanvien';
    const UPDATED_AT = null;
    public function phongban()
    {
        return $this->belongsTo(PhongBan::class, 'phongban_id');
    }

    public function chucvu()
    {
        return $this->belongsTo(ChucVu::class, 'chucvu_id');
    }
}
