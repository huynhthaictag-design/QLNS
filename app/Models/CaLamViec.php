<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CaLamViec extends Model
{
    protected $table = 'calamviec';
    public $timestamps = false;
    protected $fillable = ['ma_ca', 'ten_ca', 'gio_bat_dau', 'gio_ket_thuc', 'so_gio_cong', 'mau_nhan'];
}
