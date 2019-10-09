<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStatusTypeMediaTable extends Migration
{
    public function __construct()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media_plans', function ($table) {
            $table->string('status')->default('pending')->change();
        });

        DB::statement("UPDATE `media_plans` SET `status` = 'pending' WHERE `status` = 'Suggested' OR `status` = 'Selected' OR `status` = 'Pending'");
        DB::statement("UPDATE `media_plans` SET `status` = 'approved' WHERE `status` = 'Approved'");
        DB::statement("UPDATE `media_plans` SET `status` = 'rejected' WHERE `status` = 'Declined'");
        DB::statement("UPDATE `media_plans` SET `status` = 'in review' WHERE `status` = 'In Review'");
    }
}
