<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFileDateColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            ALTER TABLE `files`
                CHANGE time_created created_at TIMESTAMP DEFAULT now(),
                CHANGE time_modified updated_at TIMESTAMP DEFAULT now()
            "
        );
    }

    public function down()
    {

        DB::statement("
            ALTER TABLE `files`
                CHANGE created_at time_created TIMESTAMP,
                CHANGE updated_at time_modified TIMESTAMP
            "
        );
    }

}
