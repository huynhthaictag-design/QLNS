<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChamCong extends Model
{
    use HasFactory;

    protected $table = 'chamcong';
    public $timestamps = false;

    protected $fillable = [
        'phanca_id',
        'gio_checkin',
        'gio_checkout',
        'trang_thai',
    ];

    public function phanCa()
    {
        return $this->belongsTo(PhanCa::class, 'phanca_id');
    }
}
