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
        //// database/migrations/xxxx_xx_xx_create_password_otps_table.php
        Schema::create('password_otps', function (Blueprint $table) {
            $table->id();
            $table->string('nis');
            $table->string('no_hp');
            $table->string('otp_code');
            $table->timestamp('expires_at');
            $table->boolean('is_verified')->default(false);
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
        //
    }
};

// ini 2026_07_27_110228_create_password_otps_table.php