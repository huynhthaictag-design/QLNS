<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'nhanvien';
    public $timestamps = false;
    protected $fillable = ['ma_nv', 'ho_ten', 'email', 'mat_khau', 'phongban_id', 'chucvu_id', 'vai_tro', 'trang_thai'];
    protected $hidden = ['mat_khau'];

    public function getAuthPassword() { return $this->mat_khau; }
    public function phanCas() { return $this->hasMany(PhanCa::class, 'nhanvien_id'); }
    public function phongBan()
{
    return $this->belongsTo(PhongBan::class, 'phongban_id', 'id');
}
}
