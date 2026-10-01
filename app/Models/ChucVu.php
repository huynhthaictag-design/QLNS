<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChucVu extends Model
{

    protected $table = 'chucvu';
    public $timestamps = false;
    protected $fillable = ['ten_chucvu', 'he_so_luong'];
}
?>
