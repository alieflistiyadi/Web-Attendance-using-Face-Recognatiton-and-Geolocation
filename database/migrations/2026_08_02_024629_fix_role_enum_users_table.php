<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Ubah data lama: siapa pun yang masih 'admin' (selain superadmin) jadi 'guru'
        DB::table('users')
            ->where('role', 'admin')
            ->update(['role' => 'guru']);

        // Ubah enum jadi cuma guru & superadmin
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('guru', 'superadmin') NOT NULL DEFAULT 'guru'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'superadmin') NOT NULL DEFAULT 'admin'");
    }
};