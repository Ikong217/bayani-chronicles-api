<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Use raw SQL to alter column type without Doctrine
        DB::statement('ALTER TABLE `user_in_game_data` MODIFY `summative` LONGTEXT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE `user_in_game_data` MODIFY `summative` VARCHAR(255) NULL');
    }
};
