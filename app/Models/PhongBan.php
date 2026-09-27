<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhongBan extends Model
{
    protected $table = 'phongban';
    public $timestamps = false;
    protected $fillable = ['ma_phongban', 'ten_phongban'];
}
?>
