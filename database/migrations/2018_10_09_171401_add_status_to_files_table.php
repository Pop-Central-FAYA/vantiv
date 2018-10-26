<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection('api_db')->statement("
            ALTER TABLE `files` MODIFY `status` CHAR(10) NOT NULL DEFAULT 'pending'"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection('api_db')->statement("
            ALTER TABLE `files` MODIFY `status` TINYINT(4) NOT NULL DEFAULT '1'"
        );
    }
}
