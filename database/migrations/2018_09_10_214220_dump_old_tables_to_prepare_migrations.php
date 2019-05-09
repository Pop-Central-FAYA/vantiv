<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DumpOldTablesToPrepareMigrations extends Migration
{
    /**
     * This migration runs the dump of the entry point db.
     * It is a hack around running refreshing migration when
     *  running unit test.
     */
    public function up()
    {
        DB::unprepared(file_get_contents('clean_default_db_for_test.sql'));
    }
}
