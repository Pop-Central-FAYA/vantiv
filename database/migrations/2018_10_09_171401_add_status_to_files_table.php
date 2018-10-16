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
        DB::statement("ALTER TABLE `files` MODIFY `status` char(10) not null default 'pending' ");

        DB::statement("ALTER TABLE `files` CHANGE time_created created_at TIMESTAMP DEFAULT now(),
					                              CHANGE time_modified updated_at TIMESTAMP DEFAULT now() ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
