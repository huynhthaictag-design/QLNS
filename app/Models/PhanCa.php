<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PhanCa extends Model
{
    protected $table = 'phanca';
    public $timestamps = false;
    protected $fillable = ['nhanvien_id', 'calamviec_id', 'ngay_lam_viec', 'ghi_chu'];

    public function nhanVien() { return $this->belongsTo(User::class, 'nhanvien_id'); }
    public function caLamViec() { return $this->belongsTo(CaLamViec::class, 'calamviec_id'); }
}
