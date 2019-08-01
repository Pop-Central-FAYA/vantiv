<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUpdateMediaPlanEnumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE media_plans CHANGE COLUMN status status ENUM('Suggested', 'Selected', 'Pending', 'Approved', 'Declined', 'In Review') NOT NULL DEFAULT 'Suggested'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE media_plans CHANGE COLUMN status status ENUM('Suggested', 'Selected', 'Pending', 'Approved', 'Declined') NOT NULL DEFAULT 'Suggested'");
    }
}
