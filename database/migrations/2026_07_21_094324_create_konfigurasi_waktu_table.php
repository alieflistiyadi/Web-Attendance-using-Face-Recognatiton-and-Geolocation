<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('konfigurasi_waktu', function (Blueprint $table) {
            $table->id();

            $table->time('jam_mulai_masuk');
            $table->time('batas_telat');
            $table->time('batas_masuk');

            $table->time('jam_mulai_pulang');
            $table->time('batas_pulang');

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
        Schema::dropIfExists('konfigurasi_waktu');
    }
};
