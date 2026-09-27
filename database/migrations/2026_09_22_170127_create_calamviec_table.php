<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalamviecTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('calamviec', function (Blueprint $table) {
        $table->id();
        $table->string('ma_ca', 20)->unique();
        $table->string('ten_ca', 100);
        $table->time('gio_bat_dau');
        $table->time('gio_ket_thuc');
        $table->float('so_gio_cong');
        $table->string('mau_nhan', 10);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calamviec');
    }
}
